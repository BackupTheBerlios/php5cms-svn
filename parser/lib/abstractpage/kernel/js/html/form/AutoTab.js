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
AutoTab = function()
{
	this.Base = Base;
	this.Base();
};


AutoTab.prototype = new Base();
AutoTab.prototype.constructor = AutoTab;
AutoTab.superclass = Base.prototype;

/**
 * @access public
 */
AutoTab.prototype.next = function( obj, event, len, next_field )
{
	if ( event == "down" )
	{
		AutoTab.field_length = obj.value.length;
	}
	else if ( event == "up" )
	{
		if ( obj.value.length != AutoTab.field_length )
		{
			AutoTab.field_length = obj.value.length;
			
			if ( AutoTab.field_length == len )
				next_field.focus();
		}
	}
};


/**
 * @access public
 * @static
 */
AutoTab.field_length = 0;
