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
 * @package gui_grid
 */
 
/**
 * Constructor
 *
 * @access public
 */
HierarchicalGrid = function()
{
	this.Base = Base;
	this.Base();
};


HierarchicalGrid.prototype = new Base();
HierarchicalGrid.prototype.constructor = HierarchicalGrid;
HierarchicalGrid.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
HierarchicalGrid.defaultImagePath = "img/";

/**
 * @access public
 * @static
 */
HierarchicalGrid.plusIcon = "plus.gif";

/**
 * @access public
 * @static
 */
HierarchicalGrid.minusIcon = "minus.gif";

/**
 * @access public
 * @static
 */
HierarchicalGrid.cssPrefix = "hierarchical-grid-";

/**
 * @access public
 * @static
 */
HierarchicalGrid.toggleRow = function( sender )
{
	// if the hidden row has not already been generated, clone the panel into a new row
	var existingRow = document.getElementById( sender.id + "showRow" );
	
	if ( existingRow == null )
	{
		// getting a reference to the table
		var table = HierarchicalGrid._getParentElementByTagName( sender, "TABLE" );
		index = HierarchicalGrid._getParentElementByTagName( sender, "TR" ).sectionRowIndex + 1;
		
		// ???
		// concatenate name of hidden panel => replace "Icon" from sender.id with "Panel"
		rowDivName = HierarchicalGrid._replaceStr( sender.id, "Icon", "Panel" );
		var rowDiv = document.getElementById( rowDivName );
		
		// adding new row to table
		var newRow = table.insertRow( index );
		newRow.id  = sender.id + "showRow";

		// adding new cell to row
		var newTD = document.createElement( "TD" );
		newTD.colSpan = table.rows[1].cells.length;
		var myTD = newRow.appendChild( newTD );
		
		// clone Panel into new cell
		var copy = rowDiv.cloneNode( true );
		copy.style.display = "";
		myTD.appendChild( copy );
		rowDiv.parentNode.removeChild( rowDiv );
			
		sender.src = HierarchicalGrid.defaultImagePath + "minus.gif";
	}
	else
	{
		if ( existingRow.style.display == "none" )
		{
			existingRow.style.display = "";
			sender.src = HierarchicalGrid.defaultImagePath + "minus.gif";
		}
		else
		{
			existingRow.style.display = "none";
			sender.src = HierarchicalGrid.defaultImagePath + "plus.gif";
		}
	}
};


// private methods

/**
 * @access private
 * @static
 */
HierarchicalGrid._replaceStr = function( orgString, findString, replString )
{
	pos = orgString.lastIndexOf( findString );
	return orgString.substr( 0, pos ) + replString + orgString.substr( pos + findString.length );
};

/**
 * @access private
 * @static
 */
HierarchicalGrid._getParentElementByTagName = function( element, tagName )
{
	var element = element;
	
	while ( element.tagName != tagName )
		element = element.parentNode;

	return element;
};
