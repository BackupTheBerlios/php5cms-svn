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
 * VMLPath Class
 * Defines a path for a shape.
 *
 * @package image_vml_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
VMLPath = function( id )
{
	this.VMLElement = VMLElement;
	this.VMLElement();
	
	var ele  = VMLCanvas.defaultNamespace + ":path";
	this.elm = document.createElement( ele );
	this.elm.id = id || "vmlelement" + ( VMLElement.idcount++ );
};


VMLPath.prototype = new VMLElement();
VMLPath.prototype.constructor = VMLPath;
VMLPath.superclass = VMLElement.prototype;

/**
 * Determines whether arrowheads will be displayed.
 *
 * @access public
 */
VMLPath.prototype.setArrowOK = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.arrowok = val;
};

/**
 * Specifies how a curve will connect to a connection point.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLPath.prototype.setConnectAngles = function( val )
{
	if ( val != null )
		this.elm.connectangles = val; // o:connectangles
};
*/

/**
 * Defines the location of connection points on a path.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLPath.prototype.setConnectLocs = function( val )
{
	if ( val != null )
		this.elm.connectlogs = val; // o:connectlocs
};
*/

/**
 * Defines the type of connection point used for attaching shapes to other shapes.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLPath.prototype.setConnectType = function( val )
{
if ( val != null && ( val == "none" || val == "rect" || val == "segments" || val == "custom" ) )
		this.elm.connecttype = val; // o:connecttype
};
*/

/**
 * Note: script syntax not explicitly mentioned in reference
 * Determines whether an extrusion will be displayed.
 *
 * @access public
 */
/*
VMLPath.prototype.setExtrusionOK = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.extrusionok = val; // o:extrusionok
};
*/

/**
 * Determines whether a fill will be displayed.
 *
 * @access public
 */
VMLPath.prototype.setFillOK = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.fillok = val;
};

/**
 * Determines whether a gradient shape will be displayed.
 *
 * @access public
 */
VMLPath.prototype.setGradientShapeOK = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.gradientshapeok = val;
};

/**
 * Defines a stretch point on the path.
 *
 * @access public
 */
VMLPath.prototype.setLimo = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.limo = val;
};

/**
 * Determines whether a shadow will be displayed.
 *
 * @access public
 */
VMLPath.prototype.setShadowOK = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.shadowok = val;
};

/**
 * Determines whether a stroke will be displayed.
 *
 * @access public
 */
VMLPath.prototype.setStrokeOK = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.strokeok = val;
};

/**
 * Defines one or more textboxes inside a shape.
 *
 * @access public
 */
VMLPath.prototype.setTextBoxRect = function( val )
{
	if ( val != null )
		this.elm.textboxrect = val;
};

/**
 * Determines whether a textpath will be displayed.
 *
 * @access public
 */
VMLPath.prototype.setTextPathOK = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.textpathok = val;
};

/**
 * Defines the commands that make up a path.
 *
 * @access public
 */
VMLPath.prototype.setPath = function( val )
{
	if ( val != null )
		this.elm.v = val;
};
