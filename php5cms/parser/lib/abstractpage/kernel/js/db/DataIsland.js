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
 * @package db
 */
 
/**
 * Constructor
 *
 * @access public
 */
DataIsland = function( id, src, fn )
{
	this.Base = Base;
	this.Base();
	
	this.id  = id  || "dataisland" + DataIsland.count++;
	this.src = src || "";
	this.fn  = ( ( fn != null ) && ( typeof fn == "function" ) )? fn  : new Function;
	
	div = document.createElement( "XML" );
	div.id  = this.id;
	div.src = this.src;
		
	DataIsland._self = this;
	div.onreadystatechange = function()
	{	
		if ( div.readyState == "complete" )
			DataIsland._self.fn( DataIsland._self.elm.XMLDocument );
	}
		
	document.getElementsByTagName( "BODY" ).item( 0 ).appendChild( div );
	this.elm = div;
};


DataIsland.prototype = new Base();
DataIsland.prototype.constructor = DataIsland;
DataIsland.superclass = Base.prototype;


/**
 * @access public
 * @static
 */
DataIsland.count = 0;

/**
 * @access private
 * @static
 */
DataIsland._self = null;
