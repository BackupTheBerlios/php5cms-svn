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
 * @package dhtml_3d_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
ThreeDHelper = function()
{
	this.Base = Base;
	this.Base();
};


ThreeDHelper.prototype = new Base();
ThreeDHelper.prototype.constructor = ThreeDHelper;
ThreeDHelper.superclass = Base.prototype;

/* the default offset values - where the origin O(0, 0, 0) lies. */
ThreeDHelper.OFFSET_X_3DHTML = 300;
ThreeDHelper.OFFSET_Y_3DHTML = 300;
ThreeDHelper.OFFSET_Z_3DHTML = 0;


/**
 * This factory function creates and returns a new instance of the material
 * ColorRectMaterial.
 * 
 * The Builder pattern is used here: Within 3dhtml, models use the Material class.
 * To obtain a concrete instance of a material, you can call new Material() and
 * define your own material attributes.
 * To use more complex materials such as ColorRectMaterial, you don't instantiate
 * the object yourself, instead, ask createColorRectMaterial to create a new one
 * for you - it will define and assign the HTML body and the refresh method.
 *
 * @param  Color  colorFrom    Color to blend from
 * @param  Color  colorTo      Color to blend to
 * @param  String spacerImage  Path + filename of spacer image
 * @return Material
 * @access public
 * @static
 */
ThreeDHelper.createColorRectMaterial = function( colorFrom, colorTo, spacerImage )
{
	// in Netscape the size cannot be changed dynamically, therefore we
	// use a plain 10x10px image. For the other browsers, the size
	// is 1x1px (defining the minimum size of the colored box).
	var matbody = Browser.ns4?
		'<img src="' + spacerImage + '" width="10" height="10" alt="" border="0">' :
		'<img src="' + spacerImage + '" width="1" height="1" alt="" border="0">';
	
	var m = new Material( ( spacerImage? matbody : '' ), ThreeDHelper.colorRectMaterialRefresh );
	
	m.myColorBlend = new ColorBlend( colorFrom, colorTo );
	return m;
};

/**
 * Blends the color of a single Point3D depending on the point's z-value,
 * its depth. This refresh method is called whenever the point is drawn.
 *
 * @param  Point3D  p  The point to refresh
 * @access public
 * @static
 */
ThreeDHelper.colorRectMaterialRefresh = function( p )
{
	with ( p.lyr.ref )
	{
		var col = this.myColorBlend.getColor( ThreeDHelper.normalize( p.z, -100, 100, 0, 1 ) ).getHex();
		backgroundColor = col;
		bgColor = col;
		height = width = ThreeDHelper.normalize( p.z, -100, 100, 1, 20 );
	}
};

/**
 * This factory function creates and returns a new instance of the material
 * ColorRectMaterial. See createClipRectMaterial above for more information.
 *
 * @param  string image        Path + filename of image to use as ClipButton
 * @param  int    imageWidth   The width of the image
 * @param  int    imageHeight  The height of the image
 * @param  int    clipWidth    The width of the clipping area
 * @param  int    clipHeight   The height of the clipping area
 * @param  int    maxState     The number of states
 * @return Material
 * @access public
 * @static
 */
ThreeDHelper.createClipButtonMaterial = function( image, imageWidth, imageHeight, clipWidth, clipHeight, maxState )
{
	var m = new Material( '<img src="' + image + '" width="' + imageWidth + '" height="' + imageHeight + '" alt="" border="0">', ThreeDHelper.clipButtonRefresh );
	m.clipWidth  = clipWidth;
	m.clipHeight = clipHeight;
	m.maxState   = maxState;
	
	return m;
};

/**
 * Changes the state of the ClipButton of a single Point3D.
 * This refresh method is called whenever the point is drawn.
 * 
 * @param  Point3D  p  The point to refresh
 * @access public
 * @static
 */
ThreeDHelper.clipButtonRefresh = function( p )
{	
	if ( p.clipButton == null )
	{
		// creates ClipButton used with the current Point3D
		p.clipButton = new ClipButton( p.lyr.lyrname, this.clipWidth, this.clipHeight, this.maxState );
	}
	else
	{
		// updates the ClipButton's position values (it's placed in the point layer which is moved 
		// across the screen when positioning the point), so that the setState method is able
		// to set the state-dependent clipping area and reposition the ClipButton layer.
		p.clipButton.x = p.clipButton.lyr.getPos( "left" );
		p.clipButton.y = p.clipButton.lyr.getPos( "top"  );
	}
	
	// shows the right image to simulate 3D depth
	// by making the state of the clipButton depend on the z value (range between 0 and (this.maxState - 1) )
	p.clipButton.setState( Math.abs( ( this.maxState - 1 ) - Math.round( ThreeDHelper.normalize( p.z, -100, 100, 0, ( this.maxState - 1 ) ) ) ) );
};

/**
 * Normalizes a value to project it to a given range.
 * Example: v=5, source=[1, 10], destination=[1, 5]
 *          result = 2.22 
 *
 * @param  float  v     The value to project (within source range)
 * @param  float  sMin  The min value of the source range
 * @param  float  sMax  The max value of the source range
 * @param  float  dMin  The min value of the destination range
 * @param  float  dMax  The max value of the destination range
 * @return float
 * @access public
 * @static
 */
ThreeDHelper.normalize = function( v, sMin, sMax, dMin, dMax )
{
	return Math.min( Math.max( ( ( dMax - dMin ) / ( sMax - sMin ) ) * ( ( v - sMin ) + dMin ), dMin ), dMax );
};
