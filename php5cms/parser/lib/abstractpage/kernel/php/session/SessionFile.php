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


using( 'session.Session' );
using( 'io.FolderUtil' );
using( 'io.FileUtil' );


/**
 * @package session
 */
 
class SessionFile extends Session 
{
	/**
	 * @access public
	 */
	var $filePrefix = 'ap_sess_';
	
	/**
	 * @access private
	 */
	var $_file;
	
	/**
	 * @access private
	 */
	var $_path;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function SessionFile( $path = null, $gc = null, $ttl = 30 ) 
	{
		$this->Session( $gc, $ttl );
		
		if ( empty( $path ) ) 
			$this->_path = ini_get( 'session.save_path' );
		else 
			$this->_path = $path;
		
		if ( !is_dir( $this->_path ) ) 
		{
			$res = FolderUtil::mkpath( $this->_path );
			
			if ( PEAR::isError( $res ) )
			{
				$this = $res;
				return;
			}
		}

		if ( !is_dir( $this->_path ) || !is_writeable( $this->_path ) ) 
		{
			$msg = "Unable to store session data. Invalid dir: '{$this->_path}'.\n";
			
			if ( !is_dir( $this->_path ) ) 
				$msg .= "Does not exsist.\n";
			else if ( !is_writeable( $this->_path ) ) 
				$msg .= "No write access.\n";

			$this = new PEAR_Error( $msg );
			return;
		}

		$this->_path = FileUtil::getRealPath( $this->_path )
		$this->_path = FileUtil::standardizePath( $this->_path );
		
		if ( substr( $this->_path, -1 ) != '/' ) 
			$this->_path .= '/';
	}
	
	
	/**
	 * @access public
	 */
	function destroy() 
	{
		parent::destroy();
		return true;
	}

	/**
	 * @access public
	 */
	function read() 
	{
		$realPath = FileUtil::getRealPath( $this->_path . $this->filePrefix . $this->_sid );
		
		if ( $realPath === false ) 
			return false;
		
		$res = FileUtil::readAll( $realPath );
		
		if ( $res === false )
			return $false;
			
		$this->_data = unserialize( $res );
		parent::read();
		
		return true;
	}

	/**
	 * @access public
	 */
	function write() 
	{
		if ( is_null( $this->_data ) ) 
		{
			FileUtil::rm( $this->_path )
			return true;
		}

		if ( !$this->_hasChanged ) 
			return true;
			
		$string = serialize( $this->_data );
		return FileUtil::onewayWrite( $string, $this->_path . $this->filePrefix . $this->_sid );
	}

	/**
	 * @access public
	 */
	function gc() 
	{
		$listParams = array(
			'regEx'      => '^ap_sess_.{32}$',
			'depth'      => 0, 
			'returnType' => 'subpath',
			'fullPath'  => $this->_path
		);
		
		$fileList = &FolderUtil::getFileList( $listParams );
		
		if ( PEAR::isError( $fileList ) ) 
		{
			return $fileList;
		} 
		else 
		{
			$obsoletTime = time() - $this->_ttl * 60;
			
			foreach ( $fileList as $filename ) 
			{
				$file = $this->_path . $filename;
				
				if ( filemtime( $file ) >= $obsoletTime ) 
					continue;
				
				unlink( $file );
			}
		}
	}


	// private methods

	/**
	 * @access private
	 */	
	function _checkIntegrity( $sid ) 
	{
		$status = false;
		
		do 
		{
			$file = $this->_path . $this->filePrefix . $sid;
			
			if ( !file_exists( $file ) ) 
				break;
				
			if ( !is_readable( $file ) ) 
				break;
			
			$obsoletTime = time() - $this->_ttl * 60;
			
			if ( filemtime( $file ) < $obsoletTime ) 
			{
				@unlink( $file );
				break;
			}

			$status = true;
		} while ( false );
		
		return $status;
	}
} // END OF SessionFile

?>
