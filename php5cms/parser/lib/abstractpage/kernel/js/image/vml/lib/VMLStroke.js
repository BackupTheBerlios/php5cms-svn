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
 * VMLStroke Class
 * Defines a stroke for a shape.
 *
 * @package image_vml_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
VMLStroke = function( id )
{
	this.VMLElement = VMLElement;
	this.VMLElement();
	
	var ele  = VMLCanvas.defaultNamespace + ":stroke";
	this.elm = document.createElement( ele );
	this.elm.id = id || "vmlelement" + ( VMLElement.idcount++ );
};


VMLStroke.prototype = new VMLElement();
VMLStroke.prototype.constructor = VMLStroke;
VMLStroke.superclass = VMLElement.prototype;

/**
 * Specifies an alternate reference for a stroke.
 *
 * @access public
 */
VMLStroke.prototype.setAltHRef = function( val )
{
	if ( val != null )
		this.elm.althref = val;
};

/**
 * Defines the color of a stroke.
 *
 * @access public
 */
VMLStroke.prototype.setColor = function( val )
{
	if ( val != null && VMLElement._isColor( val ) )
		this.elm.color = val;
};

/**
 * Defines a second color for a stroke.
 *
 * @access public
 */
VMLStroke.prototype.setColor2 = function( val )
{
	if ( val != null && VMLElement._isColor( val ) )
		this.elm.color2 = val;
};

/**
 * Specifies the dot and dash pattern for a stroke.
 *
 * @access public
 */
VMLStroke.prototype.setDashStyle = function( val )
{
	if ( val != null )
		this.elm.dashstyle = val;
};

/**
 * Defines an arrowhead style for the end of a stroke.
 *
 * @access public
 */
VMLStroke.prototype.setEndArrow = function( val )
{
	if ( val != null && this._isArrow( val ) )
		this.elm.endarrow = val;
};

/**
 * Defines an arrowhead length for the end of a stroke.
 *
 * @access public
 */
VMLStroke.prototype.setEndArrowLength = function( val )
{
	if ( val != null && this._isArrowLength( val ) )
		this.elm.endarrowlength = val;
};

/**
 * Defines an arrowhead width for the end of a stroke.
 *
 * @access public
 */
VMLStroke.prototype.setEndArrowWidth = function( val )
{
	if ( val != null && this._isArrowWidth( val ) )
		this.elm.endarrowwidth = val;
};

/**
 * Defines the cap style for the end of a stroke.
 *
 * @access public
 */
VMLStroke.prototype.setEndCap = function( val )
{
	if ( val != null && ( val == "flat" || val == "square" || val == "round" ) )
		this.elm.endcap = val;
};

/**
 * Defines the type of fill used for the background of a stroke.
 *
 * @access public
 */
VMLStroke.prototype.setFillType = function( val )
{
	if ( val != null && ( val == "solid" || val == "tile" || val == "pattern" || val == "frame" ) )
		this.elm.type = val;
};

/**
 * Defines the URL to the original image for the stroke.
 *
 * @access public
 */
VMLStroke.prototype.setHRef = function( val )
{
	if ( val != null )
		this.elm.href = val; // o:href
};

/**
 * Determines the alignment of a stroke image.
 *
 * @access public
 */
VMLStroke.prototype.setImageAlignShape = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.imagealignshape = val;
};

/**
 * Defines how the stroke image aspect ratio will be preserved.
 *
 * @access public
 */
VMLStroke.prototype.setImageAspect = function( val )
{
	if ( val != null && ( val == "ignore" || val == "atleast" || val == "atmost" ) )
		this.elm.type = imageaspect;
};

/**
 * Defines the size of the image for the stroke.
 *
 * @access public
 */
VMLStroke.prototype.setImageSize = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.imagesize = val;
};

/**
 * Defines the join style of a polyline.
 *
 * @access public
 */
VMLStroke.prototype.setJoinStyle = function( val )
{
	if ( val != null && ( val == "round" || val == "bevel" || val == "miter" ) )
		this.elm.joinstyle = val;
};

/**
 * Defines the line style of a stroke.
 *
 * @access public
 */
VMLStroke.prototype.setLineStyle = function( val )
{
	if ( val != null && ( val == "single" || val == "thinthin" || val == "thinthick" || val == "thickthin" || val == "thickbetweenthin" ) )
		this.elm.linestyle = val;
};

/**
 * Defines the smoothness of a miter joint.
 *
 * @access public
 */
VMLStroke.prototype.setMiterLimit = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.elm.miterlimit = val;
};

/**
 * Determines whether the stroke will be displayed.
 *
 * @access public
 */
VMLStroke.prototype.setOn = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.on = val;
};

/**
 * Defines the amount of transparency of a stroke.
 *
 * @access public
 */
VMLStroke.prototype.setOpacity = function( val )
{
	if ( val != null && VMLElement._isFraction( val ) )
		this.elm.opacity = val;
};

/**
 * Defines the source image to load for a stroke fill.
 *
 * @access public
 */
VMLStroke.prototype.setSource = function( val )
{
	if ( val != null )
		this.elm.src = val
};

/**
 * Defines the arrowhead for the start of a stroke.
 *
 * @access public
 */
VMLStroke.prototype.setStartArrow = function( val )
{
	if ( val != null && this._isArrow( val ) )
		this.elm.startarrow = val;
};

/**
 * Defines the arrowhead length for the start of a stroke.
 *
 * @access public
 */
VMLStroke.prototype.setStartArrowLength = function( val )
{
	if ( val != null && this._isArrowLength( val ) )
		this.elm.startarrowlength = val;
};

/**
 * Defines the arrowhead width for the start of a stroke.
 *
 * @access public
 */
VMLStroke.prototype.setStartArrowWidth = function( val )
{
	if ( val != null && this._isArrowWidth( val ) )
		this.elm.startarrowwidth = val;
};

/**
 * Defines the title of a stroke.
 *
 * @access public
 */
VMLStroke.prototype.setTitle = function( val )
{
	if ( val != null )
		this.elm.title = val;
};

/**
 * Defines the thickness of a stroke.
 *
 * @access public
 */
VMLStroke.prototype.setWeight = function( val )
{
	if ( val != null )
		this.elm.weight = val;
};


// private methods

/**
 * @access private
 */
VMLStroke.prototype._isArrow = function( val )
{
	if ( val == "none" || val == "block" || val == "classic" || val == "diamond" || val == "oval" || val == "open" )
		return true;
	else
		return false;
};

/**
 * @access private
 */
VMLStroke.prototype._isArrowLength = function( val )
{
	if ( val == "short" || val == "medium" || val == "long" )
		return true;
	else
		return false;
};

/**
 * @access private
 */
VMLStroke.prototype._isArrowWidth = function( val )
{
	if ( val == "narrow" || val == "medium" || val == "wide" )
		return true;
	else
		return false;
};
