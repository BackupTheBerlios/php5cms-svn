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
 * VMLShadow Class
 * Defines a shadow for a shape.
 *
 * @package image_vml_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
VMLShadow = function( id )
{
	this.VMLElement = VMLElement;
	this.VMLElement();
	
	var ele  = VMLCanvas.defaultNamespace + ":shadow";
	this.elm = document.createElement( ele );
	this.elm.id = id || "vmlelement" + ( VMLElement.idcount++ );
};


VMLShadow.prototype = new VMLElement();
VMLShadow.prototype.constructor = VMLShadow;
VMLShadow.superclass = VMLElement.prototype;

/**
 * Defines the color of a shadow.
 *
 * @access public
 */
VMLShadow.prototype.setColor = function( val )
{
	if ( val != null && VMLElement._isColor( val ) )
		this.elm.color = val;
};

/**
 * Defines the second color of a shadow.
 *
 * @access public
 */
VMLShadow.prototype.setColor2 = function( val )
{
	if ( val != null && VMLElement._isColor( val ) )
		this.elm.color2 = val;
};

/**
 * Defines the perspective transform of a shadow.
 *
 * @access public
 */
VMLShadow.prototype.setMatrix = function( val )
{
	if ( val != null )
		this.elm.matrix = val;
};

/**
 * Determines whether the shadow is transparent.
 *
 * @access public
 */
VMLShadow.prototype.setObscured = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.obscured = val;
};

/**
 * Defines how far the shadow extends past the shape.
 *
 * @access public
 */
VMLShadow.prototype.setOffset = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.offset = val;
};

/**
 * Defines a second offset.
 *
 * @access public
 */
VMLShadow.prototype.setOffset2 = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.offset2 = val;
};

/**
 * Determines whether a shadow is displayed.
 *
 * @access public
 */
VMLShadow.prototype.setOn = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.on = val;
};

/**
 * Determines the transparency of a shadow.
 *
 * @access public
 */
VMLShadow.prototype.setOpacity = function( val )
{
	if ( val != null && VMLElement._isFraction( val ) )
		this.elm.opacity = val;
};

/**
 * Defines the center of the shadow.
 *
 * @access public
 */
VMLShadow.prototype.setOrigin = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.origin = val;
};

/**
 * Specifies the type of shadow.
 *
 * @access public
 */
VMLShadow.prototype.setShadowType = function( val )
{
	if ( val != null && ( val == "solid" || val == "double" || val == "perspective" || val == "shaperelative" || val == "drawingrelative" || val == "emboss" ) )
		this.elm.type = val;
};
