<?php

/*
+----------------------------------------------------------------------+
|This program is free software; you can redistribute it and/or modify  |
|it under the terms of the GNU General Public License as published by  |
|the Free Software Foundation; either version 2 of the License, or     |
|(at your option) any later version.                                   |
|                                                                      |
|This program is distributed in the hope that it will be useful,       |
|but WITHOUT ANY WARRANTY; without even the implied warranty of        |
|MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
|GNU General Public License for more details.                          |
|                                                                      |
|You should have received a copy of the GNU General Public License     |
|along with this program; if not, write to the Free Software           |
|Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.             |
+----------------------------------------------------------------------+
|Authors: Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * @package peer_soap_lib
 */
 
class SoapVal extends PEAR
{
	/**
	 * @access public
	 */
	var $me = array();
	
	/**
	 * @access public
	 */
	var $mytype = 0;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
  	function SoapVal( $val = -1, $type = "" )
	{
		global $soapTypes;
		
		$this->me = array();
		$this->mytype = 0;
		
		if ( $val != -1 || $type != "" )
		{
			if ( $type == "" )
				$type = "string";
			
			if ( $soapTypes[$type] == 1 )
				$this->addScalar( $val, $type );
	    	else if ( $soapTypes[$type] == 2 )
				$this->addArray( $val );
			else if ( $soapTypes[$type] == 3 )
				$this->addStruct( $val );
		}
    }

	
	/**
	 * @access public
	 */
	function addScalar( $val, $type = "string" )
	{
		global $soapTypes;
		global $soapBoolean;

		if ( $this->mytype == 1 )
		{
			echo( "<B>soapval</B>: scalar can have only one value<BR>" );
			return false;
		}
		
		$typeof = $soapTypes[$type];
		
		if ( $typeof != 1 )
		{
			echo( "<B>soapval</B>: not a scalar type (${typeof})<BR>" );
			return false;
		}
		
		if ( $type == $soapBoolean )
		{
			if ( strcasecmp( $val, "true" ) == 0 || $val == 1 || $val == true )
				$val = 1;
			else
				$val = 0;
		}
		
		if ( $this->mytype == 2 )
		{
			// we're adding to an array here
			$ar   = $this->me["array"];
			$ar[] = new SoapVal( $val, $type );
			$this->me["array"] = $ar;
		}
		else
		{
			// a scalar, so set the value and remember we're scalar
			$this->me[$type] = $val;
			$this->mytype    = $typeof;
		}
		
		return true;
    }

	/**
	 * @access public
	 */
	function addArray( $vals )
	{
		global $soapTypes;
		
		if ( $this->mytype != 0 )
		{
			echo( "<B>soapval</B>: already initialized as a [" .$this->kindOf() . "]<BR>" );
			return false;
		}
		
		$this->mytype = $soapTypes["array"];
		$this->me["array"] = $vals;
		
		return true;
	}

	/**
	 * @access public
	 */
	function addStruct( $vals )
	{
		global $soapTypes;
		
		if ( $this->mytype != 0 )
		{
	   		echo( "<B>soapval</B>: already initialized as a [" .$this->kindOf() . "]<BR>" );
	    	return false;
		}
		
		$this->mytype = $soapTypes["struct"];
		$this->me["struct"] = $vals;
		
		return true;
	}

	/**
	 * @access public
	 */
	function dump( $ar )
	{
		reset( $ar );
		
		while ( list( $key, $val ) = each( $ar ) )
		{
			echo( "$key => $val<br>" );
		    
			if ( $key == 'array' )
			{
				while ( list( $key2, $val2 ) = each( $val ) )
					echo( "-- $key2 => $val2<br>" );
		    }
		}
	}

	/**
	 * @access public
	 */
	function kindOf()
	{
		switch ( $this->mytype )
		{
			case 3 :
	    		return "struct";
	    		break;
		
			case 2 :
	    		return "array";
	    		break;
	    
			case 1 :
	    		return "scalar";
	    		break;
		
			default :
	  			return "undef";
		}
	}

	/**
	 * @access public
	 */
	function serializedata( $typ, $val, $i = 0, $return = 0 )
	{
		if ( !$return )
			$PARAM = "params" . $i;
		else
			$PARAM = "return";
		
		$rs = "";
		
		global $soapTypes;
		global $soapBase64;
		global $soapString;
		global $soapBoolean;
		
		switch ( $soapTypes[$typ] )
		{
			case 3 :
				// struct
				$rs .= "<$PARAM xmlns:ns2=\"http://xml.apache.org/xml-soap\" xsi:type=\"ns2:Map\">\n";
				reset( $val );
			
				while ( list( $key2, $val2 ) = each( $val ) )
					$rs .= $this->serializeval3( $key2, $val2 );
			
				$rs .= "</$PARAM>\n";		
				break;
		
			case 2:
				// array
				$rs .= "<$PARAM xmlns:ns2=\"http://schemas.xmlsoap.org/soap/encoding/\" xsi:type=\"ns2:Array\" ns2:arrayType=\"xsd:ur-type[".sizeof($val)."]\">\n";
				//echo "size of array is:".sizeof($val);
			
				for ( $j = 0; $j < sizeof( $val ); $j++ )
					$rs.=$this->serializeval2( $val[$j] );
			
				$rs.="</$PARAM>\n";
				break;
		
			case 1 :
				switch ( $typ )
				{
					case $soapBase64 :
						$rs .= "<${typ}>" . base64_encode( $val ) . "</${typ}>";
						break;
			
					case $soapBoolean :
						$rs .= "<${typ}>" . ( $val? "1" : "0" ) . "</${typ}>";
						break;
			
					case $soapString :
						$rs .= "<$PARAM xsi:type=\"xsd:${typ}\">". htmlspecialchars( $val )."</$PARAM>\n";
						break;
						
					default :
						$rs .= "<$PARAM xsi:type=\"xsd:${typ}\">"."${val}"."</$PARAM>\n";//"<${typ}>${val}</${typ}>";
				}
			
				break;
		
			default :
				break;
		}
		
		return $rs;
	}

	/**
	 * @access public
	 */
	function serialize( $i )
	{
		return $this->serializeval( $this, $i );
    }

	/**
	 * @access public
	 */	
    function serialize_response()
	{
		return $this->serializeval_response( $this );
    }

	/**
	 * @access public
	 */	
    function serializeval( $o, $i = 0 )
	{
		global $soapTypes;
		
		$rs = "";
		$ar = $o->me;
		
		reset( $ar );
		list( $typ, $val ) = each( $ar );
		$rs .= $this->serializedata( $typ, $val,$i );
		
		return $rs;
    }

	/**
	 * @access public
	 */	
  	function serializeval2( $o )
	{
  		$type = gettype( $o );
  		
		if ( $type == 'integer' )
			$type = 'int';
		
		global $soapTypes;
		
		$rs  = "";
		$rs .= "<item xsi:type=\"xsd:".$type."\">$o</item>\n";
		// $rs.="<item>$o</item>\n";
		
		return $rs;
    }

	/**
	 * @access public
	 */	
    function serializeval3( $k, $o )
	{
  		$typek = gettype( $k );
  		$typeo = gettype( $o );
  		
		if ( $typek == 'integer' )
			$typek = 'int';
  		
		if ( $typeo == 'integer' )
			$typeo = 'int';
		
		global $soapTypes;
		
		$rs  = "";
		$rs .="<item>\n";
		$rs .="<key xsi:type=\"xsd:".$typek."\">$k</key>\n";
		$rs .="<value xsi:type=\"xsd:".$typeo."\">$o</value>\n";
		$rs .="</item>\n";
		
		return $rs;
    }

	/**
	 * @access public
	 */	
    function serializeval_response( $o )
	{
		global $soapTypes;		
		
		$rs = "";
		$ar = $o->me;
		
		reset( $ar );
		list( $typ, $val ) = each( $ar );
		// $rs.= "<return xmlns:ns2=\"http://schemas.xmlsoap.org/soap/encoding/\" xsi:type=\"ns2:Array\" ns2:arrayType=\"xsd:ur-type[2]\"\n";
		$rs .= $this->serializedata( $typ, $val, 0, 1 );
		// $rs.="</return>\n";
		
		return $rs;
    }

	/**
	 * @access public
	 */	
    function structmem( $m )
	{
		$nv = $this->me["struct"][$m];
		return $nv;
    }

	/**
	 * @access public
	 */
	function structreset()
	{
		reset( $this->me["struct"] );
	}

	/**
	 * @access public
	 */	
	function structeach()
	{
		return each( $this->me["struct"] );
	}

	/**
	 * @access public
	 */
    function scalarval()
	{
		global $soapBoolean;
		global $soapBase64;
		
		reset( $this->me );
		list( $a, $b ) = each( $this->me );
		return $b;
    }

	/**
	 * @access public
	 */
    function scalartyp()
	{
		global $soapI4;
		global $soapInt;
		
		reset( $this->me );
		list( $a, $b ) = each( $this->me );
		
		if ( $a == $soapI4 ) 
			$a = $soapInt;
		
		return $a;
    }

	/**
	 * @access public
	 */
    function arraymem( $m )
	{
		$nv = $this->me["array"][$m];
		return $nv;
    }

	/**
	 * @access public
	 */
    function arraysize()
	{
		reset( $this->me );
		list( $a, $b ) = each( $this->me );
		
		return sizeof( $b );
    }
} // END OF SoapVal

?>
