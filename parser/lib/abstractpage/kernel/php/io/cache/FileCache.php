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


using( 'util.UnitConverter' );
using( 'io.FileUtil' );
using( 'io.FolderUtil' );


/**
 * Simple File Caching
 *
 * - Checks if 'origin-file' has changed AND/OR the lifetime you have set has
 *   passed (cache then becomes 'out of date').
 * - Cache versioning to identify outdated/invalid cache data. All cache with older
 *   versions then $cacheVersion are considered 'out of date'. Very useful during
 *   development and updates, where the structure of the data you generate has
 *   changed and all cache data becomes invalid. Saves you the hassle to delete all
 *   old cache files spread all over the disk.
 * - Exclusive cache writing to disk.
 *   Second level memory buffer that holds the cache data in memory for reuse
 *   (Currently only until script ends).
 * - setBufferSize(): Ability to set the memory buffer size in bytes OR in % of the
 *   available script memory.
 * - setDir(): Define where you want the cache-files to be stored. 2 options
 *   a) In one central dir (TIPP: use a RAM-disk )
 *   b) Below each 'origin-file' in a subdir.
 * - setVerboseCacheNames(): Define if the 'cache-files' should contain a verbose
 *   name or not. A verbose name contains the name of the 'origin-file' (max first 100
 *   chars).
 *
 * Usage:
 *
 * $cache =& new FileCache( 3 );
 * $cache->setBufferSize( '3k' );
 * $cache->setDir( 'r:/' );
 * $cache->setVerboseCacheNames( true );
 * $cache->setCacheLifeTime( 1 );
 * $testData = serialize( $cache );
 * $cache->store( 'D:/tmp/diff.txt',  $testData, false, 3 );
 * $cache->store( 'D:/tmp/diff2.txt', $testData );
 * $xxx = $cache->fetch( 'D:/tmp/diff.txt' );   
 * echo ( $xxx === null? "null<br>" : "DATA<br>" );
 * $xxx = $cache->fetch( 'D:/tmp/diff2.txt' );  
 * echo ( $xxx === null? "null<br>" : "DATA<br>" );
 * $xxx = $cache->fetch( 'D:/tmp/diff.txt'  );
 * $xxx = $cache->fetch( 'D:/tmp/diff2.txt' );
 * $xxx = $cache->fetch( 'D:/tmp/diff.txt'  );
 * $xxx = $cache->fetch( 'D:/tmp/diff2.txt' );
 * $xxx = $cache->fetch( 'D:/tmp/diff.txt'  );   
 * echo ( $xxx === null? "null<br>" : "DATA<br>" );
 * sleep( 2 );
 * $xxx = $cache->fetch( 'D:/tmp/diff.txt' );   
 * echo ( $xxx === null? "null<br>" : "DATA<br>" );
 * $xxx = $cache->fetch( 'D:/tmp/diff2.txt' );  
 * echo ( $xxx === null? "null<br>" : "DATA<br>" );
 *
 * @package io_cache
 */
 
class FileCache extends PEAR 
{
	/**
	 * @access private
	 */
	var $_cacheProp = array(
		'maxCacheLifeTime' => 0,
		'maxBufLifeTime'   => 10,
		'maxBufSize'       => 0,
		'freeBufSize'      => 0,
		'verboseName'      => true,
		'storeDir'         => ''
	);
	
	/**
	 * @access private
	 */
	var $_fifoBuffer = array();

	/**
	 * @access private
	 */
	var $_cacheVersion = null;
	
	/**
	 * @access private
	 */
	var $_lastErrMsg = '';
	
	/**
	 * @access private
	 */
	var $_errMsgHistory = array();

	/**
	 * @access private
	 */	
	var $_determineFileNameHash = array();
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function FileCache( $cacheVersion = 0 ) 
	{
		$this->_cacheVersion = $cacheVersion;
		
		if ( !$this->setBufferSize( '5%' ) ) 
			$this->setBufferSize( '100K' );

		$this->_cacheProp['freeBufSize'] = $this->_cacheProp['maxBufSize'];
	}

	
	/**
	 * @access public
	 */
	function isModifiedSince( $filePath, $since ) 
	{
		$cacheTime = $this->getLastModified( $filePath );
		
		if ( $cacheTime === false ) 
			return true;
			
		if ( $cacheTime > $since )
			return false;

		return true;
	}

	/**
	 * @access public
	 */
	function getLastModified( $filePath ) 
	{
		if ( empty( $filePath ) ) 
			return false;
		
		$cacheFilePath = $this->_determinePathToCache( $filePath );
		
		if ( empty( $cacheFilePath ) ) 
			return false;

		$t = @filemtime( $cacheFilePath );
		
		if ( !$t ) 
			return false;
		
		return gmmktime(
			date( 'H', $t ), 
			date( 'i', $t ), 
			date( 's', $t ), 
			date( 'M', $t ), 
			date( 'd', $t ), 
			date( 'Y', $t )
		);
	}

	/**
	 * @access public
	 */
	function fetch( $filePath ) 
	{
		if ( empty( $filePath ) ) 
			return false;
		
		$status     = false;
		$addToFifo  = false;
		$dataStream = null;
		
		do 
		{
			if ( ( $idx = $this->_getFiFoIndex( $filePath ) ) !== false ) 
			{
				$cacheBlock = $this->_fifoBuffer[$idx];
			} 
			else 
			{
				$addToFifo     = true;
				$cacheFilePath = $this->_determinePathToCache( $filePath );
				
				if ( empty( $cacheFilePath ) ) 
				{
					$this->_lastErrMsg = "Invalid parameter: \$filePath.";
					break;
				}

				if ( ( $cacheBlock = $this->_readCacheFile( $cacheFilePath ) ) === false ) 
					break;
			}

			$now = time();
			
			if ( ( $cacheBlock['maxLifeTime'] > 0 ) && ( $cacheBlock['maxUnixTime'] < $now ) ) 
			{
				$status = null;
				break;
			}

			if ( !$cacheBlock['originCheck'] ) 
			{
				$dataStream = $cacheBlock['dataStream'];
				$status = true;
				
				break;
			}

			if ( ( $now - $cacheBlock['fifoTimestamp'] ) <= $this->_cacheProp['maxBufLifeTime'] ) 
			{
				$dataStream = $cacheBlock['dataStream'];
				$status = true;
				
				break;
			}

			if ( $this->_isCacheFileUptodate( $filePath, $cacheFilePath ) === false ) 
			{
				if ( empty( $this->_lastErrMsg ) ) 
					$status = null;
				
				break;
			}

			$status = true;
		} while ( false );
		
		if ( $status ) 
		{
			if ( $addToFifo ) 
				$this->_addToFiFo( $cacheBlock );
				
			if ( !empty( $this->_lastErrMsg ) ) 
				$this->_errMsgHistory[] = $this->_lastErrMsg;
			
			$this->_lastErrMsg = "";
		}

		if ( $status ) 
			return $dataStream;
		
		return $status;
	}

	/**
	 * @access public
	 */
	function store( $filePath, $dataStream, $originCheck = true, $maxLifeTime = null ) 
	{
		if ( is_null( $maxLifeTime ) ) 
			$maxLifeTime = $this->_cacheProp['maxCacheLifeTime'];
		
		if ( !$originCheck && ( $maxLifeTime <= 0 ) ) 
			$maxLifeTime = 3600 * 24;
		
		$status = false;
		
		do 
		{
			if ( !is_string( $dataStream ) ) 
			{
				$this->_lastErrMsg = "Invalid data to store. Must be a string. TIPP: Use php's serialize() to transform any data to a string.";
				break;
			}

			$streamSize = strlen( $dataStream );
			
			$cacheBlock = array (
				'cacheVersion' => $this->_cacheVersion,
				'valid'        => true,
				'maxLifeTime'  => $maxLifeTime,
				'maxUnixTime'  => time() + $maxLifeTime,
				'originCheck'  => $originCheck,
				'filePath'     => $filePath,
				'streamSize'   => $streamSize,
				'dataStream'   => $dataStream
			);
			
			if ( ( $idx = $this->_getFiFoIndex( $filePath ) ) !== false ) 
				$this->_fifoBuffer[$idx]['valid'] = false;
			
			if ( ( $cacheFilePath = $this->_determinePathToCache( $filePath ) ) === false ) 
				break;
				
			$this->_addToFiFo( $cacheBlock );
		
			if ( $this->_writeCacheFile( $cacheFilePath, $cacheBlock ) === false ) 
				break;
				
			$status = true;
		} while ( false );
		
		if ( $status ) 
		{
			if ( !empty( $this->_lastErrMsg ) ) 
				$this->_errMsgHistory[] = $this->_lastErrMsg;
			
			$this->_lastErrMsg = '';
		}

		return $status;
	}

	/**
	 * @access public
	 */
	function clearBuffer( $filePath = '' ) 
	{
		if ( empty( $filePath ) ) 
		{
			$this->_fifoBuffer = array();
		} 
		else 
		{
			if ( ( $idx = $this->_getFiFoIndex( $filePath ) ) !== false ) 
				$this->_fifoBuffer[$idx]['valid'] = false;
		}
		
		return true;
	}

	/**
	 * @access public
	 */
	function flushFileCache() 
	{
		return FolderUtil::emptyFolder( $this->_cacheProp['storeDir'] );
	}

	/**
	 * @access public
	 */
	function setBufferSize( $newBufSize ) 
	{
		$status = false;
		
		do 
		{
			if ( is_numeric( $newBufSize ) ) 
			{
				$this->_cacheProp['maxBufSize'] = $newBufSize;
				$status = true;
				
				break;
			} 

			$newBufSize = trim( $newBufSize );
			
			if ( substr( $newBufSize, -1 ) === '%' )
			{
				$val = trim( substr( $newBufSize, 0, strlen( $newBufSize ) - 1 ) );
				
				if ( !is_numeric( $val ) ) 
					break;
				
				if ( ( $memSize = UnitConverter::unitStringToBytes( get_cfg_var( 'memory_limit' ) ) ) === false ) 
					break;
					
				if ( $val > 80 ) 
					$val = 80;
				
				$this->_cacheProp['maxBufSize'] = (int)( $val * $memSize / 100 );
			} 
			else 
			{
				if ( ( $newBufSize = UnitConverter::unitStringToBytes( $newBufSize ) ) === false )
					break;

				$this->_cacheProp['maxBufSize'] = $newBufSize;
			}

			$status = true;
		} while ( false );
		
		if ( $status ) 
		{
			if ( !empty( $this->_lastErrMsg ) ) 
				$this->_errMsgHistory[] = $this->_lastErrMsg;
			
			$this->_lastErrMsg = "";
		}

		if ( $status ) 
			return $this->_cacheProp['maxBufSize'];
		
		return false;
	}

	/**
	 * @access public
	 */
	function setBufferLifetime( $sec = 10 ) 
	{
		if ( !is_numeric( $sec ) ) 
			return false;
		
		$this->_cacheProp['maxBufLifeTime'] = $sec;
		return true;
	}

	/**
	 * @access public
	 */
	function setCacheLifeTime( $sec = 0 ) 
	{
		if ( !is_numeric( $sec ) ) 
			return false;
		
		$this->_cacheProp['maxCacheLifeTime'] = $sec;
		return true;
	}

	/**
	 * @access public
	 */
	function setDir( $path = '' ) 
	{
		$status = false;
		
		do 
		{
			if ( empty( $path ) ) 
				break;
			
			$path = str_replace( "\\", '/', trim( $path ) );
			
			if ( !file_exists( $path ) ) 
			{
				$status = FolderUtil::mkpath( $path );
				
				if ( !$status ) 
					break;
			}

			if ( !is_dir( $path ) || !is_writeable( $path ) ) 
				break;
			
			if ( substr( $path, -1 ) !== '/' )  
				$path .= '/';
			
			$this->_cacheProp['storeDir'] = $path;
			$status = true;
		} while ( false );
		
		if ( !$status ) 
		{
			$this->_cacheProp['storeDir'] = '';
			$status = true;
		}

		if ( $status ) 
		{
			if ( !empty( $this->_lastErrMsg ) ) 
				$this->_errMsgHistory[] = $this->_lastErrMsg;
			
			$this->_lastErrMsg = '';
		}

		return $status;
	}

	/**
	 * @access public
	 */
	function setVerboseCacheNames( $trueFalse = true ) 
	{
		$this->_cacheProp['verboseName'] = $trueFalse;
	}

	/**
	 * @access public
	 */
	function getLastError() 
	{
		if ( empty( $this->_lastErrMsg ) ) 
			return false;
		
		return $this->_lastErrMsg;
	}


	// private methods

	/**
	 * @access private
	 */
	function _getFiFoIndex( $filePath ) 
	{
		$fifoSize = sizeof( $this->_fifoBuffer );
		
		for ( $i = 0; $i < $fifoSize; $i++ ) 
		{
			if ( $this->_fifoBuffer[$i]['valid'] && ( $filePath === $this->_fifoBuffer[$i]['filePath'] ) ) 
				return $i;
		}

		return false;
	}

	/**
	 * @access private
	 */
	function _addToFiFo( $cacheBlock ) 
	{
		$status = false;
		
		do 
		{
			if ( !$this->_fifoFreeSpace( $cacheBlock['streamSize'] ) )
				break;

			$cacheBlock['fifoTimestamp'] = time();
			
			if ( ( $idx = $this->_getFiFoIndex( $cacheBlock['filePath'] ) ) === false ) 
			{
				$this->_cacheProp['freeBufSize'] -= $cacheBlock['streamSize'];
				$this->_fifoBuffer[] = $cacheBlock;
			} 
			else 
			{
				$this->_cacheProp['freeBufSize'] += $this->_fifoBuffer[$idx]['streamSize'] - $cacheBlock['streamSize'];
				$this->_fifoBuffer[$idx] = $cacheBlock;
			}

			$status = true;
		} while ( false );

		return $status;
	}

	/**
	 * @access private
	 */
	function _fifoFreeSpace( $sizeOfNewData ) 
	{
		if ( $this->_cacheProp['maxBufSize'] == -1 ) 
			return true;
		
		if ( $this->_cacheProp['maxBufSize'] == 0 ) 
			return false;
		
		if ($this->_cacheProp['maxBufSize'] < $sizeOfNewData ) 
			return false;
		
		$this->_fifoGarbageCollect();
		
		do 
		{
			if ( $this->_cacheProp['freeBufSize'] >= $sizeOfNewData ) 
				break;
			
			$this->_cacheProp['freeBufSize'] += $this->_fifoBuffer[0]['streamSize'];
		} while ( array_shift( $this->_fifoBuffer ) );
		
		return true;
	}

	/**
	 * @access private
	 */
	function _fifoGarbageCollect() 
	{
		$fiFoClone = $this->_fifoBuffer;
		$this->_fifoBuffer = array();
		$this->_cacheProp['freeBufSize'] = $this->_cacheProp['maxBufSize'];
		$fifoSize = sizeof( $fiFoClone );
		
		for ( $i = 0; $i < $fifoSize; $i++ ) 
		{
			if ( $fiFoClone[$i]['valid'] ) 
			{
				$this->_fifoBuffer[] = $fiFoClone[$i];
				$this->_cacheProp['freeBufSize'] -= $fiFoClone[$i]['streamSize'];
			}
		}
	}

	/**
	 * @access private
	 */
	function _determinePathToCache( $filePath ) 
	{
		if ( isset( $this->_determineFileNameHash[$filePath] ) ) 
			return $this->_determineFileNameHash[$filePath];
		
		$tmpPos   = strrpos( $filePath, '/' );
		$basename = basename( $filePath );
		$basenameLimited = ( strlen( $basename ) > 100 )? substr( $basename, 0, 100 ) : $basename;
		
		if ( empty( $this->_cacheProp['storeDir'] ) ) 
			$path = substr( $filePath, 0, $tmpPos ) . '/_cache/';
		else 
			$path = $this->_cacheProp['storeDir'];

		$md5 = md5( $path . $basename );
		$basename = $this->_cacheProp['verboseName']? $basenameLimited . '_' .  $md5 : $md5;
		$cacheFilePath = $path . $basename . '.cache';
		$this->_determineFileNameHash[$filePath] = $cacheFilePath;
		
		return $cacheFilePath;
	}

	/**
	 * @access private
	 */
	function _isCacheFileUptodate( $filePath, $cacheFilePath ) 
	{
		$status  = false;
		$dbgInfo = '';
		
		do 
		{
			if ( !file_exists( $filePath ) ) 
			{
				$this->_lastErrMsg = "'Up to date'-check problem. Missing origin-file:'{$filePath}'. Maybe moved? End with 'out of date'.";
				break;
			}

			if ( !file_exists( $cacheFilePath ) ) 
			{
				$this->_lastErrMsg = "'Up to date'-check problem. Missing cache-file:'{$cacheFilePath}'. Maybe deleted?  End with 'out of date'.";
				break;
			}

			$originTime = filemtime( $filePath );
			$cacheTime  = filemtime( $cacheFilePath );
			
			if ( $cacheTime <= $originTime ) 
				break;

			$status = true;
		} while ( false );

		return $status;
	}

	/**
	 * @access private
	 */
	function _writeCacheFile( $cacheFilePath, $cacheBlock ) 
	{
		$status = false;
		
		do 
		{
			if ( !file_exists( $cacheFilePath ) ) 
			{
				$subDir = substr( $cacheFilePath, 0, strrpos( $cacheFilePath, '/' ) );
				
				if ( !file_exists( $subDir ) )  
				{
					if ( !mkdir( $subDir, 0700 ) ) 
					{
						$this->_lastErrMsg = "Failed to create cache sub-dir '{$subDir}'.";
						break;
					}
				}
			}
			
			$res = FileUtil::exclusiveWrite( serialize( $cacheBlock ), $cacheFilePath );

			if ( PEAR::isError( $res ) ) 
			{
				$this->_lastErrMsg = $res->getMessage();
				break;
			}

			$status = true;
		} while ( false );
		
		return $status;
	}

	/**
	 * @access private
	 */
	function _readCacheFile( $cacheFilePath ) 
	{
		$status = false;
		
		do 
		{
			if ( !( $fp = @fopen( $cacheFilePath, 'rb' ) ) ) 
			{
				$this->_lastErrMsg = "Failed to open cache-file '{$cacheFilePath}' for read.";
				break;
			}

			$data = fread( $fp, filesize( $cacheFilePath ) );
			@fclose( $fp );
			
			if ( $data == false ) 
			{
				$this->_lastErrMsg = "Failed to read cache-file '{$cacheFilePath}' .";
				break;
			}

			$cacheBlock = unserialize( $data );
			
			if ( $this->_cacheVersion != $cacheBlock['cacheVersion'] ) 
				break;

			$status = true;
		} while ( false );
		
		$xAtomObj->_tempContainer['xCacheFileUptodate'] = $status;

		if ( $status ) 
			return $cacheBlock;
		
		return false;
	}
} // END OF FileCache

?>
