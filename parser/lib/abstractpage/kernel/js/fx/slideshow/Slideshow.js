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
Slideshow = function( slideshowname ) 
{
	this.Base = Base;
	this.Base();

	// Name of this object
	// (required if you want your slideshow to auto-play)
	// For example, "SLIDES1"
	this.name = slideshowname;

	// When we reach the last slide, should we loop around to start the
	// slideshow again?
	this.repeat = true;

	// Number of images to pre-fetch.
	// -1 = preload all images.
	//  0 = load each image is it is used.
	//  n = pre-fetch n images ahead of the current image.
	// I recommend preloading all images unless you have large
	// images, or a large amount of images.
	this.prefetch = -1;

	// IMAGE element on your HTML page.
	// For example, document.images.SLIDES1IMG
	this.image;

	// TEXTAREA element on your HTML page.
	// For example, document.SLIDES1FORM.SLIDES1TEXT
	this.textarea;

	// Milliseconds to pause between slides
	this.timeout = 3000;

	this.slides    = new Array();
	this.current   = 0;
	this.timeoutid = 0;
};

Slideshow.prototype = new Base();
Slideshow.prototype.constructor = Slideshow;
Slideshow.superclass = Base.prototype;

/**
 * Add a slide to the slideshow.
 * For example:
 * SLIDES1.add_slide(new Slide("s1.jpg", "link.html"))
 *
 * @access public
 */
Slideshow.prototype.add_slide = function( slide ) 
{
	// If this version of JavaScript does not allow us to
  	// change images, then we can't do the slideshow.
  	if ( !document.images ) 
		return; 

  	var i = this.slides.length;

  	// Prefetch the slide image if necessary.
  	if ( this.prefetch == -1 ) 
    	slide.load();

  	this.slides[i] = slide;
};

/**
 * This function configures the slideshow to use a textarea to display
 * the slideshow text.
 *
 * @access public
 */
Slideshow.prototype.set_textarea = function( textareaobject ) 
{
	// Set the "textarea" property of the slideshow object.
  	this.textarea = textareaobject;

  	// Initialize the text in the textarea.
  	this.display_text();
};

/**
 * This function configures the slideshow and tells it which image
 * needs to be updated.
 *
 * @access public
 */
Slideshow.prototype.set_image = function( imageobject ) 
{
	// If this version of JavaScript does not allow us to
	// change images, then we can't do the slideshow.
  	if ( !document.images )
    	return;

  	// Set the "image" property of the slideshow object.
  	this.image = imageobject;
};

/**
 * This function calls the hotlink() method for the current slide.
 *
 * @access public
 */
Slideshow.prototype.hotlink = function() 
{
  	this.slides[ this.current ].hotlink();
};

/**
 * This function updates the slideshow image on the page.
 *
 * @access public
 */
Slideshow.prototype.update = function() 
{
  	// Make sure the slideshow has been initialized correctly
  	if ( !this._valid_image() ) 
		return;

	// Load the slide image if necessary.
  	this.slides[ this.current ].load();

  	// Pre-fetch the next slide image(s) if necessary.
  	if ( this.prefetch > 0 ) 
	{
    	for ( i = this.current + 1; i <= (this.current + this.prefetch ) && i < this.slides.length; i++ ) 
      		this.slides[i].load();
  	}

  	// Update the image.
  	this.image.src = this.slides[ this.current ].image.src;

  	// Update the text.
  	this.display_text();
};

/**
 * This function jumpts to the slide number you specify.
 * If you use slide number -1, then it jumps to the last slide.
 * You can use this to make links that go to a specific slide,
 * or to go to the beginning or end of the slideshow.
 *
 * Examples:
 * <a href="javascript:myslides.goto_slide(0)">First</a>
 * <a href="javascript:myslides.goto_slide(-1)">Last</a>
 * <a href="javascript:myslides.goto_slide(5)">Catching a Fish</a>
 *
 * @access public
 */
Slideshow.prototype.goto_slide = function( n ) 
{
	if ( n == -1 ) 
    	n = this.slides.length - 1;
  
  	if ( n < this.slides.length && n >= 0 ) 
    	this.current = n;
  
  	this.update();
};

/**
 * This function advances to the next slide.
 *
 * @access public
 */
Slideshow.prototype.next = function() 
{
	// Increment the image number.
  	if ( this.current < this.slides.length - 1 ) 
    	this.current++;
  	else if ( this.repeat ) 
    	this.current = 0;

  	this.update();
};

/**
 * This function goes to the previous slide.
 *
 * @access public
 */
Slideshow.prototype.previous = function() 
{
  	// Decrement the image number
  	if ( this.current > 0 ) 
    	this.current--;
	else if ( this.repeat ) 
    	this.current = this.slides.length - 1;

  	this.update();
};

/**
 * This function displays text in the textarea.
 *
 * @access public
 */
Slideshow.prototype.display_text = function( text ) 
{
  	// If a textarea has been specified,
  	// then this function changes the text displayed in it
  	if ( this.textarea ) 
	{
    	if ( text ) 
      		this.textarea.value = text;
		else 
     		this.textarea.value = this.slides[ this.current ].text;
  	}
};

/**
 * This function returns the text of the current slide
 *
 * @access public
 */
Slideshow.prototype.get_text = function() {

  return(this.slides[ this.current ].text);
};

/**
 * This function implements the automatically running slideshow.
 *
 * @access public
 */
Slideshow.prototype.play = function(timeout) {

  // Make sure we're not already playing
  this.pause();

  // If a new timeout was specified (optional)
  // set it here
  if (timeout) {
    this.timeout = timeout;
  }

  // After the timeout, call this.loop()
  this.timeoutid = setTimeout( this.name + "._loop()", this.timeout);
};

/**
 * This function stops the slideshow if it is automatically running.
 *
 * @access public
 */
Slideshow.prototype.pause = function() 
{
  	if ( this.timeoutid != 0 )
  	{
    	clearTimeout( this.timeoutid );
    	this.timeoutid = 0;
  	}
};

/**
 * Saves the position of the slideshow in a cookie,
 * so when you return to this page, the position in the slideshow
 * won't be lost.
 *
 * @access public
 */
Slideshow.prototype.save_position = function( cookiename ) 
{
	if ( !cookiename ) 
    	cookiename = this.name + '_slideshow';

  	document.cookie = cookiename + '=' + this.current;
};

/**
 * If you previously called slideshow_save_position(),
 * returns the slideshow to the previous state.
 */
Slideshow.prototype.restore_position = function( cookiename ) 
{
  	if ( !cookiename ) 
    	cookiename = this.name + '_slideshow';

  	var search = cookiename + "=";

  	if ( document.cookie.length > 0 ) 
	{
    	offset = document.cookie.indexOf( search );
    
		// if cookie exists
    	if ( offset != -1 ) 
		{ 
      		offset += search.length;
      
	  		// set index of beginning of value
      		end = document.cookie.indexOf( ";", offset );
      
	  		// set index of end of cookie value
      		if ( end == -1 ) 
				end = document.cookie.length;
      
	  		this.current = unescape( document.cookie.substring( offset, end ) );
      	}
   	}
};

/**
 * This function is not for use as part of your slideshow,
 * but you can call it to get a plain HTML version of the slideshow
 * images and text.
 *
 * You should copy the HTML and put it within a NOSCRIPT element, to
 * give non-javascript browsers access to your slideshow information.
 * This also ensures that your slideshow text and images are indexed
 * by search engines.
 *
 * @access public
 */
Slideshow.prototype.noscript = function() 
{
  	$html = "\n";

  	// Loop through all the slides in the slideshow.
  	for ( i = 0; i < this.slides.length; i++ ) 
	{
    	slide = this.slides[i];
    	$html += '<P>';

    	if ( slide.link ) 
      		$html += '<a href="' + slide.link + '">';

    	$html += '<img src="' + slide.src + '" ALT="slideshow image">';

    	if ( slide.link ) 
      		$html += '</a>';

    	if ( slide.text ) 
      		$html += "<BR>\n" + slide.text;

    	$html += '</P>' + "\n\n";
  	}

  	// Make the HTML browser-safe.
  	$html = $html.replace( /\&/g, "&amp;" );
  	$html = $html.replace( /</g,  "&lt;"  );
  	$html = $html.replace( />/g,  "&gt;"  );

  	return( '<pre>' + $html + '</pre>' );
};


// private methods

/**
 * This function is for internal use only.
 * This function gets called automatically by a JavaScript timeout.
 * It advances to the next slide, then sets the next timeout.
 *
 * @access private
 */
Slideshow.prototype._loop = function() 
{
  	// Go to the next image.
  	this.next( );

  	// Keep playing the slideshow.
  	this.play( );
};

/**
 * Returns 1 if a valid image has been set for the slideshow.
 *
 * @access private
 */
Slideshow.prototype._valid_image = function() 
{
  	if ( !this.image )
  	{
    	// Stop the slideshow.
    	this.pause;

    	// Display an error message
    	window.status = "Error: slideshow image not initialized for " + this.name;
        
    	return 0;
  	}
  	else 
	{
    	return 1;
  	}
};
