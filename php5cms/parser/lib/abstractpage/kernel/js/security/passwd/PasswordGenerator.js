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
 * @package security_passwd
 */
 
/**
 * Constructor
 *
 * @access public
 */
PasswordGenerator = function()
{
	this.Base = Base;
	this.Base();
};


PasswordGenerator.prototype = new Base();
PasswordGenerator.prototype.constructor = PasswordGenerator;
PasswordGenerator.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
PasswordGenerator.getPassword = function( length, extraChars, firstNumber, firstLower, firstUpper, firstOther, latterNumber, latterLower, latterUpper, latterOther )
{
	var rc = "";

	if ( length > 0 )
		rc += PasswordGenerator._getRandomChar( firstNumber, firstLower, firstUpper, firstOther, extraChars );

	for ( var idx = 1; idx < length; ++idx )
		rc += PasswordGenerator._getRandomChar( latterNumber, latterLower, latterUpper, latterOther, extraChars );

	return rc;
};


// private methods

/**
 * @access private
 * @static
 */
PasswordGenerator._getRandomNum = function( lbound, ubound )
{
	return ( Math.floor( Math.random() * ( ubound - lbound ) ) + lbound );
};

/**
 * @access private
 * @static
 */
PasswordGenerator._getRandomChar = function( number, lower, upper, other, extra )
{
	var numberChars = "0123456789";
	var lowerChars  = "abcdefghijklmnopqrstuvwxyz";
	var upperChars  = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	var otherChars  = "`~!@#$%^&*()-_=+[{]}\\|;:'\",<.>/? ";
	var charSet     = extra;

	if ( number == true )
		charSet += numberChars;

	if ( lower == true )
		charSet += lowerChars;

	if ( upper == true )
		charSet += upperChars;

	if ( other == true )
		charSet += otherChars;

	return charSet.charAt( PasswordGenerator._getRandomNum( 0, charSet.length ) );
};
