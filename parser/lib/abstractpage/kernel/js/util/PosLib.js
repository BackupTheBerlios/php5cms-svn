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
 * @package util
 */
 
/**
 * Constructor
 *
 * @access public
 */
PosLib = function()
{
	this.Base = Base;
	this.Base();
};


PosLib.prototype = new Base();
PosLib.prototype.constructor = PosLib;
PosLib.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
PosLib.getIeBox = function( el )
{
	return Browser.ie && el.document.compatMode != "CSS1Compat";
};

/**
 * Relative client viewport (outer borders of viewport).
 *
 * @access public
 * @static
 */
PosLib.getClientLeft = function( el )
{
	var r = el.getBoundingClientRect();
	return r.left - this.getBorderLeftWidth( this.getCanvasElement( el ) );
};

/**
 * @access public
 * @static
 */
PosLib.getClientTop = function( el )
{
	var r = el.getBoundingClientRect();
	return r.top - this.getBorderTopWidth( this.getCanvasElement( el ) );
};

/**
 * Relative canvas/document (outer borders of canvas/document,
 * outside borders of element).
 *
 * @access public
 * @static
 */
PosLib.getLeft = function( el )
{
	return this.getClientLeft( el ) + this.getCanvasElement( el ).scrollLeft;
};

/**
 * @access public
 * @static
 */
PosLib.getTop =	function( el )
{
	return this.getClientTop( el ) + this.getCanvasElement( el ).scrollTop;
};

/**
 * Relative canvas/document (outer borders of canvas/document,
 * inside borders of element).
 *
 * @access public
 * @static
 */
PosLib.getInnerLeft = function( el )
{
	return this.getLeft( el ) + this.getBorderLeftWidth( el );
};

/**
 * @access public
 * @static
 */
PosLib.getInnerTop = function( el )
{
	return this.getTop( el ) + this.getBorderTopWidth( el );
};

/**
 * Get width (outer, border-box).
 *
 * @access public
 * @static
 */
PosLib.getWidth = function( el )
{
	return el.offsetWidth;
};

/**
 * Get height (outer, border-box).
 *
 * @access public
 * @static
 */
PosLib.getHeight = function( el )
{
	return el.offsetHeight;
};

/**
 * @access public
 * @static
 */
PosLib.getCanvasElement = function( el )
{
	var doc = el.ownerDocument || el.document;	// IE55 bug

	if ( doc.compatMode == "CSS1Compat" )
		return doc.documentElement;
	else
		return doc.body;
};

/**
 * @access public
 * @static
 */
PosLib.getBorderLeftWidth = function( el )
{
	return el.clientLeft;
};

/**
 * @access public
 * @static
 */
PosLib.getBorderTopWidth = function( el )
{
	return el.clientTop;
};

/**
 * @access public
 * @static
 */
PosLib.getScreenLeft = function( el )
{
	var doc = el.ownerDocument || el.document;	// IE55 bug
	var w   = doc.parentWindow;

	return w.screenLeft + this.getBorderLeftWidth( this.getCanvasElement( el ) ) + this.getClientLeft( el );
};

/**
 * @access public
 * @static
 */
PosLib.getScreenTop = function( el )
{
	var doc = el.ownerDocument || el.document;	// IE55 bug
	var w   = doc.parentWindow;
	
	return w.screenTop + this.getBorderTopWidth( this.getCanvasElement( el ) ) + this.getClientTop( el );
};
