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
 * @package util_persistence
 */
 
/**
 * Constructor
 *
 * @access public
 */
Persistence = function( attachedTo, storePath, pageID )
{
	this.Base = Base;
	this.Base();
	
	if ( Browser.apopt )
	{
		this._obj = attachedTo;
		this._obj.addBehavior( "#default#userData" );
		this._path = storePath;
		this._loaded = false;
		this._pageID = pageID;
	}
};


Persistence.prototype = new Base();
Persistence.prototype.constructor = Persistence;
Persistence.superclass = Base.prototype;

/**
 * @access public
 */
Persistence.prototype.Save = function()
{
	if ( Browser.apopt )
	{
		if ( this.IsLoaded() )
		{
			var enabled = true;
			var code = "try{ this.GetAttached().save(this._path); } catch(e) {enabled = false;}";
			eval( code );
		}
	}
};

/**
 * @access public
 */
Persistence.prototype.Load = function()
{
	if ( Browser.apopt )
	{
		if (!this.IsLoaded() )
		{
			var enabled = true;
			var code = "try{ this.GetAttached().load(this._path); } catch(e) {enabled = false;}";
			eval( code );
			
			if ( !enabled )
				return;
			
			this._loaded = true;
		}
	}
};

/**
 * @access public
 */
Persistence.prototype.GetAttached = function()
{
	if ( Browser.apopt )
		return this._obj;
};

/**
 * @access public
 */
Persistence.prototype.IsLoaded = function()
{
	if ( Browser.apopt )
		return this._loaded;
};

/**
 * @access public
 */
Persistence.prototype.SetAttribute = function( key, value )
{
	if ( Browser.apopt )
	{
		if ( !this.IsLoaded() )
			this.Load();

		// Confirm that the load actually happened. Cannot add data if load failed.
		if ( this.IsLoaded() )
		{
			if ( !SDKTab && SDKTab.gsPageId )
				pageID = "unknown";
			else
				pageID = SDKTab.gsPageId;	
			
			this.GetAttached().setAttribute( pageID + "." + key, value );
		}
		
		this.Save();
	}
};

/**
 * @access public
 */
Persistence.prototype.GetAttribute = function( key )
{
	if ( Browser.apopt )
	{
		if ( !this.IsLoaded() )
			this.Load();

		// Confirm that the load actually happened. Cannot get data if load failed.
		if ( this.IsLoaded() )
		{
			if ( !SDKTab && SDKTab.gsPageId )
				pageID = "unknown";
			else
				pageID = SDKTab.gsPageId;	
			
			var value = this.GetAttached().getAttribute( pageID + "." + key );
		}
	
		if ( key == "selectedTab" )
		{
			if ( !value )
				value = 0;
		}
		else if ( key == "expanded" )
		{
			if ( value == "true" )
				value = true;
			else
				value = false;
		}
		else if ( key == "scroll" )
		{
			if ( !value )
				value = 0;
		}
	
		return value;
	}
};
