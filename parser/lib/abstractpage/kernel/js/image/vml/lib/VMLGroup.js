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
 * VMLGroup Class
 * Defines a group that can be used to collect shapes.
 *
 * @package image_vml_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
VMLGroup = function( x, y, w, h, id )
{
	this.VMLElement = VMLElement;
	this.VMLElement();

	var ele  = VMLCanvas.defaultNamespace + ":group";
	this.elm = document.createElement( ele );
	
	this.elm.id = id || "vmlelement" + ( VMLElement.idcount++ );
	this.style  = this.elm.style;
	
	if ( x == null && y == null )
	{
		this.setPosition( "relative" );
	}
	else
	{
		this.setXY( x , y );
		this.setPosition( "absolute" );
	}
	
	this.setWH( w || 100, h || 100 );
	this.setVisibility( "visible" );
};


VMLGroup.prototype = new VMLElement();
VMLGroup.prototype.constructor = VMLGroup;
VMLGroup.superclass = VMLElement.prototype;

/**
 * Determines whether a shape can be placed in a table.
 *
 * @access public
 */
VMLGroup.prototype.setAllowInCell = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.allowincell = val;
};

/**
 * Determines if a shape can overlap other shapes.
 *
 * @access public
 */
VMLGroup.prototype.setAllowOverlap = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.allowoverlap = val;
};

/**
 * Defines alternative text to be displayed instead of a graphic.
 *
 * @access public
 */
VMLGroup.prototype.setAlt = function( val )
{
	if ( val != null )
		this.elm.alt = val;
};

/**
 * @access public
 */
VMLGroup.prototype.setAntiAlias = function( val )
{
	if ( val != null && Util.is_bool( val ) )
		this.style.antialias = val;
};

/**
 * Bottom border color of an inline shape.
 *
 * @access public
 */
VMLGroup.prototype.setBorderBottomColor = function( val )
{
	if ( val != null && ( VMLElement._isColor( val ) || val == "null" || val == "this" ) )
		this.elm.borderbottomcolor = val;
};

/**
 * Left border color of an inline shape.
 *
 * @access public
 */
VMLGroup.prototype.setBorderLeftColor = function( val )
{
	if ( val != null && ( VMLElement._isColor( val ) || val == "null" || val == "this" ) )
		this.elm.borderleftcolor = val;
};

/**
 * Right border color of an inline shape.
 *
 * @access public
 */
VMLGroup.prototype.setBorderRightColor = function( val )
{
	if ( val != null && ( VMLElement._isColor( val ) || val == "null" || val == "this" ) )
		this.elm.borderrightcolor = val;
};

/**
 * Top border color of an inline shape.
 *
 * @access public
 */
VMLGroup.prototype.setBorderTopColor = function( val )
{
	if ( val != null && ( VMLElement._isColor( val ) || val == "null" || val == "this" ) )
		this.elm.bordertopcolor = val;
};

/**
 * Refers to a definition of a CSS style.
 *
 * @access public
 */
VMLGroup.prototype.setClass = function( val )
{
	if ( val != null )
		this.elm.classname = val;
};

/**
 * Specifies the coordinate unit origin of the rectangle that bounds a shape.
 *
 * @access public
 */
VMLGroup.prototype.setCoordOrigin = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.coordorigin = val;
};

/**
 * Specifies the horizontal and vertical units of the rectangle that bounds a shape.
 *
 * @access public
 */
VMLGroup.prototype.setCoordSize = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.coordsize = val;
};

/**
 * Sends an event message when a shape is double-clicked.
 *
 * @access public
 */
VMLGroup.prototype.setDoubleClickNotify = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.doubleclicknotify = val;
};

/**
 * Switches the orientation of a shape.
 *
 * @access public
 */
VMLGroup.prototype.setFlip = function( val )
{
	if ( val != null && ( val == "x" || val == "y" || val == "xy" || val == "yx" ) )
		this.style.flip = val;
};

/**
 * Specifies the bottom edge of the shape's containing rectangle relative to the shape anchor. 
 *
 * @access public
 */
VMLGroup.prototype.setMarginBottom = function( val )
{
	if ( val != null )
		this.elm.marginbottom = val;
};

/**
 * Specifies the left edge of the shape's containing rectangle relative to the shape anchor.
 *
 * @access public
 */
VMLGroup.prototype.setMarginLeft = function( val )
{
	if ( val != null )
		this.elm.marginleft = val;
};

/**
 * Specifies the right edge of the shape's containing rectangle relative to the shape anchor.
 *
 * @access public
 */
VMLGroup.prototype.setMarginRight = function( val )
{
	if ( val != null )
		this.elm.marginright = val;
};

/**
 * Specifies the top edge of the shape's containing rectangle relative to the shape anchor.
 *
 * @access public
 */
VMLGroup.prototype.setMarginTop = function( val )
{
	if ( val != null )
		this.elm.margintop = val;
};

/**
 * Specifies the horizontal positioning data for objects in Microsoft Word.
 *
 * @access public
 */
/*
VMLGroup.prototype.setMSOPositionHorizontal = function( val )
{
	if ( val != null )
		this.elm.msopositionhorizontal = val;
};
*/

/**
 * Specifies relative horizontal position data for objects in Microsoft Word.
 *
 * @access public
 */
/*
VMLGroup.prototype.setMSOPositionHorizontalRelative = function( val )
{
	if ( val != null )
		this.elm.msopositionhorizontalrelative = val;
};
*/

/**
 * Specifies the vertical position data for objects in Microsoft Word.
 *
 * @access public
 */
/*
VMLGroup.prototype.setMSOPositionVertical = function( val )
{
	if ( val != null )
		this.elm.msopositionvertical = val;
};
*/

/**
 * Specifies relative vertical position data for objects in Microsoft Word.
 *
 * @access public
 */
/*
VMLGroup.prototype.setMSOPositionVerticalRelative = function( val )
{
	if ( val != null )
		this.elm.msopositionverticalrelative = val;
};
*/

/**
 * Defines the distance from the bottom side of the shape to the text that wraps around it.
 *
 * @access public
 */
/*
VMLGroup.prototype.setMSOWrapDistanceBottom = function( val )
{
	if ( val != null )
		this.style.msowrapdistancebottom = val;
};
*/

/**
 * Defines the distance from the left side of the shape to the text that wraps around it.
 *
 * @access public
 */
/*
VMLGroup.prototype.setMSOWrapDistanceLeft = function( val )
{
	if ( val != null )
		this.style.msowrapdistanceleft = val;
};
*/

/**
 * Defines the distance from the right side of the shape to the text that wraps around it.
 *
 * @access public
 */
/*
VMLGroup.prototype.setMSOWrapDistanceRight = function( val )
{
	if ( val != null )
		this.style.msowrapdistanceright = val;
};
*/

/**
 * Defines the distance from the shape top to the text that wraps around it.
 *
 * @access public
 */
/*
VMLGroup.prototype.setMSOWrapDistanceTop = function( val )
{
	if ( val != null )
		this.style.msowrapdistancetop = val;
};
*/

/**
 * Determines whether the wrap coordinates were customized by the user.
 *
 * @access public
 */
/*
VMLGroup.prototype.setMSOWrapEdited = function( val )
{
	if ( val != null )
		this.style.msowrapedited = val;
};
*/

/**
 * Defines the wrapping mode for text.
 *
 * @access public
 */
/*
VMLGroup.prototype.setMSOWrapMode = function( val )
{
	if ( val != null )
		this.style.msowrapmode = val;
};
*/

/**
 * Determines whether the extra handles of a shape are hidden.
 *
 * @access public
 */
VMLGroup.prototype.setOnEd = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.oned = val;
};

/**
 * Defines a previous group for a shape.
 *
 * @access public
 */
/*
VMLGroup.prototype.setReGroupID = function( val )
{
	if ( val != null && Util.is_int( val ) )
		this.elm.regroupid = val;
};
*/

/**
 * Defines a relative position for an object.
 *
 * @access public
 */
VMLGroup.prototype.setRelativePosition = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.relativeposition = val;
};

/**
 * Defines the angle that a shape is rotated.
 *
 * @access public
 */
VMLGroup.prototype.setRotation = function( val )
{
	if ( val != null && VMLElement._isAngle( val ) )
		this.elm.rotation = val;
};

/**
 * List of minimum height values for each row in a table.
 *
 * @access public
 */
VMLGroup.prototype.setTableLimits = function( val )
{
	if ( val != null )
		this.elm.tablelimits = val;
};

/**
 * Determines table properties.
 *
 * @access public
 */
VMLGroup.prototype.setTableProperties = function( val )
{
	if ( val != null && Util.is_int( val ) )
		this.elm.tableproperties = val;
};

/**
 * Defines a frame or window that a URL will be displayed in.
 *
 * @access public
 */
VMLGroup.prototype.setTarget = function( val )
{
	if ( val != null )
		this.elm.target = val;
};

/**
 * Defines the text displayed when the mouse pointer moves over the shape.
 *
 * @access public
 */
VMLGroup.prototype.setTitle = function( val )
{
	if ( val != null )
		this.elm.title = val;
};

/**
 * Determines whether the user has added the shape to a master slide.
 *
 * @access public
 */
VMLGroup.prototype.setUserDrawn = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.userdrawn = val;
};

/**
 * Determines whether a script anchor is hidden.
 *
 * @access public
 */
VMLGroup.prototype.setUserHidden = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.userhidden = val;
};

/**  
 * Defines the bounding polygon that surrounds a shape.
 *
 * @access public
 */
VMLGroup.prototype.setWrapCoords = function( val )
{
	if ( val != null )
		this.elm.wrapcoords = val;
};

/**
 * @access public
 */
VMLGroup.prototype.setZIndex = function( val )
{
	if ( val != null )
		this.style.zindex = val;
};

/**
 * @access public
 */
VMLGroup.prototype.setXY = function( x, y )
{
	if ( x != null )
		this.style.left = x;
	
	if ( y != null )
		this.style.top  = y;
};

/**
 * @access public
 */
VMLGroup.prototype.setWH = function( w, h )
{
	if ( w != null )
		this.style.width  = w;
	
	if ( h != null )
		this.style.height = h;
};

/**
 * @access public
 */
VMLGroup.prototype.setBgColor = function( col )
{
	if ( col != null && VMLElement._isColor( col ) )
		this.style.background = col;
};

/**
 * @access public
 */
VMLGroup.prototype.setVisibility = function( val )
{
	if ( val != null && ( val == "visible" || val == "hidden" || val == "inherit" || val == "collapse" ) )
		this.style.visibility = val;
};

/**
 * @access public
 */
VMLGroup.prototype.setPosition = function( val )
{
	if ( val != null && ( val == "static" || val == "absolute" || val == "relative" ) )
		this.style.position = val;
};


// private methods

/**
 * @access private
 */
VMLGroup.prototype._isValidVMLElement = function( element )
{
	if ( Util.is_a( element, "VMLGroup" ) || Util.is_a( element, "VMLLocks" ) || Util.is_subclass_of( element, "VMLShapeBase" ) )
		return true;
	else
		return false;
};
