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
 * Helper class with static flags.
 *
 * @package util_unit
 */
 
/**
 * Constructor
 *
 * @access public
 */
JsUtil = function()
{
	this.Base = Base;
	this.Base();
};


JsUtil.prototype = new Base();
JsUtil.prototype.constructor = JsUtil;
JsUtil.superclass = Base.prototype;

/**
 * Flag for a browser.
 * The member is true, if the script runs within a browser environment.
 *
 * @type   Boolean
 * @access public
 */
JsUtil.prototype.isBrowser = this.window != null;

/**
 * Flag for Microsoft JScript.
 * The member is true, if the script runs in the Microsoft JScript engine.
 *
 * @type   Boolean
 * @access public
 */
JsUtil.prototype.isJScript = this.ScriptEngine != null;

/**
 * Flag for Microsoft Windows Scripting Host.
 * The member is true, if the script runs in the Microsoft Windows Scripting Host.
 *
 * @type   Boolean
 * @access public
 */
JsUtil.prototype.isWSH = this.WScript != null;

/**
 * Flag for Netscape Enterprise Server (iPlanet) engine.
 * The member is true, if the script runs in the iPlanet as SSJS.
 *
 * @type   Boolean
 * @access public
 */
JsUtil.prototype.isNSServer = this.Packages != null && !this.importPackage;

/**
 * Flag for Rhino.
 * The member is true, if the script runs in Rhino of Mozilla.org.
 *
 * @type   Boolean
 * @access public
 */
JsUtil.prototype.isRhino = this.importPackage != null;

/**
 * Flag for a Mozilla JavaScript shell.
 * The member is true, if the script runs in a command line shell of a
 * Mozilla.org script engine (either SpiderMonkey or Rhino).
 *
 * @type   Boolean
 * @access public
 */
JsUtil.prototype.isMozillaShell = this.load != null;

/**
 * Flag for a command line shell.
 * The member is true, if the script runs in a command line shell.
 *
 * @type   Boolean
 * @access public
 */
JsUtil.prototype.isShell = JsUtil.prototype.isMozillaShell || JsUtil.prototype.isWSH;
	
/**
 * @type   Boolean
 * @access public
 */
JsUtil.prototype.hasCompatibleErrorClass = ( this.Error != null && ( !JsUtil.prototype.isJScript || ( JsUtil.prototype.isJScript && ( this.ScriptEngineMajorVersion() >= 6 ) ) ) );

/**
 * @param  String  str  The line to print.
 * @access public
 */
JsUtil.prototype.load = function( script )
{
	var ret = "true";
	
	if ( JsUtil.prototype.isMozillaShell )
	{
		load( script );
	}
	else if ( JsUtil.prototype.isWSH )
	{
		var fso  = new ActiveXObject( "Scripting.FileSystemObject" );
		var file = fso.OpenTextFile( script, 1 );
		ret = file.ReadAll();
		file.Close();
	}
	
	return ret;
};

/**
 * Prints a complete text line incl. line feed. Works for command line
 * shells WSH, Rhino and SpiderMonkey.
 *
 * @param  String str The line to print.
 * @access public
 */
JsUtil.prototype.print = function( str )
{
	if ( JsUtil.prototype.isMozillaShell )
		print( str );
	else if ( JsUtil.prototype.isBrowser )
		document.writeln( str );
	else if ( JsUtil.prototype.isWSH )
		WScript.Echo( str );
};

/**
 * Quits the JavaScript engine.
 * Stops current JavaScript engine and returns an exit code. Works for 
 * command line shells WSH, Rhino and SpiderMonkey.
 *
 * @param  Number  exit  The exit code.
 * @access public
 */
JsUtil.prototype.quit = function( exit )
{
	if ( JsUtil.prototype.isMozillaShell )
		quit( exit );
	else if ( JsUtil.prototype.isWSH )
		WScript.Quit( exit );
};
