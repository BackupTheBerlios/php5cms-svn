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
 * FileSystem Object (utilizes IE FileSystemObject)
 * experimental code, works only on local drives.
 *
 * @package io
 */

/**
 * Constructor
 *
 * @access public
 */
FileSystem = function()
{
	this.Base = Base;
	this.Base();
	
	this.fso     = new ActiveXObject( "Scripting.FileSystemObject" );
	this.dirlist = new Array();
	this.actPath = "";
	this.drives  = this.fso.Drives;
};


FileSystem.prototype = new Base();
FileSystem.prototype.constructor = FileSystem;
FileSystem.superclass = Base.prototype;

/**
 * @access public
 */
FileSystem.prototype.getDriveList = function()
{
	var s, n, e, x;
   
	e = new Enumerator( this.drives );
	s = "";
   
	for ( ; !e.atEnd(); e.moveNext() )
	{
		x  = e.item();
		s  = s + x.DriveLetter;
		s += " - ";
		
		if ( x.DriveType == 3 )	// See if network drive.
			n = x.ShareName;	// Get share name
		else if ( x.IsReady )	// See if drive is ready.
			n = x.VolumeName;	// Get volume name.
		else
			n = "[Drive not ready]";
			
		s +=  n + "<br>";
	}
	
	return( s );
};

/**
 * @access public
 */
FileSystem.prototype.getDirList = function( dir )
{
	// is dir?
	
	var fileNames = new Array();
	var oFolder   = this.getFolder( dir );
	
	// enumerate over all sub folders
	var sfe = new Enumerator( oFolder.SubFolders );
		
	for ( ; !sfe.atEnd(); sfe.moveNext() )
	{
		var f = sfe.item();
		fileNames[fileNames.length] = f.Name;
	}
	
	return fileNames;
};

/**
 * @access public
 */
FileSystem.prototype.getDirListByExtension = function( dir )
{
};

/**
 * @access public
 */
FileSystem.prototype.getFile = function( file )
{
	return this.fso.GetFile( file );
};

/**
 * @access public
 */
FileSystem.prototype.getFolder = function( folder )
{
	return this.fso.GetFolder( folder );
};

/**
 * @access public
 */
FileSystem.prototype.getDrive = function( drive )
{
	return this.fso.GetDrive( drive );
};

/**
 * @access public
 */
FileSystem.prototype.createFolder = function( drive, fname )
{
	var fldr = this.fso.CreateFolder( drive + ":\\" + fname );
	return fldr.name;
};
