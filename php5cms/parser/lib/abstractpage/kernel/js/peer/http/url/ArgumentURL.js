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
 * @package peer_http_url
 */
 
/**
 * Constructor
 *
 * @access public
 */
ArgumentURL = function()
{
	this.Base = Base;
	this.Base();

	this.arguments = new Array();

	var tmp;
	var sInfo;
		
	// initiation
	var separator = "&";
	var equalsign = "=";
	
	var str = window.location.search.replace( /%20/g, " " );
	var index = str.indexOf( "?" );
	var infoArray = new Array();
	
	if ( index != -1 )
	{
		sInfo = str.substring( index + 1, str.length );
		infoArray = sInfo.split( separator );
	}

	for ( var i = 0; i < infoArray.length; i++ )
	{
		tmp = infoArray[i].split( equalsign );
		
		if ( tmp[0] != "" )
		{
			var t = tmp[0];
			this.arguments[tmp[0]] = new Object();
			this.arguments[tmp[0]].value = tmp[1];
			this.arguments[tmp[0]].name  = tmp[0];
		}
	}
	
	return this;
};


ArgumentURL.prototype = new Base();
ArgumentURL.prototype.constructor = ArgumentURL;
ArgumentURL.superclass = Base.prototype;

/**
 * @access public
 */
ArgumentURL.prototype.get = function()
{
	var s = "";
	var once = true;
	
	for ( var i in this.arguments )
	{
		if ( once )
		{
			s += "?";
			once = false;
		}
		
		s += this.arguments[i].name;
		s += equalsign;
		s += this.arguments[i].value;
		s += separator;
	}
	
	return s.replace(/ /g, "%20");
};

/**
 * @access public
 */
ArgumentURL.prototype.getArgument = function()
{	
	if ( typeof( this.arguments[name].name ) != "string" )
		return null;
	else
		return this.arguments[name].value;
};

/**
 * @access public
 */
ArgumentURL.prototype.setArgument = function( name, value )
{
		this.arguments[name] = new Object()
		this.arguments[name].name  = name;
		this.arguments[name].value = value;
	}
};

/**
 * @access public
 */
ArgumentURL.prototype.removeArgument = function( name )
{
	this.arguments[name] = null;
};
