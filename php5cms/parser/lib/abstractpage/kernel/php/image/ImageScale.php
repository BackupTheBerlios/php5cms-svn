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
 * @package image
 */
 
class ImageScale extends PEAR
{
    /**
     * Placeholder for image being manipulated
     * @var  string
     */
    var $image;

    /**
     * ImageType of the image to scale to (eg. png, jpg, gif etc.)
     * @var  string
     */
    var $distExtension;

    /**
     * Quality of the scaled image (only applying to jpg)
     * @var  int
     */
    var $quality = 100;

    /**
     * Placeholder for imageType, eg. 'jpg' or 'png'
     * var $imageType;
     */
    var $imageType;

    /**
     * Wether or not to send headers when flushing image.
     * @var sendHeaders
     */
    var $sendHeaders = true;

	
    /**
	 * Constructor
	 *
     * Get various info about image. If imageType is empty, the class will try
     * to detect the 'imageType'. Currently autodetection of 'imageType' works with
     * png, gif, jpeg. Set imageType if image is eg. GD or WBMP.
     *
     * @param  string  orginal image to scale (url or file)
     * @param  string  imageType (eg. GD, WBMP)
	 * @access public
     */
    function ImageScale( $image = '', $imageType = '' )
	{
		if ( isset( $image ) )
            $this->setImage( $image, $imageType );
    }

	
    /**
     * Open info and set info.
     *
     * @param    string  (image or url)
     * @access   public
     */
    function setImage( $image, $imageType = '' )
	{
        $this->setImageInfo( $image );
		
        if ( $this->imageType == 'unknown' )
		{
            $this->imageType = $imageType;
			
            if ( empty( $this->imageType ) || $this->imageType == 'unknown' )
				return PEAR::raiseError( "Specify image type to scale from." );
        }
		
        if ( $this->imageType == 'gif' )
            $this->image = imagecreatefromgif( $image );
        else if ( $this->imageType == 'jpg' || $this->imageType == 'jpeg' )
            $this->image = imagecreatefromjpeg( $image );
        else if ( $this->imageType == 'png' )
            $this->image = imagecreatefrompng( $image );
        else if ( $this->imageType == 'gd' )
            $this->image = imagecreatefromgd( $image );
        else
            return PEAR::raiseError( "Unsupported source image type: " . $imageType );
    }

    /**
     * Find image size.
     *
     * @param    string  file path
     * @return   array   image info
     * @access   private
     */
    function setImageInfo( $image )
	{
        $this->info = getimagesize( $image, $this->info );
		
        if ( $this->info[2] == 1 )
            $this->imageType = 'gif';
        else if ( $this->info[2] == 2 )
            $this->imageType = 'jpg';
        else if ( $this->info[2] == 3 )
            $this->imageType = 'png';
        else
            $this->imageType = 'unknown';
    }

    /**
     * Scale according to a Maximum height.
     *
     * @param    int         maxWidth (maximum width)
     * @param    string      distImageType (scale to this imageType (jpg/png)
     * @param    string      save image in this file (if empty output to
     *                       browser)
     * @access   public
     */
    function scaleMaxHeight( $maxHeight, $filename = '', $distImageType = '' )
	{
        if ( empty( $distImageType ) )
            $distImageType=$this->imageType;
        
        if ( $this->info[0] <> $this->info[1] )
		{
            $x   = $maxHeight;
            $div = $this->info[0] / $maxHeight;
            $y   = (int)$this->info[1] / $div;
        }
		else
		{
            $x = $y = $maxHeight;
        }
		
        $this->scale( $x, $y, $filename, $distImageType );
    }

    /**
     * Scale according to a Maximum width.
     *
     * @param    int         maxWidth (maximum width)
     * @param    string      imageType (scale to this imageType (jpg/png)
     * @param    string      save image in this file (if empty output to
     *                       browser)
     * @access   public
     */
    function scaleMaxWidth( $maxWidth, $filename = '', $distImageType = '' )
	{
        if ( empty( $distImageType ) )
            $distImageType = $this->imageType;
        
        if ( $this->info[0] <> $this->info[1] )
		{
            $y   = $maxWidth;
            $div = $this->info[1] / $maxWidth;
            $x   = $this->info[0] / $div;
        }
		else
		{
            $x = $y = $maxWidth;
        }
		
        $this->scale( $x, $y, $filename, $distImageType );
    }

    /**
     * Scale according to x and y cordinates.
     *
     * @param    int         x Width
     * @param    int         y Height
     * @param    string      imageType (scale to this imageType (jpg/png)
     * @param    string      save image in this file (if empty output to
     *                       browser)
     * @access   public
     */
    function scaleXY( $x, $y, $filename = '', $distImageType = '' )
	{
        $this->scale( $x, $y, $filename, $distImageType );
    }

    /**
     * Scale image so the largest of x or y has gets a max of q.
     *
     * @param    int         max Width or Height
     * @param    string      imageType (scale to this imageType (jpg/png)
     * @param    string      save image in this file (if empty output to
     *                       browser)
     * @access   public
     */
    function scaleXorY( $max, $filename = '', $distImageType = '' )
	{
        if ( $this->info[0] < $this->info[1] )
            $this->scaleMaxWidth( $max, $filename, $distImageType );
        else
            $this->scaleMaxHeight( $max, $filename, $distImageType );
    }
	
    /**
     * Scale according to a percentage, eg 50.
     *
     * @param    int         percentage (percentage)
     * @param    string      imageType (scale to this imageType (eg. jpg/png)
     * @param    string      save image in this file (if empty output to
     *                       browser)
     * @access   public
     */
    function scalePercentage( $percentage, $filename = '', $distImageType = '' )
	{
        if ( empty( $distImageType ) )
            $distImageType = $this->imageType;
        
        $percentage = $percentage / 100;
        $x = $percentage * $this->info[0];
        $y = $percentage * $this->info[1];
		
        $this->scale( $x, $y, $filename, $distImageType );
    }

    /**
     * Scale the image.
     *
     * @param    $x          width
     * @param    $y          height
     * @param    $imageType  imageType (type of image)
     * @param    string      filename (file to put image to)
     * @access   private
     */
    function scale( $x, $y, $filename = '', $distImageType = '' )
	{
        if ( $distImageType == 'gif' )
		{
            $distImage = imagecreatetruecolor( $x, $y );
            $this->copyResampled( $distImage, $this->image, $x, $y );
			
            if ( empty( $filename ) )
			{
                header( "Content-Type: image/gif" );
                $res = @imagejpeg( $distImage, '', $this->quality );
            }
			else
			{
                imagegif( $distImage, $filename, $this->quality );
            }
        }
		else if ( $distImageType == 'jpg' || $distImageType == 'jpeg' )
		{
            $distImage = imagecreatetruecolor( $x, $y );
            $this->copyResampled( $distImage, $this->image, $x, $y );
			
            if ( empty( $filename ) )
			{
                header( "Content-Type: image/jpeg" );
                imagejpeg( $distImage, '', $this->quality );
            }
			else
			{
                imagejpeg( $distImage, $filename, $this->quality );
            }
        }
		else if ( $distImageType == 'png' )
		{
            $distImage = imagecreatetruecolor( $x, $y );
            $this->copyResampled( $distImage, $this->image, $x, $y );
			
            if ( empty( $filename ) )
			{
                header( "Content-Type: image/png" );
                imagepng( $distImage, '', $this->quality );
            }
			else
			{
                imagepng( $distImage, $filename, $this->quality );
            }
        }
		else if ( $distImageType == 'gd' )
		{
            $distImage = imagecreatetruecolor( $x, $y );
            $this->copyResampled( $distImage, $this->image, $x, $y );
            
			if ( empty( $filename ) )
			{
                header( "Content-Type: image/gd" );
                imagegd( $distImage, '', $this->quality );
            }
			else
			{
                imagegd( $distImage, $filename, $this->quality );
            }
        }
		else if ( $distImageType == 'wbmp' )
		{
            $distImage = imagecreatetruecolor( $x, $y );
            $this->copyResampled( $distImage, $this->image, $x, $y );
            
			if ( empty( $filename ) )
			{
                header( "Content-Type: image/wbmp" );
                imagewbmp( $distImage, '', $this->quality );
            }
			else
			{
                imagewbmp( $distImage, $filename, $this->quality );
            }
        }
		else
		{
            return PEAR::raiseError( "Could not transform image." );
        }
    }

    /**
     * Resample the image.
     *
     * @param    resource    distImage (destination image)
     * @param    resource    image (sourceImage)
     * @param    int
     * @param    int
     * @access   private
     */
    function copyResampled( &$distImage, $image, $x, $y )
	{
        imagecopyresampled(
            $distImage,
            $image,
            0, 0, 0, 0,
            $x,
            $y,
            $this->info[0],
            $this->info[1]
        );
		
        return '';
    }
} // END OF ImageScale

?>
