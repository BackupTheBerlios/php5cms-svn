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
 * VMLShapeRoundRect Class
 * Predefined round rectangle shape.
 *
 * @package image_vml_lib
 */

/**
 * Constructor
 *
 * @access public
 */
VMLShapeRoundRect = function( x, y, w, h, id )
{
	this.VMLShapeBase = VMLShapeBase;
	this.VMLShapeBase();
	
	this.init( x, y, w, h, "roundrect", id );
};


VMLShapeRoundRect.prototype = new VMLShapeBase();
VMLShapeRoundRect.prototype.constructor = VMLShapeRoundRect;
VMLShapeRoundRect.superclass = VMLShapeBase.prototype;

/**
 * Defines the amount of roundness for a rounded rectangle.
 *
 * @access public
 */
VMLShapeRoundRect.prototype.setArcSize = function( arc )
{
	if ( arc != null && VMLElement._isFraction( arc ) )
		this.elm.arcsize = arc;
};

/**
 * Defines a reference to the ID of a ShapeType element.
 *
 * @access public
 */
VMLShapeRoundRect.prototype.setShapeType = function( val )
{
	if ( val != null )
		this.elm.type = val;
};
