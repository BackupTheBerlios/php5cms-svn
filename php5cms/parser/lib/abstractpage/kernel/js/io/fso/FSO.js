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
FSO = function()
{
	this.Base = Base;
	this.Base();
	
	this.fso = new ActiveXObject( "Scripting.FileSystemObject" );
};


FSO.prototype = new Base();
FSO.prototype.constructor = FSO;
FSO.superclass = Base.prototype;

/**
 * @access public
 */
FSO.prototype.getDrives = function()
{
	// dictionary
	return this.fso.Drives;
};

/**
 * @access public
 */
FSO.prototype.buildPath = function( path, name )
{
	if ( path != null && name != null )
		this.fso.BuildPath( path, name );
};

/**
 * @access public
 */
FSO.prototype.copyFile = function( source, target, overwrite )
{
	if ( source != null && target != null )
		this.fso.CopyFile( source, target, overwrite || false )
};

/**
 * @access public
 */
FSO.prototype.copyFolder = function( source, target, overwrite )
{
	if ( source != null && target != null )
		this.fso.CopyFolder( source, target, overwrite || false )
};

/**
 * @access public
 */
FSO.prototype.createFolder = function()
{
	// TODO
};

/**
 * @access public
 */
FSO.prototype.createTextFile = function( path, overwrite, unicode )
{
	if ( path != null )
		this.fso.CreateTextFile( path, overwrite || false, unicode || false )
};

/**
 * @access public
 */
FSO.prototype.deleteFile = function( path, ignorelock )
{
	if ( path != null )
		this.fso.DeleteFile( path, ignorelock || false );
};

/**
 * @access public
 */
FSO.prototype.deleteFolder = function( path, delfiles )
{
	if ( path != null )
		this.fso.DeleteFolder( path, delfiles || false );
};

/**
 * @access public
 */
FSO.prototype.driveExists = function( drive )
{
	if ( drive != null )
		return this.fso.DriveExists( drive );
};

/**
 * @access public
 */
FSO.prototype.fileExists = function( path )
{
	if ( path != null )
		return this.fso.FileExists( path );
};

/**
 * @access public
 */
FSO.prototype.folderExists = function( folder )
{
	if ( folder != null )
		return this.fso.FolderExists( folder );
};

/**
 * @access public
 */
FSO.prototype.getAbsolutePathName = function( path )
{
	if ( path != null )
		return this.fso.GetAbsolutePathName( path );
};

/**
 * @access public
 */
FSO.prototype.getBaseName = function( path )
{
	if ( path != null )
		return this.fso.GetBaseName( path );
};

/**
 * @access public
 */
FSO.prototype.getDrive = function( name )
{
	if ( name != null && this.driveExists( name ) )
		return new FSODrive( this.fso.GetDrive( name ) );
};

/**
 * @access public
 */
FSO.prototype.getDriveName = function( path )
{
	if ( path != null )
		return this.fso.GetDriveName( path );
};

/**
 * @access public
 */
FSO.prototype.getExtensionName = function( path )
{
	if ( path != null && this.fileExists( path ) )
		return this.fso.GetExtensionName( path );
};

/**
 * @access public
 */
FSO.prototype.getFile = function( path )
{
	if ( path != null && this.fileExists( path ) )
		return new FSOFile( this.fso.GetFile( path ) );
};

/**
 * @access public
 */
FSO.prototype.getFileName = function( path )
{
	if ( path != null )
		return this.fso.GetFileName( path);
};

/**
 * @access public
 */
FSO.prototype.getFileVersion = function( path )
{
	if ( path != null )
		return this.fso.GetFileVersion( path);
};

/**
 * @access public
 */
FSO.prototype.getFolder = function( path )
{
	if ( path != null && this.folderExists( path ) )
		return new FSOFolder( this.fso.GetFolder( path ) );
};

/**
 * @access public
 */
FSO.prototype.getParentFolderName = function( path )
{
	if ( path != null )
		return this.fso.GetParentFolderName( path);
};

/**
 * Note: num: 0 = Windows; 1 = Windows/System; 2 = Windows/Temp
 *
 * @access public
 */
FSO.prototype.getSpecialFolder = function( num )
{
	if ( num != null && ( num >= 0 && num <= 2 ) )
		return new FSOFolder( this.fso.GetSpecialFolder( num ) );
};

/**
 * @access public
 */
FSO.prototype.getTempName = function()
{
	return this.fso.GetTempName();
};

/**
 * @access public
 */
FSO.prototype.moveFile = function( sourcepath, targetpath )
{
	if ( sourcepath != null && targetpath != null )
		this.fso.MoveFile( sourcepath, targetpath );
};

/**
 * @access public
 */
FSO.prototype.moveFolder = function( sourcepath, targetpath )
{
	if ( sourcepath != null && targetpath != null )
		this.fso.MoveFolder( sourcepath, targetpath );
};

/**
 * @access public
 */
FSO.prototype.openTextFile = function( path, mode, create, charset )
{
	if ( path != null && ( mode != null && ( mode == 1 || mode == 2 || mode == 8 ) ) )
		return new FSOTextStream( this.fso.OpenTextFile( path, mode, create || false, charset || "TristateFalse" ) );
};
