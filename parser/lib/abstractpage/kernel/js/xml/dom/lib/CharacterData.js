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
 * @package xml_dom_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
CharacterData = function( data )
{
	this.data = data;
  
	// inherited from Node
	this.nodeValue = data;
};


CharacterData.prototype = new Node();
CharacterData.prototype.constructor = CharacterData;
CharacterData.superclass = Node.prototype;

/**
 * @access public
 */
CharacterData.prototype.appendData = function( data )
{
	this.setData( this.getData() + data );
};

/**
 * @access public
 */
CharacterData.prototype.deleteData = function( offset, count )
{
	var begin = this.getData().substring( 0, offset );
	var end   = this.getData().substring( offset + count );

	this.setData( begin + end );
};

/**
 * @access public
 */
CharacterData.prototype.getData = function()
{
	return this.data;
};

/**
 * @access public
 */
CharacterData.prototype.getLength = function()
{
	return ( this.data )? this.data.length : 0;
};

/**
 * @access public
 */
CharacterData.prototype.insetData = function( offset, data )
{
	var begin = this.getData().substring( 0, offset );
	var end   = this.getData().substring( offset, this.getLength );

	this.setData( begin + data + end );
};

/**
 * @access public
 */
CharacterData.prototype.replaceData = function( offset, count, data )
{
	this.deleteData( offset, count );
	this.insertData( offset, data  );
};

/**
 * @access public
 */
CharacterData.prototype.setData = function( data )
{
	this.setNodeValue( data );
};

/**
 * @access public
 */
CharacterData.prototype.setNodeValue = function( value )
{
	this.data = value;
	this.nodeValue = value;
};

/**
 * @access public
 */
CharacterData.prototype.substringData = function( offset, count )
{
	return this.getData().substring( offset, offset + count );
};
