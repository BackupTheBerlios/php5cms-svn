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
 * iTunes XML Playlist Parser Class
 *
 * How to Use:
 *
 * First, activate the parser by calling the initial function 
 * (ie. $playlist_parser->parser($playlist_file); where $playlist_file
 * is the path to your xml file. After this, the information is ready. 
 * First off, the playlist title is stored in a variable called $playlist_title 
 * (ie. $playlist_parser->playlist_title). There is also an array called 
 * $playlist which is structured as follows:
 * $playlist[artist name][increment counter][album/title/genre/time]
 *
 * @package format
 */
 
class ITunesPlaylistParser extends PEAR
{	
	/**
	 * @access public
	 */
	var $xml_parser;
	
	/**
	 * @access public
	 */
	var $xml_current;
	
	/**
	 * @access public
	 */
	var $xml_key;
	
	/**
	 * @access public
	 */
	var $xml_cursong;
	
	/**
	 * @access public
	 */
	var $xml_curartist;
	
	/**
	 * @access public
	 */
	var $title_key;
	
	/**
	 * @access public
	 */
	var $playlist_title;
	
	/**
	 * @access public
	 */
	var $counter = 1;
	
	/**
	 * @access public
	 */
	var $playlist = array();
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function ITunesPlaylistParser( $playlist_file = "" )
	{
		if ( !empty( $playlist_file ) )
			$this->parser( $playlist_file );
	}
	
	
	/**
	 * @access public
	 */
	function parser( $playlist_file )
	{	
		$this->xml_parser = xml_parser_create();
		xml_set_object( $this->xml_parser, &$this );
		xml_set_element_handler( $this->xml_parser, "xml_start", "xml_end" );
		xml_set_character_data_handler( $this->xml_parser, "xml_data" );
		$xml_file = fopen( $playlist_file, "r" );
		
		while ( $xml_data = fread( $xml_file, 4096 ) )
			xml_parse( $this->xml_parser, $xml_data, feof( $xml_file ) );
		
		ksort( $this->playlist );
	}

	/**
	 * @access public
	 */	
	function xml_start( $xml_parser, $xml_tag, $xml_attributes )
	{	
		$this->xml_current .= "/~" . $xml_tag;
	}
	
	/**
	 * @access public
	 */
	function xml_end( $xml_parser, $xml_tag )
	{	
		$xml_caret_pos     = strrpos( $this->xml_current, "/~" );
		$this->xml_current = substr( $this->xml_current, 0, $xml_caret_pos );
	}
	
	/**
	 * @access public
	 */
	function xml_data( $xml_parser, $xml_data )
	{	
		if ( $this->xml_current == "/~PLIST/~DICT/~DICT/~DICT/~KEY" && $xml_data == "Track ID" )
			$this->counter++;
		
		switch ( $this->xml_current )
		{	
			case "/~PLIST/~DICT/~DICT/~DICT/~KEY":
				$this->xml_key = $xml_data;
				break;
				
			case "/~PLIST/~DICT/~DICT/~DICT/~STRING":
				switch ( $this->xml_key )
				{	
					case "Name":
						$this->xml_cursong = $xml_data;
						break;
						
					case "Artist":
						$this->playlist[$xml_data][$this->counter]['song'] = $this->xml_cursong;
						$this->xml_curartist = $xml_data;
						break;
						
					case "Album":
						$this->playlist[$this->xml_curartist][$this->counter]['album'] = $xml_data;
						break;
						
					case "Genre":
						$this->playlist[$this->xml_curartist][$this->counter]['genre'] = $xml_data;
						break;
						
					default:
						break;
				}
				
				break;
			
			case "/~PLIST/~DICT/~DICT/~DICT/~INTEGER":
				switch ( $this->xml_key )
				{	
					case "Total Time":
						$this->playlist[$this->xml_curartist][$this->counter]['time'] = $xml_data;
						break;
				}
				
				break;
			
			case "/~PLIST/~DICT/~ARRAY/~DICT/~KEY":
				$this->title_key = $xml_data;
				break;
				
			case "/~PLIST/~DICT/~ARRAY/~DICT/~STRING":
				switch ( $this->title_key )
				{	
					case"Name":
						if ( !$this->playlist_title )
							$this->playlist_title = $xml_data;
						
						break;
					
					default:
						break;
				}
				
				break;
			
			default:
				break;
		}
	}
} // END OF ITunesPlaylistParser

?>
