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
 * VMLShapeBase Class
 * Do not call directly!
 *
 * @package image_vml_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
VMLShapeBase = function()
{
	this.VMLElement = VMLElement;
	this.VMLElement();
};


VMLShapeBase.prototype = new VMLElement();
VMLShapeBase.prototype.constructor = VMLShapeBase;
VMLShapeBase.superclass = VMLElement.prototype;

/**
 * @access public
 */
VMLShapeBase.prototype.init = function( x, y, w, h, shapetype, id )
{
	this.shapetype = shapetype;
	
	var ele  = VMLCanvas.defaultNamespace + ":" + this.shapetype;
	this.elm = document.createElement( ele );
	this.elm.id = id || "vmlelement" + ( VMLElement.idcount++ );

	this.style = this.elm.style;
	
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

/**
 * @access public
 */
VMLShapeBase.prototype.killEvents = function()
{
	this.elm.onmouseup = function( e )
	{
		event.cancelBubble = true;
	}
	this.elm.onmouseout = function( e )
	{
		event.cancelBubble = true;
	}
	this.elm.onmouseover = function( e )
	{
		event.cancelBubble = true;
	}
	this.elm.onmousedown = function( e )
	{
		event.cancelBubble = true;
	}
	this.elm.oncontextmenu = function()
	{
		return false;
	}
};

/**
 * @access public
 */
VMLShapeBase.prototype.addEventListener = function( evType, fn, useCapture )
{
	if ( evType.indexOf( 'mouse' ) == 0)
		return this.elm.attachEvent( "on" + evType, fn );
	else
		eval( this["on" + evType] = fn );
};

/**
 * @access public
 */
VMLShapeBase.prototype.removeEventListener = function( evType, fn, useCapture )
{
	if ( evType.indexOf( 'mouse' ) == 0 )
		return this.elm.detachEvent( "on" + evType, fn );
	else
		eval( this["on" + evType] = null );
};

/**
 * @access public
 */
VMLShapeBase.prototype.invokeEvent = function( evType, args )
{
	var ret = true;
	
	if ( this["on" + evType] )
		ret = this["on" + evType]( args );
	
	if ( ret && this.parent )
		this.parent.invokeEvent( evType, args );
};

/**
 * Specifies an adjustment value used to define values for a formula.
 *
 * @access public
 */
VMLShapeBase.prototype.setAdj = function( val )
{
	if ( val != null )
		this.elm.adj = val;
};

/**
 * Determines whether a shape can be placed in a table.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setAllowInCell = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.allowincell = val; // o:allowincell
};
*/

/**
 * Determines if a shape can overlap other shapes. 
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setAllowOverlap = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.allowoverlap = val; // o:allowoverlap
};
*/

/**
 * Defines alternative text to be displayed instead of a graphic.
 *
 * @access public
 */
VMLShapeBase.prototype.setAlt = function( val )
{
	if ( val != null )
		this.elm.alt = val;
};

/**
 * @access public
 */
VMLShapeBase.prototype.setAntiAlias = function( val )
{
	if ( val != null && Util.is_bool( val ) )
		this.style.antialias = val;
};

/**
 * Bottom border color of an inline shape.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setBorderBottomColor = function( val )
{
	if ( val != null && ( VMLElement._isColor( val ) || val == "null" || val == "this" ) )
		this.elm.borderbottomcolor = val; // o:borderbottomcolor
};
*/

/**
 * Left border color of an inline shape.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setBorderLeftColor = function( val )
{
	if ( val != null && ( VMLElement._isColor( val ) || val == "null" || val == "this" ) )
		this.elm.borderleftcolor = val; // o:borderleftcolor or bordercolor ?
};
*/

/**
 * Right border color of an inline shape.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setBorderRightColor = function( val )
{
	if ( val != null && ( VMLElement._isColor( val ) || val == "null" || val == "this" ) )
		this.elm.borderrightcolor = val; // o:borderrightcolor or borderrightcolor ?
};
*/

/**
 * Top border color of an inline shape.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setBorderTopColor = function( val )
{
	if ( val != null && ( VMLElement._isColor( val ) || val == "null" || val == "this" ) )
		this.elm.bordertopcolor = val; // o:bordertopcolor
};
*/

/**
 * Determines whether a shape is a graphical bullet.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setBullet = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.bullet = val; // o:bullet
};
*/

/**
 * Determines whether a shape will be processed as a button.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setButton = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.button = val; // o:button
};
*/

/**
 * Determines how a shape will render for black-and-white output devices.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setBWMode = function( val )
{
	if ( val != null && this._isBWMode( val ) )
		this.elm.bwmode = val; // o:bwmode
};
*/

/**
 * Defines the black-and-white mode for normal black-and-white output devices.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setBWNormal = function( val )
{
	if ( val != null && this._isBWMode( val ) )
		this.elm.bwnormal = val; // o:bwnormal
};
*/

/**
 * Defines the black-and-white mode for pure black-and-white output devices.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setBWPure = function( val )
{
	if ( val != null && this._isBWMode( val ) )
		this.elm.bwpure = val; // o:bwpure
};
*/

/**
 * Refers to a definition of a CSS style.
 *
 * @access public
 */
VMLShapeBase.prototype.setClass = function( val )
{
	if ( val != null )
		this.elm.classname = val;
};

/**
 * Indicates the type of connector used for joining shapes.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setConnectorType = function( val )
{
	if ( val != null && ( val == "none" || val == "straight" || val == "elbow" || val == "curved" ) )
		this.elm.connectortype = val; // o:connectortype
};
*/

/**
 * Specifies the coordinate unit origin of the rectangle that bounds a shape.
 *
 * @access public
 */
VMLShapeBase.prototype.setCoordOrigin = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.coordorigin = val;
};

/**
 * Specifies the horizontal and vertical units of the rectangle that bounds a shape.
 *
 * @access public
 */
VMLShapeBase.prototype.setCoordSize = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.coordsize = val;
};

/**
 * Sends an event message when a shape is double-clicked.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setDoubleClickNotify = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.doubleclicknotify = val; // o:doubleclicknotify
};
*/

/**
 * Defines the brush color that fills the closed path of a shape.
 *
 * @access public
 */
VMLShapeBase.prototype.setFillColor = function( col )
{
	if ( col != null && VMLElement._isColor( col ) )
		this.elm.fillcolor = col;
};

/**
 * Determines whether the closed path will be filled.
 *
 * @access public
 */
VMLShapeBase.prototype.setFilled = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.filled = val;
};

/**
 * Switches the orientation of a shape.
 *
 * @access public
 */
VMLShapeBase.prototype.setFlip = function( val )
{
	if ( val != null && ( val == "x" || val == "y" || val == "xy" || val == "yx" ) )
		this.style.flip = val;
};

/**
 * Determines whether a dashed outline is used to draw a shape when a shape has no line or fill.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setForceDash = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.forcedash = val; // o:forcedash
};
*/

/**
 * Specifies that a shape is a horizontal rule.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setHR = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.hr = val; // o:hr
};
*/

/**
 * Defines the alignment of a horizontal rule.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setHRAlign = function( val )
{
	if ( val != null && ( val == "left" || val == "center" || val == "right" ) )
		this.elm.hralign = val; // o:hralign
};
*/

/**
 * Defines a URL for a shape. When the shape is clicked, the browser will load the URL.
 *
 * @access public
 */
VMLShapeBase.prototype.setHRef = function( val )
{
	if ( val != null )
		this.elm.href = val;
};

/**
 * Defines the thickness of a horizontal rule.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setHRHeight = function( val )
{
	if ( val != null )
		this.elm.hrheight = val; // o:hrheight
};
*/

/**
 * Determines whether a horizontal rule will be displayed with 3-D shading.
 * Note: script syntax not explicitly mentioned in reference<br>
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setHRNoShade = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.hrnoshade = val; // o:hrnoshade
};
*/

/**
 * Defines the length of a horizontal rule as a percentage of page width.
 * Note: script syntax not explicitly mentioned in reference<br>
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setHRPct = function( val )
{
	if ( val != null && VMLElement._isFraction( val ) )
		this.elm.hrpct = val; // o:hrpct
};
*/

/**
 * Determines whether a shape is a standard horizontal rule.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setHRStd = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.hrstd = val; // o:hrstd
};
*/

/**
 * Defines the length of a horizontal rule.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setHRWidth = function( val )
{
	if ( val != null )
		this.elm.hrwidth = val; // o:hrwidth
};
*/

/**
 * Specifies the bottom edge of the shape's containing rectangle relative to the shape anchor.
 *
 * @access public
 */
VMLShapeBase.prototype.setMarginBottom = function( val )
{
	if ( val != null )
		this.elm.marginbottom = val;
};

/**
 * Specifies the left edge of the shape's containing rectangle relative to the shape anchor.
 *
 * @access public
 */
VMLShapeBase.prototype.setMarginLeft = function( val )
{
	if ( val != null )
		this.elm.marginleft = val;
};

/**
 * Specifies the right edge of the shape's containing rectangle relative to the shape anchor.
 *
 * @access public
 */
VMLShapeBase.prototype.setMarginRight = function( val )
{
	if ( val != null )
		this.elm.marginright = val;
};

/**
 * Specifies the top edge of the shape's containing rectangle relative to the shape anchor.
 *
 * @access public
 */
VMLShapeBase.prototype.setMarginTop = function( val )
{
	if ( val != null )
		this.elm.margintop = val; // Spec: margin-top
};

/**
 * Specifies the horizontal positioning data for objects in Microsoft Word.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setMSOPositionHorizontal = function( val )
{
	if ( val != null )
		this.elm.msopositionhorizontal = val; // mso-position-horizontal
};
*/

/**
 * Specifies relative horizontal position data for objects in Microsoft Word.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setMSOPositionHorizontalRelative = function( val )
{
	if ( val != null )
		this.elm.msopositionhorizontalrelative = val; // mso-position-horizontal-relative
};
*/

/**
 * Specifies the vertical position data for objects in Microsoft Word.
 * Note: script syntax not explicitly mentioned in reference<br>
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setMSOPositionVertical = function( val )
{
	if ( val != null )
		this.elm.msopositionvertical = val; // mso-position-vertical
};
*/

/**
 * Specifies relative vertical position data for objects in Microsoft Word.
 * Note: script syntax not explicitly mentioned in reference<br>
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setMSOPositionVerticalRelative = function( val )
{
	if ( val != null )
		this.elm.msopositionverticalrelative = val; // mso-position-vertical-relative
};
*/

/**
 * Defines the distance from the bottom side of the shape to the text that wraps around it.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setMSOWrapDistanceBottom = function( val )
{
	if ( val != null )
		this.style.msowrapdistancebottom = val; // mso-wrap-distance-bottom
};
*/

/**
 * Defines the distance from the left side of the shape to the text that wraps around it.
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setMSOWrapDistanceLeft = function( val )
{
	if ( val != null )
		this.style.msowrapdistanceleft = val; // mso-wrap-distance-left
};
*/

/**
 * Defines the distance from the right side of the shape to the text that wraps around it.
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setMSOWrapDistanceRight = function( val )
{
	if ( val != null )
		this.style.msowrapdistanceright = val; // mso-wrap-distance-right
};
*/

/**
 * Defines the distance from the shape top to the text that wraps around it.
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setMSOWrapDistanceTop = function( val )
{
	if ( val != null )
		this.style.msowrapdistancetop = val; // mso-wrap-distance-top
};
*/

/**
 * Determines whether the wrap coordinates were customized by the user.
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setMSOWrapEdited = function( val )
{
	if ( val != null )
		this.style.msowrapedited = val; // mso-wrap-edited
};
*/

/**
 * Defines the wrapping mode for text.
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setMSOWrapMode = function( val )
{
	if ( val != null )
		this.style.msowrapmode = val; // mso-wrap-mode
};
*/

/**
 * Determines whether an OLE object will be displayed as an icon.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setOLEIcon = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.oleicon = val; // o:oleicon
};
*/

/**
 * Determines whether the extra handles of a shape are hidden.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setOnEd = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.oned = val; // o:oned
};
*/

/**
 * Specifies the line that makes up the edges of a shape.
 *
 * @access public
 */
VMLShapeBase.prototype.setPath = function( val )
{
	if ( val != null )
		this.elm.path = val;
};

/**
 * Defines the type of positioning used to place an element.
 *
 * @access public
 */
VMLShapeBase.prototype.setPosition = function( val )
{
	if ( val != null && ( val == "static" || val == "absolute" || val == "relative" ) )
		this.style.position = val;
};

/**
 * Determines whether the original size of an object is saved after reformatting.
 * Note: script syntax not explicitly mentioned in reference<br>
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setPreferRelative = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.preferrelative = val; // o:preferrelative
};
*/

/**
 * Determines whether the shape will be printed.
 *
 * @access public
 */
VMLShapeBase.prototype.setPrint = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.print = val;
};

/**
 * Defines a previous group for a shape.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setReGroupID = function( val )
{
	if ( val != null && Util.is_int( val ) )
		this.elm.regroupid = val; // o:regroupid
};
*/

/**
 * Defines a relative position for an object.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setRelativePosition = function( val )
{
	if ( val != null && VMLElement._isVector2D( val ) )
		this.elm.relativeposition = val; // o:relativeposition
};
*/

/**
 * Defines the angle that a shape is rotated.
 *
 * @access public
 */
VMLShapeBase.prototype.setRotation = function( val )
{
	if ( val != null && VMLElement._isAngle( val ) )
		this.elm.rotation = val;
};

/**
 * Determines whether a rules engine will be used.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setRuleInitiator = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.ruleinitiator = val; // o:ruleinitiator
};
*/

/**
 * Determines whether a proxy for the rules engine will be used.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setRuleProxy = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.ruleproxy = val;
};
*/

/**
 * Defines a number used internally by Microsoft Office to identify types of shapes.
 *
 * @access public
 */
VMLShapeBase.prototype.setSpt = function( val )
{
	if ( val != null )
		this.elm.spt = val;
};

/**
 * Defines the brush color that strokes the path of a shape.
 *
 * @access public
 */
VMLShapeBase.prototype.setStrokeColor = function( col )
{
	if ( col != null && VMLElement._isColor( col ) )
		this.elm.strokecolor = col;
};

/**
 * Defines whether the path will be stroked.
 *
 * @access public
 */
VMLShapeBase.prototype.setStroked = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.stroked = val;
};

/**
 * Defines the brush thickness that strokes the path of a shape.
 *
 * @access public
 */
VMLShapeBase.prototype.setStrokeWeight = function( val )
{
	if ( val != null )
		this.elm.strokeweight = val;
};

/**
 * List of minimum height values for each row in a table.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setTableLimits = function( val )
{
	if ( val != null )
		this.elm.tablelimits = val; // o:tablelimits
};
*/

/**
 * Determines table properties.
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setTableProperties = function( val )
{
	if ( val != null && Util.is_int( val ) )
		this.elm.tableproperties = val; // o:tableproperties
};
*/

/**
 * Defines a frame or window that a URL will be displayed in.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setTarget = function( val )
{
	if ( val != null )
		this.elm.target = val;
};
*/

/**
 * Defines the text displayed when the mouse pointer moves over the shape.
 *
 * @access public
 */
VMLShapeBase.prototype.setTitle = function( val )
{
	if ( val != null )
		this.elm.title = val;
};

/**
 * Determines whether the user has added the shape to a master slide.
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setUserDrawn = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.userdrawn = val; // o:userdrawn
};
*/

/**
 * Determines whether a script anchor is hidden.
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setUserHidden = function( val )
{
	if ( val != null && VMLElement._isTriState( val ) )
		this.elm.userhidden = val; // o:userhidden
};
*/

/**
 * @access public
 */
VMLShapeBase.prototype.setXY = function( x, y )
{
	if ( x != null )
		this.style.left = x;
	
	if ( y != null )
		this.style.top  = y;
};

/**
 * @access public
 */
VMLShapeBase.prototype.setWH = function( w, h )
{
	if ( w != null )
		this.style.width  = w;
	
	if ( h != null )
		this.style.height = h;
};

/**
 * @access public
 */
VMLShapeBase.prototype.setVisibility = function( val )
{
	if ( val != null && ( val == "visible" || val == "hidden" || val == "inherit" || val == "collapse" ) )
		this.style.visibility = val;
};

/**
 * Defines the bounding polygon that surrounds a shape. 
 * Note: script syntax not explicitly mentioned in reference
 *
 * @access public
 */
/*
VMLShapeBase.prototype.setWrapCoords = function( val )
{
	if ( val != null )
		this.elm.wrapcoords = val; // o:wrapcoords
};
*/

/**
 * @access public
 */
VMLShapeBase.prototype.setZIndex = function( val )
{
	if ( val != null )
		this.style.zindex = val;
};


// private methods

/**
 * @access private
 */
VMLShapeBase.prototype._isValidVMLElement = function( element )
{	
	// valid subelements
	if  ( element && (
	 	  Util.is_a( element, "VMLCallout"   ) || 
	 	  Util.is_a( element, "VMLExtrusion" ) || 
		  Util.is_a( element, "VMLFill"      ) || 
		  Util.is_a( element, "VMLFormulas"  ) || 
		  Util.is_a( element, "VMLHandles"   ) || 
		  Util.is_a( element, "VMLImageData" ) || 
		  Util.is_a( element, "VMLLocks"     ) || 
		  Util.is_a( element, "VMLPath"      ) || 
		  Util.is_a( element, "VMLShadow"    ) || 
		  Util.is_a( element, "VMLSkew"      ) || 
		  Util.is_a( element, "VMLStroke"    ) || 
		  Util.is_a( element, "VMLTextBox"   ) || 
		  Util.is_a( element, "VMLTextPath"  ) ) )
	{
		return true;
	}
	else
	{
		return false;
	}
};

/**
 * @access private
 */
VMLShapeBase.prototype._getShapeType = function()
{
	return this.shapetype;
};

/**
 * @access private
 * @static
 */
VMLShapeBase._isBWMode = function( val )
{
	if ( val == "color"             ||
		 val == "auto"              ||
		 val == "grayscale"         ||
		 val == "lightgrayscale"    ||
		 val == "inversegray"       ||
		 val == "grayoutline"       ||
		 val == "blacktextandlines" ||
		 val == "highcontrast"      ||
		 val == "black"             ||
		 val == "white"             ||
		 val == "undrawn" )
	{
		return true;
	}
	else
	{
		return false;
	}
};
