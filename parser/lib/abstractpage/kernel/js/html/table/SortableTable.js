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
 * @package html_table
 */
 
/**
 * Constructor
 *
 * @access public
 */
SortableTable = function( oTable, oSortTypes ) 
{
	this.Base = Base;
	this.Base();
	
	this.element  = oTable;
	this.tHead    = oTable.tHead;
	this.tBody    = oTable.tBodies[0];
	this.document = oTable.ownerDocument || oTable.document;
	
	this.sortColumn = null;
	this.descending = null;
	
	var oThis = this;
	this._headerOnclick = function( e ) 
	{
		oThis.headerOnclick( e );
	};
	
	// only IE needs this
	var win = this.document.defaultView || this.document.parentWindow;
	this._onunload = function() 
	{
		oThis.destroy();
	};
	
	if ( win && ( "attachEvent" in win ) )
		win.attachEvent( "onunload", this._onunload );
	
	this.initHeader( oSortTypes || [] );
};


SortableTable.prototype = new Base();
SortableTable.prototype.constructor = SortableTable;
SortableTable.superclass = Base.prototype;

/**
 * @access public
 */
SortableTable.prototype.onsort = function() 
{
};

/**
 * Adds arrow containers and events
 * also binds sort type to the header cells so that reordering columns does
 * not break the sort types
 *
 * @access public
 */
SortableTable.prototype.initHeader = function( oSortTypes ) 
{
	var cells = this.tHead.rows[0].cells;
	var l = cells.length;
	var img, c;
	
	for ( var i = 0; i < l; i++ ) 
	{
		c = cells[i];
		img = this.document.createElement( "IMG" );
		img.src = "img/blank.gif";
		c.appendChild( img );
		
		if ( oSortTypes[i] != null ) 
			c._sortType = oSortTypes[i];
		
		if ( "addEventListener" in c )
			c.addEventListener( "click", this._headerOnclick, false );
		else if ( "attachEvent" in c )
			c.attachEvent( "onclick", this._headerOnclick );
	}
	
	this.updateHeaderArrows();
};

/**
 * Remove arrows and events.
 *
 * @access public
 */
SortableTable.prototype.uninitHeader = function() 
{
	var cells = this.tHead.rows[0].cells;
	var l = cells.length;
	var c;
	
	for ( var i = 0; i < l; i++ ) 
	{
		c = cells[i];
		c.removeChild( c.lastChild );
		
		if ( "removeEventListener" in c )
			c.removeEventListener( "click", this._headerOnclick, false );
		else if ( "detachEvent" in c )
			c.detachEvent( "onclick", this._headerOnclick );
	}
};

/**
 * @access public
 */
SortableTable.prototype.updateHeaderArrows = function() 
{
	var cells = this.tHead.rows[0].cells;
	var l = cells.length;
	var img;
	
	for ( var i = 0; i < l; i++ ) 
	{
		img = cells[i].lastChild;
		
		if ( i == this.sortColumn )
			img.className = "sort-arrow " + ( this.descending? "descending" : "ascending" );
		else
			img.className = "sort-arrow";			
	}
};

/**
 * @access public
 */
SortableTable.prototype.headerOnclick = function( e ) 
{
	// find TD element
	var el = e.target || e.srcElement;
	while ( el.tagName != "TD" )
		el = el.parentNode;
	
	this.sort( el.cellIndex );	
};

/**
 * @access public
 */
SortableTable.prototype.getSortType = function( nColumn ) 
{
	var cell = this.tHead.rows[0].cells[nColumn];
	var val  = cell._sortType;
	
	if (val != "")
		return val;
	
	return "String";
};

/**
 * Only nColumn is required
 * if bDescending is left out the old value is taken into account
 * if sSortType is left out the sort type is found from the sortTypes array
 *
 * @access public
 */
SortableTable.prototype.sort = function( nColumn, bDescending, sSortType ) 
{
	if ( sSortType == null )
		sSortType = this.getSortType( nColumn );

	// exit if None	
	if ( sSortType == "None" )
		return;
	
	if ( bDescending == null ) 
	{
		if ( this.sortColumn != nColumn )
			this.descending = true;
		else
			this.descending = !this.descending;
	}	
	
	this.sortColumn = nColumn;
	
	if ( typeof this.onbeforesort == "function" )
		this.onbeforesort();
	
	var f = this.getSortFunction( sSortType, nColumn );
	var a = this.getCache( sSortType, nColumn );
	var tBody = this.tBody;
	
	a.sort( f );
	
	if ( this.descending )
		a.reverse();
	
	if ( SortableTable.removeBeforeSort ) 
	{
		// remove from doc
		var nextSibling = tBody.nextSibling;
		var p = tBody.parentNode;
		
		p.removeChild( tBody );
	}
	
	// insert in the new order
	var l = a.length;
	for ( var i = 0; i < l; i++ )
		tBody.appendChild( a[i].element );
	
	if ( SortableTable.removeBeforeSort ) 
	{	
		// insert into doc
		p.insertBefore( tBody, nextSibling );
	}
	
	this.updateHeaderArrows();
	this.destroyCache( a );
	
	if ( typeof this.onsort == "function" )
		this.onsort();
};

/**
 * @access public
 */
SortableTable.prototype.asyncSort = function( nColumn, bDescending, sSortType ) 
{
	var oThis = this;
	
	this._asyncsort = function() 
	{
		oThis.sort( nColumn, bDescending, sSortType );
	};
	
	window.setTimeout( this._asyncsort, 1 );	
};

/**
 * @access public
 */
SortableTable.prototype.getCache = function( sType, nColumn ) 
{
	var rows = this.tBody.rows;
	var l = rows.length;
	var a = new Array(l);
	var r;
	
	for ( var i = 0; i < l; i++ ) 
	{
		r = rows[i];
		
		a[i] = 
		{
			value:	 this.getRowValue( r, sType, nColumn ),
			element: r
		};
	};
	
	return a;
};

/**
 * @access public
 */
SortableTable.prototype.destroyCache = function( oArray ) 
{
	var l = oArray.length;
	
	for ( var i = 0; i < l; i++ ) 
	{
		oArray[i].value   = null;
		oArray[i].element = null;
		oArray[i] = null;
	}
};

/**
 * @access public
 */
SortableTable.prototype.getRowValue = function( oRow, sType, nColumn ) 
{
	var s;
	var c = oRow.cells[nColumn];
	
	if ( "innerText" in c )
		s = c.innerText;
	else
		s = SortableTable.getInnerText( c );

	return this.getValueFromString( s, sType );
};

/**
 * @access public
 */
SortableTable.getInnerText = function( oNode ) 
{
	var s  = "";
	var cs = oNode.childNodes;
	var l  = cs.length;
	
	for ( var i = 0; i < l; i++ ) 
	{
		switch ( cs[i].nodeType ) 
		{
			case 1: //ELEMENT_NODE
				s += getInnerText( cs[i] );
				break;
				
			case 3:	//TEXT_NODE
				s += cs[i].nodeValue;
				break;
		}
	}
	
	return s;
};

/**
 * @access public
 */
SortableTable.prototype.getValueFromString = function( sText, sType ) 
{
	switch ( sType ) 
	{
		case "Number":
			return Number( sText );
		
		case "CaseInsensitiveString":
			return sText.toUpperCase();
		
		case "Date":
			var parts = sText.split( "-" );
			var d = new Date( 0 );
			d.setFullYear( parts[0] );
			d.setMonth( parts[1] );
			d.setDate( parts[2] );
			
			return d.valueOf();		
	}
	
	return sText;
};

/**
 * @access public
 */
SortableTable.prototype.getSortFunction = function( sType, nColumn ) 
{
	return function compare( n1, n2 ) 
	{
		if ( n1.value < n2.value )
			return -1;
			
		if ( n2.value < n1.value )
			return 1;
			
		return 0;
	};
};

/**
 * @access public
 */
SortableTable.prototype.destroy = function() 
{
	this.uninitHeader();
	var win = this.document.parentWindow;
	
	// only IE needs this
	if ( win && ( "detachEvent" in win ) )
		win.detachEvent( "onunload", this._onunload );
	
	this._onunload      = null;
	this.element        = null;
	this.tHead          = null;
	this.tBody          = null;
	this.document       = null;
	this._headerOnclick = null;
	this.sortTypes      = null;
	this._asyncsort     = null;
	this.onsort         = null;
};


/**
 * @access public
 * @static
 */
SortableTable.gecko = navigator.product == "Gecko";

/**
 * @access public
 * @static
 */
SortableTable.msie = /msie/i.test( navigator.userAgent );

/**
 * Mozilla is faster when doing the DOM manipulations on
 * an orphaned element. MSIE is not.
 * @access public
 * @static
 */
SortableTable.removeBeforeSort = SortableTable.gecko;
