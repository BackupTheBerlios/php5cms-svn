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


using( 'image.ImageInformation' );


/**
 * Static helper functions.
 *
 * @package image
 */

class ImageUtil
{          
	/**
	 * Get GD version.
	 *
	 * @access public
	 * @static
	 */
	function getGDVersion()
	{
		static $gd_version_number = null;
		
		if ( $gd_version_number === null )
		{
			ob_start();
			phpinfo( 8 );
			$module_info = ob_get_contents();
			ob_end_clean();
			
			if ( preg_match( "/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i", $module_info, $matches ) )
				$gd_version_number = $matches[1];
			else
				$gd_version_number = 0;
		}
		
		return $gd_version_number;
	}
		
    /**
     * Returns the RGB integer values of an HTML like hexadecimal color like #00ff00.
     *
     * @param    string  HTML like hexadecimal color like #00ff00
     * @return   array   [int red, int green, int blue],
     *                   returns black [0, 0, 0] for invalid strings.
     * @access   public
	 * @static
     */
    function htmlColorToRGB( $color )
	{
		if ( strlen( $color ) != 7 )
			return array( 0, 0, 0 );

		return array(
			hexdec( substr( $color, 1, 2 ) ),
			hexdec( substr( $color, 3, 2 ) ),
			hexdec( substr( $color, 5, 2 ) )
		);
	}

	/**
	 * @access public
	 * @static
	 */
	function hexToRGB( $hex ) 
    { 
        $r    = substr( $hex, 0, 2 ); 
        $g    = substr( $hex, 2, 2 ); 
        $b    = substr( $hex, 4, 2 ); 
        $a[1] = hexdec( $r ); 
        $a[2] = hexdec( $g ); 
        $a[3] = hexdec( $b );
		 
        return $a; 
    } 
	
    /**
     * Returns the RGB integer values of a color specified by a "percentage string" like "%50,%20,%100". 
     *
     * @param    string
     * @return   array   [int red, int green, int blue]
     * @access   public
	 * @static
     */
	function percentageColorToRGB( $color )
	{    
		// split the string %50,%20,%100 by ,
		$color = explode( ",", $color );        
                
		foreach ( $color as $k => $v )
		{
			// remove the trailing percentage sign %
			$v = (int)substr( $v, 1 );
            
			// range checks
			if ( $v >= 100 )
				$color[$k] = 255;
			else if ( $v <= 0 )
				$color[$k] = 0;
			else
                $color[$k] = (int)( 2.55 * $v );
		} 

		return $color;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function cropImage( $cropX, $cropY, $cropH, $cropW, $source, $destination, $type = 'jpeg' )
	{ 
     	switch ( $type )
		{ 
			case 'wbmp':
				$sim = ImageCreateFromWBMP( $source ); 
				break; 
			
			case 'gif':
				$sim = ImageCreateFromGIF( $source );
				break; 
			
			case 'png':
				$sim = ImageCreateFromPNG( $source );
				break; 
			
			default:
				$sim = ImageCreateFromJPEG( $source );
				break; 
    	} 
     
    	$dim = imagecreate( $cropW, $cropH );  
     
		for ( $i = $cropY; $i < ( $cropY + $cropH ); $i++ )
		{ 
        	for ( $j = $cropX; $j < ( $cropX + $cropW ); $j++ )
			{ 
            	$color = imagecolorsforindex( $sim, imagecolorat( $sim, $j, $i ) ); 
            	$index = ImageColorAllocate( $dim, $color['red'], $color['green'], $color['blue'] );  
            	imagesetpixel( $dim, $j - $cropX, $i - $cropY, $index ); 
        	} 
    	} 
    
		imagedestroy( $sim ); 

     	switch ( $type )
		{ 
    		case 'wbmp':
				ImageWBMP( $dim, $destination );
				break; 
        	
			case 'gif':
				ImageGIF( $dim, $destination );
				break; 
    		
			case 'png':
				ImagePNG( $dim, $destination );
				break; 
        	
			default:
				ImageJPEG( $dim, $destination );
				break; 
    	} 
    
		imagedestroy( $dim ); 
	}
	 
	/**
	 * @access public
	 * @static
	 */
	function generateThumb( $file, $path, $thumb_width = 50, $thumb_height = 50, $output_format = "JPG", $thumb_prefix = "t_" )
	{
		$image_path = $path . $file;
		$thumb_path = $path . $thumb_prefix . $file;
		
		if ( file_exists( $thumb_path ) )
			unlink( $thumb_path );
		
		if ( !file_exists( $thumb_path ) && file_exists( $image_path ) )
		{
			$image_info = new ImageInformation( $image_path );
			$format = $image_info->format;
			
			if ( $format != "unknown" )
			{
				if ( $format == "JPG" )
					$h_img = @imagecreatefromjpeg( $image_path );
				else if ( $format == "GIF" )
					$h_img = @imagecreatefromgif( $image_path );
				else if ( $format == "PNG" )
					$h_img = @imagecreatefrompng( $image_path );
				else
					return false;
				
				if ( $h_img )
				{
					$width  = imagesx( $h_img );
					$height = imagesy( $h_img );
					$h_thb  = imagecreate( $thumb_width, $thumb_height );
				
					imagecopyresized(
						$h_thb,
						$h_img,
						0,
						0,
						0,
						0,
						$thumb_width,
						$thumb_height,
						$width,
						$height
					);
				
					if ( strtoupper( $output_format ) == "JPG" )
						imagejpeg( $h_thb, $thumb_prefix . $file );
					else if ( strtoupper( $output_format ) == "GIF" )
						imagegif( $h_thb, $thumb_prefix . $file );
					else if ( strtoupper( $output_format ) == "PNG" )
						imagepng( $h_thb, $thumb_prefix . $file );
					else
						return false;
				
					imagedestroy( $h_thb );
					imagedestroy( $h_img );
					
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
	}
	
	/**
	 * @access public
	 * @static
	 */
	function compressJPEG( $input_file, $output_file )
	{ 
		$img = imagecreatefromjpeg( $input_file ); 
		imageinterlace( $img, 1 ); 
		$compressed = imagejpeg( $img, $output_file, 60 ); 
		ImageDestroy( $img );
		
		return true; 
	}
	
	/**
	 * Make an image web safe.
	 *
	 * @param  resource image $img The image
	 * @access public
	 * @static
	 */
	function makeWebsafeColors( &$image )
	{
		for ( $r = 0; $r <= 255; $r += 51 )
		{
			for ( $g = 0; $g <= 255; $g += 51 )
			{
				for ( $b = 0; $b <= 255; $b += 51 )
					$color = imagecolorallocate( $image, $r, $g, $b );
			}
		}
	}
		
	/**
	 * Add a border to an image.
	 * This adds a border to the image specified. Beware that if you use this,
	 * everything under the border will be overwritten.
	 * 
	 * @access public
	 * @static
	 * @param resource image $img The image
	 * @param int $x X coordinate to start border
	 * @param int $y Y coordinate to start border
	 * @param int $width Width of the border
	 * @param int $height Height of the border
	 * @param array $fgColor Array of color values (r, g, b) for the foreground color
	 * @param array $bgColor Array of color values (r, g, b) for the background color
	 * @param int $lineWidth The thickness of the border
	 * @param int $roundWidth The thickness of the rounding, if you wish round borders
	 */
	function addBorder( &$img, $x = 1, $y = 1, $width = 1, $height = 1, $fgColor = array( 'r' => 0, 'g' => 0, 'b' => 0 ), $bgColor = array( 'r' => 255, 'g' => 255, 'b' => 255 ), $lineWidth = 1, $roundWidth = 0 ) 
	{
		$fgCol = imagecolorallocate( $img, $fgColor['r'], $fgColor['g'], $fgColor['b'] );
		$bgCol = imagecolorallocate( $img, $bgColor['r'], $bgColor['g'], $bgColor['b'] );

		// FG
		$startXfg = $x - $lineWidth;
		$startYfg = $y - $lineWidth;
		$endXfg   = $x + $width  + $lineWidth;
		$endYfg   = $y + $height + $lineWidth;

		$res = imagefilledrectangle( $img, $startXfg, $startYfg, $endXfg, $endYfg, $fgCol );

		// BG
		$startXbg = $x;
		$startYbg = $y;
		$endXbg   = $x + $width;
		$endYbg   = $y + $height;

		$res = imagefilledrectangle( $img, $startXbg, $startYbg, $endXbg, $endYbg, $bgCol );

		if ( $roundWidth > 0 ) 
		{
			// Top left corner erase
			$res = imagefilledrectangle( $img, $startXfg, $startYfg, $startXfg + $roundWidth + $lineWidth, $startYfg + $roundWidth + $lineWidth, $bgCol );
			// Bottom left corner erase
			$res = imagefilledrectangle( $img, $startXfg, $endYfg - $roundWidth - $lineWidth, $startXfg + $roundWidth + $lineWidth, $endYfg, $bgCol );
			// Bottom right corner erase
			$res = imagefilledrectangle( $img, $endXfg - $roundWidth - $lineWidth, $endYfg -  $roundWidth - $lineWidth, $endXfg, $endYfg, $bgCol );
			// Top right corner erase
			$res = imagefilledrectangle( $img, $endXfg - $roundWidth - $lineWidth, $startYfg, $endXfg, $startYfg + $roundWidth + $lineWidth, $bgCol );

			// Top left corner arc
			$res = imagefilledarc( $img, $startXfg + $roundWidth + $lineWidth, $startYfg + $roundWidth + $lineWidth, ( $roundWidth + $lineWidth ) * 2, ( $roundWidth + $lineWidth ) * 2, 180, 270, $fgCol, IMG_ARC_PIE );
			// Top right corner arc
			$res = imagefilledarc( $img, $endXfg - $roundWidth - $lineWidth, $startYfg + $roundWidth + $lineWidth, ( $roundWidth + $lineWidth ) * 2, ( $roundWidth + $lineWidth ) * 2, 270, 0, $fgCol, IMG_ARC_PIE );
			// Bottom right corner arc
			$res = imagefilledarc( $img, $endXfg - $roundWidth - $lineWidth, $endYfg - $roundWidth - $lineWidth, ( $roundWidth + $lineWidth ) * 2, ( $roundWidth + $lineWidth ) * 2, 0, 90, $fgCol, IMG_ARC_PIE );
			// Bottom left corner arc
			$res = imagefilledarc( $img, $startXfg + $roundWidth + $lineWidth, $endYfg - $roundWidth - $lineWidth, ( $roundWidth + $lineWidth ) * 2, ( $roundWidth + $lineWidth ) * 2, 90, 180, $fgCol, IMG_ARC_PIE );

			// Top left corner arc
			$res = imagefilledarc( $img, $startXfg + $roundWidth + $lineWidth, $startYfg + $roundWidth + $lineWidth, $roundWidth * 2, $roundWidth * 2, 180, 270, $bgCol, IMG_ARC_PIE );
			// Top right corner arc
			$res = imagefilledarc( $img, $endXfg - $roundWidth - $lineWidth, $startYfg + $roundWidth + $lineWidth, $roundWidth * 2, $roundWidth * 2, 270, 0, $bgCol, IMG_ARC_PIE );
			// Bottom right corner arc
			$res = imagefilledarc( $img, $endXfg - $roundWidth - $lineWidth, $endYfg - $roundWidth - $lineWidth, $roundWidth * 2, $roundWidth * 2, 0, 90, $bgCol, IMG_ARC_PIE );
			// Bottom left corner arc
			$res = imagefilledarc( $img, $startXfg + $roundWidth + $lineWidth, $endYfg - $roundWidth - $lineWidth, $roundWidth * 2, $roundWidth * 2, 90, 180, $bgCol, IMG_ARC_PIE );
		}
	}
} // END OF ImageUtil

?>
