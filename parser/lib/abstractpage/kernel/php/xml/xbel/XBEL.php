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
 * Class for parsing XBEL (XML Bookmark Exchange Language) documents.
 *
 * @link http://pyxml.sourceforge.net/topics/xbel/
 * @package xml_xbel
 */
 
class XBEL extends PEAR
{
	/**
	 * @access public
	 */
	var $top;
	
	/**
	 * @access public
	 */
	var $content;
	
	/**
	 * @access public
	 */
	var $output;

	/**
	 * @access private
	 */
	var $_xml_parser;
	
	/**
	 * @access private
	 */
	var $_xbelfile;

	/**
	 * @access private
	 */
	var $_current_element;
	
	/**
	 * @access private
	 */
	var $_parent_element;

	/**
	 * @access private
	 */
	var $_parse_level = 1;
	
	/**
	 * @access private
	 */
	var $_foldercount = 0;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function XBEL( $filename )
	{
		if ( $this->_xbelfile = @file( $filename, 1 ) )
		{
			$this->_xml_parser = xml_parser_create();
			xml_set_object( $this->_xml_parser, &$this );
			xml_parser_set_option( $this->_xml_parser, XML_OPTION_CASE_FOLDING, true );
			xml_parser_set_option( $this->_xml_parser, XML_OPTION_TARGET_ENCODING, 'UTF-8' );
			xml_set_element_handler( $this->_xml_parser, "startElement", "endElement" );
			xml_set_character_data_handler( $this->_xml_parser, "characterData" );
			
			$res = $this->parse();
			
			if ( PEAR::isError( $res ) )
			{
				$this = $res;
				return;
			}
		}
		else
		{
			$this = new PEAR_Error( "Could not fetch file." );
			return;
		}
	}
	

	/**
	 * @access public
	 */
	function parse()
	{
		if ( !count( $this->_xbelfile ) )
		{
			return PEAR::raiseError( "Empty or missing data." );
		}
		else
		{
			while ( list( $line_num, $line ) = each( $this->_xbelfile ) )
			{
				if ( !xml_parse( $this->_xml_parser, ereg_replace( '&', '&amp;', $line ) ) )
					return PEAR::raiseError( "Error on line " . $line_num . " " . xml_error_string( xml_get_error_code( $this->_xml_parser ) ) );
			}
			
			xml_parser_free( $this->_xml_parser );
			unset( $this->_xbelfile );
		}
		
		return true;
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function startElement( $parser, $name, $attrs = array() )
	{
		$this->_parent_element  = $this->_current_element;
		$this->_current_element = $name;
		
		switch ( $name )
		{
			case "XBEL":
				$this->output .= '<table width="100%" id="' . $attrs['ID'] . '"><tr><td>';
				$this->top    .= '<a name="top"></a>';
				
				break;
		
			case "TITLE":
		  		if ( "BOOKMARK" != $this->_parent_element )
				{
			  		$this->output  .= '<a href="#top" name="' . $this->_foldercount . '"><h'. $this->_parse_level .'>';
					$this->content .= '<span id="cont'. $this->_parse_level .'"><a href="#' . $this->_foldercount .'"><h'. $this->_parse_level .'>'; 
				}
			
				break;
		
			case "DESC":
		  		if ( "BOOKMARK" != $this->_parent_element )
			    	$this->output .= '<div id="desc">';
			
				break;
		
			case "INFO":
				break;
		
			case "METADATA":
				break;
			
			case "FOLDER":
		  		$this->output .= '<div id="' . $this->_parse_level . '"><a name="' . $this->_foldercount . '"></a><ul id="' . $attrs['ID'] . '">';
				$this->_parse_level++;
				$this->_foldercount++;
			
				break;
		
			case "SEPERATOR":
		  		$this->output .= '<br />';
				break;
		
			case "BOOKMARK":
		  		$this->output .= '<li><a id="' . $attrs['ID'] . '" href="' . $attrs['HREF'] . '" target="_blank">';
				break;
		
			case "ALIAS":
		  		break;
		}
	}
	
	/**
	 * @access private
	 */
	function characterData( $parser, $data )
	{
		switch ( $this->_current_element )
		{
			case "XBEL":
			  	$this->output .= trim( $data );
				break;
			
			case "TITLE":
			  	if ("BOOKMARK" != $this->_parent_element )
			    	$this->content .= trim( $data );
				
			  	$this->output .= trim( $data );
				break;
			
			case "DESC":
				if ( "BOOKMARK" != $this->_parent_element )
			   		$this->output .= trim( $data );
			  
				break;
			
			case "INFO":
			  	break;
			
			case "METADATA":
			  	break;
			
			case "FOLDER":
			  	$this->output .= trim( $data );
			 	break;
			
			case "SEPERATOR":
			  	break;
			
			case "BOOKMARK":
			  	$this->output .= trim( $data );
			  	break;
			
			case "ALIAS":
			  break;
		}
	}
	
	/**
	 * @access private
	 */
	function endElement( $parser, $name )
	{
	  	switch ( $name )
		{
			case "ALIAS":
				break;
			
			case "BOOKMARK":
				$this->output .= '</a></li>';
				break;
			
			case "SEPERATOR":
				$this->output .= '<hr />';
				break;
			
			case "FOLDER":
				$this->output .= '</ul></div>';
				$this->_parse_level--;
				break;
			
			case "METADATA":
				break;
			
			case "INFO":
				break;
			
			case "DESC":
				if ( "BOOKMARK" != $this->_parent_element )
					$this->output .= '</div>';
			    
				break;
				
			case "TITLE":
				if ( "BOOKMARK" != $this->_parent_element )
				{
			    	$this->output  .= '</h'. $this->_parse_level .'></a>';
			    	$this->content .= '</h'. $this->_parse_level .'></a></span>|';
				}
				
				break;
			
			case "XBEL":
				$this->output .= '</td></tr></table>';
				break;
		}
		
		$this->_current_element = $this->_parent_element;
	}
} // END OF XBEL

?>
