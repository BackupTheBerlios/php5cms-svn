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
|Authors: Lionel Micault <lionel.micault@laposte.net>                  |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


// Position constants
define( "TRANSPARENTWATERMARK_TOP",    -1 );
define( "TRANSPARENTWATERMARK_MIDDLE",  0 );
define( "TRANSPARENTWATERMARK_BOTTOM",  1 );
define( "TRANSPARENTWATERMARK_LEFT",   -1 );
define( "TRANSPARENTWATERMARK_CENTER",  0 );
define( "TRANSPARENTWATERMARK_RIGHT",   1 );

// Randomize level constants
define( "TRANSPARENTWATERMARK_RANDPIXEL_LIGHTLEVEL",    7 );
define( "TRANSPARENTWATERMARK_RANDPIXEL_POSITIONLEVEL", 2 );


/** 
 * Put watermark in image with transparent and randomize effect.
 *
 * It's IMPORTANT to save result of watermaks on disk: it's easy to retrieve 
 * an image from different versions of watermark by pixel color averages.
 * 
 * Transparent color is #808080
 *
 * @package image_watermark
 */
 
class TransparentWatermark extends PEAR
{
	/**
	 * @access public
	 */
	var $stampImage = 0;
	
	/**
	 * @access public
	 */
	var $stampWidth;
	
	/**
	 * @access public
	 */
	var $stampHeight;
	
	/**
	 * @access public
	 */
	var $stampPositionX = TRANSPARENTWATERMARK_RIGHT;
	
	/**
	 * @access public
	 */
	var $stampPositionY = TRANSPARENTWATERMARK_BOTTOM;
	
	
	/**
	 * Constructor
	 *
	 * @param  string $stampFile  filename of stamp image
	 * @access public
	 */
	function TransparentWatermark( $stampFile = "" )
	{
		$res = $this->setStamp( $stampFile );
		
		if ( PEAR::isError( $res ) )
		{
			$this = $res;
			return;
		}
	}
	
	
	/**
	 * Mark an image file and display/save it.
	 *
	 * @param  int $imageFile  image file (JPEG or PNG format)
	 * @param  int $resultImageFile new image file (same format)
	 * @return boolean
	 * @access public
	 */
	function markImageFile( $imageFile, $resultImageFile = "" )
	{
		if ( !$this->stampImage )
			return PEAR::raiseError( "Stamp image is not set." );

		$imageinfos = @getimagesize( $imageFile );
		$type  = $imageinfos[2];
		$image = $this->_readImage( $imageFile, $type );
		
		if ( !$image || PEAR::isError( $image ) )
			return PEAR::raiseError( "Error on loading '$imageFile', image must be a valid PNG or JPEG file." );
		
		$res = $this->markImage( $image );
		
		if ( PEAR::isError( $res ) )
			return $res;
			
		if ( $resultImageFile != "" )
			return $this->_writeImage( $image, $resultImageFile, $type );
		else
			return $this->_displayImage( $image, $type );
	}
	
	/**
	 * Mark an image.
	 *
	 * @param  int $imageResource resource of image
	 * @return boolean
	 * @access public
	 */
	function markImage( $imageResource)
	{
		if ( !$this->stampImage ) 
			return PEAR::raiseError( "Stamp image is not set." );
		
		$imageWidth  = imagesx( $imageResource );
		$imageHeight = imagesy( $imageResource );

		//set position of logo
		switch ( $this->stampPositionX )
		{
			case TRANSPARENTWATERMARK_LEFT: 
				$leftStamp = 0;
				break;
				
			case TRANSPARENTWATERMARK_CENTER:
				$leftStamp = ( $imageWidth - $this->stampWidth ) / 2;
				break;
			
			case TRANSPARENTWATERMARK_RIGHT:
				$leftStamp = $imageWidth - $this->stampWidth;
				break;
			
			default:
				$leftStamp = 0;
		}
		
		switch ( $this->stampPositionY )
		{
			case TRANSPARENTWATERMARK_TOP:
				$topStamp = 0;
				break;
				
			case TRANSPARENTWATERMARK_MIDDLE:
				$topStamp = ( $imageHeight - $this->stampHeight ) / 2;
				break;
				
			case TRANSPARENTWATERMARK_BOTTOM:
				$topStamp = $imageHeight - $this->stampHeight;
				break;
				
			default:
				$topStamp = 0;
		}
		
		// randomize position
		$leftStamp += rand( -TRANSPARENTWATERMARK_RANDPIXEL_POSITIONLEVEL, TRANSPARENTWATERMARK_RANDPIXEL_POSITIONLEVEL );
		$topStamp  += rand( -TRANSPARENTWATERMARK_RANDPIXEL_POSITIONLEVEL, TRANSPARENTWATERMARK_RANDPIXEL_POSITIONLEVEL );
				
		// for each pixel of stamp
		for ( $x = 0; $x < $this->stampWidth; $x++ ) 
		{
			if ( ( $x + $leftStamp < 0 ) || ( $x + $leftStamp >= $imageWidth ) ) 
				continue;
			
			for ( $y = 0; $y < $this->stampHeight; $y++ ) 
			{
				if ( ( $y + $topStamp < 0 ) || ( $y + $topStamp >= $imageHeight ) )
					continue;
				
				// search RGB values of stamp image pixel
				$indexStamp = imagecolorat( $this->stampImage, $x, $y );
				$rgbStamp   = imagecolorsforindex( $this->stampImage, $indexStamp );
				
				// search RGB values of image pixel
				$indexImage = imagecolorat( $imageResource, $x + $leftStamp, $y + $topStamp );
				$rgbImage   = imagecolorsforindex( $imageResource, $indexImage );

				// randomize light shift
				$stampAverage = ( $rgbStamp["red"] + $rgbStamp["green"] + $rgbStamp["blue"] ) / 3;
				
				if ( $stampAverage > 10 ) 
					$randomizer = rand( -TRANSPARENTWATERMARK_RANDPIXEL_LIGHTLEVEL, TRANSPARENTWATERMARK_RANDPIXEL_LIGHTLEVEL );
				else
					$randomizer = 0;
				
				// compute new values of colors pixel
				$r = max( min( $rgbImage["red"]   + $rgbStamp["red"]   + $randomizer - 0x80, 0xFF ), 0x00 );
				$g = max( min( $rgbImage["green"] + $rgbStamp["green"] + $randomizer - 0x80, 0xFF ), 0x00 );
				$b = max( min( $rgbImage["blue"]  + $rgbStamp["blue"]  + $randomizer - 0x80, 0xFF ), 0x00 );
				
				// change  image pixel
				imagesetpixel( $imageResource, $x + $leftStamp, $y + $topStamp, ( $r << 16 ) + ( $g << 8 ) + $b );
			}
		}
	}
	
	/**
	 * Set stamp position on image.
	 *
	 * @param  int $Xposition x position
	 * @param  int $Yposition y position
	 * @return void
	 * @access public
	 */
	function setStampPosition( $Xposition, $Yposition )
	{
		// set X position
		switch ( $Xposition )
		{
			case TRANSPARENTWATERMARK_LEFT: 
			
			case TRANSPARENTWATERMARK_CENTER:
			
			case TRANSPARENTWATERMARK_RIGHT:
				$this->stampPositionX = $Xposition;
				break;
		}
		
		// set Y position
		switch ( $Yposition )
		{
			case TRANSPARENTWATERMARK_TOP:
			
			case TRANSPARENTWATERMARK_MIDDLE:
			
			case TRANSPARENTWATERMARK_BOTTOM:
				$this->stampPositionY = $Yposition;
				break;
		}
	}
	
	/**
	 * Set stamp image for watermak.
	 *
	 * @param  string $stampFile  image file (JPEG or PNG)
	 * @return boolean
	 * @access public
	 */
	function setStamp( $stampFile )
	{
		$imageinfos = @getimagesize( $stampFile );
		$width  = $imageinfos[0];
		$height = $imageinfos[1];
		$type   = $imageinfos[2];
		
		if ( $this->stampImage )
			imagedestroy( $this->stampImage );
		
		$stampimg = $this->_readImage( $stampFile, $type );
		
		if ( !$stampimg || PEAR::isError( $stampimg ) )
		{
			return PEAR::raiseError( "Error on loading '$stampFile', stamp image must be a valid PNG or JPEG file." );
		}
		else
		{
			$this->stampImage  = $stampimg;
			$this->stampWidth  = $width;
			$this->stampHeight = $height;
			
			return true;
		}
	}
	
	
	// private methods

	/**
	 * Read image from file.
	 *
	 * @param  string $file  image file (JPEG or PNG)
	 * @param  int $type  file type (2:JPEG or 3:PNG)
	 * @return resource
	 * @access private
	 */
	function _readImage( $file, $type )
	{
		switch ( $type )
		{
			case 2:	// JPEG
				return imagecreatefromjpeg( $file );
				break;
			
			case 3:	// PNG
				return imagecreatefrompng( $file );
				break;
			
			default:
				return PEAR::raiseError( "File format not supported." );
		}
	}
	
	/**
	 * Write image to file.
	 *
	 * @param  resource $image  image 
	 * @param  string $file  image file (JPEG or PNG)
	 * @param  int $type  file type (2:JPEG or 3:PNG)
	 * @return void
	 * @access private
	 */
	function _writeImage( $image, $file, $type )
	{
		switch ( $type )
		{
			case 2:	// JPEG
				imagejpeg( $image, $file );
				break;
			
			case 3:	// PNG
				imagepng( $image, $file );
				break;
			
			default:
				return PEAR::raiseError( "File format not supported." );
		}
		
		return true;
	}
	
	/**
	 * Send image to stdout.
	 *
	 * @param  resource $image  image 
	 * @param  int $type  image type (2:JPEG or 3:PNG)
	 * @return void
	 * @access private
	 */
	function _displayImage( $image, $type )
	{
		switch ( $type )
		{
			case 2:	// JPEG
				header( "Content-Type: image/jpeg" );
				imagejpeg( $image );
				
				break;
			
			case 3:	// PNG
				header( "Content-Type: image/png" );
				imagepng( $image );
			
				break;
			
			default:
				 return PEAR::raiseError( "File format not supported." );
		}
		
		return true;
	}
} // END OF TransparentWatermark

?>
