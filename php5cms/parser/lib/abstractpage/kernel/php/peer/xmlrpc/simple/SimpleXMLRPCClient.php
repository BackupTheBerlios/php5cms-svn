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


using( 'peer.xmlrpc.simple.SimpleXMLRPC' );


/**
 * Class for Clientside XML RPC.
 *
 * This class offers methods for Clientside Remote Procedure Calls.
 *
 * @package peer_xmlrpc_simple
 */

class SimpleXMLRPCClient extends SimpleXMLRPC
{	
    /**
     * The RPC Server.
     *
     * @access private
     * @var    string
     */
	var $t_host;
	
    /**
     * The RPC Targetfile.
     *
     * @access private
     * @var    string
     */
	var $t_target;

    /**
     * The Remote Procedure.
     *
     * @access private
     * @var    string
     */
	var $_procedure;

    /**
     * The RPC Parameters.
     *
     * @access private
     * @var    array
     */
	var $_param = array();		

	
    /**
     * Constructor
     *
     * This constructor initializes the class and, when a Target and Host are given initalizes the appropropriate Vars.
     *
     * @access    public
     * @param     string $host The RPC Server
     * @param     string $target The Target File
	 * @return 	 boolean true
     */
	function SimpleXMLRPCClient( $host = "", $target = "" ) 
	{
		$this->t_host   = $host;
		$this->t_target = $target;
	}
	

    /**
     * Initializes the Class Var $_procedure.
     *
     * This Method initializes the class Var $_procedure with the choosen Remote Procedure.
     *
     * @access   public
     * @param    string $procedure The choosen Remote Procedure
	 * @see		 $_procedure
     */
	function rpcProcedure( $procedure )
	{
		return $this->_procedure = $procedure;
	}
	
    /**
     * Initializes the Class Var $_param.
     *
     * This method initializes the class Var $_param with the choosen Remote Procedure Parameters.
     *
     * @access   public
	 * @param	 mixed	$param XMLRPC Param	
	 * @see		 $_param
     */
	function rpcParam( $param )
	{
		return $this->_param[] = $param;
	}
	
    /**
     * Adds Values to the Class Var $_param.
     *
     * This Methods adds Params to class Var $_param.
     *
     * @access   public
	 * @param	 mixed	$param XMLRPC Param
	 * @see		 $_param
     */
	function addRPCParam( $param )
	{
		return $this->_param[] = $param;
	}
	
    /**
     * Sends the XMLRPC and parses the Response.
     *
     * @access   public
	 * @return	 boolean
	 * @see		 parseXMLRPC(), _response(), parseMethodResponse()
     */
	function sendXMLRPC()
	{
		$this->parseXMLRPC( $this->_response() );
		$this->parseMethodResponse();
		
		return true;
	}
	
    /**
     * The XML RPC Response returned by the RPC Server $t_host.
	 *
	 * Returned as a XML File.
	 *
     * @access   public
     * @param    string $mode	You can choose flat for viewing the response without the HTTP headers for Content-type and Content-length.
	 * @see		 _response()
     */
	function getXMLResponse( $mode = false )
	{
		if ( $mode )
		{
			return $this->_response();
		}
		else
		{
			ob_start();
			echo( $this->_response() );
			$buffer = ob_get_contents();
			
			header( "Content-type: text/xml; charset=ISO-8859-1"  );
			header( "Content-length: " . strlen( $buffer ) . "\n" );	
		    
			ob_end_flush();	
		}
	}

	
	// private methods
	
    /**
     * The XML RPC Response returned by the RPC Server $t_host.
	 *
	 * Returns as String containing all the Values.
	 *
     * @access   private
     * @param    string $function Our RPC function
     * @param    string $param A Param of our RPC function
	 * @return 	 mixed	The XML Data with methodRespond
	 * @see		 _httpPost()
     */
	function _response()
	{
		$data = $this->_buildCall();
		
		$response_data = $this->_httpPost(
			$this->t_host, 
			$this->t_target, 
			"http://www.docuverse.de",
			$data
		);
		
		$xml_data = explode( "<?xml", $response_data );
		return stripslashes( $xml_data = "<?xml" . $xml_data[1] );
	}

	/**
     * This method builds our XML RPC in XML format.
	 *
     * @access   private
	 * @return 	 string	The Remote Procedure Call as defined in XML RPC Spec.
	 * @see		 _getType()
     */	
	function _buildCall()
	{
		$data_pre  = '<?xml version="1.0" encoding="ISO-8859-1"?>'."\n\n";
		$data_pre .= "<methodCall>\n";
		$data_pre .= "<methodName>" . $this->_procedure . "</methodName>\n";
		$data_pre .= "<params>\n";
		
		if ( is_array( $this->_param ) )
		{
			foreach( $this->_param as $param )
			{
				if ( $param != "" || !empty( $param ) )
				{
					$data_pre .= "<param>\n";
					$data_pre .= $param . "\n";
					$data_pre .= "</param>\n";
		
				}
			}
		}
		else
		{
			if( $this->_param != "" || !empty( $this->_param ) )
			{
				$data_pre .= "<param>\n";
				$data_pre .= $this->_param . "\n";
				$data_pre .= "</param>\n";
			}
		}
		
		$data_pre .="</params>\n";		
		$data_pre .="</methodCall>";
		
		return $data_pre;
	}

    /**
     * This method posts Data to the $t_host.
	 *
     * @access   private
     * @param    string $host The RPC Server
     * @param    string $path The Target File
	 * @param    string $referer The Fake Referer
	 * @param	 string $data_to_send Our RPC
	 * @return 	 mixed The Return Values of our RPC
	 * @see		 response(), getXMLResponse()
     */
	function _httpPost( $host, $path, $referer = "http://www.docuverse.de/", $data_to_send ) 
	{
		$fp = fsockopen( $host, 80 );
		
		$header  = "POST " . $path. " HTTP/1.0\r\n";
		$header .= "User-Agent: Abstractpage XMLRPC 1.0\r\n";
		$header .= "Host: ". $host  . "\r\n";
		$header .= "Content-Type: text/xml\r\n";
		$header .= "Content-Length: " . strlen( $data_to_send ) . "\r\n\r\n";
		$header .= $data_to_send;
	  
		if ( !fputs( $fp, $header, strlen( $header ) ) )
			return false;
	  
		$res = "";
		
		while ( !feof( $fp ) )
			$res .= fgets( $fp, 32768 );
	  
		fclose( $fp );
		return $res;
	}
} // END OF SimpleXMLRPCClient

?>
