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
 * @package html_table_selectable
 */
 
/**
 * Constructor
 *
 * @access public
 */
SelectableTableRows = function( oTableElement, bMultiple )
{
	SelectableElements.call( this, oTableElement, bMultiple );
};


SelectableTableRows.prototype = new SelectableElements();
SelectableTableRows.prototype.constructor = SelectableTableRows;
SelectableTableRows.superclass = SelectableElements.prototype;

/**
 * @access public
 */
SelectableTableRows.prototype.isItem = function( node )
{
	return (
		( node != null ) &&
		( node.tagName == "TR" ) &&
		( node.parentNode.tagName == "TBODY" ) &&
		( node.parentNode.parentNode == this._htmlElement ) );
};

/**
 * @access public
 */
SelectableTableRows.prototype.getItems = function()
{
	return this._htmlElement.rows;
};

/**
 * @access public
 */
SelectableTableRows.prototype.getItemIndex = function( el )
{
	return el.rowIndex;
};

/**
 * @access public
 */
SelectableTableRows.prototype.getItem = function( i )
{
	return this._htmlElement.rows[i];
};
