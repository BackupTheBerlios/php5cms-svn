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


using( 'io.cache.CacheExpire' );
using( 'util.Debug' );


/**
 * @package io_cache
 */
 
class CacheFile extends PEAR
{
	/**
	 * @access public
	 */
	var $debug;
	
	/**
	 * @access public
	 */
	var $cache_expire;
	
	/**
	 * @access public
	 */
	var $cache_path;
	
	/**
	 * @access public
	 */
	var $am_caching;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function CacheFile()
	{
		$this->cache_expire = new CacheExpire();

		$this->debug = new Debug();
		$this->debug->Off();

		$this->cache_path = AP_ROOT_PATH . ap_ini_get( "path_cache", "path" );
		$this->cache_id   = '';
		$this->am_caching = 0;
	}


	/**
	 * @access public
	 */
	function GenerateCacheHandle()
	{
		// Come up with a generalized deterministic handle thingy.
		return 'default-id';
	}

	/**
	 * @access public
	 */
	function Cache( $id )
	{
		if ( $id == '' )
			$id = $this->GenerateCacheHandle();

		$this->cache_id = $id;
		$cache_file = $this->cache_path . DIRECTORY_SEPARATOR . $id;
		$this->debug->Message( 'Cache file : ' . $cache_file );

		if ( file_exists( $cache_file ) )
		{
			$this->debug->Message( "In cache." );

			// up to date check vs the expire time
			$current_time    = new TimeObject();
			$cached_obj_time = new TimeObject();
			$expire_time     = new TimeObject();

			clearstatcache();
			$cached_obj_time->time = filemtime( $cache_file );
			$cached_obj_time->FormatTime();
			// $cached_obj_time->DebugDump( 'Cached timestamp' );

			$expire_time->Copy( $cached_obj_time );
			// $expire_time->DebugDump( 'Expire time pre modify' );

			$expire_time->Modify(
				$this->cache_expire->hour,
				$this->cache_expire->min,
				$this->cache_expire->sec,
				$this->cache_expire->month,
				$this->cache_expire->day,
				$this->cache_expire->year
			);

			// $current_time->DebugDump( 'Current time' );
			// $expire_time->DebugDump( 'Expire time' );

			$this->debug->Message( 'Expire time settings' );
			$this->debug->Message( 'Month    : ' . $this->cache_expire->month );
			$this->debug->Message( 'Day      : ' . $this->cache_expire->day   );
			$this->debug->Message( 'Year     : ' . $this->cache_expire->year  );
			$this->debug->Message( 'Hour     : ' . $this->cache_expire->hour  );
			$this->debug->Message( 'Min      : ' . $this->cache_expire->min   );
			$this->debug->Message( 'Sec      : ' . $this->cache_expire->sec   );

			if ( $expire_time->LessThan( $cached_obj_time ) )
			{
				$this->debug->Message( "Object expiration due to time." );
				
				// Do we want to force a unlink?
				// unlink( $cache_file );

				// start buffering output
				$this->Start();
				return false;
			}
			else
			{
				$this->debug->Message( "Cached content." );
				echo join( '', file( $cache_file ) );
				
				return true;
			}
		}

		$this->debug->Message( "Content does not exist yet." );
		$this->Start();
	}

	/**
	 * @access public
	 */
	function Start()
	{
		// start output buffering
		ob_start();
		ob_implicit_flush( 0 );
		$this->am_caching = 1;
		
		return true;
	}

	/**
	 * @access public
	 */
	function Stop()
	{
		if ( $this->am_caching == 1 )
		{
			$cached_content = ob_get_contents();

			$this->debug->Message( "Caching stop."  );
			$this->debug->Message( "Writing cache." );

			$cache_file = $this->cache_path . DIRECTORY_SEPARATOR . $this->cache_id;

			if ( file_exists( $cache_file ) )
				unlink( $cache_file );
  
			$fp = fopen( $cache_file, 'w' );
			
			if ( is_array( $cached_content ) )
				fwrite( $fp, join('', $cached_content ) );
			else
				fwrite( $fp, $cached_content );

			fclose( $fp );
			$this->debug->Message( "Done writing." );

			// Flush sends it out to the browser.
			// Flushing at the end minimizes the race condition of cache to disk.
			ob_end_flush();
		}
	}

	/**
	 * @access public
	 */
	function DisableCache()
	{
		$this->DoNotCache();
	}

	/**
	 * @access public
	 */
	function DoNotCache()
	{
		$this->am_caching = 0;
	}
} // END OF CacheFile

?>
