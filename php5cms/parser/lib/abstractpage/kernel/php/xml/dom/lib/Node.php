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
 * Base Class for all Nodes in a DOM Tree.
 *
 * The Node interface is the primary datatype for the entire Document Object Model. 
 * All other objects inherit from this class.
 *
 * @package xml_dom_lib
 */
 
class Node extends PEAR
{
	/**
	 * The Name of the Node.
	 *
	 * The Name of the Node depending on it's type
	 * @var		string		$nodeName
	 * @access	private
	 */
	var $nodeName = "";
	
	/**
	 * The Value of the node, used as &lt;tag&gt; $nodevalue &lt;/tag&gt;
	 * @var		string		$nodevalue
	 * @access	private
	 */
	var $nodevalue;
	
	/**
	 * Array with all attributes for the node
	 * This array will only be used, when the node is not yet attached to a document!
	 *
	 * @var		array		$attribute
	 * @access	private
	 */
	var $attribute = array();
	
	/**
	 * contains the DomNode Object
	 * @var		object		DomNode $node
	 * @access	private
	 */
	var $node;
	
	
	/**
	 * Constructor of the Node
	 *
	 * This is a constructor for simple usage of nodes. Derived classes, and the
	 * node class should not be used directly, will define their own constructor.
	 *
	 * @param	string		Name of the node
	 * @param	string 		Optional, the content of the node
	 * @access	public
	 */
	function Node( $tag = "", $content = "" )
	{
		$this->nodeName  = $tag;
		$this->nodevalue = $content;
	}
	
	
	/**
	 * Tag specifies the name of the tag such as table, tr, td, span, p, body etc. for HTML-Documents.
	 *
	 * @param	string		Name of the tag
	 * @param	boolean		Optional, defaults to true. Indicates whether a closing tag exists or not
	 * @return	boolean		returns always true
	 * @access	private
	 */
	function Tag( $tag, $hasClosing = true )
	{
		$this->nodeName   = $tag;
		$this->hasClosing = $hasClosing;

		return true;
	}
	
	/**
	 * appendChild adds a new node to the current node.
	 *
	 * appendChild adds a new node to the current node. If the current node is not yet
	 * attached to the document, a independent node will be created and the new node will
	 * be attached to the independent node. This will be a document independent tree!
	 *
	 * It is possible to clone the independent tree to the document by appending the root
	 * element to a node of the document.
	 *
	 * @param	object 		Node	$NewChild
	 * @return	object 		Node
	 * @access	public
	 * @see		_internal_selfCheck(), _internal_cloneNode()
	 */
	function appendChild( $NewChild )
	{
		if ( is_string( $NewChild ) )
		{
			$classes   = get_declared_classes();
			$classname = $NewChild;
			
			if ( !in_array( $classname, $classes ) )
				$classname = "Node";
			
			$WorkObj = new $classname;
			
			if ( $classname == "Node" )
				$WorkObj->Tag( $NewChild );
		}
		else if ( is_object( $NewChild ) )
		{
			if ( strtolower( get_class( $NewChild ) ) == strtolower( "DomNode" ) )
				$WorkObj = new Node;
			else
				$WorkObj = $NewChild;
		}
		
		if ( ( !is_subclass_of( $WorkObj, "Node" ) ) && ( !strtolower( get_class( $NewChild ) ) == strtolower( "Node" ) ) )
			return PEAR::raiseError( "Node element is of the wrong type." );
		
		// Check for a DomNode-Object - Create one, if it is not there!
		$this->_internal_selfCheck();
		
		// Is the new child attached to a DomNode?
		if ( !$WorkObj->node )
		{
			// New node, i can store the value of the node
			$WorkObj->node = $this->node->new_child( $WorkObj->nodeName, $WorkObj->nodevalue );
			
			// New node, so copy the stored attributes!
			for ( reset( $WorkObj->attribute ); $k = key( $WorkObj->attribute ); next( $WorkObj->attribute ) )
				$WorkObj->node->setattr( $k, $WorkObj->attribute[$k] );
		}
		else
		{
			// Already attached to a Node!
			// Create the New-Child of the same type as the new one ...
			$tmpnode = $this->node->new_child( $WorkObj->nodeName, $WorkObj->nodevalue );
			$this->_internal_cloneNode( $tmpnode, $WorkObj->node, true );
		}
		
		$WorkObj->node = $this->node->lastchild();
		return $WorkObj;
	}
	
	/**
	 * Returns a duplicate of this node, i.e., serves as a generic copy constructor for nodes.
	 * The duplicate node has no parent ( getParentNode() returns null.). 
	 *
	 * @param	boolean 	$deep		Specifies whether to perform a deep clone or not. 
	 * @return	object		Node 		The cloned object!
	 * @access	public
	 */
	function cloneNode( $deep )
	{
		if ( $this->node )
		{
			// create a independent node
			$clone = domxml_node( $this->node->name );
			
			// perform the clone
			$this->_internal_cloneNode( $clone, $this->node, $deep );
			
			// determine the current class of the element/node to clone
			$classname = get_class( $this );
			$ret = new $classname;
			$ret->nodeName = $this->node->name;
			$ret->node = $clone;
			
			return $ret;
		}
		
		return false;
	}
	
	/**
	 * removeChild deletes a child node of the current node
	 *
	 * since the domxml functions don´t provide a method to remove a node, this
	 * is currently not supported!
	 *
	 * @deprec		Currently deprecated because of the missing of an appropriate domxml_... function.
	 * @param		object 		Node 		The node to be deleted
	 * @return		boolean 	true if the deletion was successful, false otherwise
	 * @access		public
	 */
	function removeChild( $ChildToRemove )
	{
		return false;
	}
	
	/**
	 * getOwnerDocument retrieves the document object associated with this node
	 *
	 * since the domxml functions don´t provide a method to retrieve the associated document, this
	 * is currently not supported!
	 *
	 * @return 	object 		Document 	The Document object
	 * @access 	public
	 */
	function getOwnerDocument()
	{
		return false;
	}
	
	/**
	 * getFirstChild
	 *
	 * returns a object linked to the first element of the current node.
	 * If there is no child node, it will return false.
	 *
	 * @return 	object 		Node 		$FirstChild
	 * @access	public
	 */
	function getFirstChild()
	{
		unset( $this->children );
		
		if ( $this->hasMoreElements() )
		{
			if ( $this->children[$this->childOffset]->type == XML_ELEMENT_NODE )
				$ret = new Element( $children[$this->childOffset]->name );
			else
				$ret = new Node( $children[$this->childOffset]->name );
			
			$ret->node = $this->children[$this->childOffset];
			$ret->nodeName = $ret->node->name;
			
			return $ret;
		}
		else return false;
	}
	
	/**
	 * getNextChild
	 *
	 * returns a object linked to the first element of the current node.
	 * If there is no child node, it will return false.
	 *
	 * @return 	object 		Node 		$NextChild
	 * @access 	public
	 */
	function getNextChild( )
	{
		if ( $this->hasMoreElements() )
		{
			$ret = new Node;
			$ret->node = $this->children[$this->childOffset];
			$ret->nodeName = $ret->node->name;
			
			return $ret;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * getLastChild
	 *
	 * returns a object linked to the last element of the current node.
	 * If there is no child node, it will return false.
	 *
	 * @return 	object 		Element 	$LastChild
	 * @access 	public
	 */
	function getLastChild( )
	{
		if ( $this->node )
		{
			$ret = new Element;
			$ret->node = $this->node->lastchild();
			$ret->nodeName = $ret->node->name;
			
			return $ret;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * getNodeById
	 *
	 * parses the children of the current node for a node with a given "id" attribute.
	 * If such a node can be found, the object will be returned, false otherwise.
	 *
	 * @param	string 		$IdToSearch
	 * @return 	object 		Node 		$NodeOfId
	 * @access 	public
	 */
	function getNodeById( $IdToSearch )
	{
		if ( $this->node )
		{
			$ret = new Node;
			$ret->node = $this->_internal_getNodeById( $this->node, $IdToSearch );
			
			if ( is_object( $ret->node ) )
			{
				$ret->nodeName = $ret->node->name;		
				return $ret;
			}
		}
		
		return false;
	}
	
	/**
	 * Checks for subsequent elements.
	 *
	 * @return  boolean Returns true, if the current element has child nodes, false otherwise
	 * @see		getFirstChild(), getNextChild(), hasMoreElements()
	 * @access 	public
	 */
	function hasChildNodes()
	{
		if ( $this->node )
			$this->children = $this->node->children();
		
		if ( is_array( $this->children ) )
			return true;
		
		return false;
	}
	
	/**
	 * Checks, whether threre are more Elements
	 *
	 * @return 	boolean 		Returns true, if the current element has more child nodes.
	 * @see		getFirstChild(), getNextChild()
	 * @access 	private
	 */
	function hasMoreElements()
	{
		if ( !is_array( $this->children ) )
		{
			$this->children = $this->node->children();
			$this->childOffset = 0;
		}
		else
		{
			$this->childOffset++;
		}
		
		if ( $this->childOffset < sizeof( $this->children ) )
			return true;
		
		return false;
	}
	
	/**
	 * getParentNode returns the parent node of the current node, false if no parent is present!
	 *
	 * Note: This function may cause an error! If you call this function from the root element,
	 * the parser crashes.
	 *
	 * @return 	object 		Node 		The parent node.
	 * @access 	public
	 */
	function getParentNode()
	{
		if ( ($this->node ) && (!$this->isRootNode) )
		{
			$ret = new Node;
			$ret->node = $this->node->parent();
			
			if ( $ret->node )
			{
				$ret->nodeName = $ret->node->name;
				return $ret;
			}
		}
		
		return false;
	}
	
	/**
	 * getParent returns the parent node of the current node, false if no parent is present!
	 *
	 * Note: This function may cause an error! If you call this function from the root element,
	 * the parser crashes.
	 *
	 * @return 	object 		Node 		The parent node.
	 * @access 	public
	 */
	function getParent()
	{
		return $this->getParentNode();
	}
	
	/**
	 * getChildNodes returns a list of all child nodes of the current node
	 *
	 * Note: This function may cause an error! If you call this function from the root element,
	 * the parser crashes.
	 *
	 * @return 	object 		NodeList 	An object containing all child elements
	 * @access 	public
	 */
	function getChildNodes()
	{
		if ( $this->node )
		{
			$ret = new NodeList;
			$children = $this->node->children();
			
			if ( is_array( $children ) )
			{
				reset( $children );
				
				while ( list( $k, $node ) = each( $children ) )
				{
					if ( $node->type  == XML_ELEMENT_NODE )
						$childnode = new Element( $node->name );
					else if ( $node->type == XML_TEXT_NODE )
						$childnode = new Text;
					else
						$childnode = new Node( $node->name );
					
					$childnode->node = $node;
					$ret->nodes[] = $childnode;
				}
			}
		}
		
		return $ret;
	}
	
	/**
	 * setNodeValue sets the content of the current node.
	 *
	 * setNodeValue stores the given parameter as the content of the current node.
	 *
	 * @param 	string 		The value of the node
	 * @return 	boolean 	Returns true if the value has been successfully set, false otherwise.
	 * @see		$nodevalue
	 * @access 	public
	 */
	function setNodeValue( $value)
	{
		if ( $this->node )
			$this->node->set_content( $value );
		else
			$this->nodevalue	= $value;
		
		return true;
	}
	
	/**
	 * getNodeValue returns the Value of the node, if present.
	 *
	 * @return 	string 		The value of the node
	 * @access		public
	 */
	function getNodeValue()
	{
		if ( $this->node )
		{
			$nodechild = $this->node->children();
			
			if ( is_array( $nodechild ) )
			{
				if ( $nodechild[0]->type == XML_TEXT_NODE )
					$this->nodevalue = $nodechild[0]->content;
			}
		}
		
		return $this->nodevalue;
	}
	
	/**
	 * getAttributes returns an array with the attributes set.
	 *
	 * @return 	object 		NamedNodeMap $attributes
	 * @access	public
	 */
	function getAttributes()
	{
		if ( $this->node )
		{
			if ( $this->node->type == XML_ELEMENT_NODE )
			{
				$ret = new NamedNodeMap;
				$ret->nodes = $this->node->attributes();
				
				return $ret;
			}
		}
		
		return $false;
	}
	
	/**
	 * toString
	 *
	 * Creates and retrieves the text representation of the current node.
	 *
	 * @return 	string		$retstr 		The string, which represents the current node
	 * @access 	public
	 */
	function toString()
	{
		$retstr	= "";
	
		if ( $this->node )
			$this->_internal_toString( $this->node, $retstr );
		
		return $retstr;
	}
	
	/**
	 * getNodeName The name of the node
	 *
	 * Retrieves the name of the current node. Text-Nodes have no name, therefore the
	 * function returns an empty string.
	 *
	 * @return 	string 		$name
	 * @access	public
	 */
	function getNodeName( )
	{
		if ( $this->node )
		{
			if ( $this->node->type == XML_ELEMENT_NODE )
				return $this->node->name;
		}
		
		return "";
	}
	
    /**
     * getNodeType
     * returns the type of the node
     *
     * The type of the node can be either
     * ->  1 - XML_ELEMENT_NODE
     * ->  2 - XML_ATTRIBUTE_NODE
     * ->  3 - XML_TEXT_NODE
     * ->  4 - XML_CDATA
     * ->  5 - XML_ENTITY_REF_NODE
	 * ->  6 - XML_ENTITY_NODE
	 * ->  7 - XML_PI_NODE
	 * ->  8 - XML_COMMENT_NODE
	 * ->  9 - XML_DOCUMENT_NODE
	 * -> 10 - XML_DOCUMENT_TYPE_NODE
	 * -> 11 - XML_DOCUMENT_FRAG_NODE
	 * -> 12 - XML_NOTATION_NODE
	 *
     * @return 	int 		$type 		The type of the underlying object!
     * @access	public
     */
    function getNodeType( )
	{
    	if ( $this->node )
			return $this->node->type;
		
		return false;
	}
	
	/**
	 * adds a new element to the end of the elementlist
	 *
	 * This function does not return a reference to the inserted Object.
	 * If you intend to insert only one element, its reference will be returnd.
	 * When inserting an array, the function always returns true!
	 *
	 * @param 	object		Node		Element, to be added to the current node. This may be an array, too.
	 * @return	object		Node 		The newly inserted node, now linked to the document, is returned.
	 * @see		appendChild()
	 * @access 	public
	 */
	function addElement( $elem )
	{
		if ( is_array( $elem ) )
		{
			while (list( $k, $el ) = each( $elem ) )
				$this->appendChild( $el );
		}
		else
		{
			return $this->appendChild( $elem );
		}
		
		return true;
	}
	
	/**
	 * creates and retrieves the text representation of the current node
	 *
	 * creates and retrieves the text representation of the current node. This functions
	 * deals with DomNode-Objects, it is for internal use only.
	 * 
	 * @param	object		DomNode		The node to create the string
	 * @param	string 		A reference to a string. The representation of the current node will be appended to this string.
	 * @return 	string		The text representation of the current DomNode
	 * @access 	private
	 * @see		toString()
	 */
	function _internal_toString( $NodeToString, &$retstr )
	{
		switch ( $NodeToString->type )
		{
			case XML_ELEMENT_NODE:
				$retstr	.= "<" . $NodeToString->name;
				
				// Checking all Attributes
				$attrlist = $NodeToString->attributes();
				
				if ( is_array( $attrlist ) )
				{
					while ( list( $k, $attr ) = each( $attrlist ) )
						$retstr	.= " " . $attr->name ."=\"". $NodeToString->getattr( $attr->name ) . "\"";
				}
				
				$elem = $NodeToString->children();
				
				if ( is_array( $elem ) )
				{
					// children are present -> closing the Opening tag
					$retstr	.= ">";
					reset( $elem );
					
					while ( list( $k, $node ) = each( $elem ) )
						$this->_internal_toString( $node, $retstr );
					
					$retstr	.= "</" . $NodeToString->name . ">";
				}
				else
				{
					// no children are present - closing the Opening tag
					$retstr .= "/>";
				}
				
				break;
				
			case XML_TEXT_NODE:
				$retstr .= $NodeToString->content;
				break;
				
			case XML_ENTITY_REF_NODE:
				$retstr	.= "&" . $NodeToString->name . ";";
				break;
				
			case XML_COMMENT_NODE:
				$retstr	.= "<!-- " . $NodeToString->content . " -->";
				break;
		}
	}
	
	/**
	 * _internal_cloneNode clones a node
	 *
	 * This private function copies a independent node to a "already attached" node. This
	 * functions deals with >>DomNode<< Objects!
	 *
	 * @param	object 		DomNode 	$NodeToAdd
	 * @param	object 		DomNode 	$NodeToClone
	 * @param	boolean 	$deep 		Specifies whether to perform a deep clone or to clone only the current node.
	 * @return	object 		DomNode
	 * @see		cloneNode()
	 * @access	private
	 */
	function _internal_cloneNode( $NodeToAdd, $NodeToClone, $deep = true )
	{
		// Clone the attributes!
		$attr = $NodeToClone->attributes();
		
		if ( is_array( $attr ) )
		{
			while ( list( $k, $v ) = each( $attr ) )
				$NodeToAdd->setattr( $v->name, $NodeToClone->getattr( $v->name ) );
		}
		
		if ( $deep )
		{
			// Clone the Elements!
			$elem = $NodeToClone->children();
			
			if ( is_array( $elem ) )
			{
				reset( $elem );
				
				while ( list( $k, $node ) = each( $elem ) )
				{
					$nodechild = $node->children();
					$nodevalue = "";
					
					if ( is_array( $nodechild ) )
					{
						// If the node has children - check for text and entity-ref nodes 
						// Need to collect since no creation of text/entity-ref is possible
						reset( $nodechild );
						$hasElements = false;
						$hasContent	 = false;
						
						while ( list( $ck, $cnode ) = each ($nodechild) )
						{
							switch ( $cnode->type )
							{
								case XML_TEXT_NODE:
									$nodevalue .= $cnode->content;
									break;
									
								case XML_ENTITY_REF_NODE:
									$nodevalue .= "&" . $cnode->name . ";";
									break;
									
								case XML_ELEMENT_NODE:
									$hasElements = true;
									break;
							}
						}
					}
					
					if ( $node->type == XML_ELEMENT_NODE )
						$tmpnode = $NodeToAdd->new_child( $node->name, $nodevalue );
					
					$this->_internal_cloneNode( $tmpnode, $node, $deep );
				}
			}
		}
	}
	
	/**
	 * _internal_selfCheck checks the current node, whether it is linked to a document or not!
	 *
	 * This function is used internally to determine, whether a independent node
	 * has to be created. This is the case, when you add a new element to a node, which
	 * is not yet attached to a parent node!
	 *
	 * @return 	boolean 		true
	 * @see		appendChild()
	 * @access 	private
	 */
	function _internal_selfCheck()
	{
		if ( !$this->node )
		{
			$this->node	= domxml_node( $this->nodeName );
			return false;
		}
		
		return true;
	}
	
	/**
	 * _internal_getNodeById
	 *
	 * This private functions performs the recursive scan through all child nodes for the
	 * current node. This functions deals with >>DomNode<< Objects.
	 *
	 * @param	object 			DomNode
	 * @param	string 			Id of the node to be retrieved
	 * @return 	object 			DomNode The first node with the given id, false if not found!
	 * @access 	private
	 * @see		getNodeById()
	 */
	function _internal_getNodeById( $search, $IdToSearch )
	{
		$attribs = $search->attributes( );
		$ret = false;
		
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
			while ( ( list( $k, $elem ) = each( $nodelist ) ) && ( !$ret ) )
				$ret = $this->_internal_getNodeById( $elem, $IdToSearch );
		}
		
		return $ret;
	}
} // END OF Node

?>
