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
|         Andrew Collington <php@amnuts.com>                           |
+----------------------------------------------------------------------+
*/


define( 'IMAGEMASK_TOPLEFT',     0 );
define( 'IMAGEMASK_TOP',         1 );
define( 'IMAGEMASK_TOPRIGHT',    2 );
define( 'IMAGEMASK_LEFT',        3 );
define( 'IMAGEMASK_CENTRE',      4 );
define( 'IMAGEMASK_CENTER',      4 );
define( 'IMAGEMASK_RIGHT',       5 );
define( 'IMAGEMASK_BOTTOMLEFT',  6 );
define( 'IMAGEMASK_BOTTOM',      7 );
define( 'IMAGEMASK_BOTTOMRIGHT', 8 );
define( 'IMAGEMASK_RESIZE',      9 );


/**
 * This class is meant to apply a mask to a given image, 
 * much like you could do in PhotoShop, Gimp, or any other 
 * such image manipulation program.
 *
 * If the mask is smaller than the image then the mask can 
 * be placed in various positions (top left, left, top right, 
 * left, centre, right, bottom left, bottom, bottom right) 
 * or the mask can be resized to the dimensions of the image.
 *
 * This class has to copy an image one pixel at a time. 
 * Please bare in mind that this process may take quite some 
 * time on large images, so it is probably best that it is 
 * used on thumbnails and smaller images.
 *
 * The processing is done in true color. Therefore the 
 * resulting masked is generated in either JPEG or PNG.
 *
 * Usage:
 * 
 * $im = new ImageMask( $color );
 * $im->maskOption($_POST['position']);
 * 
 * if ( $im->loadImage( $image_file )
 * {
 *		if ( !PEAR::isError( $im->applyMask( $mask_file ) )
 *			$im->showImage( 'png' );
 * }
 *
 * @package image
 */
 
class ImageMask extends PEAR
{
	/**
	 * @access private
	 */
    var $_colours;
	
	/**
	 * @access private
	 */
    var $_img;
	
	/**
	 * @access private
	 */
    var $_mask;
	
	/**
	 * @access private
	 */
    var $_bgc;

	/**
	 * @access private
	 */
    var $_maskDynamic;
    
    
    /**
	 * Constructor
	 *
     * @param  string $bg Pass the background colour as an HTML colour string.
	 * @access public
     */
    function ImageMask( $bg = 'FFFFFF' )
    {
        $this->maskOption( IMAGEMASK_CENTER );
		
        $this->_colours = array();
        $this->_img     = array();
        $this->_mask    = array();
        $this->_bgc     = $this->_htmlHexToBinArray( $bg );
    }
    
    
    /**
	 * Load an image from the file system - method based on file extension.
	 *
     * @return bool
     * @param  string $filename
	 * @access public
     */
    function loadImage( $filename )
    {
        return ( $this->_realLoadImage( $filename, $this->_img['orig'] ) );
    }
    
    /**
	 * Load an image from a string (eg. from a database table).
	 *
     * @return bool
     * @param  string $string
     * @access public
     */
    function loadImageFromString( $string )
    {
        $this->_img['orig'] = @imagecreatefromstring( $string );
		
        if ( $this->_img['orig'] )
            return true;
        else
            return PEAR::raiseError( "The original image could not be loaded." );
    }
    
    /**
	 * Save the masked image.
	 *
     * @return bool
     * @param  string $filename
     * @param  int $quality
     * @access public
     */
    function saveImage( $filename, $quality = 100 )
    {
        if ( $this->_img['final'] == null )
            return PEAR::raiseError( "There is no processed image to save." );
        
        $ext  = strtolower( $this->_getExtension( $filename ) );
        $func = "image$ext";
        
        if ( !@function_exists( $func ) )
            return PEAR::raiseError( "That file cannot be saved with the function '$func'.");
        
        $saved = ( $ext == 'png' )? $func($this->_img['final'], $filename ) : $func( $this->_img['final'], $filename, $quality );

        if ( $saved == false )
            return PEAR::raiseError( "Could not save the output file '$filename' as a $ext." );
        
        return true;
    }
    
    /**
	 * Shows the masked image without any saving.
	 *
     * @return bool
     * @param  string $type
     * @param  int $quality
	 * @access public
     */
    function showImage( $type = 'png', $quality = 100 )
    {
        $type = strtolower( $type );
		
        if ( $this->_img['final'] == null )
      	{
            return PEAR::raiseError( "There is no processed image to show." );
        }
        else if ( $type == 'png' )
        {
            header( 'Content-type: image/png' );
            echo @imagepng( $this->_img['final'] );

            return true;
        }
        else if ( $type == 'jpg' || $type == 'jpeg' )
        {
            header( 'Content-type: image/jpeg' );
            echo @imagejpeg( $this->_img['final'], '', $quality );
			
            return true;
        }
        else
        {
            return PEAR::raiseError( "Could not show the output file as a $type." );
        }
		
        return false;
    }
    
    /**
	 * Set the mask overlay option (position or resize to image size).
	 *
     * @return void
     * @param int $do
	 * @access public
     */
    function maskOption( $do = IMAGEMASK_CENTER )
    {
        $this->_maskDynamic = $do;
    }
        
    /**
	 * Apply the mask to the image.
	 *
     * @return bool
     * @param  string $filename
	 * @access public
     */
    function applyMask( $filename )
    {
        if ( $this->_img['orig'])
        {
            if ( !PEAR::isError( $this->_generateInitialOutput() ) )
            {
                if ( !PEAR::isError( $this->_realLoadImage( $filename, $this->_mask['orig'] ) ) )
                {
                    if ( $this->_getMaskImage() )
                    {
                        $sx = imagesx( $this->_img['final'] );
                        $sy = imagesy( $this->_img['final'] );
                        
                        set_time_limit( 120 );
						
                        for ( $x = 0; $x < $sx; $x++ )
                        {
                            for ( $y = 0; $y < $sy; $y++ )
                            {
                                $thres = $this->_pixelAlphaThreshold( $this->_mask['gray'], $x, $y );
								
                                if ( !in_array( $thres, array_keys( $this->_colours ) ) )
                                    $this->_colours[$thres] = imagecolorallocatealpha( $this->_img['final'], $this->_bgc[0], $this->_bgc[1], $this->_bgc[2], $thres );
                                
                                imagesetpixel( $this->_img['final'], $x, $y, $this->_colours[$thres] );
                            }
                        }
						
                        return true;
                    }
                    else
                    {
                        return PEAR::raiseError( "The grayscale mask could not be created." );
                    }
                }
				else
				{
					return PEAR::raiseError( "Unable to apply mask." );
				}
            }
        }
        else
        {
            return PEAR::raiseError( "The original image has not been loaded." );
        }
    }
    
	
	// private methods
    
    /**
     * @return bool
     * @param  string $filename
     * @param  pointer $img
     * @access private
     */
    function _realLoadImage( $filename, &$img )
    {
        if ( !@file_exists( $filename) )
            return PEAR::raiseError( "The supplied filename '$filename' does not point to a readable file." );
        
        $ext  = strtolower( $this->_getExtension( $filename ) );
        $func = "imagecreatefrom$ext";
        
        if ( !@function_exists( $func ) )
            return PEAR::raiseError( "That file cannot be loaded with the function '$func'." );
        
        $img = @$func( $filename );
		
		if ( $img )
			return true;
		else
			return PEAR::raiseError( "Unable to load image." );
    }
    
    /**
	 * Copies the original image into the final image ready for the mask overlay.
	 *
     * @return bool
	 * @access private
     */
    function _generateInitialOutput()
    {
        if ( $this->_img['orig'] )
        {
            $isx = imagesx( $this->_img['orig'] );
            $isy = imagesy( $this->_img['orig'] );
            $this->_img['final'] = imagecreatetruecolor( $isx, $isy );
            
			if ( $this->_img['final'] )
            {
                imagealphablending( $this->_img['final'], true );
                imagecopyresampled( $this->_img['final'], $this->_img['orig'], 0, 0, 0, 0, $isx, $isy, $isx, $isy );
				
                return true;
            }
            else
            {
                return PEAR::raiseError( "The final image (without the mask) could not be created." );
            }
        }
        else
        {
            return PEAR::raiseError( "The original image has not been loaded." );
        }
    }
    
    /**
	 * Creates the mask image and determines position and size of mask
     * based on the _maskOption value and image size.  If the image is
     * smaller than the mask (and the mask isn't set to resize) then the
     * mask defaults to the top-left position and will be cut off.
	 *
     * @return bool
     * @access private
     */
    function _getMaskImage()
    {
        $isx = imagesx( $this->_img['final'] );
        $isy = imagesy( $this->_img['final'] );
        $msx = imagesx( $this->_mask['orig'] );
        $msy = imagesy( $this->_mask['orig'] );
        
        $this->_mask['gray'] = imagecreatetruecolor( $isx, $isy );
        imagefill( $this->_mask['gray'], 0, 0, imagecolorallocate( $this->_mask['gray'], 0, 0, 0 ) );
        
        if ( $this->_mask['gray'] )
        {
            switch ( $this->_maskDynamic )
            {
                case IMAGEMASK_TOPLEFT:
                    $sx = 0;
					$sy = 0;
                    
					break;
					
                case IMAGEMASK_TOP:
                    $sx = ceil( ( $isx - $msx ) / 2 );
                    $sy = 0;
                    
					break;
                
				case IMAGEMASK_TOPRIGHT:
                    $sx = ( $isx - $msx );
                    $sy = 0;
					
                    break;
                
				case IMAGEMASK_LEFT:
                    $sx = 0;
                    $sy = ceil( ( $isy - $msy ) / 2 );
					
                    break;
                
				case IMAGEMASK_CENTRE:
                    $sx = ceil( ( $isx - $msx ) / 2 );
                    $sy = ceil( ( $isy - $msy ) / 2 );
					
                    break;
                
				case IMAGEMASK_RIGHT:
                    $sx = ( $isx - $msx );
                    $sy = ceil( ( $isy - $msy ) / 2 );
					
                    break;
                
				case IMAGEMASK_BOTTOMLEFT:
                    $sx = 0;
                    $sy = ( $isy - $msy );
					
                    break;
                
				case IMAGEMASK_BOTTOM:
                    $sx = ceil( ( $isx - $msx ) / 2 );
                    $sy = ( $isy - $msy );
					
                    break;
                
				case IMAGEMASK_BOTTOMRIGHT:
                    $sx = ( $isx - $msx );
                    $sy = ( $isy - $msy );

                    break;
            }
			
            if ( $isx < $msx )
                $sx = 0;
            
            if ( $isy < $msy )
                $sy = 0;
            
            if ( $this->_maskDynamic == IMAGEMASK_RESIZE )
            {
                $this->_mask['temp'] = imagecreatetruecolor( $isx, $isy );
                imagecopyresampled( $this->_mask['temp'], $this->_mask['orig'], 0, 0, 0, 0, $isx, $isy, $msx, $msy );
                imagecopymergegray( $this->_mask['gray'], $this->_mask['temp'], 0, 0, 0, 0, $isx, $isy, 100 );
                imagedestroy( $this->_mask['temp'] );
            }
            else
            {
                imagecopymergegray( $this->_mask['gray'], $this->_mask['orig'], $sx, $sy, 0, 0, $msx, $msy, 100 );
            }
			
            return true;
        }
		
        return false;
    }
    
    /**
	 * Determines the colour value of a pixel and returns the required value for the alpha overlay.
	 *
     * @return int
     * @param  resource $img
     * @param  int $x
     * @param  int $y
     * @access private
     */
    function _pixelAlphaThreshold( $img, $x, $y )
    {   
        $rgb = imagecolorat( $img, $x, $y );
        $r   = ( $rgb >> 16 ) & 0xFF;
        $g   = ( $rgb >>  8 ) & 0xFF;
        $b   = $rgb & 0xFF;
        $ret = round( ( $r + $g + $b ) / 6 );
		
        return ( $ret > 1 )? ( $ret - 1 ) : 0;
    }
    
    /**
	 * Converts an HTML hex colour value to an array of integers.
	 *
     * @return array
     * @param  string $hex
     * @access private
     */
    function _htmlHexToBinArray( $hex )
    {
        $hex = @preg_replace( '/^#/', '', $hex );
		
        for ( $i = 0; $i < 3; $i++ )
        {
            $foo = substr( $hex, 2 * $i, 2 );
            $rgb[$i] = 16 * hexdec( substr( $foo, 0, 1 ) ) + hexdec( substr( $foo, 1, 1 ) );
        }
		
        return $rgb;
    }
    
    /**
	 * Get the extension of a file name.
	 *
     * @return string
     * @param  string $filename
     * @access private
     */
    function _getExtension( $filename )
    {
        $ext = @strtolower( @substr( $filename, ( @strrpos( $filename, "." )? @strrpos( $filename, "." ) + 1 : @strlen( $filename ) ), @strlen( $filename ) ) );
        return ( $ext == 'jpg' )? 'jpeg' : $ext;
    }
} // END OF ImageMask

?>
