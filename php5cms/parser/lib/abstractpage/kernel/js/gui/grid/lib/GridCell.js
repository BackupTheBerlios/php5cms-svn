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
GridCell = function( sValue ) 
{
	this.Base = Base;
	this.Base();
	
	this.id       = gridHandler.getId();
	this.value    = sValue;
	this.index    = 0;
	this.parent   = null;
	this.style    = '';
	
	this._collapsed = false;
	this._changed   = false;
	this._dropdown  = false;
	
	gridHandler.all[this.id] = this;
};


GridCell.prototype = new Base();
GridCell.prototype.constructor = GridCell;
GridCell.superclass = Base.prototype;

/**
 * @access public
 */
GridCell.prototype.click = function() 
{
	this.parent.parent._hideBox( true );
	this.parent.select( true );
	this.parent.parent._showBox( this.id );
};


// private methods

/**
 * @access private
 */
GridCell.prototype._toString = function( i, r ) 
{
	this.index = i;
	var str, foo, rows = 0;

	if ( ( this.parent.parent.groupByFirst ) && ( i == 0 ) && ( !this._collapsed ) ) 
	{
		for ( var n = r + 1; n < this.parent.parent.rows.length; n++ ) 
		{
			if ( this.parent.parent.rows[n].cells[0].value == this.value ) 
			{ 
				this.parent.parent.rows[n].cells[0]._collapsed = true; 
				rows++; 
			}
		}	
	}
	
	str = "<td id=\"" + this.id + "\" ";
	
	if ( rows )
		this.style = 'border-bottom: 0px;';
	else if ( this._collapsed )
		this.style = 'border-top: 0px; border-bottom: 0px;';
		
	// this.style += 'width: ' + ( ( this.parent.parent.colSizes[i])? this.parent.parent.colSizes[i] : this.parent.parent.defColSize ) + 'px';

	str += " style=\"" + this.style + "\"";
	str += ">";

	if ( !this.value )
		this.value = '';
		
	if ( this._collapsed ) 
	{ 
		foo = ''; 
	}
	else if ( ( this.parent.parent.colLinkData.length > i ) && ( this.parent.parent.colLinkData[i].length > 1 ) ) 
	{
		this._dropdown = true;
		var d = this.parent.parent.colLinkData[i];
		var selected = 1;
		
		if ( this.value != '' ) 
		{
			if ( this.value.indexOf( ',' ) > 0 ) 
			{ 
				foo = '<Multiple Values>'; 
			}
			else 
			{
				for ( var l = 1; l < d.length; l++ ) 
				{
					if ( d[l][0] == this.value )
						selected = l;
				}
				
				if ( d[0] == -1 )
					foo = '';
				else
					foo = d[selected][1];
			}	
		}
		else { foo = ''; }
	}
	else 
	{ 
		foo = this.value; 
	}
	
	if ( foo ) 
	{
		foo = foo.replace( '<', '&lt;' );
		foo = foo.replace( '>', '&gt;' );
	}
	
	str += '<span>' + foo + '</span></td>';
	return str;
};

/**
 * @access private
 */
GridCell.prototype._generateCell = function( e, r, c ) 
{
	this.index = c;
	var d   = this.parent.parent.colLinkData[c];
	var foo = document.createElement( "TD" );
	var bar = document.createElement( "SPAN" );
	foo.id = this.id;

	if ( d[1] ) 
	{
		this._dropdown = true;
		
		if ( this.value ) 
		{
			var foobar;
			
			if ( this.value.indexOf( ',' ) >= 0 ) 
			{ 
				foobar = '<Multiple Values>'; 
			}
			else 
			{
				for ( var i = 1; i < d.length; i++ ) 
				{
					if ( d[i][0] == this.value )
						selected = i;
				}
				
				if ( this.value == -1 )
					foobar = "";
				else
					foobar = d[selected][1];
			}	
		}
		else 
		{ 
			foobar = ""; 
		}
		
		bar.innerText = foobar;
	}
	else 
	{ 
		bar.innerText =  ( (this.value )? this.value : "" ); 
	}
	
	foo.appendChild( bar );
	return foo;
};
