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
 * @package fx_path
 */
 
/**
 * Constructor
 *
 * @access public
 */
PathList = function( inPathList )
{
	this.Path = Path;
	this.Path();

	this.x  = 0;
	this.y  = 0;

	this.pathList = inPathList;
	this.currentPath = 0;
};


PathList.prototype = new Path();
PathList.prototype.constructor = PathList;
PathList.superclass = Path.prototype;

/**
 * @access public
 */
PathList.prototype.step = function()
{
	if ( this.currentPath >= this.pathList.length )
		return false;
	
	if ( this.pathList[this.currentPath].step() )
	{
		this.x = this.pathList[this.currentPath].x;
		this.y = this.pathList[this.currentPath].y;
	}
	else
	{
		this.currentPath++;
		
		if ( this.currentPath >= this.pathList.length )
			return false;
		
		this.x = this.pathList[this.currentPath].x;
		this.y = this.pathList[this.currentPath].y;
	}
	return true;
};

/**
 * @access public
 */
PathList.prototype.reset = function()
{
	this.currentPath = 0;
	
	for ( var i = 0; i < this.pathList.length; i++ )
		this.pathList[i].reset();
	
	this.x = this.pathList[this.currentPath].x;
	this.y = this.pathList[this.currentPath].y;		
};
	