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
 * @package util_agent
 */
 
/**
 * Constructor
 *
 * @access public
 */
MSBrowserData = function()
{
	this.Base = Base;
	this.Base();
	
	this.userAgent = window.navigator.userAgent.toString();
	var rPattern = /(MSIE)\s(\d+)\.(\d+)((b|p)([^(s|;)]+))?;?(.*(98|95|NT|3.1|32|Mac|X11))?\s*([^\)]*)/;
	
	if ( this.userAgent.match( rPattern ) )
	{
		this.browser  = "MSIE";
		this.majorVer = parseInt( RegExp.$2 ) || 0;
		this.minorVer = RegExp.$3.toString()  || "0";
		this.betaVer  = RegExp.$6.toString()  || "0";
		this.platform = RegExp.$8 || "Other";
		this.platVer  = RegExp.$9 || "0";
	}
	else if ( this.userAgent.match( /Mozilla[/].*(95[/]NT|95|NT|98|3.1).*Opera.*(\d+)\.(\d+)/ ) )
	{
		// "Mozilla/4.0 (Windows NT 5.0;US) Opera 3.60  [en]";
		this.browser  = "Opera";
		this.majorVer = parseInt( RegExp.$2 ) || parseInt( RegExp.$2 ) || 0;
		this.minorVer = RegExp.$3.toString()  || RegExp.$3.toString()  || "0";
		this.platform = RegExp.$1 || "Other";
	}
	else if ( this.userAgent.match( /Mozilla[/](\d*)\.?(\d*)(.*(98|95|NT|32|16|68K|PPC|X11))?/ ) )
	{
		// "Mozilla/4.5 [en] (WinNT; I)"
		this.browser  = "Nav";
		this.majorVer = parseInt( RegExp.$1 ) || 0;
		this.minorVer = RegExp.$2.toString()  || "0";
		this.platform = RegExp.$4 || "Other";
	}
	else
	{
		this.browser = "Other";
	}
	
	this.getsNavBar      = ( "MSIE" == this.browser && 4 <= this.majorVer && "Mac"  != this.platform && "X11" != this.platform );
	this.doesActiveX     = ( "MSIE" == this.browser && 3 <= this.majorVer && ( "95" == this.platform || "98"  == this.platform || "NT" == this.platform ) );
	this.fullVer         = parseFloat( this.majorVer + "." + this.minorVer );
	this.doesPersistence = ( "MSIE" == this.browser && 5 <= this.majorVer && "Mac" != this.platform && "X11" != this.platform );
};


MSBrowserData.prototype = new Base();
MSBrowserData.prototype.constructor = MSBrowserData;
MSBrowserData.superclass = Base.prototype;
