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
 * @package util_array
 */
 
/**
 * Constructor
 *
 * @access public
 */
ArrayList = function()
{
	this.Base = Base;
	this.Base();
	
	this.nativeArray = new Array();
};


ArrayList.prototype = new Base();
ArrayList.prototype.constructor = ArrayList;
ArrayList.superclass = Base.prototype;

/**
 * @access public
 */
ArrayList.prototype.getNative = function()
{
	return this.nativeArray;
};

/**
 * @access public
 */
ArrayList.prototype.setNative = function( nativeArray )
{
	this.nativeArray = nativeArray;
};

/**
 * @access public
 */
ArrayList.prototype.add = function()
{
	switch ( arguments.length )
	{
		case 1:
			return this._add1( arguments[0] );
		
		case 2:
			return this._add2( arguments[0], arguments[1] );
	}
};

/**
 * @access public
 */
ArrayList.prototype.addAll = function()
{
	switch ( arguments.length )
	{
		case 1:
			return this._addAll1( arguments[0] );
		
		case 2:
			return this._addAll2( arguments[0], arguments[1] );
	}
};

/**
 * @access public
 */
ArrayList.prototype.get = function( index )
{
	// Note: It works with index as a name.
	return this.nativeArray[index];
};

/**
 * @access public
 */
ArrayList.prototype.indexOf = function( obj )
{
	var len = this.nativeArray.length;
	
	for ( var i = 0; i < len; i++ )
	{
		var curobj = this.nativeArray[i];
		
		if ( obj == curobj )
			return i;
	}

	return -1;
};

/**
 * @access public
 */
ArrayList.prototype.isEmpty = function()
{
	return ( this.nativeArray.length == 0 );
};

/**
 * @access public
 */
ArrayList.prototype.remove = function()
{
	var arg = arguments[0];
	
	if ( typeof( arg ) == "number" )
		return this._remove1( arg );
	else 
		return this._remove2( arg );
};

/**
 * @access public
 */
ArrayList.prototype.set = function( index, element )
{
	var oldElem = this.nativeArray[index];
	this.nativeArray[index] = element;
	
	return oldElem;
};

/**
 * @access public
 */
ArrayList.prototype.size = function()
{
	return this.nativeArray.length;
};

/**
 * @access public
 */
ArrayList.prototype.toArray = function()
{
	var res = new Array();
	var len = this.nativeArray.length;
	
	for ( var i = 0; i < len; i++ )
		res[i] = this.nativeArray[i];

	return res;
};


// private methods

/**
 * @access private
 */
ArrayList.prototype._add1 = function( obj )
{
	this.nativeArray[this.nativeArray.length] = obj;
	return true;
};

/**
 * @access private
 */
ArrayList.prototype._add2 = function( index, obj )
{
	var len = this.nativeArray.length;
	
	for ( var i = len - 1; i >= index; i-- )
		this.nativeArray[i+1] = this.nativeArray[i];

	this.nativeArray[index] = obj;
};

/**
 * @access private
 */
ArrayList.prototype._addAll1 = function( col )
{
	var len = col.size();
	
	for ( var i = 0; i < len; i++ )
		this._add1( col.get( i ) );
};

/**
 * @access private
 */
ArrayList.prototype._addAll2 = function( index, col )
{
	var len = col.size();
	
	for ( var i = 0; i < len; i++ )
		this._add2( index + i, col.get( i ) );
};

/**
 * @access private
 */
ArrayList.prototype._remove1 = function( index )
{
	var res = this.nativeArray[index];
	var len = this.nativeArray.length;
	
	for ( var i = index; i < ( len - 1 ); i++ )
		this.nativeArray[i] = this.nativeArray[i + 1];
	
	if ( ( len - 1 ) != index )
		delete this.nativeArray[len - 1];
		
	this.nativeArray.length = len - 1;
	return res;
};

/**
 * @access private
 */
ArrayList.prototype._remove2 = function( obj )
{
	var index = this.indexOf( obj );
	
	if ( index == -1 )
		return false;
	
	this.remove( index );
	return true;
};
