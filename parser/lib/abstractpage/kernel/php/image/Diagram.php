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
|         Diogo Resende <diogo@ect-ua.com>                             |
+----------------------------------------------------------------------+
*/


/**
 * Diagram Class
 *
 * Offers you the ability to create diagram graphs.
 *
 * @package image
 */

class Diagram extends PEAR
{
	/**
	 * @access public
	 */
	var $data = array();
	
	/**
	 * @access public
	 */
	var $bgcolor = array( 255, 255, 255 );
	
	/**
	 * @access public
	 */
    var $bordercolor = array( 100, 100, 100 );
	
	/**
	 * @access public
	 */
    var $borderwidth = 1;
	
	/**
	 * @access public
	 */
    var $rect_bordercolor = array( 170, 170, 170 );
	
	/**
	 * @access public
	 */
    var $rect_bgcolor = array( 200, 200, 200 );
	
	/**
	 * @access public
	 */
    var $fontcolor = array( 0, 0, 0 );
	
	/**
	 * @access public
	 */
    var $font = 2;
	
	/**
	 * @access public
	 */
    var $fontwidth = 0;
	
	/**
	 * @access public
	 */
    var $fontheight = 0;
	
	/**
	 * @access public
	 */
    var $padding = 10;
	
	/**
	 * @access public
	 */
    var $inpadding = 5;
	
	/**
	 * @access public
	 */
    var $spacepadding = 5;
	
	/**
	 * Note: 0 (opaque) to 127 (transparent)
	 * @access public
	 */
    var $alpha = 0;
	
	/**
	 * @access public
	 */
    var $leftoffset = 0;
    
	
    /**
     * Set diagram data. Should be an indexed array [of arrays ..]
     *
     * @param  array  $data
	 * @access public
     */
    function setData( $data )
    {
		if ( is_array( $data ) )
		{
			$this->data = $data;
			$this->leftoffset = 0;
			
			return true;
		}
		else
		{
			return false;
		}
	}
    
	/**
	 * @param  int  r
	 * @param  int  g
	 * @param  int  b
	 * @access public
	 */
    function setBackgroundColor( $r, $g, $b )
    {
      	$this->bgcolor = array( $r, $g, $b );
    }
	
	/**
	 * @param  int  r
	 * @param  int  g
	 * @param  int  b
	 * @access public
	 */
    function setBorderColor( $r, $g, $b )
    {
      	$this->bordercolor = array( $r, $g, $b );
    }
	
	/**
	 * @param  int  r
	 * @param  int  g
	 * @param  int  b
	 * @access public
	 */
    function setBorderWidth( $n )
    {
      	$this->borderwidth = ( $n < 0? 0 : (int)$n );
    }
	
	/**
	 * @param  int  r
	 * @param  int  g
	 * @param  int  b
	 * @access public
	 */
    function setRectangleBackgroundColor( $r, $g, $b )
    {
      	$this->rect_bgcolor = array( $r, $g, $b );
    }
	
	/**
	 * @param  int  r
	 * @param  int  g
	 * @param  int  b
	 * @access public
	 */
    function setRectangleBorderColor( $r, $g, $b )
    {
      	$this->rect_bordercolor = array( $r, $g, $b );
    }

	/**
	 * @param  int  r
	 * @param  int  g
	 * @param  int  b
	 * @access public
	 */	
    function setFontColor( $r, $g, $b )
    {
		$this->fontcolor = array( $r, $g, $b );
    }
	
	/**
	 * @param  string  font
	 * @access public
	 */
    function setFont( $font )
    {
		$this->font = $font;
    }
	
	/**
	 * @param  int     padding
	 * @access public
	 */
    function setPadding( $p )
    {
		$this->padding = (int)$p;
    }
	
	/**
	 * @param  int     padding
	 * @access public
	 */
    function setInPadding( $p )
    {
		$this->inpadding = (int)$p;
    }
	
	/**
	 * @param  int     spacing
	 * @access public
	 */
    function setSpacing( $p )
    {
		$this->spacepadding = (int)$p;
    }

	/**
	 * @param  string  file
	 * @access public
	 */
    function draw( $file = "" )
    {
		if ( count( $this->data ) == 0 )
			return;

		$arrk = array_keys( $this->data );
		$this->fontwidth  = imagefontwidth( $this->font );
		$this->fontheight = imagefontheight( $this->font );
		$maxw = $this->_getMaxWidth( $this->data );

		$w = $maxw + ( 2 * $this->padding ) + 1;
		$h = $this->_getMaxDeepness( $this->data );
		$h = ( 2 * $this->padding ) +
			 ( ( $this->fontheight + ( 2 * $this->inpadding ) ) * $h ) +
			 ( ( 2 * $this->spacepadding ) * ( $h - 1 ) ) + 1;

		$this->im = imagecreatetruecolor( $w, $h );
      
		// background color
		$this->_allocateColor( "im_bgcolor", $this->bgcolor, false );
		imagefilledrectangle( $this->im, 0, 0, $w, $h, $this->im_bgcolor );
		
		if ( $this->borderwidth > 0 )
      	{
        	$this->_allocateColor( "im_bordercolor", $this->bordercolor );
        
			for ( $i = 0; $i < $this->borderwidth; $i++ )
          		imagerectangle( $this->im, $i, $i, $w - 1 - $i, $h - 1 - $i, $this->im_bordercolor );
      	}
      
      	// allocate colors
      	$this->_allocateColor( "im_rect_bgcolor",     $this->rect_bgcolor     );
      	$this->_allocateColor( "im_rect_bordercolor", $this->rect_bordercolor );
      	$this->_allocateColor( "im_fontcolor",        $this->fontcolor        );
      
      	// draw all data
      	$this->_drawData( $this->data[$arrk[0]], $this->padding );
      
      	// draw 1st square
      	$rw = ( $this->fontwidth * strlen( $arrk[0] ) ) + ( 2 * $this->inpadding );
      	$x1 = round( ( $w - $rw ) / 2 );
      	$y1 = $this->padding;
      	$x2 = $x1 + $rw;
      	$y2 = $y1 + ( 2 * $this->inpadding ) + $this->fontheight;
      	$this->_rectangle( $x1, $y1, $x2, $y2, $this->im_rect_bordercolor, $this->im_rect_bgcolor );
      	imagestring( $this->im, $this->font, $x1 + $this->inpadding, $y1 + $this->inpadding, $arrk[0], $this->im_fontcolor );
      	$x1 = $x1 + round( ( $x2 - $x1 ) / 2 );
      	imageline( $this->im, $x1, $y2 + 1, $x1, $y2 + $this->spacepadding - 1, $this->im_rect_bordercolor );
      
      	// output
      	if ( strlen( $file ) > 0 && is_dir( dirname( $file ) ) )
      	{
        	imagepng( $this->im, $file );
      	}
      	else
      	{
        	header( "Content-Type: image/png" );
        	imagepng( $this->im );
      	}
	}

	
	// private methods
	
	/**
	 * @access private
	 */
	function _drawData( &$data, $offset = 0, $level = 1, $width = 0 )
    {
      	$top = $this->padding + ( $level * ( ( $this->spacepadding * 2 ) + $this->fontheight + ( 2 * $this->inpadding ) ) );
      	$startx = $endx = 0;
      
	  	foreach ( $data as $k => $v )
      	{
        	if ( is_array( $v ) )
        	{
          		$width = $this->_getMaxWidth( $v );
          		$rw = ( $this->fontwidth * strlen( $k ) ) + ( 2 * $this->inpadding );
          	
				if ( $width < $rw )
            		$width = $rw;

          		$x1 = $offset + round( ( $width - $rw ) / 2 );
          		$y1 = $top;
          		$x2 = $x1 + $rw;
          		$y2 = $y1 + ( 2 * $this->inpadding ) + $this->fontheight;

          		$this->_rectangle( $x1, $y1, $x2, $y2, $this->im_rect_bordercolor, $this->im_rect_bgcolor );
          		imagestring( $this->im, $this->font, $x1 + $this->inpadding, $y1 + $this->inpadding, $k, $this->im_fontcolor );
          
          		// upper line
          		$x1 = $x1 + round( ( $x2 - $x1 ) / 2 );
          		imageline( $this->im, $x1, $y1 - 1, $x1, $y1 - $this->spacepadding + 1, $this->im_rect_bordercolor );

          		// lower line
          		imageline( $this->im, $x1, $y2 + 1, $x1, $y2 + $this->spacepadding - 1, $this->im_rect_bordercolor );

          		$this->_drawData( $v, $offset, $level + 1, $width );
          		$offset += $width + $this->spacepadding + 1;
        	}
        	else
        	{
          		$rw = ( $this->fontwidth * strlen( $v ) ) + ( 2 * $this->inpadding );

          		if ( count( $data ) == 1 )
            		$offset += round( ( $width - $rw ) / 2 );

				$x1 = $offset;
				$y1 = $top;
				$x2 = $x1 + $rw;
				$y2 = $y1 + ( 2 * $this->inpadding ) + $this->fontheight;
          
				$this->_rectangle( $x1, $y1, $x2, $y2, $this->im_rect_bordercolor, $this->im_rect_bgcolor );
				imagestring( $this->im, $this->font, $x1 + $this->inpadding, $y1 + $this->inpadding, $v, $this->im_fontcolor );

				// upper line
				$x1 = $x1 + round( ( $x2 - $x1 ) / 2 );
				imageline( $this->im, $x1, $y1 - 1, $x1, $y1 - $this->spacepadding + 1, $this->im_rect_bordercolor );

				$offset += $rw + $this->spacepadding + 1;
			}
			
			if ( $startx == 0 )
          		$startx = $x1;
        
        	$endx = $x1;
      	}
      
	  	$top -= $this->spacepadding;
      	imageline( $this->im, $startx, $top, $endx, $top, $this->im_rect_bordercolor );
	}
    
	/**
	 * @access private
	 */
    function _getMaxWidth( &$arr )
    {
      	$c = 0;
      
	  	foreach ( $arr as $k => $v )
      	{
        	if ( $c > 0 )
          		$c += $this->spacepadding + 1;
        
        	if ( is_array( $v ) )
        	{
          		$n = $this->_getMaxWidth( $v );
          
		  		if ( $n > ( 2 * $this->inpadding ) + ( imagefontwidth( $this->font ) * strlen( $k ) ) )
            		$c += $n;
          		else
            		$c += ( 2 * $this->inpadding ) + ( imagefontwidth( $this->font ) * strlen( $k ) );
        	}
        	else
        	{
          		$c += ( 2 * $this->inpadding ) + ( imagefontwidth( $this->font ) * strlen( $v ) );
        	}
      	}
      
	  	return $c;
	}
    
	/**
	 * @access private
	 */
	function _getMaxDeepness( &$arr )
    {
      	$p = 0;
      
	  	foreach ( $arr as $k => $v )
      	{
        	if ( is_array( $v ) )
        	{
          		$r = $this->_getMaxDeepness( $v );
          
		  		if ( $r > $p )
            		$p = $r;
        	}
      	}
      
	  	return ( $p + 1 );
	}

	/**
	 * @access private
	 */    
    function _rectangle( $x1, $y1, $x2, $y2, $color, $bgcolor )
    {
      	imagerectangle( $this->im, $x1, $y1, $x2, $y2, $color );
      	imagefilledrectangle( $this->im, $x1 + 1, $y1 + 1, $x2 - 1, $y2 - 1, $bgcolor );
    }
    
	/**
	 * @access private
	 */
    function _allocateColor( $var, $color, $alpha = true )
    {
      	$alpha = ( $alpha? $this->alpha : 0 );
      	$this->$var = imagecolorallocatealpha( $this->im, $color[0], $color[1], $color[2], $alpha );
    }
} // END OF Diagram

?>
