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
 * XPath program flow from depth perspective:
 *
 * > $node_set = evaluate_path($location_path);
 * > $steps = get_steps($location_path);
 * > evaluate_step($context, $steps);
 *    > tokenize_step($step);
 *    > "axis type here"($context, $tokens);
 *    > evaluate_predicate($node_set, $tokens);
 *      > parse_predicate($expression);
 *
 *
 * abstract tree of nodes:		
 *
 * array $nodes["base-uri"]["attributes"] = array
 *  	["name"] = string
 *  	["text"] = string
 *  	["children"] = array
 *  	["parent"] = string
 *  	["elements"] = array (necessary?)
 *  	["document-position"] = string
 *  	["context-position"] = string
 *
 * @package xml_xpath
 */

class XPathClass extends PEAR
{
	/** 
	 * Constructor
	 *
	 * @access public
	 */
	function XPathClass()
	{
		$this->encoding = 'ISO-8859-1';
		
		// array containing all data
		$this->nodes            = array();
		$this->ids              = array();
		$this->curpath          = "";
		$this->context_position = 0;
		$this->doc_position     = 0;
		$this->root_node        = "";
		
		// xml source file vars
		$this->fp;
		$this->mode;
		$this->filename;
		
		// root tagname
		$this->name = "";
		$this->xml_parser;
		
		// for maintaing context state while evaluating to a node-set on mutliple branches
		$this->context    = "";
		$this->descendant = false;
		
		// maps all function names to the object it belongs to
		$this->function_map = array(
			"node-set" => array(
				"last",
				"position",
				"count",
				"id",
				"local-name",
				"namespace-uri",
				"name"
			),
			"string" => array(
				"string",
				"concat",
				"starts-with",
				"contains",
				"substring-before",
				"substring-after",
				"substring",
				"string-length",
				"normalize-space",
				"translate"
			),
			"boolean" => array(
				"boolean",
				"not",
				"true",
				"false",
				"lang"
			),
			"number" => array(
				"number",
				"sum",
				"floor",
				"ceiling",
				"round"
			)
		);
		
		// array of valid axes
		$this->axes = array(
			"child",
			"descendant",
			"parent",
			"ancestor",
			"following-sibling",
			"preciding-sibling",
			"following",
			"preceding",
			"attribute",
			"namespace",
			"self",
			"descendant-or-self",
			"ancestor-or-self"
		);
	}
	
	
	/**
	 * String as xml data.
	 *
	 * @access public
	 */
	function parseString( $string )
	{
		$this->xmlparser = xml_parser_create();
		xml_set_object( $this->xmlparser, &$this );
		xml_set_element_handler( $this->xmlparser, "startElement", "endElement" );
		xml_set_character_data_handler( $this->xmlparser, "characterData" );
		
		if ( !xml_parse( $this->xmlparser, $string, true ) )
		{
			xml_parser_free( $this->xmlparser );
			
			return PEAR::raiseError(
				"XML error: %s at line %d",
				xml_error_string( xml_get_error_code( $this->xmlparser ) ),
				xml_get_current_line_number( $this->xmlparser )
			);
		}
		
		xml_parser_free( $this->xmlparser );	
		return false;
	}
	
	/**
	 * Call if opening a file, else if parsing a string, call parseString.
	 *
	 * @access public
	 */
	function parse( $filename, $mode = "r" )
	{		
		$this->mode = $mode;
		$this->filename = $filename;
		
		if ( $mode != "r" )
			$mode = "r+";
		
		if ( !( $this->fp = fopen( $filename, $mode ) ) )
			return PEAR::raiseError( "Could not open XML file." );
		
		$data = fread( $this->fp, filesize( $filename ) );
		fclose( $this->fp );
		
		return $this->parseString( $data );
	}

	/**
	 * @access public
	 */	
	function startElement( $parser, $name, $attrs )
	{
		// all lowercase
		$name = strtolower( $name );
		
		// set root tagname
		if ( $this->name == "" )
		{
			$this->name = '/' . $name;
			$this->root_node = "/" . $name . "[1]";
		}
		
		// set the path: current path/current element name
		$path = $this->curpath . "/" . $name;
		
		// set relative context and position
		$position = ++$this->ids[$path];
		$relpath  = $name . "[" . $position . "]";
		
		// set document position
		$this->doc_position++;
		
		// set path
		$fullpath = $this->curpath . "/" . $relpath;
		
		// set context position (position within elements of the same name in parent node)
		$this->nodes[$fullpath]["context-position"] = $position;
		
		// for following and preceding axis detection
		$this->nodes[$fullpath]["document-position"] = $this->doc_position;
		
		// convert all attribute names to lower-case
		foreach( $attrs as $att => $val )
			$lower_attrs[strtolower($att)] = $val;
		
		$this->nodes[$fullpath]["attributes"] = $lower_attrs;
		$this->nodes[$fullpath]["name"]       = $name;
		$this->nodes[$fullpath]["text"]       = "";
		$this->nodes[$fullpath]["parent"]     = $this->curpath;
		
		// add this tagname to the elements array of current parent path
		$this->nodes[$this->curpath]["elements"][] = $name;
		
		// add this onto the element count array
		if ( !$this->nodes[$this->curpath]["children"][$name] )
			$this->nodes[$this->curpath]["children"][$name] = 1;
		else
			$this->nodes[$this->curpath]["children"][$name] = $this->nodes[$this->curpath]["children"][$name] + 1;
		
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
		
		// decrement doc_position
		$this->doc_position--;
	}

	/**
	 * @access public
	 */	
	function characterData( $parser, $text )
	{
		$text = $this->convertEntities( $text );
		$this->nodes[$this->curpath]["text"] .= trim( $text );
	}

	/**
	 * @access public
	 */
	function evaluate_path( $location_path, $context = false )
	{
		// get array of steps
		$steps = $this->get_steps( $location_path );
		
		// get rid of the blank one
		array_shift( $steps );
		
		if ( ereg( "^/".$this->nodes[$this->root_node]["name"], $location_path ) )
		{
			array_shift( $steps );
			$context = $this->root_node;
		}
		
		// evaluate path
		return array_reverse( $this->evaluate_step( $context, $steps ) );
	}
	
	/**
	 * @access public
	 */
	function evaluate_step( $context, $steps )
	{
		$node_set = array();
		
		// if context is empty, set it to root node
		if ( $context == "" )
			$context = $this->root_node;
		
		// get next step
		$step = trim( array_shift( $steps ) );
		
		// get the amount of steps left
		$count = count( $steps );
		
		// tokenize step
		$tokens = $this->tokenize_step( $step )
		
		if ( !$tokens || PEAR::isError( $tokens ) )
			return $node_set;
		
		// decide action as defined by axis
		switch ( $tokens["axis"] )
		{
			// attribute
			case "attribute" :
				$node_set = $this->attribute( $context, $tokens );			
				break;
				
			// namespace
			case "namespace" :
				$node_set = $this->namespace( $context, $tokens );			
				break;
				
			// child
			case "child" :
				$node_set = $this->child( $context, $tokens );			
				break;
				
			// descendant
			case "descendant" :
				$this->descendant = true;
				$node_set = $this->descendant( $context, $tokens );			
				break;
				
			// parent
			case "parent" :
				$node_set = $this->parent( $context, $tokens );			
				break;
				
			// following-sibling
			case "following-sibling" :
				$node_set = $this->following_sibling( $context, $tokens );			
				break;
				
			// preceding-sibling
			case "preceding-sibling" :
				$node_set = $this->preceding_sibling( $context, $tokens );			
				break;
				
			// following
			case "following" :
				$node_set = $this->following( $context, $tokens );			
				break;
				
			// preceding
			case "preceding" :
				$node_set = $this->preceding( $context, $tokens );			
				break;
				
			// self
			case "self" :
				$node_set = $this->self($context,$tokens);			
				break;
				
			// descendant-or-self
			case "descendant-or-self" :
				$this->descendant = true;
				$node_set = $this->descendant_or_self( $context, $tokens, &$steps );			
				break;
				
			// ancestor-or-self
			case "ancestor-or-self" :
				$node_set = $this->ancestor_or_self( $context, $tokens );			
				break;
		}
		
		// filter node-set with predicates
		if ( is_array( $tokens["predicates"] ) )
			$node_set = $this->eval_predicate( $node_set, $tokens );
		
		// if not last step, recurse
		if ( count( $steps ) > 0 )
		{	
			if ( !is_array( $node_set ) )
				$node_set = array();
			
			$temp_set = $node_set;
			
			foreach( $temp_set as $node )
			{
				// recurse
				$new_set = $this->evaluate_step( $node, $steps );

				if ( $this->descendant )
					$node_set = array_merge( $node_set, $new_set );
				else
					$node_set = $new_set;
			}
		}
		
		return $node_set;
	}
	
	/**
	 * @access public
	 */
	function tokenize_step( $step )
	{		
		// if the entire step is empty, then the axis is decendant-or-self xpath rec., 2.5 - abbreviated syntax
		if ( $step == "" )
		{
			$tokens["axis"] = "descendant-or-self";
			return $tokens;
		}
		
		// if step contains [, get rid of it, and use the temp string for axis analysis
		if ( ereg( "\[", $step ) )
		{
			$temp_axis = $this->prestr( $step, "[" );
			$temp_predicate = strstr( $step, "[" );
		}
		else
		{
			$temp_axis = $step;
		}
		
		// if the step contains an axis delimiter
		if ( ereg( "::", $temp_axis ) )
		{
			// then retrieve the string preceding the first axis delimiter
			if ( $axis = $this->prestr( $temp_axis, "::" ) )
			{
				// and validate the axis
				if ( $this->validate_axis( $axis ) )
				{
					// then add the axis to the tokens array
					$tokens["axis"] = $axis;
					
					// and get the rest of the step (strip axis and ::)
					if ( !$node_temp = str_replace( "::", "", strstr( $temp_axis, "::" ) ) )
						return PEAR::raiseError( "Must have something after the axis." );
				}
				else
				{
					return PEAR::raiseError( $axis . " is not a valid axis as defined by the w3c xpath recommendation." );
				}
			}
			else
			{
				return PEAR::raiseError( "You must precede an axis delimiter with a string that is a valid axis as defined by the W3C XPATH recommendation." );
			}
		} 
		/* if there is no axis delimiter, we must be using the abbreviated syntax, and 
		   must figure out what the implied axis is.
		   ()			axis is child
		   *			axis is child
		   @string		axis is attribute
		   string		axis is child
		   .			axis is self
		   ..			axis is parent
		*/
		else if ( $temp_axis == "." )
		{
			// axis is self
			$tokens["axis"] = "self";
			$node_temp = $step;
		}
		else if ( $temp_axis == ".." )
		{
			// axis is parent
			$tokens["axis"] = "parent";
			$node_temp = $step;
		}
		else if ( $temp_axis == "*" )
		{
			// axis is child
			$tokens["axis"] = "child";
			$node_temp = $step;
		}
		else if ( ereg( "\(", $temp_axis ) )
		{
			// axis is child
			$tokens["axis"] = "child";
			$node_temp = $step;
		}
		else if ( ereg( "^@", $temp_axis ) )
		{
			// axis is attribute
			$tokens["axis"] = "attribute";
			$node_temp = $step;
		}
		else if ( ereg( "^[a-zA-Z0-9]+$", $temp_axis ) )
		{
			// axis is child
			$tokens["axis"] = "child";
			$node_temp = $step;
		}
		else
		{
			return PEAR::raiseError( "Couldn't find a valid axis." );
		}
		
		// trim
		$node_temp = trim($node_temp);
		
		if ( ereg( "\[", $node_temp ) )
			$node_temp = $this->prestr( $node_temp, "[" );
		
		// if asterisk
		if ( ereg( "^\*$", $node_temp ) )
		{
			// selecting all nodes
			$tokens["node-test"] = "*";
		}
		else if ( $node_temp == ".." )
		{
			// selecting parent
			$tokens["node-test"] = "..";
		}
		else if ( ereg("\(\)", $node_temp ) )
		{
			// must be a function to select a node-type
			//only options are text(),node(),comment(),processing-intruction()
			if ( ereg( "^(node|comment|text|processing-instruction)$", $this->prestr( $node_temp, "(" ) ) )
				$tokens["node-test"] = $node_temp;
		}
		else if ( ereg( "^[0-9a-zA-Z\-_]+$", $node_temp ) )
		{
			$tokens["node-test"] = $node_temp;
		}
		else
		{
			return PEAR::raiseError( $node_temp . " is not a valid node-test, as defined by the W3C XPATH recommendation." );
		}

		if ( $temp_predicate )
		{
			// get array of predicates by splitting along ']'
			$predicates = explode( "]", $temp_predicate );
			
			foreach ( $predicates as $predicate )
			{
				if ( ereg( "^\[", $predicate ) )
					$predicate = substr( $predicate, 1, strlen( $predicate ) - 1 ); // get rid of the '['
				
				if ( $predicate != "" )
					$tokens["predicates"][] = $predicate;
			}
		}
		
		if ( $tokens )
			return $tokens;
		else
			return PEAR::raiseError( "Unhandled error on " . $step );
	}

	/**
	 * @access public
	 */	
	function validate_axis( $axis )
	{
		// validate axis
		foreach ( $this->axes as $test )
		{
			if ( $axis == $test )
				return true;
		}
		
		return false;
	}
	
	/**
	 * Sub-expressions will always evaluate to either true of false.
	 *
	 * @access public
	 */
	function eval_predicate( $node_set, $tokens )
	{		
		foreach ( $tokens["predicates"] as $expression )
		{
			// parse the expression
			$predicate = $this->parse_predicate( $expression );

			// if a function is present
			if ( $predicate["function"] )
			{
				if ( $predicate["function"] == "position" )
				{
					foreach ( $node_set as $path )
					{
						if ( $this->nodes[$path]["context-position"] == $predicate["literal"] )
							$new_nodeset[] = $path;
					}
				}
			}
			
			// axis = attribute
			if ( $predicate["axis"] == "attribute" )
			{			
				if ( $predicate["operator"] == "=" )
				{				
					foreach ( $node_set as $path )
					{
						if ( $this->nodes[$path]["attributes"][$predicate["node-test"]] == $predicate["literal"] )
							$new_nodeset[] = $path;
					}
				}
			}
		}
		
		return $new_nodeset;
	}

	/**
	 * @access public
	 */		
	function parse_predicate( $expression )
	{	
		// test to see if predicate is a single integer
		if ( ereg( "^[1-9]+$", $expression ) )
		{
			$exp["operator"] = "=";
			$exp["function"] = "position";
			$exp["literal"]  = $expression;
			
			return $exp;
		}
		
		// get operator
		if ( ereg( "=", $expression ) )
			$exp["operator"] = "=";
		
		// get axis
		$op = $exp["operator"];
		$axis_temp = $this->prestr( $expression, "$op" );
		
		// test for @
		if ( ereg( "^@", $axis_temp ) )
		{
			$exp["axis"] = "attribute";
			
			// get node-test
			$exp["node-test"] = str_replace( "@", "", $axis_temp );
		}
		
		// get literal
		$tmp = str_replace( "\"", "", stripslashes(strstr($expression,$op)) );
		$exp["literal"] = str_replace( $op, "", $tmp );
		
		return $exp;
	}
	
	/**
	 * Returns an array of location steps.
	 *
	 * @access public
	 */
	function get_steps( $location_path )
	{
		$steps = explode( "/", $location_path );
		return $steps;
	}
	
	/**
	 * Verifies that the context node contains a child by the name of $child.
	 *
	 * @access public
	 */
	function validate_step( $context_node, $child )
	{
		foreach ( $this->nodes as $node => $data )
		{
			if ( $this->nodes[$node]["name"] == $context_node )
			{
				if ( $this->nodes[$node]["type"] == "element" )
				{
					foreach ( $this->nodes[$node]["elements"] as $child_element )
					{
						if( $child_element == $child )
							return true;
					}
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Function that returns all text before delimiter, not inluding delimiter.
	 *
	 * @access public
	 */
	function prestr( $string, $delim )
	{
		return substr( $string, 0, strlen( $string ) - strlen( strstr( $string, "$delim" ) ) );
	}
	
	/**
	 * Returns the nodeset that matches the location-path submitted.
	 *
	 * @access public
	 */
	function get_nodeset()
	{
		return $this->returnable;
	}

	/**
	 * @access public
	 */	
	function add_node( $path, $name, $cdata, $attributes = false )
	{	
		// is the given parent valid?
		if ( $this->nodes[$path] )
		{	
			// does the given parent have any children?
			if ( $this->nodes[$path]["children"][$name] )
			{		
				// update the parent's child quantity by that name
				$qty = $this->nodes[$path]["children"][$name] + 1;
				$this->nodes[$path]["children"][$name] = $qty;
				
				// make path for new node
				$new_path = $path . "/" . $name . "[" . $qty . "]";
			}
			else
			{	
				// add child to children array for parent
				$position = 1;
				$this->nodes[$path]["children"][$name] = $position;
				
				// make path for new node
				$new_path = $path . "/" . $name . "[1]";
			}
			
			// add node to array
			$this->nodes[$new_path]["context_position"]  = $position;
			$this->nodes[$new_path]["document-position"] = $this->nodes[$path]["document-position"] + 1;
			
			if ( $attributes )
				$this->nodes[$new_path]["attributes"] = $attributes;
			
			$this->nodes[$new_path]["name"]     = $name;
			$this->nodes[$new_path]["text"]     = addslashes( $cdata );
			$this->nodes[$new_path]["parent"]   = $path;
			$this->nodes[$new_path]["elements"] = array();
			$this->nodes[$new_path]["children"] = array();
		}
		else
		{
			return PEAR::raiseError( "Path does not exist in this xml document: " . $path );
		}
		
		return $new_path;
	}

	/**
	 * @access public
	 */
	function xpath_add_node( $path )
	{
		// what kind of path?
		if ( !ereg( "^/", $location_path ) )
		{
			// it's a relative location path, not supported yet
			return PEAR::raiseError( "Not supporting relative location paths for writing." );
		}
		
		// get array of steps
		$steps = $this->get_steps( $location_path );
		
		// get rid of the blank one
		array_shift ($steps );
		
		// trim last step
		$new_step = substr( $location_path, strrchr( $location_path, "/" ) + 1, strlen( $location_path ) - strrchr( $location_path, "/" ) + 1 );

		// trim location path
		$location_path = substr( $location_path, 0, $strrchr( $location_path, "/" ) );

		// evaluate path!
		$nodes = $this->evaluate_path( $steps );

		// get first path
		$path = array_shift( $nodes );
		
		if ( count( $nodes ) > 0 )
			return PEAR::raiseError( "Too many matches, not supporting writing to multiple nodes." );
		
		// tokenize last step
		$tokens = $this->tokenize_step( $new_step );
		
		// this is the part where we add the node to the nodes array
		// all possible token array elements we must account for are:
		// $tokens = array(
		// 		"axis" => "",
		// 		"node-test" => "",
		// 		"predicates" => array()
		// );
		
		// is the given parent valid?
		if ( $this->nodes[$path] )
		{
			// if the parent has children, update it's children array
			if ( count( $this->nodes[$path]["children"] ) > 0 )
			{		
				// loop thru children, and add new node to children array
				foreach( $this->nodes[$path]["children"] as $child => $pos )
				{
					if ( $name == $child )
					{
						// add to children array
						$position = $pos + 1;
						$this->nodes[$path]["children"][$child] = $position;
						
						// make path for new node
						$new_path = $path . "/" . $name . "[" . $position . "]";
					}
				}
			}
			else
			{			
				// add child to children array for parent
				$position = 1;
				$this->nodes[$path]["children"][$child] = $position;
				
				// make path for new node
				$new_path = $path . "/" . $name . "[1]";
			}
			
			// add node to array
			$this->nodes[$new_path]["context_position"] = $position;
			
			if ( $attributes )
				$this->nodes[$new_path]["attributes"] = $attributes;
			
			$this->nodes[$new_path]["name"]     = $name;
			$this->nodes[$new_path]["text"]     = $cdata;
			$this->nodes[$new_path]["elements"] = array();
		}
		else
		{
			return PEAR::raiseError( "Path does not exist in this xml document." );
		}
		
		return $new_path;
	}
	
	/**
	 * Turn path into xml, recursing through paths using $this->nodes["children"].
	 *
	 * @access public
	 */
	function toString( $path, $self = true, $dep = 0 )
	{	
		if ( !$this->nodes[$path] )
			return false;
		
		$txt = "";
		$tab = "\n";
		
		for ( $i = 0; $i < $dep; $i++ )
			$tab .= "  ";
		
		// this var contains the sub-buffer (gets wrapped with parent tags)
		$inn = "";
		
		// get the children of $path
		if ( count( $this->nodes[$path]["children"] ) > 0 )
		{
			$children = $this->nodes[$path]["children"];
			
			// loop thru children
			foreach ( $children as $child => $pos )
			{
				// if any child of $path matches the current step element
				$buffer = "";
				
				while( $pos > 0 )
				{
					$buffer  .= "$pos ";
					$nextpath = $path . "/" . $child . "[" . $pos . "]";
					
					$inn .= $this->toString( $nextpath, true, $dep + 1 );
					$pos--;
				}
			}
		}
		else
		{
			if ( $this->nodes[$path]["text"] != "" )
				$inn .= $this->nodes[$path]["text"];
		}
		
		// wrap data in it's tag
		if ( $self )
		{
			// write tagname
			$txt .= "$tab<" . strtolower( $this->nodes[$path]["name"] );
			
			// write attributes
			if ( is_array( $this->nodes[$path]["attributes"] ) )
			{
				reset( $this->nodes[$path]["attributes"] );
				
				while ( list( $key, $val ) = each( $this->nodes[$path]["attributes"] ) )
				{
					if ( strpos( $val, "\""))
						$quot = "'";
					else
						$quot = "\"";

					$txt .= " " . strtolower( $key ) . "=$quot$val$quot";
				}
			}
			
			// add cdata and rest of tag data
			if ( $inn )
				$txt .=">$inn$tab</".strtolower($this->nodes[$path]["name"]).">";
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
	function save( $filename )
	{
		if ( $fxml = fopen( $filename, "w" ) )
		{
			if ( $txt = $this->toString( $this->root_node ) )
			{
				if ( $txt == "" )
					return PEAR::raiseError( "XML string data is empty." );
				
				fwrite( $fxml, "<?xml version=\"1.0\" encoding='$this->encoding'?>\n" );
				fwrite( $fxml, "$txt\n" );
			}
			else
			{
				return PEAR::raiseError( "Couldn't write the file." );
			}
			
			fclose( $fxml );
		}
		else
		{
			return PEAR::raiseError( "Could not open XML file." );
		}
		
		return true;
	}
	
	/**
	 * @return array
	 * @access public
	 */
	function child( $context, $tokens )
	{
		$node_set = array();
		
		// if node-test contains ()
		if ( ereg( "\(\)", $tokens["node-test"] ) )
		{		
			if ( $this->prestr( $tokens["node-test"], "()" ) == "text" )
			{
				if ( $this->nodes[$context]["text"] != "" )
					$node_set[] = $context;
			}
			else if ( $this->prestr( $tokens["node-test"], "()" ) == "node" )
			{
				$node_set[] = $context;
			}
			else if ( $this->prestr( $tokens["node-test"], "()" ) == "comment" )
			{
				if ( $this->nodes[$context]["comment"] != "" )
					$node_set[] = $context;
			}
			else if ( $this->prestr( $tokens["node-test"], "()" ) == "processing-instruction" )
			{
				if ( $this->nodes[$context]["processing-instruction"] != "" )
					$node_set[] = $context;
			}
			else
			{
				return $node_set;
			}
		}
		else
		{
			if ( !$children = $this->nodes[$context]["children"] )
				return $node_set;
			
			// loop thru children
			foreach( $children as $child => $pos )
			{			
				if ( $tokens["node-test"] == "*" )
				{
					while( $pos > 0 )
					{
						$new_path = $context . "/" . $child . "[" . $pos . "]";
						
						// add node to final node-set
						$node_set[] = $new_path;
						$pos--;
					}
				}
				else if ( $child == $tokens["node-test"] )
				{
					while( $pos > 0 )
					{
						$new_path = $context . "/" . $child . "[" . $pos . "]";
						
						// add node to final node-set
						$node_set[] = $new_path;
						$pos--;
					}
				}
			}
		}
		
		return $node_set;
	}
	
	/**
	 * @access public
	 */
	function attribute( $context, $tokens )
	{
		$node_set = array();
		
		if ( $tokens["node-test"] == "*" )
			$node_set[] = $this->nodes[$context];
		else if ( $this->nodes[$context]["attributes"][$tokens["node-test"]] != "" )
			$node_set[] = $this->nodes[$context];
		
		return $node_set;
	}
	
	/**
	 * @access public
	 */
	function namespace( $context, $tokens )
	{
		$node_set = array();
		
		if ( $this->nodes[$context]["namespace"] != "" )
			$node_set[] = $context;
		
		return $node_set;
	}
	
	/**
	 * @access public
	 */
	function descendant( $context, $tokens, $steps )
	{
		$node_set = array();
		
		// if no children, return node-set
		if ( count( $this->nodes[$context]["children"] ) < 1 )
			return $node_set;
		
		// get children
		$children = $this->nodes[$context]["children"];
		
		// loop thru children
		foreach ( $children as $child => $pos )
		{
			while ( $pos > 0 )
			{
				$new_path = $context . "/" . $child . "[" . $pos . "]";
				
				// get results of $new_path with $steps
				$new_nodes = $this->evaluate_step( $new_path, $steps );
				$node_set  = array_merge( $node_set, $new_nodes );
				
				// recurse thru descendants of context node
				$new_descendant_nodes = $this->descendant( $new_path, $tokens, $steps );
				$node_set = array_merge( $node_set, $new_descendant_nodes);
				$pos--;
			}
		}

		return $node_set;
	}
	
	/**
	 * @access public
	 */
	function parent( $context, $tokens )
	{
		$node_set = array();
		
		// if node-test contains ()
		if ( ereg( "\(\)", $tokens["node-test"] ) )
		{
			if ( $this->prestr( $tokens["node-test"], "()" ) == "text" )
			{
				if ( $this->nodes[$context]["text"] != "" )
					$node_set[] = $this->nodes[$context]["parent"];
			}
			else if ( $this->prestr( $tokens["node-test"], "()" ) == "node" )
			{
				$node_set[] = $this->nodes[$context]["parent"];
			}
			else if ( $this->prestr( $tokens["node-test"], "()" ) == "comment" )
			{
				if ( $this->nodes[$context]["comment"] != "" )
					$node_set[] = $this->nodes[$context]["parent"];
			}
			else if ( $this->prestr( $tokens["node-test"], "()" ) == "processing-instruction" )
			{
				if ( $this->nodes[$context]["processing-instruction"] != "" )
					$node_set[] = $this->nodes[$context]["parent"];
			}
			else
			{
				return $node_set;
			}
		}
		else if ( $tokens["node-test"] == "*" )
		{
			$node_set[] = $this->nodes[$context]["parent"];
		}
		else if ( $tokens["node-test"] == ".." )
		{
			$node_set[] = $this->nodes[$context]["parent"];
		}
		else if ( ereg( "^[a-zA-Z0-9\-_]+$", $tokens["node-test"] ) )
		{
			if ( $tokens["node-test"] == $this->nodes[$context]["parent"] )
				$node_set[] = $this->nodes[$context]["parent"];
		}
		else
		{
			return $node_set;
		}

		return $node_set;
	}
	
	/**
	 * @access public
	 */
	function following_sibling( $context, $tokens )
	{
		$node_set = array();
		$parent   = substr( $context, 0, strrpos( $context, "/" ) - 1 );
		$siblings = $this->child( $parent, $tokens );
		
		foreach( $siblings as $sibling )
		{
			if ( $self )
				$node_set[] = $sibling;
			
			if ( $sibling = $context )
				$self = true;
		}
		
		return $node_set;
	}
	
	/**
	 * @access public
	 */
	function preceding_sibling( $context,$tokens )
	{
		$node_set = array();
		$parent   = substr( $context, 0, strrpos( $context, "/" ) - 1 );
		$siblings = $this->child( $parent, $tokens );
		$self     = true;
		
		foreach( $siblings as $sibling )
		{
			if ( $sibling = $context )
				$self = false;
			
			if ( $self )
				$node_set[] = $sibling;
		}
		
		return $node_set;
	}
	
	/**
	 * @access public
	 */
	function following( $context, $tokens )
	{
		$node_set = array();
		$current_doc_pos = $this->nodes[$context]["document-position"];
		
		foreach ( $this->nodes as $node => $data )
		{
			if ( $self )
			{
				if ( $this->nodes[$node]["document-position"] == $current_doc_pos )
					$node_set[] = $node;
			}
			
			if ( $node == $context )
				$self = true;
		}
		
		return $node_set;
	}
	
	/**
	 * @access public
	 */
	function preceding( $context, $tokens )
	{
		$node_set = array();
		$current_doc_pos = $this->nodes[$context]["document-position"];
		
		foreach( $this->nodes as $node => $data )
		{
			if ( $node == $context )
				$self = false;
			
			if ( $self )
			{
				if ( $this->nodes[$node]["document-position"] == $current_doc_pos )
					$node_set[] = $node;
			}
		}
		
		return $node_set;
	}
	
	/**
	 * @access public
	 */
	function self( $context, $tokens )
	{
		$node_set   = array();
		$node_set[] = $context;
	
		return $node_set;
	}
	
	/**
	 * @access public
	 */
	function descendant_or_self( $context, $tokens, $steps )
	{
		$node_set = array();
		
		// the "self" part of "descendant-or-self"
		$node_set = $this->evaluate_step( $context, $steps );
		
		// if no children, return node-set
		if ( count( $this->nodes[$context]["children"] ) < 1 )
			return $node_set;
		
		// get children
		$children = $this->nodes[$context]["children"];
		
		// loop thru children
		foreach( $children as $child => $pos )
		{
			while ( $pos > 0 )
			{
				$new_path = $context . "/" . $child . "[" . $pos . "]";
				
				// get results of $new_path with $steps
				$new_nodes = $this->evaluate_step( $new_path, $steps );
				$node_set  = array_merge( $node_set, $new_nodes );
				
				$new_descendant_nodes = $this->descendant( $new_path, $tokens,$steps );
				$node_set = array_merge( $node_set, $new_descendant_nodes );
				
				$pos--;
			}
		}
		
		return $node_set;
	}
	
	/**
	 * @access public
	 */
	function ancestor_or_self( $context, $tokens )
	{
		//$node_set = array();
		$node_set = $this->parent( $context, $tokens );
		
		foreach( $node_set as $node )
		{
			$new_node = $this->parent( $node, $tokens );
			$node_set = array_merge( $node_set, $new_node );
		}
		
		return $node_set;
	}
	
	/**
	 * @access public
	 */
	function asString( $path )
	{
		return $this->nodes[$path]["text"];
	}
		
	// arg is an xpath base-uri, returns an associative array of attributes
	/**
	 * @access public
	 */
	function getAttributes( $path )
	{
		return $this->nodes[$path]["attributes"];
	}

	/**
	 * @access public
	 */	
	function convertEntities( $text )
	{
		$text = str_replace( "&",  "&amp", $text );
		$text = str_replace( "<",   "&lt", $text );
		$text = str_replace( ">",   "&gt", $text );
		$text = str_replace( "'", "&apos", $text );
		$text = str_replace( '"', "&quot", $text );
		
		return $text;
	}
} // END OF XPathClass

?>
