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
 * VMLCallout Class
 * Defines a callout for a shape.
 * The Callout element is a Microsoft Office Extension to VML.
 *
 * @package image_vml_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
VMLCallout = function( id )
{
	this.VMLElement = VMLElement;
	this.VMLElement();
	
	var ele  = VMLCanvas.officeNamespace + ":callout";
	this.elm = document.createElement( ele );
	this.elm.id = id || "vmlelement" + ( VMLElement.idcount++ );
};


VMLCallout.prototype = new VMLElement();
VMLCallout.prototype.constructor = VMLCallout;
VMLCallout.superclass = VMLElement.prototype;

/**
 * Determines whether an accent bar will be used with the callout.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLCallout.prototype.setAccentBar = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.accentbar = val;
};
*/

/**
 * Defines the angle that the callout makes with respect to the bounding box of the shape.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLCallout.prototype.setAngle = function( val )
{
	if ( val != null && VMLElement._isFixedAngle( val ) )
		this.elm.angle = val;
};
*/

/**
 * Defines the drop distance of a callout.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLCallout.prototype.setDistance = function( val )
{
	if ( val != null )
		this.elm.distance = val;
};
*/

/**
 * Determines where the drop of a callout will be placed.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLCallout.prototype.setDrop = function( val )
{
	if ( val != null && VMLElement._isVAlign( val ) )
		this.elm.drop = val;
};
*/

/**
 * Determines whether the callout will have an automatic drop.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLCallout.prototype.setDropAuto = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.dropauto = val;
};
*/

/**
 * Defines how a callout is processed.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLCallout.prototype.setExt = function( val )
{
	if ( val != null )
		this.elm.ext = val; // v:ext
};
*/

/**
 * Defines the distance fo the callout from its bounding rectangle.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLCallout.prototype.setGap = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.elm.gap = val;
};
*/

/**
 * Defines the length of the callout.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLCallout.prototype.setLength = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.elm.length = val;
};
*/

/**
 * Determines whether the Length attribute will be used for the callout.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLCallout.prototype.setLengthSpecified = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.lengthspecified = val;
};
*/

/**
 * Determines whether the callout will flip along the x-axis.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLCallout.prototype.setMinusX = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.minusx = val;
};
*/

/**
 * Determines whether the callout will flip along the y-axis.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLCallout.prototype.setMinusY = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.minusy = val;
};
*/

/**
 * Determines whether a shape is a callout.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLCallout.prototype.setOn = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.on = val;
};
*/

/**
 * Determines whether a callout will have a text border.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLCallout.prototype.setTextBorder = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.textborder = val;
};
*/

/**
 * Defines the type of callout.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLCallout.prototype.setCalloutType = function( val )
{
	if ( val != null && ( val == "rectangle" || val == "roundrectangle" || val == "oval" || val == "cloud" ) )
		this.elm.type = val;
};
*/
