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
 * Usage:
 *
 * $h = new RGBHistogram( "test.jpg", "jpeg", "jpeg", 5 /*5-100*/, 1 );
 * $h->output();
 *
 * @package image
 */
 
class RGBHistogram extends PEAR
{
	/**
	 * Constructor
	 */
	function RGBHistogram( $image, $input_format, $output_format, $rating, $type_fill )
	{
		$this->image         = $image;
		$this->type_fill     = $type_fill;
		$this->input_format  = $input_format;
		$this->output_format = $output_format;
		$this->rating        = $rating;	
	}
		
		
	function output()
	{			
		$this->_analyze();
		$coef = (int)( $this->index / 800 );
		
		if ( $coef < 1 )
			$coef = 1;
								
		$image = imagecreate( 800, 400 );
		
		if ( !$image )
			return PEAR::raiseError( "Cannot Initialize new GD image stream." );
		
		$background_color = imagecolorallocate( $image,   0,   0,   0 );			
		$axis_color       = imagecolorallocate( $image, 255, 255, 255 );
		
		$red   = imagecolorallocate( $image, 255,   0,   0 );
		$green = imagecolorallocate( $image,   0, 255,   0 );
		$blue  = imagecolorallocate( $image,   0,   0, 255 );
			
		imageline( $image, 10,   0,  10, 400, $axis_color );
		imageline( $image,  0, 100, 800, 100, $axis_color );
		imageline( $image,  0, 200, 800, 200, $axis_color );
		imageline( $image,  0, 300, 800, 300, $axis_color );			
			 
		$pos = 0;
		for ( $i = 0; $i < $this->index; $i += $coef )
		{
			if ( $this->type_fill == 0 )
			{
				imagesetpixel( $image, $pos + 10, 100 - ( $this->r[$i] / 3 ), $red   );
				imagesetpixel( $image, $pos + 10, 200 - ( $this->g[$i] / 3 ), $green );
				imagesetpixel( $image, $pos + 10, 300 - ( $this->b[$i] / 3 ), $green );
			}
			else if ( $this->type_fill == 1 )
			{
				imageline( $image, $pos + 10, 100, $pos + 10, 100 - ( $this->r[$i] / 3 ), $red   );
				imageline( $image, $pos + 10, 200, $pos + 10, 200 - ( $this->g[$i] / 3 ), $green );
				imageline( $image, $pos + 10, 300, $pos + 10, 300 - ( $this->b[$i] / 3 ), $blue  );
			}
			
			$pos++;
		}
				
		imagestring( $image, 2, 30, 310, "Sample No. : 1 ----------------------------------------------------------------------------------------------> Sample No. : $this->index", $axis_color );
		imagestring( $image, 3, 20, 340, "Image Histogram for Red, Green, Blue elements . ", $axis_color );
		imagestring( $image, 2, 20, 360, "Image Name : test.jpg   ---   Number Of Pixels : $this->size   ---   Sampling Rate : 1 Sample for each $this->rating * $this->rating Pixels .",$axis_color );
						
		switch ( $this->output_format )
		{				
			case "jpeg":
				imagejpeg( $image );
				break;				
			
			case "png":
				imagepng( $image );								
		}
	}
	
	
	// private methods
	
	function _analyze()
	{	
		switch ( $this->input_format )
		{
			case "jpeg":
				$im = imagecreatefromjpeg( $this->image );
				break;				
			
			case "png":
				$im = imagecreatefrompng( $this->image );								
		}
		
		$xx = imagesx( $im );
		$yy = imagesy( $im );
		
		$this->size  = $xx * $yy;
		$this->index = 0;
			
		for ( $i = 0; $i < $xx; $i += $this->rating )
		{
			for ( $j = 0; $j < $yy; $j += $this->rating )
			{
				$rgb    = imagecolorat( $im, $i, $j );
				$rrggbb = imagecolorsforindex( $im, $rgb );				
				
				$this->r[$this->index] = $rrggbb['red'];					
				$this->g[$this->index] = $rrggbb['green'];
				$this->b[$this->index] = $rrggbb['blue'];
				
				$this->index++;					
			}
		}
	}
} // END OF RGBHistogram

?>
