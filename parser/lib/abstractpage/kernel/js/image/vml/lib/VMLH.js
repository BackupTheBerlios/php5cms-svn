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
 * VMLH Class
 * Defines a handle for a shape.
 *
 * @package image_vml_lib
 */

/**
 * Constructor
 *
 * @access public
 */
VMLH = function( id )
{
	this.VMLElement = VMLElement;
	this.VMLElement();
	
	var ele  = VMLCanvas.defaultNamespace + ":h";
	this.elm = document.createElement( ele );
	this.elm.id = id || "vmlelement" + ( VMLElement.idcount++ );
};


VMLH.prototype = new VMLElement();
VMLH.prototype.constructor = VMLH;
VMLH.superclass = VMLElement.prototype;

/**
 * Determines whether the x position of the handle is inverted.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLH.prototype.setInvX = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.invx = val;
};
*/

/**
 * Determines whether the y position of the handle is inverted.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLH.prototype.setInvY = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.invy = val;
};
*/

/**
 * Defines the mapping range of a handle.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLH.prototype.setMap = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.map = val;
};
*/

/**
 * Defines the center position for polar handles.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLH.prototype.setPolar = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.polar = val;
};
*/

/**
 * Specifies the x and y values of the handle.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLH.prototype.setPosition = function( val )
{
	if ( val != null )
		this.elm.position = val;
};
*/

/**
 * Defines the range of a polar handle.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLH.prototype.setRadiusRange = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.radiusrange = val;
};
*/

/**
 * Determines whether the handle directions are swapped.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLH.prototype.setSwitch = function( val )
{
	// Note: switch is a reserved word!
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.switch = val;
};
*/

/**
 * Defines the x range of a handle.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLH.prototype.setXRange = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.xrange = val;
};
*/

/**
 * Defines the y range of a handle.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLH.prototype.setYRange = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.yrange = val;
};
*/
