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
 * @package util
 */
 
/**
 * Constructor
 *
 * @access public
 */
Field = function( w, h, val )
{
	this.Base = Base;
	this.Base();
	
	this.w = w || 10;
	this.h = h || 10;

	this.field = new Array();
	
	for ( var i = 0; i < this.h; i++ )
	{
		this.field[i] = new Array();
		
		for ( var j = 0; j < this.w; j++ )
			this.field[i][j] = val || "";
	}
};


Field.prototype = new Base();
Field.prototype.constructor = Field;
Field.superclass = Base.prototype;

/**
 * @access public
 */
Field.prototype.getField = function()
{
	return this.field;
};

/**
 * @access public
 */
Field.prototype.hasColumn = function( column )
{
	if ( column > this.w )
		return false;
	else
		return true;
};

/**
 * @access public
 */
Field.prototype.hasRow = function( row )
{
	if ( row > this.h )
		return false;
	else
		return true;
};

/**
 * @access public
 */
Field.prototype.get = function( x, y )
{
	if ( !this.hasColumn( x ) || !this.hasRow( y ) )
		return false;
	else
		return this.field[y][x];
};

/**
 * @access public
 */
Field.prototype.set = function( x, y, val )
{
	if ( !this.hasColumn( x ) || !this.hasRow( y ) )
	{
		return false;
	}
	else
	{
		this.field[y][x] = val;
		return true;
	}
};

/**
 * @access public
 */
Field.prototype.getColumn = function( col )
{
	if ( !this.hasColumn( col ) )
		return false;
	
	var arr = new Array();	
		
	for ( var i in this.field )
		arr[arr.length] = this.field[i][col];
		
	return arr;
};

/**
 * @access public
 */
Field.prototype.getRow = function( row )
{
	if ( !this.hasRow( row ) )
		return false;
		
	var arr = new Array();
	
	for ( var i in this.field )
		arr[arr.length] = this.field[row][i];
		
	return arr;
};

/**
 * @access public
 */
Field.prototype.fillColumn = function( col, val )
{
	if ( !this.hasColumn( col ) )
		return false;
		
	for ( var i in this.field )
		this.field[i][col] = val;
		
	return true;
};

/**
 * @access public
 */
Field.prototype.fillRow = function( row, val )
{
	if ( !this.hasRow( row ) )
		return false;
		
	for ( var i in this.field[row] )
		this.field[row][i] = val;
		
	return true;
};

/**
 * @access public
 */
Field.prototype.dump = function( sep, br )
{
	var i;
	var str = "";
	var sep = sep || ' . ';
	var br  = br  || '\n';

	for ( i = 0; i < this.h; i++ )
	{
		for ( var j = 0; j < this.w; j++ )
			str += this.field[i][j] + ( ( j < this.w - 1)? sep : '' );
			
		str += br;
	}
	
	return str;
};
