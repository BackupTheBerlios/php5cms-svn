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


using( 'peer.xmlrpc.lib.XMLRPCCall' );
using( 'peer.xmlrpc.lib.XMLRPCResponse' );
using( 'peer.xmlrpc.lib.XMLRPCFunction' );


/**
 * Class which creates and handles an XML-RPC server.
 *
 * @package peer_xmlrpc_lib
 */

class XMLRPCServer extends PEAR
{
	/**
	 * @access public
	 */
	var $serverMasquerade;
	
	/**
	 * contains the raw HTTP post data
	 * @access public
	 */
    var $RawPostData;

	/**
	 * contains a list over registered functions
	 * @access public
	 */
    var $FunctionList;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */ 
    function XMLRPCServer()
    {
        global $HTTP_RAW_POST_DATA;
		
		$this->serverMasquerade = ap_ini_get( "agent_name", "settings" );
        $this->RawPostData = $HTTP_RAW_POST_DATA;
    }

	
	/**
	 * Processes the XML-RPC request and prints out the
	 * propper response.
	 *
	 * @access public
	 */
    function processRequest()
    {
        if ( $_SERVER["REQUEST_METHOD"] != "POST" )
			return PEAR::raiseError( "This web page does only onderstand POST methods." );
        
        $call = new XMLRPCCall();
        $call->decodeStream( $this->RawPostData );

        $functionWasFound    = false;
        $equalParameterCount = true;
		
        foreach ( $this->FunctionList as $function )
        {
            if ( $function->name() == $call->methodName() )
            {
                $func = $function->name();

                if ( function_exists( $func ) )
                {
                    $functionWasFound = true;

                    if ( count( $call->parameterList() ) == count( $function->parameters() ) )
						$result = $func( $call->parameterList() );
                    else
						$equalParameterCount = false;
                }
                else
                {
					return PEAR::raiseError( "Could not find function." );
                }
            }
        }

        if ( get_class( $result ) == "xmlrpcresponse" )
        {
            $response =& $result;
        }
        else
        {
            // do the server response
            $response = new XMLRPCResponse( );
            
            if ( $functionWasFound == false )
				$response->setError( 1, "Requested function not found." );
            
            if ( $equalParameterCount == false )
				$response->setError( 2, "Wrong parameter count for requested function." );
            
            $response->setResult( $result );
        }            

        $payload =& $response->payload();
        
        header( "Server: " . $this->serverMasquerade );
        header( "Content-type: text/xml" );
        header( "Content-Length: " . strlen( $payload ) );

        print( $payload );
    }

	/**
	 * Registers a new function on the server.
	 * Returns false if the function could not be registered.
	 *
	 * @access public
	 */
    function registerFunction( $name, $params = array() )
    {
        $func = new XMLRPCFunction( $name );

        foreach ( $params as $param )
			$func->addParameter( $param );

        $this->FunctionList[] = $func;
    }
} // END OF XMLRPCServer

?>
