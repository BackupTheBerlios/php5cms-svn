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
 * @package fx_slideshow
 */
 
/**
 * Constructor
 *
 * @access public
 */
Slide = function( src, link, text, window, attr ) 
{
	this.Base = Base;
	this.Base();

	// Image URL
	this.src = src;

	// Link URL
	this.link = link;

	// Text to display
	this.text = text;

	// Name of the target window ("_blank")
	this.window = window;

	// Attributes for the target window:
	// width=n,height=n,resizable=yes or no,scrollbars=yes or no,
	// toolbar=yes or no,location=yes or no,directories=yes or no,
	// status=yes or no,menubar=yes or no,copyhistory=yes or no
	// Example: "width=200,height=300"
	this.attr = attr;

	// Create an image object for the slide
	if ( document.images ) 
		this.image = new Image();
};

Slide.prototype = new Base();
Slide.prototype.constructor = Slide;
Slide.superclass = Base.prototype;


/**
 * This function loads the image for the slide
 *
 * @access public
 */
Slide.prototype.load = function() 
{
  	if ( !document.images ) 
		return; 

  	if ( !this.image.src ) 
    	this.image.src = this.src;
};

/**
 * This function jumps to the slide's link.
 * If a window was specified for the slide, then it opens a new window.
 *
 * @access public
 */
Slide.prototype.hotlink = function() 
{
	if ( this.window ) 
	{
		// If window attributes are specified, use them to open the new window.
		if ( this.attr ) 
		{
      		window.open( this.link, this.window, this.attr );
    	} 
		else 
		{
      		// If window attributes are not specified, do not use them
      		// (this will copy the attributes from the originating window).
      		window.open( this.link, this.window );
    	}
  	} 
	else 
	{
    	// Open the hotlink in the current window.
		location.href = this.link;
  	}
};
