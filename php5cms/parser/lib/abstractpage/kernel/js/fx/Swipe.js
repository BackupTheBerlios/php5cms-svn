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
Swipe = function()
{
	this.Base = Base;
	this.Base();
};


Swipe.prototype = new Base();
Swipe.prototype.constructor = Swipe;
Swipe.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
Swipe.swipeSteps = 4;

/**
 * @access public
 * @static
 */
Swipe.swipemsec = 25;

/**
 * @access public
 * @static
 */
Swipe.swipeArray = new Array();

/**
 * @access public
 * @static
 */
Swipe.swipe = function( el, dir, steps, msec )
{
	if ( steps == null )
		steps = Swipe.swipeSteps;
	
	if ( msec == null )
		msec = Swipe.swipemsec;
	
	if ( el.swipeIndex == null )
		el.swipeIndex = Swipe.swipeArray.length;
	
	if ( el.swipeTimer != null )
		window.clearTimeout( el.swipeTimer );
		
	Swipe.swipeArray[el.swipeIndex] = el;
	el.style.clip = "rect(-99999, 99999, 99999, -99999)";

	if ( el.swipeCounter == null )
	{
		el.orgLeft   = el.offsetLeft;
		el.orgTop    = el.offsetTop;
		el.orgWidth  = el.offsetWidth;
		el.orgHeight = el.offsetHeight;
	}
	else if ( el.swipeCounter == 0 )
	{
		el.orgLeft   = el.offsetLeft;
		el.orgTop    = el.offsetTop;
		el.orgWidth  = el.offsetWidth;
		el.orgHeight = el.offsetHeight;
	}
	
	el.style.left = el.orgLeft;
	el.style.top  = el.orgTop;
	
	el.swipeCounter = steps;
	el.style.clip   = "rect(0,0,0,0)";
			
	window.setTimeout( "Swipe.repeat(" + dir + "," + el.swipeIndex + "," + steps + "," + msec + ")", msec );
};

/**
 * @access public
 * @static
 */
Swipe.repeat = function( dir, index, steps, msec )
{
	el = Swipe.swipeArray[index];
	
	var left   = el.orgLeft;
	var top    = el.orgTop;
	var width  = el.orgWidth;
	var height = el.orgHeight;
	
	if ( el.swipeCounter == 0 )
	{
		el.style.clip = "rect(-99999, 99999, 99999, -99999)";
		return;
	}
	else
	{
		el.swipeCounter--;
		el.style.visibility = "visible";
		
		switch ( dir )
		{
			case 2:
				el.style.clip = "rect(" + height*el.swipeCounter / steps + "," + width + "," + height + "," + 0 + ")";
				el.style.top = top - height * el.swipeCounter / steps;
				break;
				
			case 8:
				el.style.clip = "rect(" + 0 + "," + width + "," + height * ( steps - el.swipeCounter ) / steps + "," + 0 + ")";
				el.style.top = top + height * el.swipeCounter / steps;
				break;
				
			case 6:
				el.style.clip = "rect(" + 0 + "," + width + "," + height + "," + width * ( el.swipeCounter ) / steps + ")";
				el.style.left = left - width * el.swipeCounter / steps;
				break;
				
			case 4:
				el.style.clip = "rect(" + 0 + "," + width * ( Swipe.swipeSteps - el.swipeCounter ) / steps + "," + height + "," + 0 + ")";
				el.style.left = left + width * el.swipeCounter / steps;
				break;
		}
		
		el.swipeTimer = window.setTimeout( "Swipe.repeat(" + dir + "," + index + "," + steps + "," + msec + ")", msec );
	}
};

/**
 * @access public
 * @static
 */
Swipe.hideSwipe = function( el )
{
	window.clearTimeout( el.swipeTimer );
	
	el.style.visibility = "hidden";
	el.style.clip       = "rect(-99999, 99999, 99999, -99999)";
	el.swipeCounter     = 0;
};
