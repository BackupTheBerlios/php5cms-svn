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
 * @package xml
 */
 
class PathParser extends PEAR
{
	/**
	 * @access public
	 */
	var $paths;
	
	/**
	 * @access public
	 */
	var $parser;  
	
	/**
	 * @access public
	 */
	var $error;
	
	/**
	 * @access public
	 */
	var $path;
	
	/**
	 * @access public
	 */
	var $context = array();
	

	/**
	 * Constructor
	 *
	 * @access public
	 */
	function PathParser()
	{
		$this->init();
	}
  

	/**
	 * @access public
	 */   
	function init()
	{
		$this->paths  = array(); 
		$this->parser = xml_parser_create_ns( "", '^' );
		
		xml_set_object( $this->parser, &$this );
		xml_parser_set_option( $this->parser, XML_OPTION_CASE_FOLDING, false );
		xml_set_element_handler( $this->parser, "_startElement", "_endElement" );
		xml_set_character_data_handler( $this->parser, "_data" );
	}
  
  	/**
	 * @access public
	 */
	function getError()
	{
		return $this->error; 
	}
    
	/**
	 * @access public
	 */
	function parseFile( $xml )
	{
		if ( !( $fp = fopen( $xml, "r" ) ) )
		{
			$this->error = "Cannot open $rddl";
			return false;
		}
		
		while ( $data = fread( $fp, 4096 ) )
		{
			if ( !xml_parse( $this->parser, $data, feof( $fp ) ) )
			{
				$this->error = "XML error: " . xml_error_string( xml_get_error_code( $this->parser ) ) . " at line " . xml_get_current_line_number( $this->parser );
				return false;                    
			}
		}
		
		xml_parser_free( $this->parser );
		return true;
	}
  
  	/**
	 * @access public
	 */
	function parse( $data, $is_final )
	{
		$ret = xml_parse( $this->parser, $data, $is_final );
		
		if ( $is_final )
			xml_parser_free( $this->parser ); 
    
		if ( !$ret )
			$this->error = "XML error: " . xml_error_string( xml_get_error_code( $this->parser ) ) . " at line " . xml_get_current_line_number( $this->parser );
    
		return $ret;
	}
  
  	/**
	 * @access public
	 */
	function setHandler( $path, $handler_name )
	{
		$this->paths[$path]["handler"] = $handler_name;
		$this->paths[$path]["depth"]   = -1;
	}


	// private methods
	
	/**
	 * @access private
	 */	   
	function _startElement( $parser, $name, $attribs )
	{
		// Add the element to the context.
		$names = explode( '^', $name );
		
		if ( count( $names ) > 1 )
		{
			$name = $names[1];
			$name_namespace_uri = $names[0]; 
		}
		else
		{
			$name = $names[0]; 
		}
    
		array_push( $this->context, $name );
		$path = '/' . implode( "/", $this->context );
		$this->path = $path;

		// Check all opened paths and update them.
		foreach( array_keys( $this->paths ) as $pathk )
		{
			if ( $this->paths[$pathk]["depth"] > 0 )
			{
				$this->paths[$pathk]["depth"]++; 
				$this->paths[$pathk]["content"] .= '<' . $name;
				
				foreach( $attribs as $atk => $atv )
					$this->paths[$pathk]["content"] .= ' ' . $atk . '="' . $atv . '"'; 
        
				$this->paths[$pathk]["content"] .= '>';
			}
		}
    
		// If the context path matches some UNMATCHED path then init element data.
		if ( in_array( $path, array_keys( $this->paths ) ) )
		{
			if ( $this->paths[$path]["depth"] == -1 )
			{
				$this->paths[$path]["depth"]   = 1;
				$this->paths[$path]["content"] = '';
				$this->paths[$path]["content"] = '<' . $name;
				$this->paths[$path]["name"]    = $name;
				$this->paths[$path]["attribs"] = $attribs;
				
				foreach( $attribs as $atk => $atv )
					$this->paths[$path]["content"] .= ' ' . $atk . '="' . $atv . '"'; 
         
				$this->paths[$path]["content"] .= '>';
			}
		}  
	}
  
  	/**
	 * @access private
	 */	   
	function _endElement( $parser, $name )
	{
		// decrement element depth
		array_pop( $this->context );
		$path = '/' . implode( "/", $this->context );
		$this->path = $path;
    
		foreach( array_keys( $this->paths ) as $pathk )
		{
			if ( $this->paths[$pathk]["depth"] > 0 )
			{
				$this->paths[$pathk]["depth"]--; 
				$this->paths[$pathk]["content"] .= '</' . $name . '>';
			}
			
			if ( $this->paths[$pathk]["depth"] == 0 )
			{
				$this->paths[$pathk]["depth"] = -1;
				$this->paths[$pathk]["handler"]( $this->paths[$pathk]["name"], $this->paths[$pathk]["attribs"], $this->paths[$pathk]["content"] );
			}
		}
	}

	/**
	 * @access private
	 */	     
	function _data( $parser, $data )
	{
		foreach( array_keys( $this->paths ) as $pathk )
		{
			if ( $this->paths[$pathk]["depth"] > 0 )
				$this->paths[$pathk]["content"] .= $data;
		}
	}
} // END OF PathParser

?>
