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
 * @package peer
 */
 
/**
 * Constructor
 *
 * @access public
 */
ChmodSum = function()
{
	this.Base = Base;
	this.Base();
};


ChmodSum.prototype = new Base();
ChmodSum.prototype.constructor = ChmodSum;
ChmodSum.superclass = Base.prototype;

/**
 * Input array with booleans:
 * [
 *		[read, write, execute], // owner
 *		[read, write, execute], // group
 *		[read, write, execute]  // other
 * ]
 *
 * @access public
 */
ChmodSum.prototype.get = function( arr, symbolic )
{
	if ( arr == null || typeof( arr ) != "object" )
		return false;
	
	var i;	
	var owner = symbolic? "-" : 0;
	var group = symbolic? "-" : 0;
	var other = symbolic? "-" : 0;
	
	for ( i in arr[0] )
	{
		if ( arr[0][i] )
			owner += symbolic? ChmodSum.smbolicMap[i] : ChmodSum.valueMap[i];
	}
		
	for ( i in arr[1] )
	{
		if ( arr[1][i] )
			group += symbolic? ChmodSum.smbolicMap[i] : ChmodSum.valueMap[i];
	}
		
	for ( i in arr[2] )
	{
		if ( arr[2][i] )
			other += symbolic? ChmodSum.smbolicMap[i] : ChmodSum.valueMap[i];
	}
	
	return ( ( symbolic? "-" : "" ) + owner + group + other );
};


/**
 * @access public
 * @static
 */
ChmodSum.smbolicMap = ["r","w","x"];

/**
 * @access public
 * @static
 */
ChmodSum.valueMap = [4,2,1];
