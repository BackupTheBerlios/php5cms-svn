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
 * VMLFrame Class
 * Defines a frame for an external shape.
 *
 * @package image_vml_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
VMLFrame = function( x, y, w, h, id )
{
	this.VMLElement = VMLElement;
	this.VMLElement();
	
	var ele  = VMLCanvas.defaultNamespace + ":vmlframe";
	this.elm = document.createElement( ele );
	this.elm.id = id || "vmlelement" + ( VMLElement.idcount++ );

	this.style  = this.elm.style;
	
	if ( x == null && y == null )
	{
		this.style.position = "relative";
	}
	else
	{
		this.setXY( x , y );
		this.style.position = "absolute";
	}
	
	this.setWH( w || 100, h || 100 );
	this.setVisibility( "visible" );
	this.setClip( true )
};


VMLFrame.prototype = new VMLElement();
VMLFrame.prototype.constructor = VMLFrame;
VMLFrame.superclass = VMLElement.prototype;

/**
 * @access public
 */
VMLFrame.prototype.setXY = function( x, y )
{
	this.style.left = x;
	this.style.top  = y;
};

/**
 * @access public
 */
VMLFrame.prototype.setWH = function( w, h )
{
	this.style.width  = w;
	this.style.height = h;
};

/**
 * @access public
 */
VMLFrame.prototype.setBgColor = function( col )
{
	if ( col != null && VMLElement._isColor( col ) )
		this.style.background = col;
};

/**
 * @access public
 */
VMLFrame.prototype.setVisibility = function( val )
{
	if ( val != null && ( val == "visible" || val == "hidden" || val == "inherit" || val == "collapse" ) )
		this.style.visibility = vis;
};

/**
 * Determines whether the image will be clipped.
 *
 * @access public
 */
VMLFrame.prototype.setClip = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.clip = val;
};

/**
 * Specifies the origin of the frame.
 *
 * @access public
 */
VMLFrame.prototype.setOrigin = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.origin = val;
};

/**
 * Specifies the size of the frame.
 *
 * @access public
 */
VMLFrame.prototype.setSize = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.size = val;
};

/**
 * Specifies the source of the data that will be displayed in the frame.
 *
 * @access public
 */
VMLFrame.prototype.setSource = function( val )
{
	if ( val != null )
		this.elm.src = val
};
