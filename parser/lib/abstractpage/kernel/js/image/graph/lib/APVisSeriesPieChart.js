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
APVisSeriesPieChart = function()
{
	this.Base = Base;
	this.Base();
	
	this.seriesCount  = 0;
	this.columnCount  = 0;

	this.values;
};


APVisSeriesPieChart.prototype = new Base();
APVisSeriesPieChart.prototype.constructor = APVisSeriesPieChart;
APVisSeriesPieChart.superclass = Base.prototype;

/**
 * obj format: [ [value,label,col,url], [value,label,col,url], [value,label,col,url], ... ]
 *
 * @access public
 */
APVisSeriesPieChart.prototype.set = function( obj )
{
	if ( obj != null && typeof( obj ) == "object" )
	{
		this.values = obj;
		
		this.columnCount = obj.length;
		this.seriesCount++;
		
		return true;
	}
	
	return false;
};

/**
 * @access public
 */
APVisSeriesPieChart.prototype.getSeriesCount = function()
{
	return this.seriesCount;
};

/**
 * @access public
 */
APVisSeriesPieChart.prototype.getColumnCount = function()
{
	return this.columnCount;
};
