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
 * @package image_vml_3d_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
LightsOutCubeModel = function()
{
	this.Base = Base;
	this.Base();
	
	this.LEFT  = 0;
	this.RIGHT = 2;
	this.UP    = 1;
	this.DOWN  = 3;
	
	this.offsets = new Array();

	this.offsets[0]  = new Array(  0,  0,  0,  0 );
	this.offsets[1]  = new Array( 52, 48,  1,  3 );
	this.offsets[2]  = new Array( 59, 44,  1,  3 );
	this.offsets[3]  = new Array( 59, 40, 20,  3 );

	this.offsets[4]  = new Array( 52, 57,  1,  3 );
	this.offsets[5]  = new Array( 59, 57,  1,  3 );
	this.offsets[6]  = new Array( 59, 57, 16,  3 );

	this.offsets[7]  = new Array( 52, 57,  1,  4 );
	this.offsets[8]  = new Array( 59, 57,  1,  4 );
	this.offsets[9]  = new Array( 59, 57, 12,  4 );

	this.offsets[10] = new Array(  0,  0,  0,  0 );
	this.offsets[11] = new Array( 48, 56,  1,  3 );
	this.offsets[12] = new Array( 59, 56,  1,  3 );
	this.offsets[13] = new Array( 59, 56,  8,  3 );

	this.offsets[14] = new Array( 44, 57,  1,  3 );
	this.offsets[15] = new Array( 59, 57,  1,  3 );
	this.offsets[16] = new Array( 59, 57,  8,  3 );

	this.offsets[17] = new Array( 40, 57,  1, 20 );
	this.offsets[18] = new Array( 59, 57,  1, 16 );
	this.offsets[19] = new Array( 59, 57,  8, 12 );

	this._tog1l = new Array();
	this._tog1h = new Array();

	this._tog5l = new Array();
	this._tog5h = new Array();
};


LightsOutCubeModel.prototype = new Base();
LightsOutCubeModel.prototype.constructor = LightsOutCubeModel;
LightsOutCubeModel.superclass = Base.prototype;

/**
 * @access public
 */
LightsOutCubeModel.prototype.adjacent = function( i, j )
{
	return ( i + this.offsets[i % 20][j] ) % 60;
};

/**
 * @access public
 */
LightsOutCubeModel.prototype.init = function()
{
	var i;
	
	var m = 2;
	for ( i = 1; i < 30; ++i, m*=2 )
	{
		if ( ( i % 10 ) != 0 )
		{
			this._tog1l[i] = m;
			this._tog1h[i] = 0;
			this._tog5l[i] = 0;
			this._tog5h[i] = 0;
		}
	}
	
	var m = 2;
	for ( i = 1; i < 30; ++i, m*=2 )
	{
		if ( ( i % 10 ) != 0 )
		{
			this._tog1h[i+30] = m;
			this._tog1l[i+30] = 0;
			this._tog5l[i+30] = 0;
			this._tog5h[i+30] = 0;
		}
	}
	
	for ( i = 1; i < 60; ++i )
	{
		if ( ( i % 10 ) != 0 )
		{
			this.togButton( i, i );
			this.togButton( i, this.adjacent( i, this.LEFT  ) );
			this.togButton( i, this.adjacent( i, this.UP    ) );
			this.togButton( i, this.adjacent( i, this.DOWN  ) );
			this.togButton( i, this.adjacent( i, this.RIGHT ) );	    	    		
		}
	}
};

/**
 * @access public
 */
LightsOutCubeModel.prototype.togButton = function( i, but )
{
	if ( but > 30 )
		this._tog5h[i]|=this._tog1h[but];
	else
		this._tog5l[i]|=this._tog1l[but];	
};

/**
 * @access public
 */
LightsOutCubeModel.prototype.find = function( loc )
{
	var button = 0;
	var mask   = 0;
	
	if ( loc.high > 0 )
	{
		button += 32;
		mask = loc.high;
	}
	else
	{
		mask = loc.low;
	}
	
	if ( mask > 0x0000ffff )
	{
		button += 16;
		mask>>=16;
	}
	
	if ( mask > 0x00ff )
	{
		button += 8;
		mask>>=8;
	}
    
	if ( mask > 0x0f )
	{
		button += 4;
		mask>>=4;
	}
	
	if ( mask > 0x3 )
	{
		button += 2;
		mask>>=2;
	}
    
	if ( mask > 0x1 )
		button += 1;
		
	if ( button > 30 )
		button -= 2;
		
	return button;
};

/**
 * @access public
 */
LightsOutCubeModel.prototype.tog1 = function( index, loc )
{
	loc.low  ^= this._tog1l[index];
	loc.high ^= this._tog1h[index];
};

/**
 * @access public
 */
LightsOutCubeModel.prototype.tog5 = function( index, loc )
{
	loc.low  ^= this._tog5l[index];
	loc.high ^= this._tog5h[index];
};

/**
 * @access public
 */
LightsOutCubeModel.prototype.dump = function( loc )
{
	var i;
	var solBuf = "";
	var l = new LightsOutCube();
	
    for ( i = 1; i < 60; i++ )
	{
		if ( ( i % 10 ) != 0 )
		{
			l.low  = 0;
			l.high = 0;
			this.tog1( i, l );
			
			if ( ( l.low & loc.low ) != 0 | ( l.high & loc.high ) != 0 )
		    	solBuf += i + ",";
		}
	}
	
	return solBuf;
};

/**
 * @access public
 */
LightsOutCubeModel.prototype.dumpArray = function( loc )
{
	var i;
	var solArray = new Array();
	var l = new LightsOutCube();
	var a = 0;
	
	for ( i = 1; i < 60; i++ )
	{
		if ( ( i % 10 ) != 0 )
		{
			l.low  = 0;
			l.high = 0;
			this.tog1( i, l );
			
			if ( ( l.low & loc.low ) != 0 | ( l.high & loc.high ) != 0 )
			{
			   	solArray[a] = i;
				a++;
			}
		}
	}
	
	return solArray;
};
