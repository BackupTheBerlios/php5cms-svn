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
 * ImageMagick class 
 *
 * Requirements:
 *
 * - PHP4, not running in safe mode
 * - ImageMagick installation
 * - The ability to chown a dir
 *
 * @package image
 */

class ImageMagick extends PEAR
{
	/**
	 * httpd must be able to write there
	 * @access public
	 */
	var $temp_dir;
	
	/**
	 * @access public
	 */
	var $targetdir = '';
	
	/**
	 * @access public
	 */
	var $imagemagickdir = '/usr/local/bin/';
	
	/**
	 * @access public
	 */
	var $file_history = array();

	/**
	 * @access public
	 */
	var $temp_file = '';
	
	/**
	 * @access public
	 */
	var $jpg_quality = '65';
	
	/**
	 * @access public
	 */
	var $count = 0;
	
	/**
	 * @access public
	 */
	var $image_data = array();


	/**
	 * Constructor places uploaded file in $this->temp_dir
	 * Gets the imagedata and stores it in $this->image_data
	 * $filedata = $_FILES['file1']
	 *
	 * @access public
	 */
	function ImageMagick( $filedata ) 
	{
		$this->temp_dir  = ap_ini_get( "path_tmp_os", "path" );
		$this->temp_file = time() . ereg_replace("[^a-zA-Z0-9_.]", '_', $filedata['name'] );
		
		if ( !@rename( $filedata['tmp_name'], $this->temp_dir . 'tmp' . $this->count . '_' . $this->temp_file ) )
		{
			$this = new PEAR_Error( "Upload failed." );
			return;
		}
		
		$this->file_history[] = $this->temp_dir . 'tmp' . $this->count . '_' . $this->temp_file;
		$this->getSize();
	}


	/**
	 * Sets the dir to where the images are saved
	 * httpd user must have write access there.
	 *
	 * @access public
	 */
	function setTargetDir( $target ) 
	{
		if ( $target == '' )
			$this->targetdir = $this->temp_dir;
		else
			$this->targetdir = $target;
	}

	/**
	 * Returns the current filename.
	 *
	 * @access public
	 */
	function getFilename() 
	{
		return $this->temp_file;
	}

	/**
	 * Returns the size of the image in an array.
	 * array[0] = x-size
	 * array[1] = y-size
	 *
	 * @access public
	 */
	function getSize() 
	{
		$command = $this->imagemagickdir . "identify -verbose '" . $this->temp_dir . 'tmp' . $this->count . '_' . $this->temp_file . "'";
		exec( $command, $returnarray, $returnvalue );
		
		if ( $returnvalue )
		{
			return PEAR::raiseError( "Corrupt image." );
		}
		else 
		{
			$num = count( $returnarray );
			
			for ( $i = 0; $i < $num; $i++ )
				$returnarray[$i] = trim( $returnarray[$i] );
				
			$this->image_data = $returnarray;
		}
		
		$num = count( $this->image_data );
		
		for ( $i = 0; $i < $num; $i++ )
		{
			if ( eregi( 'Geometry', $this->image_data[$i] ) ) 
			{
				$tmp1 = explode( ' ', $this->image_data[$i] );
				$tmp2 = explode( 'x', $tmp1[1] );
				$this->size = $tmp2;
				
				return $tmp2;
			}
		}
	}

	/**
	 * Flips the image.
	 * Possible arguments:
	 * 'horizontal' > flips the image horizontaly
	 * 'vertical'   > flips the image verticaly
	 * default is horizontal
	 *
	 * @access public
	 */
	function flip( $var = 'horizontal' ) 
	{
		$tmp = ( $var == 'horizontal' )? '-flop' : ( $var == 'vertical'? '-flip' : '' );
		$command = "{$this->imagemagickdir}convert {$tmp} '{$this->temp_dir}tmp{$this->count}_{$this->temp_file}' '{$this->temp_dir}tmp" . ++$this->count . "_{$this->temp_file}'";

		exec( $command, $returnarray, $returnvalue );
		
		if ( $returnvalue )
			return PEAR::raiseError( "Flip failed." );
		else
			$this->file_history[] = $this->temp_dir . 'tmp' . $this->count . '_' . $this->temp_file;
	}

	/**
	 * Dithers the image.
	 *
	 * @access public
	 */
	function dither() 
	{
		$command = "{$this->imagemagickdir}convert -dither '{$this->temp_dir}tmp{$this->count}_{$this->temp_file}' '{$this->temp_dir}tmp" . ++$this->count . "_{$this->temp_file}'";
		exec($command, $returnarray, $returnvalue);
		
		if ( $returnvalue )
			return PEAR::raiseError( "Dither failed." );
		else
			$this->file_history[] = $this->temp_dir . 'tmp' . $this->count . '_' . $this->temp_file;
	}

	/**
	 * Converts the image to monochrome (2 color black-white).
	 *
	 * @access public
	 */
	function monochrome() 
	{
		$command = "{$this->imagemagickdir}convert -monochrome '{$this->temp_dir}tmp{$this->count}_{$this->temp_file}' '{$this->temp_dir}tmp" . ++$this->count . "_{$this->temp_file}'";
		exec( $command, $returnarray, $returnvalue );
		
		if ( $returnvalue )
			return PEAR::raiseError( "Monochrome failed." );
		else
			$this->file_history[] = $this->temp_dir . 'tmp' . $this->count . '_' . $this->temp_file;
	}

	/**
	 * Converts the image to it's negative.
	 *
	 * @access public
	 */
	function negative() 
	{
		$command = "{$this->imagemagickdir}convert -negate '{$this->temp_dir}tmp{$this->count}_{$this->temp_file}' '{$this->temp_dir}tmp" . ++$this->count . "_{$this->temp_file}'";
		exec( $command, $returnarray, $returnvalue );
		
		if ( $returnvalue )
			return PEAR::raiseError( "Negative failed." );
		else
			$this->file_history[] = $this->temp_dir . 'tmp' . $this->count . '_'.$this->temp_file;
	}

	/**
	 * Rotates the image.
	 * possible values for arg1:
	 *	numbers from 0-360
	 * possible values for arg2:
	 *	hexadecimal color without the "#" for example: C3D6A0
	 * possible values for arg3:
	 *	no value > standard rotation
	 *	'morewidth' > rotates the image only if only if its width exceeds the height
	 *	'lesswidth' > rotates the image only if its width is less than the height
	 *
	 * @access public
	 */
	function rotate( $deg = 90, $bgcolor = '000000', $how = '' ) 
	{
		$tmp = ( $how == 'lesswidth' )? "<" : ( ( $how == 'morewidth' )? ">" : '' );
		$command = "{$this->imagemagickdir}convert -background '#{$bgcolor}' -rotate '{$deg}{$tmp}' '{$this->temp_dir}tmp{$this->count}_{$this->temp_file}' '{$this->temp_dir}tmp" . ++$this->count . "_{$this->temp_file}'";

		exec( $command, $returnarray, $returnvalue );
		
		if ( $returnvalue )
			return PEAR::raiseError( "Rotate failed." );
		else
			$this->file_history[] = $this->temp_dir . 'tmp' . $this->count . '_' . $this->temp_file;
	}

	/**
	 * Blur the image with a gaussian operator.
	 * arg1 > radius
	 * arg2 > sigma
	 *
	 * @access public
	 */
	function blur( $radius = 5, $sigma = 2 ) 
	{
		$command = "{$this->imagemagickdir}convert -blur '{$radius}x{$sigma}' '{$this->temp_dir}tmp{$this->count}_{$this->temp_file}' '{$this->temp_dir}tmp" . ++$this->count . "_{$this->temp_file}'";
		exec( $command, $returnarray, $returnvalue );
		
		if ( $returnvalue )
			return PEAR::raiseError( "Blur failed." );
		else
			$this->file_history[] = $this->temp_dir . 'tmp' . $this->count . '_' . $this->temp_file;
	}

	/**
	 * Draws a frame around the image
	 * arg1 > frame width in pixels
	 * arg2 > frame color in hexadecimal, for example: 4AF2C9
	 *
	 * @access public
	 */
	function frame( $width = 6, $color = '666666' ) 
	{
		$command = "{$this->imagemagickdir}convert -mattecolor '#{$color}' -frame '{$width}x{$width}' '{$this->temp_dir}tmp{$this->count}_{$this->temp_file}' '{$this->temp_dir}tmp".++$this->count."_{$this->temp_file}'";
		exec( $command, $returnarray, $returnvalue );
		
		if ( $returnvalue )
			return PEAR::raiseError( "Frame failed." );
		else
			$this->file_history[] = $this->temp_dir . 'tmp' . $this->count . '_' . $this->temp_file;
	}

	/**
	 * Resize the image to given size.
	 * possible values:
	 * arg1 > x-size, unsigned int
	 * arg2 > y-size, unsigned int
	 * arg3 > resize method;
	 *	'keep_aspect' > changes only width or height of image
	 *	'fit' > fit image to given size
	 *
	 * @access public
	 */
	function resize( $x_size, $y_size, $how = 'keep_aspect' ) 
	{
		$method  = ( $how == 'keep_aspect' )? '>' : ( ( $how == 'fit' )? '!' : '' );
		$command = "{$this->imagemagickdir}convert -geometry '{$x_size}x{$y_size}{$method}' '{$this->temp_dir}tmp{$this->count}_{$this->temp_file}' '{$this->temp_dir}tmp" . ++$this->count . "_{$this->temp_file}'";

		exec( $command, $returnarray, $returnvalue );

		if ( $returnvalue )
			return PEAR::raiseError( "Resize failed." );
		else
			$this->file_history[] = $this->temp_dir . 'tmp' . $this->count . '_' . $this->temp_file;
	}

	/**
	 * Makes the image a square.
	 * possible arguments:
	 * 'center' > crops to a square in the center of the image
	 * 'left'   > crops to a square on the left side of the image
	 * 'right'  > crops to a square on the right side of the image
	 *
	 * @access public
	 */
	function square( $how = 'center' ) 
	{
		$this->size = $this->getSize();
		
		if ( $how == 'center' ) 
		{
			if ( $this->size[0] > $this->size[1] )
				$line = $this->size[1] . 'x' . $this->size[1] . '+'   . round( ( ( $this->size[0] - $this->size[1] ) / 2 ) ) . '+0';
			else
				$line = $this->size[0] . 'x' . $this->size[0] . '+0+' . round( ( ( $this->size[1] - $this->size[0] ) ) / 2 );
		}
		
		if ( $how == 'left' ) 
		{
			if ( $this->size[0] > $this->size[1] )
				$line = $this->size[1] . 'x' . $this->size[1] . '+0+0';
			else
				$line = $this->size[0] . 'x' . $this->size[0] . '+0+0';
		}
		
		if ( $how == 'right' ) 
		{
			if ( $this->size[0] > $this->size[1] )
				$line = $this->size[1] . 'x' . $this->size[1] . '+'   . ( $this->size[0] - $this->size[1] ) . '+0';
			else
				$line = $this->size[0] . 'x' . $this->size[0] . '+0+' . ( $this->size[1] - $this->size[0] );
		}

		$command = "{$this->imagemagickdir}convert -crop '{$line}' '{$this->temp_dir}tmp{$this->count}_{$this->temp_file}' '{$this->temp_dir}tmp" . ++$this->count . "_{$this->temp_file}'";
		exec( $command, $returnarray, $returnvalue );
		
		if ( $returnvalue )
			return PEAR::raiseError( "Square failed." );
		else
			$this->file_history[] = $this->temp_dir . 'tmp' . $this->count . '_' . $this->temp_file;
	}

	/**
	 * Crops the image to given size.
	 * arg1 > x-size, unsigned int
	 * arg2 > y-size, unsigned int
	 * arg3 > method;
	 *	'center', crops the image leaving the center
	 *	'left', crops only from the right side
	 * 'right', crops only from the left side
	 *
	 * @access public
	 */
	function crop( $size_x, $size_y, $how = 'center' ) 
	{
		$this->size = $this->getSize();

		if ( $size_x > $this->size[0] )
			$size_x = $this->size[0];

		if ( $size_y > $this->size[1] )
			$size_y = $this->size[1];

		if ( $how == 'center' )
			$line = $size_x . 'x' . $size_y . '+' . round( ( $this->size[0] - $size_x ) / 2 ) . '+' . round( ( ( $this->size[1] - $size_y ) / 2 ) );

		if ( $how == 'left' )
			$line = $size_x . 'x' . $size_y . '+0+0';

		if ( $how == 'right' )
			$line = $size_x . 'x' . $size_y . '+' . ( $this->size[0] - $size_x ) . '+0';

		$command = "{$this->imagemagickdir}convert -crop '{$line}' '{$this->temp_dir}tmp{$this->count}_{$this->temp_file}' '{$this->temp_dir}tmp" . ++$this->count . "_{$this->temp_file}'";
		exec( $command, $returnarray, $returnvalue );

		if ( $returnvalue )
			return PEAR::raiseError( "Crop failed." );
		else
			$this->file_history[] = $this->temp_dir . 'tmp' . $this->count . '_' . $this->temp_file;
	}

	/**
	 * Converts the image to any format using the given file extension.
	 * Defaults to jpg.
	 *
	 * @access public
	 */
	function convert( $format = 'jpg' ) 
	{
		$name = explode( '.' , $this->temp_file );
		$name = "{$name[0]}.{$format}";

		$command = "{$this->imagemagickdir}convert -colorspace RGB -quality {$this->jpg_quality} '{$this->temp_dir}tmp{$this->count}_{$this->temp_file}' '{$this->temp_dir}tmp" . ++$this->count . "_{$name}'";
		exec( $command, $returnarray, $returnvalue );

		$this->temp_file = $name;

		if ( $returnvalue )
			return PEAR::raiseError( "Convert failed." );
		else
			$this->file_history[] = $this->temp_dir . 'tmp' . $this->count . '_' . $this->temp_file;
	}

	/**
	 * Saves the image to the targetdir, returning the filename.
	 * arg1 > set prefix, for example : 'thumb_'
	 *
	 * @access public
	 */
	function save( $prefix = '' ) 
	{
		if ( !@copy( $this->temp_dir . 'tmp' . $this->count . '_' . $this->temp_file, $this->targetdir . '/' . $prefix . $this->temp_file ) ) 
			return PEAR::raiseError( "Couldn't save to '{$this->targetdir}/{$prefix}{$this->temp_file}'." );

		return $prefix . $this->temp_file;
	}

	/**
	 * Cleans up all the temp data in $this->tempdir.
	 *
	 * @access public
	 */
	function cleanup()
	{
		$num = count( $this->file_history );

		for ( $i = 0; $i < $num; $i++ ) 
		{
			if ( !unlink( $this->file_history[$i] ) )
				return PEAR::raiseError( "Removal of temporary file '{$this->file_history[$i]}' failed." );
		}
	}
} // END OF ImageMagick

?>
