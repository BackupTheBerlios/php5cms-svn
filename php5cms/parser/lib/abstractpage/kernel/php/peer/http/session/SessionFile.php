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


using( 'peer.http.session.Session' );
using( 'io.FolderUtil' );
using( 'io.FileUtil' );


/**
 * @package peer_http_session
 */
 
class SessionFile extends Session 
{
	/**
	 * @access public
	 */
	public $filePrefix = 'ap_sess_';
	
	/**
	 * @access protected
	 */
	protected $_file;
	
	/**
	 * @access protected
	 */
	protected $_path;
	
	
	/**
	 * Constructor
	 *
	 * @throws Exception
	 * @access public
	 */
	public function __construct( $path = null, $gc = null, $ttl = 30 ) 
	{
		$this->Session( $gc, $ttl );
		
		if ( empty( $path ) ) 
			$this->_path = ini_get( 'session.save_path' );
		else 
			$this->_path = $path;
		
		if ( !is_dir( $this->_path ) ) 
		{
			$res = FolderUtil::mkpath( $this->_path );
			
			if ( !$res )
				throw new Exception( "Unable to create directory." );
		}

		if ( !is_dir( $this->_path ) || !is_writeable( $this->_path ) ) 
		{
			$msg = "Unable to store session data. Invalid dir: '{$this->_path}'.\n";
			
			if ( !is_dir( $this->_path ) ) 
				$msg .= "Does not exsist.\n";
			else if ( !is_writeable( $this->_path ) ) 
				$msg .= "No write access.\n";

			throw new Exception( $msg );
		}

		$this->_path = FileUtil::getRealPath( $this->_path )
		$this->_path = FileUtil::standardizePath( $this->_path );
		
		if ( substr( $this->_path, -1 ) != '/' ) 
			$this->_path .= '/';
	}
	
	
	/**
	 * @access public
	 */
	public function read() 
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
	public function write() 
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
	public function gc() 
	{
		$listParams = array(
			'regEx'      => '^ap_sess_.{32}$',
			'depth'      => 0, 
			'returnType' => 'subpath',
			''fullPath'  => $this->_path
		);
		
		try
		{
			$fileList = &FolderUtil::getFileList( $listParams );
			$obsoletTime = time() - $this->_ttl * 60;
			
			foreach ( $fileList as $filename ) 
			{
				$file = $this->_path . $filename;
				
				if ( filemtime( $file ) >= $obsoletTime ) 
					continue;
				
				unlink( $file );
			}
		}
		catch ( Exception $e )
		{
			swallow
		}
	}


	// protected methods

	/**
	 * @access protected
	 */	
	protected function _checkIntegrity( $sid ) 
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
