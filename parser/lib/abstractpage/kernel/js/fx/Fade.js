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
Fade = function()
{
	this.Base = Base;
	this.Base();
};


Fade.prototype = new Base();
Fade.prototype.constructor = Fade;
Fade.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
Fade.fadeSteps = 4;

/**
 * @access public
 * @static
 */
Fade.fademsec  = 35;

/**
 * @access public
 * @static
 */
Fade.fadeArray = new Array();

/**
 * @access public
 * @static
 */
Fade.fade = function( el, fadeIn, steps, msec )
{
	if ( steps == null )
		steps = Fade.fadeSteps;
	
	if ( msec == null )
		msec = Fade.fademsec;
	
	if ( el.fadeIndex == null )
		el.fadeIndex = Fade.fadeArray.length;
	
	Fade.fadeArray[el.fadeIndex] = el;
	
	if ( el.fadeStepNumber == null )
	{
		if ( el.style.visibility == "hidden" )
			el.fadeStepNumber = 0;
		else
			el.fadeStepNumber = steps;
		
		if ( fadeIn )
			el.style.filter = "Alpha(Opacity=0)";
		else
			el.style.filter = "Alpha(Opacity=100)";
	}
			
	window.setTimeout( "Fade.repeatFade(" + fadeIn + "," + el.fadeIndex + "," + steps + "," + msec + ")", msec );
};

/**
 * @access public
 * @static
 */
Fade.repeatFade = function( fadeIn, index, steps, msec )
{	
	el = Fade.fadeArray[index];
	c  = el.fadeStepNumber;
	
	if ( el.fadeTimer != null )
		window.clearTimeout( el.fadeTimer );
	
	if ( ( c == 0 ) && ( !fadeIn ) )
	{
		el.style.visibility = "hidden";
		return;
	}
	else if ( ( c == steps ) && ( fadeIn ) )
	{
		el.style.filter = "";
		el.style.visibility = "visible";
		return;
	}
	else
	{
		( fadeIn )? c++ : c--;
		el.style.visibility = "visible";
		el.style.filter = "Alpha(Opacity=" + 100 * c / steps + ")";

		el.fadeStepNumber = c;
		el.fadeTimer = window.setTimeout( "Fade.repeatFade(" + fadeIn + "," + index + "," + steps + "," + msec + ")", msec );
	}
};
