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
 * @package dhtml_gen_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
DomScroll = function( x, y, w, h, contentLayer, mBgOn, mBgOff, dBgUP, dBgOV, dBgDW )
{
	this.DomLayer = DomLayer;
	this.DomLayer( null, x, y, w + 22, h );

	var l = new DomEventListener( this );

	this.mBgOn   = mBgOn;
	this.mBgOff  = mBgOff;
	this.dBgUP   = dBgUP;
	this.dBgOV   = dBgOV;
	this.dBgDW   = dBgDW;
	
	this.content = contentLayer;
	this.slider  = new DomLayer( null, this.w - 22, 0, 22, this.w, this.mBgOn );
	this.dragCT  = new DomLayer( null, this.w - 22, 0, 22, 8, this.dBgUP );

	this.dragCTevents = new DomEventListener( this );
	this.dragCTevents.onmouseover = function( e )
	{	
		if ( !e.isondrag )
			e.target.dragCT.setBgColor( e.target.dBgOV );
			
		e.bubble = false;
	}
	this.dragCTevents.onmousedown = function( e )
	{	
		if ( !e.isondrag )
			e.target.dragCT.setBgColor( e.target.dBgDW );
			
		e.bubble = false;
	}
	this.dragCTevents.onmouseout = function( e )
	{	
		if ( !e.isondrag )
			e.target.dragCT.setBgColor( e.target.dBgUP );
			
		e.bubble = false;
	}
	this.dragCTevents.onmouseclick = function( e )
	{	
		e.target.dragCT.setBgColor( e.target.dBgOV );
		e.bubble = false;
	}
	this.dragCTevents.ondragstart = function( e )
	{
		e.target.dragCT.setBgColor( e.target.dBgDW );
		e.target.scrollInterval = setInterval( e.target.toString() + '.updateScroll()', 20 );
		e.bubble = false;
	}
	this.dragCTevents.ondragend = function( e )
	{
		if ( e.dragout )
			e.source.invokeEvent( 'mouseout' );
		else
			e.source.invokeEvent( 'mouseover' );
			
		clearInterval( e.target.scrollInterval );
		e.bubble = false;
	}

	this.dragCT.setLimit( 0, this.w, 0 + this.h, 0 + this.w - 22 );
	this.dragCT.addEventListener( this.dragCTevents );

	this.block( true );

	if ( !this.content.eventListeners )
	{
		var l = new DomEventListener( this );
	}
	else
	{
		var l = this.content.eventListeners;
		this.content.eventListeners.target = this;
	}

	l.onbeforeload = function( e )
	{	
		e.target.maxH = 0;
		e.target.dragCT.moveTo( null, 0 );
		e.target.block( true );
	}
	l.onload = function( e )
	{
		e.target.maxH = e.target.content.contentH();
		e.target.content.moveTo( null, 0 );
		e.target.content.setClip( [ 0, null, e.target.h, null ] );
		
		if ( e.target.maxH > e.target.h )
			e.target.block( false );
			
		e.target.invokeEvent( 'load' );
	}
	
	if ( !this.content.eventListeners )
		this.content.addEventListener( l );

	this.add( this.content );
	this.add( this.slider  );
	this.add( this.dragCT  );
};


DomScroll.prototype = new DomLayer();
DomScroll.prototype.constructor = DomScroll;
DomScroll.superclass = DomLayer.prototype;

/**
 * @access public
 */
DomScroll.prototype.updateScroll = function()
{
	var SCROLLCROP = Math.round( ( this.dragCT.y / ( this.h - this.dragCT.h ) ) * ( this.maxH - this.h ) );
	
	this.content.moveTo( null , -SCROLLCROP );
	this.content.setClip( [ SCROLLCROP, null, SCROLLCROP + this.h, null ] );
};

/**
 * @access public
 */
DomScroll.prototype.block = function( b )
{
	if ( b )
	{
		this.slider.setBgColor( this.mBgOff );
		this.dragCT.setVisible( false );
	}
	else
	{
		this.slider.setBgColor( this.mBgOn );
		this.dragCT.setVisible( true );
	}
};
