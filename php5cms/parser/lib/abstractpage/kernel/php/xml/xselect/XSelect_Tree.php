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
 * @package xml_xselect
 */
 
class XSelect_Tree extends PEAR
{
	/**
	 * @access public
	 */
	var $fp;
	
	/**
	 * @access public
	 */
	var $mode;
	
	/**
	 * @access public
	 */
	var $filename;
	
	/**
	 * @access public
	 */
	var $xml_parser;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function XSelect_Tree()
	{
		// array containing all data
		$this->nodes   = array();
		$this->ids     = array();
		$this->curpath = "";
		
		// root tagname
		$this->name = "";
	}
	
	
	/**
	 * @access public
	 */
	function parseString( $string )
	{
		$this->xmlparser = xml_parser_create();
		xml_set_object( $this->xmlparser, $this );
		xml_set_element_handler( $this->xmlparser, "startElement", "endElement" );
		xml_set_character_data_handler( $this->xmlparser, "characterData" );
		
		if ( !xml_parse( $this->xmlparser, $string, true ) )
		{
			xml_parser_free( $this->xmlparser );
			return PEAR::raiseError( "XML Error: " . xml_error_string( xml_get_error_code( $this->xmlparser ) ) . ", " . xml_get_current_line_number( $this->xmlparser ) );
		}
		
		return true;
	}
	
	/**
	 * Call if opening a file, else if parsing a string, call parseString.
	 *
	 * @access public
	 */
	function parse( $filename, $mode = "r" )
	{	
		$this->mode     = $mode;
		$this->filename = $filename;
		
		if ( $mode != "r" ) 
			$mode = "r+";
		
		if ( !( $this->fp = fopen( $filename, $mode ) ) )
			return PEAR::raiseError( "Could not open XML input." );
		
		$data = fread( $this->fp, filesize( $filename ) );
		fclose( $this->fp );
		
		return $this->parseString( $data );
	}

	/**
	 * @access public
	 */	
	function startElement( $parser, $name, $attrs )
	{
		$name = strtolower( $name );
	
		// set root tagname
		if ( $this->name == "" )
			$this->name= '/' . $name . '(1)';
		
		// $path = current path/current tagname
		$path     = "$this->curpath/$name";
		
		if (!isset($this->ids[$path])) {
			$this->ids[$path] = 0;	
		}
		
		$relpath  = "$name(" . (++ $this->ids[$path]) . ")";
		$fullpath = "$this->curpath/$relpath";

		$this->nodes[$fullpath]["attributes"] = $attrs;
		$this->nodes[$fullpath]["name"]       = $name;
		$this->nodes[$fullpath]["text"]       = "";
		$this->nodes[$fullpath]["elements"]   = array();
		// TODO: Changed $this->nodes[$elements]["elements"]   = array();
	
		// add this tagname to the elements array of current parent path
		$this->nodes[$this->curpath]["elements"][] = $name;
	
		// add this tag to current parent path "text"
		if (!isset($this->nodes[$this->curpath]["text"])) {
			$this->nodes[$this->curpath]["text"] = '';	
		}
		$this->nodes[$this->curpath]["text"] .= "<\$$relpath>";
		
		// change current path to fullpath
		$this->curpath = $fullpath;
	}
	
	/**
	 * @access public
	 */
	function endElement( $parser, $name )
	{
		// at an end tag, strip it's name off the end of curpath
		$this->curpath = substr( $this->curpath, 0, strrpos( $this->curpath, "/" ) ); 		
	}

	/**
	 * @access public
	 */	
	function characterData( $parser, $text )
	{
		if ( $text == "&" )
			$text="&amp;";
		
		$this->nodes[$this->curpath]["text"] .= trim( $text );
	}
	
	/**
	 * Turn path into xml, recursing through paths using $this->nodes["text"].
	 *
	 * @access public
	 */
	function toStringByPath( $path, $self = false, $dep = 0 )
	{
		// if path doesn't exist, die!
		if ( !is_array( $this->nodes[$path] ) )
			return false;
		
		$txt = "";
		$tab = "\n";
		
		for ( $i = 0; $i < $dep; $i++ )
			$tab .= "  ";
		
		// this var contains the 
		$inn = "";
		
		// get nodes["text"]
		$pieces = explode( "<$", $this->nodes[$path]["text"] );
		
		// extract the data
		while ( list( $key, $val ) = each( $pieces ) )
		{
			// if it's a parent tag, ie: <$tag(1)>
			if ( preg_match( "|^([^(]*)\(([^)]*)\)>(.*)$|m", $val, $matches ) )
			{
				$name = $matches[1]; // element name
				$id   = $matches[2]; // position
				
				$txtpiece = trim($matches[3]); 	// anything else?
				
				// recurse through tags
				$inn .= $this->toStringByPath( "$path/$name($id)", true, $dep + 1 ) . $txtpiece;
			}
			else
			{
				// must be a data tag
				$inn .= trim( $val );
			}
		}
		
		// wrap data in it's tag
		if ( $self )
		{
			// write tagname
			$txt .= "$tab<" . strtolower( $this->nodes[$path]["name"] );
			
			// write attributes
			reset( $this->nodes[$path]["attributes"] );
			while ( list( $key, $val ) = each( $this->nodes[$path]["attributes"] ) )
			{
				if ( strpos( $val, "\"" ) )
					$quot = "'";
				else
					$quot = "\"";
				
				//$quot = strpos($val, "\"") ? "'" : "\"" ;
				$txt .= " " . strtolower( $key ) . "=$quot$val$quot";
			}
			
			// add cdata and rest of tag data
			if ( $inn )
				$txt .=">$inn$tab</" . strtolower( $this->nodes[$path]["name"] ) . ">";
			else
				$txt .="/>";
		}
		else
		{
			if ( $inn )
				$txt .= "$inn";
		}
		
		return $txt;
	}
	
	/**
	 * @access public
	 */
	function getAttribute( $path, $att )
	{
		return $this->nodes[$path]["attributes"][strtoupper($att)];
	}

	/**
	 * @access public
	 */
	function getEltByPath( $path )
	{
		$last_slash = strrpos( $path, "/" );
		
		switch ( $path[$last_slash+1] )
		{
			case "@" : 
				return $this->getAttribute( substr( $path, 0, $last_slash ), substr( $path, $last_slash + 2 ) );
			
			case "*" :
				return $this->toStringByPath( substr( $path, 0, $last_slash ), true );
			
			default :
				return $this->toStringByPath( $path );		
		}
	}

	/**
	 * @access public
	 */
	function setAttribute( $path, $att, $val )
	{
		$this->nodes[$path]["attributes"][strtoupper($att)] = $val;
		return $this;
	}

	/**
	 * @access public
	 */
	function setNode( $path, $value, $action = false )
	{
		if ( $action == "new" )
		{
			reset( $value->nodes );
			while ( list( $key, $val ) = each( $value->nodes ) )
			{
				if ( $key )
				{
					$pieces = explode( "<$", $this->nodes[$path]["text"] );

					// avoid overwriting tags of same name
					$maxId = 0;
					while ( list( $k, $v ) = each( $pieces ) )
					{
						// caseless, multiline regex match
						if ( preg_match("|^([^(]*)\(([^)]*)\)>(.*)$|m", $v, $matches ) )
						{
							$name = $matches[1];	// matches whole pattern
							$id   = $matches[2];		// matches first parenthesized sub-pattern
						}
						
						echo( "$id, $maxId ;;;; $name, " . $value->nodes[$key]["name"] . "<br>" );
						
						if ( $id > $maxId && $name == $value->nodes[$key]["name"] )
							$maxId = $id;
					}
					
					$maxId++;
					$newRelPath = $value->nodes[$key]["name"] . "($maxId)";
					
					// must change nodes[$path]["text"] for path, or changes won't be saved
					$this->nodes[$path]["text"] = $this->nodes[$path]["text"] . "<$" . $newRelPath . ">";
					echo( "Adding new node: " . "$path/ $newRelPath" );
					$new_path = $path . $newRelPath;
					$this->nodes["$path" . "/$newRelPath"] = $val;
					ksort( $this->nodes );
					reset( $this->nodes );
				}
			}
		}
		else
		{
			// delete old tag and value from nodes array
			reset( $this->nodes );
			while ( list( $key, $val ) = each( $this->nodes ) )
			{
				if ( strpos( " " . $key, $path ) == 1 )
					unset ( $this->nodes[$key] );
			}
			
			reset( $value->nodes );
			while ( list( $key, $val ) = each( $value->nodes ) )
			{
				if ( $key )
				{
					if ( $pos = strpos( $key, "/", 1 ) )
						$rel_path = "/" . substr( $key, $pos + 1 );
					else
						$rel_path = "";
					
					$new_path = $path . $newRelPath;
					$this->nodes["$path" . "$rel_path"] = $val; 
				}
			}
		}
		
		return $new_path;
	}

	/**
	 * @access public
	 */	
	function delNode( $path )
	{
		unset( $this->nodes[$path] );
		$this->reload();
	}

	/**
	 * @access public
	 */	
	function setEltByPath( $path, $value )
	{
		// get position of last slash in path
		$last_slash = strrpos( $path, "/" );
		
		switch ( $path[$last_slash+1] )
		{
			case "@" : 
				return $this->setAttribute( substr( $path, 0, $last_slash ), substr( $path, $last_slash + 2 ), $value );
				
			case "*" :
				return $this->setNode( substr( $path, 0, $last_slash ), $value );
				
			case ">" :
				return $this->setNode( substr( $path, 0, $last_slash ), $value, "new" );
				
			default :
				return $this->setNode( $path, $value );		
		}
	}

	/**
	 * @access public
	 */	
	function save()
	{
		$fxml = fopen( $this->filename, "w" );
		
		fwrite( $fxml, "<?xml version=\"1.0\" encoding='ISO-8859-1'?>\n" );
		fwrite( $fxml, $this->toStringByPath( $this->name, true ) );
		fclose( $fxml );
	}

	/**
	 * @access public
	 */
	function element_type( $path )
	{
		if ( strpos( $path, "@" ) )
			return "attribute";
		else
			return "element";
	}

	/**
	 * @access public
	 */
	function element_name( $path )
	{
		$path = substr( $path, strrpos( $path, "/" ) );
		return substr( $path, 1, strpos( $path, "(") - 1);
	}

	/**
	 * @access public
	 */	
	function getDirlist( $path )
	{
		$pieces = explode( "<$", $this->nodes[$path]["text"] );
		while ( list( $k, $v ) = each( $pieces ) )
		{
			// caseless, multiline regex match
			if ( preg_match("|^([^(]*)\(([^)]*)\)>(.*)$|m", $v, $matches ) )
			{
				$name = $matches[1];	// matches whole pattern
				$id   = $matches[2];	// matches first parenthesized sub-pattern
				$dirList[$name] = $id;
			}
			else
			{
				// must be a data tag
				$dirList["cdata"] = trim((isset($val) ? $val : ''));
			}
		}
		
		if ( $dirList["cdata"] == "" )
			unset( $dirList["cdata"] );
		
		while ( list( $k, $v ) = each( $dirList ) )
		{
			if ( $k == "" )
				unset( $dirList["$k"] );
		}
		
		return $dirList;
	}
	
	/**
	 * @access public
	 */
	function pathBykey( $keyword )
	{
		while ( list( $k, $v ) = each( $this->nodes ) )
		{
			if ( ereg( "$keyword", $k ) )
			{
				if ( ereg( "$keyword\([(0-9)*]\)$", $k ) )
					return $k;
			}
		}
		
		return false;
	}
	
	/**
	 * @access public
	 */
	function pathBykeyspecificattribute( $keyword, $data )
	{
		while ( list( $k, $v ) = each( $this->nodes ) )
		{
			if ( $k != "" )
			{
				if ( ereg( "$keyword", $k ) )
				{
					if ( ereg( "$keyword\([(0-9)*]\)$", $k ) )
					{
						while ( list( $a, $b ) = each( $this->nodes[$k]["attributes"] ) )
						{
							if ( $b == $data )
								return $k;
						}
					}
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Makes changes to xml file, and reparses.
	 *
	 * @access public
	 */
	function reload()
	{
		$this->save();
		$this->parse( $this->filename );
	}
	
	/**
	 * Path would be the path to the new node's parent. $attrs is an assoc. array: $node["attrib"] = $value
	 *
	 * @access public
	 */
	function addNode( $path, $node, $value = false, $attrs = false )
	{
	}

	/**
	 * @access public
	 */	
	function quikAdd( $path, $node, $atts )
	{
		$XMLtree->setEltByPath( $path, $node );
		
		foreach( $atts as $k => $v )
			$XMLtree->setEltByPath( "/book(1)/chapter(1)/@title", "New title attribute" );
	}
	
	/**
	 * Edit a node's cdata or attributes. $attrs is an assoc. array: $node["attrib"] = $value.
	 *
	 * @access public
	 */
	function updateNode( $path, $value = false, $attrs = false )
	{
	}
	
	/**
	 * Return an array of attribute names for a tag.
	 *
	 * @access public
	 */
	function getAttributes( $tagname )
	{
		// edit this code to check for path validity, and to accept a
		// keyword or a path, and extend that code for all these new functions.
		// also, have 2 distinct functions for atts: one that returns an att value, and
		// one that returns an array of all the atts
		return $this->nodes[$path]["attributes"][strtoupper( $att )];
	}
	
	/**
	 * Returns a node's cdata. returns false if node is a parent.
	 *
	 * @access public
	 */
	function getCdata( $path, $attribute = false, $value = false )
	{
		if ( ereg( "^\/",$this->nodes[$path]["text"] ) )
			return false;
		else
			return $this->nodes[$path]["text"];
	}
	
	/**
	 * Returns string of parent tagname of supplied tagname (behaviour if fed root tagname?).
	 *
	 * @access public
	 */
	function getParent( $tagname )
	{
		$self_path   = $this->pathBykey( $tagname );
		$parent_path = substr( $self_path, 0, strlen( strstr( $self_path, $tagname ) - 1 ) );

		return $this->element_name( $parent_path );
	}
} // END OF XSelect_Tree

?>
