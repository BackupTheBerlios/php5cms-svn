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
ProcessingInstruction = function( ownerDoc, target, data )
{
	this.target = target;
	this.data   = data;
  
	// inherited from Node
	this.nodeName  = target;
	this.nodeType  = Node.PROCESSING_INSTRUCTION_NODE;
	this.nodeValue = data;
	this.ownerDocument = ownerDoc;
};


ProcessingInstruction.prototype = new Node();
ProcessingInstruction.prototype.constructor = ProcessingInstruction;
ProcessingInstruction.superclass = Node.prototype;

/**
 * @access public
 */
ProcessingInstruction.prototype.getData = function()
{
	return this.data;
};

/**
 * @access public
 */
ProcessingInstruction.prototype.getTarget = function()
{
	return this.target;
};

/**
 * @access public
 */
ProcessingInstruction.prototype.setData = function( data )
{
	this.setNodeValue( data );
};

/**
 * @access public
 */
ProcessingInstruction.prototype.setNodeValue = function( value )
{
	this.data = data;
	this.nodeValue = data;
};
