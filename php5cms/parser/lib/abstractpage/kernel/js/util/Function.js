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
 * @access public
 */
Function.prototype.globalize = function( name )
{
	if ( name == null )
	{
		Function.setToGlobalScope( null, this );
	}
	else
	{
		var raw  = this.toString();
		var name = raw.substring( raw.indexOf( "function" ) + 9, raw.indexOf( "(" ) );
		var body = raw.substring( raw.indexOf( "(" ), raw.length );
	
		Function.setToGlobalScope( name, body );
	}
};

/**
 * Ensures that a function fulfills an interface.
 * Since with ECMA 262 (3rd edition) interfaces are not supported yet, this
 * function will simulate the functionality. The arguments for the functions
 * are all classes that the current class will implement. The function checks
 * whether the current class fulfills the interface of the given classes or not.
 * @exception TypeError If the current object is not a class or the interface
 * is not a Function object with a prototype.
 * @exception InterfaceDefinitionError If an interface is not fulfilled or the 
 * interface has invalid members.
 *
 * @access public
 */
Function.prototype.fulfills = function()
{
	for ( var i = 0; i < arguments.length; ++i )
	{
		var I = arguments[i];
		
		if ( typeof I != "function" || !I.prototype )
			throw new TypeError( I.toString() + " is not an Interface" );
		
		if ( !this.prototype )
			throw new TypeError( "Current instance is not a Function definition" );
		
		for ( var f in I.prototype )
		{
			if ( typeof I.prototype[f] != "function" )
				throw new InterfaceDefinitionError( f.toString() + " is not a method in Interface " + I.toString() );
				
			if ( typeof this.prototype[f] != "function" && typeof this[f] != "function" )
			{
				if ( typeof this.prototype[f] == "undefined" && typeof this[f] == "undefined" )
					throw new InterfaceDefinitionError( f.toString() + " is not defined" );
				else
					throw new InterfaceDefinitionError( f.toString() + " is not a function" );
			}
		}
	}
};


/**
 * @access public
 * @static
 */
Function.setToGlobalScope = function( name, fn )
{
	if ( name == null  )
		eval( fn );
	else
		eval( name + " = function" + fn );
};
