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


using( 'xml.dom.lib.Node' );

 
/**
 * Base Class for all Element Nodes in a DOM Tree (XML_ELEMENT_NODE == 1)
 *
 * Nearly all nodes in a tree are element nodes. If you have the following
 * XML document
 * 
 * <library language="php">
 * 	<package name="phpDOM" state="devel" platform="Unix">
 * 		<class>Node</class>
 * 		<class>Document</class>
 * 	</package>
 * 
 		<package name="phpDOM.XHTML" state="devel" platform="Unix"/>
 * 	<package name="phpDOM.ref" state="devel" platform="WinNT"/>
 * </library>
 * 
 * The document has a root element "library" and defines sub-elements, packages.
 * All elements may have attributes and children. If an element does not have
 * any children, the opening tag can be closed by a "/>" and the closing tag
 * can be omitted.
 *
 * @package xml_dom_lib
 */
 
class Element extends Node
{
	/** 
	 * The Name of the Node. 
	 *
	 * The Name of the Node will be used as the tag enclosed in &lt;tagName attrlist&gt;
	 *
	 * @var		string		$tagName
	 * @access	private
	 */
	var $tagName = "";
	
	
	/** 
	 * Constructor
	 *
	 * @param	string		Name of the elment node
	 * @param	string 		Optional, the content of the element node
	 * @access 	public
	 */
	function Element( $tag = "", $content = "" )
	{
		$this->Node( $tag, htmlentities( $content ) );
		$this->tagName = $tag;
	}
	
	
	/**
	 * getTagName retrieves the name of the element 
	 * @return 	string 		$tagName
	 * @access 	public
	 */
	function getTagName()
	{
		if ( $this->node )
			return $this->node->name;
		else
			return $this->tagName;
	}
	
	/** 
	 * getElementById
	 *
	 * parses the children of the current node for a node with a given "id" attribute.
	 * If such a node can be found, the object will be returned, false otherwise.
	 *
	 * @param	string 		$IdToSearch
	 * @return 	object 		Element $ElementWithId 
	 * @access 	public
	 */
	function getElementById( $IdToSearch )
	{
		if ( $this->node )
		{
			$ret = new Element;
			$ret->node = $this->_internal_getElementById( $this->node, $IdToSearch );
			
			if ( is_object($ret->node ) )
			{
				$ret->tagName  = $ret->node->name;
				$ret->nodeName = $ret->node->name;
				
				return $ret;
			}
		}
		
		return false;
	}
	
	/** 
	 * getElementsByTagName
	 *
	 * parses the tree of the current node for all node with the given name.
	 * 
	 * @param	string 		$NameToSearch
	 * @return 	object 		NodeList An list with all objects found will be returned!
	 * @access 	public
	 */
	function getElementsByTagName( $NameToSearch )
	{
		if ( $this->node )
		{
			$nlist = array();
			$this->_internal_getElementsByTagName( $this->node, $NameToSearch, $nlist );
			
			// creating the NodeList
			$ret = new NodeList;
			$ret->nodes	= $nlist;
			
			return $ret;
		}
		
		return false;
	}
	
	/** 
	 * setAttribute set/changes an attribute of the current element.
	 *
	 * @param 	string $attr name of the attribute
	 * @param	string $value value of the attribute
	 * @access 	public
	 * @return	boolean true 
	 */
	function setAttribute( $attr, $value)
	{
		if ( $this->node )
		{
			$this->node->setattr( $attr, $value );
			return true;
		}
		else
		{
			if ( is_bool( $value ) )
			{
				if ( $value )
					$this->attribute[strtolower($attr)] = $value;
				else
					unset( $this->attribute[strtolower($attr)] );
			}
			else
			{
				$this->attribute[strtolower($attr)] = $value;
			}
			
			return true;
		}
		
	}
	
	/** 
	 * setName sets the Name attribute of the current node
	 * 
	 * Setting the name attribute is different from setting the 
	 * name of the node!
	 *
	 * Example: <nodename name="nameattribute"/>
	 *
	 * @param	string		The name attribute
	 * @return	boolean		true
	 * @see 	Element::setAttribute()
	 * @access	public
	 */	
	function setName( $name )
	{
		return $this->setAttribute( "name", $name );
	}
	
	/** 
	 * setId sets the ID attribute of the current node.
	 *
	 * @param	string		The id attribute
	 * @return	boolean		true
	 * @see		Element::setAttribute()
	 * @access	public
	 */	
	function setId( $id )
	{
		return $this->setAttribute( "id", $id );
	}
	
	/** 
	 * getName returns the name attribute of the current node.
	 *
	 * @return	string		the name attribute
	 * @see		Element::getAttribute()
	 * @access	public
	 */	
	function getName()
	{
		return $this->getAttribute( "name" );
	}
	
	/** 
	 * @return	string		$id
	 * @access	public
	 * @see		Element::getAttribute()
	 */	
	function getId( )
	{
		return $this->getAttribute( "id" );
	}
	
	/** 
	 * getAttribute returns the value of the named attribute or false if not set.
	 *
	 * @param	string 		$attr 
	 * @return	string 		$value
	 * @access	public
	 */	
	function getAttribute( $attr )
	{
		if ( $this->node )
		{
			return $this->node->getattr( $attr );
		}
		else
		{
			if (! isset( $this->attribute[strtolower($attr)] ) )
				return false;
				
			if (! $this->attribute[strtolower($attr)] )
				return true;
			
			return $this->attribute[strtolower($attr)];
		}
	}
	
	
	// private methods
	
	/** 
	 * _internal_getElementById
	 *
	 * This private functions performs the recursive scan through all child nodes for the 
	 * current node. This functions deals with >>DomNode<< Objects.
	 *
	 * @param	object 		DomNode
	 * @param	string 		Id of the node to be retrieved
	 * @return 	object 		DomNode The first node with the given id, false if not found!
	 * @access 	private
	 * @see		Element::getElementById()
	 */
	function _internal_getElementById( $search, $IdToSearch )
	{
		$attribs = $search->attributes();
		$ret     = false;
		
		if ( is_array( $attribs ) )
		{
			reset( $attribs );
			
			while ( list( $k, $attr ) = each( $attribs ) )
			{
				if ( $attr->name == "id" )
				{
					if ( $search->getattr( "id" ) == $IdToSearch )
						return $search;
				}
			}
		}
		
		$nodelist	= $search->children();
		
		if ( is_array( $nodelist ) )
		{
			while ( ( list( $k, $elem ) = each( $nodelist )) && (!$ret) )
				$ret = $this->_internal_getElementById( $elem, $IdToSearch );
		}
		
		return $ret;
	}
	
	/** 
	 * _internal_getElementsByTagName
	 *
	 * This private functions performs the recursive scan through all child nodes for the 
	 * current node. This functions deals with >>DomNode<< Objects.
	 *
	 * @param	object 		DomNode
	 * @param	string 		Name of the node to be retrieved
	 * @param	array  		Array to append all nodes with the given tag name.
	 * @return 	object 		DomNode The first node with the given name, false if not found!
	 * @access 	private
	 */
	function _internal_getElementsByTagName( $search, $NameToSearch, &$AppendArray )
	{
		$ret = true;
		
		if ( $search->type == XML_ELEMENT_NODE )
		{
			$nodelist = $search->children();
			
			if ( is_array( $nodelist ) )
			{
				while ( list( $k, $elem ) = each( $nodelist ) )
				{
					if ( $elem->name == $NameToSearch )
					{
						$node = new Element( $elem->name );
						$node->node	   = $elem;
						$AppendArray[] = $node;
					}
					
					$this->_internal_getElementsByTagName( $elem, $NameToSearch, $AppendArray );
				}
			}
		}
		
		return $ret;
	}
} // END OF Element

?>
