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
 * @package html_table_selectable
 */
 
/**
 * Constructor
 *
 * @access public
 */
SelectableListItems = function( oListElement, bMultiple )
{
	SelectableElements.call( this, oListElement, bMultiple );
};


SelectableListItems.prototype = new SelectableElements();
SelectableListItems.prototype.constructor = SelectableListItems;
SelectableListItems.superclass = SelectableElements.prototype;

/**
 * @access public
 */
SelectableListItems.prototype.getItems = function()
{
	return this._htmlElement.getElementsByTagName( "LI" );
};

/**
 * @access public
 */
SelectableListItems.prototype.isItem = function( node )
{
	return ( ( node != null ) && ( node.tagName == "LI" ) );
};

/**
 * @access public
 */
SelectableListItems.prototype.getNext = function( el )
{
	var next = this._getFirstDescendant( el );

	if ( el != next )
		return next;
		
	next  = this._getNextSibling( el );
	var p = el.parentNode;
	
	while ( next == null )
	{
		while ( p != null && !this.isItem( p ) )
			p = p.parentNode;
			
		if ( p == null )
			return null;
			
		next = this._getNextSibling( p );
		p = p.parentNode;
	}
	
	return next;
};

/**
 * @access public
 */
SelectableListItems.prototype.getPrevious = function( el )
{
	var previous = this._getPreviousSibling( el );
	var p = el.parentNode;
	
	if ( previous == null )
	{
		while ( p != null && !this.isItem( p ) )
			p = p.parentNode;
			
		return p;
	}
	
	return this._getLastDescendant( previous );
};


// private methods

/**
 * @access private
 */
SelectableListItems.prototype._getNextSibling = function( el )
{
	var n = el.nextSibling;
	
	while ( n != null && !this.isItem( n ) )
		n = n.nextSibling;
		
	return n;
};

/**
 * @access private
 */
SelectableListItems.prototype._getFirstDescendant = function( el )
{
	var lis = el.getElementsByTagName( "LI" );
	
	if ( lis.length == 0 )
		return el;
		
	return lis[0];
};

/**
 * @access private
 */
SelectableListItems.prototype._getPreviousSibling = function( el )
{
	var p = el.previousSibling;
	
	while ( p != null && !this.isItem( p ) )
		p = p.previousSibling;
		
	return p;
};

/**
 * @access private
 */
SelectableListItems.prototype._getLastDescendant = function( el )
{
	var lis = el.getElementsByTagName( "LI" );
	
	if ( lis.length == 0 )
		return el;
		
	return lis[lis.length - 1];
};
