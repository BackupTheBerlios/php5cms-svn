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
 * @package fx
 */
 
/**
 * Constructor
 *
 * @access public
 */
SyncScroll = function()
{
	this.Base = Base;
	this.Base();
};


SyncScroll.prototype = new Base();
SyncScroll.prototype.constructor = SyncScroll;
SyncScroll.superclass = Base.prototype;

/**
 * Returns a function that is used in the event listener.
 *
 * @access public
 */
SyncScroll.getOnScrollFunction = function( oElement )
{
	return function ()
	{
		if ( ( oElement._scrollSyncDirection == "horizontal" ) || ( oElement._scrollSyncDirection == "both" ) )
			oElement.scrollLeft = event.srcElement.scrollLeft;
		
		if ( ( oElement._scrollSyncDirection == "vertical" ) || ( oElement._scrollSyncDirection == "both" ) )
			oElement.scrollTop = event.srcElement.scrollTop;
	}
};

/**
 * This function adds scroll syncronization for the fromElement to the toElement -
 * this means that the fromElement will be updated when the toElement is scrolled.
 *
 * @access public
 */
SyncScroll.addScrollSynchronization = function( fromElement, toElement, direction )
{
	SyncScroll.removeScrollSynchronization( fromElement );
	
	fromElement._syncScroll = SyncScroll.getOnScrollFunction( fromElement );
	fromElement._scrollSyncDirection = direction;
	fromElement._syncTo = toElement;
	toElement.attachEvent( "onscroll", fromElement._syncScroll );
};

/**
 * Removes the scroll synchronization for an element.
 *
 * @access public
 */
SyncScroll.removeScrollSynchronization = function( fromElement )
{
	if ( fromElement._syncTo != null )
		fromElement._syncTo.detachEvent( "onscroll", fromElement._syncScroll);

	fromElement._syncTo = null;
	fromElement._syncScroll = null;
	fromElement._scrollSyncDirection = null;
};
