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
 * @package util_text
 */
 
/**
 * Constructor
 *
 * @access public
 */
StringTokenizer = function( str, delim )
{
	this.Base = Base;
	this.Base();
	
	this.setString( str, delim );
};


StringTokenizer.prototype = new Base();
StringTokenizer.prototype.constructor = StringTokenizer;
StringTokenizer.superclass = Base.prototype;

/**
 * @access public
 */
StringTokenizer.prototype.setString = function( str, delim )
{
	this.string = new String( str || "" );
	this.tokens = this.string.split( delim || StringTokenizer.defaultDelimiter );

	this.tokenCount  = 0;
	this.tokenLength = this.tokens.length;
};

/**
 * @access public
 */
StringTokenizer.prototype.reset = function()
{
	this.tokenCount = 0;
};

/**
 * @access public
 */
StringTokenizer.prototype.hasMoreTokens = function()
{
	if ( this.tokenCount == this.tokenLength )
		return false;

	return true;
};

/**
 * @access public
 */
StringTokenizer.prototype.getCount = function()
{
	return this.tokenCount;
};

/**
 * @access public
 */
StringTokenizer.prototype.nextToken = function()
{
	return this.tokens[this.tokenCount++];
};

/**
 * @access public
 */
StringTokenizer.prototype.countTokens = function()
{
	return this.tokenLength;
};


/**
 * @access public
 * @static
 */
StringTokenizer.defaultDelimiter = ",";
