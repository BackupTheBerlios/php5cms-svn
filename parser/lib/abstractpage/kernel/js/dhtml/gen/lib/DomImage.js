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
DomImage = function()
{
	this.Base = Base;
	this.Base();
};


DomImage.prototype = new Base();
DomImage.prototype.constructor = DomImage;
DomImage.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
DomImage.imgs = new Array();

/**
 * @access public
 * @static
 */
DomImage.done = 0;

/**
 * @access public
 * @static
 */
DomImage.error = 0;


/**
 * @access public
 * @static
 */
DomImage.addImg = function( src )
{
	var imgs = DomImage.imgs;
	var max  = imgs.length;
	DomImage.queued++;
	
	imgs[max] = new Image();
	imgs[max].loaded  = false;
	
	imgs[max].onload = function()
	{
		this.loaded = false;
		DomImage.done++;
	}
	imgs[max].onerror = function()
	{
		DomImage.done++;
		DomImage.error++;
	}
	
	imgs[max].src = src;
	return imgs[max];
};

/**
 * @access public
 * @static
 */
DomImage.onImgLoaded = function()
{
	// overload
};

/**
 * @access public
 * @static
 */
DomImage.check = function( aredone )
{
	var imgs = DomImage.imgs;
	var max  = imgs.length;
	
	if ( DomImage.onload )
	{
		if ( aredone != DomImage.done )
			DomImage.onImgLoaded();
			
		if ( DomImage.done < max )
			setTimeout( 'DomImage.check(' + DomImage.done + ')', 20 );
		else
			setTimeout( 'DomImage.onload()', 500 );
	}
};

/**
 * @access public
 * @static
 */
DomImage.html = function( imgObj, w, h )
{
	var w = w? w : imgObj.width;
	var h = h? h : imgObj.height;
	var s = '';
	
	if ( w != null )
		s += ' width=' + w;
		
	if ( h != null )
		s += ' height=' + h;
		
	return '<img src="'+imgObj.src+'"'+s+' border=0>';
};
