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
 * @package xml_dom_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
DeepNodeList = function( rootNode, tagName )
{
	this.rootNode = rootNode;
	this.tagName = tagName;
	this.getElementsByTagName( rootNode );
};


DeepNodeList.prototype = new NodeList();
DeepNodeList.prototype.constructor = DeepNodeList;
DeepNodeList.superclass = NodeList.prototype;

/**
 * @access public
 */
DeepNodeList.prototype.getElementsByTagName = function( contextNode )
{
	var nextNode;

	while ( contextNode != null )
	{
		if ( contextNode.hasChildNodes() )
		{
			contextNode = contextNode.firstChild;
		}
		else if ( contextNode != this.rootNode && null != ( next = contextNode.nextSibling ) )
		{
			contextNode = next;
		}
		else
		{
			next = null;
			
			for ( ; contextNode != this.rootNode; contextNode = contextNode.parentNode )
			{
				next = contextNode.nextSibling;
				
				if ( next != null )
					break;
			}
			
			contextNode = next;
		}
		
		if ( contextNode != this.rootNode && contextNode != null && contextNode.nodeType == Node.ELEMENT_NODE )
		{
			if ( this.tagName == "*" || contextNode.tagName == this.tagName )
				this.add( contextNode );
		}
	}
	
	return null;
};
