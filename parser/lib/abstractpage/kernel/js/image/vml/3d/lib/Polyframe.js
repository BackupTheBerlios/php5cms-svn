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
 * @package image_vml_3d_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
Polyframe = function()
{
	this.Base = Base;
	this.Base();
	
	this.pool   = null;
	this.pGroup = null;
	this.polys  = null;
};


Polyframe.prototype = new Base();
Polyframe.prototype.constructor = Polyframe;
Polyframe.superclass = Base.prototype;

/**
 * @access public
 */
Polyframe.prototype.init = function( parentName, oid )
{
	var parentobj = document.getElementById( parentName );
	this.pGroup   = document.createElement( "v:group" );
	this.pGroup.style.position = "absolute";
	this.pGroup.style.top      = 0;
	this.pGroup.style.left     = 0;
	this.pGroup.style.width    = parentobj.style.width;
	this.pGroup.style.height   = parentobj.style.height;
	this.pGroup.coordsize      = "1000,1000";
	this.pGroup.coordorigin    = "-500,-500";
	this.pGroup.id             = "vmlgroup";
	
	parentobj.insertBefore( this.pGroup, null );
};

/**
 * @access public
 */
Polyframe.prototype.loadObject = function( dataSource, scale )
{
	this.polys = new Polypool();

	var xobj = dataSource.XMLDocument;
	this.polys.createObject( xobj, scale );

	this.polys.w = this.pGroup.style.width;
	this.polys.h = this.pGroup.style.height;

	// perspective point
	this.polys.cx = 0;
	this.polys.cy = 0;

	this.polys.transformPoints( 0, 0, 0, 0, 0, 0 );
	this.polys.perspTransform();

	this.polys.createDomObjects( this.pGroup );
};
