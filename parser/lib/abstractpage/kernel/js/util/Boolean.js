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
 * @package util
 */
 
/**
 * @param  mixed
 * @access public
 * @static
 */
Boolean.booleanize = function( val )
{
	if ( typeof val == "boolean" )
		return val;
		
	if ( val == true    ||
		 val == "true"  ||
		 val == "yes"   ||
		 val == "on"    ||
		 val == "t"     ||
		 val == 1 )
	{
		return new Boolean( true );
	}
	else if ( val == false   ||
		 val == "false" ||
		 val == "no"    ||
		 val == "off"   ||
		 val == "f"     ||
		 val == 0 ) 
	{
		return new Boolean( false );
	}
	else
	{
		return null;
	}
};
