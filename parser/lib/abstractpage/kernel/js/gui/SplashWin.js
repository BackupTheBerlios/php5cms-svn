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
 * @package gui
 */
 
/**
 * Constructor
 *
 * @access public
 */
SplashWin = function()
{
	this.Base = Base;
	this.Base();
};


SplashWin.prototype = new Base();
SplashWin.prototype.constructor = SplashWin;
SplashWin.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
SplashWin.launch = function( contentType, contentString, width, height, left, top, autoCloseTime )
{
	var w   = window.screen.width;
	var h   = window.screen.height;
	var l   = ( left != null )? left : ( w - width  ) / 2;
	var t   = ( top  != null )? top  : ( h - height ) / 2;
	var uri = ( contentType.toLowerCase() == "uri" )? contentString : "";
	
	SplashWin.splashWin = window.open( uri, '_splash', 'fullscreen=1,toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=0,resizable=0' );
	SplashWin.splashWin.blur();
	window.focus();
	
	SplashWin.splashWin.resizeTo( width, height );
	SplashWin.splashWin.moveTo(l, t);

	if ( contentType.toLowerCase() == "string" )
	{
		var swd = SplashWin.splashWin.document;
		
		swd.open();
		swd.write( contentString );
		swd.close();
	}

	SplashWin.splashWin.focus();	
	SplashWin.ontopIntervalHandle = SplashWin.splashWin.setInterval( "window.opener.SplashWin.splashWin.focus();", 50 );
	
	SplashWin.splashWin.document.body.onbeforeunload = function()
	{
		// splash is being closed, no need to close it again
		window.clearInterval( SplashWin.autoCloseTimeoutHandle );
		window.onbeforeunload = null;
	}
	
	SplashWin.splashWin.document.body.onload = function()
	{
		SplashWin.splashWin.setInterval( "window.opener.SplashWin.splashWin.focus();", 50 );
	}
	
	// in case some one calls this twice
	window.clearTimeout( SplashWin.autoCloseTimeoutHandle );

	if ( autoCloseTime != null && autoCloseTime > 0 )
		SplashWin.autoCloseTimeoutHandle = window.setTimeout( "SplashWin.splashWin.close()", autoCloseTime );

	// close splash when this page is unloaded	
	window.onbeforeunload = function()
	{
		SplashWin.splashWin.close();
	}
};
