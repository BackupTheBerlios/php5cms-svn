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
 * @package io_fso
 */
 
/**
 * Constructor
 *
 * @access public
 */
FSOTextStream = function( textstream )
{
	this.Base = Base;
	this.Base();
	
	this.textstream = textstream;
};


FSOTextStream.prototype = new Base();
FSOTextStream.prototype.constructor = FSOTextStream;
FSOTextStream.superclass = Base.prototype;

/**
 * @access public
 */
FSOTextStream.prototype.close = function()
{
	this.textstream.Close();
};

/**
 * @access public
 */
FSOTextStream.prototype.read = function( count )
{
	return this.textstream.Read( count || 4096 );
};

/**
 * @access public
 */
FSOTextStream.prototype.readAll = function()
{
	return this.textstream.ReadAll();
};

/**
 * @access public
 */
FSOTextStream.prototype.readLine = function()
{
	return this.textstream.ReadLine();
};

/**
 * @access public
 */
FSOTextStream.prototype.skip = function( count )
{
	this.textstream.Skip( count || 0 );
};

/**
 * @access public
 */
FSOTextStream.prototype.skipLine = function()
{
	this.textstream.SkipLine();
};

/**
 * @access public
 */
FSOTextStream.prototype.write = function( text )
{
	this.textstream.Write( text || "" );
};

/**
 * @access public
 */
FSOTextStream.prototype.writeBlankLines = function( num )
{
	this.textstream.WriteBlankLines( num || 0 );
};

/**
 * @access public
 */
FSOTextStream.prototype.writeLine = function( text )
{
	this.textstream.WriteLine( text || "" );
};

/**
 * @access public
 */
FSOTextStream.prototype.isAtEndOfLine = function()
{
	return this.textstream.AtEndOfLine;
};

/**
 * @access public
 */
FSOTextStream.prototype.isAtEndOfStream = function()
{
	return this.textstream.AtEndOfStream;
};

/**
 * @access public
 */
FSOTextStream.prototype.getLine = function()
{
	return this.textstream.Line();
};

/**
 * @access public
 */
FSOTextStream.prototype.getColumn = function()
{
	return this.textstream.Column();
};
