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
 * Creates a thumbnail from a source image with GD2 functions.
 *
 * Tested with Apache 1.3.27 and PHP 4.3.3
 *
 * @package image_watermark_lib
 */

class Thumbnail extends PEAR
{
	/**
	 * possible image formats
	 *
	 * @var array
	 */
	var $formats = array(
		'gif'    => 1, 
		'jpg'    => 2, 
		'png'    => 3, 
		'wbmp'   => 15,
		'string' => 999
	);

	/**
	 * maximal height of the generated thumbnail
	 *
	 * @var int
	 * @access protected
	 */
	var $thumb_max_height = 100;

	/**
	 * maximal width of the generated thumbnail
	 *
	 * @var int
	 * @access protected
	 */
	var $thumb_max_width = 100;

	/**
	 * quality or speed when generating the thumbnail
	 *
	 * @var boolean
	 * @access protected
	 */
	var $quality_thumb = true;

	/**
	 * path/filename of imagefile
	 *
	 * @var string
	 * @access protected
	 */
	var $image_path;

	/**
	 * @var int
	 * @access protected
	 */
	var $image_width;

	/**
	 * @var int
	 * @access protected
	 */
	var $image_height;

	/**
	 * image format of source
	 *
	 * @var int
	 * @access protected
	 */
	var $image_type;

	/**
	 * @var int
	 * @access protected
	 */
	var $thumbnail_height;

	/**
	 * @var int
	 * @access protected
	 */
	var $thumbnail_width;

	/**
	 * image format of the thumbnail
	 *
	 * @var int
	 * @access protected
	 */
	var $thumbnail_type = 3;

	/**
	 * @var resource
	 * @access protected
	 */
	var $image;

	/**
	 * @var resource
	 * @access protected
	 */
	var $thumbnail;

	/**
	 * @var string
	 * @access protected
	 */
	var $version = '1.001';

	
	/**
	 * Constructor
	 *
	 * @param string $file  path/filename of picture or stream from a DB field
	 * @return void
	 * @access protected
	 * @uses $image_path
	 */
	function Thumbnail( $file = '' ) 
	{
		$this->image_path = $file;
	}

	
	/**
	 * Sets the output type of the thumbnail.
	 *
	 * @param string $format gif, jpg, png, wbmp
	 * @return void
	 * @access public
	 * @uses $thumbnail_type
	 * @uses $formats
	 */
	function setOutputFormat( $format = 'png' ) 
	{
		if ( array_key_exists( trim( $format ), $this->formats ) )
			$this->thumbnail_type = $this->formats[trim( $format )];
	}

	/**
	 * Sets the max. height of the thumbnail.
	 *
	 * @param int $height
	 * @return boolean
	 * @access public
	 * @uses readSourceImageData()
	 * @uses $image_height
	 * @uses $thumb_max_height
	 */
	function setMaxHeight( $height = 0 ) 
	{
		$res = $this->readSourceImageData();
		
		if ( PEAR::isError( $res ) )
			return $res;
			
		if ( $height < $this->image_height && $height > 0 ) 
		{
			$this->thumb_max_height = $height;
			return true;
		}
		
		return false;
	}

	/**
	 * Sets the max. width of the thumbnail.
	 *
	 * @param int $width
	 * @return boolean
	 * @access public
	 * @uses readSourceImageData()
	 * @uses $image_width
	 * @uses $thumb_max_width
	 */
	function setMaxWidth( $width = 0 ) 
	{
		$res = $this->readSourceImageData();
		
		if ( PEAR::isError( $res ) )
			return $res;

		if ( $width < $this->image_width && $width > 0 ) 
		{
			$this->thumb_max_width = $width;
			return true;
		}
		
		return false;
	}

	/**
	 * Sets the max. width and height of the thumbnail.
	 *
	 * passes values to the functions setMaxHeight() and setMaxWidth()
	 *
	 * @param int $width
	 * @param int $height
	 * @return boolean
	 * @access public
	 * @uses setMaxHeight()
	 * @uses setMaxWidth()
	 */
	function setMaxSize( $width = 0, $height = 0 ) 
	{
		if ( $this->setMaxWidth( $width ) === true && $this->setMaxHeight( $height ) === true )
			return true;
		else
			return false;
	}

	/**
	 * Whether to create thumbs fast or with good quality.
	 *
	 * @param boolean $boolean
	 * @return void
	 * @access public
	 * @uses $quality_thumb
	 */
	function setQualityOutput( $boolean = true ) 
	{
		$this->quality_thumb = $boolean;
	}

	/**
	 * Reads metadata of the source image.
	 *
	 * @return void
	 * @access protected
	 * @uses $image_width
	 * @uses $image_height
	 * @uses $image_type
	 * @uses $formats
	 */
	function readSourceImageData() 
	{
		if ( !file_exists( $this->image_path ) ) 
		{ 
			// if source pic wasnt found
			$this->image_path   =  'error_pic';
			$this->image_width  =& $this->thumb_max_width;
			$this->image_height =& $this->thumb_max_height;
			
			$image = @imagecreatetruecolor( $this->image_width, $this->image_height );
			
			if ( !$image )
				return PEAR::raiseError( "Cannot initialize new GD image stream." ); 

			$text_color = imagecolorallocate( $this->image, 255, 255, 255 );
			
			imagestring( $this->image, 1, 2, ( $this->image_height / 2 - 10 ), "Could't find", $text_color );
			imagestring( $this->image, 1, 2, ( $this->image_height / 2 -  4 ), "Source image", $text_color );
			imagestring( $this->image, 1, 2, ( $this->image_height / 2 +  4 ), "(Thumbnail V" . $this->version . ")", $text_color );
		} 
		else 
		{
			if ( !isset($this->image_width ) ) 
			{
				list( $this->image_width, $this->image_height, $this->image_type, $attr ) = getimagesize( $this->image_path );
				unset( $attr );
				
				if ( !in_array( $this->image_type, $this->formats ) ) 
					return PEAR::raiseError( "Can't create thumbnail from '" . $this->image_type . "' source: " . $this->image_path );
			}
		}
	}

	/**
	 * Reads the source image into a variable.
	 *
	 * @return void
	 * @access protected
	 * @uses $image
	 * @uses readSourceImageData()
	 * @uses $image_type
	 * @uses $image_path
	 */
	function readSourceImage() 
	{
		if ( !isset( $this->image ) ) 
		{
		    $res = $this->readSourceImageData();
		
			if ( PEAR::isError( $res ) )
				return $res;
		
		    switch ( $this->image_type ) 
			{
		        case 1:
		            $this->image = imagecreatefromgif( $this->image_path );
		            break;
					
		        case 2:
		            $this->image = imagecreatefromjpeg( $this->image_path );
		            break;
					
		        case 3:
		            $this->image = imagecreatefrompng( $this->image_path );
		            break;
					
		        case 15:
		            $this->image = imagecreatefromwbmp( $this->image_path );
		            break;
					
		        case 999:
		        
				default:
					$this->image = imagecreatefromstring( $this->image_path );
					break;
		    }
		}
	}

	/**
	 * Sets the actual width and height of the thumbnail based on the 
	 * source image size and the max limits for the thumbnail.
	 *
	 * @return void
	 * @access protected
	 * @uses readSourceImageData()
	 * @uses $image_height
	 * @uses $thumb_max_height
	 * @uses $image_width
	 * @uses $thumb_max_width
	 */
	function setThumbnailSize() 
	{
	    $res = $this->readSourceImageData();
		
		if ( PEAR::isError( $res ) )
			return $res;
			
	    if ( ( $this->image_height > $this->thumb_max_height ) || ( $this->image_width < $this->thumb_max_width ) )
	        $sizefactor = (double)( ( $this->image_height > $this->image_width )? ( $this->thumb_max_height / $this->image_height ) : ( $this->thumb_max_width / $this->image_width ) );
	    else
	        $sizefactor = (int)1;
	    
	    $this->thumbnail_width  = (int)( $this->image_width  * $sizefactor );
	    $this->thumbnail_height = (int)( $this->image_height * $sizefactor );
	    unset( $sizefactor );
	}

	/**
	 * Creates the thumbnail and saves it to a variable.
	 *
	 * @return void
	 * @access protected
	 * @uses setThumbnailSize()
	 * @uses readSourceImage()
	 * @uses $thumbnail
	 * @uses $thumbnail_width
	 * @uses $thumbnail_height
	 * @uses $quality_thumb
	 * @uses $image
	 * @uses $image_width
	 * @uses $image_height
	 */
	function createThumbnail()
	{
		$this->setThumbnailSize();

		$res = $this->readSourceImage();
		
		if ( PEAR::isError( $res ) )
			return $res;
			
		if ( !isset( $this->thumbnail ) ) 
		{
			$this->thumbnail = imagecreatetruecolor( $this->thumbnail_width, $this->thumbnail_height );

			if ( $this->quality_thumb === true ) 
			{
				imagecopyresampled( $this->thumbnail, $this->image, 0, 0, 0, 0,
								    $this->thumbnail_width, $this->thumbnail_height,
								    $this->image_width, $this->image_height );
			} 
			else 
			{
				imagecopyresized( $this->thumbnail, $this->image, 0, 0, 0, 0,
								  $this->thumbnail_width, $this->thumbnail_height,
								  $this->image_width, $this->image_height );
			}
		}
	}

	/**
	 * Outputs the thumbnail to the browser.
	 *
	 * @param string $format
	 * @param int $quality
	 * @return void
	 * @access public
	 * @uses setOutputFormat()
	 * @uses createThumbnail()
	 * @uses $thumbnail_type
	 * @uses $thumbnail
	 */
	function outputThumbnail( $format = 'png', $quality = 75 ) 
	{
	    $this->setOutputFormat( $format );
	    $this->createThumbnail();
		
	    switch ( $this->thumbnail_type ) 
		{
	        case 1:
	        	header( 'Content-type: image/gif' );
	        	imagegif( $this->thumbnail );

	            break;

	        case 2:
	        	if ( $quality < 0 || $quality > 100 )
					$quality = 75;

	        	header( 'Content-type: image/jpeg' );
	        	imagejpeg( $this->thumbnail, '', $quality );

	            break;

	        case 3:
	        	header( 'Content-type: image/png' );
	            imagepng( $this->thumbnail );

	            break;

	        case 15:
	            header( 'Content-type: image/vnd.wap.wbmp' );
	            imagewbmp( $this->thumbnail );

	            break;
	    }

	    imagedestroy( $this->thumbnail );
	    imagedestroy( $this->image );
	}

	/**
	 * Returns the variable with the thumbnail image.
	 *
	 * @param string $format
	 * @return mixed
	 * @access public
	 * @uses setOutputFormat()
	 * @uses createThumbnail()
	 * @uses $thumbnail
	 */
	function returnThumbnail()
	{
		$this->setOutputFormat();
		$this->createThumbnail();
		
		return $this->thumbnail;
	}

	/**
	 * Returns the height of the thumbnail.
	 *
	 * @return int
	 * @access public
	 * @uses $thumbnail_height
	 */
	function getThumbHeight()
	{
		$this->createThumbnail();
		return $this->thumbnail_height;
	}

	/**
	 * Returns the width of the thumbnail.
	 *
	 * @return int
	 * @access public
	 * @uses $thumbnail_width
	 */
	function getThumbWidth()
	{
		$this->createThumbnail();
		return $this->thumbnail_width;
	}

	/**
	 * Returns the name of the thumbnail.
	 *
	 * @return int
	 * @access public
	 * @uses $thumbnail_width
	 */
	function getPictureName()
	{
		return $this->image_path;
	}
} // END OF Thumbnail

?>
