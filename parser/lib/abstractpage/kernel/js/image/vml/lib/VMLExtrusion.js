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
 * VMLExtrusion Class
 * Defines an extrusion for a shape.
 *
 * @package image_vml_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
VMLExtrusion = function( id )
{
	this.VMLElement = VMLElement;
	this.VMLElement();
	
	var ele  = VMLCanvas.defaultNamespace + ":extrusion";
	this.elm = document.createElement( ele );
	this.elm.id = id || "vmlelement" + ( VMLElement.idcount++ );
};


VMLExtrusion.prototype = new VMLElement();
VMLExtrusion.prototype.constructor = VMLExtrusion;
VMLExtrusion.superclass = VMLElement.prototype;

/**
 * Determines whether the center of rotation will be the geometric center of the extrusion.
 *
 * @access public
 */
VMLExtrusion.prototype.setAutoRotationCenter = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.autorotationcenter = val;
};

/**
 * Defines the amount of backward extrusion.
 *
 * @access public
 */
VMLExtrusion.prototype.setBackDepth = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.elm.backdepth = val;
};

/**
 * Specifies the amount of brightness of a scene.
 *
 * @access public
 */
VMLExtrusion.prototype.setBrightness = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.elm.brightness = val;
};

/**
 * Defines the color of the extrusion faces.
 *
 * @access public
 */
VMLExtrusion.prototype.setColor = function( val )
{
	if ( val != null && VMLElement._isColor( val ) )
		this.elm.color = val;
};

/**
 * Determines the mode of extrusion color.
 *
 * @access public
 */
VMLExtrusion.prototype.setColorMode = function( val )
{
	if ( val != null && ( val == "auto" || val == "custom" ) )
		this.elm.colormode = val;
};

/**
 * Defines the amount of diffusion of reflected light from an extruded shape.
 *
 * @access public
 */
VMLExtrusion.prototype.setDiffusity = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.elm.diffusity = val;
};

/**
 * Defines the apparent bevel of the extrusion edges.
 *
 * @access public
 */
VMLExtrusion.prototype.setEdge = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.elm.edge = val;
};

/**
 * Defines the default extrusion behavior for graphical editors.
 *
 * @access public
 */
VMLExtrusion.prototype.setExt = function( val )
{
	if ( val != null )
		this.elm.ext = val; // v:ext
};

/**
 * Defines the number of facets used to describe curved surfaces of an extrusion.
 *
 * @access public
 */
VMLExtrusion.prototype.setFacet = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.elm.facet = val;
};

/**
 * Defines the amount of forward extrusion.
 *
 * @access public
 */
VMLExtrusion.prototype.setForeDepth = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.elm.foredepth = val;
};

/**
 * Determines whether the front face of the extrusion will respond to changes in the lighting.
 *
 * @access public
 */
VMLExtrusion.prototype.setLightFace = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.lightface = val;
};

/**
 * Determines whether the primary light source will be harsh.
 *
 * @access public
 */
VMLExtrusion.prototype.setLightHarsh = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.lightharsh = val;
};

/**
 * Determines whether the secondary light source will be harsh.
 *
 * @access public
 */
VMLExtrusion.prototype.setLightHarsh2 = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.lightharsh2 = val;
};

/**
 * Defines the intensity of the primary light source for the scene.
 *
 * @access public
 */
VMLExtrusion.prototype.setLightLevel = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.elm.lightlevel = val;
};

/**
 * Defines the intensity of the secondary light source for the scene.
 *
 * @access public
 */
VMLExtrusion.prototype.setLightLevel2 = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.elm.lightlevel2 = val;
};

/**
 * Specifies the position of the primary light in a scene.
 *
 * @access public
 */
VMLExtrusion.prototype.setLightPosition = function( val )
{
	if ( val != null && VMLElement._isVector3D( val ) )
		this.elm.lightposition = val;
};

/**
 * Specifies the position of the secondary light in a scene.
 *
 * @access public
 */
VMLExtrusion.prototype.setLightPosition2 = function( val )
{
	if ( val != null && VMLElement._isVector3D( val ) )
		this.elm.lightposition2 = val;
};

/**
 * Determines whether the rotation of the extruded object is specified by the RotationAngle attribute.
 *
 * @access public
 */
VMLExtrusion.prototype.setLockRotationCenter = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.lockrotationcenter = val;
};

/**
 * Determines whether the surface of the extruded shape will resemble metal.
 *
 * @access public
 */
VMLExtrusion.prototype.setMetal = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.metal = val;
};

/**
 * Determines whether an extrusion will be displayed.
 *
 * @access public
 */
VMLExtrusion.prototype.setOn = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.on = val;
};

/**
 * Specifies the vector around which a shape will be rotated.
 *
 * @access public
 */
VMLExtrusion.prototype.setOrientation = function( val )
{
	if ( val != null && VMLElement._isVector3D( val ) )
		this.elm.orientation = val;
};

/**
 * Defines the angle that an extrusion rotates around the orientation.
 *
 * @access public
 */
VMLExtrusion.prototype.setOrientationAngle = function( val )
{
	if ( val != null && VMLElement._isAngle( val ) )
		this.elm.orientationangle = val;
};

/**
 * Specifies the plane that is at right angles to the extrusion.
 *
 * @access public
 */
VMLExtrusion.prototype.setPlane = function( val )
{
	if ( val != null && ( val == "xy" || val == "zx" || val == "yz" ) )
		this.elm.plane = val;
};

/**
 * Defines the rendering mode of the extrusion.
 *
 * @access public
 */
VMLExtrusion.prototype.setRender = function( val )
{
	if ( val != null && ( val == "solid" || val == "wireframe" || val == "boundingcube" ) )
		this.elm.render = val;
};

/**
 * Specifies the rotation of the object about the x- and y-axes.
 *
 * @access public
 */
VMLExtrusion.prototype.setRotationAngle = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.rotationangle = val;
};

/**
 * Specifies the center of rotation for a shape.
 *
 * @access public
 */
VMLExtrusion.prototype.setRotationCenter = function( val )
{
	if ( val != null && VMLElement._isVector3D( val ) )
		this.elm.rotationcenter = val;
};

/**
 * Defines the concentration of reflected light of an extrusion surface.
 *
 * @access public
 */
VMLExtrusion.prototype.setShininess = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.elm.shininess = val;
};

/**
 * Defines the amount of skew of an extrusion.
 *
 * @access public
 */
VMLExtrusion.prototype.setSkewAmt = function( val )
{
	if ( val != null && Util.is_percent( val ) )
		this.elm.skewamt = val;
};

/**
 * Defines the angle of skew of an extrusion.
 *
 * @access public
 */
VMLExtrusion.prototype.setSkewAngle = function( val )
{
	if ( val != null && VMLElement._isAngle( val ) )
		this.elm.skewangle = val;
};

/**
 * Defines the specularity of an extruded shape.
 *
 * @access public
 */
VMLExtrusion.prototype.setSpecularity = function( val )
{
	if ( val != null && VMLElement._isNumber( val ) )
		this.elm.specularity = val;
};

/**
 * Defines the way that the shape is extruded.
 *
 * @access public
 */
VMLExtrusion.prototype.setExtrusionType = function( val )
{
	if ( val != null && ( val == "parallel" || val == "perspective" ) )
		this.elm.type = val;
};

/**
 * Defines the viewpoint of the observer.
 *
 * @access public
 */
VMLExtrusion.prototype.setViewPoint = function( val )
{
	if ( val != null && VMLElement._isVector3D( val ) )
		this.elm.viewpoint = val;
};

/**
 * Defines the origin of the viewpoint within the bounding box of the shape.
 *
 * @access public
 */
VMLExtrusion.prototype.setViewPointOrigin = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.viewpointorigin = val;
};
