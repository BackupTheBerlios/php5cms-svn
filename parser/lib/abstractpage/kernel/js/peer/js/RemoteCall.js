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
 * Constructor
 *
 * @access public
 */
RemoteCall = function( scripturl, clientfunc, serverfunc, val )
{
	this.Base = Base;
	this.Base();
	
	this.setScriptPath( scripturl );
	this.setClientFunction( clientfunc );
	this.setServerFunction( serverfunc );
	this.setArgument( val );
};


RemoteCall.prototype = new Base();
RemoteCall.prototype.constructor = RemoteCall;
RemoteCall.superclass = Base.prototype;

/**
 * @access public
 */
RemoteCall.prototype.setScriptPath = function( path )
{
	if ( path != null )
	{
		this.url = path;
		return true;
	}
	
	return false;
};

/**
 * @access public
 */
RemoteCall.prototype.getScriptPath = function()
{
	return this.url;
};

/**
 * @access public
 */
RemoteCall.prototype.setClientFunction = function( fn )
{
	if ( fn != null && typeof( fn ) == "function" )
	{
		this.clientfunc = fn;
		return true;
	}
	
	return false;
};

/**
 * @access public
 */
RemoteCall.prototype.getClientFunction = function()
{
	return this.clientfunc;
};

/**
 * @access public
 */
RemoteCall.prototype.setServerFunction = function( fn )
{
	if ( fn != null && typeof( fn ) == "string" )
	{
		this.serverfunc = fn;
		return true;
	}
	
	return false;
};

/**
 * @access public
 */
RemoteCall.prototype.getServerFunction = function()
{
	return this.serverfunc;
};

/**
 * @access public
 */
RemoteCall.prototype.setArgument = function( a )
{
	this.argument = a || null;
};

/**
 * @access public
 */
RemoteCall.prototype.getArgument = function()
{
	return this.argument;
};

/**
 * @access public
 */
RemoteCall.prototype.perform = function()
{
	JSRS.Execute(
		this.getScriptPath(),
		this.getClientFunction(),
		this.getServerFunction(),
		this.getArgument(),
		false // invisible
	);
};
