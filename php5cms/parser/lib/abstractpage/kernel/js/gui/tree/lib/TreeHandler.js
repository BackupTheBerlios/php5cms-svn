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
 * Constructor
 *
 * @access public
 */
TreeHandler = function()
{
	this.Base = Base;
	this.Base();
};


TreeHandler.prototype = new Base();
TreeHandler.prototype.constructor = TreeHandler;
TreeHandler.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
TreeHandler.idCounter = 0;

/**
 * @access public
 * @static
 */
TreeHandler.idPrefix = "tree-object-";

/**
 * @access public
 * @static
 */
TreeHandler.all = {};

/**
 * @access public
 * @static
 */
TreeHandler.behavior = null;

/**
 * @access public
 * @static
 */
TreeHandler.selected   = null;

/**
 * Note: should be part of tree, not handler
 * @access public
 * @static
 */
TreeHandler.onSelect = null;

/**
 * @access public
 * @static
 */
TreeHandler.getId = function()
{
	return this.idPrefix + this.idCounter++;
};

/**
 * @access public
 * @static
 */
TreeHandler.toggle = function( oItem )
{
	this.all[oItem.id.replace( '-plus', '' )].toggle();
};

/**
 * @access public
 * @static
 */
TreeHandler.select = function( oItem )
{
	this.all[oItem.id.replace( '-icon', '' )].select();
};

/**
 * @access public
 * @static
 */
TreeHandler.focus = function( oItem )
{
	this.all[oItem.id.replace( '-anchor', '' )].focus();
};

/**
 * @access public
 * @static
 */
TreeHandler.blur = function( oItem )
{
	this.all[oItem.id.replace( '-anchor', '' )].blur();
};

/**
 * @access public
 * @static
 */
TreeHandler.keydown = function( oItem, e )
{
	return this.all[oItem.id].keydown( e.keyCode );
};

/**
 * @access public
 * @static
 */
TreeHandler.insertHTMLBeforeEnd = function( oElement, sHTML )
{
	if ( oElement.insertAdjacentHTML != null )
	{
		oElement.insertAdjacentHTML( "BeforeEnd", sHTML )
		return;
	}
		
	var df;	// DocumentFragment
	var r = oElement.ownerDocument.createRange();
	r.selectNodeContents( oElement );
	r.collapse( false );
	df = r.createContextualFragment( sHTML );
	oElement.appendChild( df );
};
