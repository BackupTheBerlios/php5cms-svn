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
TabNext = function()
{
	this.Base = Base;
	this.Base();
};


TabNext.prototype = new Base();
TabNext.prototype.constructor = TabNext;
TabNext.superclass = Base.prototype;

/**
 * Pass an input object, returns index in form.elements[] for the object.
 * Returns -1 if error.
 *
 * @access public
 */
TabNext.prototype.getElementIndex = function( obj )
{
	var theform = obj.form;
	
	for ( var i = 0; i < theform.elements.length; i++ )
	{
		if ( obj.name == theform.elements[i].name )
			return i;
	}
	
	return -1;
};

/**
 * Pass an form input object. Will focus() the next field in the form
 * after the passed element.
 *   a) Will not focus to hidden or disabled fields
 *   b) If end of form is reached, it will loop to beginning
 *   c) If it loops through and reaches the original field again without
 *      finding a valid field to focus, it stops
 *
 * @access public
 */
TabNext.prototype.tabNext = function( obj )
{
	// Sun's onFocus() is messed up
	if ( navigator.platform.toUpperCase().indexOf( "SUNOS" ) != -1 )
	{
		obj.blur();
		return;
	}

	var theform = obj.form;
	var i = this.getElementIndex(obj);
	var j = i + 1;
	
	if ( j >= theform.elements.length )
		j = 0;
		
	if ( i == -1 )
		return;
		
	while ( j != i )
	{
		if ( ( theform.elements[j].type != "hidden" ) && ( theform.elements[j].name != theform.elements[i].name ) && ( !theform.elements[j].disabled ) )
		{
			theform.elements[j].focus();
			break;
		}
		
		j++;
		
		if ( j >= theform.elements.length )
			j = 0;
	}
};
