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
 * Console Debugging Utility.
 *
 * @package util
 */

/**
 * Constructor
 *
 * @access public
 */
Console = function()
{
	this.Base = Base;
	this.Base();
};


Console.prototype = new Base();
Console.prototype.constructor = Console;
Console.superclass = Base.prototype;

/**
 * @access public
 */
Console.prototype.enabled = true;


/**
 * @access public
 */
Console.prototype.open = function()
{
	if ( !Console.enabled )
		return;
		
	this.consolewin = window.open( '', 'ConsoleWindow', 'resizable=1,scrollbars=1' );
	this.consolewin.document.open( 'text/plain' ) ;
};

/**
 * @access public
 */
Console.prototype.write = function( msg )
{
	if ( !this.enabled )
		return;
		
	if ( !this.consolewin || this.consolewin.closed )
		this.open();
		
	this.consolewin.document.writeln( msg );
};

/**
 * @access public
 */
Console.prototype.close = function()
{
	if ( this.consolewin )
		this.consolewin.close();
};

/**
 * @access public
 */
Console.prototype.enable = function()
{
	this.enabled = true;
};

/**
 * @access public
 */
Console.prototype.disable = function()
{
	this.enabled = false;
};

/**
 * @access public
 */
Console.prototype.clear = function()
{
	this.consolewin.document.open( 'text/plain' );
};

/**
 * @access public
 */
Console.prototype.dumpProperties = function( obj, hidemethods )
{
	this.write( '\nObject Properties\n-----------------' )
		
	var s = [];
	for ( var i in obj )
	{
		var l = s.length;
			
		if ( typeof obj[i] == 'function' )
		{
			if ( !hidemethods )
				s[l] = i + ' = [method]';
			else
				continue;
		}
		else if ( typeof obj[i] == 'object' )
		{
			s[l] = i + ' = ' + obj[i];
		}
		else
		{
			s[l] = i + ' (' + ( typeof obj[i] ) + ')' + ' = ' + obj[i];
		}
	}
		
	s.sort();
	this.write( s.join( '\n' ) );
};
