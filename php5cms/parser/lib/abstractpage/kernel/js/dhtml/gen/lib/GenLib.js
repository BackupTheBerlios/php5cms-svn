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
 * @package dhtml_gen_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
GenLib = function()
{
	this.Base = Base;
	this.Base();
};


GenLib.prototype = new Base();
GenLib.prototype.constructor = GenLib;
GenLib.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
GenLib.loaded = false;

/**
 * @access public
 * @static
 */
GenLib.onload = null;

/**
 * @access public
 * @static
 */
GenLib.path = '';

/**
 * @access public
 * @static
 */
GenLib.all = [];

/**
 * @access public
 * @static
 */
GenLib.elm = document;

/**
 * @access public
 * @static
 */
GenLib.onloadBackup = window.onload;


/**
 * @access public
 * @static
 */
GenLib.containerOf = function( el )
{
	if ( !el )
		return null;
		
	while ( !el.domLayer && el.parentNode && el.parentNode != el )
		el = el.parentNode;
		
	return el.domLayer? ( el.domLayer.eventListeners? el.domLayer : null ) : null;
};

/**
 * @access public
 * @static
 */
GenLib.removeElement = function( a, el )
{
	for ( var i = 0; i < a.length; i++ )
	{
		if ( a[i] == el )
		{
			if ( a.splice )
				a.splice( i, 1 );
			else
				a = a.slice( 0, i ).concat( a.slice( i + 1 ) );
			
			break;
		}
	}
	
	return a;
};

/**
 * @access public
 * @static
 */
GenLib.between = function( x, n, y )
{
	return ( Math.min( x, y ) < n && n < Math.max( x, y ) );
};
