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
 * Class XML RPC
 *
 * This class offers methods for XML Remote Procedure Calls
 *
 * @package peer_xmlrpc_simple
 */

class SimpleXMLRPC extends PEAR
{
    /**
     * The RP Call Param.
     *
     * @access private
     * @var    array
     */	
	var	$_method_params = array();
	
    /**
     * XMLRPC Values in the appropriate Format.
     *
     * @access private
     * @var    string
     */
	var $_xmlrpcvalues;
	
    /**
     * Output from xml_parse_into_struct(). Contains the Values.
     *
     * @access private
     * @var    array
	 * @see	parseXMLRPC()	
     */
	var $_xml_values;
	
    /**
     * Output from xml_parse_into_struct(). Contains the Tags.
     *
     * @access private
     * @var    array
	 * @see	parseXMLRPC()
     */
	var $_xml_tags;
	
	
    /**
     * This method parses the Remote Procedure Call or Response in XML format.
	 *
     * @access   public
     * @param    string $xml_rpc The XMLRPC.
	 * @return 	 boolean
	 * @see		 _xml_tags, _xml_values
     */
	function parseXMLRPC( $xml_rpc )
	{
		$xml_rpc = stripslashes( $xml_rpc );
		$xml_rpc = eregi_replace( "\n|\n\r|\r|\r\n|\t", "", $xml_rpc );
		
		$p = xml_parser_create();
		xml_parser_set_option( $p, XML_OPTION_CASE_FOLDING, 0 );
		xml_parse_into_struct( $p, $xml_rpc, $values, $tags );
		xml_parser_free( $p );
		
		$this->_xml_values = $values;
		$this->_xml_tags   = $tags;
	}
	
    /**
     * This method parses the Remote Procedure Call in XML format
	 *
     * @access   public
     * @param    string $xml_rpc The XMLRPC.
	 * @return 	 boolean
	 * @see		 _xml_tags, _xml_values, _method_params
     */
	function parseMethodCall()
	{
		// eval method name
		for ( $i = 0; $i < count( $this->_xml_values ); $i++ )
		{
			if ( $this->_xml_values[$i]["tag"] == "methodName" && $this->_xml_values[$i]["type"] == "complete" )
				$method_tag_content = $this->_xml_values[$i]["value"];
			else
				$this->_errorResponse( "No Method specified!", 0 );
		}
		
		// END for
		// func->"sample.sumAndDifference"
		//		-> package = sample
		//		-> method = sumAndDifference
						
		if ( eregi( "\.", $method_tag_content ) )
		{
			$method_tag_content   = explode(".", $method_tag_content);
			$this->_rpc_package   = $method_tag_content[0];
			$this->_xmlrpc_method = $method_tag_content[1];				
		}
		else
		{
			$this->_rpc_package   = $this->_user_package;
			$this->_xmlrpc_method = $method_tag_content;
		}
			
		// 2. create $xml_method_params[]
		for ( $i = 0; $i < count( $this->_xml_tags["param"] ); $i++ )
			$offset[] = $this->_xml_tags["param"][$i];
		
		$k = 0;
		for ( $j = 0; $j < count( $offset ); $j++ )
		{
			$this->_method_params[$k] = array_slice($this->_xml_values, ( $offset[$j] ), ( $offset[( $j + 1 )] - $offset[$j] + 1 ) );
			
			array_pop( $this->_method_params[$k] );		// delete last word   (<param>)
			array_shift( $this->_method_params[$k] );	// delete first value (<param>)
			
			$k++;
			$j++;
		}
		
		return true;
	}
	
    /**
     * This method parses the Remote Procedure Response in XML format.
	 *
     * @access   public
     * @param    string $xml_rpc The XMLRPC.
	 * @return 	 boolean
	 * @see		 _xml_tags, _xml_values, _method_params
     */
	function parseMethodResponse()
	{
		$offset = array();
		
		// 1. create $xml_method_params[]
		for ( $i = 0; $i < count( $this->_xml_tags["param"] ); $i++ )
			$offset[] = $this->_xml_tags["param"][$i];
		
		$k = 0;
		for ( $j = 0; $j < count( $offset ); $j++ )
		{
			$this->_method_params[$k] = array_slice($this->_xml_values, ( $offset[$j] ), ( $offset[( $j + 1 )] - $offset[$j] + 1 ) );
			
			array_pop( $this->_method_params[$k] );		// delete last word   (<param>)
			array_shift( $this->_method_params[$k] );	// delete first value (<param>)		
            
			$k++;
			$j++;
		}
		
		return true;
	}
	
    /**
     * This method returns the XMLRPC Values in the specified Format.
	 *
     * @access   public
     * @param    string $value The XMLRPC Value.
     * @param    string $type The Type of the XMLRPC Value ("struct", "array", "scalar").
	 * @return 	 void
	 * @see		 _addStruct(), _addValue(), _addArray()
     */
	function xmlrpcvalue( $value, $type )
	{
		switch( $type )
		{
			case "struct":
				$this->_xmlrpcvalues = $this->_addStruct( $value );
				break;
			
			case "array":
				$this->_xmlrpcvalues = $this->_addArray( $value );
				break;
			
			default:
				$this->_xmlrpcvalues = $this->_addValue( $value, $type );
				break;
		}
		
		return "<value>" . $this->_xmlrpcvalues . "</value>";
	}
	
    /**
     * This method returns Values of given <struct> Member.
	 *
	 * If an additional $position param is defined only the Value at defined position is returned.
	 *
     * @access   public
	 * @param	 int	$param	Which Param?
	 * @param	 int $member	Which Member
	 * @param	 mixed $position if specified the returned value is a string with the according Value
	 * @return	 mixed <struct> Member Value(s)
	 * @see		 $_method_params
     */	
	function getStructMemberValues( $param = 0, $member,  $type = false )
	{
		for ( $j = 0; $j < count( $this->_method_params[$param] ); $j++ )
		{
			if ( $this->_method_params[$param][$j]["tag"] == "name" && $this->_method_params[$param][$j]["value"] == $member )
			{
				if ( $type == true )
				{
					$ret_array[] = array(
						"type" => $this->_method_params[$param][$j+2]["tag"],
						"value"=> $this->_method_params[$param][$j+2]["value"]
					);
				}
				else
				{
					$ret_array[] = $this->_method_params[$param][$j+2]["value"];
				}
			}
		}
		
		return $ret_array;
	}

    /**
     * This method returns all Values of <struct>.
	 *
	 * If $position is defined only the Value at defined position is returned.
	 *
     * @access   public
	 * @param	 int $param Which Param?
	 * @param	 boolean $type If type is false, the array returned does not contain the Type of the returned value (i.e: $structvalue[0] = $val). If $type is true the array returned contains the type of the vlaue (i.e. $structvalue[0] = array($type=>$val) )
	 * @return	 mixed <struct> Member Value(s)
	 * @see		 $_method_params
     */	
	function getAllStructValues( $param = 0, $type = false )
	{
		for ( $j = 0; $j < count( $this->_method_params[$param] ); $j++ )
		{
			if ( $this->_method_params[$param][$j]["tag"] == "name" )
			{
				if ( $type == true )
				{
					$structvalues[] = array(
						"type"  =>$this->_method_params[$param][$j+2]["tag"],
						"value" =>$this->_method_params[$param][$j+2]["value"],
						"name"  =>$this->_method_params[$param][$j]["value"]
					);
				}
				else
				{
					$structvalues[] = $this->_method_params[$param][$j+2]["value"];
				}
			}
		}
		
		return $structvalues;
	}
	
    /**
     * This method returns the Number of <param> in <params>.
	 *
     * @access   public
	 * @return	 int Number of params
	 * @see		 $_method_params
     */
	function getNoParams()
	{
		$no = count( $this->_method_params );
		return $no;
	}

    /**
     * This method returns Array Values.
	 *
     * @access   public
     * @param    int $param Which param holds the Array
	 * @return	 mixed Array Values
	 * @see		 $_method_params
     */
	function getArrayValues( $param = "0", $position = "all", $type = false )
	{
		for ( $j = 0; $j < count( $this->_method_params[$param] ); $j++ )
		{
			if ( $this->_method_params[$param][$j]["tag"] == "data" && $this->_method_params[$param][$j]["type"] == "open" )
			{
				for ( $i = ( $j + 1 ); $i < count( $this->_method_params[$param] ); $i++ )
				{
					if ( $this->_method_params[$param][$i]["tag"] == "value" && $this->_method_params[$param][$i]["type"] == "complete" )
					{
						$arrayvalues[] = array(
							"type"  => "string",
							"value" =>	$this->_method_params[$param][$i]["value"]
						);
					}
					else
					{
						if ( $this->_method_params[$param][$i]["tag"]!="value" && !empty( $this->_method_params[$param][$i]["value"] ) )
						{
							if ( $type == true )
							{
								$arrayvalues[] = array(
									"type"  => $this->_method_params[$param][$i]["tag"],
									"value" => $this->_method_params[$param][$i]["value"]
								);
							}
							else
							{
								$arrayvalues[] = $this->_method_params[$param][$i]["value"];
							}
						}
					}
						
					if ( $this->_method_params[$param][$j]["tag"] == "data" && $this->_method_params[$param][$j]["type"] == "close" ) 
						break;
				}
			}
		}
		
		if ( $position != "all" )
			return $arrayvalues[$position];
		else
			return $arrayvalues;
	}

    /**
     * This method returns a Scalar Value at specified Position.
	 *
     * @access   public
     * @param    string $param Which param holds the Skalar
	 * @param	 string $position of the Skalar value	
	 * @return	 mixed Array Values
	 * @see		 $_method_params
     */
	function getScalarValue( $param = 0, $position = "all", $type = false )
	{
		$skalar = array();
		
		for ( $j = 0; $j < count( $this->_method_params[$param] ); $j++ )
		{
			if ( $this->_method_params[$param][$j]["tag"] == "value" && $this->_method_params[$param][$j]["type"] == "open" )
			{
				if ( $type == true )
				{
					$skalar[$j] = array(
						"type"  => $this->_method_params[$param][$j+1]["tag"],
						"value" => $this->_method_params[$param][$j+1]["value"]
					);
				}
				else
				{
					$skalar[$j] = 	$this->_method_params[$param][$j+1]["value"];
				}
			}
			else if ( $this->_method_params[$param][$j]["tag"] == "value" && $this->_method_params[$param][$j]["type"] == "complete" )
			{
				if ( $type == true )
				{
					$skalar[$j] = array(
						"type"  => "string",
						"value" => $this->_method_params[$param][$j]["value"]
					);
				}
				else
				{
					$skalar[$j] = $this->_method_params[$param][$j]["value"];
				}
			}
		}
		
		if ( $position != "all" )
    		return $skalar[$position];
		else
    		return $skalar;
	}

    /**
     * This method returns all the RPC Params.
	 *
     * @access   public
     * @param    boolean $type Set to 'true' if you need the Type of the returned Value
	 * @return	 array Params
	 * @see		 $_method_params
     */
	function getParams( $type = false )
	{
		$skalar = array();
		
		for ( $i = 0; $i < count( $this->_method_params ); $i++ )
		{
			for ( $j = 0; $j < count( $this->_method_params[$i] );$j++ )
			{
				if ( ( $this->_method_params[$i][$j]["tag"]   == "value"  ) &&
					 ( $this->_method_params[$i][$j]["type"]  == "open"   ) && (
					 ( $this->_method_params[$i][$j+1]["tag"] != "array"  ) &&
					 ( $this->_method_params[$i][$j+1]["tag"] != "struct" ) ) )
				{
					if ( $type == true )
					{
						$skalar[] = array(
							"type"  => $this->_method_params[$i][$j+1]["tag"],
							"value" => $this->_method_params[$i][$j+1]["value"]
						);
					}
					else
					{
						$skalar[] = $this->_method_params[$i][$j+1]["value"];
					}
				}
				else if ( ( $this->_method_params[$i][$j]["tag"]   == "value"    ) &&
						  ( $this->_method_params[$i][$j]["type"]  == "complete" ) && (
						  ( $this->_method_params[$i][$j+1]["tag"] != "array"    ) &&
						  ( $this->_method_params[$i][$j+1]["tag"] != "struct"   ) ) )
				{
					if ( $type == true )
					{
						$skalar[] = array(
							"type"  => "string",
							"value" => $this->_method_params[$i][$j]["value"]
						);
					}
					else
					{
						$skalar[] = $this->_method_params[$i][$j]["value"];
					}
				}
			}
		}
		
		return $skalar;
	}

	
	// private methods
	
    /**
     * This method returns the XMLRPC Scalar-Values in the specified Format.
	 *
     * @access   private
     * @param    string $value The XMLRPC Value.
     * @param    string $type The Type of the XMLRPC Value ("int", "double", ...)
	 * @return 	 void
	 * @see		 _addStruct(), _xmlrpcvalues(), _addArray()
     */
	function _addValue( $value, $type = "string" ) 
	{
		$val = "<" . $type . ">" . $value . "</" . $type . ">";
		return $val;
	}
	
    /**
     * This method returns the XMLRPC Array-Values in the specified Format.
	 *
     * @access   private
     * @param    string $array The XMLRPC Value.
	 * @return 	 void
	 * @see		 _addStruct(), _addValue(), _xmlrpcvalues()
     */
	function _addArray( $array )
	{
    	$rpc_arr  = "";
		$pretag   = "<array>\n";
		$pretag  .= "<data>\n";
		$posttag  = "</data>\n";
		$posttag .= "</array>\n";
		
		foreach ( $array as $values )
			$rpc_arr .= $values."\n";		
		
		return $pretag . $rpc_arr . $posttag;
	}
	
    /**
     * This method returns the XMLRPC Struct-Values in the specified Format.
	 *
     * @access   private
     * @param    string $array The XMLRPC Value.
	 * @return 	 void
	 * @see		 _addArray(), _addValue(), _xmlrpcvalues()
     */
	function _addStruct( $array )
	{
		$pretag  = "<struct>\n";
		$posttag = "</struct>\n";
		
		foreach ( $array as $key => $values )
		{
			$rpc_struct .= "<member>\n";
			$rpc_struct .= "<name>" . $key . "</name>\n";
			$rpc_struct .= $values."\n";		
			$rpc_struct .= "</member>\n";
		}
		
		return $pretag . $rpc_struct . $posttag;
	}
	
    /**
     * This Method gets the type of the Value and returns the appropriate tag.
	 *
     * @access   private
     * @param    mixed $val	The Var that needs to be checked
     * @param    string $tagtype Opening or Closing Tag?
	 * @return	 string Tag with typedefinition
     */		
	function _getType( $val, $tagtype ) 
	{
		switch( $tagtype )
		{
			case "open":
				$pre_tag = "<";
				break;
			
			case "close":
				$pre_tag = "</";
				break;
		}
		
		if ( is_bool( $val ) )
			$tag = "boolean>";
		else if ( ereg( "([0-9]{4})([0-9]{2})([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})", $val ) )
			$tag ="dateTime.iso8601>";
		else if ( is_double( $val ) )
			$tag = "double>";
		else if ( is_int( $val ) )
			$tag = "int>";
		else if ( is_string( $val ) )
			$tag = "string>";
		else if ( empty( $val ) && $val != 0 )
			$tag = "nil>";
		else
			$tag = "string>";
		
		$tag = $pre_tag . $tag;
		return $tag;
	}	
	
	/**
	 * Returns the appropriate Error Message.
	 *
	 * @access	private
	 * @param	string	$faultString Error Message
	 * @param	int	$faultCode	Error Code
	 * @return	mixed	XML Error Message
	 * @see 	_execRPC()
	 */
	function _errorResponse( $faultString = "Unknown Error", $faultCode = "0" )
	{
		$ON_ERROR ='
			<methodResponse>
				<fault>
    			  <value>
			         <struct>
	    		        <member>
		               <name>faultCode</name>
		               <value><int>' . $faultCode . '</int></value>
		               </member>
		            <member>
		               <name>faultString</name>
		               <value><string>' . $faultString . '</string></value>
		               </member>
		            </struct>
		         </value>
		      </fault>
		   </methodResponse>';
		   
		return $ON_ERROR;
	}	
} // END OF SimpleXMLRPC

?>
