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
 * @package db_sql
 */
 
/**
 * Constructor
 *
 * @access public
 */
SQLUtil = function( table )
{
	this.Base = Base;
	this.Base();
	
	this.table = table || "";

	this.useDoubleQuotes = false;
	this.lastStatement   = "";
	this.selectMode      = null;
};


SQLUtil.prototype = new Base();
SQLUtil.prototype.constructor = SQLUtil;
SQLUtil.superclass = Base.prototype;

/**
 * @access public
 */
SQLUtil.prototype.quote = function( value )
{
	if ( this.useDoubleQuotes == true )
		return '"' + value + '"';
	else
		return '\'' + value + '\'';
};

/**
 * @access public
 */
SQLUtil.prototype.setTable = function( table )
{
	if ( table != null )
	{
		this.table = table;
		return true;
	}
	
	return false;
};

/**
 * @access public
 */
SQLUtil.prototype.getTable = function()
{
	return this.table;
};

/**
 * @access public
 */
SQLUtil.prototype.getLastStatement = function()
{
	return this.lastStatement;
};

/**
 * @access public
 */
SQLUtil.prototype.getDeleteStatement = function( match_criteria )
{
	if ( match_criteria == null )
		return false;
		
	var str = 'DELETE FROM ' + this.table;
	
	if ( match_criteria != null )
		str += ' WHERE ' + match_criteria;

	this.lastStatement = str;
	return str;
};

/**
 * @access public
 */
SQLUtil.prototype.getUpdateStatement = function( table_elems, match_criteria )
{	
	if ( table_elems == null || match_criteria == null || typeof( table_elems ) != "object" )
		return false;

	var moreThanOne = 0;
	var str  = 'UPDATE ' + this.table + ' SET ';		
	var keys = table_elems.getKeys();
	
	for ( var i in keys )
	{
		if ( moreThanOne != 0 )
			str += ", ";
		
		str += keys[i] + "=" + this.quote( table_elems.get( keys[i] ) );
		moreThanOne++;
	}
	
	if ( match_criteria != null )
		str += " WHERE " + match_criteria;

	this.lastStatement = str;
	return str;
};

/**
 * @access public
 */
SQLUtil.prototype.getInsertStatement = function( table_elems, show_fields )
{
	if ( table_elems == null || typeof( table_elems ) != "object" )
		return false;

	var i;
	var moreThanOne = 0;
	var str  = 'INSERT INTO ' + this.table + ' ';	
	var keys = table_elems.getKeys();
	
	if ( show_fields )
	{
		str += '(';
		
		for ( i in keys )
		{
			if ( moreThanOne != 0 )
				str += ", ";
		
			str += keys[i];
			moreThanOne++;
		}
		
		str += ') ';
	}
	
	str += 'VALUES ( ';
	moreThanOne = 0;
	
	for ( i in keys )
	{
		if ( moreThanOne != 0 )
			str += ", ";
		
		str += this.quote( table_elems.get( keys[i] ) );
		moreThanOne++;
	}
	
	str += ' )';
	
	this.lastStatement = str;
	return str;
};

/**
 * @access public
 */
SQLUtil.prototype.setSelectMode = function( mode )
{
	if ( mode != null )
		this.selectMode = mode;
		
	return this.getSelectMode();
};

/**
 * @access public
 */
SQLUtil.prototype.getSelectMode = function()
{
	return this.selectMode;
};

/**
 * @access public
 */
SQLUtil.prototype.getSelectStatement = function( table_elems, match_criteria, tables, operator )
{
	if ( table_elems == null || match_criteria == null || typeof( table_elems ) != "object" )
		return false;

	var i;
	var moreThanOne = 0;
	var str  = 'SELECT ' + ( ( this.selectMode != null )? this.selectMode + ' ' : '' );
	var keys = table_elems.getKeys();
	
	for ( i in keys )
	{
		if ( moreThanOne != 0 )
			str += ", ";
		
		str += keys[i];
		moreThanOne++;
	}
	
	str += ' FROM ';
	moreThanOne = 0;
	
	if ( tables == null )
		tables = [this.table];
	
	for ( i in tables )
	{
		if ( moreThanOne != 0 )
			str += ", ";
		
		str += tables[i];
		moreThanOne++;
	}
	
	if ( match_criteria != null )
	{
		if ( typeof( match_criteria ) == "object" )
		{
			var criteria = match_criteria.getKeys();
			
			moreThanOne = 0;
			str += " WHERE ";
			
			for ( i in criteria )
			{
				if ( moreThanOne != 0 )
					str += ( operator == null )? ' AND ' : ' ' +  operator +' ';
		
				str += criteria[i] + "=" + this.quote( match_criteria.get( criteria[i] ) );
				moreThanOne++;
			}
		}
		else
		{
			str += " WHERE " + match_criteria;
		}
	}
	
	this.lastStatement = str;
	return str;
};

/**
 * @access public
 */
SQLUtil.prototype.getSelectAllStatement = function()
{
	var str = 'SELECT * FROM ' + this.table;
	this.lastStatement = str;
	
	return str;
};
