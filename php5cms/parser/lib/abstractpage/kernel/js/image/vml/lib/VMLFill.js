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
 * VMLFill Class
 * Defines a fill for a shape.
 *
 * @package image_vml_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
VMLFill = function( id )
{
	this.VMLElement = VMLElement;
	this.VMLElement();
	
	var ele  = VMLCanvas.defaultNamespace + ":fill";
	this.elm = document.createElement( ele );
	this.elm.id = id || "vmlelement" + ( VMLElement.idcount++ );
};


VMLFill.prototype = new VMLElement();
VMLFill.prototype.constructor = VMLFill;
VMLFill.superclass = VMLElement.prototype;

/**
 * Determines whether an image will align with a shape.
 *
 * @access public
 */
VMLFill.prototype.setAlignShape = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.alignshape = val;
};

/**
 * Specifies an alternate reference for an image.
 *
 * @access public
 */
VMLFill.prototype.setAltHRef = function( val )
{
	if ( val != null )
		this.elm.althref = val;
};

/**
 * Defines the angle of a gradient fill.
 *
 * @access public
 */
VMLFill.prototype.setAngle = function( val )
{
	if ( val != null && VMLElement._isAngle( val ) )
		this.elm.angle = val;
};

/**
 * Specifies how the fill image aspect will be preserved.
 *
 * @access public
 */
VMLFill.prototype.setAspect = function( val )
{
	if ( val != null )
		this.elm.aspect = val;
};

/**
 * Defines the color of a fill.
 *
 * @access public
 */
VMLFill.prototype.setColor = function( val )
{
	if ( val != null && VMLElement._isColor( val ) )
		this.elm.color = val;
};

/**
 * Defines the second color for a fill.
 *
 * @access public
 */
VMLFill.prototype.setColor2 = function( val )
{
	if ( val != null && VMLElement._isColor( val ) )
		this.elm.color2 = val;
};

/**
 * Defines multiple colors for a gradient fill.
 *
 * @access public
 */
VMLFill.prototype.setColors = function( val )
{
	if ( val != null && VMLElement._isGradientColorArray( val ) )
		this.elm.colors = val;
};

/**
 * Determines whether a mouse click is detected.
 *
 * @access public
 */
VMLFill.prototype.setDetectMouseClick = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.detectmouseclick = val;
};

/**
 * Defines the center of a linear gradient fill.
 *
 * @access public
 */
VMLFill.prototype.setFocus = function( val )
{
	if ( val != null && VMLElement._isFraction( val ) )
		this.elm.fraction = val;
};

/**
 * Defines the center of a radial gradient fill.
 *
 * @access public
 */
VMLFill.prototype.setFocusPosition = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.focusposition = val;
};

/**
 * Defines the focus size for a radial fill.
 *
 * @access public
 */
VMLFill.prototype.setFocusSize = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.focussize = val;
};

/**
 * Defines a URL to the original image file.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLFill.prototype.setHRef = function( val )
{
	if ( val != null )
		this.elm.href = val;
};
*/

/**
 * Defines the method used to generate a gradient fill.
 *
 * @access public
 */
VMLFill.prototype.setMethod = function( val )
{
	if ( val != null && VMLElement._isSigma( val ) )
		this.elm.method = val;
};

/**
 * Determines whether the fill wil be displayed.
 *
 * @access public
 */
VMLFill.prototype.setOn = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.on = val;
};

/**
 * Defines the transparency of a fill.
 *
 * @access public
 */
VMLFill.prototype.setOpacity = function( val )
{
	if ( val != null && VMLElement._isFraction( val ) )
		this.elm.opacity = val;
};

/**
 * Defines the transparency of the second fill color.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLFill.prototype.setOpacity2 = function( val )
{
	if ( val != null && VMLElement._isFraction( val ) )
		this.elm.opacity2 = val;
};
*/

/**
 * Defines the center of an image.
 *
 * @access public
 */
VMLFill.prototype.setOrigin = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.origin = val;
};

/**
 * Defines the position of an image.
 *
 * @access public
 */
VMLFill.prototype.setPosition = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.position = val;
};

/**
 * Defines the size of an image.
 *
 * @access public
 */
VMLFill.prototype.setSize = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.size = val;
};

/**
 * Defines the image to load for a fill.
 *
 * @access public
 */
VMLFill.prototype.setSource = function( val )
{
	if ( val != null )
		this.elm.src = val
};

/**
 * Defines the title of a fill.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLFill.prototype.setTitle = function( val )
{
	if ( val != null )
		this.elm.title = val;
};
*/

/**
 * Defines the type of fill.
 *
 * @access public
 */
VMLFill.prototype.setFillType = function( val )
{
	if ( val != null && ( val == "solid" || val == "gradient" || val == "gradientradial" || val == "tile" || val == "pattern" || val == "frame" ) )
		this.elm.type = val;
};
