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
using( 'peer.soap.lib.SoapVal' );
using( 'peer.soap.lib.SoapResp' );
using( 'util.Debug' );


/**
 * @package peer_soap_lib
 */
 
class SoapMsg extends SoapObject
{
	/**
	 * @access public
	 */
	var $xml;
	
	/**
	 * @access public
	 */
	var $payload;
	
	/**
	 * @access public
	 */
	var $methodname;
	
	/**
	 * @access public
	 */
	var $targetid;
	
	/**
	 * @access public
	 */
	var $params = array();

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function SoapMsg( $meth, $tid = "", $pars = 0 )
	{
		global $soapTargetId;
		global $soapTargetMethod;
		
		$this->debug = new Debug();
		$this->debug->Off();

		$this->methodname = $meth;
		$this->targetid   = $tid;
		
		$soapTargetId     = $tid;
		$soapTargetMethod = $meth;
		
		if ( is_array( $pars ) && sizeof( $pars ) > 0 )
		{
			for ( $i = 0; $i < sizeof( $pars ); $i++ ) 
				$this->addParam( $pars[$i] );
		}
  	}


	/**
	 * @access public
	 */	
	function xml_header()
	{
		return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
			"<SOAP-ENV:Envelope xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:xsi=\"http://www.w3.org/1999/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/1999/XMLSchema\">\n" .
			"<SOAP-ENV:Body>\n";
	}

	/**
	 * @access public
	 */
	function xml_footer()
	{
		return "</SOAP-ENV:Body>\n</SOAP-ENV:Envelope>\n";
	}

	/**
	 * @access public
	 */
	function createPayload()
	{
		$this->payload  = $this->xml_header();
		$this->payload .= "<ns1:$this->methodname xmlns:ns1=\"$this->targetid\" SOAP-ENV:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\">\n";
        
		for ( $i = 0; $i < sizeof( $this->params ); $i++ )
		{
			$p = $this->params[$i];
			$this->payload .= "" . $p->serialize( $i + 1 );
		}
	    
		$this->payload .= "</ns1:$this->methodname>\n";
		$this->payload .= $this->xml_footer();
		$this->payload  = str_replace( "\n", "\r\n", $this->payload );
	}

	/**
	 * @access public
	 */
  	function method( $meth = "" )
	{
		if ( $meth != "" )
			$this->methodname = $meth;
		
		return $this->methodname;
    }

	/**
	 * @access public
	 */
	function serialize()
	{
		$this->createPayload();
		return $this->payload;
	}

	/**
	 * @access public
	 */
	function addParam( $par )
	{
		$this->params[] = $par;
	}
	
	/**
	 * @access public
	 */
	function getParam( $i )
	{
		return $this->params[$i];
	}
	
	/**
	 * @access public
	 */
	function getNumParams()
	{
		return sizeof( $this->params );
	}

	/**
	 * @access public
	 */
    function parseResponseFile( $fp )
	{
		$ipd = "";

		while ( $data = fread( $fp, 32768 ) )
	    	$ipd .= $data;
		
		return $this->parseResponse( $ipd );
    }

	/**
	 * @access public
	 */	
    function parseResponse( $data = "" )
	{
		global $_xh;
		global $soaperr;
		global $soapstr;
		global $soap_defencoding;
	
		$this->xml = xml_parser_create( $soap_defencoding );
		xml_set_object( $this->xml, &$this );
		
		$_xh[$this->xml]         = array();
		$_xh[$this->xml]['st']   = ""; 
		$_xh[$this->xml]['cm']   = 0; 
		$_xh[$this->xml]['isf']  = 0; 
		$_xh[$this->xml]['ac']   = "";
		$_xh[$this->xml]['rt']   = "";
		$_xh[$this->xml]['flag'] = 0; // decide whether the first element of array

		xml_parser_set_option( $this->xml, XML_OPTION_CASE_FOLDING, false );
		xml_set_element_handler( $this->xml, "soap_se", "soap_ee" );
		xml_set_character_data_handler( $this->xml, "soap_cd" );
		xml_set_default_handler( $this->xml, "soap_dh" );

		$soap_value = new SoapVal();
		$hdrfnd = 0;
		
		$this->debug->Message( "<PRE>---GOT---\n" ."#######\n". htmlspecialchars( $data ) . "#######\n\n---END---\n</PRE>" );
		
		// see if we got an HTTP 200 OK, else bomb
		// but only do this if we're using the HTTP protocol.
		if ( ereg( "^HTTP", $data ) && !ereg( "^HTTP/[0-9\.]+ 200 ", $data ) )
		{
			$errstr = substr( $data, 0, strpos( $data, "\n" ) - 1 );
			$r = new SoapResp( 0, $soaperr["http_error"], $soapstr["http_error"] . " (" . $errstr . ")" );
			xml_parser_free( $this->xml );

			return $r;
		}
	
		if ( ( !$hdrfnd ) && ereg( "^(.*)\r\n\r\n", $data, $_xh[$this->xml]['ha'] ) )
		{
			$data   = ereg_replace( "^.*\r\n\r\n", "", $data );
			$hdrfnd = 1;
		}
		
	    if ( !xml_parse( $this->xml, $data, sizeof( $data ) ) )
		{
			if ( ( xml_get_current_line_number( $this->xml ) ) == 1 )   
				$errstr = "XML error at line 1, check URL";
			else
				$errstr = sprintf( "XML error: %s at line %d", xml_error_string( xml_get_error_code( $this->xml ) ), xml_get_current_line_number( $this->xml ) );
			
			$r = new SoapResp( 0, $soaperr["invalid_return"], $soapstr["invalid_return"] );
			xml_parser_free( $this->xml );
			
			return $r;
		}
		
		xml_parser_free( $this->xml );

		$this->debug->Message(
			"<PRE>---EVALING---[" .
			strlen( $_xh[$this->xml]['st'] ) . " chars]---\n" .
			htmlspecialchars( $_xh[$this->xml]['st'] ) .
			";\n---END---</PRE>"
		);
		
		if ( strlen( $_xh[$this->xml]['st'] ) == 0 )
		{
	  		// then something odd has happened
	  		// and it's time to generate a client side error
	  		// indicating something odd went on
	  		$r = new SoapResp( 0, $soaperr["invalid_return"], $soapstr["invalid_return"] );
		}
		else
		{
	  		eval( '$v=' . $_xh[$this->xml]['st'] . '; $allOK=1;' );
	  		
			if ( $_xh[$this->xml]['isf'] )
			{
				$f  = $v->structmem( "faultCode"   );
				$fs = $v->structmem( "faultString" );
				$r  = new SoapResp( $v, $f->scalarval(), $fs->scalarval() );
	  		}
			else
			{
				$r  = new SoapResp( $v );
	  		}
		}
		
		$r->hdrs = split( '\r?\n', $_xh[$this->xml]['ha'][1] );
		return $r;
  	}
} // END OF SoapMsg

?>
