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


using( 'peer.xmlrpc.lib.XMLRPCString' );
using( 'peer.xmlrpc.lib.XMLRPCDataTypeDecoder' );


/**
 * Handles a XML-RPC server call.
 *
 * @package peer_xmlrpc_lib
 */

class XMLRPCCall extends PEAR
{
    /**
	 * name of the method to call
	 * @access public
	 */
    var $MethodName;
    
    /**
	 * parameters to send with the method
	 * @access public
	 */
    var $ParameterList;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */ 
    function XMLRPCCall()
    {
        $this->clearParameters();
    }

	
	/**
	 * Sets the method name.
	 *
	 * @access public
	 */
    function setMethodName( $name )
    {
        $this->MethodName = $name;
    }

	/**
	 * Returns the method name.
	 *
	 * @access public
	 */
    function methodName( )
    {
        return $this->MethodName;
    }

	/**
	 * Adds a new parameter to the parameter list.
	 * The parameters can be of the types: XMLRPCString, XMLRPCInt ...
	 * If the value is a normal PHP type it will be decoded as a XMLRPCString.
	 * This function returns false if the parameter was not successful added
	 * to the parameter list.
	 *
	 * @access public
	 */
    function addParameter( $value )
    {
        $ret = false;
		
        switch ( get_class( $value ) )
        {
            case "xmlrpcstring" :
				$this->ParameterList[] = $value;
                $ret = true;                
			
            	break;
            
            case "xmlrpcint" :
                $this->ParameterList[] = $value;
                $ret = true;
				
				break;

            case "xmlrpcdouble" :
				$this->ParameterList[] = $value;
                $ret = true;

            	break;
            
            case "xmlrpcarray" :
				$this->ParameterList[] = $value;
                $ret = true;

            	break;

            case "xmlrpcbase64" :
				$this->ParameterList[] = $value;
				$ret = true;
				
				break;

            case "xmlrpcboolean" :
				$this->ParameterList[] = $value;
				$ret = true;
            	
				break;

            case "xmlrpcstruct" :
				$this->ParameterList[] = $value;
				$ret = true;
				
				break;

            default :
				if ( $value != "Object" )
				{
					$string = new XMLRPCString( $value );
                    $this->ParameterList[] =& $string;
					$ret = true;
                }
        }
        
        return $ret;
    }

	/**
	 * Returns the parameter list.
	 *
	 * @access public
	 */
    function parameterList()
    {
        return $this->ParameterList;
    }
    
	/**
	 * Clears the parameter list.
	 *
	 * @access public
	 */
    function clearParameters( )
    {
        $this->ParameterList = array();
    }
    
	/**
	 * Returns the call payload. This is the requst encoded
	 * as an XML-RPC call.   
	 *
	 * @access public
	 */
    function &payload()
    {
        $parameters = "";
		
        if ( count( $this->ParameterList ) > 0 )
        {
            $parameters = "<params>\n";

            foreach ( $this->ParameterList as $parameter )
				$parameters .= "<param>\n" . $parameter->serialize() . "</param>\n";                                  
                 
            $parameters .= "</params>";                 
        }
        
        $payload = "<?xml version=\"1.0\"?>\n" . "<methodCall>\n" . "<methodName>" . $this->MethodName . "</methodName>\n" . $parameters . "</methodCall>\n";
        return $payload;        
    }
	
	/**
	 * Decodes the XML-RPC stream.
	 * qdom_tree versiom
	 *
	 * @access public
	 */
    function decodeStream( $rawResponse )
    {
        // create a new decoder object
        $decoder = new XMLRPCDataTypeDecoder;
        $domTree =& qdom_tree( $rawResponse );

        foreach ( $domTree->children as $call )
        {
            if ( $call->name == "methodCall" )
            {
                foreach ( $call->children as $callItem )
                {
                    // method name
                    if ( $callItem->name == "methodName" )
                    {
                        foreach ( $callItem->children as $value )
                        {
                            if ( $value->name == "#text" )
 								$this->MethodName = $value->content;
                        }
                    }

                    // parameters
                    if ( $callItem->name == "params" && is_array( $callItem->children ) )
                    {
                        foreach ( $callItem->children as $param )
                        {
                            if  ( $param->name == "param" )
                            {
                                foreach ( $param->children as $value )
                                {
                                    if ( $value->name == "value" )
										$this->ParameterList[] = $decoder->decodeDataTypes( $value );
                                }
                            }
                        }
                    }
                }
            }
        }
    }
} // END OF XMLRPCCall

?>
