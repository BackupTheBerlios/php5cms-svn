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
 * VMLShapePolyLine Class
 * Predefined polyline shape.
 *
 * @package image_vml_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
VMLShapePolyLine = function( x, y, w, h, id )
{
	this.VMLShapeBase = VMLShapeBase;
	this.VMLShapeBase();
	
	this.init( x, y, w, h, "polyline", id );
};


VMLShapePolyLine.prototype = new VMLShapeBase();
VMLShapePolyLine.prototype.constructor = VMLShapePolyLine;
VMLShapePolyLine.superclass = VMLShapeBase.prototype;

/**
 * Defines a set of points that make up a polyline.
 *
 * @access public
 */
VMLShapePolyLine.prototype.setPoints = function( points )
{
	if ( points != null && VMLElement._isPoints( points ) )
		this.elm.points = points;
};

/**
 * Defines a reference to the ID of a ShapeType element.
 *
 * @access public
 */
VMLShapePolyLine.prototype.setShapeType = function( val )
{
	if ( val != null )
		this.elm.type = val;
};
