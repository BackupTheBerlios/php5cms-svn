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
 * VMLLocks Class
 * Defines a lock for a shape.
 * The Locks element is a Microsoft Office Extension to VML.
 *
 * @package image_vml_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
VMLLocks = function( id )
{
	this.VMLElement = VMLElement;
	this.VMLElement();
	
	var ele  = VMLCanvas.defaultNamespace + ":locks";
	this.elm = document.createElement( ele );
	this.elm.id = id || "vmlelement" + ( VMLElement.idcount++ );
};


VMLLocks.prototype = new VMLElement();
VMLLocks.prototype.constructor = VMLLocks;
VMLLocks.superclass = VMLElement.prototype;

/**
 * Determines whether the handles of a shape can be edited.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLLocks.prototype.setAdjustHandles = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.adjusthandles = val;
};
*/

/**
 * Determines whether the aspect ratio of a shape can be changed by an editor.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLLocks.prototype.setAspectRation = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.aspectratio = val;
};
*/

/**
 * Determines whether cropping will be allowed in an editor.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLLocks.prototype.setCropping = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.cropping = val;
};
*/

/**
 * Defines the behavior of locking actions for a graphical editor.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLLocks.prototype.setExt = function( val )
{
	if ( val != null )
		this.elm.ext = val; // v:ext
};
*/

/**
 * Determines whether shapes can be grouped in an editor.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLLocks.prototype.setGrouping = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.grouping = val;
};
*/

/**
 * Determines whether the position of a shape is locked in an editor.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLLocks.prototype.setPosition = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.position = val;
};
*/

/**
 * Determines whether rotation of shapes will be allowed in an editor.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLLocks.prototype.setRotation = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.rotation = val;
};
*/

/**
 * Determines whether the shape is selectable in an editor.
 *
 * @access public
 */
/*
VMLLocks.prototype.setSelection = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.selection = val;
};
*/

/**
 * Determines whether the AutoShapes type can be changed by an editor.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLLocks.prototype.setShapeType = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.shapetype = val;
};
*/

/**
 * Determines whether the text attached to a shape can be edited.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLLocks.prototype.setText = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.text = val;
};
*/

/**
 * Determines whether the vertices of a path can be changed by an editor.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLLocks.prototype.setVertices = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.vertices = val;
};
*/
