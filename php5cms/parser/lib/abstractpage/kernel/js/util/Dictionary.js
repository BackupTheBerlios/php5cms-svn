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
Dictionary = function( array )
{
	this.Base = Base;
	this.Base();
	
	this.hash = 
	{
		array: new Array,
		
		add: function( ele )
		{
			this.array[this.array.length] = ele;
		},
		removeAt: function( pos )
		{
			if ( pos > this.getSize() - 1 )
				return;
		
			for ( var i = pos; i < this.getSize(); i++ )
				this.array[i] = this.array[i + 1];
		
			this.array.length--;
		},
		get: function( pos )
		{
			return this.array[pos];
		},
		empty: function()
		{
			this.array.length = 0;
		},
		isEmpty: function()
		{
			return ( this.array.length == 0 )? 1 : 0;
		},
		getSize: function()
		{
			return this.array.length;
		}
	};
	
	// initial setup
	if ( array != null )
		this.hash.array = array;
};


Dictionary.prototype = new Base();
Dictionary.prototype.constructor = Dictionary;
Dictionary.superclass = Base.prototype;

/**
 * @access public
 */
Dictionary.prototype.set = function( key, ele )
{
	if ( !this.contains( key ) )
		return false;
		
	var tmp;
	
	for ( var i = 0; i < this.hash.getSize(); i++ )
	{
		tmp = this.hash.get( i );
			
		if ( key == tmp.k )
		{
			tmp.e = ele;
			return true;
		}
	}
	
	return false;
};

/**
 * @access public
 */
Dictionary.prototype.get = function( key )
{
	if ( !this.contains( key ) )
		return false;
		
	var tmp;
		
	for ( var i = 0; i < this.hash.getSize(); i++ )
	{
		tmp = this.hash.get( i );
			
		if ( key == tmp.k )
			return tmp.e;
	}
	
	return false;
};

/**
 * @access public
 */
Dictionary.prototype.remove = function( key )
{
	if ( !this.contains( key ) )
		return false;
		
	var tmp;
		
	for ( var i = 0; i < this.hash.getSize(); i++ )
	{
		tmp = this.hash.get( i );
			
		if ( key == tmp.k )
		{
			this.hash.removeAt( i );
			return true;
		}	
	}
	
	return false;
};

/**
 * @access public
 */
Dictionary.prototype.getKeys = function()
{
	if ( this.isEmpty() )
		return;
		
	var tmp;
	var keys = new Array();
		
	for ( var i = 0; i < this.hash.getSize(); i++ )
	{
		tmp = this.hash.get( i );
		keys[keys.length] = tmp.k;
	}
		
	return keys;
};

/**
 * @access public
 */
Dictionary.prototype.getElements = function()
{
	if ( this.isEmpty() )
		return;
		
	var tmp;
	var elements = new Array();
		
	for ( var i = 0; i < this.hash.getSize(); i++ )
	{
		tmp = this.hash.get( i );
		elements[elements.length] = tmp.e;
	}
		
	return elements;
};

/**
 * @access public
 */
Dictionary.prototype.contains = function( key )
{
	var tmp;
		
	for ( var i = 0; i < this.hash.getSize(); i++ )
	{
		tmp = this.hash.get( i );
			
		if ( key == tmp.k )
			return true;
	}
		
	return false;
};

/**
 * @access public
 */
Dictionary.prototype.add = function( key, ele )
{
	if ( this.contains( key ) )
		return false;
	
	this.hash.add(
	{
		k:key,
		e:ele
	} );
};

/**
 * @access public
 */
Dictionary.prototype.empty = function()
{
	this.hash.empty();
};

/**
 * @access public
 */
Dictionary.prototype.isEmpty = function()
{
	return this.hash.isEmpty()? 1 : 0;
};

/**
 * @access public
 */
Dictionary.prototype.getSize = function()
{
	return this.hash.getSize();
};

/**
 * @access public
 */
Dictionary.prototype.getHash = function()
{
	return this.hash.array;
};

/**
 * Import simple object.
 *
 * @access public
 */
Dictionary.prototype.addObject = function( o )
{
	if ( o != null && ( typeof o != "object" ) )
		return false

	var prop, propVal;

	for ( prop in o )
	{
		propVal = o[prop];
		
		if ( this.contains( prop ) )
			this.set( prop, propVal );
		else
			this.add( prop, propVal );
	}
	
	return true;
};

/**
 * Import other dictionary.
 *
 * @access public
 */
Dictionary.prototype.addDictionary = function( set )
{
	if ( set != null && set.contains )
	{
		var keys = set.getKeys();
		
		for ( var i in keys )
		{
			if ( this.contains( keys[i] ) )
				this.set( keys[i], set.get( keys[i] ) );
			else
				this.add( keys[i], set.get( keys[i] ) );
		}
	
		return true;
	}
	
	return false;
};

/**
 * Parse flat textfile like:
 * name1="value1"
 * name2="value2"
 *
 * @access public
 */
Dictionary.prototype.addFlat = function( str )
{
	var i, pair, key, val;
	var raw = str || "";

	raw = raw.tokenize();
		
	for ( var i in raw )
	{
		// pair
		if ( raw[i].indexOf( "=" ) != -1 )
		{
			pair = raw[i].split( "=" );
			key  = pair[0].trim();
			val  = pair[1].trim().removeQuotes();
			
			if ( this.contains( key ) )
				this.set( key, val );
			else
				this.add( key, val );
		}
			
		// skip empty line
		if ( raw[i].isEmpty() )
			continue;
				
		// skip comment
		if ( ( raw[i].charAt( 0 ) == "#" ) || ( raw[i].charAt( 0 ) == ";" ) )
			continue;
	}
};

/**
 * @access public
 */
Dictionary.prototype.toXML = function( tagnames )
{
	// TODO:
	// - type sniffing (like xmlrpc), support for complex objects
	// - what about xml header?
	
	var rootname, itemname, keyname, valname;

	if ( ( tagnames != null )          && 
		 ( tagnames instanceof Array ) && 
		 ( tagnames.length == 4 ) )
	{
		rootname = tagnames[0];
		itemname = tagnames[1];
		keyname  = tagnames[2];
		valname  = tagnames[3];
	}
	else
	{
		rootname = "root";
		itemname = "item";
		keyname  = "key";
		valname  = "value";
	}
	
	var keys = this.getKeys();	
	var str  = "<" + rootname + ">\n";
	
	for ( var i in keys )
	{
		str += "\t<"   + itemname + ">\n";
		str += "\t\t<" + keyname  + ">" + keys[i] + "</" + keyname + ">\n";
		str += "\t\t<" + valname  + ">" + this.get( keys[i] ) + "</" + valname + ">\n";
		str += "\t</" + itemname + ">\n";
	}	
	
	str += "</" + rootname + ">";
	return str;
};

/**
 * @access public
 */
Dictionary.prototype.dump = function( simple )
{
	var str  = "";
	var keys = this.getKeys();	

	for ( var i in keys )
	{
		if ( simple )
			str += keys[i] + ": " + this.get( keys[i] ) + "\n";
		else
			str += "key: " + keys[i] + " - value: " + this.get( keys[i] ) + "\n";
	}	
		
	return str;
};
