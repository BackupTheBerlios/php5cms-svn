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
 * @package html_form
 */
 
/**
 * Constructor
 *
 * @access public
 */
Form = function( formField ) 
{
	this.Base = Base;
	this.Base();
	
	for ( var name in this ) 
		formField[name] = this[name];
};


Form.prototype = new Base();
Form.prototype.constructor = Form;
Form.superclass = Base.prototype;

/**
 * @access public
 */
Form.prototype.hasValue = function( val ) 
{
	val = val + '';
		
	for ( var i = 0; i < this.length; i++ ) 
	{
		var t = this.options[i].value + '';
			
		if ( t == val ) 
			return true;
	}

	return false;
};

/**
 * @access public
 */
Form.prototype.getValue = function() 
{
	var selIndex = this.selectedIndex;
	
	if ( ( selIndex != 'undefined' ) && ( selIndex > -1 ) ) 
	{
		if ( this.options[selIndex].value != 'undefined' ) 
			return this.options[selIndex].value;
		
		if ( this.options[selIndex].text != 'undefined' ) 
			return this.options[selIndex].text;
	}

	return 'undefined';
};

/**
 * @access public
 */
Form.prototype.setTo = function( value ) 
{
	for ( var i = 0; i < this.length; i++ ) 
	{
		if ( this.options[i].text == value ) 
		{
			this.selectedIndex = i;
			return true;
		}
	}

	return false;
};

/**
 * @access public
 */
Form.prototype.moveSelectedTo = function( toField, keepSelected ) 
{
	if ( typeof( toField ) == 'string' ) 
		toField = document.getElementById( toField );
	
	if ( !toField ) 
		return false;
	
	var unsetArray = new Array();
	
	for ( var i = 0; i < this.length; i++ ) 
	{
		if ( this.options[i].selected ) 
		{
			var newOpt = new Option( this.options[i].text, this.options[i].value, false, false );
			toField.options[toField.length] = newOpt;
			unsetArray[unsetArray.length] = i;
		}
	}

	unsetArray.reverse();
	
	for ( var i = 0; i < unsetArray.length; i++ ) 
		this.options[unsetArray[i]] = null;

	return true;
};

/**
 * @access public
 */
Form.prototype.moveHashTo = function( toField, hash ) 
{
	if ( typeof( toField ) == 'string' ) 
		toField = document.getElementById( toField );
		
	if ( !toField ) 
		return false;
	
	var unsetArray = new Array();
	
	for ( var i = 0; i < this.length; i++ ) 
	{
		if ( typeof( hash[this.options[i].value] ) != 'undefined' ) 
		{
			var newOpt = new Option( this.options[i].text, this.options[i].value, false, false );
			toField.options[toField.length] = newOpt;
			unsetArray[unsetArray.length] = i;
		}
	}

	unsetArray.reverse();
	
	for ( var i = 0; i < unsetArray.length; i++ ) 
		this.options[unsetArray[i]] = null;

	return true;
};

/**
 * @access public
 */
Form.prototype.getAllKeys = function() 
{
	var ret = new Array();
	
	for ( var i = 0; i < this.options.length; i++ ) 
		ret[i] = this.options[i].value;

	return ret;
};

/**
 * @access public
 */
Form.prototype.prune = function() 
{
	this.options.length = 0;
};
