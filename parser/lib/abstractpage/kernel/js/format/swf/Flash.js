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
 * Flash Object (IE)
 * Wrapper for Flash Javascript interaction
 *
 * @package format_swf
 */

/**
 * Constructor
 *
 * @access public
 */
Flash = function( w, h, file, loop, autostart, menu, quality, div, id )
{
	this.Base = Base;
	this.Base();
	
	var swfID = id || "flash" + ( Flash.idcount++ );
	
	code  = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" id="' + swfID + '" width=' + w + ' height=' + h + '>\n';
	code += '<param name="movie"   value="' + ( file       || ""      ) + '">\n';
	code += '<param name="quality" value='  + ( quality    || "high"  ) + '>\n';
	code += '<param name="loop"    value='  + ( loop?      "true" : "false" ) + '>\n';
	code += '<param name="play"    value='  + ( autostart? "true" : "false" ) + '>\n';
	code += '<param name="menu"    value='  + ( menu?      "true" : "false" ) + '>\n';
	code += '</object>\n';

	this.current = 0;
	this.panMode = 0; // 0: pixel, 1: percent
	this.events  = new Object();
	
	// error callbacks
	this.events.onResize         = 
	this.events.onLoad           = 
	this.events.onBeginPlay      = 
	this.events.onPause          = 
	this.events.onBack           = 
	this.events.onForward        = 
	this.events.onRewind         = 
	this.events.onStop           = 
	this.events.onStopPlay       = 
	this.events.onZoom           = 
	this.events.onGotoFrame      =
	this.events.onPan            =
	this.events.onCallFrame      =
	this.events.onCallLabel      =
	this.events.onGotoFrame      =
	this.events.onGotoLabel      =
	this.events.onPropertyChange = function()
	{
		return false;
	}
	
	// the old fashioned way (´cause we get no access to core params via dom)
	if ( div != null && document.getElementById( div ) )
		document.all[div].innerHTML = code;
	else
		document.body.insertAdjacentHTML( 'beforeEnd', code );
		
	this.elm = document.getElementById( swfID );
};


Flash.prototype = new Base();
Flash.prototype.constructor = Flash;
Flash.superclass = Base.prototype;

/**
 * @access public
 */
Flash.prototype.setMovie = function( url )
{
	if ( url != null )
		this.elm.movie = url;
};

/**
 * @access public
 */
Flash.prototype.load = function( url, layer )
{
	if ( url != null )
	{
		this.elm.LoadMovie( layer || 0, url );
		this.events.onLoad();
	}
};

/**
 * @access public
 */
Flash.prototype.getCurrentFrame = function()
{
	return this.elm.CurrentFrame();
};

/**
 * @access public
 */
/*
Flash.prototype.getTotalFrames = function()
{
	return this.elm.TotalFrames();
};
*/

/**
 * @access public
 */
Flash.prototype.getVersion = function()
{
	return this.elm.FlashVersion();
};

/**
 * @access public
 */
Flash.prototype.getPercentLoaded = function()
{
	return this.elm.PercentLoaded();
};

/**
 * @access public
 */
Flash.prototype.isPlaying = function()
{
	return this.elm.IsPlaying();
};

/**
 * @access public
 */
/*
Flash.prototype.isFrameLoaded = function( frame )
{
	if ( frame != null )
		return this.elm.IsFrameLoaded( frame );
	else
		return false;
};
*/

/**
 * @access public
 */
Flash.prototype.isLoaded = function()
{
	return ( this.getPercentLoaded() == 100 )? true : false
};

/**
 * @access public
 */
Flash.prototype.start = function()
{
	this.rewind();
	this.zoomTo( 0 );
	this.elm.Play();
	this.events.onBeginPlay();
};

/**
 * @access public
 */
Flash.prototype.play = function()
{
	this.rewind();
	
	// subtract 1 because keyframes are zero based
	this.gotoFrame( this.current );
	
	this.elm.Play();
	this.events.onBeginPlay();
};

/**
 * @access public
 */
Flash.prototype.pause = function()
{
	this.current = this.getCurrentFrame();
	this.elm.Stop();
	this.events.onPause();
};

/**
 * @access public
 */
Flash.prototype.playFrom = function( frame )
{
	this.rewind();
		
	// subtract 1 because keyframes are zero based
	this.gotoFrame( frame - 1 || 0 );
		
	this.elm.Play();
	this.events.onBeginPlay();
};

/**
 * @access public
 */
Flash.prototype.back = function()
{
	this.elm.Back();
	this.events.onBack();
};

/**
 * @access public
 */
Flash.prototype.forward = function()
{
	this.elm.Forward();
	this.events.onForward();
};

/**
 * @access public
 */
Flash.prototype.rewind = function()
{
	this.elm.Rewind();
	this.events.onRewind();
};

/**
 * @access public
 */
Flash.prototype.stop = function()
{
	this.elm.Stop();
	this.current = 0;
	this.events.onStop();
};

/**
 * @access public
 */
Flash.prototype.stopPlay = function()
{
	this.elm.StopPlay();
	this.events.onStopPlay();
};

/**
 * @access public
 */
Flash.prototype.gotoFrame = function( frame )
{
	if ( frame != null /*&& this.isFrameLoaded( frame )*/ )
	{
		this.elm.GotoFrame( frame ) // subtract 1?
		this.events.onGotoFrame();
	}
};

/**
 * @access public
 */
Flash.prototype.zoomTo = function( arg )
{
	if ( arg != null )
	{
		this.elm.Zoom( parseInt( arg ) )
		this.events.onZoom();
	}
};

/**
 * @access public
 */
Flash.prototype.zoomRect = function( l, t, r, b )
{
	if ( arguments.length == 4 )
	{
		this.elm.SetZoomRect( l, t, r, b );
		this.events.onZoom();
	}
};

/**
 * @access public
 */
Flash.prototype.pan = function( x, y )
{
	// pixel
	if ( this.panMode == 0 )
	{
		if ( x == null || !Util.is_int( x ) || y == null || !Util.is_int( y ) )
			return false;
	}
	// percent
	else if ( this.panMode = 1 )
	{
		if ( x == null || !Util.is_percent( x ) || y == null || !Util.is_percent( y ) )
			return false;	
	}
	
	this.elm.Pan( parseInt( x || 0 ), parseInt( y || 0 ), this.panMode );
	this.events.onPan();
};

/**
 * @access public
 */
Flash.prototype.panX = function( x )
{
	if ( x!= null)
		this.pan( x, 0 );
};

/**
 * @access public
 */
Flash.prototype.panY = function( y )
{
	if ( y != null )
		this.pan( 0, y );
};

/**
 * @access public
 */
Flash.prototype.setPanModeToPixel = function()
{
	this.panMode = 0;
};

/**
 * @access public
 */
Flash.prototype.setPanModeToPercent = function()
{
	this.panMode = 1;
};

/**
 * @access public
 */
Flash.prototype.getVar = function( v )
{
	if ( v != null )
		this.elm.GetVariable( v );
};

/**
 * @access public
 */
Flash.prototype.setVar = function( v, val )
{
	if ( v != null && val != null )
		this.elm.SetVariable( v );
};

/**
 * @access public
 */
Flash.prototype.tPlay = function( target )
{
	if ( target != null )
	{
		this.elm.TPlay( target );
		this.events.onBeginPlay();
	}
};

/**
 * @access public
 */
Flash.prototype.tStopPlay = function( target )
{
	if ( target != null )
	{
		this.elm.TStopPlay( target );
		this.events.onStopPlay();
	}
};

/**
 * @access public
 */
Flash.prototype.tCallFrame = function( target, frame )
{
	if ( target != null && frame != null )
	{
		this.elm.TCallFrame( target, frame ); // subtract 1?
		this.events.onCallFrame();
	}
};

/**
 * @access public
 */
Flash.prototype.tCallLabel = function( target, label )
{
	if ( target != null && label != null )
	{
		this.elm.TCallLabel( target, label );
		this.events.onCallLabel();
	}
};

/**
 * @access public
 */
Flash.prototype.tGotoFrame = function( target, frame )
{
	if ( target != null && frame != null )
	{
		this.elm.TGotoFrame( target,frame ) // subtract 1?
		this.events.onGotoFrame();
	}
};

/**
 * @access public
 */
Flash.prototype.tGotoLabel = function( target, label )
{
	if ( target != null && label != null )
	{
		this.elm.TGotoLabel( target, label );
		this.events.onGotoLabel();
	}
};

/**
 * @access public
 */
Flash.prototype.tGetCurrentFrame = function( target )
{
	if ( target != null )
		return this.elm.TCurrentFrame( target );
};

/**
 * @access public
 */
Flash.prototype.tGetCurrentLabel = function( target )
{
	if ( target != null )
		return this.elm.TCurrentLabel( target );
};

/**
 * @access public
 */
Flash.prototype.tGetProperty = function( target )
{
	if ( target != null )
		return this.elm.TGetProperty( target );
};

/**
 * @access public
 */
Flash.prototype.tGetPropertyNumber = function( target )
{
	if ( target != null )
		return this.elm.TGetPropertyNum( target );
};

/**
 * @access public
 */
Flash.prototype.tSetProperty = function( target, property, value )
{
	if ( target != null && property != null && value != null )
	{
		this.elm.TSetProperty( target, property, value );
		this.events.onPropertyChange();
	}
};

/**
 * @access public
 */
Flash.prototype.tSetPropertyNumber = function( target, property, num )
{
	if ( target != null && property != null && num != null )
	{
		this.elm.TSetPropertyNum( target, property, num );
		this.events.onPropertyChange();
	}
};


/**
 * @access public
 * @static
 */
Flash.idcount = 0;
