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
 * VMLSkew Object
 * Defines a skew for a shape.
 *
 * @package image_vml_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
VMLSkew = function( id )
{
	this.VMLElement = VMLElement;
	this.VMLElement();
	
	var ele  = VMLCanvas.defaultNamespace + ":skew";
	this.elm = document.createElement( ele );
	this.elm.id = id || "vmlelement" + ( VMLElement.idcount++ );
};


VMLSkew.prototype = new VMLElement();
VMLSkew.prototype.constructor = VMLSkew;
VMLSkew.superclass = VMLElement.prototype;

/**
 * Defines the way a skew is displayed.
 *
 * @access public
 */
VMLSkew.prototype.setExt = function( val )
{
	if ( val != null && ( val == "edit" || val == "view" || val == "backwardcompatible" ) )
		this.elm.ext = val; // v:ext
};

/**
 * Defines a perspective transform for a skew.
 *
 * @access public
 */
VMLSkew.prototype.setMatrix = function( val )
{
	if ( val != null )
		this.elm.matrix = val;
};

/**
 * Defines the offset of a skew.
 *
 * @access public
 */
VMLSkew.prototype.setOffset = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.offset = val;
};

/**
 * Determines whether the skew will be displayed.
 *
 * @access public
 */
VMLSkew.prototype.setOn = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.on = val;
};

/**
 * Determines the origin of a skew.
 *
 * @access public
 */
VMLSkew.prototype.setOrigin = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.origin = val;
};
