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
QueryUtil = function( url )
{
	this.Base = Base;
	this.Base();
};


QueryUtil.prototype = new Base();
QueryUtil.prototype.constructor = QueryUtil;
QueryUtil.superclass = Base.prototype;

/**
 * Create a passedArgs[] array from the data sent in a query string.
 *
 * @access public
 * @static
 */
QueryUtil.getArgs = function()
{
	passedArgs = new Array();
	
	search = self.location.href;
	search = search.split( '?' );
	
	if ( search[1] )
	{
		argList = search[1];
		argList = argList.split( '&' );
		
		for ( var i = 0; i < argList.length; i++ )
		{
			newArg = argList[i];
			newArg = argList[i].split( '=' );
			
			passedArgs[i] = unescape( newArg[1] );
		}
		
		return passedArgs;
	}
	
	return false;
};

/**
 * Create a dictionary from the data sent in a query string.
 *
 * @access public
 * @static
 */
QueryUtil.getArgsDict = function()
{
	passedArgs = new Dictionary();
	
	search = self.location.href;
	search = search.split( '?' );
	
	if ( search[1] )
	{
		argList = search[1];
		argList = argList.split( '&' );
		
		for ( var i = 0; i < argList.length; i++ )
		{
			newArg = argList[i];
			newArg = argList[i].split( '=' );
			
			passedArgs.add( newArg[0], unescape( newArg[1] ) );
		}
		
		return passedArgs;
	}
	
	return false;
};

/**
 * Create a query string to send to a another page.
 *
 * @access public
 * @static
 */
QueryUtil.passArgs = function( url, target )
{
	argsArray = '';
	
	for ( var i = 2; i < arguments.length; i++ )
	{
		if ( i != ( arguments.length - 1 ) )
			eval( 'argsArray+="arg' + ( i - 1 ) + '=' + escape( arguments[i] ) + ',"' );
		else
			eval( 'argsArray+="arg' + ( i - 1 ) + '=' + escape( arguments[i] ) + '"'  );
	}
	
	passTarget = target.split( ':' );
	
	switch( passTarget[0] )
	{
		case 'top':
			eval( 'top.location.href="' + url + '?' + argsArray + '"' );
			break;
			
		case 'frame':
			eval( 'top.' + passTarget[1] + '.location.href="' + url + '?' + argsArray + '"' );
			break;
			
		case 'blank':
			eval( 'window.open("' + url + '?' + argsArray + '")' );
			break;
	}
};

/**
 * Assemble a string with the url arguments.
 *
 * @access public
 * @static
 */
QueryUtil.returnArgs = function( url )
{
	argsArray = '';
	
	for ( var i = 1; i < arguments.length; i++ )
	{
		if ( i != ( returnArgs.arguments.length - 1 ) )
			eval( 'argsArray+="arg' + ( i - 1 ) + '=' + escape( arguments[i] ) + ',"' );
		else
			eval( 'argsArray+="arg' + ( i - 1 ) + '=' + escape( arguments[i] ) + '"'  );
	}
	
	eval( 'returnArgs="' + url + '?' + argsArray + '"' );
	return returnArgs;
};
