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
using( 'peer.xmlrpc.lib.XMLRPCInt' );
using( 'peer.xmlrpc.lib.XMLRPCDouble' );
using( 'peer.xmlrpc.lib.XMLRPCBool' );
using( 'peer.xmlrpc.lib.XMLRPCBase64' );
using( 'peer.xmlrpc.lib.XMLRPCDateTime' );
using( 'peer.xmlrpc.lib.XMLRPCArray' );
using( 'peer.xmlrpc.lib.XMLRPCStruct' );


/**
 * Handles decoding of XML-RPC data types.
 *
 * @package peer_xmlrpc_lib
 */

class XMLRPCDataTypeDecoder extends PEAR
{
	/**
	 * Decodes the datatypes from the XML-RPC stream and returns the
	 * appropriate value as an XMLRPC datatype object.
	 *
	 * @access public
	 */
    function &decodeDataTypes( $value )
    {
        $result = 0;
		
        // check the type
        foreach ( $value->children as $type )
        {
            switch ( $type->name )
            {
                // if no type is specified make it a string
                case "#text" :
				
                case "string" :
					$result =& $this->decodeString( $type );
					break;

                case "i4" :
				
                case "int" :
					$result =& $this->decodeInt( $type );
					break;

                case "double" : 
					$result =& $this->decodeDouble( $type );
					break;
                                
                case "boolean" :
					$result =& $this->decodeBoolean( $type );
					break;

                case "base64" :
					$result =& $this->decodeBase64( $type );
					break;

                case "dateTime.iso8601" :
					$result =& $this->decodeDateTime( $type );
					break;

                case "array" :
					$result =& $this->decodeArray( $type );
					break;

                case "struct" :
					$result =& $this->decodeStruct( $type );
					break;
            }
        }
        
        return $result;
    }

	/**
	 * @access public
	 */
    function &decodeString( $type )
    {
        $result = 0;
		
        if ( count( $type->children ) > 0 )
        {
            foreach ( $type->children as $content )
            {
                if ( $content->name == "#text" )
					$result = new XMLRPCString( $content->content );
            }
        }
        else
        {
            $result = new XMLRPCString( $type->content );
        }

        return $result;
    }

	/**
	 * @access public
	 */    
    function &decodeInt( $type )
    {
        $result = 0;
		
        foreach ( $type->children as $content )
        {
            if ( $content->name == "#text" )
				$result = new XMLRPCInt( $content->content );
        }
		
        return $result;        
    }

	/**
	 * @access public
	 */
    function &decodeDouble( $type )
    {
        $result = 0;
		
        foreach ( $type->children as $content )
        {
            if ( $content->name == "#text" )
				$result = new XMLRPCDouble( $content->content );
        }
		
        return $result;
    }

	/**
	 * @access public
	 */
    function decodeBoolean( $type )
    {
        $result = 0;
		
        foreach ( $type->children as $content )
        {
            if ( $content->name == "#text" )
            {
                $bool = new XMLRPCBool( );
                $bool->decode( $content->content );
                $result = $bool;
            }
        }
		
        return $result;        
    }

	/**
	 * @access public
	 */
    function decodeBase64( $type )
    {
        $result = 0;
		
        foreach ( $type->children as $content )
        {
            if ( $content->name == "#text" )
            {
                $bin = new XMLRPCBase64( );
                $bin->decode( $content->content );
                $result = $bin;
            }
        }
		
        return $result;        
    }

	/**
	 * @access public
	 */
    function decodeDateTime( $type )
    {
        $result = 0;
		
        foreach ( $type->children as $content )
        {
            if ( $content->name == "#text" )
            {
                $date = new XMLRPCDateTime( );
                $date->decode( $content->content );
                $result = $date;
            }
        }
		
        return $result;
    }

	/**
	 * @access public
	 */
    function decodeArray( $type )
    {
        $array = array();
		
        if ( count( $type->children )  > 0)
		{
        	foreach ( $type->children as $data )
        	{
            	if ( $data->name == "data" )
            	{
                	if ( count( $data->children ) > 0 )
					{
                		foreach ( $data->children as $dataValue )
                		{
                    		if ( $dataValue->name == "value" )
								$array[] = $this->decodeDataTypes( $dataValue );

                    		if ( $dataValue->name == "array" )
								$array[] = $this->decodeDataTypes( $dataValue );
						}
                	}
            	}
        	}
		}
		
		return new XMLRPCArray( $array );
    }

	/**
	 * @access public
	 */
    function decodeStruct( $type )
    {
        $array = array();

        if ( count( $type->children ) > 0 )
		{
        	foreach ( $type->children as $member )
        	{
            	if ( $member->name == "member" )
            	{
                	unset( $memberName );
                	unset( $memberData );
                
                	foreach ( $member->children as $memberValue )
                	{
                    	if ( $memberValue->name == "name" )
                    	{
                        	foreach ( $memberValue->children as $content )
                        	{
                            	if ( $content->name == "#text" )
									$memberName = $content->content;
                        	}
                    	}

                    	if ( $memberValue->name == "value" )
 							$memberData = $this->decodeDataTypes( $memberValue );
                	}

                	$array = array_merge( $array, array( $memberName => $memberData ) );
            	}
        	}
		}
		
        return new XMLRPCStruct( $array );
    }
} // END OF XMLRPCDataTypeDecoder

?>
