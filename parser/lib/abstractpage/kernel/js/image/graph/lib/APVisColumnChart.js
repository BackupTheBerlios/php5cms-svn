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
APVisColumnChart = function( width, height )
{
	this.APVis = APVis;
	this.APVis( width, height );

	this.setChartType( "APVisColumn" );
	
	this.series = null;
};


APVisColumnChart.prototype = new APVis();
APVisColumnChart.prototype.constructor = APVisColumnChart;
APVisColumnChart.superclass = APVis.prototype;

/**
 * @access public
 */
APVisColumnChart.prototype.addSeries = function( seriesObj )
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
APVisColumnChart.prototype.setXTitle = function( text, x, y, font, fstyle, fsize, col )
{
	if (  ( text == null ) || ( x == null ) || ( y == null ) )
		return false;
		
	this._addTextData( this.params, "xtitle", text, x, y, font, fstyle, fsize, col );
	return true;
};

/**
 * @access public
 */
APVisColumnChart.prototype.setYTitle = function( text, x, y, font, fstyle, fsize, col )
{
	if (  ( text == null ) || ( x == null ) || ( y == null ) )
		return false;
		
	this._addTextData( this.params, "ytitle", text, x, y, font, fstyle, fsize, col );
	return true;
};

/**
 * @access public
 */
APVisColumnChart.prototype.setGridMode = function( bool )
{
	this._param( "grid", bool );
};

/**
 * @access public
 */
APVisColumnChart.prototype.getGridMode = function()
{
	return this.params.get( "grid" );
};

/**
 * @access public
 */
APVisColumnChart.prototype.setAxisMode = function( bool )
{
	this._param( "axis", bool );
};

/**
 * @access public
 */
APVisColumnChart.prototype.getAxisMode = function()
{
	return this.params.get( "axis" );
};

/**
 * @access public
 */
APVisColumnChart.prototype.setYLabelsMode = function( bool )
{
	this._param( "ylabels", bool );
};

/**
 * @access public
 */
APVisColumnChart.prototype.getYLabelsMode = function()
{
	return this.params.get( "ylabels" );
};

/**
 * @access public
 */
APVisColumnChart.prototype.setVSpace = function( space )
{
	this._param( "vSpace", space );
};

/**
 * @access public
 */
APVisColumnChart.prototype.getVSpace = function()
{
	return this.params.get( "vSpace" );
};

/**
 * @access public
 */
APVisColumnChart.prototype.setBarWidth = function( width )
{
	this._param( "barwidth", width );
};

/**
 * @access public
 */
APVisColumnChart.prototype.getBarWidth = function()
{
	return this.params.get( "barwidth" );
};

/**
 * @access public
 */
APVisColumnChart.prototype.setGridX = function( x )
{
	this._param( "gridxpos", x );
};

/**
 * @access public
 */
APVisColumnChart.prototype.getGridX = function()
{
	return this.params.get( "gridxpos" );
};

/**
 * @access public
 */
APVisColumnChart.prototype.setGridY = function( y )
{
	this._param( "gridypos", y );
};

/**
 * @access public
 */
APVisColumnChart.prototype.getGridY = function()
{
	return this.params.get( "gridxpos" );
};

/**
 * @access public
 */
APVisColumnChart.prototype.setYPosOfXAxisLabels = function( y )
{
	this._param( "labelsY", y );
};

/**
 * @access public
 */
APVisColumnChart.prototype.getYPosOfXAxisLabels = function()
{
	return this.params.get( "labelsY" );
};

/**
 * @access public
 */
APVisColumnChart.prototype.setChartScale = function( scale )
{
	this._param( "chartScale", scale );
};

/**
 * @access public
 */
APVisColumnChart.prototype.getChartScale = function()
{
	return this.params.get( "chartScale" );
};

/**
 * @access public
 */
APVisColumnChart.prototype.setChartY = function( y )
{
	this._param( "chartStartY", y );
};

/**
 * @access public
 */
APVisColumnChart.prototype.getChartY = function()
{
	return this.params.get( "chartStartY" );
};

/**
 * @access public
 */
APVisColumnChart.prototype.setYLabelsFont = function( font, fstyle, fsize )
{
	this._param( "font14", font, fstyle, fsize );
};

/**
 * @access public
 */
APVisColumnChart.prototype.getYLabelsFont = function()
{
	return this.params.get( "font14" );
};

/**
 * @access public
 */
APVisColumnChart.prototype.setXLabelsFont = function( font, fstyle, fsize )
{
	this._param( "font15", font, fstyle, fsize );
};

/**
 * @access public
 */
APVisColumnChart.prototype.getXLabelsFont = function()
{
	return this.params.get( "font15" );
};

/**
 * @access public
 */
APVisColumnChart.prototype.setGridColor = function( col )
{
	this._addColorData( "color14", col );
};

/**
 * @access public
 */
APVisColumnChart.prototype.getGridColor = function()
{
	return this.params.get( "color14" );
};

/**
 * @access public
 */
APVisColumnChart.prototype.setAxisColor = function( col )
{
	this._addColorData( "color15", col );
};

/**
 * @access public
 */
APVisColumnChart.prototype.getAxisColor = function()
{
	return this.params.get( "color15" );
};

/**
 * @access public
 */
APVisColumnChart.prototype.setFloorColor = function( col )
{
	this._addColorData( "color16", col );
};

/**
 * @access public
 */
APVisColumnChart.prototype.getFloorColor = function()
{
	return this.params.get( "color16" );
};

/**
 * @access public
 */
APVisColumnChart.prototype.setBarOutlineColor = function( col )
{
	this._addColorData( "color17", col );
};

/**
 * @access public
 */
APVisColumnChart.prototype.getBarOutlineColor = function()
{
	return this.params.get( "color17" );
};

/**
 * @access public
 */
APVisColumnChart.prototype.setYLabelsColor = function( col )
{
	this._addColorData( "color19", col );
};

/**
 * @access public
 */
APVisColumnChart.prototype.getYLabelsColor = function()
{
	return this.params.get( "color19" );
};

/**
 * @access public
 */
APVisColumnChart.prototype.setGridRows = function( rows )
{
	this._param( "nRows", rows );
};

/**
 * @access public
 */
APVisColumnChart.prototype.getGridRows = function()
{
	return this.params.get( "nRows" );
};


// private methods

/**
 * @access private
 */
APVisColumnChart.prototype._buildSpecific = function()
{
	// no data added
	if ( this.series == null )
		return false;
	
	var i, j;
	var colNum = this.series.getColumnCount();
	var serNum = this.series.getSeriesCount(); // silently assuming that all series are of equal length
	
	this._param( "nCols",   colNum );
	this._param( "nSeries", serNum );
	
	for ( i = 1; i < serNum + 1; i++ )
	{
		for ( j = 1; j < colNum + 1; j++ )
		{
			this._addSeriesData(
				"column" + j + "series" + i,
				this.series.values[i-1][j-1][0],	// value
				this.series.colors[i-1],			// color
				this.series.values[i-1][j-1][1] || APVis.defaultURL
			);
		}
	}
	
	// label data
	for ( i = 1; i < colNum + 1; i++ )
		this._param( "label" + i, this.series.labels[i-1] || APVis.defaultLabel );
};
