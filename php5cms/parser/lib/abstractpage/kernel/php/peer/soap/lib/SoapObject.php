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


using( 'peer.soap.lib.SoapVal' );


/**
 * @package peer_soap_lib
 */
 
class SoapObject extends PEAR
{
	/**
	 * @access public
	 */
	function soap_se( $parser, $name, $attrs )
	{
		global $_xh;
		global $soapDateTime;
		global $soapString;
	
		if ( ereg( "^[n][s][1-9][:]", $name ) )
		{
			while ( list( $key, $value ) = each( $attrs ) )
			{ 
				if ( ereg( "^xmlns:ns", $key ) )
					$_xh[$parser]['tid'] = $value;
			}
		}
	
		while ( list( $key, $value ) = each( $attrs ) )
		{
			if ( ereg( "xsi:type", $key ) )
				$name_type = substr( strrchr( $value, ":" ), 1 );
		}
	
		// echo "this is type of val:" . $name_type;
	
		if ( ereg( "return", $name ) )
			$_xh[$parser]['rt'] = $name_type;
		
		if ( ereg( "params", $name ) )
		{
			$name = "parameter";	
			$_xh[$parser]['pt'] = $name_type;
		}
	
		switch ( $name )
		{	
			case "item" : 
	    		//$_xh[$parser]['flag']++;
				// this last line turns quoting off
				// this means if we get an empty array we'll 
				// simply get a bit of whitespace in the eval
	    		$_xh[$parser]['qt'] = 0;
	    		$_xh[$parser]['ac'] = "";
		
				break;
	
			case "STRUCT" :
	
			case "ARRAY" :
	 			$_xh[$parser]['st'] .= "array(";
	  			$_xh[$parser]['cm']++;
				// this last line turns quoting off
				// this means if we get an empty array we'll 
				// simply get a bit of whitespace in the eval
	  			$_xh[$parser]['qt'] = 0;
	  
	  			break;
	
			case "NAME" :
		  		$_xh[$parser]['st'] .= "'";
				$_xh[$parser]['ac']  = "";
	  		
				break;
	
			case "FAULT" :
		 		$_xh[$parser]['isf'] = 1;
		  		break;
	
			case "PARAM" :
		  		$_xh[$parser]['st'] = "";
	  			break;
			
			case "parameter" :
		  		$_xh[$parser]['st']  = "";
	  			$_xh[$parser]['st'] .= "new SoapVal("; 
	  
	 	 		if ( ( $_xh[$parser]['pt'] == 'Array' ) || ( $_xh[$parser]['pt'] == 'Vector' ) || ( $_xh[$parser]['pt'] == 'Map' ) )
	  				$_xh[$parser]['st'] .= "array(";
	  
	 	 		$_xh[$parser]['lv'] = 1;
	  			$_xh[$parser]['vt'] = $soapString;
	 	 		// $_xh[$parser]['vt'] = 'int';
	  			// echo "*****".$soapString."*****";
	  			$_xh[$parser]['ac'] = "";
	  			// look for a value: if this is still 1 by the
	  			// time we reach the first data segment then the type is string
	  			// by implication and we need to add in a quote
				
				break;
	
			case "return" :
	  			$_xh[$parser]['st'] .= "new SoapVal("; 
	  
	  			if ( ( $_xh[$parser]['rt'] == 'Array' ) || ( $_xh[$parser]['rt'] == 'Vector' ) || ( $_xh[$parser]['rt'] == 'Map' ) )
	  				$_xh[$parser]['st'] .= "array(";
	  
		  		$_xh[$parser]['lv'] = 1;
		  		$_xh[$parser]['vt'] = $soapString;
		  		$_xh[$parser]['ac'] = "";
				
		  		// look for a value: if this is still 1 by the
		  		// time we reach the first data segment then the type is string
		  		// by implication and we need to add in a quote
			
				break;
	
			case "I4" :
	
			case "INT" :
	
			case "STRING" :
	
			case "BOOLEAN" :
	
			case "DOUBLE" :
	
			case "DATETIME.ISO8601" :
	
			case "BASE64" :
				// reset the accumulator
		  		$_xh[$parser]['ac'] = "";

	 	 		if ( $name == "DATETIME.ISO8601" || $name == "STRING" )
				{
					$_xh[$parser]['qt'] = 1; 
			
					if ( $name == "DATETIME.ISO8601" )
						$_xh[$parser]['vt'] = $soapDateTime;
	  			}
				else if ( $name == "BASE64" )
				{
					$_xh[$parser]['qt'] = 2;
				}
				else
				{
					$_xh[$parser]['qt'] = 0;
	  			}
		
				break;
	
			case "MEMBER" :
				$_xh[$parser]['ac'] = "";
	
			case "key" :
				$_xh[$parser]['qt'] = 0;
				$_xh[$parser]['ac'] = "";
	  
	  			break;
	
			case "value" :
				$_xh[$parser]['qt'] = 0;
				$_xh[$parser]['ac'] = "";
	  
	  			break;
	
			default :
				break;
		}

		if ( $name != "VALUE" )
			$_xh[$parser]['lv'] = 0;
	}

	/**
	 * @access public
	 */
	function soap_ee( $parser, $name )
	{
		global $_xh;
		global $soapTypes;
		global $soapString;
	
		if ( ereg( "^[n][s][1-9][:]", $name ) )
		{
			$methName = substr( strrchr( $name, ":" ), 1 );
			$name     = "ismethod";
		}
	
		if ( ereg( "params", $name ) )
			$name = "parameter";	
	
		switch ( $name )
		{
			case "item" : 
				if ( $_xh[$parser]['rt'] == 'Map' )
					$_xh[$parser]['st'].= ",";
				else
					$_xh[$parser]['st'] .= utf8_decode( $_xh[$parser]['ac'] ) . ",";
	    
				$_xh[$parser]['vt'] = strtolower( $name );
	    		$_xh[$parser]['ac'] = "";
		
				break;
	
			case "key" : 
				$_xh[$parser]['st'] .= "\"".utf8_decode($_xh[$parser]['ac'])."\""."=>";
	    		$_xh[$parser]['vt']  = strtolower( $name );
	    		$_xh[$parser]['ac']  = "";
		
				break;
	
			case "value" : 
				$_xh[$parser]['st'] .= "\"".utf8_decode($_xh[$parser]['ac'])."\"";
	    		$_xh[$parser]['vt']  = strtolower( $name );
	    		$_xh[$parser]['ac']  = "";
		
				break;
	
			case "STRUCT" :
	
			case "ARRAY" :
	  			if ( $_xh[$parser]['cm'] && substr( $_xh[$parser]['st'], -1 ) ==',' )
					$_xh[$parser]['st'] = substr( $_xh[$parser]['st'], 0, -1 );
	  
		  		$_xh[$parser]['st'] .= ")";	
		  		$_xh[$parser]['vt']  = strtolower( $name );
		  		$_xh[$parser]['cm']--;
	  
		  		break;
	
			case "NAME" :
		  		$_xh[$parser]['st'] .= $_xh[$parser]['ac'] . "' => ";
		  		break;
	
			case "BOOLEAN" :
				// special case here: we translate boolean 1 or 0 into PHP
				// constants true or false
				if ( $_xh[$parser]['ac'] == '1' ) 
					$_xh[$parser]['ac'] = "true";
				else 
					$_xh[$parser]['ac'] = "false";
		
				$_xh[$parser]['vt'] = strtolower( $name );
				// Drop through intentionally.
	
			case "I4" :
	
			case "INT" :
	
			case "STRING" :
	
			case "DOUBLE" :
	
			case "DATETIME.ISO8601" :
	
			case "BASE64" :
	  			if ( $_xh[$parser]['qt'] == 1 )
				{
					// we use double quotes rather than single so backslashification works OK
					$_xh[$parser]['st'] .= "\"" . $_xh[$parser]['ac'] . "\""; 
				}
				else if ( $_xh[$parser]['qt'] == 2 )
				{
					$_xh[$parser]['st'] .= "base64_decode('" . $_xh[$parser]['ac'] . "')"; 
				}
				else
				{
					$_xh[$parser]['st'] .= $_xh[$parser]['ac'];
				}
		
				$_xh[$parser]['ac'] = "";
				$_xh[$parser]['qt'] = 0;
	  
	  			break;
		
			case "parameter" :
				// echo $_xh[$parser]['vt']."ngleng;lewmk";
				// $_xh[$parser]['vt']=$_xh[$parser]['pt'];
		
				if ( ( $_xh[$parser]['pt'] == 'Array' ) || ( $_xh[$parser]['pt'] == 'Vector' ) || ( $_xh[$parser]['pt'] == 'Map' ) )
				{
					$_xh[$parser]['st']  = substr( $_xh[$parser]['st'], 0, -1 );
					$_xh[$parser]['st'] .= ")"; 
				}
		
				if ( ( $_xh[$parser]['pt'] == 'Array' ) || ( $_xh[$parser]['pt'] == 'Vector' ) )
					$_xh[$parser]['vt'] = 'array';
		
				if ( $_xh[$parser]['pt'] == 'Map' )
					$_xh[$parser]['vt'] = 'struct';
		
				if ( $_xh[$parser]['pt'] == 'string' )
					$_xh[$parser]['vt'] = 'string';
		
				if ( $_xh[$parser]['pt'] == 'int' )
					$_xh[$parser]['vt'] = 'int';
		
				if ( $_xh[$parser]['pt'] == 'double' )
					$_xh[$parser]['vt'] = 'double';
		
				if ( ( $_xh[$parser]['pt'] == 'Array' ) || ( $_xh[$parser]['pt'] == 'Vector' ) || ( $_xh[$parser]['pt'] == 'Map' ) ) 	
					$_xh[$parser]['st'] .= "";
				else
					$_xh[$parser]['st'] .= "\"" . $_xh[$parser]['ac'] . "\""; 	

				$_xh[$parser]['st'] .= ", '" . $_xh[$parser]['vt'] . "')";
		
				if ( $_xh[$parser]['cm'] )
					$_xh[$parser]['st'] .= ",";
		
				// echo $_xh[$parser]['st']."HIJKLMN";
		
				$_xh[$parser]['params'][] = $_xh[$parser]['st'];
				break;
	
			case "return" :
				if ( ( $_xh[$parser]['rt'] == 'Array' ) || ( $_xh[$parser]['rt'] == 'Vector' ) || ( $_xh[$parser]['rt'] == 'Map' ) )
				{
					$_xh[$parser]['st']  = substr( $_xh[$parser]['st'], 0, -1 );
					$_xh[$parser]['st'] .= ")"; 
				}
			
				if ( ( $_xh[$parser]['rt'] == 'Array' ) || ( $_xh[$parser]['rt'] == 'Vector' ) )
					$_xh[$parser]['vt'] = 'array';
		
				if ( $_xh[$parser]['rt'] == 'Map' )
					$_xh[$parser]['vt'] = 'struct';
		
				if ( $_xh[$parser]['rt'] == 'string' )
					$_xh[$parser]['vt'] = 'string';
		
				if ( $_xh[$parser]['rt'] == 'int' )
					$_xh[$parser]['vt'] = 'int';
		
				if ( $_xh[$parser]['rt'] == 'double' )
					$_xh[$parser]['vt'] = 'double';
		
				if ( ( $_xh[$parser]['rt'] == 'Array' ) || ( $_xh[$parser]['rt'] == 'Vector' ) || ( $_xh[$parser]['rt'] == 'Map' ) ) 	
					$_xh[$parser]['st'] .= "";
				else	  	
					$_xh[$parser]['st'] .= "\"" . $_xh[$parser]['ac'] . "\""; 

				// This if() detects if no scalar was inside <VALUE></VALUE>
				// and pads an empty "".
				if ( $_xh[$parser]['st'][strlen( $_xh[$parser]['st'] ) - 1] == '(' )
					$_xh[$parser]['st'] .= '""';
			
				$_xh[$parser]['st'] .= ", '" . $_xh[$parser]['vt'] . "')";
			
				if ( $_xh[$parser]['cm'] )
					$_xh[$parser]['st'] .= ",";
		
				$_xh[$parser]['params'][] = $_xh[$parser]['st'];
				break;
	
			case "MEMBER" :
	  			$_xh[$parser]['ac'] = "";
				$_xh[$parser]['qt'] = 0;
	 			
				break;
	
			case "DATA" :
	  			$_xh[$parser]['ac'] = "";
				$_xh[$parser]['qt'] = 0;
	  		
				break;
	
			case "PARAM" :
		  		$_xh[$parser]['params'][] = $_xh[$parser]['st'];
	  			break;
	
			case "ismethod" :
				$_xh[$parser]['method'] = $methName;
				break;
	
			case "BOOLEAN" :
				// special case here: we translate boolean 1 or 0 into PHP
				// constants true or false
				if ( $_xh[$parser]['ac'] == '1' ) 
					$_xh[$parser]['ac'] = "true";
				else 
					$_xh[$parser]['ac'] = "false";
		
				$_xh[$parser]['vt'] = strtolower( $name );
				break;
	
			default :
				break;
		}
	
		// if it's a valid type name, set the type
		if ( $soapTypes[strtolower( $name )] )
			$_xh[$parser]['vt'] = strtolower( $name );	
	}

	/**
	 * @access public
	 */
	function soap_cd( $parser, $data )
	{	
		global $_xh;
		global $soap_backslash;
		global $soap_twoslash;

		if ( $_xh[$parser]['lv'] == 1 )
		{  
			$_xh[$parser]['qt'] = 1; 
			$_xh[$parser]['lv'] = 2; 
		}
  
  		if ( $_xh[$parser]['qt'] )
			$_xh[$parser]['ac'] .= str_replace( '\$', '\\$', str_replace( '"', '\"', str_replace( chr( 92 ), $soap_backslash, $data ) ) );
		else 
			$_xh[$parser]['ac'].=$data;
	}

	/**
	 * @access public
	 */
	function soap_dh( $parser, $data )
	{
		global $_xh;

		if ( substr( $data, 0, 1 ) == "&" && substr( $data, -1, 1 ) == ";" )
		{
			if ( $_xh[$parser]['lv'] == 1 )
			{  
				$_xh[$parser]['qt'] = 1; 
				$_xh[$parser]['lv'] = 2; 
			}
		
			$_xh[$parser]['ac'] .= $data;
		}
	}

	/**
	 * soap_decode takes a message in PHP soap object format and
	 * tranlates it into native PHP types
	 *
	 * @access public
	 */
	function soap_decode( $soap_val )
	{
		$kind = $soap_val->kindOf();

		if ( $kind == "scalar" )
		{
			return $soap_val->scalarval();
		}
		else if ( $kind == "array" )
		{
			$size = $soap_val->arraysize();
			$arr = array();

			for ( $i = 0; $i < $size; $i++ )
				array_append( $arr, soap_decode( $soap_val->arraymem( $i ) ) );
      
			return $arr; 
		}
		else if ( $kind == "struct" )
		{
			$soap_val->structreset();
			$arr = array();

			while ( list( $key, $value ) = $soap_val->structeach() )
				$arr[$key] = soap_decode( $value );
      
			return $arr;
		}
	}

	/**
	 * soap_encode takes native php types and encodes them into
	 * soap PHP object format
	 *
	 * BUG: All sequential arrays are turned into structs.  I don't
	 * know of a good way to determine if an array is sequential only.
	 *
	 * feature creep -- could support more types via optional type
	 * argument
	 *
	 * @access public
	 */
	function soap_encode( $php_val )
	{
		global $soapInt;
		global $soapDouble;
		global $soapString;
		global $soapArray;
		global $soapStruct;

		$type = gettype( $php_val );
		$soap_val = new SoapVal();

		switch ( $type )
		{
			case "array" :

			case "object" :
				$arr = array();
			
				while ( list( $k, $v ) = each( $php_val ) )
					$arr[$k] = soap_encode( $v );
         
				$soap_val->addStruct( $arr );
				break;
			
			case "integer" :
				$soap_val->addScalar( $php_val, $soapInt );
				break;
			
			case "double" :
				$soap_val->addScalar( $php_val, $soapDouble );
				break;
			
			case "string" :
				$soap_val->addScalar( $php_val, $soapString );
				break;
		
			case "unknown type" :
		
			default :
				$soap_val = false;
				break;
		}
	
		return $soap_val;
	}


	// date helpers

	/**
	 * @access public
	 */
	function iso8601_encode( $timet, $utc = 0 )
	{
		// return an ISO8601 encoded string
		// really, timezones ought to be supported
		// but the XML-RPC spec says:
		//
		// "Don't assume a timezone. It should be specified by the server in its
		// documentation what assumptions it makes about timezones."
		// 
		// these routines always assume localtime unless 
		// $utc is set to 1, in which case UTC is assumed
		// and an adjustment for locale is made when encoding
		if ( !$utc )
		{
			$t = strftime( "%Y%m%dT%H:%M:%S", $timet );
		}
		else
		{
			if ( function_exists( "gmstrftime" ) )
			{
				// gmstrftime doesn't exist in some versions of PHP
				$t = gmstrftime( "%Y%m%dT%H:%M:%S", $timet );
			}
			else
			{
				$t = strftime( "%Y%m%dT%H:%M:%S", $timet - date( "Z" ) );
			}
		}
	
		return $t;
	}

	/**
	 * @access public
	 */
	function iso8601_decode( $idate, $utc = 0 )
	{
		// return a timet in the localtime, or UTC
		$t = 0;
	
		if ( ereg( "([0-9]{4})([0-9]{2})([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})", $idate, $regs ) )
		{
			if ( $utc )
				$t = gmmktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
			else
				$t = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
	
		return $t;
	}
} // END OF SoapObject

?>
