<?php

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
 * This class provides a high level set of image manipulation tools.
 *
 * @package image
 */

class GenericImage extends PEAR
{ 
	/**
	 * @access public
	 */
	var $font;
	
	/**
	 * default text to display
	 * @access public
	 */
	var $msg = "undefined";
	
	/**
	 * @access public
	 */
	var $font_size = 18; 
	
	/**
	 * rotation in degrees
	 * @access public
	 */
	var $rot = 0;
	
	/**
	 * padding
	 * @access public
	 */
	var $pad = 0; 
	
	/**
	 * transparency set to on
	 * @access public
	 */
	var $transparent = true; 
	
	/**
	 * @access public
	 */
	var $interlace = false; 
	
	/**
	 * @access public
	 */
	var $alpha = 1;
	
	/**
	 * white text
	 * @access public
	 */
	var $red = 0x23;
	
	/**
	 * @access public
	 */
	var $grn = 0x3e; 
	
	/**
	 * @access public
	 */
	var $blu = 0x75; 

	/**
	 * @access public
	 */	
	var $bg_red = 255;
	
	/**
	 * @access public
	 */
	var $bg_grn = 255; 
	
	/**
	 * @access public
	 */
	var $bg_blu = 255; 
    
	/**
	 * @access public
	 */ 
    var $supported = array(
		"png",
		"jpeg",
		"jpg"
	); 
	
	
	/**
	 * @access public
	 */ 
    function fg( $r, $g, $b ) 
	{ 
        $this->red = $r; 
        $this->grn = $g; 
        $this->blu = $b; 
    } 

	/**
	 * @access public
	 */ 
    function bg( $r, $g, $b ) 
	{ 
		$this->bg_red = $r; 
		$this->bg_grn = $g; 
		$this->bg_blu = $b; 
	} 

	/**
	 * @access public
	 */ 
    function load( $file ) 
	{ 
		list( $base, $format ) = preg_split( "/\./", $file, 2 ); 
        
        if ( $format == "jpg" ) 
		{ 
            $this->image  = ImageCreateFromJPEG( $file ); 
            $this->format = "jpeg"; 
        } 
		else if ( $format == "png" ) 
		{ 
            $this->image  = ImageCreateFromPNG( $file ); 
            $this->format = "png"; 
        } 
		else if ( $format == "gif" ) 
		{ 
            $this->image  = ImageCreateFromPNG( $file ); 
            $this->format = "gif"; 
        } 
		else 
		{ 
			return PEAR::raiseError( "Unsupported filetype: $format." );
        } 

        imagealphablending( $this->image, $this->alpha ); 
		return true;
    } 
    
	/**
	 * @access public
	 */ 
    function watermark( $text ) 
	{ 
        $this->msg         = $text; 
        $this->font_size   = 18; 
        $this->transparent = 0; 
         
        $this->red = 50;
        $this->grn = 50; 
		$this->blu = 50; 
		  
        $this->offset_y = imagesy( $this->image ) - 8; 
        $this->offset_x = 22; 
		
        $this->renderFont(); 
         
        $this->red = 220;
		$this->grn = 220; 
		$this->blu = 220; 
		  
        $this->offset_y = imagesy( $this->image ) - 10; 
        $this->offset_x = 20; 
		
        $this->renderFont(); 
     
    } 

	/**
	 * @access public
	 */ 
    function resizeHeight( $height ) 
	{ 
        $width = ( ( $height / imagesy( $this->image ) ) * imagesx( $this->image ) ); 
        $this->resize( $width, $height ); 
    } 
     
	/**
	 * @access public
	 */ 
    function resizeWidth( $width ) 
	{ 
        $height = ( ( $width / imagesx( $this->image ) ) * imagesy( $this->image ) ); 
        $this->resize( $width, $height ); 
    } 
     
	/**
	 * @access public
	 */ 
    function resize( $width, $height ) 
	{ 
        $temp = imagecreate( $width, $height ); 
        imagecopyresized( $temp, $this->image, 0, 0, 0, 0, $width, $height, imagesx( $this->image ), imagesy( $this->image ) ); 
        $this->image = $temp;
    } 
     
	/**
	 * @access public
	 */ 
    function newTextImage() 
	{ 
        // determine font height
        $bounds = array(); 
		$bounds = ImageTTFBBox( $this->font_size, $this->rot, $this->font, "d" ); 
         
		if ( $this->rot < 0 ) 
			$font_height = abs( $bounds[7]-$bounds[1] );         
		else if ($this->rot > 0)
			$font_height = abs( $bounds[1]-$bounds[7] ); 
		else
			$font_height = abs( $bounds[7]-$bounds[1] ); 
     
        // determine bounding box
		$bounds = ImageTTFBBox( $this->font_size, $this->rot, $this->font, $this->msg ); 

		if ( $this->rot < 0 ) 
		{ 
			$width  = abs( $bounds[4] - $bounds[0] ); 
			$height = abs( $bounds[3] - $bounds[7] );
			
			$this->offset_y = $font_height; 
			$this->offset_x = 0; 
      	} 
		else if ( $this->rot > 0 ) 
		{ 
			$width  = abs( $bounds[2] - $bounds[6] ); 
			$height = abs( $bounds[1] - $bounds[5] );
			
			$this->offset_y = abs( $bounds[7] - $bounds[5] ) + $font_height; 
			$this->offset_x = abs( $bounds[0] - $bounds[6] ); 
		} 
		else 
		{ 
			$width  = abs( $bounds[4] - $bounds[6] ); 
			$height = abs( $bounds[7] - $bounds[1] );
			
			$this->offset_y = $font_height; 
			$this->offset_x = 0; 
		} 
		
		$this->image = imagecreate( $width + ( $this->pad * 2 ) + 10, $height + ( $this->pad * 2 ) + 1 ); 
    } 

	/**
	 * @access public
	 */ 
    function renderFont() 
	{ 
		$background = ImageColorAllocate( $this->image, $this->bg_red, $this->bg_grn, $this->bg_blu ); 
		$foreground = ImageColorAllocate( $this->image, $this->red,    $this->grn,    $this->blu    ); 
         
        if ( $this->transparent ) 
            ImageColorTransparent( $this->image, $background ); 
         
		// render it
		ImageTTFText( $this->image, $this->font_size, $this->rot, $this->offset_x + $this->pad, $this->offset_y + $this->pad, $foreground, $this->font, $this->msg );          
    } 

	/**
	 * @access public
	 */      
    function render() 
	{ 
        ImageInterlace( $this->image, $this->interlace ); 
         
        // output PNG object
        if ( $this->format == 'gif' ) 
		{ 
            header( "Content-type: image/png" ); 
            imagePNG( $this->image ); 
        } 
		else if ( $this->format == 'png' ) 
		{ 
            header( "Content-type: image/png" ); 
            imagePNG( $this->image ); 
        } 
		else if ( $this->format == 'jpeg' ) 
		{ 
            header( "Content-type: image/jpeg" ); 
            imageJPEG( $this->image ); 
        } 
		
        imageDestroy( $this->image ); 
    } 

	/**
	 * @access public
	 */ 
    function isSupported( $filetype ) 
	{ 
        if( in_array( $filetype, $this->supported ) ) 
            return true; 
        else 
            return false; 
    }
	
	/**
	 * Example:
	 * $img=imagecreatefromgif($src);
	 * GenericImage::makeColoursGrey( $img, $col );
	 * header( "Content-Type: image/gif" );
	 * ImageGif( $img );
	 *
	 * @access public
	 * @static
	 */
	function makeColoursGrey( $im, $col )
	{
  		$total = ImageColorsTotal( $im );
  
  		for ( $i = 0; $i < $total; $i++ )
		{
     		$old = ImageColorsForIndex( $im, $i );
     
     		// trying to keep proper saturation when converting
     		$commongrey = (int)( $old[red] + $old[green] + $old[blue] ) / 3;
     
	 		if ( !$col )
				ImageColorSet( $im, $i, $commongrey, $commongrey, $commongrey );
     		else if ( $col == 1 )
				ImageColorSet( $im, $i, $commongrey, 0, 0 );
     		else if ( $col == 2 )
				ImageColorSet( $im, $i, 0, $commongrey, 0 );
     		else if ( $col == 3 )
				ImageColorSet( $im, $i, 0, 0, $commongrey );
		}
	}
	
    /**
     * Calculate a lighter (or darker) version of a color.
     *
     * @param string $color    An HTML color, e.g.: #ffffcc.
     *
     * @return string  A modified HTML color.
	 * @access public
	 * @static
     */
    function modifyColor( $color, $factor = 0x11 )
    {
        $r = hexdec( substr( $color, 1, 2 ) );
        $g = hexdec( substr( $color, 3, 2 ) );
        $b = hexdec( substr( $color, 5, 2 ) );

        if ( $r >= $g && $r >= $b ) 
		{
            $g = $g / $r;
            $b = $b / $r;
            $r = $r + $factor;
            $g = floor( $g * $r );
            $b = floor( $b * $r );
        } 
		else if ( $g >= $r && $g >= $b ) 
		{
            $r = $r / $g;
            $b = $b / $g;
            $g = $g + $factor;
            $r = floor( $r * $g );
            $b = floor( $b * $g );
        } 
		else 
		{
            $r = $r / $b;
            $g = $g / $b;
            $b = $b + $factor;
            $r = floor( $r * $b );
            $g = floor( $g * $b );
        }

        return '#' . str_pad( dechex( min( $r, 255 ) ), 2, '0', STR_PAD_LEFT ) . str_pad( dechex( min( $g, 255 ) ), 2, '0', STR_PAD_LEFT ) . str_pad( dechex( min( $b, 255 ) ), 2, '0', STR_PAD_LEFT );
    }
} // END OF GenericImage 

?>
