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
 * @package gui_grid_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
GridRow = function( p0, sid, bUnescapeData ) 
{
	this.Base = Base;
	this.Base();
	
	var c         = ( p0.length )? p0.length : p0;
	this.id       = gridHandler.getId();
	this.cells    = new Array();
	this.parent   = null;
	this.sid      = sid || null;
	this._deleted = false;
	gridHandler.all[this.id] = this;

	for ( var i = 0; i < c; i++ ) 
	{
		this.cells[i] = new GridCell( ( p0.length > i )? ( ( bUnescapeData )? unescape( p0[i] ) : p0[i]) : "" );
		this.cells[i].parent = this;
	}	
};


GridRow.prototype = new Base();
GridRow.prototype.constructor = GridRow;
GridRow.superclass = Base.prototype;

/**
 * @access public
 */
GridRow.prototype.over = function() 
{
	if ( this.parent._selected != this ) 
	{
		var e = document.getElementById( this.id );
		e.style.background = '#DEDEFF'
	}	
};

/**
 * @access public
 */
GridRow.prototype.out = function() 
{
	if ( this.parent._selected != this ) 
	{
		var e = document.getElementById( this.id );
		e.className = ( ( !this.parent._showsid ) && ( e.rowIndex & 0x01 ) )? 'odd' : 'even';
	}	
};

/**
 * @access public
 */
GridRow.prototype.select = function( b ) 
{
	if ( ( this.parent._selected ) && ( this.parent._selected != this ) ) 
	{
		var e = document.getElementById( this.parent._selected.id );
		e.className = ( ( !this.parent._showsid ) && ( e.rowIndex & 0x01 ) )? 'odd' : 'even';
	}
	
	document.getElementById( this.id ).className = 'selected';
	this.parent._active      = true;
	this.parent._selected    = this;
	this.parent._selectedRow = document.getElementById( this.id );

	if ( b ) 
	{
		document.getElementById( this.id ).childNodes( 0 ).focus();
		document.getElementById( this.parent._main ).scrollLeft = 0
	}
	
	if ( this.parent.onSelect )
		this.parent.onSelect( this );
};

/**
 * @access public
 */
GridRow.prototype.deselect = function( b ) 
{
	if ( !( b ) && ( ( document.activeElement.tagName == 'INPUT' ) || ( document.activeElement.tagName == 'SELECT' ) ) )
		return;
		
	var e = document.getElementById( this.parent._selected.id );
	e.className = ( ( !this.parent._showsid ) && ( e.rowIndex & 0x01 ) )? 'odd' : 'even';

	var w;
	for ( var i = 1; i < e.childNodes.length; i++ ) 
	{
		w = e.childNodes( i ).style.width;
		e.childNodes( i ).style.cssText = this.cells[i-1].style;
		
		if ( w )
			e.childNodes( i ).style.width = w;
	}
	
	this.parent._active = false;
};


// private methods

/**
 * @access private
 */
GridRow.prototype._initRow = function() 
{
	var e, l, v, str;
	var d = this.parent.colLinkData;
	
	for ( var i = 0; i < this.cells.length; i++ ) 
	{
		e = document.getElementById( this.cells[i].id ); 
		v = 0;
		
		if ( ( this.parent.colDefault[i] ) && !( this.cells[i].value ) ) 
			this.cells[i].value = this.parent.colDefault[i];
			
		if ( this.cells[i]._dropdown ) 
		{
			for ( l = 1; l < d[i].length; l++ ) 
			{
				if ( d[i][l][0] == this.cells[i].value ) 
				{ 
					v = l; 
					break; 
				}
			}
			
			str = d[i][v][1];
		}
		else 
		{ 
			str = this.cells[i].value; 
		}
		
		e.childNodes(0).innerText = str;
		this.cells[i]._changed = true;
	}
};

/**
 * @access private
 */
GridRow.prototype._handleKey = function() 
{
	var key = window.event.keyCode;
	
	if ( ( key == 38 ) || ( key == 40 ) || ( key == 35 ) || ( key == 36 ) ) 
	{
		var o;
		
		if ( key == 38 )
			o = document.getElementById( this.id ).previousSibling;
		else if ( key == 40 )
			o = document.getElementById( this.id ).nextSibling;
		else if ( key == 35 )
			o = document.getElementById( this.parent.rows[this.parent.rows.length - 1].id );
		else
			o = document.getElementById( this.parent.rows[0].id );
			
		if ( o ) 
		{
			var main = document.getElementById( this.parent._main );
			var head = document.getElementById( this.parent._head );

			if ( main.scrollTop > o.offsetTop )
				main.scrollTop = o.offsetTop - 1;

			if ( ( main.scrollTop + main.clientHeight ) - o.offsetTop - head.offsetHeight < o.offsetHeight )
				main.scrollTop = o.offsetTop - ( main.clientHeight - o.offsetHeight ) + head.offsetHeight - 1;

			gridHandler.all[o.id].select();
		}
	}
	else if ( key == 13 ) 
	{ 
		this.parent._showBox( this.cells[0].id ); 
	}
	else if ( key == 46 ) 
	{ 
		this.parent.removeRow(); 
	}
	/*
	else if ( key == 33 ) 
	{ 
		document.getElementById( this.parent.id ).childNodes( 1 ).doScroll( 'scrollbarPageUp' ); 
	}
	*/
	/*
	else if ( key == 34 ) 
	{ 
		document.getElementById( this.parent.id ).childNodes( 1 ).doScroll( 'scrollbarPageDown' ); 
	}
	*/
};

/**
 * @access private
 */
GridRow.prototype._toString = function( r ) 
{
	var str = "<tr id=\"" + this.id +"\">";
	// str += "<th>" + ( ( this.parent._showsid )? ( ( this.sid != null )? this.sid : '-' ) : i ) + "</th>";

	for ( var i = 0; i < this.cells.length; i++ )
		str += this.cells[i]._toString(i, r);
	
	str += "</tr>";
	return str;
};

/**
 * @access private
 */
GridRow.prototype._remove = function() 
{
	if ( ( !this._deleted ) && ( this.sid ) ) 
	{ 
		this._deleted = true;
	}
	else 
	{
		for ( var i = 0; i + 1 < this.parent.rows.length; i++ ) 
		{
			if ( this.parent.rows[i].id >= this.id )
				this.parent.rows[i] = this.parent.rows[i + 1];
		}
		
		this.parent.rows[this.parent.length-1] = null;
		this.parent.rows.length -= 1;
	}
};

/**
 * @access private
 */
GridRow.prototype._generate = function( e, r ) 
{
	var bar = document.createElement( "TR" );
	bar.id = this.id;

	for ( var i = 0; i < this.cells.length; i++ ) 
		bar.appendChild( this.cells[i]._generateCell( e, r, i ) );
	
	return bar;
};

/**
 * @access private
 */
GridRow.prototype._dump = function( b ) 
{
	var str = '&' + ( ( this.sid != null )? this.sid : 'new' ) + '=';
	var changed = 0;

	if ( this._deleted ) 
	{
		str += 'd';
		changed++;
	}
	else 
	{
		str += '[';
		
		for ( var i = 0; i < this.cells.length; i++ ) 
		{
			if ( ( this.cells[i]._changed ) || ( b ) ) 
			{ 
				str += "'" + escape( this.cells[i].value ) + "'"; 
				changed++; 
			}
			
			this.cells[i]._changed = false;
			
			if ( i + 1 < this.cells.length )
				str += ',';
		}
		
		str += ']';
	}
	
	if ( changed )
		return str;
	else
		return null;
};
