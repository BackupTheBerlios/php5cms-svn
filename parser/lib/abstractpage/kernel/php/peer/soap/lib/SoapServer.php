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


using( 'peer.soap.lib.SoapObject' );
using( 'peer.soap.lib.SoapResp' );
using( 'peer.soap.lib.SoapMsg' );
using( 'peer.soap.lib.SoapVal' );


/**
 * @package peer_soap_lib
 */
 
class SoapServer extends SoapObject
{
	/**
	 * @access public
	 */
	var $xml;

	/**
	 * @access public
	 */	
	var $dmap = array();


	/**
	 * Constructor
	 *
	 * @access public
	 */   
	function SoapServer( $serviceNow = 1 )
	{
		if ( $serviceNow )
			$this->service();
	}
	

	/**
	 * @access public
	 */	
	function serializeDebug()
	{
		global $_soap_debuginfo;
		
		if ( $_soap_debuginfo != "" ) 
			return "";
		else
			return "";
	}

	/**
	 * @access public
	 */
	function service()
	{
		$r = $this->parseRequest();
		
		$payload = "" .
			// xml version=\"1.0\" encoding=\"UTF-8\"\n" . 
			$this->serializeDebug() .
			$r->serialize();
		
		header( "Content-type: text/xml\nContent-length: " . strlen( $payload ) );
		print $payload;
	}

	/**
	 * @access public
	 */
	function verifySignature( $in, $sig )
	{
		for ( $i = 0; $i < sizeof( $sig ); $i++ )
		{
			// check each possible signature in turn
			$cursig = $sig[$i];
			
			if ( sizeof( $cursig ) == $in->getNumParams() + 1 )
			{
				$itsOK = 1;
				
				for ( $n = 0; $n < $in->getNumParams(); $n++ )
				{
					$p = $in->getParam( $n );
					// print "<!-- $p -->\n";
					
					if ( $p->kindOf() == "scalar" )
						$pt = $p->scalartyp();
					else
						$pt = $p->kindOf();
					
					// $n+1 as first type of sig is return type
					if ( $pt != $cursig[$n+1] )
					{
						$itsOK  = 0;
						$pno    = $n + 1;
						$wanted = $cursig[$n + 1];
						$got    = $pt;
						
						break;
					}
				}
				
				if ( $itsOK ) 
					return array( 1 );
			}
		}
		
		return array( 0, "Wanted ${wanted}, got ${got} at param ${pno})" );
	}

	/**
	 * @access public
	 */
	function parseRequest( $data = "" )
	{
		global $_xh;
		global $HTTP_RAW_POST_DATA;
		global $targetObjId;
		global $soaperr;
		global $soapstr;
		global $soaperrxml;
		global $soap_defencoding;
		global $_soaps_dmap;

		if ( $data == "" )
			$data = $HTTP_RAW_POST_DATA;
	
		$this->xml = xml_parser_create( $soap_defencoding );
		xml_set_object( $this->xml, &$this );
		
		$_xh[$this->xml] = array();
		$_xh[$this->xml]['st']     = "";
		$_xh[$this->xml]['cm']     = 0; 
		$_xh[$this->xml]['isf']    = 0; 
		$_xh[$this->xml]['params'] = array();
		$_xh[$this->xml]['method'] = "";
		$_xh[$this->xml]['tid']    = "";
		$_xh[$this->xml]['pt']     = "";
	
		// decompose incoming XML into request structure
		xml_parser_set_option( $this->xml, XML_OPTION_CASE_FOLDING, false );
		xml_set_element_handler( $this->xml, "soap_se", "soap_ee" );
		xml_set_character_data_handler( $this->xml, "soap_cd" );
		xml_set_default_handler( $this->xml, "soap_dh" );
	
		if ( !xml_parse( $this->xml, $data, 1 ) )
		{
	  		// return XML error as a faultCode
	  		$r = new SoapResp( 0, $soaperrxml + xml_get_error_code( $this->xml ), sprintf( "XML error: %s at line %d", xml_error_string( xml_get_error_code( $this->xml ) ), xml_get_current_line_number( $this->xml ) ) );
			xml_parser_free( $this->xml );
		}
		else
		{
			xml_parser_free( $this->xml );
			$targetObjId = $_xh[$this->xml]['tid'];
			eval( "include(\"$targetObjId\");" );
	
			// echo "test***************\n".$targetObjId."\n*********************test";
	  
	  		$m = new SoapMsg( $_xh[$this->xml]['method'], $_xh[$this->xml]['tid'] );
	  
	  		// now add parameters in
	  		for ( $i = 0; $i < sizeof( $_xh[$this->xml]['params'] ); $i++ )
			{
				// print "<!-- " . $_xh[$this->xml]['params'][$i]. "-->\n";
				$plist .= "$i - " . $_xh[$this->xml]['params'][$i] . " \n";
				eval( '$m->addParam(' . $_xh[$this->xml]['params'][$i] . ");" );
	  		}
		
			$this->soap_debugmsg( $plist );
	  
	  		// now to deal with the method
			$methName = $_xh[$this->xml]['method'];
		
			//echo "*********\n".$methName."\n***********";
		
			if ( ereg( "^system\.", $methName ) )
			{
				$dmap    = $_soaps_dmap;
				$sysCall = 1;
			}
			else
			{
				$dmap    = $this->dmap;
				$sysCall = 0;
			}
	  
			if ( isset( $methName ) )
			{
				// dispatch if exists
				if ( isset( $dmap[$methName]['signature'] ) )
					$sr = $this->verifySignature( $m, $dmap[$methName]['signature'] );
		
				if ( $sysCall )
				{ 
					eval( '$r=' . $methName . '($this, $m);' );
				}
				else
				{
					$param_num = $m->getNumParams();
					
					for ( $j = 0; $j < $param_num; $j++ )
					{
						$param_curr  = $m->getParam( $j );
						$param_val   = $param_curr->scalarval();
						eval( "\$param_value" . $j . "=\$param_val;" );
						$param_list .= "\$param_value" . $j . ",";
					}
					
					$param_list   = substr( $param_list, 0, -1 );
					$function_str = $methName . "($param_list)";
					eval( '$r=' . "$function_str;" );
					$ret_typ = gettype( $r );
					
					switch ( $ret_typ )
					{
						case "integer" : 
							$ret_typ = "int";
							break;
					
						case "double" :
							break;
					
						case "string" :
							break;
					
						case "array" :
							if ( key( $r ) == "0" )
								$ret_typ = "array";
							else 
								$ret_typ = "struct";
									
							break;
					
						default : 
							$ret_typ = "object";
							break;
					}
					
					$r = new SoapResp( new SoapVal( $r, "$ret_typ" ) );
				}
	  		}
			else
			{
				// else prepare error response
				$r = new SoapResp( 0, $soaperr["unknown_method"], $soapstr["unknown_method"] );
	  		}
		}
			
		return $r;
  	}

	/**
	 * @access public
	 */
	function echoInput()
	{
		global $HTTP_RAW_POST_DATA;

		// a debugging routine: just echos back the input
		// packet as a string value

		$r     = new SoapResp();
		$r->xv = new SoapVal( "'Aha said I: '" . $HTTP_RAW_POST_DATA, "string" );
		
		echo( $r->serialize() );
	}

	/**
	 * @access public
	 */	
	function soap_debugmsg( $m )
	{
		global $_soap_debuginfo;
		$_soap_debuginfo = $_soap_debuginfo . $m . "\n";
	}
} // END OF SoapServer

?>
