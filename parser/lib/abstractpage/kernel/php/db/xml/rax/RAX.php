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
|         ??                                                           |
+----------------------------------------------------------------------+
*/


using( 'db.xml.rax.RAXRecord' );


/**
 * RAX - PHP Record-oriented API for XML
 *
 * Affords a database recordset-like view of an XML document
 * in documents which lend themselves to such interpretation.
 *
 * A port of the Perl XML::RAX module by Robert Hanson 
 * (http://search.cpan.org/search?mode=module&query=rax)
 * based on the RAX API created by Sean McGrath 
 * (http://www.xml.com/pub/2000/04/26/rax)
 *
 * @package db_sql_rax
 */

class RAX extends PEAR
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function RAX ()
	{
		$this->record_delim = '';
		$this->fields       = array();
		$this->records      = array();
		$this->parser       = null;
		$this->in_rec       = 0;
		$this->in_field     = 0;
		$this->field_data   = '';
		$this->tag_stack    = array();
		$this->xml          = '';
		$this->xml_file     = null;
		$this->rax_opened   = 0;
	}


	/**
	 * @access public
	 */
	function open( $xml )
	{
		if ( $this->rax_opened )
			return false;
		
		$this->xml = $xml;
		$this->rax_opened = 1;
	}

	/**
	 * @access public
	 */
	function openfile( $filename )
	{
		if ( $this->rax_opened )
			return false;
		
		$fp = fopen( $filename, "r" );
		
		if ( $fp )
		{
			$this->xml_file = $fp;
			$this->rax_opened = 1;
			
			return true;
		}
		
		return false;
	}

	/**
	 * @access public
	 */
	function startparse()
	{
		$this->parser = xml_parser_create();
		xml_set_object( $this->parser, &$this );
		xml_set_element_handler( $this->parser, "startElement", "endElement" );
		xml_set_character_data_handler( $this->parser, "characterData" );
		xml_parser_set_option( $this->parser, XML_OPTION_CASE_FOLDING, 0 );
		
		if ( xml_parse( $this->parser, '' ) )
		{
			$this->parse_started = 1;
			return true;
		}
		
		return false;
	}

	/**
	 * @access public
	 */
	function parse()
	{	
		if ( !$this->rax_opened )
			return false;
		
		if ( isset( $this->parse_done ) )
			return false;
		
		if ( !isset( $this->parse_started ) )
		{
			if ( !$this->startparse() )
				return false;
		}
		
		if ( $this->xml_file )
		{
			$buffer = fread( $this->xml_file, 4096 );
			
			if ( $buffer )
				xml_parse( $this->parser, $buffer, feof( $this->xml_file ) );
			else
				$this->parse_done = 1;
		}
		else
		{
			xml_parse( $this->parser, $this->xml, 1 );
			$this->parse_done = 1;
		}
		
		return true;
	}

	/**
	 * @access public
	 */
	function startElement( $parser, $name, $attrs )
	{	
		array_push( $this->tag_stack, $name );
		
		if ( !$this->in_rec && !strcmp( $name, $this->record_delim ) )
		{
			$this->in_rec    = 1;
			$this->rec_lvl   = sizeof( $this->tag_stack );
			$this->field_lvl = $this->rec_lvl + 1;
		}
		else if ( $this->in_rec && sizeof( $this->tag_stack ) == $this->field_lvl )
		{
			$this->in_field = 1;
		}
	}

	/**
	 * @access public
	 */
	function endElement( $parser, $name )
	{
		array_pop( $this->tag_stack );
		
		if ( $this->in_rec )
		{
			if ( sizeof( $this->tag_stack ) < $this->rec_lvl )
			{
				$this->in_rec = 0;
				array_push( $this->records, new RAXRecord( $this->fields ) );
				$this->fields = array();
			}
			else if ( sizeof($this->tag_stack) < $this->field_lvl )
			{
				$this->in_field = 0;
				$this->fields[$name] = $this->field_data;
				$this->field_data = '';
			}
		}
	}

	/**
	 * @access public
	 */
	function characterData( $parser, $data )
	{		
		if ( $this->in_field ) 
			$this->field_data .= $data;
	}

	/**
	 * @access public
	 */
	function setRecord( $delim )
	{		
		if ( $this->parse_started )
			return false;
		
		$this->record_delim = $delim;
		return true;
	}

	/**
	 * @access public
	 */
	function readRecord()
	{		
		while ( !sizeof( $this->records ) && !isset( $this->parse_done ) )
			$this->parse();
		
		return array_shift( $this->records );
	}
} // END OF Rax

?>
