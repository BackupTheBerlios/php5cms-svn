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


using( 'io.config.deep.DeepIniXMLElement' );
using( 'util.Util' );


/** 
 * Parses deep ini files, a format designed to complement XML in the simplest fashion.
 *
 * @todo Indizes
 * @todo Variables   -> [ %%{name}: 3 /]
 * @todo Conditionals (e.g. '%%something? 1 : 2')
 * @todo Expressions -> %%{count + 2}
 * @todo Attributes (@@-Prefix)
 *
 * @package io_config_deep
 */
 
class DeepIni extends PEAR 
{
	/** 
	 * Root Data_XML_Element for this document. 
	 * @access public
	 */
	var $root;
	
	/** 
	 * Internal XML Parser resource.
	 * @access public
	 */
	var $parser;
	
	/** 
	 * Array for storing depth counter.
	 * @access public
	 */
	var $depth;
	
	/** 
	 * Allow children and text content.. mixed text and XML 
	 * @access public
	 */
	var $mixed_content = 1;
	
	/** 
	 * Force caps 
	 * @access public
	 */
	var $force_caps = 0;
	
	/** 
	 * Use extension: 'xml' or 'domxml' 
	 * @access public
	 */
	var $phpext;
	
	/** 
	 * Turn off storing content that may be mixed with XML. 
	 * @access public
	 */
	var $mixed_content = 0;

	/** 
	 * Element prefixes
	 * @access public
	 */
	var $ele_prefixes = array(
		'@'	=> 'metadata',
		'.'	=> 'model_object',
		'!'	=> 'action',
		'+'	=> 'presentation'
	);

	
	/** 
	 * Constructor
	 * Creates a tree structure based on the string sent in.
	 *
	 * @param $doc A complete XML document string.
	 * @access public
	 */
	function DeepIni( $doc = "" )
	{
		// use an installed extension 
		if ( !$this->phpext ) 
		{
			if ( Util::extensionExists( "xml" ) ) 
			{
				$this->phpext = "xml";
				$this->force_caps = 1;
			} 
			else if ( Util::extensionExists( "domxml" ) ) 
			{
				$this->phpext = "domxml";
			}
		}

		if ( strlen( $doc ) < 1 )
			return;

		$this->parse( $doc );		
	}
	
	
	/**
	 * Parses a complete ini document, stored in a string.
	 *
	 * @param $doc A complete deep ini document string.
	 * @access public
	 */ 
	function parse( $doc ) 
	{
		$this->clear();
		$this->startHandler( 0, trim( "root" ), array() );
		$lines = explode( "\n", $doc );
		
		foreach ( $lines as $l )
			$this->parseLine( $l );
		
		$this->endHandler( 0, '' );
	}

	/**
	 * Parses a deep ini file, using the internal parseLine() method
	 * @param $file_path The path to the file being parsed
	 * @access public
	 */
	function parseFile( $file_path ) 
	{
		$this->clear();
		$this->startHandler( 0, trim( "root" ), array() );

		if ( $f = fopen( $file_path, "r" ) ) 
		{
			while ( $str = fgets( $f, 4096 ) )
				$this->parseLine( trim( $str ) );
		}
		
		$this->endHandler( 0, '' );
	}

	/**
	 * Parses a single line of a deep ini document (used by parse() and parseFile()).
	 *
	 * @param $str A single line string (no CR)
	 * @access public
	 */
	function parseLine( $str ) 
	{
		// ## Comments
		if ( ereg( "^##(.*)$", $str, $regs ) )
		{
			$this->commentHandler( 0, $regs[1] . "\n" );
			return;
		}
		// @@ Attributes
		/*
		else if ( ereg( "^@@[[:space:]]*([^[:space:]]+)[[:space:]]+(.*)$", $str, $regs ) )
		{
			$this->attrHandler( 0, $regs[1], $regs[2] );
			return;
		}
		*/
		
		$state = "";
		$start = $ltct = 0;
		
		for ( $i = 0; $i < strlen( $str ); $i++ ) 
		{
			// Opening bracket descends
			if ( $str[$i] == "[" ) 
			{
				$state = "[";
				
				if ( $this->depth > 1 && $ltct > 0 )
					$this->cdataHandler( 0, trim( substr( $str, $start, $ltct ) ) );
					
				$start = $i + 1;
				$ltct  = 0;
			}
			// Colon delimits the depth name 
			else if ( $str[$i] == ":" && $state == "[" ) 
			{
				$state   = "";
				$attrs   = array( 'type' => 'structure' );
				$tag_tmp = trim( substr( $str, $start, $ltct ) );
				
				if ( ereg( "^([" . join( "", array_keys( $this->ele_prefixes ) ) . "]?)([A-Za-z0-9\._-]+)$", $tag_tmp, $regs ) )
				{
					if ( isset( $this->ele_prefixes[ $regs[1] ] ) )
						$attrs['type'] = $this->ele_prefixes[ $regs[1] ];
					
					$this->startHandler( 0, $regs[2], $attrs );
					$start = $i + 1;
					$ltct = 0;
				}
			} 
			else if ( $str[$i] == "\\" ) 
			{
				$str = substr( $str, 0, $i ) . substr( $str, $i + 1 );
				$ltct++;
			}
			// Check for closing bracket 
			else if ( $str[$i] == "/" && $str[$i + 1] == "]" ) 
			{
				$this->cdataHandler( 0, trim( substr( $str, $start, $ltct ) ) );
				$this->endHandler( 0, '' );
			} 
			else 
			{	
				$ltct++;
			}
		}
		
		if ( $this->depth > 1 && $ltct > 0 )
			$this->cdataHandler( 0, trim( substr( $str, $start, $ltct ) ) . "\n" );
	}

	/**
	 * Clear the document.
	 *
	 * @access public
	 */
	function clear() 
	{
		$this->root  = new DeepIniXMLElement();		
		$this->depth = 0;
	}
	
	/**
	 * Outputs the tree as a deep ini file.
	 *
	 * @access public
	 */
	function out( $doc, $lvl = 0 )
	{
		$str  = "";
		$tabs = str_repeat( "\t", $lvl );
		
		if ( $doc->comment )
		{
			foreach( explode( "\n", $doc->comment ) as $cline )
			{
				if ( $cline != "" )
					$str .= $tabs . "!! " . trim( $cline ) . "\n";
			}
		}
		
		foreach( $doc->children as $c )
		{
			$str .= $tabs;
			$str .= "[" . array_search( $c->attrs['type'], $this->ele_prefixes ) . $c->name . ":";

			if ( count( $c->children ) > 0 )
				$str .= "\n" . $this->out( $c, $lvl + 1 ) . $tabs;
			else
				$str .= " " . $c->content . " ";
			
			$str .= "/]\n";
		}
		
		return $str;
	}
	
	/**
	 * Returns the root element of the document.
	 *
	 * @return DeepINIXMLElement
	 * @access public
	 */
	function root() 
	{
		return $this->root;
	}
	
	/**
	 * Dumps the tree to the PHP STDOUT.
	 *
	 * @access public
	 */
	function dumpAll() 
	{
		$this->_dump( $this->root, 0 );
	}

	
	// private methods
	
	/**
	 * Internal XML StartElement Handler.
	 *
	 * @access private
	 */
	function startHandler( $parser, $name, $attrs ) 
	{
		$ele = new DeepIniXMLElement( $name, $attrs, $this->force_caps );
		$this->_newCurrent( $ele );
	} 

	/**
	 * Internal XML EndElement Handler.
	 *
	 * @access private
	 */
	function endHandler( $parser, $name ) {
		array_pop( $this->depth );
	}

	/**
	 * Internal XML CharacterData Handler.
	 *
	 * @access private
	 */
	function cdataHandler( $parser, $data ) 
	{
		$ele = $this->_current();
		
		if ( $this->mixed_content == 0 && count( $ele->children ) > 0 )
			$ele->content = "";
		else
			$ele->addContent( $data );

		$this->_setCurrent( $ele );
	}

	/**
	 * Internal XML Attribute Handler.
	 */
	function attrHandler( $parser, $name, $val ) 
	{
		$ele = $this->_current();
		$ele->addAttribute( $name, $val );
		$this->_setCurrent( $ele );
	}
	
	/**
	 * Internal XML Parser Function.
	 *
	 * @access private
	 */
	function commentHandler( $parser, $data ) 
	{
		$ele = $this->_current();
		$ele->addComment( $data );
		$this->_setCurrent( $ele );
	}
	
	/**
	 * Internal XML Parser Function.
	 *
	 * @access private
	 */
	function _current() 
	{
		return $this->root->getChild( $this->depth );
	}

	/**
	 * Internal XML Parser Function.
	 *
	 * @access private
	 */
	function _setCurrent( $ele ) 
	{
		$this->root->setChild( $this->depth, $ele );
	}

	/**
	 * Internal XML Parser Function.
	 *
	 * @access private
	 */
	function _newCurrent( $ele ) 
	{
		if ( $this->depth == 0 ) 
		{
			$this->root  = $ele;
			$this->depth = array();
		} 
		else 
		{
			$this->depth[] = $this->_nextChild();
			$this->_setCurrent( $ele );
		}
	}

	/**
	 * Internal XML Parser Function.
	 *
	 * @access private
	 */
	function _nextChild() 
	{
		$ele = $this->_current();
		return $ele->nextChild();
	}
	
	/**
	 * Dumps a single element.
	 *
	 * @param $ele Name of the element to dump.
	 * @param $dep Depth of element (used by dumpAll).
	 * @see dumpAll
	 */
	function _dump( $ele, $dep = 0 ) 
	{
		if ( is_object( $ele ) ) 
		{
			print "<BR />\n";
			
			for ( $i = 0; $i < $dep; $i++ ) 
				print ">>";
			
			print "ID: $ele->name CONTENT: " . htmlspecialchars( $ele->content );
			
			if ( count( $ele->children ) > 0 ) 
			{
				for ( $i = 0; $i < count( $ele->children ); $i++ )
					$this->_dump( $ele->children[$i], $dep + 1 );
			}
		}
	}
} // END OF DeepIni

?>
