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
 * @package peer_js
 */
 
/**
 * RemoteDictionary Class
 * provides easy access to simple key-value-pairs
 */
 
/**
 * Constructor
 *
 * @access public
 */
RemoteDictionary = function()
{
	this.Dictionary = Dictionary;
	this.Dictionary();

	this.buffer = new HTTPBuffer();
	
	this.loaded         = false;
	this.actualFile     = null;
	this.filesNotLoaded = new Array();
	
	// event
	this.onload = new Function;
};


RemoteDictionary.prototype = new Dictionary();
RemoteDictionary.prototype.constructor = RemoteDictionary;
RemoteDictionary.superclass = Dictionary.prototype;

/**
 * @access public
 */
RemoteDictionary.prototype.load = function( file )
{
	this.loaded = false;
	this.actualFile = file;
	
	// Download behaviour has no error callback, i guess ...
	// So I better put the filename to this list and remove it later.
	this.filesNotLoaded[this.filesNotLoaded.length] = file;
	
	var me = this;
	
	// callback stuff
	this.buffer.onload = function( e )
	{
		var i, pair;
		var raw = this.getHTML();

		raw = raw.tokenize();
		
		for ( var i in raw )
		{
			// pair
			if ( raw[i].indexOf( "=" ) != -1 )
			{
				pair = raw[i].split( "=" );
				me.add( pair[0].trim(), pair[1].trim().removeQuotes() );
			}
			
			// skip empty line
			if ( raw[i].isEmpty() )
				continue;
				
			// skip comment
			if ( ( raw[i].charAt( 0 ) == "#" ) || ( raw[i].charAt( 0 ) == ";" ) )
				continue;
		}
		
		// success
		Util.removeFromArray( me.filesNotLoaded, me.actualFile );
		me.loaded = true;
		
		// fire event
		me.onload();
	}

	this.buffer.getURL( file );
};

/**
 * @access public
 */
RemoteDictionary.prototype.hasLoaded = function()
{
	return this.loaded;
};
