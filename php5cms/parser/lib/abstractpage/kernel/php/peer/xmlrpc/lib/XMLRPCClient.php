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


using( 'peer.xmlrpc.lib.XMLRPCResponse' );


/**
 * Class which creates and handles an XML-RPC client.
 *
 * @package peer_xmlrpc_lib
 */

class XMLRPCClient extends PEAR
{
	/**
	 * @access public
	 */
	var $clientMasquerade;
	
	/**
	 * the name or IP of the server to communicate with
	 * @access public
	 */
    var $Server;

	/**
	 * the path to the XML-RPC server
	 * @access public
	 */
    var $Path;    

	/**
	 * the port of the server to communicate with
	 * @access public
	 */
    var $Port;

	/**
	 * the username to use for authentification
	 * @access public
	 */
    var $Login;
    
	/**
	 * the password to use for authentification
	 * @access public
	 */
    var $Password;

	/**
	 * the error string
	 * @access public
	 */
    var $ErrorString;

	/**
	 * the error number
	 * @access public
	 */
    var $ErrorNumber;

	/**
	 * how long to wait for the call
	 * @access public
	 */
    var $TimeOut = 0;
		
	
	/**
	 * Constructor
	 *
	 * Will create a new XML RPC client which will communicate
	 * with the server given as argument.
	 * You can specify a port for communication, the default port is 80.
	 *
	 * @access public
	 */
    function XMLRPCClient( $server, $path, $port = 80 )
    {
		$this->clientMasquerade = ap_ini_get( "agent_name", "settings" );
		
        $this->Server = $server;
        $this->Path   = $path;
        $this->Port   = $port;
    }
	

	/**
	 * Sets the timeout in seconds for the connect call.
	 * The default is no timeout.
	 *
	 * @access public
	 */
    function setTimeOut( $timeout )
    {
        $this->TimeOut = $timeout;
    }

	/**
	 * Returns the timeout value.
	 *
	 * @access public
	 */
    function timeOut()
    {
        return $this->TimeOut;
    }
   
	/**
	 * Returns the error string.
	 *
	 * @access public
	 */
    function errorString()
    {
        return $this->ErrorString;
    }

	/**
	 * Will connect to the server and return the response as
	 * a XMLRPCResponse object.
	 * If an error occured false (0) is returned.
	 *
	 * @access public
	 */
    function &send( &$call, $useSSL = false )
    {
        $rawResponse = 0;
		
        if ( !$useSSL || !in_array( "curl", get_loaded_extensions() ) )
        {
            if ( get_class( $call ) == "xmlrpccall" )
            {
                if ( $Timeout != 0 )
                {
                    $fp = fsockopen(
						$this->Server,
						$this->Port,
						&$this->errorNumber,
						&$this->errorString,
						$this->TimeOut
					);
                }
                else
                {
                    $fp = fsockopen(
						$this->Server,
						$this->Port,
						&$this->errorNumber,
						&$this->errorString
					);
                }

                $payload =& $call->payload();

                // send the XML-RPC call
                if ( $fp != 0 )
                {
                    $authentification = "";
					
                    if ( ( $this->login() != "" ) )
   						$authentification = "Authorization: Basic " . base64_encode( $this->login() . ":" . $this->password() ) . "\r\n" ;
                
                    $HTTPCall =
						"POST " . $this->Path . " HTTP/1.0\r\n" .
						"User-Agent: " . $this->clientMasquerade . "\r\n" .
						"Host: " . $this->Server . "\r\n" .
						$authentification .
						"Content-Type: text/xml\r\n" .
						"Content-Length: " . strlen( $payload ) . "\r\n\r\n" .
						$payload;

					if ( !fputs( $fp, $HTTPCall, strlen( $HTTPCall ) ) )
                    {
                        $this->ErrorString = "Could not send the XML-RPC call. Could not write to the socket.";
                        return false;
                    }
                }
            
                $rawResponse = "";
                unSet( $rawResponse );
				
                // fetch the XML-RPC response
                while( $data = fread( $fp, 32768 ) )
					$rawResponse .= $data;
            
                // close the socket
                fclose( $fp );
            }
        }
        else
        {
            // Call was made with useSSL == true
            // to use this functionality, you must have cURL (curl.haxx.se) installed and compiled into PHP with --with-ssl enabled.
            if ( get_class( $call ) == "xmlrpccall" )
            {
                $URL = "https://" . $this->Server . ":" . $this->Port . $this->Path;
                $ch  = curl_init( $URL );
                
				if ( $Timeout != 0 )
 					curl_setopt( $ch, CURLOPT_TIMEOUT, $this->TimeOut );
					
                $payload =& $call->payload();
                
				// send the XML-RPC call
                if ( $ch != 0 )
                {
                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
					 
                    $HTTPCall =
						"POST " . $this->Path . " HTTP/1.0\r\n" .
						"User-Agent: " . $this->clientMasquerade . "\r\n" .
						"Host: " . $this->Server . "\r\n" .
						"Content-Type: text/xml\r\n" .
						"Content-Length: " . strlen( $payload ) ."\r\n";
						
                    if ($this->Username != "") 
						$HTTPCall .= "Authorization: Basic " .	base64_encode($this->Username . ":" . $this->Password) . "\r\n";
					
                    $HTTPCall .= "\r\n" . $payload;
                    curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $HTTPCall );
                    unSet( $rawResponse );
                    $rawResponse = curl_exec( $ch );
					
                    if ( !$rawResponse )
                    {
                        $this->ErrorString = "Could not send the XML-RPC call. Could not write to the socket.";
                        return false;
                    }
                }
				
                curl_close( $ch );
            }
        }
        
        $response = new XMLRPCResponse();
        $response->decodeStream( $rawResponse );
        
        return $response;
    }

	/**
	 * Set the login.
	 *
	 * @access public
	 */
    function setLogin( $value )
    {   
        $this->Login = $value;
    }

	/**
	 * Set the username.
	 *
	 * @access public
	 */
    function setPassword( $value )
    {
        $this->Password = $value;
    }

	/**
	 * Returns the login.
	 *
	 * @access public
	 */
    function login()
    {
        return $this->Login;
    }

	/**
	 * Returns the password.
	 *
	 * @access public
	 */
    function password()
    {
        return $this->Password;
    }
} // END OF XMLRPCClient

?>
