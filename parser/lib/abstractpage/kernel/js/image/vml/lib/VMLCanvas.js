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
 * @package image_vml_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
VMLCanvas = function( x, y, w, h, id )
{
	this.Base = Base;
	this.Base();
	
	this.elm = document.createElement( "DIV" );
	this.elm.id = id || "vmlcanvas" + ( VMLCanvas.idcount++ );
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

	this.setVisibility( "visible" );
	this.setOverflow( "hidden" );
	this.setWH( w || 100, h || 100 );
	
	this.css = new CSS();
	this.css.addRule( ( VMLCanvas.defaultNamespace + "\\:*" ), "behavior: url(#default#VML);" );
	this.css.addRule( ( VMLCanvas.officeNamespace  + "\\:*" ), "behavior: url(#default#VML);" );
	
	this.elements = new Array();	
	this.count = 0;
};


VMLCanvas.prototype = new Base();
VMLCanvas.prototype.constructor = VMLCanvas;
VMLCanvas.superclass = Base.prototype;

/**
 * @access public
 */
VMLCanvas.prototype.setCanvasID = function( id )
{
	if ( id != null )
		this.elm.id = id;
};

/**
 * @access public
 */
VMLCanvas.prototype.getCanvasID = function( id )
{
	return this.elm.id;
};

/**
 * @access public
 */
VMLCanvas.prototype.add = function( element )
{
	if ( !this._isValidVMLElement( element ) )
		return false;

	this.elements[this.elements.length] = element;
	this.count++;
	
	return true;
};

/**
 * @access public
 */
VMLCanvas.prototype.getElementCount = function()
{
	return this.count;
};

/**
 * @access public
 */
VMLCanvas.prototype.setOverflow = function( val )
{
	if ( val != null && ( val == "visible" || val == "hidden" || val == "auto" || val == "scroll" ) )
		this.style.overflow = val;
};

/**
 * @access public
 */
VMLCanvas.prototype.setXY = function( x, y )
{
	if ( x != null )
		this.style.left = x;
	
	if ( y != null )
		this.style.top  = y;
};

/**
 * @access public
 */
VMLCanvas.prototype.setWH = function( w, h )
{
	if ( w != null )
		this.style.width  = w;
	
	if ( h != null )
		this.style.height = h;
};

/**
 * @access public
 */
VMLCanvas.prototype.setBgColor = function( col )
{
	if ( col != null && VMLElement._isColor( col ) )
		this.style.background = col;
};

/**
 * @access public
 */
VMLCanvas.prototype.setVisibility = function( val )
{
	if ( val != null && ( val == "visible" || val == "hidden" || val == "inherit" || val == "collapse" ) )
		this.style.visibility = val;
};

/**
 * @access public
 */
VMLCanvas.prototype.getX = function()
{
	return this.elm.offsetLeft;
};

/**
 * @access public
 */
VMLCanvas.prototype.getY = function()
{
	return this.elm.offsetTop;
};

/**
 * @access public
 */
VMLCanvas.prototype.getW = function()
{
	return this.elm.offsetWidth;
};

/**
 * @access public
 */
VMLCanvas.prototype.getH = function()
{
	return this.elm.offsetHeight;
};

/**
 * @access public
 */
VMLCanvas.prototype.setPosition = function( val )
{
	if ( val != null && ( val == "static" || val == "absolute" || val == "relative" ) )
		this.style.position = val;
};

/**
 * @access public
 */
VMLCanvas.prototype.draw = function( div )
{
	if ( this.elements.length == 0 )
		return false;
		
	for ( var i in this.elements )
	{
		this.elements[i]._build();
		this.elm.appendChild( this.elements[i].elm );
	}
	
	if ( div != null && document.getElementById( div ) )
		document.getElementById( div ).appendChild( this.elm );
	else
		document.getElementsByTagName( "BODY" ).item( 0 ).appendChild( this.elm );	
};


// private methods

/**
 * @access private
 */
VMLCanvas.prototype._isValidVMLElement = function( element )
{
	if ( Util.is_a( element, "VMLGroup" ) || Util.is_a( element, "VMLFrame" ) || Util.is_subclass_of( element, "VMLShapeBase" ) )
		return true;
	else
		return false;
};


/**
 * @access public
 * @static
 */
VMLCanvas.defaultNamespace = "v";

/**
 * @access public
 * @static
 */
VMLCanvas.officeNamespace = "o";

/**
 * @access public
 * @static
 */
VMLCanvas.idcount = 0;
