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


using( 'xml.dom.simple.lib.SimpleDomElement' );
using( 'xml.dom.simple.lib.SimpleDomAttribute' );
using( 'xml.dom.simple.lib.SimpleDomComment' );


/**
 * @package xml_dom_simple_lib
 */
 
class SimpleDomDocument extends PEAR
{
	/**
	 * @access public
	 */
	var $comment;
	
	/**
	 * @access public
	 */
	var $filename = ''; 
	
	/**
	 * @access public
	 */
	var $encoding = ''; 

	/**
	 * @access public
	 */
	var $doctype = array();
	
	/**
	 * @access public
	 */
	var $entity = array();
	
	/**
	 * @access public
	 */
	var $element = array();
	
	/**
	 * @access public
	 */
	var $others = array();
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function SimpleDomDocument( $filename ) 
	{
		$this->setFile( $filename );
	} 
	
	
	/**
	 * @access public
	 */
	function setFile( $filename ) 
	{
		$this->initial();

		$this->is_DocType       = false;
		$this->is_Entity        = false;

		$this->element_stack[0] = 'root';
		$this->filename         = $filename;

		if ( file_exists( $filename ) && is_file( $filename ) ) 
		{
			$fHdl = fopen( $filename, 'r' );
			$this->document = str_replace( chr( 13 ) . chr( 10 ), "", fread( $fHdl, filesize( $filename ) ) );

            fclose( $fHdl );

            $this->parse();
            $this->setElements();
            $this->release();
		}
	} 

	/**
	 * @access public
	 */
	function parse() 
	{
		$parser = xml_parser_create();

		xml_set_object( $parser, &$this );
		xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
		xml_set_default_handler( $parser, "defaultHandler" );
		xml_set_element_handler( $parser, "startElement", "endElement" );
		xml_set_character_data_handler( $parser, "characterHandler" );
		xml_parse( $parser, $this->document, false );
		xml_parser_free( $parser );
	}

	/**
	 * @access public
	 */	
	function setElements() 
	{
		$keys  = array_keys( $this->element );
		$count = count( $keys ) - 2;
		$idx   = array();

		for ( $i = $count; $i >= 0; $i-- ) 
		{
			for ( $j=0; $j < count( $this->element[$keys[$i]] ); $j++ ) 
			{
				for ( $e = 0; $e < count( $this->element[$keys[$i]][$j] ); $e++ ) 
				{
					if ( isset( $this->names[$this->element[$keys[$i]][$j][$e]->name] ) ) 
					{
                     	if ( !isset( $idx[$this->element[$keys[$i]][$j][$e]->name] ) ) 
							$idx[$this->element[$keys[$i]][$j][$e]->name] = 0;
   
						for ( $y = 0; $y < count( $this->element[$this->element[$keys[$i]][$j][$e]->name][$idx[$this->element[$keys[$i]][$j][$e]->name]] ); $y++ ) 
							$this->element[$keys[$i]][$j][$e]->addElement( $this->element[$this->element[$keys[$i]][$j][$e]->name][$idx[$this->element[$keys[$i]][$j][$e]->name]][$y] );

						$idx[$this->element[$keys[$i]][$j][$e]->name]++;
					}
				} 
			}
		}  

		$this->element = $this->element['root'][0][0];
	} 

	/**
	 * @access public
	 */	
	function startElement( $parser, $element, $attributes ) 
	{
		$this->is_endElement = false;
		$parent = $this->element_stack[count( $this->element_stack ) - 1];

		if ( !isset( $this->parent_stack[$parent] ) )
			$this->parent_stack[$parent] = 0;

		$index = $this->parent_stack[$parent];
		array_push( $this->element_stack, trim( $element ) );

		if ( !isset( $this->element[$parent][$index] ) ) 
		{
			$this->element[$parent][$index] = array();
			$this->names[$parent] = array();
		} 

		$memory = new SimpleDomElement( $element, $parent, new SimpleDomAttribute( $attributes ) );

		array_push( $this->element[$parent][$index], $memory );
		array_push( $this->names[$parent], $element );
	} 
	
	/**
	 * @access public
	 */
	function characterHandler( $parser, $value ) 
	{
		if ( !$this->is_endElement ) 
		{
			$parent = $this->element_stack[count( $this->element_stack ) - 2];
			$index  = $this->parent_stack[$parent];
			$last   = count( $this->element[$parent][$index] ) - 1;

			$this->element[$parent][$index][$last]->setValue( trim( $value ) );
		}
	}

	/**
	 * @access public
	 */	
	function defaultHandler( $parser, $data ) 
	{
		if ( substr( $data, 0, 5 ) == "<?xml" ) 
		{
			$start = strpos( $data, 'encoding="' ) + 10;
			$end   = strpos( $data, '?>' ) - $start;
            
			$this->encoding = trim( substr( $data, $start, $end ) );
            $this->encoding = substr( $this->encoding, 0, strlen( $this->encoding ) - 1 );
		}
		else if ( substr( $data, 0, 4 ) == "<!--" ) 
		{
			$end     = strpos( $data, ' -->' ) - 5;
            $comment = substr( $data, 5, $end );

			if ( !isset( $this->is_endElement ) ) 
				$parent = 'root';
			else if ( $this->is_endElement ) 
               $parent = $this->element_stack[count( $this->element_stack ) - 1];
			else 
               $parent = $this->element_stack[count( $this->element_stack ) - 2];

			if ( $parent == 'root' ) 
			{
				if ( empty( $this->comment ) ) 
					$this->comment = new SimpleDomComment();

				$this->addComment( trim( $comment ) );
			} 
			else 
			{
				$index = $this->parent_stack[$parent];
				$last  = count( $this->element[$parent][$index] ) - 1;

				$this->element[$parent][$index][$last]->addComment( trim( $comment ) );
			}
		}
		else if ( $data == '<!DOCTYPE' ) 
		{
			$this->is_DocType = true;
		}
		else if ( $data == '<!ENTITY' ) 
		{
			$this->is_Entity = true;
		}
		else 
		{
			if ( $this->is_DocType ) 
			{
				$data = trim( $data );

				if ( !empty( $data  ) && ( $data != '>' && $data != ']>' && $data != '[' && $data != ']' ) ) 
				{
					if ( $this->is_Entity ) 
					{
						if ( !isset( $this->EntityName ) ) 
						{
							$this->EntityName = $data;
						} 
						else 
						{
							$this->entity[$this->EntityName] = substr( $data, 1, strlen( $data ) - 2 );
							unset( $this->EntityName );
						}
                  	} 
					else 
					{
						array_push( $this->doctype, $data );
					}
				}

				if ( $data == '>' && $data == ']>' ) 
				{
					$this->is_DocType = false;
					$this->is_Entity  = false;
				}
			} 
			else 
			{
				array_push( $this->others, $data );
			} 
		}
	}

	/**
	 * @access public
	 */
	function endElement( $parser, $element ) 
	{
		$this->is_endElement = true;
		$parent = array_pop( $this->element_stack );

		if ( isset( $this->parent_stack[$parent] ) ) 
            $this->parent_stack[$parent]++;
	} 

	/**
	 * @access public
	 */
	function addComment( $comment ) 
	{
 		if ( empty( $this->comment ) ) 
            $this->comment = new SimpleDomComment();

		$this->comment->addComment( $comment );
	}

	/**
	 * @access public
	 */
	function getEncoding() 
	{
		return $this->encoding;
	}

	/**
	 * @access public
	 */
	function getComment() 
	{
		return $this->comment;
	}

	/**
	 * @access public
	 */
	function getDocType() 
	{
		return $this->doctype;
	}

	/**
	 * @access public
	 */
	function getElement() 
	{
		return $this->element;
	}

	/**
	 * @access public
	 */
	function getEntity() 
	{
		return $this->entity;
	}

	/**
	 * @access public
	 */
	function getFilename() 
	{
		return $this->filename;
	} 

	/**
	 * @access public
	 */
	function getOthers() 
	{
		return $this->others;
	}

	/**
	 * @access public
	 */
	function initial() 
	{
		$this->filename = '';

		$this->encoding = '';
		$this->doctype  = array();
		$this->entity   = array();

		$this->element  = array();
		$this->comment  = null;

		$this->others   = array();
	}

	/**
	 * @access public
	 */
	function release() 
	{
		unset( $this->document      );
		unset( $this->is_endElement );
		unset( $this->is_DocType    );
		unset( $this->is_Entity     );
		unset( $this->parent_stack  );
		unset( $this->element_stack );
		unset( $this->names         );
	}
} // END OF SimpleDomDocument

?>   
