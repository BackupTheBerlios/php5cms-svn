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
 * Creates a thumbnail from a source image and caches it for a given time.
 *
 * Tested with Apache 1.3.27 and PHP 4.3.3
 *
 * @package image_watermark_lib
 */

class PictureCache extends PEAR
{
	/**
	 * name of the cache dir
	 *
	 * @var string
	 * @access protected
	 */
	var $cache_dir = 'cache/';

	/**
	 * prefix for every cache file
	 *
	 * @var string
	 * @access protected
	 */
	var $prefix = 'tc_';

	/**
	 * check if cache dir is available
	 *
	 * @var boolean
	 * @access protected
	 */
	var $check_cache_dir = false;

	/**
	 * amount of seconds thumbs should be cached. 0 = no cache
	 *
	 * @var int
	 * @access protected
	 */
	var $cache_time = 0;

	/**
	 * possible image formats
	 *
	 * @var array
	 * @access protected
	 */
	var $types = array(
		1   => 'gif', 
		2   => 'jpg', 
		3   => 'png', 
		15  => 'wbmp', 
		999 => 'string'
	);

	/**
	 * name/path of picture
	 *
	 * @var string
	 * @access protected
	 */
	var $image_path;

	/**
	 * image type of cached thumbnail
	 *
	 * @var int
	 * @access protected
	 */
	var $image_type = 3;

	
	/**
	 * Constructor
	 *
	 * @param string $file  path/filename of picture
	 * @param int $type  image type
	 * @param int $seconds  amount of seconds thumbs should be cached. 0 = no cache
	 * @return void
	 * @access public
	 * @uses checkCacheDir()
	 * @uses $cache_time
	 * @uses $image_path
	 * @uses $image_type
	 */
	function PictureCache( $file = '', $type = 3, $seconds = 0 ) 
	{
  		if ( $this->checkCacheDir() === false ) 
		{
			$this = new PEAR_Error( "Cannot create cache directory." );
			return;
		}
		$this->cache_time = (int)$seconds;
		$this->image_path = (string)$file;
		$this->image_type = (int)$type;
	}


	/**
	 * Checks if the cache directory exists, else trys to create it.
	 *
	 * @return boolean
	 * @access protected
	 * @uses $check_cache_dir
	 * @uses $cache_dir
	 */
	function checkCacheDir() 
	{
		if ( $this->check_cache_dir === false || is_dir( $this->cache_dir ) )
			return true;
		else
			return ( ( !mkdir( $this->cache_dir, 0700 ) )? false : true );
	}

	/**
	 * Sticks together filename + path.
	 *
	 * @return string
	 * @access public
	 * @uses $cache_dir
	 * @uses $prefix
	 * @uses $image_path
	 * @uses $types
	 * @uses $image_type
	 */
	function returnCachePicturename() 
	{
		return $this->cache_dir . $this->prefix . md5( $this->image_path ) . '.' . $this->types[$this->image_type];
	}

	/**
	 * Checks if a picture is cached and up to date.
	 *
	 * @return boolean
	 * @access public
	 * @uses returnCachePicturename()
	 * @uses $cache_time
	 */
	function isPictureCached() 
	{
		$filetime = @filemtime( $this->returnCachePicturename() );
		
		if ( !isset( $filetime ) )
		 	return false;
		
		if ( ( time() - $filetime ) > $this->cache_time )
			return false;
		
		return true;
	}

	/**
	 * Returns a cached picture.
	 *
	 * @return mixed
	 * @access public
	 * @uses returnCachePicturename()
	 */
	function returnPictureCache() 
	{
		return readfile( $this->returnCachePicturename(), 'r' );
	}

	/**
	 * Writes a thumbnail to a file.
	 *
	 * @param $image variable with image
	 * @param int $quality jpg-quality: 0-100
	 * @return void
	 * @access public
	 * @uses $thumbnail_type
	 * @uses $thumbnail
	 * @uses returnCachePicturename()
	 */
	function writePictureCache( $image = '', $quality = 75 ) 
	{
	    if ( strlen( trim( $image ) ) > 0 ) 
		{
		    switch ( $this->image_type ) 
			{
		        case 1:
		        	imagegif( $image, $this->returnCachePicturename() );
		            break;
					
		        case 2:
		        	if ( $quality < 0 || $quality > 100 )
						$quality = 75;
		        	
					imagejpeg( $image, $this->returnCachePicturename(), $quality );
		            break;
					
		        case 3:
		            imagepng( $image, $this->returnCachePicturename() );
		            break;
					
		        case 15:
		        	imagewbmp( $image, $this->returnCachePicturename() );
		            break;
		    }
		}
	}

	/**
	 * Returns the path/filename of the cached thumbnail.
	 * If cached pic is not available returns false.
	 *
	 * @param string $format gif, jpg, png, wbmp
	 * @param int $quality jpg-quality: 0-100
	 * @return mixed string or false if no cached pic is available
	 * @access public
	 * @uses returnCachePicturename()
	 */
	function getCacheFilepath( $format = 'png', $quality = 75 ) 
	{
		if ( file_exists( $this->returnCachePicturename() ) )
			return $this->returnCachePicturename();
		else
			return false;
	}

	/**
	 * Returns the cache time in seconds.
	 *
	 * @return int $cache_time
	 * @access public
	 * @uses $cache_time
	 */
	function getCacheTime()
	{
		return $this->cache_time;
	}
} // END OF PictureCache

?>
