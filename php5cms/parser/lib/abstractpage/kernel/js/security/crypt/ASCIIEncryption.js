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
 * @package security_crypt
 */
 
/**
 * Constructor
 *
 * @access public
 */
ASCIIEncryption = function()
{
	this.Base = Base;
	this.Base();
};


ASCIIEncryption.prototype = new Base();
ASCIIEncryption.prototype.constructor = ASCIIEncryption;
ASCIIEncryption.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
ASCIIEncryption.encrypt = function( str )
{
	var rnd;
	var output   = new String;
	var temp     = new Array();
	var temp2    = new Array();
	var textsize = str.length;

	for ( i = 0; i < textsize; i++ )
	{
		rnd = Math.round( Math.random() * 122 ) + 68;
		temp[i]  = str.charCodeAt( i ) + rnd;
		temp2[i] = rnd;
	}

	for ( i = 0; i < textsize; i++ )
		output += String.fromCharCode( temp[i], temp2[i] );

	return output;
};

/**
 * @access public
 * @static
 */
ASCIIEncryption.decrypt = function( str )
{
	var output   = new String;
	var temp     = new Array();
	var temp2    = new Array();
	var textsize = str.length;

	for ( i = 0; i < textsize; i++ )
	{
		temp[i]  = str.charCodeAt( i );
		temp2[i] = str.charCodeAt( i + 1 );
	}

	for ( i = 0; i < textsize; i = i + 2 )
		output += String.fromCharCode( temp[i] - temp2[i] );

	return output;
};
