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
AutoComplete = function()
{
	this.Base = Base;
	this.Base();
};


AutoComplete.prototype = new Base();
AutoComplete.prototype.constructor = AutoComplete;
AutoComplete.superclass = Base.prototype;

/**
 * @access public
 */
AutoComplete.prototype.complete = function( field, select, property, forcematch )
{
	var found = false;
	
	for ( var i = 0; i < select.options.length; i++ )
	{
		if ( select.options[i][property].indexOf( field.value ) == 0 )
		{
			found = true;
			break;
		}
	}
	
	if ( found )
		select.selectedIndex = i;
	else
		select.selectedIndex = -1;
		
	if ( field.createTextRange )
	{
		if ( forcematch && !found )
		{
			field.value = field.value.substring( 0, field.value.length - 1 ); 
			return;
		}
		
		var cursorKeys = "8;46;37;38;39;40;33;34;35;36;45;";
		
		if ( cursorKeys.indexOf( event.keyCode + ";" ) == -1 )
		{
			var r1 = field.createTextRange();
			var oldValue = r1.text;
			var newValue = found? select.options[i][property] : oldValue;
			
			if ( newValue != field.value )
			{
				field.value = newValue;
				var rNew = field.createTextRange();
				rNew.moveStart( 'character', oldValue.length );
				rNew.select();
			}
		}
	}
};
