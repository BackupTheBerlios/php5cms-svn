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
FSOFolder = function( folder )
{
	this.Base = Base;
	this.Base();
	
	this.folder = folder;
	this.attributes = this.folder.Attributes;
};


FSOFolder.prototype = new Base();
FSOFolder.prototype.constructor = FSOFolder;
FSOFolder.superclass = Base.prototype;

/**
 * @access public
 */
FSOFolder.prototype.copy = function( path, overwrite )
{
	if ( path != null )
		this.folder.Copy( path, overwrite || false );
};

/**
 * @access public
 */
FSOFolder.prototype.del = function( ignorelock )
{
	this.folder.Delete( ignorelock || false );
};

/**
 * @access public
 */
FSOFolder.prototype.move = function( path )
{
	if ( path != null )
		this.folder.Move( path );
};

/**
 * @access public
 */
FSOFolder.prototype.getDateCreated = function()
{
	return this.folder.DateCreated;
};

/**
 * @access public
 */
FSOFolder.prototype.getDateLastAccessed = function()
{
	return this.folder.DateLastAccessed;
};

/**
 * @access public
 */
FSOFolder.prototype.getDateLastModified = function()
{
	return this.folder.DateLastModified;
};

/**
 * @access public
 */
FSOFolder.prototype.getDrive = function()
{
	return this.folder.Drive;
};

/**
 * @access public
 */
FSOFolder.prototype.getFiles = function()
{
	return this.folder.Files;
};

/**
 * @access public
 */
FSOFolder.prototype.isRootFolder = function()
{
	return this.folder.IsRootFolder;
};

/**
 * @access public
 */
FSOFolder.prototype.setName = function( name )
{
	if ( name != null )
		this.folder.Name = name;
};

/**
 * @access public
 */
FSOFolder.prototype.getName = function()
{
	return this.folder.Name;
};

/**
 * @access public
 */
FSOFolder.prototype.getParentFolder = function()
{
	return this.folder.ParentFolder;
};

/**
 * @access public
 */
FSOFolder.prototype.getPath = function()
{
	return this.folder.Path;
};

/**
 * @access public
 */
FSOFolder.prototype.getShortName = function()
{
	return this.folder.ShortName;
};

/**
 * @access public
 */
FSOFolder.prototype.getShortPath = function()
{
	return this.folder.ShortPath;
};

/**
 * @access public
 */
FSOFolder.prototype.getSize = function( kb )
{
	return kb? this.folder.Size / 1024 : this.folder.Size;
};

/**
 * @access public
 */
FSOFolder.prototype.getSubFolders = function()
{
	return this.folder.SubFolders;
};

/**
 * @access public
 */
FSOFolder.prototype.getType = function()
{
	return this.folder.Type;
};
