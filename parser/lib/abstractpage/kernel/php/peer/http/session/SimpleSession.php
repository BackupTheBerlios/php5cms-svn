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


using( 'io.FolderUtil' );


$GLOBALS["AP_SESSION_INFOLIST"]    = null;
$GLOBALS["AP_SIMPLESESSION_CLASS"] = new SimpleSession();


/**
 * SimpleSession class
 *
 * Extends PHP’s session handling, making it more usable.
 *
 * Features:
 *
 * - Variables can be passed directly and do not have to be global.
 * - Is only based on PHP's built in sessions handling, no additional SID is used. So
 *   we benefit from all features that PHP sessions handling uses to pass the SID
 *   (like url_rewriter.tags )
 * - Automatically stores session data to disk when PHP exits.
 * - Auto-change detection. If session-data is left unchanged, then no data is written
 *   to disk.
 * - Supports garbage collecting.
 * - Supports 'maxLifeTime' and 'maxStandbyTime'
 * - 2do Protection against Session hijacking
 * - You may have parallel sessions. You ask: "What are parallel sessions?" the
 *   short answer is: To have multiple session using *one* SID.
 *
 * Let me give you some examples in where you would like to have parallel
 * sessions:
 *
 * 1) You have 2 (or more) independent login pages on the same site and
 *    want to keep a session for each. Also you must guarantee that the same
 *    session vars are not overwritten by accident.
 *
 * 2) You would like to have a different timeout for some session-vars. E.g.
 *    Some pages in a portal could be very conservative and would like to
 *    drop data while others would like to keep it until the browser is closed.
 *
 * 3) You have to store a high volume of session data, but most of the data is
 *    NOT needed in every session. Using a normal PHP-session would load
 *    _all_ session-vars collected so far (if used or not). If the session data is
 *    complex (like serialized objects) this will result in a high overhead.
 *
 * Typical Use Cases:
 *
 * - You are programming object oriented and also think it’s very _dirty_ to have to
 *   define the session variables _global_.
 *
 * - It's possible to have more then one session at a time (parallel sessions) and
 *   only the session vars are loaded that are used.
 *
 * @package peer_http_session
 */
 
class SimpleSession extends Base 
{
	/**
	 * @access public
	 */
	public $sessionPath;
	
	/**
	 * @access public
	 */
	public $maxLifeTime;
	
	/**
	 * @access public
	 */
	public $maxStandbyTime;
	
	/**
	 * @access public
	 */
	public $gc;
	
	/**
	 * @access public
	 */
	public $garbageLifetime;
	
	/**
	 * @access protected
	 */
	protected $_sessPropTemplate = array();
	
	/**
	 * @access protected
	 */
	protected $_sessPrefix = 'ap_sess_';
	
	/**
	 * @access protected
	 */
	protected $_isValid = array();
	
	/**
	 * @access protected
	 */
	protected $_sourceVersion = 0;
	
	/**
	 * @access protected
	 */
	protected $_lastError = "";
	
	/**
	 * @access protected
	 */
	protected $_sessStarted = null;
	
	/**
	 * @access protected
	 */
	protected $_sessInfoList = array();
	
	/**
	 * @access protected
	 */
	protected $_sessData = array();
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct() 
	{
		mt_srand( (double)microtime() * 1000000 );
		
		$this->sessionPath     = ini_get( 'session.save_path' );
		$this->maxLifeTime     = 0;
		$this->maxStandbyTime  = 180;
		$this->gc              = 1;
		$this->garbageLifetime = 60 * 240; // 10 days is default

		$this->_sessPropTemplate = array(
			'version'        => 0,
			'path'           => $this->sessionPath,
			'maxStandbyTime' => 180,
			'maxLifeTime'    => 0,
		);

		if ( $this->maxLifeTime >= $this->garbageLifetime ) 
			$this->garbageLifetime = $this->maxLifeTime;
	}

	/**
	 * Destructor
	 *
	 * @access public
	 */
	public function __destruct()
	{
		try
		{
			$this->_pathCheck( $this->_sessPropTemplate['path'] );
			$this->writeClose();
		}
		catch ( Exception $e )
		{
			// swallow
		}
	}
	
	
	/**
	 * @access public
	 */
	public function start() 
	{
		if ( $this->_sessStarted !== null ) 
			return $this->_sessStarted;
			
		$this->_sessStarted = session_start();
		
		if ( !isset( $_SESSION['AP_SESSION_INFOLIST'] ) ) 
			session_register( 'AP_SESSION_INFOLIST' );

		$this->_sessInfoList =& $_SESSION['AP_SESSION_INFOLIST'];
	}

	/**
	 * @access public
	 */
	public function register( $key, &$value, $sessName = 'default' ) 
	{
		if ( !isset( $this->_isValid[$sessName] ) ) 
			$this->_init( $sessName );
		
		if ( $this->_isValid[$sessName] ) 
		{
			if ( isset( $this->_sessData[$sessName]['data'][$key] ) ) 
			{
				$value = $this->_sessData[$sessName]['data'][$key];
				unset( $this->_sessData[$sessName]['data'][$key] );
				
				$this->_sessData[$sessName]['data'][$key] =& $value;
			} 
			else 
			{
				$this->_sessData[$sessName]['data'][$key] =& $value;
				$this->_sessInfoList[$sessName]['state']['forceWrite'] = true;
			}

			return true;
		}

		return false;
	}

	/**
	 * @access public
	 */	
	public function unRegister( $key, $sessName = 'default' ) 
	{
		if ( !isset( $this->_isValid[$sessName] ) ) 
			$this->_init( $sessName );
		
		if ( $this->_isValid[$sessName] ) 
		{
			unset( $this->_sessData[$sessName]['data'][$key] );
			$this->_sessInfoList[$sessName]['state']['forceWrite'] = true;
			$this->_lastError = "";
			
			return true;
		}

		return false;
	}

	/**
	 * @access public
	 */
	public function isRegistered( $key, $sessName = 'default' ) 
	{
		if ( !isset( $this->_isValid[$sessName] ) ) 
			$this->_init( $sessName );
		
		if ( $this->_isValid[$sessName] ) 
		{
			return (bool)isset( $this->_sessData[$sessName]['data'][$key] );
			$this->_lastError = "";
		}

		return false;
	}

	/**
	 * @access public
	 */	
	public function &getVar( $key, $sessName = 'default' ) 
	{
		if ( !isset( $this->_isValid[$sessName] ) ) 
			$this->_init( $sessName );
			
		if ( $this->_isValid[$sessName] ) 
		{
			if ( isset( $this->_sessData[$sessName]['data'][$key] ) ) 
				return $this->_sessData[$sessName]['data'][$key];

			$this->_lastError = "";
		}

		return null;
	}

	/**
	 * @access public
	 */	
	public function destroy( $sessName = 'default' ) 
	{  
		do 
		{
			if ( empty( $this->_sessInfoList[$sessName] ) ) 
				break;
				
			$prop  = $this->_sessInfoList[$sessName]['prop'];
			$state = $this->_sessInfoList[$sessName]['state'];
			
			if ( empty( $state['fileName'] ) ) 
				break;
			
			$filePath = $prop['path'] . $state['fileName'];
			@unlink( $filePath );
		} while ( false );
		
		unset( $this->_sessInfoList[$sessName] );
		unset( $this->_sessData[$sessName] );
		unset( $this->_isValid[$sessName] );
		
		return true;
	}

	/**
	 * @access public
	 */
	public function reset( $sessName = 'default' ) 
	{
		if ( !empty( $this->_sessInfoList[$sessName] ) ) 
		{
			$this->_sessInfoList[$sessName]['state']['createTime'] = time();
			$this->_sessInfoList[$sessName]['state']['accessTime'] = time();
			$this->_sessInfoList[$sessName]['state']['forceWrite'] = true;

			$this->_sessData[$sessName] = array( 'data' => null );
		}
		
		return true;
	}

	/**
	 * @access public
	 */
	public function setProperty( $prop, $sessName = 'default' ) 
	{
		if ( !isset( $this->_isValid[$sessName] ) ) 
			$this->_init( $sessName );
		
		$status = false;
		
		do 
		{
			if ( !$this->_isValid[$sessName] ) 
				break;
				
			if ( !is_array( $prop ) ) 
			{
				$this->_lastError .= "Frist parameter is not a array.\n";
				break;
			}

			$SystemStandbyTime = ini_get( 'session.gc_maxlifetime' );
			
			if ( isset( $prop['maxStandbyTime'] ) && is_numeric( $SystemStandbyTime ) ) 
			{
				if ( ( $SystemStandbyTime >0 ) && ( $SystemStandbyTime <= $prop['maxStandbyTime'] * 60 ) ) 
				{
					$this->_lastError .= "Warning: 'maxStandbyTime' can't exceed [$SystemStandbyTime sec] given by PHP's 'session.gc_maxlifetime' defined in php.ini-file. Tipp: Use .htaccess or change value in php.ini.\n";
					break;
				}
			}

			if ( !$this->_copyProp( $prop, $this->_sessInfoList[$sessName]['prop'] ) ) 
				break;
			
			$status = true;
		} while ( false );
		
		return $status;
	}

	/**
	 * @access public
	 */
	public function getSid() 
	{
		return session_id();
	}

	/**
	 * @access public
	 */
	public function getLastError() 
	{
		return $this->_lastError;
	}

	/**
	 * @access public
	 */
	public function write_close() 
	{
		foreach ( $this->_sessData as $sessName => $sessStruct ) 
		{
			$filePath   = $this->_sessInfoList[$sessName]['prop']['path'] . $this->_sessInfoList[$sessName]['state']['fileName'];
			$dataStream = serialize( $sessStruct['data'] );
			$md5 = md5( $dataStream );
			
			if ( empty( $this->_sessInfoList[$sessName]['state']['forceWrite'] ) ) 
			{
				if ( $this->_sessInfoList[$sessName]['state']['md5'] == $md5 ) 
					continue;
			}

			$this->_sessInfoList[$sessName]['state']['forceWrite'] = false;
			$this->_sessInfoList[$sessName]['state']['md5'] = $md5;
			
			if ( !empty( $fp ) ) 
				@fclose( $fp );
				
			if ( ( $fp = @fopen( $filePath, 'wb' ) ) === false ) 
			{
				$this->_lastError .= "Failed to open sesssion-file '{$filePath}' for write.\n";
				continue;
			}

			if ( !@fwrite( $fp, $dataStream, strlen( $dataStream ) ) ) 
			{
				@unlink( $filePath );
				$this->_lastError .= "Failed to write to file '{$filePath}'.\n";
				continue;
			}
		}
		
		@fclose( $fp );
		session_write_close();
		$this->_sessStarted = null;
		
		return empty( $this->_lastError );
	}

	/**
	 * @access public
	 */
	public function gc() 
	{
		if ( $this->garbageLifetime == 0 ) 
			return true;

		$this->_lastError = "";
		
		$listParams = array(
			'regEx'      => ':^' . $this->_sessPrefix . ':', 
			'regFunction'=> 'preg_match',
			'depth'      => 0, 
			'returnType' => 'subpath'
		);
		
		$alreadyParsed = array();
		$now = time();
		
		foreach ( $this->_sessInfoList as $sessName => $sessStruct ) 
		{
			$prop = $this->_sessInfoList[$sessName]['prop'];
			
			if ( isset( $alreadyParsed[$prop['path']] ) ) 
				continue; 
			else 
				$alreadyParsed[$prop['path']] = true;
					
			$additionalParams = array(
				'fullPath' => $prop['path']
			);
					
			$listParams = array_merge( $additionalParams, $listParams );
			
			try
			{
				$fileList = FolderUtil::getFileList( $listParams );
			}
			catch ( Exception $e )
			{
				// swallow exception
				$this->_lastError .= "File list fail with.\n";
				continue;
			}
	
			foreach ( $fileList as $fileName ) 
			{
				$filePath = $prop['path'] . $fileName;
				
				if ( !( $fileModTime = @filemtime( $filePath ) ) ) 
				{
					$this->_lastError .= "Failed to get file  mod-time from: " . $filePath. "\n";
					break;
				}

				$age = $now - $fileModTime;
				
				if ( $age > ( $this->garbageLifetime * 60 ) ) 
					@unlink( $filePath );
			}
		}

		return empty( $this->_lastError );
	}

	
	// protected methods

	/**
	 * @access protected
	 */	
	protected function _setup( $sessName )  
	{  
		$this->_sessInfoList[$sessName]['prop'] = $this->_sessPropTemplate;
		$sid    = md5( uniqid( mt_rand() ) );
		$prefix = $this->_sessPrefix . $sessName .'_';
		
		$this->_sessInfoList[$sessName]['state'] = array(
			'SID'         => $sid,
			'filePrefix'  => $prefix,
			'fileName'    => $prefix . $sid,
			'createTime'  => time(),
			'accessTime'  => time(),
			'md5'         => '',
			'forceWrite'  => false
		);
		
		$this->_sessData[$sessName] = array(
			'data' => null
		);
		
		return true;
	}

	/**
	 * @access protected
	 */	
	protected function _init( $sessName ) 
	{
		if ( isset( $this->_isValid[$sessName] ) && ( $this->_isValid[$sessName] !== null ) ) 
			return $this->_isValid[$sessName];
		
		$status = false;
		
		do 
		{
			if ( !$this->start() ) 
			{
				$this->_lastError .= "Unable to start PHP session handler session_start().\n";
				break;
			}

			if ( empty( $this->_sessInfoList[$sessName] ) ) 
			{
				$status = $this->_setup( $sessName );
				break;
			}

			$prop  = $this->_sessInfoList[$sessName]['prop'];
			$state = $this->_sessInfoList[$sessName]['state'];
			
			if ( $prop['version'] != $this->_sourceVersion )  
			{
				$this->destroy( $sessName );
				$status = $this->_setup( $sessName );
				
				break;
			}

			$randVal = mt_rand( 1, 100 );
			
			if ( $randVal <= $this->gc ) 
				$this->gc();
			
			$now = time();
			
			if ( $prop['maxLifeTime'] > 0 ) 
			{
				if ( ( $now - $state['createTime'] ) > ( $prop['maxLifeTime'] * 60 ) ) 
					break;
			}

			if ( $prop['maxStandbyTime'] > 0 ) 
			{
				if ( ( $now - $state['accessTime'] ) > ( $prop['maxStandbyTime'] * 60 ) ) 
					break;
			}

			if ( !$this->_fetch( $sessName ) ) 
				break;
				
			$status = true;
		} while ( false );
		
		if ( $status ) 
		{
			$this->_sessInfoList[$sessName]['state']['accessTime'] = time();
		} 
		else 
		{
			$this->_isValid[$sessName] = true;
			$status = $this->reset( $sessName );
		}

		$this->_isValid[$sessName] = $status;
	}

	/**
	 * @access protected
	 */	
	protected function _fetch( $sessName ) 
	{ 
		$status   = false;
		$filePath = $this->_sessInfoList[$sessName]['prop']['path'] . $this->_sessInfoList[$sessName]['state']['fileName'];
		
		do 
		{
			if ( !($fp = @fopen( $filePath, 'rb' ) ) ) 
			{
				$this->_lastError .= "Failed to open sesssion-file '{$filePath}' for read.\n";
				break;
			}

			$dataStream = @fread( $fp, filesize( $filePath ) );
			
			if ( empty( $dataStream ) ) 
			{
				$this->_lastError .= "Failed to read sesssion-file '{$filePath}' OR empty.\n";
				break;
			}

			$this->_sessData[$sessName] = array(
				'data' => @unserialize( $dataStream )
			);
			
			if ( empty( $this->_sessData[$sessName]['data'] ) ) 
				$this->_lastError .= "Empty data or failed to unserialize sesssion-data fome file '{$filePath}'\n";

			$status = true;
		} while ( false );
		
		return $status;
	}

	/**
	 * @access protected
	 */	
	protected function _copyProp( $fromProp, &$toProp ) 
	{
		foreach ( $toProp as $key => $val ) 
		{
			if ( empty( $fromProp[$key] ) ) 
				continue;
			
			$toProp[$key] = $fromProp[$key];
		}

		$toProp['version'] = $this->_sourceVersion;
	}
	
	/**
	 * @throws Exception
	 * @access protected
	 */	
	protected function _pathCheck( &$path ) 
	{
		if ( empty( $path ) ) 
			$path = ini_get( 'session.save_path' );
			
		$path = str_replace( "\\", '/', trim( $path ) );
		
		if ( substr( $path, -1 ) != '/' ) 
			$path .= '/';
		
		$status = false;
		
		do 
		{
			if ( !is_dir( $path ) ) 
			{
				if ( !@mkdir( $path, 0770 ) ) 
				{
					$msg = "Faild to make the DIR [$path] to store session data.\n";
					break;
				}
			}
			
			if ( !is_dir( $path ) || !is_writeable( $path ) ) 
			{
				$msg = "Unable to store session data. Invalid dir: '{$path}'.\n";
				
				if ( !is_dir( $path ) ) 
					$msg .= "Does not exsist.\n";
				else 
					$msg .= "No write access.\n";

				break;
			}

			$status = true;
		} while ( false );
		
		if ( !$status )
			$this->_lastError .= $msg . "\n";

		return $status;
	}
} // END OF SimpleSession

?>
