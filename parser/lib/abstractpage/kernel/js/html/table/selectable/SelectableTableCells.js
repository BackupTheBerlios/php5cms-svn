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
SelectableTableCells = function( oTableElement, bMultiple )
{
	SelectableElements.call( this, oTableElement, bMultiple );
};


SelectableTableCells.prototype = new SelectableElements();
SelectableTableCells.prototype.constructor = SelectableTableCells;
SelectableTableCells.superclass = SelectableElements.prototype;

/**
 * @access public
 */
SelectableTableCells.prototype.isItem = function( node )
{
	return (
		( node != null ) &&
		( node.tagName == "TD" ) &&
		( node.parentNode.parentNode.tagName == "TBODY" ) &&
		( node.parentNode.parentNode.parentNode == this._htmlElement ) );
};

/**
 * @access public
 */
SelectableTableCells.prototype.getNext = function( el )
{
	var i = this.getItemIndex( el );
	
	try
	{
		return this.getItem( i + 1 );
	}
	catch ( ex )
	{
		return null;
	}
};

/**
 * @access public
 */
SelectableTableCells.prototype.getPrevious = function( el )
{
	var i = this.getItemIndex( el );
	
	try
	{
		return this.getItem( i - 1 );
	}
	catch ( ex )
	{
		return null;
	}
};

/**
 * @access public
 */
SelectableTableCells.prototype.getItems = function()
{
	var cells, cl;
	var rows = this._htmlElement.rows;
	var rl   = rows.length;
	var tmp  = [];
	var j    = 0;
	
	for ( var y = 0; y < rl; y++ )
	{
		cells = rows[y].cells;
		cl = cells.length;
		
		for ( var x = 0; x < cl; x++ )
			tmp[j++] = cells[x];
	}
	
	return tmp;
};

/**
 * @access public
 */
SelectableTableCells.prototype.getItem = function( i )
{
	var rows = this._htmlElement.rows;
	var rl   = rows.length;
	var cl   = rows[0].cells.length;
	var ri   = Math.floor( i / cl );
	var ci   = i - ri * cl;
	
	return rows[ri].cells[ci];
};

/**
 * @access public
 */
SelectableTableCells.prototype.getItemIndex = function( el )
{
	var rows = this._htmlElement.rows;
	var cl   = rows[0].cells.length;
	var ri   = el.parentNode.rowIndex;
	var ci   = el.cellIndex;
	
	return ri * cl + ci;
};
