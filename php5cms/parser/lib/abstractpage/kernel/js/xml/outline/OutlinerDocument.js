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
 * @package xml_outline
 */
 
/**
 * Constructor
 *
 * @access public
 */
OutlinerDocument = function( source )
{
	this.Base = Base;
	this.Base();
	
	this.source = source + '\0';
	this.cursor = 0;

	var uberroot = new OutlinerParser( '{document}', this ).node;

	for ( var i in uberroot )
	{
		if ( uberroot[i].constructor == OutlinerNode )
		{
			this.root = uberroot[i];
			break;
		}
	}
};


OutlinerDocument.prototype = new Base();
OutlinerDocument.prototype.constructor = OutlinerDocument;
OutlinerDocument.superclass = Base.prototype;

/**
 * @access public
 */
OutlinerDocument.prototype.getChar = function()
{
	return this.source.charAt( this.cursor++ );
};
