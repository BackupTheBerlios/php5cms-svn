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
|Authors: David L. Blackledge                                          |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * @package image_xbm
 */
 
/**
 * Constructor
 *
 * @access public
 */
XBMImage = function( width, height, name ) 
{
	this.Base = Base;
	this.Base();
	
 	this.name   = name;
 	this.width  = width + ( ( width % 8 ) > 0? ( 8 - ( width % 8 ) ) : 0 ); // expand to a multiple of 8
 	this.height = height;
 	
	this.header = "#define " + name + "_width " + this.width + "\n" + "#define " + name + "_height " + this.height + "\n" + "static char " + name + "_bits[] = {\n";
 	this.footer = "};";
 
 	this.data = new Array( this.height );
 
 	for ( var i = 0 ; i < this.data.length ; ++i ) 
	{
  		this.data[i] = new Array( this.width );
  
  		for ( var j = 0 ; j < this.data[i].length ; ++j )
   			this.data[i][j] = 0;
 	}
 
 	// store copies of this.data;
 	this.frames = new Array();

 	this.xbm = this.getXBM();
};


XBMImage.prototype = new Base();
XBMImage.prototype.constructor = XBMImage;
XBMImage.superclass = Base.prototype;

/**
 * @access public
 */
XBMImage.prototype.body = function()
{
 	var bod = "";
 
 	for ( var i = 0; i < this.height; ++i )
	{
  		for ( var j = 0; j < this.width / 8; ++j ) 
		{
   			if ( typeof( this.data[i] ) != "undefined" && typeof( this.data[i][j] ) != "undefined" ) 
			{
    			// must be reversed to work right, apparently.
    			var bool = 0;
    			bool = this.data[i][j];
    			var hex = ( new Number( bool ) ).toString( 16 );
    
				if ( hex.length == 1 )
     				hex = "0" + hex;
    
				bod += "0x" + hex + ",";
   			} 
			else 
			{
    			bod += "0x00,";
   			}
  		}
 	}
 
 	if ( bod.length > 0 ) // remove trailing comma
  		bod = bod.substring( 0, bod.length - 1 );
 
 	return bod;
};

/**
 * @access public
 */
XBMImage.prototype.draw = function( x, y ) 
{
 	if ( !( x > -1 && x < this.width && y > -1 && y < this.height ) )
  		return;
 
 	if ( typeof( this.data[y] ) == "undefined" )
   		this.data[y] = new Array();
 
 	var bit = x % 8;
 	var byt = ( x - bit ) / 8;
 
 	if ( typeof( this.data[y][byt] ) == "undefined" )
   		this.data[y][byt] = 0;
 
 	this.data[y][byt] |= ( 0x01 << bit );
};

/**
 * @access public
 */
XBMImage.prototype.clearImage = function()
{
 	for ( var i = 0 ; i < this.data.length ; ++i ) 
	{
  		if ( typeof( this.data[i] ) != "undefined" )
		{
   			for ( var j = 0; j < this.data[i].length; ++j )
    			this.data[i][j] = 0;
		}
 	}
};

/**
 * @access public
 */
XBMImage.prototype.saveFrame = function( idx ) 
{
 	this.frames[idx] = new Array( this.data.length );
 
 	for ( var i = 0; i < this.data.length; ++i ) 
	{
  		if ( typeof( this.data[i] ) != "undefined" )
   			this.frames[idx][i] = XBMImage.arrayCopy( this.data[i] );
 	}
};

/**
 * @access public
 */
XBMImage.prototype.showFrame = function( idx ) 
{
 	for ( var i = 0; i < this.frames[idx].length; ++i ) 
	{
  		if ( typeof( this.frames[idx][i] ) != "undefined" ) 
			this.data[i] = XBMImage.arrayCopy( this.frames[idx][i] );
  		else
   			this.data[i] = null;
 	}
};

/**
 * @access public
 */
XBMImage.prototype.drawCircleCoords = function( x1, y1, x2, y2 ) 
{
 	var x = Math.floor( ( x1 + x2 ) / 2 );
 	var y = Math.floor( ( y1 + y2 ) / 2 );
 	var r = Math.floor( Math.min( Math.abs( x1 - x2 ), Math.abs( y1 - y2 ) ) / 2 );
 
 	this.drawCircle( x, y, r );
};

/**
 * @access public
 */
XBMImage.prototype.drawCircle = function( x, y, r ) 
{
 	var previx = null;
 	var previy = null;
 
 	for ( var a = 0; a <= Math.PI / 2; a += .01 ) 
	{
  		var ix = Math.floor( r * Math.cos( a ) );
  
  		if ( ix != previx ) 
		{
   			var iy = Math.floor( r * Math.sin( a ) );
   
   			// draw the quarter circle on every quadrant, and flip them and draw again
   			// so we get equal coverage for vertical and horizontal sections
   			if ( previy != null ) 
			{
    			var xa1 = x + ix;
    			var xb1 = x - ix;
    			var ya1 = y + iy;
    			var yb1 = y - iy;
    			var xa2 = y + ix;
    			var xb2 = y - ix;
    			var ya2 = x + iy;
    			var yb2 = x - iy;
    
				this.draw( xa1, ya1 );
    			this.draw( ya2, xa2 );
    			this.draw( xa1, yb1 );
    			this.draw( ya2, xb2 );
    			this.draw( xb1, ya1 );
    			this.draw( yb2, xa2 );
    			this.draw( xb1, yb1 );
    			this.draw( yb2, xb2 );
   			}
   
   			previx = ix;
   			previy = iy;
  		}
 	}
};

/**
 * Attempt to do a fast horizontal line algorithm.
 *
 * @access public
 */
XBMImage.prototype.drawHLine = function( x1, y1, x2 ) 
{
 	if ( !( y1 > -1 && y1 < this.height ) )
  		return;
 
 	if ( x1 > x2 )
	{
  		var xs = x1;
		x1 = Math.max( 0, x2 );
		x2 = Math.min( this.width, xs );
 	}
 
 	var filled    = 0xFF;
 	var startbits = x1 % 8;
 	var startbyt  = ( x1 - x1 % 8 ) / 8;
 	var endbits   = 8 - x2 % 8;
 	var endbyt    = ( x2 - x2 % 8 ) / 8;
 
 	if ( startbyt == endbyt ) 
	{
  		this.data[y1][startbyt] |= ( filled << startbits ) & ( filled >> endbits );
  		return;
 	}
 
 	for ( var i = startbyt + 1; i < endbyt; ++i )
  		this.data[y1][i] = filled;
 
 	for ( var j = x1; j < ( x1 + ( 8 - x1 % 8 ) ); ++j )
  		this.draw( j, y1 );

 	this.data[y1][endbyt] |= ( filled >>endbits );
};

/**
 * @access public
 */
XBMImage.prototype.drawVLine = function( x1, y1, y2 ) 
{
 	if ( !( x1 > -1 && x1 < this.width ) )
  		return;
 
 	if ( y1 > y2 )
	{
  		var ys = y1;
		y1 = Math.max( 0, y2 );
		y2 = Math.min( this.height, ys );
 	}
 
 	var bit = x1 % 8;
 	var byt = ( x1 - bit ) / 8;
 	var bitmask = ( 0x01 << bit );
 
 	for ( var y = y1; y <= y2; ++y )
  		this.data[y][byt] |= bitmask;
};

/**
 * @access public
 */
XBMImage.prototype.drawLine = function( x1, y1, x2, y2 ) 
{
 	if ( x1 > x2 ) 
	{
  		var xx = x1; 
		x1 = x2; 
		x2 = xx;
  
  		var yy = y1; 
		y1 = y2; 
		y2 = yy;
 	}
 
 	var y = y1;
 
 	if ( y1 == y2 )
	{
   		if ( x1 == x2 )
	 		return this.draw( x1, y1 );
   		else
	 		return this.drawHLine( x1, y1, x2 );
	}
 
 	if ( x1 == x2 ) 
		return this.drawVLine( x1, y1, y2 );
 
 	var slope = ( y1 - y2 ) / ( x1 - x2 );
 	var yint  = y1 - Math.floor( slope * x1 ); // y-intercept
 
 	for ( var x = x1; x < x2; ++x ) 
	{
		// y1 < y2 (top to bottom)
  		if ( slope > 0 ) 
		{
   			for ( y = Math.floor( slope * x ) + yint; y < ( Math.floor( slope * ( x + 1 ) ) + yint ); ++y )
    			this.draw( x, y );
   
   			if ( Math.floor( slope * x ) == Math.floor( slope * ( x + 1 ) ) )
    			this.draw( x, y );
   
   			if ( x == x2 - 1 ) 
			{
    			for ( y; y <= y2; ++y )
     				this.draw( x, y );
   			}
  		}
		// y1 > y2 (bottom to top) 
		else 
		{
   			for ( y = Math.floor( slope * x ) + yint; y > ( Math.floor( slope * ( x + 1 ) ) + yint ); --y )
    			this.draw( x, y );
   
   			if ( Math.floor( slope * x ) == Math.floor( slope * ( x + 1 ) ) )
    			this.draw( x, y );
   
   			if ( x == x2 - 1 ) 
			{
    			for (y; y >= y2; --y )
     				this.draw( x, y );
   			}
  		}
 	}
 
 	return null;
};

/**
 * @access public
 */
XBMImage.prototype.drawBox = function( x1, y1, x2, y2 ) 
{
 	this.drawHLine( x1, y1, x2 );
 	this.drawLine( x2, y1, x2, y2 );
 	this.drawHLine( x2, y2, x1 );
 	this.drawLine( x1, y2, x1, y1 );
};

/**
 * @access public
 */
XBMImage.prototype.drawBoxFilled = function( x1,y1,x2,y2 ) 
{
 	for ( var y = y1; y <= y2; ++y )
  		this.drawHLine( x1, y, x2 );
};

/**
 * @access public
 */
XBMImage.prototype.getXBM = function()
{
 	return this.header + this.body() + this.footer;
};

/**
 * @access public
 */
XBMImage.prototype.setXBM = function( str )
{
 	var xbmdata = str.substring( str.indexOf( "{" ) + 1, str.lastIndexOf( "}" ) );
 	var a_data  = xbmdata.split( "," );
 
 	for ( var j = 0; j < this.height; ++j )
	{
  		this.data[j] = new Array();
  
  		for ( var i = 0; i < Math.floor( this.width / 8 ); ++i ) 
		{
   			var a_idx = i + j * ( Math.floor( this.width / 8 ) );

   			if ( a_idx < a_data.length )
    			this.data[j][i] = ( new Number( parseInt( a_data[a_idx], 16 ) ) ).valueOf();// parseInt(a_data[a_idx]);
  		}
 	}
};


/**
 * @access public
 * @static
 */
XBMImage.arrayCopy = function( o_array ) 
{
 	var ret_array = new Array();
 
 	if ( typeof( ret_array.concat ) == "function" )
  		return ret_array.concat( o_array );
 
 	for ( var j = 0 ; j < o_array.length ; ++j )
  		ret_array[ret_array.length] = o_array[j];
 
 	return ret_array;
};
