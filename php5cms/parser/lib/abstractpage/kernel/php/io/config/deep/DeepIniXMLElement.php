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
 * DOM Element emulation class (for people --without-dom and --with-xml)
 *
 * @package io_config_deep
 */
 
class DeepIniXMLElement extends PEAR 
{
	/** 
	 * DeepIniXMLElement Array of children. 
	 * @access public
	 */
	var $children = null;
	
	/** 
	 * String name of element.
	 * @access public
	 */
	var $name = null;
	
	/** 
	 * String contents of element.
	 * @access public
	 */
	var $content = null;
	
	/** 
	 * Dictionary of element attributes.
	 * @access public
	 */
	var $attrs = null;
	
	/** 
	 * Force caps
	 * @access public
	 */
	var $force_caps = null;

	/** 
	 * Comment
	 * @access public
	 */
	var $comment = null;
	
	
	/** 
	 * Constructor
	 *
	 * DeepIniXMLElement creates a single element based on name and attributes.
	 *
	 * @param  $name  Name of the element.
	 * @param  $attrs Dictionary of element attributes.
	 * @access public
	 */
	function DeepIniXMLElement( $name = "", $attrs = "", $force_caps = 0 ) 
	{
		$this->children = array();
		
		if ( $attrs ) 
			$this->attrs = $attrs;
		
		if ( $name ) 
			$this->name = $name;
		
		$this->content = "";
	}

	
	/**
	 * Use a PHP domxml object to describe this node and it's children.
	 *
	 * @param  $dom A DOMXML class instance (like the one created by xmldoc())
	 * @access public
	 */
	function importDomXML( $dom ) 
	{
		$this->name = $dom->name;
		$this->attributes = array();
		$attributes = $dom->attributes();
		
		if ( is_array( $attributes ) ) 
		{
			foreach( $attributes as $a )
				$this->attributes[ $a->name ] = $a->children[0]->content;
		}
		
		$this->children = array();
		$i = 0;
		$children = $dom->children();
		
		if ( is_array( $children ) ) 
		{
			foreach ( $children as $c ) 
			{
				if ( $c->type == 1 ) 
				{
					$this->children[$i] = new DeepIniXMLElement();
					$this->children[$i]->importDomXML( $c );
					$i++;
				}
			}
		}
		
		$this->content = $dom->content;
	}

	/**
	 * Add an attribute.
	 *
	 * @param  $name The attribute name
	 * @param  $val Set attribute to this value
	 * @access public
	 */
	function addAttribute( $name, $val ) 
	{
		$this->attributes[ $name ] = $val;
	}
	
	/**
	 * Append a string to the element's contents.
	 *
	 * @param  $str String of content to append.
	 * @access public
	 */
	function addContent( $str ) 
	{
		if ( strlen( $this->content ) > 1 )
			$this->content .= " ";
		
		$this->content .= $str;
	}

	/**
	 * Append a string to the element's comments.
	 *
	 * @param  $str String to append
	 * @access public
	 */
	function addComment( $str ) 
	{
		if ( strlen( $this->comment ) > 1 )
			$this->comment .= " ";

		$this->comment .= $str;
	}
	
	/**
	 * Determine if this node has any children.
	 *
	 * @return boolean TRUE if children exist.
	 * @access public
	 */
	function hasChildren()
	{
		return ( count( $this->children )? true : false );
	}

	/**
	 * Access a set of children beneath this root element.
	 *
	 * @param  $names (Optional) An array of names or a single name string to query for. If not supplied, all children are returned.
	 * @return array of DeepIniXMLElements
	 * @access public
	 */
	function children( $names = "" ) 
	{
		if ( is_array( $names ) ) 
		{
			$tmp = array();
			
			while ( list( , $v ) = each( $names ) ) 
			{
				$v = $this->cap( $v );
				$tmp += $this->children( $v );
			}
			
			return $tmp;
		} 
		else if ( is_string( $names ) && strlen( $names ) > 0 ) 
		{
			$tmp = array();
			reset( $this->children );
			$names = $this->cap( $names );
			
			while ( list( , $child ) = each( $this->children ) ) 
			{
				if ( $child->name == $names )
					$tmp[] = $child;
			}
			
			return $tmp;
		} 
		else 
		{
			return $this->children;
		}
	}

	/**
	 * Access a single child of this root element.
	 *
 	 * @return DeepIniXMLElement or -1 on failure
	 * @access public
	 */
	function child( $name ) 
	{
		if ( is_array( $name ) ) 
		{
			$ele = $this;
			
			while ( list( , $v ) = each( $name ) ) 
			{
				$v = $this->cap( $v );
				
				if ( is_object( $ele ) )
					$ele = $ele->child( $v );
			}
			
			return $ele;
		} 
		else 
		{
			reset( $this->children );
			$name = $this->cap( $name );

			while ( list( , $child ) = each( $this->children ) ) 
			{
				if ( $child->name == $name )
					return $child;
			}
		}
		
		return -1;
	}

	/**
	 * Access a single child and return an array. The array will consist of tuples
	 * where the key is the Tag Name and the value is the Tag Content. Tag attributes
	 * are not available from this array.
	 *
	 * @param $name See child() above.
	 * @return array
	 * @access public
	 */
	function childAsArray( $name ) 
	{
		$ele = $this->child( $name );
		$kid_list = array();
		
		if ( is_array( $ele->children ) ) 
		{
			foreach ( $ele->children as $kid )
				$kid_list[$kid->name] = $kid->content;
		}
		
		return $kid_list;
	}

	/**
	 * Return an array of all the children in this element. The array will consist of tuples
	 * where the key is the Tag Name and the value is the Tag Content. Tag attributes
	 * are not available from this array.
	 *
	 * @return array
	 * @access public
	 */
	function childrenAsArray() 
	{
		$kid_list = array();
		
		if ( is_array( $this->children ) ) 
		{
			foreach ( $this->children as $kid ) 
			{
				if ( count( $kid->children ) > 0 )
					$kid_list[$kid->name] = $kid->childrenAsArray();
				else
					$kid_list[$kid->name] = $kid->content;
			}
		}
		
		return $kid_list;
	}

	/**
	 * Access the content of a single child element.
	 *
	 * @param  $name A name string or array. (See <B>child()</B>)
	 * @return string
	 * @access public
	 */
	function childContent( $name ) 
	{
		$ele = $this->child( $name );
		
		if ( !is_object( $ele ) )
			return "";
		
		return $ele->content;
	}

	/**
	 * Access a child by numerical order in the tree.
	 *
	 * @param  $depth An array of orders to traverse.<br />(e.g. $ele->getChild( array( 0, 2, 1 ) ) will go to the first child of the current element, then the third child of that element, and finally the second child of that element.  It is this last element found that is returned.) 
	 * @return DeepIniXMLElement or empty on failure.
	 * @access public
	 */
	function getChild( $depth ) 
	{
		$subs = "";
		
		if ( count( $depth ) < 1 || !is_array( $depth ) )
			return $this;
		
		reset( $depth );
		while ( list( , $v ) = each( $depth ) ) 
		{
			$v = $this->cap( $v );
			$subs .= "->children[$v]";
		}
		 
		eval( "\$ele = \$this$subs;" );
		return $ele;
	}

	/**
	 * Places an element in the current tree.
	 *
	 * @param  $depth An array of orders instructing where to place the element.
	 * @param  $ele DeepIniXMLElement instance to place.
	 * @access public
	 */
	function setChild( $depth, $ele ) 
	{
		$subs = "";
		
		if ( !is_array( $depth ) )
			return;
		
		reset( $depth );
		while ( list( , $v ) = each( $depth ) )
			$subs .= "->children[$v]";
		
		eval( "\$this$subs = \$ele;" );
	}

	/**
	 * Places an element in the current tree following a path of tags.
	 *
	 * @param  $depth An array of tag names instructing where to place the element.
	 * @param  $ele DeepIniXMLElement instance to place.
	 * @access public
	 */
	function setChildByName( $depth_names, $ele ) 
	{
		if ( !is_array( $depth_names ) )
			return;
		
		for ( $i = 0; $i < count( $this->children ); $i++ )
		{
			if ( $this->children[$i]->name == $this->cap( $depth_names[0] ) )
			{
				if ( count( $depth_names ) == 1 )
					$this->children[$i] = $ele;
				else
					$this->children[$i]->setChildByName( array_slice( $depth_names, 1 ), $ele );
			}
		}
	}

	/**
	 * Determines the next child spot available to place.
	 *
	 * @return int
	 * @access public
	 */
	function nextChild() 
	{
		return count( $this->children );
	}

	/**
	 * Gets the value of an attribute, given it's name.
	 *
	 * @return value
	 * @access public
	 */
	function getAttribute( $name ) 
	{
		return $this->attrs[ $this->cap( $name ) ];
	}

	/**
	 * Capitalize if caps are being enforced (the normal "xml" extension auto-caps).
	 *
	 * @access public
	 */
	function cap( $str ) 
	{
		if ( $this->force_caps )
			$str = strtoupper( $str );
		
		return $str;
	}
} // END OF DeepIniXMLElement

?>
