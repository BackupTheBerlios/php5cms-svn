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
 * VMLShapeImage Class
 * Predefined image shape.
 *
 * @package image_vml_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
VMLShapeImage = function( x, y, w, h, id )
{
	this.VMLShapeBase = VMLShapeBase;
	this.VMLShapeBase();
	
	this.init( x, y, w, h, "image", id );
};


VMLShapeImage.prototype = new VMLShapeBase();
VMLShapeImage.prototype.constructor = VMLShapeImage;
VMLShapeImage.superclass = VMLShapeBase.prototype;

/**
 * @access public
 */
VMLShapeImage.prototype.setImage = function( path )
{
	if ( path != null )
		this.elm.src = path;
};

/**
 * Defines a reference to the ID of a ShapeType element.
 *
 * @access public
 */
VMLShapeImage.prototype.setShapeType = function( val )
{
	if ( val != null )
		this.elm.type = val;
};
