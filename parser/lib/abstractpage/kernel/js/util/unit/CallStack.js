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
|Authors: Joerg Schaible <joehni@mail.berlios.de>                      |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * CallStack Class
 *
 * The object is extremly system dependent, since its functionality is not
 * within the range of ECMA 262, 3rd edition. It is supported by the
 * JScript engine and was supported in Netscape Enterprise Server 2.x, but
 * not in the newer version 4.x.
 *
 * @package util_unit
 */
 
/**
 * Constructor
 *
 * The object collects the current call stack up to the JavaScript engine.
 * Most engines will not support call stack information with a recursion.
 * Therefore the collection is stopped when the stack has two identical
 * functions in direct sequence.
 *
 * @param  Number  depth  Maximum recorded stack depth (defaults to 10).
 * @access public
 */
CallStack = function( depth )
{
	this.Base = Base;
	this.Base();
	
	/**
	 * The array with the stack. 
	 * @type Array<String>
	 */
	this.mStack = new Array();

	// set stack depth to default
	if( depth == null )
		depth = 10;

	var fn = this.getCaller( CallStack );
	if( fn === undefined )
	{
		this.mStack.push( "[CallStack information not supported]" );
	}
	else
	{
		while( fn != null && depth > 0 )
		{
			s = new String( fn );
			--depth;
	
			// Extract function name and argument list
			var r = /function (\w+)([^\{\}]*\))/;
			r.exec( s );
			var f = new String( RegExp.$1 );
			var args = new String( RegExp.$2 );
			this.mStack.push( f + args );
	
			// Retrieve caller function
			if( fn == this.getCaller( fn ))
			{
				this.mStack.push( "[JavaScript recursion]" );
				break;
			}
			else
				fn = this.getCaller( fn );
		}
	
		if( fn == null )
		{
			this.mStack.push( "[JavaScript engine]" );
		}
	}
};


CallStack.prototype = new Base();
CallStack.prototype.constructor = CallStack;
CallStack.superclass = Base.prototype;

/** 
 * Retrieve the caller of a function.
 *
 * @param  Function fn The function to examin.
 * @return Function The caller as Function or undefined.
 * @access public
 */
CallStack.prototype.getCaller = function( fn )
{
	if( fn.caller !== undefined )
		return fn.caller;
	if( fn.arguments !== undefined && fn.arguments.caller !== undefined )
		return fn.arguments.caller;
			
	return undefined;
};

/**
 * Retrieve call stack as string.
 * The function returns the call stack as string. Each stack frame has an 
 * own line and is prepended with the call stack depth.
 *
 * @return String The call stack as string.
 * @access public
 */
CallStack.prototype.toString = function()
{
	var s = new String();
	for( var i = 1; i <= this.mStack.length; ++i )
	{
		if( s.length != 0 )
			s += "\n";
		s += i.toString() + ": " + this.mStack[i-1];
	}
	return s;
};
