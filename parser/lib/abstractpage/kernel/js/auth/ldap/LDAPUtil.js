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
 * @package auth_ldap
 */

/**
 * Constructor
 *
 * @access public
 */
LDAPUtil = function()
{
	this.Base = Base;
	this.Base();
};


LDAPUtil.prototype = new Base();
LDAPUtil.prototype.constructor = LDAPUtil;
LDAPUtil.superclass = Base.prototype;


/**
 * Parses a string in the format YYYYMMDDHHMMSS[Z] and creates
 * a JavaScript Date object for it. If the string ends with a 
 * Z the time is GMT (Zulu).
 *
 * @param  string  s
 * @return Date
 * @access public
 */
LDAPUtil.parseLDAP = function( s )
{
	if ( ( s == null ) || ( s == "" ) )
		return null;

	s = s.toUpperCase();
	var isGMT = ( s.indexOf( "Z" ) != -1 );

	// Use parseInt(..., 10) as "08" and "09" are
	// interpreted as invalid OCTAL values.
	var year   = parseInt( s.substring(  0,  4 ), 10 );
	var month  = parseInt( s.substring(  4,  6 ), 10 );
	var day    = parseInt( s.substring(  6,  8 ), 10 );
	var hour   = parseInt( s.substring(  8, 10 ), 10 );
	var minute = parseInt( s.substring( 10, 12 ), 10 );
	var second = parseInt( s.substring( 12, 14 ), 10 );
	var d;

	month = month - 1;

	if ( isGMT )
		d = new Date( Date.UTC( year, month, day, hour, minute, second ) );
	else
		d = new Date( year, month, day, hour, minute, second );

	return d;
};
