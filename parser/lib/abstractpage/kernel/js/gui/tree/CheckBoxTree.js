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
 * @package gui_tree
 */
 
/**
 * Constructor
 *
 * @access public
 */
CheckBoxTree = function( treeData, container, collapsible, hasBorder )
{
	this.Base = Base;
	this.Base();
	
	this.items       = treeData;
	this.container   = container;
	this.itemsID     = container + 'Items';

	this.collapsible = collapsible;	// can open and close nodes?
	this.hasBorder   = hasBorder;	// display in scrolling window in window?
	this.textButton  = false;		// use text +/- button instead of graphic?
};


CheckBoxTree.prototype = new Base();
CheckBoxTree.prototype.constructor = CheckBoxTree;
CheckBoxTree.superclass = Base.prototype;

/**
 * Function to draw the CheckboxTree html.
 *
 * @access public
 */
CheckBoxTree.prototype.draw = function()
{
	// will hold one row of data
	var cells;
	
	// will remember data from last row for this cell
	var oldCells = new Array();
	var cellKey;
	var cellValue;               
	var cellCode;
	var dhtmlCheckbox;
	      
	var dhtmlCheckboxTree = '<table border="0" cellpadding="1" width="300" align="center" cellspacing="1" bgcolor="white">';

	// find out the number of columns by extracting the columns of the first item
	for ( rowKey in this.items )
	{
		cells = this.items[rowKey].split( ";" ); 
		break;
  	}

	numCols = cells.length;

	// oldCells remembers what was in a given column in the last row.
	// initialize this to a value that should never match. We need to
	// initialize this so that the oldCells array has the same number
	// of columns as does the data
	for ( c = 0; c < numCols; c++ )
		oldCells[c] = "no way"; 

	var c;     // cell (column) index
	var r = 0; // row index
	
	for ( rowKey in this.items )
	{ 
		cells = this.items[rowKey].split(";");    
		
		for ( c = 0; c < numCols; c++ )
		{
			cellValue = cells[c].toLowerCase(); 

			// skip cells that have repeat info for the column
			if ( oldCells[c] == cellValue && c < numCols -1 )
			{
				oldCells[c] = cellValue;
				continue;
			}
			
			oldCells[c] = cellValue;

			switch ( c )
			{
				case 0:
					cellClass = "topicLine";
					break;
				
				default:
					cellClass = "";
			}
			  
			strJS   = 'onclick="CheckBoxTree.toggleTree(this);"';
			cellKey = rowKey;

			// if this isn't a bottom level node (leaf node), we don't want it to
			// submit a value, so erase the key
			if ( c != numCols - 1 )
				cellKey = "";

			dhtmlCheckbox = '<input type="checkbox" '
				+ 'id="'     + this.itemsID + '" ' 
				+ 'cvalue="' + cellValue + '" ' 
				+ 'ccode="'  + cellKey + '" ' 
				+ 'rowIndex = "' + r + '" '
				+ 'colIndex = "' + c + '" '
				+ strJS 
				+ '>';
		
			// if the checkbox tree is supposed to be collapsible, add a +/- button
			// We have to give this an attribute called "linkedID" that indicates which
			// checkbox collection this button is linked to. We also have to give it
			// rowIndex and colIndex attributes, just like the checkbox elements.
			if ( this.collapsible && c < numCols - 1 )
			{  
				// if it is a text button, the + or - text character will be used for the 
		 		// button, otherwise an image will be used.
		 		if ( this.textButton )
				{
					buttonHTML = ' <table align="left" ' 
						+ 'style="border:1px solid #000000; height:14px; cursor:hand" bgcolor="gainsboro" cellspacing="0"'
						+ ' cellpadding="0">'
						+ ' <tr>'
						+ ' <td align="center"' 
						+  ' class="nodeButton"'  
						+  ' rowIndex = "' + r + '" '
						+  ' colIndex = "' + c + '" '
						+  ' linkedID="' + this.itemsID + '"' 
						+  ' onclick="CheckBoxTree.toggleNodeOpen(this)">'
						+ '&#8722;' // this is a unicode minus sign
						+ ' </td>'
						+ '</tr>'
						+ '</table>';
				}
				else
				{
					buttonHTML = ' <img src="../img/icons16x16/tree_minus.gif"' 
						+  ' class="nodeButtonImage" '  
						+  ' rowIndex = "' + r + '" '
						+  ' colIndex = "' + c + '" '
						+  ' linkedID="' + this.itemsID + '"' 
						+  ' onclick="CheckBoxTree.toggleNodeOpen(this)">';
						+  ' >';
				}
				
				dhtmlCheckbox = buttonHTML + dhtmlCheckbox;
			}  

			// If we've found a cell that we are going to write out, make a row
			// for it, with the TD colspan equal to the number of remaining cols
			// and with TD elements preceding it equal in number to c (the col index)	  
	   
			r++;
			dhtmlCheckboxTree += '<tr class="' + cellClass + '">';
			
			for ( i = 0; i < c; i++ )
				dhtmlCheckboxTree += '<td class="spacerCell">&nbsp;</td>';
			
			dhtmlCheckboxTree += '<td colspan="' + ( numCols - c ) + '">' 
				+ dhtmlCheckbox 
				+ cellValue 
				+ '</td>'; 
			
			dhtmlCheckboxTree += '</tr>';
		}		
	}
	
	dhtmlCheckboxTree += '</table>';

	// add a border by putting the table in a table if this was chosen in the constructor
	if ( this.hasBorder )
	{
		dhtmlCheckboxTree = '<table align="center" cellpadding=0 cellpadding=0 bgcolor="#B1CCCE"><tr><td>' 
			+ dhtmlCheckboxTree 
			+ '</td></tr></table>';
	}
  
	// we've got the table built, now stick it in the DIV or other tag that
	// was specified when this object was constructed.
	eval( this.container + ".innerHTML = dhtmlCheckboxTree;" );
};


/**
 * Function to handle turning checkboxes on and off.
 *
 * @access public
 * @static
 */
CheckBoxTree.toggleTree = function( me )
{
	CheckBoxTree.flagIt( me, me.checked );
	CheckBoxTree.toggleChildren( me, me.checked );
	CheckBoxTree.toggleParents( me, me.checked );   
};

/**
 * Check or uncheck a checkbox and add a style.
 *
 * @access public
 * @static
 */
CheckBoxTree.flagIt = function( me, markValue )
{
	me.checked = markValue;
};

/**
 * This is a recursive function that uses getChildren to get a list
 * of children checkboxes and then sets their values to checked or unchecked.
 *
 * @access public
 * @static
 */
CheckBoxTree.toggleChildren = function( me, checkValue )
{
	var i = 0; 
	var children = CheckBoxTree.getChildren( me );
	
	if ( children.length == 0 )
		return;

	for ( i = 0; i < children.length; i++ )
	{
		CheckBoxTree.flagIt( children[i], checkValue );

		//recurse
		CheckBoxTree.toggleChildren( children[i], checkValue );
	}  
};

/**
 * Turn all ancestor checkboxes on or off (recursive).
 *
 * @access public
 * @static
 */
CheckBoxTree.toggleParents = function( me, checkValue )
{
	var i = 0;

	// If we are turning the checkbox off, we need to first
	// check and see if there are any siblings besides the current item
	// that are checked on. If there are, we don't.
	if ( !checkValue )
	{
		var siblings = CheckBoxTree.getSiblings( me );
		var rowIndex = me.rowIndex;
		
		for ( i = 0; i < siblings.length; i++ )
		{
	  		if ( siblings[i].checked != checkValue && siblings[i].rowIndex != rowIndex )
				return;
		}
	} 

	// get the parent, if there is one, flag it 
	var parent = CheckBoxTree.getParent( me );
	
	if ( parent == undefined )
		return;

	CheckBoxTree.flagIt( parent, checkValue );

	// recurse
	CheckBoxTree.toggleParents( parent, checkValue );
};

/**
 * @access public
 * @static
 */
CheckBoxTree.getSiblings = function( me )
{
	var siblings = new Array();
 	siblings = CheckBoxTree.getChildren( CheckBoxTree.getParent( me ) );
 
 	return siblings;
};

/**
 * @access public
 * @static
 */
CheckBoxTree.getParent = function( me )
{
	var r = me.rowIndex / 1;

	if ( me.colIndex == 0 )
		return;

	eval( "var startColIndex = " + me.id + "(r).colIndex;" );
	startColIndex = startColIndex / 1;

	var parent;
	
	// Each item has an id with the same name. The DOM has a collection of all these
	// items which is named with the id name. So, to access these items, we get the
	// id, and then access the DOM collection of that name. Here we loop through the
	// the collection starting with the first previous row, and looking for the next
	// previous item that has a column number that is one less than the column number
	// of "me". It is necessary to use "eval" to do this since otherwise the name of
	// the id/collection would have to be hard coded here. See the LEO file for this
	// program for more documentation. 
	for ( i = r - 1; i >= 0; i-- )
	{
		eval ( "if (" + me.id + "(i).colIndex < startColIndex) parent = " + me.id + "(i);" );
		
		if ( parent != undefined )
			return parent;
	}
};

/**
 * Get the immediate descendant items of the current item.
 * 
 * @access public
 * @static
 */
CheckBoxTree.getChildren = function( me )
{
	var i = 0;

	// return an empty array if there are no children
	if ( me == undefined )
		return new Array();

	var r = me.rowIndex / 1;  

	eval( "var startColIndex = " + me.id + "(r).colIndex;" );
	startColIndex = startColIndex / 1;
	var children = new Array();
	var colIndex;
	
	eval ( "for (i = r + 1; i < " + me.id + ".length; i++) {"
		+ "  colIndex = " + me.id + "(i).colIndex;"
		+ "  if (colIndex <= startColIndex) break;"
		+ "  if (colIndex == startColIndex + 1) children.push(" + me.id + "(i));"
		+ "}" 
	);
	
	return children;
};

/**
 * Functions to open or close nodes.
 *
 * @access public
 * @static
 */
CheckBoxTree.toggleNodeOpen = function( me )
{
	// first figure out whether we are supposed to close or open this node
	var display;
	
	// if it is a text button...
	if ( me.tagName == 'TD' )
	{ 
		if ( me.innerHTML == '+' )
		{
			// The minus sign is unicode 8722. This
			// is different than the keyboard hyphen (-).
			me.innerHTML = "&#8722;";
			display = 'block';
		}
		else
		{
			me.innerHTML = "+";
			display = 'none';
		}
	}
	// it's an image button
	else
	{
		var src = me.src;
		
		if ( src.search(/plus/) != -1 )
		{
			// The minus sign is unicode 8722. This
			// is different than the keyboard hyphen (-).
			me.src = src.replace(/plus/, 'minus');
			display = 'block';
		}
		else
		{
			me.src = src.replace(/minus/, 'plus');
			display = 'none';
		}
	}

	eval( 'var maxR = ' + me.linkedID + '.length' );
	var rowIndex = me.rowIndex / 1;
	var colIndex = me.colIndex;

	// Loop through the elements either hiding or showing. When we
	// get to an element that is "higher" in the tree, i.e. has a lower
	// column number than the element that was clicked on, exit the loop.
	eval( "for (r = rowIndex + 1; r < maxR; r++) {"
		+ "  if (" + me.linkedID + "(r).colIndex <= colIndex) break;"
		+    me.linkedID + "(r).parentElement.parentElement.style.display = '" + display + "';"
		+ "}"   
	);
};

/**
 * This function is used when attaching a collapse behavior to a checkbox.
 *
 * @access public
 * @static
 */
CheckBoxTree.collapse = function( colIndex, itemsID )
{
	if ( colIndex == undefined )
		colIndex = 0;

	var r = 0;
	var children;
	
	eval ( "do {"
		+ "  if (" + itemsID + "(r).colIndex == colIndex) {"
		+ "    display = 'none';"
		+ "    if (" + itemsID + "(r).checked) display = 'block';"
		+ "    r++;"  
		+ "    do {"
		+ "     " + itemsID + "(r).parentElement.parentElement.style.display = display;"
		+ "      r++;"
		+ "      if (r >= " + itemsID + ".length) break;"
		+ "    } while (" + itemsID + "(r).colIndex > colIndex);"
		+ "  } else {"
		+ "  r++;"
		+ "  }"
		+ "} while (r < " + itemsID + ".length);"
	);
};
