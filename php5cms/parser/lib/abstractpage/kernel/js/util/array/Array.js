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
 * @access public
 */
/*
Array.prototype.enumCount = 0;
*/

/**
 * @access public
 */
/*
Array.prototype.hasMore = function()
{
	if ( this.enumCount == this.length )
		return false;

	return true;
};
*/

/**
 * @access public
 */
/*
Array.prototype.next = function()
{
	return this[this.enumCount++];
};
*/

/**
 * @access public
 */
/*
Array.prototype.reset = function()
{
	this.enumCount = 0;
};
*/

/**
 * @access public
 */
Array.prototype.count = function()
{
	return this.length;
};

/**
 * @access public
 */
Array.prototype.clone = function()
{
	return new Array( this );
};

/**
 * @access public
 */
Array.prototype.rand = function()
{
	var index = Math.round( Math.random() * ( this.length - 1 ) );
	return this[index];
};

/**
 * @access public
 */
Array.prototype.fill = function( val )
{
	if ( val == null )
		return;
		
	for ( var i = 0; i < this.length; i++ )
		this.setElementAt( i, val );
		
	return this;
};

/**
 * @access public
 */
Array.prototype.insertAt = function( pos, ele )
{
	if ( pos > this.getSize() - 1 )
		return;
		
	var tmp = this.slice( pos, this.getSize() );
	this[pos] = ele;
		
	for ( a = 0; a <= tmp.length - 1; a++ )
		this[a + pos + 1] = tmp[a];
};

/**
 * @access public
 */
Array.prototype.setElementAt = function( pos, ele )
{
	if ( pos > this.getSize() - 1 )
		return;
		
	this[pos] = ele;
};

/**
 * @access public
 */
Array.prototype.shift = function( ele )
{
	if ( ele != null )
		this.insertAt( 0, ele );
};

/**
 * @access public
 */
Array.prototype.unshift = function( arr )
{
	if ( arr == null || typeof( arr ) != "object" )
		return

	arr.reverse();
	
	for ( var i = 0; i < arr.length; i++ )
		this.shift( arr[i] );
};

/**
 * @access public
 */
Array.prototype.remove = function( ele )
{
	var pos = this.getIndex( ele );
		
	if ( pos != -1 )
	{
		for ( a = pos; a < this.getSize(); a++ )
			this[a] = this[a + 1];
			
		this.length--;
		this.remove( ele );
	}
};

/**
 * @access public
 */
Array.prototype.removeAt = function( pos )
{
    if ( isNaN( pos ) || pos > this.length )
		return false;
		
    for ( var i = 0, n = 0; i < this.length; i++ )
    {  
        if ( this[i] != this[pos] )
			this[n++] = this[i];
    }
	
    this.length -= 1;
};

/**
 * @access public
 */
Array.prototype.printSrc = function( name )
{
	var aL = this.length;
	var i = 0;
	var retStr = new String( "var " + name + " = new Array(" + aL + "); ");

	for ( i = 0; i < aL; i++ )
		retStr = retStr + name + "[" + i +"] = '" + this[i] + "'; ";

	return retStr;
};

/**
 * @access public
 */
Array.prototype.getIndex = function( ele )
{
	for ( var i = 0; i < this.getSize(); i++ )
	{
		if ( ele == this[i] )
			return i;
	}
		
	return -1;
};

/**
 * Sort of pop(), but does not affect the length of the array.
 *
 * @access public
 */
Array.prototype.lastItem = function()
{
	return this[ this.length - 1 ];
};

/**
 * @access public
 */
Array.prototype.swap = function( i, j )
{
	if ( ( i > this.getSize() - 1 ) && ( j > this.getSize() - 1 ) )
		return;
	
	var a   = this[i];
	this[i] = this[j];
	this[j] = a;
};

/**
 * @access public
 */
Array.prototype.sortByProperty = function( property, rev )
{
	var fn = function( a, b )
	{
		if ( a[property] < b[property] )
			return rev?  1 : -1;
		else if ( a[property] > b[property] )
			return rev? -1 : 1;
		else
			return 0;
	}
	
	this.sort( fn );
};

/**
 * @access public
 */
Array.prototype.setSize = function( size, val )
{
	var tmp = this.getSize();
	this.length = size;
		
	for ( a = tmp; a < this.getSize(); a++ )
		this.setElementAt( a, val || null );
};

/**
 * For backward compatibility.
 *
 * @access public
 */
Array.prototype.add = function( ele )
{
	this.push( ele );
};

/**
 * @access public
 */
Array.prototype.get = function( pos )
{
	return this[pos];
};

/**
 * @access public
 */
Array.prototype.empty = function()
{
	this.length = 0;
};

/**
 * @access public
 */
Array.prototype.isEmpty = function()
{
	return ( this.length == 0 )? 1 : 0;
};

/**
 * @access public
 */
Array.prototype.contains = function( ele ) 
{
	return ( this.getIndex( ele ) != -1 )? 1 : 0;
};

/**
 * @access public
 */
Array.prototype.getSize = function()
{
	return this.length;
};

/**
 * @access public
 */
Array.prototype.min = function()
{
	// TODO
};

/**
 * @access public
 */
Array.prototype.max = function()
{
	// TODO
};

/**
 * @access public
 */
Array.prototype.dump = function( linebreak )
{
	var str = "";
	
	for ( var i = 0; i < this.length; i++ )
		str += this[i] + ( linebreak || "\n" );	
	
	return str;
};


/* MS engine does not implement Array.push and Array.pop until JScript 5.6 */
if ( !Array.prototype.push )
{ 
	/**
	 * Pushes elements into Array.
	 * The function is an implementation of the Array::push method described
	 * in the ECMA standard. It adds all given parameters at the end of the
	 * array.
	 *
	 * The function is active if the ECMA implementation does not implement
	 * it (like Microsoft JScript engine up to version 5.5).
	 *
	 * @access public
	 * @return Object Number of added elements
	 */
	Array.prototype.push = function()
	{
		var i = 0;
		
		if ( this instanceof Array )
		{
			i = this.length;
			
			// Preallocation of array
			if ( arguments.length > 0 )
				this[arguments.length + this.length - 1] = null;
			
			for ( ; i < this.length; ++i )
				this[i] = arguments[i - this.length + arguments.length];
		}
		
		return i;
	};
};

if ( !Array.prototype.pop )
{
	/**
	 * Pops last element from Array.
	 * The function is an implementation of the Array::pop method described
	 * in the ECMA standard. It removes the last element of the Array and
	 * returns it.
	 *
	 * The function is active if the ECMA implementation does not implement
	 * it (like Microsoft JScript engine up to version 5.5).
	 *
	 * @return Object Last element or undefined
	 * @access public
	 */
	Array.prototype.pop = function()
	{
		var obj;
		
		if ( this instanceof Array && this.length > 0 )
		{
			var last = parseInt( this.length ) - 1;
			obj = this[last];
			this.length = last;
		}
		
		return obj;
	};
};


/**
 * @access public
 * @static
 */
Array.list = function()
{
	var val;
	
	if ( ( arguments[0] ) != null && arguments[0].slice )
	{
		for ( var i = 1; i < arguments.length; i++ )
		{
			val = arguments[0][i -1];
			val = ( typeof val == "string" )? '"' + val + '"' : val;
			
			eval( arguments[i] + " = " + val );
		}
		 
		return true;
	}
	
	return false;
};

/**
 * @access public
 * @static
 */
Array.implode = function( arr, separator )
{
	var str = "";
	
	if ( ( typeof arr != "object" ) && !arr.length )
		return str;
	
	var sep = separator || ": ";
		
	for ( var i = 0; i < arr.length; i++ )
		str += arr[i] + ( ( i < arr.length - 1 )? sep : "" );
		
	return str;
};

/**
 * @access public
 * @static
 */
Array.maxSizeOfLevel = function( array, level ) 
{
	if ( !array ) 
		return 0;
		
	if ( array.length == 0 ) 
		return 0;
	
	if ( level == 1 ) 
		return array.length;
		
	var ret = 0;
	
	for ( var i=0; i < array.length; i++ ) 
	{
		if ( array[i].length > ret ) 
			ret = array[i].length;
	}
	
	return ret;
};

/**
 * @access public
 * @static
 */
Array.unique = function( arr ) 
{
	var new_arr = new Array();
	arr.sort();
	var tmp = '';

	for ( var i = 0; i < arr.length; i++ ) 
	{
		if ( arr[i] != tmp ) 
		{
			new_arr.push( arr[i] );
			tmp = arr[i];
		}
	}

	return new_arr;
};

/**
 * @access public
 * @static
 */
Array.copy = function( arr ) 
{
	var new_arr = new Array();
	
	for ( var i = 0; i < arr.length; i++ )
		new_arr[i] = arr[i];
	
	return new_arr;
};

/**
 * @access public
 * @static
 */
Array.toCSV = function( array, separator ) 
{
	if ( typeof( separator ) != 'string' ) 
		separator = ';';
		
	var lineA;
	var ret = '';
	
	for ( var i = 0; i < array.length; i++ ) 
	{
		lineA = new Array();
		
		for ( var j = 0; j < array[i].length; j++ ) 
			lineA[j] = array[i][j];

		ret += lineA.join( separator ) + "\n";
	}

	return ret;
};

/**
 * @access public
 * @static
 */
Array.toXML = function( data_array, tagdef_array )
{
	// TODO
};
