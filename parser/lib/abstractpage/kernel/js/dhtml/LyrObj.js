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
 * @package dhtml
 */
 
/**
 * Constructor
 *
 * This class encapsulates a DIV element and provides cross-browser
 * access to its properties such as position, visibility, and clipping.
 * This implementation is the lean core class for working with layers.
 *
 * @access public
 */
LyrObj = function( lyrName, nestedRef )
{
	this.Base = Base;
	this.Base();
	
	// assign id
	this.lyrname = lyrName;

	// if the nestedRef argument is supplied, this constructs the path for Netscape 4.x
	if ( Browser.ns4 && nestedRef )
		lyrName = nestedRef + ".document." + lyrName;
	
	// get the layer's object reference
	this.ref = Browser.ns4? LyrObj.getRef( lyrName ) : LyrObj.getRef( lyrName ).style;

	return this;
};


LyrObj.prototype = new Base();
LyrObj.prototype.constructor = LyrObj;
LyrObj.superclass = Base.prototype;

/**
 * Returns the current position of the layer, use the CSS
 * names, "left" for x and "top" for y.
 *
 * @access public
 */
LyrObj.prototype.getPos = function( which )
{
	if ( Browser.ns4 )
		return this.ref[which];
	
	if ( Browser.ie || Browser.ns6 )
		return this.ref[which].split( "px" )[0];
};

/**
 * Sets the position of the layer, again, this works with
 * CSS names, "left" for x and "top" for y.
 *
 * @access public
 */
LyrObj.prototype.setPos = function( which, pos )
{
	if ( this.ref )
		this.ref[which] = pos;
};

/**
 * Returns the current clip value of the layer.
 * The value has to be set beforehand, otherwise
 * the result is undefined.
 *
 * @access public
 */
LyrObj.prototype.getClip = function( which )
{
	if ( Browser.ns4 )
		return this.ref.clip[which];
	
	if ( Browser.ie || Browser.ns6 )
	{
		// strip 
		var clipPos = this.ref.clip.split("rect(")[1].split(")")[0].split("px");
		
		switch ( which )
		{
			case "top" :
				return Number( clipPos[0] );
			
			case "right" :
				return Number( clipPos[1] );
			
			case "bottom" :
				return Number( clipPos[2] );
			
			case "left" :
				return Number( clipPos[3] );
		}
	}
};

/**
 * Sets the layer's clip rect property to the
 * rectangle (left, top, right, bottom).
 *
 * @access public
 */
LyrObj.prototype.setClip = function( left, top, right, bottom )
{
	if ( Browser.ns4 )
	{
		this.ref.clip.top    = top;
		this.ref.clip.right  = right;
		this.ref.clip.bottom = bottom;
		this.ref.clip.left   = left;
	}
	
	if ( Browser.ie || Browser.ns6 )
		this.ref.clip = "rect(" + top +"px " + right +"px " + bottom +"px " + left +"px)";
};

/**
 * Returns the layer's current visibility.
 *
 * @access public
 */
LyrObj.prototype.getVisibility = function()
{
	v = this.ref.visibility;
	return v.indexOf( "hid" ) == -1;
};

/**
 * Sets the layer's visibility.
 *
 * @access public
 */
LyrObj.prototype.setVisibility = function( visible )
{
	if ( Browser.ns4 )
		this.ref.visibility = visible? "show" : "hide";
	
	if ( Browser.ie || Browser.ns6 )
		this.ref.visibility = visible? "visible" : "hidden";
};

/**
 * Shows the layer. Shortcut to setVisibility(true).
 *
 * @access public
 */
LyrObj.prototype.show = function()
{
	this.setVisibility( true );
};

/**
 * Hides the layer. Shortcut to setVisibility(false).
 *
 * @access public
 */
LyrObj.prototype.hide = function()
{
	this.setVisibility( false );
};

/**
 * Returns the layer's current z-Index.
 *
 * @access public
 */
LyrObj.prototype.getzIndex = function()
{
	return this.ref.zIndex;
};

/**
 * Sets the layer's z-Index.
 *
 * @access public
 */
LyrObj.prototype.setzIndex = function( zIndex )
{
	if ( this.ref )
		this.ref.zIndex = zIndex;
};

/**
 * Returns the current x position of the layer.
 * Shortcut to getPos("left").
 *
 * @access public
 */
LyrObj.prototype.getX = function()
{
	return this.getPos( "left" );
};

/**
 * Returns the current y position of the layer.
 * Shortcut to getPos("top").
 *
 * @access public
 */
LyrObj.prototype.getY = function()
{
	return this.getPos( "top" );
};

/**
 * Returns a string representation of the current LyrObj.
 * The html parameter determines the kind of line breaks:
 * true for HTML line breaks, false for normal text.
 *
 * @access public
 */
LyrObj.prototype.toString = function( html )
{
	c = ( html != null && html )? "<br>" : "\n" ;
	
	return this.lyrname + c +
		"pos : (x:" + this.getX() + ", y:" + this.getY() + ", z:" + this.getzIndex() + ")" + c +
		"clip : (left:" + this.getClip("left")   + ", top:"    +
						  this.getClip("top")    + ", right:"  +
						  this.getClip("right")  + ", bottom:" +
						  this.getClip("bottom") + ")" + c +
		"vis : " + this.getVisibility();
};

/**
 * Sets the layer to the target position (x, y).
 *
 * @access public
 */
LyrObj.prototype.moveTo = function( x, y )
{
	this.setPos( "left", x );
	this.setPos( "top",  y );
};

/**
 * Moves the layer by the specified diffences, relative
 * to its current position.
 *
 * @access public
 */
LyrObj.prototype.moveBy = function( dx, dy )
{
	this.setPos( "left", 1 * this.getPos( "left" ) + dx );
	this.setPos( "top",  1 * this.getPos( "top"  ) + dy );
};


/**
 * Returns the reference to the actual DIV element.
 *
 * @access public
 * @static
 */
LyrObj.getRef = function( layerName )
{
	if ( Browser.ns4 )
		return eval( "document." + layerName );
		
	if ( Browser.ie )
		return eval( "document.all." + layerName );
	
	if ( Browser.ns6 )
		return document.getElementById( layerName );
};

/**
 * Intention: After starting Netscape 4.x, the first
 * call to the constructor fails, throwing an error.
 * This is a fix that simply causes a reload of the
 * page in case the test layer couldn't be accessed.
 * To apply, make sure the page contains the line
 * <div id="fixnetscape" style="position:absolute;visibility:hidden"></div>
 * and call this function in your onLoad-handler.
 *
 * @access public
 * @static
 */
LyrObj.fixNetscape = function()
{
	if ( !LyrObj.getRef( "fixnetscape" ) )
		document.location.reload();
};
