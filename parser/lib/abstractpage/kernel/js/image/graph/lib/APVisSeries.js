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
APVisSeries = function()
{
	this.Base = Base;
	this.Base();
	
	this.seriesCount  = 0;
	this.columnCount  = 0;

	// multidimensional
	this.values = new Array();
	
	// one-dimensional
	this.colors = new Array();
	this.labels = new Array();
};


APVisSeries.prototype = new Base();
APVisSeries.prototype.constructor = APVisSeries;
APVisSeries.superclass = Base.prototype;

/**
 * @access public
 */
APVisSeries.prototype.add = function( obj, color )
{
	if ( obj != null && typeof( obj ) == "object" )
	{
		this.values[this.values.length] = obj;
		this.colors[this.colors.length] = color || APVisSeries.defaultColor;
		
		this.columnCount = obj.length;
		this.seriesCount++;
		
		return true;
	}
	
	return false;
};

/**
 * @access public
 */
APVisSeries.prototype.addLabels = function( labels )
{
	if ( labels != null && typeof( labels ) == "object" )
	{
		this.labels = labels;
		return true;
	}
	
	return false;
};

/**
 * @access public
 */
APVisSeries.prototype.getSeriesCount = function()
{
	return this.seriesCount;
};

/**
 * @access public
 */
APVisSeries.prototype.getColumnCount = function()
{
	return this.columnCount;
};


/**
 * @access public
 * @static
 */
APVisSeries.defaultColor = "#800000";
