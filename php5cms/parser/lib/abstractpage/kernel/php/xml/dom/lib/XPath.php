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


using( 'xml.dom.lib.NodeList' );
using( 'xml.dom.lib.Element' );
using( 'xml.dom.lib.Text' );
using( 'xml.dom.lib.Node' );


/**
 * The XPath Class enables searching an XML document with XPATH.
 * Currently, it is not possible to set another context as the root node!
 *
 * @package xml_dom_lib
 */
 
class XPath extends PEAR
{
	/** 
	 * An XPathContext object, used for the eval function.
	 * @var 	object XPathContext
	 * @access	private
	 */
	var $xpath_context;
	
	
	/** 
	 * Constructor
	 *
	 * The constructor requires the Document object as a paramteter. The context will be
	 * set on the root node of the given document.
	 *
	 * @param 	object Document The document object
	 * @access	public
	 */
	function XPath( $document )
	{
		if ( $document != "" )
			$this->xpath_context = $document->document->xpath_new_context();
	}
	
	
	/**
	 * eval evaluates an search path.
	 *
	 * Examples:
	 * //child::* -> selects all element child nodes
	 * //child::text() -> selects all text nodes
	 *
	 * @param  string A search path for the document
	 * @return object NodeList
	 * @access public
	 */
	function evaluate( $search )
	{
		if ( is_object( $this->xpath_context) )
		{
			$result = xpath_eval( $this->xpath_context, $search );
			
			if ( $result )
			{
				$ret = new NodeList;
				
				while ( list( $k, $node ) = each( $result->nodeset ) )
				{
					if ( $node->type == XML_ELEMENT_NODE )
						$childnode = new Element( $node->name );
					else if ( $node->type == XML_TEXT_NODE )
						$childnode = new Text();
					else
						$childnode = new Node( $node->name );
					
					$childnode->node = $node;
					$ret->nodes[] = $childnode;
				}
			}
		}
		
		return $ret;
	}
} // END OF XPath

?>
