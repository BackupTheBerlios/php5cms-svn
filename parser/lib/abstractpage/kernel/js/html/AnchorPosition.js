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
 * @package html
 */
 
/**
 * Constructor
 *
 * @access public
 */
AnchorPosition = function()
{
	this.Base = Base;
	this.Base();
};


AnchorPosition.prototype = new Base();
AnchorPosition.prototype.constructor = AnchorPosition;
AnchorPosition.superclass = Base.prototype;

/**
 * This function returns an object having .x and .y properties which are the coordinates
 * of the named anchor, relative to the page.
 *
 * @access public
 */
AnchorPosition.prototype.getAnchorPosition = function( anchorname )
{
	// This function will return an Object with x and y properties.
	var useWindow   = false;
	var coordinates = new Object();
	var x = 0;
	var y = 0;
	
	// Browser capability sniffing
	var use_gebi   = false;
	var use_css    = false;
	var use_layers = false;
	
	if ( document.getElementById )
		use_gebi = true;
	else if ( document.all )
		use_css = true;
	else if ( document.layers )
		use_layers = true;
		
	// logic to find position
 	if ( use_gebi && document.all )
	{
		x = this._getPageOffsetLeft( document.all[anchorname] );
		y = this._getPageOffsetTop( document.all[anchorname] );
	}
	else if ( use_gebi )
	{
		var o = document.getElementById( anchorname );
		x = o.offsetLeft;
		y = o.offsetTop;
	}
 	else if ( use_css )
	{
		x = this._getPageOffsetLeft( document.all[anchorname] );
		y = this._getPageOffsetTop( document.all[anchorname] );
	}
	else if ( use_layers )
	{
		var found = 0;
		
		for ( var i = 0; i < document.anchors.length; i++ )
		{
			if ( document.anchors[i].name == anchorname )
			{
				found = 1;
				break;
			}
		}
		
		if ( found == 0 )
		{
			coordinates.x = 0;
			coordinates.y = 0;
			
			return coordinates;
		}
		
		x = document.anchors[i].x;
		y = document.anchors[i].y;
	}
	else
	{
		coordinates.x = 0;
		coordinates.y = 0;
		
		return coordinates;
	}
	
	coordinates.x = x;
	coordinates.y = y;
	
	return coordinates;
};

/**
 * This function returns an object having .x and .y properties which are the coordinates
 * of the named anchor, relative to the window.
 *
 * @access public
 */
AnchorPosition.prototype.getAnchorWindowPosition = function( anchorname )
{
	var coordinates = this.getAnchorPosition( anchorname );
	var x = 0;
	var y = 0;
	
	if ( document.getElementById )
	{
		if ( isNaN( window.screenX ) )
		{
			x = coordinates.x - document.body.scrollLeft + window.screenLeft;
			y = coordinates.y - document.body.scrollTop  + window.screenTop;
		}
		else
		{
			x = coordinates.x + window.screenX + ( window.outerWidth - window.innerWidth ) - window.pageXOffset;
			y = coordinates.y + window.screenY + ( window.outerHeight - 24 - window.innerHeight ) - window.pageYOffset;
		}
	}
	else if ( document.all )
	{
		x = coordinates.x - document.body.scrollLeft + window.screenLeft;
		y = coordinates.y - document.body.scrollTop  + window.screenTop;
	}
	else if ( document.layers )
	{
		x = coordinates.x + window.screenX + ( window.outerWidth - window.innerWidth ) - window.pageXOffset;
		y = coordinates.y + window.screenY + ( window.outerHeight - 24 - window.innerHeight ) - window.pageYOffset;
	}
	
	coordinates.x = x;
	coordinates.y = y;
	
	return coordinates;
};


// private methods

/**
 * Functions for IE to get position of an object.
 *
 * @access private
 */
AnchorPosition.prototype._getPageOffsetLeft = function( el )
{
	var ol = el.offsetLeft;
	
	while ( ( el = el.offsetParent ) != null ) 
		ol += el.offsetLeft; 
	
	return ol;
};

/**
 * @access private
 */
AnchorPosition.prototype._getWindowOffsetLeft = function( el )
{
	var scrollamount = document.body.scrollLeft;
	return this._getPageOffsetLeft(el) - scrollamount;
};

/**
 * @access private
 */
AnchorPosition.prototype._getPageOffsetTop = function( el )
{
	var ot = el.offsetTop;

	while ( ( el = el.offsetParent ) != null ) 
		ot += el.offsetTop; 
	
	return ot;
};

/**
 * @access private
 */
AnchorPosition.prototype._getWindowOffsetTop = function( el )
{
	var scrollamount = document.body.scrollTop;
	return this._getPageOffsetTop( el ) - scrollamount;
};
