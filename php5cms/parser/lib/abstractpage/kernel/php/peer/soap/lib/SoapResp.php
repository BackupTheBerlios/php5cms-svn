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
 
class SoapResp extends PEAR
{
	/**
	 * @access public
	 */
	var $xv;
	
	/**
	 * @access public
	 */
	var $fn;
	
	/**
	 * @access public
	 */
	var $fs;
	
	/**
	 * @access public
	 */
	var $hdrs;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
    function SoapResp( $val, $fcode = 0, $fstr = "" )
	{
		if ( $fcode != 0 )
		{
			$this->fn = $fcode;
			$this->fs = htmlspecialchars( $fstr );
			$this->xv = "Error Occur.";
		}
		else
		{
		    $this->xv = $val;
		}
    }


	/**
	 * @access public
	 */
	function faultCode()
	{
		return $this->fn;
	}

	/**
	 * @access public
	 */	
	function faultString()
	{
		return $this->fs;
	}

	/**
	 * @access public
	 */	
	function value()
	{
		return $this->xv;
	}

	/**
	 * @access public
	 */
    function serialize()
	{ 
    	global $soapTargetMethod;
		global $soapTargetId;
    	    	
		$rs = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
			"<SOAP-ENV:Envelope xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:xsi=\"http://www.w3.org/1999/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/1999/XMLSchema\">\n" .
			"<SOAP-ENV:Body>\n"."<ns1:".$soapTargetMethod."Response xmlns:ns1=\"".$soapTargetId."\" SOAP-ENV:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\">\n";
		
		if ( $this->fn )
		{
			$rs .= "<fault>
				  <value>
				    <struct>
				      <member>
				        <name>faultCode</name>
				        <value><int>" . $this->fn . "</int></value>
				      </member>
				      <member>
				        <name>faultString</name>
				        <value><string>" . $this->fs . "</string></value>
				      </member>
				    </struct>
				  </value>
				</fault>";
		}
		else
		{
			$rs .= $this->xv->serialize_response();
		}
		
		$rs .= "</ns1:" . $soapTargetMethod . "Response>\n" . "</SOAP-ENV:Body>\n</SOAP-ENV:Envelope>";
		return $rs;
    }
} // END OF SoapResp

?>
