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
 * @package peer_xmlrpc
 */
 
class BasicXMLRPCServer extends PEAR
{
	/**
	 * @access public
	 * @static
	 */
	function respond( $value, $error = null )
	{
		if ( isset( $error ) )
			$body = $value;
		else
			$body = "<methodResponse><params><param><value>" . BasicXMLRPCServer::convert( $value ) . "</value></param></params></methodResponse>";
	
		ob_start();
		
		echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
		echo $body;
		
		header( "Content-Type: text/xml" );
		header( "Server: Abstractpage XMLRPC Server 1.0" );
		
		ob_end_flush();
	}

	/**
	 * @access public
	 * @static
	 */
	function convert( $var )
	{
		if ( is_null( $var ) )
			return BasicXMLRPCServer::booleanToXMLRPC( false );
	
		if ( is_bool( $var ) )
			return BasicXMLRPCServer::booleanToXMLRPC( $var );
	
		if ( is_string( $var ) )
			return BasicXMLRPCServer::stringToXMLRPC( $var );
	
		if ( is_long( $var ) )
			return BasicXMLRPCServer::intToXMLRPC( $var );
	
		if ( is_numeric( $var ) )
			return BasicXMLRPCServer::doubleToXMLRPC( $var );
	
		if ( is_array( $var ) )
		{
			if ( $var[0] || count( $var ) == 0 )
				return BasicXMLRPCServer::arrayToXMLRPC( $var );
			else
				return BasicXMLRPCServer::structToXMLRPC( $var );
		}
	}

	/**
	 * @access public
	 * @static
	 */
	function structToXMLRPC( $struct )
	{
		$retstr = "<struct>";
	
		foreach ( $struct as $key => $value )
			$retstr .= "<member><name>$key</name><value>" . BasicXMLRPCServer::convert( $value ) . "</value></member>";
	
		$retstr .= "</struct>";
		return retstr;
	}

	/**
	 * @access public
	 * @static
	 */
	function stringToXMLRPC( $string )
	{
		return "<string>$string</string>";
	}

	/**
	 * @access public
	 * @static
	 */
	function intToXMLRPC( $int )
	{
		return "<int>$int</int>";
	}

	/**
	 * @access public
	 * @static
	 */
	function doubleToXMLRPC( $double )
	{
		return "<double>$double</double>";
	}

	/**
	 * @access public
	 * @static
	 */
	function booleanToXMLRPC( $bool )
	{
		$val = 0;
	
		if ( $bool )
			$val = 1;
	
		return "<boolean>$val</boolean>";
	}

	/**
	 * @access public
	 * @static
	 */
	function arrayToXMLRPC( $arr )
	{
		$retstr = "<array><data>";
	
		for ( $i = 0; $i < count( $arr ); $i++ )
			$retstr .= "<value>" . BasicXMLRPCServer::convert( $arr[$i] ) . "</value>";
	
		return $retstr . "</data></array>";
	}

	/**
	 * @access public
	 * @static
	 */
	function receive()
	{
		global $methodName, $params, $j, $HTTP_RAW_POST_DATA;
	
		$parser = xml_parser_create();
		xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
		xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE,   1 );
		xml_parse_into_struct( $parser, $HTTP_RAW_POST_DATA, $data, $tags );
		xml_parser_free( $parser );

		BasicXMLRPCServer::toObject( $data );
	
		if ( $methodName == "system.multicall" )
			$r = BasicXMLRPCServer::multicall( $params );
		else if ( BasicXMLRPCServer::validateMethodName( $methodName ) )
			$r = eval( "return $methodName(\$params);" );
		else
			BasicXMLRPCServer::throwError( 0, "Incorrect method name: " . $params[$i]["methodName"] );
	
		return BasicXMLRPCServer::respond( $r );
	}

	/**
	 * @access public
	 * @static
	 */
	function multicall( $params )
	{
		global $methodName;
		
		$responds = array();
		$params = $params[0];

		for ( $i = 0; $i < count( $params ); $i++ )
		{
			if ( BasicXMLRPCServer::validateMethodName( $params[$i]["methodName"] ) )
			{
				$ps = $params[$i]["params"];
				$responds[] = eval( "return $methodName(\$ps);" );
			}
			else
			{
				BasicXMLRPCServer::throwError( 0, "Incorrect method name: " . $params[$i]["methodName"] . " while doing multicall" );
			}
		}
	
		return $responds;
	}

	/**
	 * @access public
	 * @static
	 */
	function throwError( $nr, $msg )
	{
		$msg = "<methodResponse><fault><value><struct><member><name>faultCode</name><value><int>" . $nr . 
			 "</int></value></member><member><name>faultString</name><value><string>" . $msg .
			 "</string></value></member></struct></value></fault></methodResponse>";
	
		BasicXMLRPCServer::respond( $msg, true );
		die;
	}

	/**
	 * @access public
	 * @static
	 */
	function validateMethodName( $name )
	{
		global $fList, $methodName;
	
		/*
		The string may only contain identifier characters, 
		upper and lower-case A-Z, the numeric characters, 0-9, 
		underscore, dot, colon and slash. 
		*/
		if ( preg_match( "/^[A-Za-z0-9\._\/:]+$/", $name ) )
		{
			if ( $fList[$name] )
			{ 
				$methodName = $fList[$name];
				return true;
			}
		}

		return false;
	}

	/**
	 * @access public
	 * @static
	 */
	function toObject( $data )
	{
		global $methodName, $params, $j;
	
		switch ( $data[$j]["tag"] )
		{
			case "methodCall":
				if ( $data[$j]["type"] == "close" )
					return;
			
				$j++; // methodName
				
				if ( $data[$j]["tag"] != "methodName" )
					BasicXMLRPCServer::throwError( 0, "Unknown MethodName" );
				else
					$methodName = $data[$j]["value"];
				
				$j++; // params
				$j++; // param
			
				// params
				while ( $data[$j]["type"] != "close" )
					$params[] = BasicXMLRPCServer::toObject( $data );
			
				return true;
		
			case "param":
				$j++; // next...
				$val = BasicXMLRPCServer::toObject( $data );
				$j++; // next...
			
				return $val;
		
			case "string":
				$val = $data[$j]["value"];
				$j++; // next...
			
				return $val;
		
			case "int":
		
			case "i4":
		
			case "double":
				$val = intval( $data[$j]["value"] );
				$j++; // next...
			
				return $val;
		
			case "datetime.iso8601":
				/*
				Have to read the spec to be able to completely 
				parse all the possibilities in iso8601
				07-17-1998 14:08:55
				19980717T14:08:55
				*/
			
				$val = strtotime( $data[$j]["value"] );
				$j++;//next...
			
				return $val;
		
			case "array":
				$j++; // data open
				$val = array();
			
				if ( $data[$j]["tag"] == "data" && $data[$j]["type"] == "open" )
				{
					$j++; // value
				
					while ( $data[$j]["type"] != "close" )
						$val[] = BasicXMLRPCServer::toObject( $data );
				}

				$j++; // array close
				$j++; // next...
			
				return $val;
		
			case "struct":
				$j++; // member open
				$val = array();
			
				while ( $data[$j]["type"] != "close" )
				{
					$j++; // name
					$name = $data[$j]["value"];
					$j++; // value
					$val[$name] = BasicXMLRPCServer::toObject($data);
					$j++; // member open || struct close
				}

				$j++; // next...
				return $val;
		
			case "boolean":
				$val = $data[$j]["value"];
				$j++; // next...

				if ( intval( $val ) )
					return true;
			
				return false;
		
			case "base64":
				return "base64 cannot be processed at this point in time";
		
			case "value":
				if ( $data[$j]["type"] == "complete" )
				{
					$val = $data[$j]["value"];
					$j++; // next..
				
					return $val;
				}
			
				$j++; // next..
				$val = BasicXMLRPCServer::toObject( $data );
				$j++; // next

				return $val;
		
			default:
				BasicXMLRPCServer::throwError( 0, "unknown tagname: " . $data[$j]["tag"] );
		}
	}
} // END OF BasicXMLRPCServer

?>
