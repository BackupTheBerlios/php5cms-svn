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
FSOFile = function( file )
{
	this.Base = Base;
	this.Base();
	
	this.file = file;
	this.attributes = this.file.Attributes;
};


FSOFile.prototype = new Base();
FSOFile.prototype.constructor = FSOFile;
FSOFile.superclass = Base.prototype;

/**
 * @access public
 */
FSOFile.prototype.copy = function( path, overwrite )
{
	if ( path != null )
		this.file.Copy( path, overwrite || false );
};

/**
 * @access public
 */
FSOFile.prototype.del = function( ignorelock )
{
	this.file.Delete( ignorelock || false );
};

/**
 * @access public
 */
FSOFile.prototype.move = function( path )
{
	if ( path != null )
		this.file.Move( path );
};

/**
 * @access public
 */
FSOFile.prototype.openAsTextStream = function( mode, charset )
{
	if ( mode != null && ( mode == 1 || mode == 2 || mode == 8 ) )
		this.file.OpenAsTextStream( mode, charset || 0 );
};

/**
 * @access public
 */
FSOFile.prototype.getDateCreated = function()
{
	return this.file.DateCreated;
};

/**
 * @access public
 */
FSOFile.prototype.getDateLastAccessed = function()
{
	return this.file.DateLastAccessed;
};

/**
 * @access public
 */
FSOFile.prototype.getDateLastModified = function()
{
	return this.file.DateLastModified;
};

/**
 * @access public
 */
FSOFile.prototype.getDrive = function()
{
	return this.file.Drive;
};

/**
 * @access public
 */
FSOFile.prototype.setName = function( name )
{
	if ( name != null )
		this.file.Name = name ;
};

/**
 * @access public
 */
FSOFile.prototype.getName = function()
{
	return this.file.Name;
};

/**
 * @access public
 */
FSOFile.prototype.getParentFolder = function()
{
	return this.file.ParentFolder;
};

/**
 * @access public
 */
FSOFile.prototype.getPath = function()
{
	return this.file.Path;
};

/**
 * @access public
 */
FSOFile.prototype.getShortName = function()
{
	return this.file.ShortName;
};

/**
 * @access public
 */
FSOFile.prototype.getShortPath = function()
{
	return this.file.ShortPath;
};

/**
 * @access public
 */
FSOFile.prototype.getSize = function( kb )
{
	return kb? this.file.Size / 1024 : this.file.Size;
};

/**
 * @access public
 */
FSOFile.prototype.getType = function()
{
	return this.file.Type;
};
