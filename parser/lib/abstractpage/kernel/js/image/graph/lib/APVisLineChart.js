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
APVisLineChart = function( width, height )
{
	this.APVis = APVis;
	this.APVis( width, height );

	this.setChartType( "APVisLine" );
	
	this.series = null;
};


APVisLineChart.prototype = new APVis();
APVisLineChart.prototype.constructor = APVisLineChart;
APVisLineChart.superclass = APVis.prototype;

/**
 * @access public
 */
APVisLineChart.prototype.addSeries = function( seriesObj )
{
	if ( ( seriesObj != null ) && ( typeof( seriesObj ) == "object" ) )
	{
		this.series = seriesObj;
		return true;
	}
	
	return false;
};

/**
 * @access public
 */
APVisLineChart.prototype.setXTitle = function( text, x, y, font, fstyle, fsize, col )
{
	if (  ( text == null ) || ( x == null ) || ( y == null ) )
		return false;
		
	this._addTextData( this.params, "xtitle", text, x, y, font, fstyle, fsize, col );
	return true;
};

/**
 * @access public
 */
APVisLineChart.prototype.setYTitle = function( text, x, y, font, fstyle, fsize, col )
{
	if (  ( text == null ) || ( x == null ) || ( y == null ) )
		return false;
		
	this._addTextData( this.params, "ytitle", text, x, y, font, fstyle, fsize, col );
	return true;
};

/**
 * @access public
 */
APVisLineChart.prototype.setGridMode = function( bool )
{
	this._param( "grid", bool );
};

/**
 * @access public
 */
APVisLineChart.prototype.getGridMode = function()
{
	return this.params.get( "grid" );
};

/**
 * @access public
 */
APVisLineChart.prototype.setAxisMode = function( bool )
{
	this._param( "axis", bool );
};

/**
 * @access public
 */
APVisLineChart.prototype.getAxisMode = function()
{
	return this.params.get( "axis" );
};

/**
 * @access public
 */
APVisLineChart.prototype.setYLabelsMode = function( bool )
{
	this._param( "ylabels", bool );
};

/**
 * @access public
 */
APVisLineChart.prototype.getYLabelsMode = function()
{
	return this.params.get( "ylabels" );
};

/**
 * @access public
 */
APVisLineChart.prototype.setVSpace = function( space )
{
	this._param( "vSpace", space );
};

/**
 * @access public
 */
APVisLineChart.prototype.getVSpace = function()
{
	return this.params.get( "vSpace" );
};

/**
 * @access public
 */
APVisLineChart.prototype.setHSpace = function( space )
{
	this._param( "hSpace", space );
};

/**
 * @access public
 */
APVisLineChart.prototype.getHSpace = function()
{
	return this.params.get( "hSpace" );
};

/**
 * @access public
 */
APVisLineChart.prototype.setGridX = function( x )
{
	this._param( "gridxpos", x );
};

/**
 * @access public
 */
APVisLineChart.prototype.getGridX = function()
{
	return this.params.get( "gridxpos" );
};

/**
 * @access public
 */
APVisLineChart.prototype.setGridY = function( y )
{
	this._param( "gridypos", y );
};

/**
 * @access public
 */
APVisLineChart.prototype.getGridY = function()
{
	return this.params.get( "gridxpos" );
};

/**
 * @access public
 */
APVisLineChart.prototype.setYPosOfXAxisLabels = function( y )
{
	this._param( "labelsY", y );
};

/**
 * @access public
 */
APVisLineChart.prototype.getYPosOfXAxisLabels = function()
{
	return this.params.get( "labelsY" );
};

/**
 * @access public
 */
APVisLineChart.prototype.setChartScale = function( scale )
{
	this._param( "chartScale", scale );
};

/**
 * @access public
 */
APVisLineChart.prototype.getChartScale = function()
{
	return this.params.get( "chartScale" );
};

/**
 * @access public
 */
APVisLineChart.prototype.setChartY = function( y )
{
	this._param( "chartStartY", y );
};

/**
 * @access public
 */
APVisLineChart.prototype.getChartY = function()
{
	return this.params.get( "chartStartY" );
};

/**
 * @access public
 */
APVisLineChart.prototype.setYLabelsFont = function( font, fstyle, fsize )
{
	this._param( "font14", font, fstyle, fsize );
};

/**
 * @access public
 */
APVisLineChart.prototype.getYLabelsFont = function()
{
	return this.params.get( "font14" );
};

/**
 * @access public
 */
APVisLineChart.prototype.setXLabelsFont = function( font, fstyle, fsize )
{
	this._param( "font15", font, fstyle, fsize );
};

/**
 * @access public
 */
APVisLineChart.prototype.getXLabelsFont = function()
{
	return this.params.get( "font15" );
};

/**
 * @access public
 */
APVisLineChart.prototype.setGridColor = function( col )
{
	this._addColorData( "color14", col );
};

/**
 * @access public
 */
APVisLineChart.prototype.getGridColor = function()
{
	return this.params.get( "color14" );
};

/**
 * @access public
 */
APVisLineChart.prototype.setAxisColor = function( col )
{
	this._addColorData( "color15", col );
};

/**
 * @access public
 */
APVisLineChart.prototype.getAxisColor = function()
{
	return this.params.get( "color15" );
};

/**
 * @access public
 */
APVisLineChart.prototype.setFloorColor = function( col )
{
	this._addColorData( "color16", col );
};

/**
 * @access public
 */
APVisLineChart.prototype.getFloorColor = function()
{
	return this.params.get( "color16" );
};

/**
 * @access public
 */
APVisLineChart.prototype.setLineOutlineColor = function( col )
{
	this._addColorData( "color17", col );
};

/**
 * @access public
 */
APVisLineChart.prototype.getLineOutlineColor = function()
{
	return this.params.get( "color17" );
};

/**
 * @access public
 */
APVisLineChart.prototype.setYLabelsColor = function( col )
{
	this._addColorData( "color19", col );
};

/**
 * @access public
 */
APVisLineChart.prototype.getYLabelsColor = function()
{
	return this.params.get( "color19" );
};

/**
 * @access public
 */
APVisLineChart.prototype.setGridRows = function( rows )
{
	this._param( "nRows", rows );
};

/**
 * @access public
 */
APVisLineChart.prototype.getGridRows = function()
{
	return this.params.get( "nRows" );
};


// private methods

/**
 * @access private
 */
APVisLineChart.prototype._buildSpecific = function()
{
	// no data added
	if ( this.series == null )
		return false;
	
	var i, j, col;
	var colNum = this.series.getColumnCount(); // what means Points in this case
	var serNum = this.series.getSeriesCount(); // silently assuming that all series are of equal length
	
	this._param( "nPoints", colNum );
	this._param( "nSeries", serNum );
	
	for ( i = 1; i < serNum + 1; i++ )
	{
		for ( j = 1; j < colNum + 1; j++ )
		{
			this._param(
				"point" + j + "series" + i,
				this.series.values[i-1][j-1] // point values
			);
		}
	}
	
	// point styles	
	for ( i = 1; i < serNum + 1; i++ )
	{
		col = ColorUtil.hexToRGB( this.series.colors[i-1] );
		
		this._param(
			"series" + i,
			this.series.points[i-1] + "," +
			col[0] + "," +
			col[1] + "," +
			col[2]
		);
	}	
	
	// label data
	for ( i = 1; i < colNum + 1; i++ )
		this._param( "label" + i, this.series.labels[i-1] || APVis.defaultLabel );
};
