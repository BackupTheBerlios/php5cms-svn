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


define( 'XMLTREEIMPL_ELEMENT', 1 );
define( 'XMLTREEIMPL_TEXT',    4 );


/**
 * @package xml
 */
 
class XMLTreeImpl extends PEAR
{
	/**
	 * @access public
	 */
	var $topnode;
	
	/**
	 * @access public
	 */
	var $treepath;
	
	/**
	 * @access public
	 */
	var $xml_parser;
	
	/**
	 * @access public
	 */
	var $nodes;
	
	/**
	 * @access public
	 */
	var $root;
	
	/**
	 * @access public
	 */
	var $ids;
	
	/**
	 * @access public
	 */
	var $tmpdata;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function XMLTreeImpl() 
	{
		$this->treepath = "";
		$this->node     = null;
		$this->nodes    = null;
		$this->root     = null;
		$this->ids      = null;
		$this->tmpdata  = "";
	}

	
	/**
	 * @access public
	 */
	function &getNeededNodes( $nodelist, $tagname = "", $attname = "", $attvalue = "" ) 
	{
		$new_nodelist = array();

		if ( empty( $tagname ) && empty( $attname ) ) 
			return $nodelist;

		if ( !empty( $tagname ) ) 
		{
			if ( empty( $attname ) ) 
			{
				foreach ( $nodelist as $nodepath ) 
				{
					if ( $this->nodes[$nodepath]['name'] == $tagname )
						array_push( $new_nodelist, $nodepath );
				}
			} 
			else 
			{
				foreach ( $nodelist as $nodepath ) 
				{
					if ( ( $this->nodes[$nodepath]['name'] == $tagname ) && ( isset( $this->nodes[$nodepath]['attributes'][$attname] ) ) ) 
					{
						if ( empty( $attvalue ) )
							array_push( $new_nodelist, $nodepath );
						else if ( $this->nodes[$nodepath]['attributes'][$attname] == $attvalue )
							array_push( $new_nodelist, $nodepath );
					}
				}
			}
		} 
		else 
		{
			foreach ( $nodelist as $nodepath ) 
			{
				if ( isset( $this->nodes[$nodepath]['attributes'][$attname] ) ) 
				{
					if ( empty( $attvalue ) ) 
						array_push( $new_nodelist, $nodepath );
					else if ( $this->nodes[$nodepath]['attributes'][$attname] == $attvalue )
						array_push( $new_nodelist, $nodepath );
				}
			}
		}

		return $new_nodelist;
	}

	/**
	 * @access public
	 */
	function &getAllNodes( $nodepath, $recursive, $type = XMLTREEIMPL_ELEMENT ) 
	{
		$retarray = array();

		if ( !empty( $this->nodes[$nodepath]['children'] ) ) 
		{
			// get subnodes of node
			$subnodes = $this->nodes[$nodepath]['children'];

			// run through all subnodes
			foreach ( $subnodes as $subnode_path ) 
			{
				$subnode_path = $nodepath . "/" . $subnode_path;

				// put the node in our list
				if ( $this->nodes[$subnode_path]['type'] & $type )
					array_push( $retarray, $subnode_path );

				// if $recursive is set run also through subnodes
				if ( $recursive ) 
					$retarray = array_merge( $retarray, $this->getAllNodes( $subnode_path, true ) );
			}
		}

		return $retarray;
	}

	/**
	 * @access public
	 */
	function getAttribute( $path, $name ) 
	{
		if ( isset( $this->nodes[$path]['attributes'][$name] ) )
			return $this->nodes[$path]['attributes'][$name];
	}

	/**
	 * @access public
	 */
	function getAttributes( $path ) 
	{
		if ( isset( $this->nodes[$path] ) )
			return $this->nodes[$path]['attributes'];
	}

	/**
	 * @access public
	 */
	function getName( $path ) 
	{
		if ( isset( $this->nodes[$path] ) )
			return $this->nodes[$path]['name'];
	}

	/**
	 * @access public
	 */
	function getDepth( $path ) 
	{
		return $this->nodes[$path]['depth'];
	}

	/**
	 * @access public
	 */
	function getNode( $path, $recursive, $tagname = "", $attname = "", $attvalue = "" ) 
	{
		return $this->getNeededNodes( $this->getAllNodes( $path, $recursive ), $tagname, $attname, $attvalue );
	}

	/**
	 * @access public
	 */
	function getParent( $path, $dstpath, $tagname = "", $attname = "", $attvalue = "" ) 
	{
		$nodelist = array();

		if ( isset( $dstpath ) ) 
		{
			while ( $path != $dstpath ) 
			{
				array_push( $nodelist, $path );
				$path = $this->nodes[$path]['parent'];

				if ( empty( $path ) ) 
					break;
			}
		} 
		else 
		{
			array_push( $nodelist, $path );
			$path = $this->nodes[$path]['parent'];

			if ( empty( $path ) ) 
				break;
		}

		return $this->getNeededNodes( $nodelist, $tagname, $attname, $attvalue );
	}

	/**
	 * @access public
	 */
	function getSibling( $path, $tagname = "", $attname = "", $attvalue = "" ) 
	{
		return $this->getNeededNodes( $this->getAllNodes( $this->nodes[$path]['parent'], false ), $tagname, $attname, $attvalue );
	}

	/**
	 * @access public
	 */
	function getTagData( $path, $recursive = false ) 
	{
		$data  = array();
		$nodes = $this->getAllNodes( $path, $recursive, XMLTREEIMPL_TEXT );

		foreach ( $nodes as $subnode )
			array_push( $data, $this->nodes[$subnode]['content'] );

		return implode( "", $data );
	}

	/**
	 * @access public
	 */
	function parseXML( $xmlstring, $withdata = true, $filename = "" ) 
	{
		$this->xml_parser = xml_parser_create();
		xml_set_object( $this->xml_parser, $this );
		xml_set_element_handler( $this->xml_parser, "startElement", "endElement" );
		
		if ( $withdata ) 
			xml_set_character_data_handler( $this->xml_parser, "dataElement" );

		xml_parser_set_option( $this->xml_parser, XML_OPTION_CASE_FOLDING, false );

		if ( !xml_parse( $this->xml_parser, $xmlstring, true ) ) 
		{
			if ( empty( $filename ) )
				$error_string =  "XML Error: ";
			else
				$error_string =  "XML Error in '$filename': ";
			
			$linenum       = xml_get_current_line_number( $this->xml_parser );
			$error_string .= "'" . xml_error_string( xml_get_error_code( $this->xml_parser ) ) . "'" . " in line " . $linenum;
			$xmllines      = explode( "\n", htmlspecialchars( $xmlstring ) );
			$i             = 0;
			
			foreach ( $xmllines as $line ) 
			{
				$i++;
				$error_string .= str_pad( $i, 4, " ", STR_PAD_LEFT ) .": " . str_replace( "\t", "  ", $line ) ."\n";
			}
			
			return PEAR::raiseError( $error_string );
		}

		xml_parser_free( $this->xml_parser );
	}

	/**
	 * @access public
	 */
	function parseXMLFile( $xml_filename, $withdata = true ) 
	{
		return $this->parseXML( join( "", file( $xml_filename ) ) );
	}

	/**
	 * @access public
	 */
	function &serialize( $nodepath, $include_root = true, $splittag = '', $identdepth = 0 ) 
	{
		if ( !isset( $code ) ) 
			$code='';
		
		if ( !empty( $splittag ) ) 
			$out = array();
			
		$depth          = 0;
		$last_was_text  = true;
		$childnum       = 0;
		$nodestack      = array();
		$childnum_stack = array();
		$rootpath       = $nodepath;
		$ident_chars    = '';

		do 
		{
			$node = &$this->nodes[$nodepath];
	
			if ( !empty( $splittag ) ) 
			{
				if ( $node['name'] == $splittag ) 
				{
					$out[count($out)] = $code;
					$code     = '';
					$childnum = array_pop( $childnum_stack );
					$nodepath = array_pop( $nodestack );
					
					continue;
				}
			}

			if ( count( $node['children'] ) > $childnum ) 
			{
				if ( $childnum == 0 ) 
				{
					switch ( $node['type'] ) 
					{
						case XMLTREEIMPL_ELEMENT:
							if ( !$include_root ) 
								if ( $nodepath == $rootpath ) 
									break;
							
							$ident = str_repeat( $ident_chars, $identdepth + $depth );
							
							if ( !$last_was_text )
								$code .= $ident . "\n";
							
							$code .= $ident . "<" . $node['name'];
							
							foreach ( $node['attributes'] as $key => $value ) 
								$code .= " " . $key . '="' . $value . '"';
								
							$code .= ">";
							$last_was_text = false;

							break;
					}

					$depth++;
				}

				array_push( $nodestack, $nodepath );
				$nodepath .= "/" . $node['children'][$childnum];
				array_push( $childnum_stack, $childnum + 1 );
				$childnum = 0;
			} 
			else if ( empty( $node['children'] ) ) 
			{
				$ident = str_repeat( $ident_chars, $identdepth + $depth );
				
				switch ( $node['type'] ) 
				{
					case XMLTREEIMPL_ELEMENT:
						if ( !$include_root )
						{ 
							if ( $nodepath == $rootpath ) 
								break;
						}
						
						if ( !$last_was_text )
							$code .= $ident . "\n";
						
						$code .= "<" . $node['name'];
						
						foreach ( $node['attributes'] as $key => $value ) 
							$code .= " " . $key . '="' . $value . '"';
						
						$code .= " />";
						$last_was_text = false;
						
						break;
					
					case XMLTREEIMPL_TEXT:
						$code .= $node['content'];
						$last_was_text = true;
						
						break;
				}

				$childnum = array_pop( $childnum_stack );
				$nodepath = array_pop( $nodestack );
			} 
			else 
			{
				if ( !$include_root && $nodepath == $rootpath ) 
				{
					;
				} 
				else 
				{
					$depth--;
					$ident = str_repeat( $ident_chars, $identdepth + $depth );

					if ( !$last_was_text )
						$code .= $ident . "\n";
					
					$code .= $ident . "</" . $node['name'] . ">";
					$last_was_text = false;
				}

				$childnum = array_pop( $childnum_stack );
				$nodepath = array_pop( $nodestack );
			}
		} while ( isset( $nodepath ) );
		
		if ( empty( $splittag ) )
		{
			return $code;
		}
		else 
		{
			$out[count( $out )] = &$code;
			return $out;
		}
	}

	/**
	 * @access public
	 */
	function addNode( $path, $name, $type ) 
	{
		// check whether root element is already set
		if ( empty( $this->root ) )
			$this->root = '/' . $name . '[1]';

		// set the full path and the position.
		$pathname = $path . '/' . $name;

		if ( !isset( $this->ids[$pathname] ) ) 
		{
			$this->ids[$pathname] = 1;
			$position = 1;
			$namepos = $name . '[1]';
		} 
		else 
		{
			$position = ++$this->ids[$pathname];
			$namepos  = $name . '[' . $position . ']';
		}

		$fullpath = $path . '/' . $namepos;

		$node = array(
			'name'       => $name,
			'type'       => $type,
			'parent'     => $path,
			'children'   => array(),
			'attributes' => array(),
			'content'    => '',
			'uniquenum'  => $position,
			'depth'      => 0
		);	

		if ( isset( $this->nodes[$path] ) )
			$node['depth'] = $this->nodes[$path]['depth'] + 1;

		if ( !empty( $path ) )
			array_push( $this->nodes[$path]['children'], $namepos );

		$this->nodes[$fullpath] = $node;

		// return the path of the new node
		return $fullpath;
	}

	/**
	 * @access public
	 */
	function startElement( $parser, $name, $attributes ) 
	{
		if ( !empty( $this->tmpdata ) ) 
		{
			$this->nodes[$this->addNode( $this->treepath, "", XMLTREEIMPL_TEXT )]['content'] = $this->tmpdata;
			$this->tmpdata = "";
		}
		
		$this->treepath = $this->addNode( $this->treepath, $name, XMLTREEIMPL_ELEMENT );
		$this->nodes[$this->treepath]['attributes'] = $attributes;
	}

	/**
	 * @access public
	 */
	function endElement( $parser, $name ) 
	{
		if ( !empty( $this->tmpdata ) ) 
		{
			$this->nodes[$this->addNode( $this->treepath, "", XMLTREEIMPL_TEXT )]['content'] = $this->tmpdata;
			$this->tmpdata = "";
		}
		
		$this->treepath = $this->nodes[$this->treepath]['parent'];
	}

	/**
	 * @access public
	 */
	function dataElement( $parser, $data ) 
	{
		$this->tmpdata .= $data;
	}
} // END OF XMLTreeImpl

?>
