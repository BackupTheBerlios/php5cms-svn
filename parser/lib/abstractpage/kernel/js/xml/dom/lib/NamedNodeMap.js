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
NamedNodeMap = function()
{
	this.length = 0;
};


/**
 * @access public
 */
NamedNodeMap.prototype.getLength = function()
{
	return this.length;
};

/**
 * @access public
 */
NamedNodeMap.prototype.getNamedItem = function( name )
{
	return ( this[ name ] || null );
};

/**
 * @access public
 */
NamedNodeMap.prototype.item = function( index )
{
	var item;

	item = ( index < 0 ) ? this[ this.length + index ] : this[ index ];
	return ( item || null );
};

/**
 * @access public
 */
NamedNodeMap.prototype.removeNamedItem = function( name )
{
	var removed = this[ name ];

	if ( !removed )
		return null;

	delete this[ name ];
	for ( var i = 0; i < this.length - 1; i++ )
	{
		if ( !this[i] )
		{
			this[i] = this[ i + 1 ];
			delete this[ i + 1 ];
		}
	}
	
	this.length--;
	return removed;
};

/**
 * @access public
 */
NamedNodeMap.prototype.setNamedItem = function( node )
{
	var nodeName = node.getNodeName();
	var item = this.getNamedItem( nodeName );
	
	this[ nodeName ] = node;
  
	if ( item == null )
		this[ this.length++ ] = node;
  
	return item;
};
