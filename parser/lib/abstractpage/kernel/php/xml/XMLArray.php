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


/*
 * XMLArray - Convert XML to Associative Array 
 *
 * Example:
 *
 * $xml = new XMLArray( 'http://www.slashdot.org/slashdot.xml', array( 'backslash' ), array( 'story' => '_array_' ), true ); 
 * print_r( $xml->ReturnArray()      ); 
 * print_r( $xml->ReturnReplaced()   ); 
 * print_r( $xml->ReturnAttributes() );
 *
 * @package xml
 */

class XMLArray extends PEAR
{
	/**
	 * @access private
	 */
	var $_showAttribs; 
	 
	/**
	 * @access private
	 */
	var $_level = 0;  

	/**
	 * @access private
	 */
	var $_parser = 0; 
	
	/**
	 * @access private
	 */
	var $_data = array(); 
	
	/**
	 * @access private
	 */
	var $_name = array();
	
	/**
	 * @access private
	 */
	var $_rep = array();
	
	/**
	 * @access private
	 */
	var $_ignore = array();
	
	/**
	 * @access private
	 */
	var $_replace = array();


	/**
	 * Constructor
	 *
	 * @access public
	 */ 
    function XMLArray( &$data, $ignore = array(), $replace = array(), $showattribs = false, $toupper = false )
	{
        $this->_showAttribs = $showattribs; 
        $this->_parser = xml_parser_create(); 

		xml_set_object( $this->_parser, $this ); 
        
		if ( $toupper )
		{ 
        	foreach ( $ignore  as $key => $value )
				$this->_ignore[strtoupper( $key )] = strtoupper( $value ); 
        
			foreach ( $replace as $key => $value )
				$this->_replace[strtoupper( $key )] = strtoupper( $value ); 
        
			xml_parser_set_option( $this->_parser,XML_OPTION_CASE_FOLDING, true ); 
		} 
		else
		{ 
        	$this->_ignore  = &$ignore; 
        	$this->_replace = &$replace; 
        
			xml_parser_set_option( $this->_parser, XML_OPTION_CASE_FOLDING, false ); 
        } 
        
		xml_set_element_handler( $this->_parser, "_startElement", "_endElement" ); 
        xml_set_character_data_handler( $this->_parser, "_cdata" ); 

        $this->_data  = array(); 
        $this->_level = 0; 
        
		if ( !xml_parse( $this->_parser, $data, true ) )
			return false; 
        
        xml_parser_free( $this->_parser ); 
	}
    
	
	/**
	 * @access public
	 */
	function &ReturnArray()
	{ 
        return $this->_data[0]; 
    }
	 
	/**
	 * @access public
	 */
    function &ReturnReplaced()
	{ 
        return $this->_data['_Replaced_']; 
    }

	/**
	 * @access public
	 */	 
    function &ReturnAttributes()
	{ 
        return $this->_data['_Attributes_']; 
    }
	
	
	// private methods
	
	/**
	 * @access private
	 */ 
    function _startElement( $parser, $name, $attrs )
	{ 
        if ( !isset( $this->_rep[$name] ) )
			$this->_rep[$name] = 0; 
        
		if ( !in_array( $name, $this->_ignore ) )
		{ 
        	$this->_addElement( $name, $this->_data[$this->_level], $attrs, true ); 
        	$this->_name[$this->_level] = $name; 
        	$this->_level++; 
        } 
    } 
	
	/**
	 * @access private
	 */ 
    function _endElement( $parser, $name )
	{ 
        if ( !in_array( $name, $this->_ignore ) && isset( $this->_name[$this->_level - 1] ) )
		{ 
        	if ( isset( $this->_data[$this->_level] ) )
            	$this->_addElement( $this->_name[$this->_level - 1], $this->_data[$this->_level - 1], $this->_data[$this->_level], false ); 
        
        	unset( $this->_data[$this->_level] ); 
        	$this->_level--; 
        	$this->_rep[$name]++; 
        }
    }

	/**
	 * @access private
	 */ 	
    function _cdata( $parser, $data )
	{ 
        if ( $this->_name[$this->_level - 1] )
			$this->_addElement( $this->_name[$this->_level - 1], $this->_data[$this->_level - 1], str_replace( array( "&gt;", "&lt;", "&quot;", "&amp;" ), array( ">", "<", '"', "&" ), $data ), false );
    }

	/**
	 * @access private
	 */ 	
    function _addElement( &$name, &$start, $add = array(), $isattribs = false )
	{ 
        if ( ( sizeof( $add ) == 0 && is_array( $add ) ) || !$add )
		{ 
        	if ( !isset( $start[$name] ) )
				$start[$name] = ''; 
        
			$add = ''; 
        }
		
        if ( strtoupper( $this->_replace[$name] ) == '_ARRAY_' )
		{ 
        	if ( !$start[$name] )
				$this->_rep[$name] = 0; 
        
			$update = &$start[$name][$this->_rep[$name]]; 
        } 
        else if ( $this->_replace[$name] )
		{ 
        	if ( $add[$this->_replace[$name]] )
			{
				$this->_data['_Replaced_'][$add[$this->_replace[$name]]] = $name;
				$name = $add[$this->_replace[$name]];
			} 
        
			$update = &$start[$name]; 
		} 
		else
		{ 
        	$update = &$start[$name]; 
        }

        if ( $isattribs && !$this->_showAttribs )
			return; 
        else if ( $isattribs )
			$this->_data['_Attributes_'][$this->_level][$name][] = $add; 
        else if ( is_array( $add ) && is_array( $update ) )
			$update += $add; 
        else if ( is_array( $update ) )
			return; 
        else if ( is_array( $add ) )
			$update = $add; 
        else if ( $add )
			$update .= $add; 
    } 
} // END OF XMLArray

?>
