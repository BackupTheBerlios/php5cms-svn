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
 * @package io
 */
 
/**
 * Constructor
 *
 * @access public
 */
VirtualFolder = function()
{
	this.Base = Base;
	this.Base();

	this.pd = new PersistentData();
	
	// sort function for getList()
	this.sortfn = function( a, b )
	{
		return a > b;
	}
	
	// event handlers
	this.onDocNotFound = new Function;
	/*
	this.onOverWrite = function()
	{
		return window.confirm( 'Overwrite?' );
	};
	*/

	this.allowOverwriting = true;
	
	if ( !this.hasVirtualFolder() )
		this.pd.set( "virtualfolder", "true" );
};


VirtualFolder.prototype = new Base();
VirtualFolder.prototype.constructor = VirtualFolder;
VirtualFolder.superclass = Base.prototype;

/**
 * @access public
 */
VirtualFolder.prototype.hasVirtualFolder = function()
{
	return this.pd.has( "virtualfolder" );
};

/**
 * @access public
 */
VirtualFolder.prototype.getList = function()
{
	var list = this.pd.get( "virtualfolder" );
	list = list.split( VirtualFolder.arrayDelimiter );
	
	// strip header
	list = this._removeFromList( list, "true" );

	return list.sort( this.sortfn );
};

/**
 * @access public
 */
VirtualFolder.prototype.hasDocument = function( name )
{
	return this.pd.has( name );
};

/**
 * @access public
 */
VirtualFolder.prototype.getDocument = function( name )
{
	if ( name == null || !this.hasDocument( name ) )
	{
		this.onDocNotFound();
		return false;
	}
	
	return this.pd.get( name );
};

/**
 * @access public
 */
VirtualFolder.prototype.addDocument = function( name, doc )
{
	if ( name == null || doc == null )
		return;
	
	if ( this.allowOverwriting )
	{
		// append only if new to the list
		if ( !this.hasDocument( name ) )
		{
			var list = this.pd.get( "virtualfolder" );
			list = list.split( VirtualFolder.arrayDelimiter );
			list[list.length] = name;
			list = list.join( VirtualFolder.arrayDelimiter );
			this.pd.set( "virtualfolder", list );
		}
			
		// save document
		this.pd.set( name, doc );
	}
};

/**
 * @access public
 */
VirtualFolder.prototype.removeDocument = function( name )
{
	if ( name == null || !this.hasDocument( name ) )
		return;

	// update folderlist	
	var list = this.pd.get( "virtualfolder" );
	list = list.split( VirtualFolder.arrayDelimiter );
	
	// strip header
	list = this._removeFromList( list, name );

	list = list.join( VirtualFolder.arrayDelimiter );
	this.pd.set( "virtualfolder", list );
	
	this.pd.del( name );
};

/**
 * @access public
 */
VirtualFolder.prototype.removeAll = function()
{
	var list = this.getList();
	
	for ( var i in list )
		this.pd.del( list[i] );
};


// private methods

/**
 * @access private
 */
VirtualFolder.prototype._removeFromList = function( list, item )
{
	if ( list == null || item == null )
		return false;

	for ( var i = 0; i < list.length; i++ )
	{
		if ( list[i] == item )
			list.splice( i, 1 );
	}
	
	return list;
};


/**
 * @access public
 * @static
 */
VirtualFolder.arrayDelimiter = ":";
