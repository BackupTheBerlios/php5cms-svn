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
 * @package io_fso
 */
 
/**
 * Constructor
 *
 * @access public
 */
FSODrive = function( drive )
{
	this.Base = Base;
	this.Base();
	
	this.drive = drive;
};


FSODrive.prototype = new Base();
FSODrive.prototype.constructor = FSODrive;
FSODrive.superclass = Base.prototype;

/**
 * @access public
 */
FSODrive.prototype.getAvailableSpace = function( kb )
{
	return kb? this.drive.AvailableSpace / 1024 : this.drive.AvailableSpace;
};

/**
 * @access public
 */
FSODrive.prototype.getDriveLetter = function()
{
	return this.drive.DriveLetter;
};

/**
 * @access public
 */
FSODrive.prototype.getDriveType = function()
{
	return this.drive.DriveType;
};

/**
 * @access public
 */
FSODrive.prototype.getFileSystem = function()
{
	return this.drive.FileSystem;
};

/**
 * @access public
 */
FSODrive.prototype.getFreeSpace = function( kb )
{
	return kb? this.drive.FreeSpace / 1024 : this.drive.FreeSpace;
};

/**
 * @access public
 */
FSODrive.prototype.isReady = function()
{
	return this.drive.IsReady;
};

/**
 * @access public
 */
FSODrive.prototype.getPath = function()
{
	return this.drive.Path;
};

/**
 * @access public
 */
FSODrive.prototype.getRootFolder = function()
{
	return this.drive.RootFolder;
};

/**
 * @access public
 */
FSODrive.prototype.getSerialNumber = function()
{
	return this.drive.SerialNumber;
};

/**
 * @access public
 */
FSODrive.prototype.getShareName = function()
{
	return this.drive.ShareName;
};

/**
 * @access public
 */
FSODrive.prototype.getTotalSize = function()
{
	return this.drive.TotalSize;
};

/**
 * @access public
 */
FSODrive.prototype.getVolumeName = function()
{
	return this.drive.VolumeName;
};
