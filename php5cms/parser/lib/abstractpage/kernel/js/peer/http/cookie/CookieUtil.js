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
 * @package peer_http_cookie
 */
 
/**
 * Constructor
 *
 * @access public
 */
CookieUtil = function()
{
	this.Base = Base;
	this.Base();
};


CookieUtil.prototype = new Base();
CookieUtil.prototype.constructor = CookieUtil;
CookieUtil.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
CookieUtil.save = function( name, value, days )
{
	if ( days )
	{
		var date = new Date();
		date.setTime( date.getTime() + ( days * 24 * 60 * 60 * 1000 ) );
		var expires = "; expires=" + date.toGMTString();
	} 
	else
	{
		expires = "";
	}
		
	document.cookie = name + "=" + value + expires + "; path=/";
};

/**
 * @access public
 * @static
 */
CookieUtil.read = function( name )
{
	var nameEQ = name + "=";
	var ca = document.cookie.split( ';' );
		
	for ( var i = 0; i < ca.length; i++ )
	{
		var c = ca[i];
			
		while ( c.charAt( 0 ) == ' ' )
			c = c.substring( 1, c.length );
				
		if ( c.indexOf( nameEQ ) == 0 )
			return c.substring( nameEQ.length, c.length );
	}
		
	return null;
};

/**
 * @access public
 * @static
 */
CookieUtil.remove = function( name )
{
	CookieUtil.save( name, "", -1 );
};
