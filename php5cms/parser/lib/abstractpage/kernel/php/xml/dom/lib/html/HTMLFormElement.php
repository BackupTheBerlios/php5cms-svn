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


using( 'xml.dom.lib.html.HTMLElement' );
using( 'xml.dom.lib.NodeList' );

 
/**
 * The HTMLFormElement-Class represents a form tag.
 *
 * @package xml_dom_lib_html
 */
 
class HTMLFormElement extends HTMLElement
{
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function HTMLFormElement()
	{
		$this->HTMLElement( "form", true );
	}
	
	
	/** 
	 * setMethod set the method attribute.
	 *
	 * @param	string		$method		either POST or GET
	 * @return	boolean		true
	 * @see		Node::setAttribute()
	 * @access	public
	 */	
	function setMethod( $method )
	{
		return $this->setAttribute( "method", $method );
	}
	
	/** 
	 * setAction set the action attribute.
	 *
	 * @param	string		$action
	 * @return	boolean		true
	 * @see		Node::setAttribute()
	 * @access	public
	 */
	function setAction( $action )
	{
		return $this->setAttribute( "action", $action );
	}
	
	/** 
	 * setTarget set the target attribute.
	 *
	 * @param	string		$target
	 * @return	boolean		true
	 * @see		Node::setAttribute()
	 * @access	public
	 */	
	function setTarget( $target)
	{
		return $this->setAttribute( "target", $target );
	}
	
	/** 
	 * getMethod returns the method attribute, if set.
	 *
	 * @return	string		$method
	 * @see		Element::getAttribute()
	 * @access	public
	 */	
	function getMethod()
	{
		return $this->getAttribute( "method" );
	}
	
	/** 
	 * getAction returns the action attribute, if set.
	 *
	 * @return	string		$action
	 * @see		Element::getAttribute()
	 * @access	public
	 */	
	function getAction()
	{
		return $this->getAttribute( "action" );
	}
	
	/** 
	 * getTarget returns the target attribute, if set.
	 *
	 * @return	string		$target
	 * @see		Element::getAttribute()
	 * @access	public
	 */	
	function getTarget()
	{
		return $this->getAttribute( "target" );
	}
	
	/** 
	 * getElements retrieves all form elements of the current form
	 *
	 * @return	array		An array with all form elements in the scope of the current form
	 * @access	public
	 */
	function getElements()
	{
		$ret = new NodeList;
		
		if ( $this->node )
		{
			$children = $this->node->children();
			
			if ( is_array( $children ) )
			{
				reset( $children );
				
				while ( list( $k, $node ) = each( $children ) )
					$this->_internal_getElements( $node, $ret );
			}
		}
		
		return $ret;
	}
	
	
	// private methods
	
	/**
	 * _internal_getElements searches the sub-tree of a form for child elements.
	 *
	 * This private function, never use directly, works with DomNode objects. It 
	 * searches the subtree for all elements which count in the javascript element list.
	 *
	 * Elements will be returned as:
	 *
	 * input ..... HTMLInputElement
	 * select .... HTMLSelectElement
	 * textarea .. HTMLTextareaElement
	 * button .... HTMLButtonElement
	 * object .... HTMLObjectElement
	 * 
	 * @param	object		DomNode			The DomNode to check for a form element
	 * @param	array		&$NodesFound	A reference to an array. All form elements will be added to the array.
	 * @return	boolean		true
	 * @access	private
	 */
	function _internal_getElements( $NodeToCheck, &$NodesFound )
	{
		// FormElements which count in the javascript list
		$formelements = array(
			"input",
			"select",
			"textarea",
			"button",
			"object"
		);
		
		if ( in_array( strtolower( $NodeToCheck->name ), $formelements ) )
		{
			$classname = "HTML" . ucwords( $NodeToCheck->name ) . "Element";
			$elem = new $classname;
			$elem->node	= $NodeToCheck;
			
			// append the element to the Array
			$NodesFound->nodes[] = $elem;
		}
		else
		{
			// searching the subtree of the current node
			$children = $NodeToCheck->children();
			
			if ( is_array( $children ) )
			{
				reset( $children );
				
				while ( list( $k, $node ) = each( $children ) )
					$this->_internal_getElements( $node, $NodesFound );
			}
		}
	}
} // END OF HTMLFormElement

?>
