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
 * @package gui_button
 */
 
/**
 * Constructor
 *
 * @access public
 */
ScrollButton = function( oEl, oScrollContainer, nDir )
{
	this.Base = Base;
	this.Base();
	
	this.htmlElement = oEl;
	this.scrollContainer = oScrollContainer;
	this.dir = nDir;
	
	var oThis = this;
	oEl.attachEvent( "onmouseover", function ()
	{
		oThis.startScroll();
	} );
	oEl.attachEvent( "onmouseout", function ()
	{
		oThis.endScroll();
	} );
};


ScrollButton.prototype = new Base();
ScrollButton.prototype.constructor = ScrollButton;
ScrollButton.superclass = Base.prototype;

/**
 * @access public
 */
ScrollButton.prototype.startScroll = function()
{
	var oThis = this;
	this._interval = window.setInterval( function()
	{
		switch ( oThis.dir )
		{
			case 8:
				oThis.scrollContainer.scrollTop  -= ScrollButton.scrollAmount;
				break;
			
			case 2:
				oThis.scrollContainer.scrollTop  += ScrollButton.scrollAmount;
				break;
		
			case 4:
				oThis.scrollContainer.scrollLeft -= ScrollButton.scrollAmount;
				break;
			
			case 6:
				oThis.scrollContainer.scrollLeft += ScrollButton.scrollAmount;
				break;
		}
	}, ScrollButton.scrollIntervalPause );
};

/**
 * @access public
 */
ScrollButton.prototype.endScroll = function()
{
	if ( this._interval != null )
	{
		window.clearInterval( this._interval );
		delete this._interval;
	}
};


/**
 * @access public
 * @static
 */
ScrollButton.scrollIntervalPause = 100;

/**
 * @access public
 * @static
 */
ScrollButton.scrollAmount = 18;
