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
 * @package image_graph_lib 
 */
 
/**
 * Constructor
 *
 * @access public
 */
APVis = function( width, height )
{
	this.Base = Base;
	this.Base();
		
	this.charttype = "";
	
	this.images = new Dictionary();
	this.text   = new Dictionary();
	this.params = new Dictionary();
	
	this.setWidth( width );
	this.setHeight( height );
	
	// set defaults
	this.setArchive( "APVis.jar" );
	this.set3DMode( true );
	this.setOutlineMode( true );
	this.set3DDepth( 15 );
	this.setDecimalPlaces( 0 );
	this.setLabelOrientation( 1 );
};


APVis.prototype = new Base();
APVis.prototype.constructor = APVis;
APVis.superclass = Base.prototype;

/**
 * @access public
 */
APVis.prototype.setWidth = function( width )
{
	this.width = width || APVis.defaultWidth;	
};

/**
 * @access public
 */
APVis.prototype.getWidth = function()
{
	return this.width;
};

/**
 * @access public
 */
APVis.prototype.setHeight = function( height )
{
	this.height = height || APVis.defaultHeight;	
};

/**
 * @access public
 */
APVis.prototype.getHeight = function()
{
	return this.height;
};

/**
 * @access public
 */
APVis.prototype.setArchive = function( archive )
{
	if ( archive != null )
		this.archive = archive;	
};

/**
 * @access public
 */
APVis.prototype.getArchive = function()
{
	return this.archive;
};

/**
 * @access public
 */
APVis.prototype.setChartType = function( name )
{
	if ( name != null )
	{
		this.charttype = name;
		return this.getChartType();
	}
	
	return false;
};

/**
 * @access public
 */
APVis.prototype.getChartType = function()
{
	return this.charttype;
};

/**
 * @access public
 */
APVis.prototype.setLoadingMessage = function( msg )
{
	this._param( "LOADINGMESSAGE", msg );
};

/**
 * @access public
 */
APVis.prototype.getLoadingMessage = function()
{
	return this.params.get( "LOADINGMESSAGE" );
};

/**
 * @access public
 */
APVis.prototype.setMessageColor = function( col )
{
	this._addColorData( "STEXTCOLOR", col );
};

/**
 * @access public
 */
APVis.prototype.getMessageColor = function()
{
	return this.params.get( "STEXTCOLOR" );
};

/**
 * @access public
 */
APVis.prototype.setBackgroundColor = function( col )
{
	this._addColorData( "STARTUPCOLOR", col );
};

/**
 * @access public
 */
APVis.prototype.getBackgroundColor = function()
{
	return this.params.get( "STARTUPCOLOR" );
};

/**
 * @access public
 */
APVis.prototype.setTargetFrame = function( frame )
{
	this.targetFrame = frame;
};

/**
 * @access public
 */
APVis.prototype.getTargetFrame = function()
{
	return this.targetFrame || APVis.defaultTargetFrame;
};

/**
 * @access public
 */
APVis.prototype.set3DMode = function( bool )
{
	this._param( "3D", bool );
};

/**
 * @access public
 */
APVis.prototype.get3DMode = function()
{
	return this.params.get( "3D" );
};

/**
 * @access public
 */
APVis.prototype.setOutlineMode = function( bool )
{
	this._param( "outline", bool );
};

/**
 * @access public
 */
APVis.prototype.getOutlineMode = function()
{
	return this.params.get( "outline" );
};

/**
 * @access public
 */
APVis.prototype.set3DDepth = function( depth )
{
	this._param( "depth3D", depth );
};

/**
 * @access public
 */
APVis.prototype.get3DDepth = function()
{
	return this.params.get( "depth3D" );
};

/**
 * @access public
 */
APVis.prototype.setDecimalPlaces = function( p )
{
	this._param( "ndecplaces", p );
};

/**
 * @access public
 */
APVis.prototype.getDecimalPlaces = function()
{
	return this.params.get( "ndecplaces" );
};

/**
 * @access public
 */
APVis.prototype.setLabelOrientation = function( orientation )
{
	this._param( "labelsOrientation", orientation );
};

/**
 * @access public
 */
APVis.prototype.getLabelOrientation = function()
{
	return this.params.get( "labelsOrientation" );
};

/**
 * @access public
 */
APVis.prototype.setLabelColor = function( col )
{
	this._addColorData( "color18", col );
};

/**
 * @access public
 */
APVis.prototype.getLabelColor = function()
{
	return this.params.get( "color18" );
};

/**
 * Provision is made to display 10 images, the first five are drawn behind
 * the chart and the last five are drawn over the chart.
 *
 * @access public
 */
APVis.prototype.addImage = function( file, x, y )
{
	if (  ( file == null ) || ( x == null ) || ( y == null ) || ( APVis.imageCount == APVis.maxImage ) )
		return false;
		
	this.images.add(
		"image" + ( ++APVis.imageCount),
		file + "," + x + "," + y
	);
	
	return true;
};

/**
 * Provision is made to display 10 lines of text.
 *
 * @access public
 */
APVis.prototype.addText = function( text, x, y, font, fstyle, fsize, col )
{
	if (  ( text == null ) || ( x == null ) || ( y == null ) || ( APVis.textCount == APVis.maxText ) )
		return false;
	
	this._addTextData( this.text, "text" + ( ++APVis.textCount), text, x, y, font, fstyle, fsize, col );
	return true;
};

/**
 * @access public
 */
APVis.prototype.setChartTitle = function( text, x, y, font, fstyle, fsize, col )
{
	if (  ( text == null ) || ( x == null ) || ( y == null ) )
		return false;
		
	this._addTextData( this.params, "title", text, x, y, font, fstyle, fsize, col );
	return true;
};

/**
 * @access public
 */
APVis.prototype.getAppletHTML = function()
{
	var keys,i;
	var str = '<applet code="' + this.getChartType() +
		'" archive="' + this.getArchive() +
		'" width="'   + this.getWidth()   +
		'" height="'  + this.getHeight()  + '">\n';
	
	// let charts build their stuff
	this._buildSpecific();
	
	// core params
	if ( !this.params.isEmpty() )
	{
		keys = this.params.getKeys();
		
		for ( i in keys )
			str += '<param name="' + keys[i] + '" value="' + this.params.get( keys[i] ) + '">\n';
	}
	
	// images
	if ( !this.images.isEmpty() )
	{
		keys = this.images.getKeys();
		
		for ( i in keys )
			str += '<param name="' + keys[i] + '" value="' + this.images.get( keys[i] ) + '">\n';
	}

	// text
	if ( !this.text.isEmpty() )
	{
		keys = this.text.getKeys();
		
		for ( i in keys )
			str += '<param name="' + keys[i] + '" value="' + this.text.get( keys[i] ) + '">\n';
	}
	
	str += '</applet>';
	return str;
};


// private methods

/**
 * @access private
 */
APVis.prototype._param = function( name, value )
{
	if ( ( name == null ) || ( value == null ) )
		return false;
		
	if ( this.params.contains( name ) )
		this.params.set( name, value );
	else
		this.params.add( name, value );
};

/**
 * @access private
 */
APVis.prototype._addTextData = function( obj, paramname, text, x, y, font, fstyle, fsize, col )
{
	var col = ColorUtil.hexToRGB( col || APVis.defaultFontColor );
	
	obj.add(
		paramname,
		text + "," +
		x    + "," +
		y    + "," +
		( font   || APVis.defaultFontType  ) + "," +
		( fstyle || APVis.defaultFontStyle ) + "," +
		( fsize  || APVis.defaultFontSize  ) + "," +
		col[0] + "," +	// red
		col[1] + "," +	// green
		col[2]			// blue
	);
};

/**
 * @access private
 */
APVis.prototype._addColorData = function( paramname, col )
{
	var col = ColorUtil.hexToRGB( col || APVis.defaultFontColor );
	
	this.params.add(
		paramname,
		col[0] + "," +	// red
		col[1] + "," +	// green
		col[2]			// blue
	);
};

/**
 * @access private
 */
APVis.prototype._addFontData = function( paramname, font, fstyle, fsize )
{
	this.params.add(
		paramname,
		( font   || APVis.defaultFontType  ) + "," +
		( fstyle || APVis.defaultFontStyle ) + "," +
		( fsize  || APVis.defaultFontSize  )
	);
};

/**
 * @access private
 */
APVis.prototype._addSeriesData = function( paramname, value, col, url, frame )
{
	var col = ColorUtil.hexToRGB( col );
	
	this.params.add(
		paramname,
		value  + "," +
		col[0] + "," +	// red
		col[1] + "," +	// green
		col[2] + "," +	// blue
		url    + "," + 
		this.getTargetFrame()
	);
};

/**
 * @access private
 */
APVis.prototype._buildSpecific = function()
{
	// overload
	return null;
};


/**
 * @access public
 * @static
 */
APVis.defaultWidth = 500;

/**
 * @access public
 * @static
 */
APVis.defaultHeight = 300;

/**
 * @access public
 * @static
 */
APVis.imageCount = 0;

/**
 * @access public
 * @static
 */
APVis.maxImage = 10;

/**
 * @access public
 * @static
 */
APVis.textCount = 0;

/**
 * @access public
 * @static
 */
APVis.maxText = 10;

/**
 * @access public
 * @static
 */
APVis.defaultFontSize = 12;

/**
 * @access public
 * @static
 */
APVis.defaultFontType = "Arial";

/**
 * @access public
 * @static
 */
APVis.defaultFontStyle = "N";

/**
 * @access public
 * @static
 */
APVis.defaultFontColor = "#808080";

/**
 * @access public
 * @static
 */
APVis.defaultTargetFrame = "_self";

/**
 * @access public
 * @static
 */
APVis.defaultURL = " ";

/**
 * @access public
 * @static
 */
APVis.defaultLabel = " ";
