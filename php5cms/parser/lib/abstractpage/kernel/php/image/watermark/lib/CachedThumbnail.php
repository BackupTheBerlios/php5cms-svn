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


using( 'image.watermark.lib.Thumbnail' );
using( 'image.watermark.lib.PictureCache' );


/**
 * Creates a thumbnail from a source image and caches it for a given time.
 *
 * Tested with Apache 1.3.27 and PHP 4.3.3
 *
 * @package image_watermark_lib
 */

class CachedThumbnail extends Thumbnail
{
	/**
	 * amount of seconds thumbs should be cached. 0 = no cache
	 *
	 * @var int
	 * @access protected
	 */
	var $cache_time = 0;

	/**
	 * flipped formats array
	 *
	 * @var array
	 * @access protected
	 */
	var $types;

	/**
	 * holds PictureCache object
	 *
	 * @var object
	 * @access protected
	 */
	var $cache;

	
	/**
	 * Constructor
	 *
	 * @param string $file  path/filename of picture
	 * @param int $seconds  amount of seconds thumbs should be cached. 0 = no cache
	 * @return void
	 * @access public
	 * @uses Thumbnail::Thumbnail()
	 * @uses $cache_time
	 * @uses $types
	 */
	function CachedThumbnail( $file = '', $seconds = 0 ) 
	{
  		$this->Thumbnail( $file );
		
		$this->cache_time = $seconds;
		$this->types = array_flip( $this->formats );
	}

	
	/**
	 * Fills the cache variable with the cache object.
	 *
	 * @return void
	 * @access protected
	 * @uses PictureCache
	 * @uses $cache_time
	 * @uses $cache
	 * @uses $image_path
	 * @uses $thumbnail_type
	 */
	function setCache()
	{
		if ( !isset( $this->cache ) )
			$this->cache = new PictureCache( $this->image_path, $this->thumbnail_type, $this->cache_time );
	}

	/**
	 * Outputs the thumbnail to the browser.
	 *
	 * @param string $format gif, jpg, png, wbmp
	 * @param int $quality jpg-quality: 0-100
	 * @return mixed
	 * @access public
	 * @uses setOutputFormat()
	 * @uses setCache()
	 * @uses $cache_time
	 * @uses Thumbnail::isPictureCached()
	 * @uses Thumbnail::createThumbnail()
	 * @uses PictureCache::writePictureCache()
	 * @uses Thumbnail::outputThumbnail()
	 * @uses $thumbnail
	 * @uses PictureCache::returnPictureCache()
	 */
	function outputThumbnail( $format = 'png', $quality = 75 ) 
	{
		parent::setOutputFormat( $format );
		$this->setCache();
		
		if ( $this->cache_time === 0 || $this->cache->isPictureCached() === false )
		{
			parent::createThumbnail();
			
			if ( $this->cache_time > 0 )
				$this->cache->writePictureCache( $this->thumbnail, $quality );
			
			parent::outputThumbnail( $format, $quality );
		} 
		else 
		{
		    $ct = array(
				1  => 'gif', 
				2  => 'jpeg', 
				3  => 'png', 
				15 => 'vnd.wap.wbmp'
			);
			
		    if ( array_key_exists( $this->thumbnail_type, $ct ) )
		    	header( 'Content-type: image/' . $ct[$this->thumbnail_type] );

		    header( 'Expires: ' . date( "D, d M Y H:i:s", time() + $this->cache_time ) . ' GMT' );
			header( 'Cache-Control: public' );
			header( 'Cache-Control: max-age=' . $this->cache_time );

			echo $this->cache->returnPictureCache();
		}
	}

	/**
	 * Returns the variable with the thumbnail image.
	 *
	 * @param string $format gif, jpg, png, wbmp
	 * @return mixed
	 * @access public
	 * @uses setOutputFormat()
	 * @uses Thumbnail::setCache()
	 * @uses $cache_time
	 * @uses Thumbnail::isPictureCached()
	 * @uses Thumbnail::createThumbnail()
	 * @uses Thumbnail::writePictureCache()
	 * @uses $thumbnail_type
	 * @uses $thumbnail
	 * @uses Thumbnail::returnCachePicturename()
	 */
	function returnThumbnail( $format = 'png' ) 
	{
		$this->setOutputFormat( $format );
		$this->setCache();
		
		if ( $this->cache_time === 0 || $this->cache->isPictureCached() === false ) 
		{
			parent::createThumbnail();
			
			if ( $this->cache_time > 0 )
				$this->cache->writePictureCache( $this->thumbnail, 100 );
		} 
		else 
		{
		    switch ( $this->thumbnail_type ) 
			{
		        case 1:
		            $this->thumbnail = imagecreatefromgif( $this->cache->returnCachePicturename() );
		            break;
					
		        case 2:
		            $this->thumbnail = imagecreatefromjpeg( $this->cache->returnCachePicturename() );
		            break;
					
		        case 3:
		            $this->thumbnail = imagecreatefrompng( $this->cache->returnCachePicturename() );
		            break;
					
		        case 15:
		            $this->thumbnail = imagecreatefromwbmp( $this->cache->returnCachePicturename() );
		            break;
					
		        case 999:

		        default:
					$this->thumbnail = imagecreatefromstring( $this->cache->returnCachePicturename() );
					break;
		    }
		}
		
		return $this->thumbnail;
	}

	/**
	 * Returns the path/filename of the cached thumbnail.
	 * If cached pic is not available, tries to create it with the given parameters.
	 *
	 * @param string $format gif, jpg, png, wbmp
	 * @param int $quality jpg-quality: 0-100
	 * @return mixed string or false if no cached pic is available
	 * @access public
	 * @uses $cache_time
	 * @uses PictureCache::isPictureCached()
	 * @uses setOutputFormat()
	 * @uses PictureCache::writePictureCache()
	 * @uses Thumbnail::createThumbnail()
	 */
	function getCacheFilepath( $format = 'png', $quality = 75 ) 
	{
		if ( $this->cache_time === 0 )
			return false; // no cached thumb available

		$this->setOutputFormat( $format );
		$this->setCache();
		
		$path = $this->cache->getCacheFilepath( $format, $quality );

		if ( $path != false ) 
		{
			return $path;
		} 
		else 
		{ 
			// trys to create cache and return filename
			parent::createThumbnail();
			$this->cache->writePictureCache( $this->thumbnail, $quality );

			return $this->cache->getCacheFilepath( $format, $quality );
		}
	}
} // END OF CachedThumbnail

?>
