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
 * @package xml_rss
 */
 
class RSSParser extends PEAR
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function RSSParser( $file )
	{
  		$this->file		 = $file; 
  		$this->channel   = array();
  		$this->data      = ''; 
  		$this->stack     = array();
  		$this->num_items = 0; 
  
  		$this->xml_parser = xml_parser_create();
  		xml_set_element_handler( $this->xml_parser, "rss_start_element", "rss_end_element" );
  		xml_set_character_data_handler( $this->xml_parser, "rss_character_data" );
	}

	
	/**
	 * @access public
	 */
	function parse()
	{
  		if ( !( $fp = @fopen( $this->file, "r" ) ) )
			return PEAR::raiseError( "Could not open RSS source: " . $this->file );
  
  		while ( $data = fread( $fp, 4096 ) )
		{
    		if ( !xml_parse( $this->xml_parser, $data, feof( $fp ) ) )
			{
				xml_parser_free( $this->xml_parser );
				
				return PEAR::raiseError(
					"XML Error: " . 
					xml_error_string( xml_get_error_code( $this->xml_parser ) ) . ", " . 
					xml_get_current_line_number( $this->xml_parser ) 
				);
			}
  		}
  
  		xml_parser_free( $this->xml_parser );
  		return true;
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function character_data( $parser, $data )
	{
  		if ( empty( $this->data ) )
			$this->data = trim( $data );	  // concatenate non-parsed data...
  		else
			$this->data .= ' '.trim( $data ); // and get rid of white space.
	}

	/**
	 * @access private
	 */
	function start_element( $parser, $name, $attrs )
	{
  		switch( $name )
		{
    		case 'RSS' :
      			break;
  
    		case 'CHANNEL' :
     		 	break;
  
    		case 'IMAGE' :
      			array_push( $this->stack, $name );
      			break;
    
    		case 'ITEM' : 
      			array_push( $this->stack, $name );
      			array_push( $this->stack, $this->num_items ); // push item index
      			$this->item[$this->num_items] = array();
      			$this->num_items++;
      			break;
      
    		case 'TEXTINPUT' :
      			array_push( $this->stack, $name );
      			break;
      
    		default :
      			array_push( $this->stack, $name );
      			break;
		}  
	}

	/**
	 * @access private
	 */
	function end_element( $parser, $name )
	{
  		switch ( $name )
		{
    		case 'RSS' :
      			break;
      
    		case 'CHANNEL' :
      			break;
       
    		case 'IMAGE' :
      			array_pop( $this->stack );
      			break;
    
    		case 'ITEM' :
      			array_pop( $this->stack );
      			array_pop( $this->stack );
     	 		break;
      
    		case 'TEXTINPUT' :
      			array_pop( $this->stack );
      			break;
      
    		default: // child element
      			$element = ( implode( "']['",$this->stack ) );     
      			eval( "\$this->channel['$element']=\$this->data;" ); // this does all the hard work.
      			array_pop( $this->stack );
      			$this->data = '';
      
	  			break;
  		}
	}
} // END OF RSSParser

?>
