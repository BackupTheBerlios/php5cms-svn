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
SelectableElements = function( oElement, bMultiple )
{
	this.Base = Base;
	this.Base();
	
	if ( oElement == null )
		return;
		
	this._htmlElement = oElement;
	this._multiple = Boolean( bMultiple );
	
	this._selectedItems = [];
	this._fireChange = true;
	
	var oThis = this;

	var f = function( e )
	{
		if ( e == null )
			e = oElement.ownerDocument.parentWindow.event;
		
		oThis.click( e );
	};
	
	if ( oElement.addEventListener )
		oElement.addEventListener( "click", f, false );
	else if ( oElement.attachEvent )
		oElement.attachEvent( "onclick", f );
};


SelectableElements.prototype = new Base();
SelectableElements.prototype.constructor = SelectableElements;
SelectableElements.superclass = Base.prototype;

/**
 * @access public
 */
SelectableElements.prototype.setItemSelected = function( oEl, bSelected )
{
	if ( !this._multiple )
	{
		if ( bSelected )
		{
			var old = this._selectedItems[0]
			
			if ( oEl == old )
				return;
				
			if ( old != null )
				this.setItemSelectedUi( old, false );
				
			this.setItemSelectedUi( oEl, true );
			this._selectedItems = [oEl];
			this.fireChange();
		}
		else
		{
			if ( this._selectedItems[0] == oEl )
			{
				this.setItemSelectedUi( oEl, false );
				this._selectedItems = [];
			}
		}
	}
	else
	{
		if ( Boolean( oEl._selected ) == Boolean( bSelected ) )
			return;
		
		this.setItemSelectedUi( oEl, bSelected );
		
		if ( bSelected )
		{
			this._selectedItems[this._selectedItems.length] = oEl;
		}
		else
		{
			// remove
			var tmp = [];
			var j = 0;
			
			for ( var i = 0; i < this._selectedItems.length; i++ )
			{
				if ( this._selectedItems[i] != oEl )
					tmp[j++] = this._selectedItems[i];
			}
			
			this._selectedItems = tmp;
		}
		
		this.fireChange();
	}
};

/**
 * This method updates the UI of the item.
 *
 * @access public
 */
SelectableElements.prototype.setItemSelectedUi = function( oEl, bSelected )
{
	if ( bSelected )
		SelectableElements.addClassName( oEl, "selected" );
	else
		SelectableElements.removeClassName( oEl, "selected" );
	
	oEl._selected = bSelected;		
};

/**
 * @access public
 */
SelectableElements.prototype.getItemSelected = function( oEl )
{
	return Boolean( oEl._selected );
};

/**
 * @access public
 */
SelectableElements.prototype.fireChange = function()
{
	if ( !this._fireChange )
		return;
		
	if ( typeof this.onchange == "string" )
		this.onchange = new Function( this.onchange );
		
	if ( typeof this.onchange == "function" )
		this.onchange();
};

/**
 * @access public
 */
SelectableElements.prototype.click = function( e )
{
	var oldFireChange = this._fireChange;
	this._fireChange  = false;
	
	// create a copy to compare with after changes
	var selectedBefore = this.getSelectedItems();	// is a cloned array
	
	// find row
	var el = ( e.target != null )? e.target : e.srcElement;
	
	while ( el != null && !this.isItem( el ) )
		el = el.parentNode;
	
	// happens in IE when down and up occur on different items
	if ( el == null )
	{
		this._fireChange = oldFireChange;
		return;
	}
		
	var rIndex = el;
	var aIndex = this._anchorIndex;
	
	// test whether the current row should be the anchor
	if ( this._selectedItems.length == 0 || ( e.ctrlKey && !e.shiftKey && this._multiple ) )
		aIndex = this._anchorIndex = rIndex;

	if ( !e.ctrlKey && !e.shiftKey || !this._multiple )
	{
		// deselect all
		var items = this._selectedItems;
		
		for ( var i = items.length - 1; i >= 0; i-- )
		{
			if ( items[i]._selected && items[i] != el )
				this.setItemSelectedUi( items[i], false );
		}
		
		this._anchorIndex = rIndex;
		
		if ( !el._selected )
			this.setItemSelectedUi( el, true );
		
		this._selectedItems = [el];
	}
	// ctrl
	else if ( this._multiple && e.ctrlKey && !e.shiftKey )
	{
		this.setItemSelected( el, !el._selected );
		this._anchorIndex = rIndex;
	}
	// ctrl + shift
	else if ( this._multiple && e.ctrlKey && e.shiftKey )
	{
		// up or down?
		var dirUp = this.isBefore( rIndex, aIndex );
		
		var item = aIndex;
		
		while ( item != null && item != rIndex )
		{
			if ( !item._selected && item != el )
				this.setItemSelected( item, true );
			
			item = dirUp? this.getPrevious( item ) : this.getNext( item );
		}
		
		if ( !el._selected )
			this.setItemSelected( el, true );
	}
	// shift
	else if ( this._multiple && !e.ctrlKey && e.shiftKey )
	{
		// up or down?
		var dirUp = this.isBefore( rIndex, aIndex );
		
		// deselect all
		var items = this._selectedItems;
		
		for ( var i = items.length - 1; i >= 0; i-- )
			this.setItemSelectedUi( items[i], false );

		this._selectedItems = [];
		
		// select items in range
		var item = aIndex;
		
		while ( item != null )
		{
			this.setItemSelected( item, true );
			
			if ( item == rIndex )
				break;
				
			item = dirUp? this.getPrevious( item ) : this.getNext( item );
		}
	}

	// find change!!!	
	var found;
	var changed = ( selectedBefore.length != this._selectedItems.length );
	
	if ( !changed )
	{
		for ( var i = 0; i < selectedBefore.length; i++ )
		{
			found = false;
			
			for ( var j = 0; j < this._selectedItems.length; j++ )
			{
				if ( selectedBefore[i] == this._selectedItems[j] )
				{
					found = true;
					break;
				}
			}
			
			if ( !found )
			{
				changed = true;
				break;
			}
		}	
	}

	this._fireChange = oldFireChange;
	
	if ( changed && this._fireChange )
		this.fireChange();
};

/**
 * @access public
 */
SelectableElements.prototype.getSelectedItems = function()
{
	// clone
	var items = this._selectedItems;
	var l     = items.length;
	var tmp   = new Array(l);
	
	for ( var i = 0; i < l; i++ )
		tmp[i] = items[i];
		
	return tmp;
};

/**
 * @access public
 */
SelectableElements.prototype.isItem = function( node )
{
	return ( ( node != null ) && ( node.nodeType == 1 ) && ( node.parentNode == this._htmlElement ) );
};

/**
 * @access public
 */
SelectableElements.prototype.getNext = function( el )
{
	var n = el.nextSibling;
	
	if ( n == null || this.isItem( n ) )
		return n;
		
	return this.getNext( n );
};

/**
 * @access public
 */
SelectableElements.prototype.getPrevious = function( el )
{
	var p = el.previousSibling;
	
	if ( p == null || this.isItem( p ) )
		return p;
		
	return this.getPrevious( p );
};

/**
 * @access public
 */
SelectableElements.prototype.isBefore = function( n1, n2 )
{
	var next = this.getNext( n1 );
	
	while ( next != null )
	{
		if ( next == n2 )
			return true;
			
		next = this.getNext( next );
	}
	
	return false;
};

/**
 * @access public
 */
SelectableElements.prototype.getItems = function()
{
	var tmp = [];
	var j   = 0;
	var cs  = this._htmlElement.childNodes;
	var l   = cs.length;
	
	for ( var i = 0; i < l; i++ )
	{
		if ( cs[i].nodeType == 1 )
			tmp[j++] = cs[i]
	}
	
	return tmp;
};

/**
 * @access public
 */
SelectableElements.prototype.getItem = function( nIndex )
{
	var j  = 0;
	var cs = this._htmlElement.childNodes;
	var l  = cs.length;
	
	for ( var i = 0; i < l; i++ )
	{
		if ( cs[i].nodeType == 1 )
		{
			if ( j == nIndex )
				return cs[i];
			
			j++;
		}
	}
	
	return null;
};

/**
 * @access public
 */
SelectableElements.prototype.getSelectedIndexes = function()
{
	var items = this.getSelectedItems();
	var l     = items.length;
	var tmp   = new Array( l );
	
	for ( var i = 0; i < l; i++ )
		tmp[i] = this.getItemIndex( items[i] );
		
	return tmp;
};

/**
 * @access public
 */
SelectableElements.prototype.getItemIndex = function( el )
{
	var j  = 0;
	var cs = this._htmlElement.childNodes;
	var l  = cs.length;
	
	for ( var i = 0; i < l; i++ )
	{
		if ( cs[i] == el )
			return j;
			
		if ( cs[i].nodeType == 1 )
			j++;
	}
	
	return -1;
};


/**
 * @access public
 * @static
 */
SelectableElements.addClassName = function( el, sClassName )
{
	var s = el.className;
	var p = s.split( " " );
	var l = p.length;
	
	for ( var i = 0; i < l; i++ )
	{
		if ( p[i] == sClassName )
			return;
	}
	
	p[p.length]  = sClassName;
	el.className = p.join( " " );		
};

/**
 * @access public
 * @static
 */
SelectableElements.removeClassName = function( el, sClassName )
{
	var s  = el.className;
	var p  = s.split( " " );
	var np = [];
	var l  = p.length;
	var j  = 0;
	
	for ( var i = 0; i < l; i++ )
	{
		if ( p[i] != sClassName )
			np[j++] = p[i];
	}
	
	el.className = np.join( " " );
};
