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
 * A material has a body, basically containing a string with HTML code
 * (which will make up the content of a point's DIV). Additionally it 
 * has a JavaScript statement - the refresh() method - that is called 
 * when drawing the points, so the material appearance can be changed
 * by changing what is happening in the DIV.
 *
 * @access public
 */
Material = function( body, refresh )
{
	this.Base = Base;
	this.Base();
	
	this.body = body;
	
	// stores reference to the specified refresh method
	this.refresh = refresh;

	return this;
};


Material.prototype = new Base();
Material.prototype.constructor = Material;
Material.superclass = Base.prototype;

/**
 * @access public
 */
Material.prototype.toString = function()
{
	return this.body;
};
