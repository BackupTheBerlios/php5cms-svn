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
 * @package format_wmf_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
MPCDRomCollection = function( cdromcollection )
{
	this.Base = Base;
	this.Base();
	
	this.cdromcollection = cdromcollection;
};


MPCDRomCollection.prototype = new Base();
MPCDRomCollection.prototype.constructor = MPCDRomCollection;
MPCDRomCollection.superclass = Base.prototype;

/**
 * Retrieves the number of available CD-Rom drives on the system.
 *
 * @access public
 */
MPCDRomCollection.prototype.getCDRomCount = function()
{
	return this.cdromcollection.count;
};

/**
 * Retrieves a MPCDRom object associated with a particular drive letter.
 *
 * @access public
 */
MPCDRomCollection.prototype.getByDriveSpecifier = function( driveSpecifier )
{
	if ( driveSpecifier != null )
		return new MPCDRom( this.cdromcollection.getByDriveSpecifier( driveSpecifier ) );
};

/**
 * Retrieves a MPCDRom Object at the given index.
 *
 * @access public
 */
MPCDRomCollection.prototype.getItem = function( index )
{
	if ( index != null && Util.is_int( index ) && index <= this.getCDRomCount() )
		return new MPCDRom( this.cdromcollection.item( index ) );
};
