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
 * @package peer_http
 */
 
/**
 * Constructor
 *
 * @access public
 */
QueryBuilder = function()
{
	this.Dictionary = Dictionary;
	this.Dictionary();
	
	this.query = "";
};


QueryBuilder.prototype = new Dictionary();
QueryBuilder.prototype.constructor = QueryBuilder;
QueryBuilder.superclass = Dictionary.prototype;

/**
 * @access public
 */
QueryBuilder.prototype.build = function( without )
{
	if ( this.isEmpty() )
		return false;
		
	var str  = ( without == true )? "" : "?";
	var vars = this.getKeys();	
	
	for ( var i = 0; i < vars.length; i++ )
	{
		str += vars[i] + "=" + this.get( vars[i] );
	
		if ( i != vars.length - 1 )
			str += "&";
	}
	
	this.query = str;
	return str;
};
