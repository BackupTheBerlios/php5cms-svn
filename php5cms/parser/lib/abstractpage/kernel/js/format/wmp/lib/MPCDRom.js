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
MPCDRom = function( cdrom )
{
	this.Base = Base;
	this.Base();
	
	this.cdrom = cdrom;
	this.onEject = new Function;
};


MPCDRom.prototype = new Base();
MPCDRom.prototype.constructor = MPCDRom;
MPCDRom.superclass = Base.prototype;

/**
 * Returns a MPPlaylist object representing the tracks on the CD currently in the CD-Rom.
 *
 * @access public
 */
MPCDRom.prototype.getPlaylist = function()
{
	return new MPPlaylist( this.cdrom.playlist );
};

/**
 * Ejects the CD.
 *
 * @access public
 */
MPCDRom.prototype.eject = function()
{
	this.cdrom.eject();
	this.onEject();
};

/**
 * Retrieves the CD-Rom drive letter.
 *
 * @access public
 */
MPCDRom.prototype.getDriveSpecifier = function()
{
	return this.cdrom.driveSpecifier;
};
