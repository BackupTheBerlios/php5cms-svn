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
APVisPieChart = function( width, height )
{
	this.APVis = APVis;
	this.APVis( width, height );

	this.setChartType( "APVisPie" );
	
	// in contrast to the other chart objects - this is an array
	this.series = new Array();
};


APVisPieChart.prototype = new APVis();
APVisPieChart.prototype.constructor = APVisPieChart;
APVisPieChart.superclass = APVis.prototype;

/**
 * @access public
 */
APVisPieChart.prototype.addPie = function( seriesObj, x, y, size )
{
	if ( ( seriesObj != null ) && ( typeof( seriesObj ) == "object" ) && ( x != null ) && ( y != null ) && ( size != null ) )
	{
		this.series[this.series.length] = seriesObj;
		
		this._param(
			"Pie" + this.series.length,
			x    + "," +
			y    + "," +
			size + "," +
			seriesObj.values.length // detecting number of segments
		);
		
		return true;
	}
	
	return false;
};

/**
 * @access public
 */
APVisPieChart.prototype.setSegmentLabelsMode = function( bool )
{
	this._param( "Slabels", bool );
};

/**
 * @access public
 */
APVisPieChart.prototype.getSegmentLabelsMode = function()
{
	return this.params.get( "Slabels" );
};

/**
 * @access public
 */
APVisPieChart.prototype.setLabelFont = function( font, fstyle, fsize )
{
	this._param( "font14", font, fstyle, fsize );
};

/**
 * @access public
 */
APVisPieChart.prototype.getLabelFont = function()
{
	return this.params.get( "font14" );
};

/**
 * @access public
 */
APVisPieChart.prototype.getSegmentLabels = function()
{
	return this.params.get( "Slabels" );
};

/**
 * @access public
 */
APVisPieChart.prototype.setSegmentOutlineColor = function( col )
{
	this._addColorData( "color17", col );
};

/**
 * @access public
 */
APVisPieChart.prototype.getSegmentOutlineColor = function()
{
	return this.params.get( "color17" );
};


// private methods

/**
 * @access private
 */
APVisPieChart.prototype._buildSpecific = function()
{
	// no data added
	if ( this.series.length == 0 )
		return false;
	
	var i, j, p, colNum, serNum, actValues;
	this._param( "nPies", this.series.length );

	colNum = this.series[0].getColumnCount();
	serNum = this.series[0].getSeriesCount(); // silently assuming that all series are of equal length

		
	for ( p = 0; p < this.series.length; p++  )
	{
		// [value,label,col,url]
		
		actValues = this.series[p].values;
		
		for ( i = 1; i < serNum + 1; i++ )
		{
			for ( j = 1; j < colNum + 1; j++ )
			{
				this._addSeriesData(
					"pie" + ( p + 1 ) + "segment" + j,
					actValues[j-1][0], // value
					actValues[j-1][2], // color
					actValues[j-1][3] || APVis.defaultURL
				);
			}
			
			for ( j = 1; j < colNum + 1; j++ )
			{
				this._param(
					"pie" + ( p + 1 ) + "label" + j,
					actValues[j-1][1] || APVis.defaultLabel
				);
			}	
		}
	}
};
