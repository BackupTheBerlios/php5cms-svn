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
 * @package db
 */
 
/**
 * Constructor
 *
 * @access public
 */
DataSelect = function( filename, delimiter, objectid )
{
	this.Base = Base;
	this.Base();
	
	// this.dataset = new Object();
	
	this.setFilename( filename || "" );
	this.setDelimiter( delimiter || "," );
	this.setObjectID( objectid || "oData" );
	
	if ( !DataSelect.created && ( DataSelect.idOfCreated != this.getObjectID() ) )
	{
		document.body.insertAdjacentHTML(
			'beforeEnd',
			this.getActiveXControl()
		);
	
		DataSelect.created = true;
		DataSelect.idOfCreated = this.getObjectID();
	}
};


DataSelect.prototype = new Base();
DataSelect.prototype.constructor = DataSelect;
DataSelect.superclass = Base.prototype;

/**
 * @access public
 */
DataSelect.prototype.getActiveXControl = function()
{
	var str = "";
	
	str += '<object id="' + this.objectid + '" classid="clsid:333C7BC4-460F-11D0-BC04-0080C7055A83">\n';
	str += '<param name="DataURL" value="'    + this.filename  + '">\n';
	str += '<param name="FieldDelim" value="' + this.delimiter + '">\n';
	str += '</object>\n';
	
	return str;
};

/**
 * @access public
 */
DataSelect.prototype.setObjectID = function( id )
{
	if ( id != null )
	{
		this.objectid = id;
		return this.getObjectID();
	}
	
	return false;
};

/**
 * @access public
 */
DataSelect.prototype.getObjectID = function()
{
	return this.objectid;
};

/**
 * @access public
 */
DataSelect.prototype.setFilename = function( name )
{
	if ( name != null )
	{
		this.filename = name;
		return this.getFilename();
	}
	
	return false;
};

/**
 * @access public
 */
DataSelect.prototype.getFilename = function()
{
	return this.filename;
};

/**
 * @access public
 */
DataSelect.prototype.setDelimiter = function( delim )
{
	if ( delim != null )
	{
		this.delimiter = delim;
		return this.getDelimiter();
	}
	
	return false;
};
DataSelect.prototype.getDelimiter = function()
{
	return this.delimiter;
};


/**
 * @access public
 * @static
 */
DataSelect.created = false;

/**
 * @access public
 * @static
 */
DataSelect.idOfCreated = null;
