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
 * VMLImageData Class
 * Defines an image for a shape.
 *
 * @package image_vml_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
VMLImageData = function( id )
{
	this.VMLElement = VMLElement;
	this.VMLElement();
	
	var ele  = VMLCanvas.defaultNamespace + ":imagedata";
	this.elm = document.createElement( ele );
	this.elm.id = id || "vmlelement" + ( VMLElement.idcount++ );
};


VMLImageData.prototype = new VMLElement();
VMLImageData.prototype.constructor = VMLImageData;
VMLImageData.superclass = VMLElement.prototype;

/**
 * Specifies an alternate reference for an image.
 *
 * @access public
 */
VMLImageData.prototype.setAltHRef = function( val )
{
	if ( val != null )
		this.elm.althref = val;
};

/**
 * Determines whether an image will display in black and white.
 *
 * @access public
 */
VMLImageData.prototype.setBiLevel = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.bilevel = val;
};

/**
 * Determines the intensity of black in an image.
 *
 * @access public
 */
VMLImageData.prototype.setBlackLevel = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.elm.blacklevel = val;
};

/**
 * Defines the color in the palatte that will be treated as transparent.
 *
 * @access public
 */
VMLImageData.prototype.setChromaKey = function( val )
{
	if ( val != null && VMLElement._isColor( val ) )
		this.elm.chromakey = val;
};

/**
 * Defines the percentage of picture removal from the bottom side.
 *
 * @access public
 */
VMLImageData.prototype.setCropBottom = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.elm.cropbottom = val;
};

/**
 * Defines the percentage of picture removal from the left side.
 *
 * @access public
 */
VMLImageData.prototype.setCropLeft = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.elm.cropleft = val;
};

/**
 * Defines the percentage of picture removal from the right side.
 *
 * @access public
 */
VMLImageData.prototype.setCropRight = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.elm.cropright = val;
};

/**
 * Defines the percentage of picture removal from the top side.
 *
 * @access public
 */
VMLImageData.prototype.setCropTop = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.elm.croptop = val;
};

/**
 * Determines whether a mouse click will be detected.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLImageData.prototype.setDetectMouseClick = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.detectmouseclick = val; // o:detectmouseclick
};
*/

/**
 * Defines the color for embossed color effects.
 *
 * @access public
 */
VMLImageData.prototype.setEmbossColor = function( val )
{
	if ( val != null && Util.is_percent( val ) )
		this.elm.embosscolor = val; // o:embosscolor
};

/**
 * Defines the intensity of all colors in an image.
 *
 * @access public
 */
VMLImageData.prototype.setGain = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.elm.gain = val;
};

/**
 * Defines the amount of contrast for an image.
 *
 * @access public
 */
VMLImageData.prototype.setGamma = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.elm.gamma = val;
};

/**
 * Determines whether a picture will display in grayscale mode.
 *
 * @access public
 */
VMLImageData.prototype.setGrayScale = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.grayscale = val;
};

/**
 * Defines a URL for an image.
 *
 * @access public
 */
VMLImageData.prototype.setHRef = function( val )
{
	if ( val != null )
		this.elm.href = val; // o:href
};

/**
 * Defines a pointer to a movie image.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLImageData.prototype.setMovie = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.elm.movie = val; // o:movie
};
*/

/**
 * Stores the OLE ID of an image.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLImageData.prototype.setOLEID = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.elm.oleid = val; // o:oleid
};
*/

/**
 * Defines a source for the image.
 *
 * @access public
 */
VMLImageData.prototype.setSource = function( val )
{
	if ( val != null )
		this.elm.src = val
};

/**
 * Defines the title of an image.
 *
 * @access public
 */
VMLImageData.prototype.setTitle = function( val )
{
	if ( val != null )
		this.elm.title = val; // o:title
};
