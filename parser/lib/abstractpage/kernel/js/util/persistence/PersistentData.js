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
 * PersistentData Class (utilizing IE behaviour)
 *
 * @package util_persistence
 */
 
/**
 * Constructor
 *
 * @access public
 */
PersistentData = function( id, branch )
{
	this.Base = Base;
	this.Base();
	
	this.id = id || "persistentDataDiv";
	this.branch = branch || "oXMLBranch";	
	
	var div = document.createElement( "DIV" );
	div.setAttribute( "id", this.id );
	div.addBehavior( "#default#userdata" );
	div.style.visibility = "hidden";
	document.getElementsByTagName( "BODY" ).item( 0 ).appendChild( div );
	
	this.context = div;
};


PersistentData.prototype = new Base();
PersistentData.prototype.constructor = PersistentData;
PersistentData.superclass = Base.prototype;

/**
 * @access public
 */
PersistentData.prototype.set = function( name, value )
{
	if ( name != null && value != null )
	{
		this.context.load( this.branch );
		this.context.setAttribute( name, value );
		this.context.save( this.branch );
			
		return true;
	}
	
	return false;
};

/**
 * @access public
 */
PersistentData.prototype.get = function( name )
{
	if ( name == null )
		return false;
		
	this.context.load( this.branch );
	var value = this.context.getAttribute( name );
	this.context.save( this.branch );
	
	if ( !value || value == "" )
		return false;
	else
		return value;
};

/**
 * @access public
 */
PersistentData.prototype.has = function( name )
{
	if ( name == null )
		return false;
		
	this.context.load( this.branch );
	return this.context.getAttribute( name );
	this.context.save( this.branch );
};

/**
 * @access public
 */
PersistentData.prototype.del = function( name )
{
	if ( name == null )
		return false;
		
	this.context.load( this.branch );
	this.context.removeAttribute( name );
	this.context.save( this.branch );
};

/**
 * @access public
 */
PersistentData.prototype.setExpirationDate = function( date )
{
	if ( date == null )
		return false;
		
	this.context.load( this.branch );
	this.context.expires = date;
	this.context.save( this.branch );
};
