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
 * This class provides a simple way of marking an image with a 
 * digital "watermark" to prevent unauthorized use.
 *
 * Usage:
 *
 * $wm = new ImageWatermark( "/path/to/images/image.png" );
 *
 * Next you may specify where the watermark should be put
 * on the source image by calling setPosition.
 * Valid parameters for setPosition are:
 *
 * TL - Top left
 * TM - Top middle
 * TR - Top right
 * CL - Center left 
 * C  - Center
 * CR - Center right
 * BL - Bottom left
 * BM - Bottom middle
 * BR - Botton right
 *
 * Or:
 *
 * +--+--+--+
 * |TL|TM|TR|
 * +--+--+--+
 * |CL|C |CR|
 * +--+--+--+
 * |BL|BM|BR|
 * +--+--+--+
 *
 * As a gimmick you may specify "RND" which will choose a
 * position by random.
 *
 * Next you should call "addWatermark" and give the
 * watermark text as a parameter:
 * $wm->addWatermark( "Some text" );
 *
 * Finally you can fetch a reference to the newly created image
 * by calling getMarkedImage:
 * $im = $wm->getMarkedImage();
 *
 * Current features include:
 *
 * - Accepts either resources or filenames
 * - Automatic choosing of best color for watermark text
 * - Configurable position of watermark on image
 *
 * @todo TTF support
 * @todo Support for watermark images (e.g. company logos)
 * @todo Better checking of required functions
 * @todo Configurable overlay intensity
 * @package image_watermark
 */

class ImageWatermark extends PEAR
{
	var $image;
	var $type;
	var $width;
	var $height;
	var $marked_image;
	var $sizes;
	var $offset_x;
	var $offset_y;
	var $orientation;
	
	var $imageCreated = false;
	var $position     = "BL";

	
	/**
	 * Constructor
	 * 
	 * You need to specify either a filename or an image resource
	 * when instatiating.
	 */
	function ImageWatermark( $res ) 
	{
		if ( intval( @imagesx( $res ) ) > 0 ) 
		{
			$this->image = $res;
		} 
		else 
		{
			$imginfo = getimagesize( $res );

			switch ( $imginfo[2] ) 
			{
				case 1:
					$this->type = "GIF";
					
					if ( function_exists( "imagecreatefromgif" ) )
						$this->image = imagecreatefromgif( $res );
					
					break;
				
				case 2:
					$this->type = "JPG";
					
					if ( function_exists( "imagecreatefromjpeg" ) )
						$this->image = imagecreatefromjpeg( $res );
					
					break;
				
				case 3:
					$this->type = "PNG";
					
					if ( function_exists( "imagecreatefrompng" ) )
						$this->image = imagecreatefrompng( $res );
					
					break;
			}
		}

		if ( !$this->image ) 
		{
			$this = new PEAR_Error( "Type not supported: " . $this->type );
			return;
		}
	
		$this->width  = imagesx( $this->image );
		$this->height = imagesy( $this->image );
	}


	// Adds a watermark to the image
	// Public void addWatermark(string)
	function addWatermark( $mark ) 
	{
		// TODO: - Support for watermark images (e.g. company logo)
		//       - Automatically determine type of watermark

		$type = "TEXT";

		if ( $type == "TEXT" ) 
		{
			$this->orientation = ( $this->width > $this->height )? "H" : "V"; // Choose orientation
			$this->sizes       = $this->_getTextSizes( $mark );

			$this->_getOffsets();

			// Copy a chunk of the oriiginal image (this is where the watermark will be placed).
			$chunk = $this->_getChunk();
			
			if ( !$chunk )
				return PEAR::raiseError( "Could not extract chunk from image." );
			
			$img_mark = $this->_createEmptyWatermark();
			$img_mark = $this->_addTextWatermark($mark, $img_mark, $chunk);

			// delete chunk
			imagedestroy( $chunk );

			// finish image
			$this->_createMarkedImage( $img_mark, $type, 30 );
		}
	}
	
	// Public int getMarkedImage
	// Returns the final image
	function getMarkedImage() 
	{
		return $this->marked_image;
	}

	// Public bool setPosition
	// Set position of watermark on image
	// Return true on valid parameter, otherwise false
	function setPosition( $newposition ) 
	{
		$valid_positions = array(
			"TL", 
			"TM", 
			"TR", 
			"CL", 
			"C", 
			"CR", 
			"BL", 
			"BM", 
			"BR", 
			"RND"
		);

		$newposition = strtoupper( $newposition );

		if ( in_array( $newposition, $valid_positions ) ) 
		{
			if ( $newposition == "RND" )
				$newposition = $valid_positions[rand( 0, sizeof( $valid_positions ) - 2 )];
			
			$this->position = $newposition;
			return true;
		}
		
		return false;
	}

	
	// private methods

	function _getTextSizes( $text ) 
	{
		$act_scale = 0;
		$act_font  = 0;
		
		$marklength    = strlen( $text );
		$scale         = ( $this->orientation == "H" )? $this->width : $this->height; 	// Define maximum length of complete mark
		$char_widthmax = intval( ( $scale / $marklength ) - 0.5 ); 				// Maximum character length in watermark

		for ( $size = 5; $size >= 1; $size-- ) 
		{
			$box_w        = imagefontwidth( $size );
			$box_h        = imagefontheight( $size );
			$box_spacer_w = 0;
			$box_spacer_h = 0;

			if ( $this->orientation == "H" ) 
			{
				$box_h        *= 2;
				$box_w        *= 1.75;
				$box_w        *= $marklength;
				$box_w        += intval( $this->width  * 0.05 );
				$box_spacer_w  = intval( $this->width  * 0.05 );
				$box_spacer_h  = intval( $this->height * 0.01 );
			} 
			else 
			{
				$box_w        *= 3;
				$box_h        *= 1.1;
				$box_h        *= $marklength;
				$box_spacer_h  = intval( $this->height * 0.05 );
				$box_spacer_w  = intval( $this->width  * 0.01 );
			}
			
			$box_scale = ( $this->orientation == "H" )? $box_w + $box_spacer_w : $box_h + $box_spacer_h;

			if ( $box_scale < $scale && $box_scale > $act_scale ) 
			{
				$act_font  = $size; 
				$act_scale = $box_scale; 
			}
		}

		return array(	
			"fontsize"	=> $act_font,
			"box_w"		=> $box_w,
			"box_h"		=> $box_h,
			"spacer_w"	=> $box_spacer_w,
			"spacer_h"	=> $box_spacer_h
		);
	}

	function _getChunk() 
	{
		$chunk = imagecreatetruecolor( $this->sizes["box_w"], $this->sizes["box_h"] );
		
		imagecopy(	
			$chunk,
			$this->image, 
			0, 
			0,
			$this->offset_x,
			$this->offset_y,
			$sizes["box_w"],
			$sizes["box_h"]
		);
		
		/*
		imagecopy(	
			$chunk,
			$this->image, 
			0, 
			0,
			( $this->width  - $this->sizes["box_w"] - $this->sizes["spacer_w"] ) / 1.5,
			( $this->height - $this->sizes["box_h"] - $this->sizes["spacer_h"] ) / 1.5,
			$sizes["box_w"],
			$sizes["box_h"]
		);
		*/
		
		return $chunk;
	}

	function _createEmptyWatermark() 
	{
		return imagecreatetruecolor( $this->sizes["box_w"], $this->sizes["box_h"] );
	}

	function _addTextWatermark( $mark, $img_mark, $chunk ) 
	{
		imagetruecolortopalette( $chunk, true, 65535 );
		
		$text_color = array(
			"r" => 0, 
			"g" => 0, 
			"b" => 0 
		);

		// Search color for overlay text.
		for ( $x = 0; $x <= $this->sizes["box_w"]; $x++ ) 
		{
			for ( $y = 0; $y <= $this->sizes["box_h"]; $y++ ) 
			{ 
				$colors = imagecolorsforindex( $chunk, imagecolorat( $chunk, $x, $y ) );
				$text_color["r"] += $colors["red"];
				$text_color["r"] /= 2;
				$text_color["g"] += $colors["green"];
				$text_color["g"] /= 2;
				$text_color["b"] += $colors["blue"];
				$text_color["b"] /= 2;
			}
		}
		
		$text_color["r"] = ( $text_color["r"] < 128 )? $text_color["r"] + 128 : $text_color["r"] - 128;
		$text_color["g"] = ( $text_color["g"] < 128 )? $text_color["g"] + 128 : $text_color["g"] - 128;
		$text_color["r"] = ( $text_color["r"] < 128 )? $text_color["r"] + 128 : $text_color["r"] - 128;

		// Choose transparent color for watermark.
		$mark_bg = imagecolorallocate( $img_mark,
			( ( $text_color["r"] > 128 )? 10 : 240 ),
			( ( $text_color["g"] > 128 )? 10 : 240 ),
			( ( $text_color["b"] > 128 )? 10 : 240 ) 
		);

		// Choose text color for watermark.
		$mark_col = imagecolorallocate( $img_mark, $text_color["r"], $text_color["g"], $text_color["b"] );

		// Fill watermark with transparent color.
		imagefill( $img_mark, 0, 0, $mark_bg );
		imagecolortransparent( $img_mark, $mark_bg );

		// Add text to watermark.
		if ( $this->orientation == "H" )
			imagestring( $img_mark, $this->sizes["fontsize"], 1, 0, $mark, $mark_col ); 
		else
			imagestringup( $img_mark, $this->sizes["fontsize"], 0, $this->sizes["box_h"] - 5, $mark, $mark_col );

		return $img_mark;
	}

	function _createMarkedImage( $img_mark, $type, $pct ) 
	{
		// TODO: - Support for other watermark types
		//       - pct should be configurable
		$this->marked_image = imagecreatetruecolor( $this->width, $this->height );
		imagecopy( $this->marked_image, $this->image, 0, 0, 0, 0, $this->width, $this->height );
		imagecopymerge( $this->marked_image, $img_mark, $this->offset_x, $this->offset_y, 0, 0, $this->sizes["box_w"], $this->sizes["box_h"], $pct );
		$this->imageCreated = true;
	}

	function _getOffsets() 
	{
		$width_mark  = $this->sizes["box_w"] + $this->sizes["spacer_w"];
		$height_mark = $this->sizes["box_h"] + $this->sizes["spacer_h"];
		$width_left  = $this->width  - $width_mark;
		$height_left = $this->height - $height_mark; 
	
		switch ( $this->position ) 
		{
			case "TL":	// Top Left
				$this->offset_x = ( $width_left  >= 5 )? 5 : $width_left;
				$this->offset_y = ( $height_left >= 5 )? 5 : $height_left;
	
				break;
	
			case "TM":	// Top middle 
				$this->offset_x = intval( ( $this->width - $width_mark ) / 2 );
				$this->offset_y = ( $height_left >= 5 )? 5 : $height_left;
	
				break;
	
			case "TR":	// Top right
				$this->offset_x = $this->width - $width_mark;
				$this->offset_y = ( $height_left >= 5 )? 5 : $height_left;
	
				break;
	
			case "CL":	// Center left
				$this->offset_x = ( $width_left >= 5 )? 5 : $width_left;
				$this->offset_y = intval( ( $this->height - $height_mark ) / 2 );
	
				break;
	
			case "CR":	// Center right
				$this->offset_x = $this->width - $width_mark;
				$this->offset_y = intval( ( $this->height - $height_mark ) / 2 );
	
				break;
	
			case "BL":	// Bottom left
				$this->offset_x = ( $width_left >= 5 )? 5 : $width_left;
				$this->offset_y = $this->height - $height_mark;
	
				break;
	
			case "BM":	// Bottom middle
				$this->offset_x = intval( ( $this->width - $width_mark ) / 2 );
				$this->offset_y = $this->height - $height_mark;
	
				break;
	
			case "BR":	// Bottom right
				$this->offset_x = $this->width  - $width_mark;
				$this->offset_y = $this->height - $height_mark;
	
				break;
			
			case "C":
			
			default:	// Center (the default)
				$this->offset_x = intval( ( $this->width  - $width_mark  ) / 2 );
				$this->offset_y = intval( ( $this->height - $height_mark ) / 2 );

				break;				
		}
	}
} // END OF ImageWatermark

?>
