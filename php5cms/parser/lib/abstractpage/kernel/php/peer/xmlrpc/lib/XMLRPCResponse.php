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


using( 'peer.xmlrpc.lib.XMLRPCDataTypeDecoder' );
using( 'peer.xmlrpc.lib.XMLRPCStruct' );
using( 'peer.xmlrpc.lib.XMLRPCInt' );
using( 'peer.xmlrpc.lib.XMLRPCString' );


/**
 * Handles a XML-RPC server response.
 *
 * @package peer_xmlrpc_lib
 */

class XMLRPCResponse extends PEAR
{
	/**
	 * contains the result
	 * @access public
	 */
    var $Result;

	/**
	 * contains the error struct
	 * @access public
	 */
    var $Error;

	/**
	 * is true if the response is a fault
	 * @access public
	 */
    var $IsFault;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */ 
    function XMLRPCResponse()
    {
        $this->Result  = 0;
        $this->Error   = 0;
        $this->IsFault = false;        
    }

	
	/**
	 * Decodes the XML-RPC response stream and stores the result.
	 * You can get the result by calling the result() function.
	 * qdom version
	 *
	 * @access public
	 */
    function decodeStream( $stream )
    {
        // create a new decoder object
        $decoder =  new XMLRPCDataTypeDecoder;
		$stream  =  $this->stripHTTPHeader( $stream );
		$domTree =& qdom_tree( $stream );

		foreach ( $domTree->children as $response )
        {
            if ( $response->name == "methodResponse" )
            {                
                foreach ( $response->children as $params )
                {
                    if ( $params->name == "params" )
                    {
                        foreach ( $params->children as $param )
                        {
                            if ( $param->name == "param" )
                            {
                                foreach ( $param->children as $value )
                                {
                                    if ( $value->name == "value" )
										$this->Result =& $decoder->decodeDataTypes( $value );
                                }
                            }
                        }
                    }

                    if ( $params->name == "fault" )
                    {
                        foreach ( $params->children as $param )
                        {
                            if ( $param->name == "value" )
                            {
                                $this->IsFault = true;
                                $this->Error =& $decoder->decodeDataTypes( $param );
                            }
                        }
                    }
                }
            }
        }

        // could not decode the stream
        if ( $this->Result == 0 && $this->Error == 0 )
			$this->setError( 3, "Could not decode stream. Server error." );   
    }
	
	/**
	 * Sets the result value.
	 * The argument must be a valid
	 * XML-RPC datatype object. XMLRPCInt, XMLRPCString ...
	 *
	 * @access public
	 */
    function setResult( $result )
    {
        $this->Result = $result;
    }

	/**
	 * Sets an error message.
	 *
	 * @access public
	 */
    function setError( $faultCode, $faultString )
    {
        $this->IsFault = true;
		
        $this->Error = new XMLRPCStruct( array(
			"faultCode"   => new XMLRPCInt( $faultCode ),
			"faultString" => new XMLRPCString( $faultString ),
		) );
    }
    
	/**
	 * Returns the result of the response
	 * The result is a valid
	 * XML-RPC datatype object. XMLRPCInt, XMLRPCString ...
	 * If not false is returned
	 *
	 * @access public
	 */
    function result( )
    {
        return $this->Result;
    }

	/**
	 * Returns the response payload. This is the response encoded
	 * as an XML-RPC call.
	 *
	 * @access public
	 */
    function &payload()
    {
        $payload = "<?xml version=\"1.0\"?>";

        if ( $this->Error == "" )
        {
            $payload .= "<methodResponse><params><param>";
            $payload .= $this->Result->serialize();
            $payload .= "</param></params></methodResponse>";
        }
        else
        {
            $payload .= "<methodResponse><fault>";
            $payload .= $this->Error->serialize();
            $payload .= "</fault></methodResponse>";
        }

        return $payload;
    }

	
	// private methods
	
	/**
	 * Strips the header information from the HTTP raw response.
	 *
	 * @access private
	 */
    function &stripHTTPHeader( $data )
    {
        $start = strpos( $data, "<?xml version=\"1.0\"?>" );
        $data  = substr( $data, $start, strlen( $data ) - $start );
            
        return $data;
    }

	/**
	 * Returns true if the response is a fault.
	 *
	 * @access private
	 */
    function isFault()
    {
        return $this->IsFault;
    }
	
	/**
	 * Returns the fault code if there was an error. False if not.
	 *
	 * @access private
	 */
    function faultCode()
    {
        $ret = false;

        if ( $this->IsFault && ( get_class( $this->Error ) == "xmlrpcstruct" ) )
        {
            $error = $this->Error->value();
            $ret   = $error["faultCode"]->value();
        }
        
        return $ret;
    }

	/**
	 * Returns the fault string if there was an error. False if not.
	 *
	 * @access private
	 */
    function faultString()
    {
        $ret = false;

        if ( $this->IsFault && ( get_class( $this->Error ) == "xmlrpcstruct" )  )
        {
            $error = $this->Error->value();   
            $ret = $error["faultString"]->value();
        }
        
        return $ret;
    }
} // END OF XMLRPCResponse

?>
