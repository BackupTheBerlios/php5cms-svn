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
APVisBarChart = function( width, height )
{
	this.APVis = APVis;
	this.APVis( width, height );
	
	this.setChartType( "APVisBar" );
	
	this.series = null;
};


APVisBarChart.prototype = new APVis();
APVisBarChart.prototype.constructor = APVisBarChart;
APVisBarChart.superclass = APVis.prototype;

/**
 * @access public
 */
APVisBarChart.prototype.addSeries = function( seriesObj )
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
APVisBarChart.prototype.setXTitle = function( text, x, y, font, fstyle, fsize, col )
{
	if (  ( text == null ) || ( x == null ) || ( y == null ) )
		return false;
		
	this._addTextData( this.params, "xtitle", text, x, y, font, fstyle, fsize, col );
	return true;
};

/**
 * @access public
 */
APVisBarChart.prototype.setYTitle = function( text, x, y, font, fstyle, fsize, col )
{
	if (  ( text == null ) || ( x == null ) || ( y == null ) )
		return false;
		
	this._addTextData( this.params, "ytitle", text, x, y, font, fstyle, fsize, col );
	return true;
};

/**
 * @access public
 */
APVisBarChart.prototype.setGridMode = function( bool )
{
	this._param( "grid", bool );
};

/**
 * @access public
 */
APVisBarChart.prototype.getGridMode = function()
{
	return this.params.get( "grid" );
};

/**
 * @access public
 */
APVisBarChart.prototype.setAxisMode = function( bool )
{
	this._param( "axis", bool );
};

/**
 * @access public
 */
APVisBarChart.prototype.getAxisMode = function()
{
	return this.params.get( "axis" );
};

/**
 * @access public
 */
APVisBarChart.prototype.setXLabelsMode = function( bool )
{
	this._param( "xlabels", bool );
};

/**
 * @access public
 */
APVisBarChart.prototype.getXLabelsMode = function()
{
	return this.params.get( "xlabels" );
};

/**
 * @access public
 */
APVisBarChart.prototype.setHSpace = function( space )
{
	this._param( "hSpace", space );
};

/**
 * @access public
 */
APVisBarChart.prototype.getHSpace = function()
{
	return this.params.get( "hSpace" );
};

/**
 * @access public
 */
APVisBarChart.prototype.setBarWidth = function( width )
{
	this._param( "barwidth", width );
};

/**
 * @access public
 */
APVisBarChart.prototype.getBarWidth = function()
{
	return this.params.get( "barwidth" );
};

/**
 * @access public
 */
APVisBarChart.prototype.setGridX = function( x )
{
	this._param( "gridxpos", x );
};

/**
 * @access public
 */
APVisBarChart.prototype.getGridX = function()
{
	return this.params.get( "gridxpos" );
};

/**
 * @access public
 */
APVisBarChart.prototype.setGridY = function( y )
{
	this._param( "gridypos", y );
};

/**
 * @access public
 */
APVisBarChart.prototype.getGridY = function()
{
	return this.params.get( "gridxpos" );
};

/**
 * @access public
 */
APVisBarChart.prototype.setXPosOfYAxisLabels = function( x )
{
	this._param( "labelsX", x );
};

/**
 * @access public
 */
APVisBarChart.prototype.getXPosOfYAxisLabels = function()
{
	return this.params.get( "labelsX" );
};

/**
 * @access public
 */
APVisBarChart.prototype.setChartScale = function( scale )
{
	this._param( "chartScale", scale );
};

/**
 * @access public
 */
APVisBarChart.prototype.getChartScale = function()
{
	return this.params.get( "chartScale" );
};

/**
 * @access public
 */
APVisBarChart.prototype.setChartX = function( x )
{
	this._param( "chartStartX", x );
};

/**
 * @access public
 */
APVisBarChart.prototype.getChartX = function()
{
	return this.params.get( "chartStartX" );
};

/**
 * @access public
 */
APVisBarChart.prototype.setYLabelsFont = function( font, fstyle, fsize )
{
	this._param( "font14", font, fstyle, fsize );
};

/**
 * @access public
 */
APVisBarChart.prototype.getYLabelsFont = function()
{
	return this.params.get( "font14" );
};

/**
 * @access public
 */
APVisBarChart.prototype.setXLabelsFont = function( font, fstyle, fsize )
{
	this._param( "font15", font, fstyle, fsize );
};

/**
 * @access public
 */
APVisBarChart.prototype.getXLabelsFont = function()
{
	return this.params.get( "font15" );
};

/**
 * @access public
 */
APVisBarChart.prototype.setGridColor = function( col )
{
	this._addColorData( "color14", col );
};

/**
 * @access public
 */
APVisBarChart.prototype.getGridColor = function()
{
	return this.params.get( "color14" );
};

/**
 * @access public
 */
APVisBarChart.prototype.setAxisColor = function( col )
{
	this._addColorData( "color15", col );
};

/**
 * @access public
 */
APVisBarChart.prototype.getAxisColor = function()
{
	return this.params.get( "color15" );
};

/**
 * @access public
 */
APVisBarChart.prototype.setFloorColor = function( col )
{
	this._addColorData( "color16", col );
};

/**
 * @access public
 */
APVisBarChart.prototype.getFloorColor = function()
{
	return this.params.get( "color16" );
};

/**
 * @access public
 */
APVisBarChart.prototype.setBarOutlineColor = function( col )
{
	this._addColorData( "color17", col );
};

/**
 * @access public
 */
APVisBarChart.prototype.getBarOutlineColor = function()
{
	return this.params.get( "color17" );
};

/**
 * @access public
 */
APVisBarChart.prototype.setXLabelsColor = function( col )
{
	this._addColorData( "color19", col );
};

/**
 * @access public
 */
APVisBarChart.prototype.getXLabelsColor = function()
{
	return this.params.get( "color19" );
};

/**
 * @access public
 */
APVisBarChart.prototype.setGridColumns = function( columns )
{
	this._param( "nCols", columns );
};

/**
 * @access public
 */
APVisBarChart.prototype.getGridColumns = function()
{
	return this.params.get( "nCols" );
};


// private methods

/**
 * @access private
 */
APVisBarChart.prototype._buildSpecific = function()
{
	// no data added
	if ( this.series == null )
		return false;
	
	var i, j;
	var colNum = this.series.getColumnCount(); // what means Bars in this case
	var serNum = this.series.getSeriesCount(); // silently assuming that all series are of equal length
	
	this._param( "nBars",   colNum );
	this._param( "nSeries", serNum );
	
	for ( i = 1; i < serNum + 1; i++ )
	{
		for ( j = 1; j < colNum + 1; j++ )
		{
			this._addSeriesData(
				"bar" + j + "series" + i,
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
