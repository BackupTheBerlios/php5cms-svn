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
 * @package peer_ftp
 */
 
class FTPClient extends PEAR 
{
	/**
	 * @access public
	 */
	var $username;
	
	/**
	 * @access public
	 */
	var $password;
	
	/**
	 * @access public
	 */
	var $host;

	/**
	 * @access public
	 */	
	var $port = 21;
	
	/**
	 * @access public
	 */
	var $anonymous = true;
	
	/**
	 * @access public
	 */
	var $transferMode = FTP_ASCII;

	/**
	 * @access private
	 */
	var $_remotePath;
	
	/**
	 * @access private
	 */
	var $_localPath;
	
	/**
	 * @access private
	 */
	var $_sysType;
	
	/**
	 * @access private
	 */
	var $_conn_id
	
	/**
	 * @access private
	 */
	var $_isConnected = false;
	
	
	/**
	 * @access public
	 */
	function reset() 
	{
		if ( $this->isConnected() ) 
			$this->quit();
		
		$this->port = 21;
		$this->anonymous = true;
		
		unset( $this->host     );
		unset( $this->username );
		unset( $this->password );
		
		unset( $this->_conn_id    );
		unset( $this->_remotePath );
		unset( $this->_localPath  );
		unset( $this->_sysType    );
		
		$this->transferMode = FTP_ASCII;
		$this->_isConnected = false;
	}

	/**
	 * @access public
	 */	
	function isConnected() 
	{
		return $this->_isConnected;
	}

	/**
	 * @access public
	 */
	function connect() 
	{
		if ( $this->isConnected() ) 
			$this->quit();

		$connId = ftp_connect( $this->host, $this->port );
		
		if ( !$connId ) 
		{
			return false;
		} 
		else 
		{
			$this->_conn_id     = $connId;
			$this->_isConnected = true;
			
			return true;
		}
	}

	/**
	 * @access public
	 */
	function login() 
	{
		return (bool)ftp_login( $this->_conn_id, $this->username, $this->password );
	}

	/**
	 * @access public
	 */
	function pwd( $useCache = true ) 
	{
		if ( $useCache && ( !empty( $this->_remotePath ) ) ) 
			return $this->_remotePath;
			
		$t = ftp_pwd( $this->_conn_id );
		
		if ( !$t ) 
		{
			$this->_remotePath = null;
			return false;
		} 
		else 
		{
			return $this->_remotePath = $t;
		}
	}

	/**
	 * @access public
	 */
	function localPwd() 
	{
		return false;
	}

	/**
	 * @access public
	 */
	function cdUp() 
	{
		$t = ftp_cdup( $this->_conn_id );
		
		if ( $t )
			$this->pwd;
		else
			return false;
	}

	/**
	 * @access public
	 */
	function localCdUp() 
	{
		return false;
	}

	/**
	 * @access public
	 */
	function chDir( $directory ) 
	{
		$t = ftp_chdir( $this->_conn_id, $directory );
		
		if ( $t )
			$this->pwd;
		else
			return false;
	}

	/**
	 * @access public
	 */
	function localChDir() 
	{
		return false;
	}

	/**
	 * @access public
	 */
	function mkDir( $directory ) 
	{
		return (bool)ftp_mkdir( $this->_conn_id, $directory );
	}

	/**
	 * @access public
	 */	
	function localMkDir( $directory ) 
	{
		return false;
	}

	/**
	 * @access public
	 */
	function rmDir( $directory ) 
	{
		return (bool)ftp_rmdir( $this->_conn_id, $directory );
	}

	/**
	 * @access public
	 */
	function rmDirRec( $directory ) 
	{
		if ( $directory != '' ) 
		{
			$ar_files = $this->nList( $directory );
			
			if ( is_array( $ar_files ) ) 
			{
				for ( $i = 0; $i < count( $ar_files ); $i++ ) 
				{
					$st_file = $ar_files[$i];
					
					if ( $this->size( $directory . DIRECTORY_SEPARATOR . $st_file ) == -1 ) 
						$this->rmDirRec( $directory . DIRECTORY_SEPARATOR . $st_file );
					else 
						$this->delete( $directory . DIRECTORY_SEPARATOR . $st_file );
				}
			}
			
			$this->rmDir( $directory );
		}
	}

	/**
	 * @access public
	 */	
	function localRmDir( $directory ) 
	{
		return false;
	}

	/**
	 * @access public
	 */
	function localRmDirRec() 
	{
		return false;
	}

	/**
	 * @access public
	 */
	function &nList( $directory ) 
	{
		$t = ftp_nlist( $this->_conn_id, $directory );
		
		if ( !$t ) 
			return false;
		
		return $t;
	}

	/**
	 * @access public
	 */	
	function localNlist( $directory ) 
	{
		return false;
	}

	/**
	 * @access public
	 */
	function &rawList( $directory, $parse = true ) 
	{
		$t = ftp_rawlist( $this->_conn_id, $directory );
		
		if ( !is_array( $t ) ) 
			return false;
			
		if ( !$parse ) 
		{
			return $t;
		} 
		else 
		{
			$t = &$this->parseRawList( $t );
			return $t;
		}
	}

	/**
	 * @access public
	 */	
	function &parseRawList( &$rawList ) 
	{
		if ( is_array( $rawList ) ) 
		{
			$ret = array();
			
			while ( list( $k ) = each( $rawList ) ) 
			{
				$t = split( ' {1,}', $rawList[$k], 9 );
				
				if ( is_array( $t ) && ( sizeOf( $t ) == 9 ) ) 
				{
					unset( $ret2 );
					
					$ret2['name'] = $t[8];
					$ret2['size'] = (int)$t[4];
					
					$month = $this->_monthStringToNumber( $t[5], true );
					$day   = ( strlen( $t[6] ) == 2 )? $t[6] : '0' . $t[6];
					
					if ( strlen( $t[7] ) == 4 ) 
						$ret2['date'] = $t[7] . '/' . $month . '/' . $day;
					else 
						$ret2['date'] = date('Y') . '/' . $month . '/' . $day . ' ' . $t[7];
					
					$ret2['attr']  = $t[0];
					$ret2['type']  = ( $t[0][0] == '-' )? 'file' : 'dir';
					$ret2['dirno'] = (int)$t[1];
					$ret2['user']  = $t[2];
					$ret2['group'] = $t[3];
					
					$ret[] = $ret2;
				} 
				else 
				{
				}
			}
			
			return $ret;
		}
		
		return false;
	}
	
	/**
	 * @access public
	 */
	function &localRawList( $localDir ) 
	{
		return false;
	}

	/**
	 * @access public
	 */
	function sysType( $useCache = true ) 
	{
		if ( $useCache && ( !empty( $this->_sysType ) ) ) 
			return $this->_sysType;
			
		$t = ftp_systype( $this->_conn_id );
		
		if ( !$t ) 
			return false;
		
		return $this->_sysType = $t;
	}

	/**
	 * @access public
	 */
	function pasv( $param ) 
	{
		return (bool)ftp_pasv( $this->_conn_id, $param );
	}

	/**
	 * @access public
	 */
	function get( $localFile, $remoteFile, $mode = null ) 
	{
		if ( is_null( $mode ) ) 
			$mode = &$this->transferMode;
			
		return (bool)ftp_get( $this->_conn_id, $localFile, $remoteFile, $mode );
	}

	/**
	 * @access public
	 */
	function fGet( $fp, $remoteFile, $mode = null ) 
	{
		if ( is_null( $mode ) ) 
			$mode = &$this->transferMode;
			
		return (bool)ftp_fget( $this->_conn_id, $fp, $remoteFile, $mode );
	}

	/**
	 * @access public
	 */
	function put( $localFile, $remoteFile, $mode = null ) 
	{
		if ( is_null( $mode ) ) 
			$mode = &$this->transferMode;
			
		return (bool)ftp_put( $this->_conn_id, $remoteFile, $localFile, $mode );
	}

	/**
	 * @access public
	 */
	function fPut( $fp, $remoteFile, $mode = null ) 
	{
		if ( is_null( $mode ) ) 
			$mode = &$this->transferMode;
			
		return (bool)ftp_fput( $this->_conn_id, $remoteFile, $fp, $mode );
	}

	/**
	 * @access public
	 */	
	function fileExists( $remoteFile ) 
	{
		return null;
	}

	/**
	 * @access public
	 */
	function localExists() 
	{
		return null;
	}

	/**
	 * @access public
	 */
	function dirExists() 
	{
		return null;
	}
	
	/**
	 * @access public
	 */
	function localDirExists() 
	{
		return null;
	}

	/**
	 * @access public
	 */
	function size( $remoteFile ) 
	{
		$t = ftp_size( $this->_conn_id, $remoteFile );
		
		if ( !$t ) 
			return false;
		
		return $t;
	}

	/**
	 * @access public
	 */
	function localSize( $localFile ) 
	{
		return false;
	}

	/**
	 * @access public
	 */
	function lastMod( $remoteFile ) 
	{
		$t = ftp_mdtm( $this->_conn_id, $remoteFile );
				
		if ( !$t ) 
			return false;
					
		return $t;
	}

	/**
	 * @access public
	 */
	function localLastMod( $localFile ) 
	{
		return false;
	}

	/**
	 * @access public
	 */
	function rename( $remoteFile, $newRemoteFile ) 
	{
		return (bool)ftp_rename( $this->_conn_id, $remoteFile, $newRemoteFile );
	}

	/**
	 * @access public
	 */
	function localRename( $localFile, $newLocalFile ) 
	{
		return false;
	}

	/**
	 * @access public
	 */
	function delete( $remoteFile ) 
	{
		return (bool)ftp_delete( $this->_conn_id, $remoteFile );
	}

	/**
	 * @access public
	 */	
	function localDelete( $localFile ) 
	{
		return false;
	}

	/**
	 * @access public
	 */	
	function site( $command ) 
	{
		return (bool)ftp_site( $this->_conn_id, $command );
	}

	/**
	 * @access public
	 */	
	function quit() 
	{
		$this->_isConnected = false;
		$status = @ftp_quit( $this->_conn_id );
		unset( $this->_conn_id );
	}
	
	/**
	 * @access public
	 */
	function synchronizeFile( $localFile, $remoteFile, $direction = 'both' ) 
	{
		return false;
	}

	/**
	 * @access public
	 */
	function synchronizeDir( $localDir, $remoteDir, $depth = 0, $direction = 'both' ) 
	{
		return false;
	}


	// private methods

	/**
	 * @access private
	 */
	function _monthStringToNumber( $month, $zeroFill = false ) 
	{
		switch ( strtolower( substr( $month, 0, 3 ) ) ) 
		{
			case 'jan':

			case 'gen':

			case 'ene':
				if ( $zeroFill ) 
					return '01';
					
				return 1;
				break;
				
			case 'feb':

			case 'fév':

			case 'fev':
				if ( $zeroFill ) 
					return '02';
					
				return 2;
				break;
				
			case 'mar':

			case 'mär':

			case 'maa':
				if ( $zeroFill ) 
					return '03';
					
				return 3;
				break;
			
			case 'apr':
			
			case 'avr':

			case 'abr':
				if ( $zeroFill ) 
					return '04';
					
				return 4;
				break;
				
			case 'may':

			case 'mai':

			case 'mei':

			case 'maj':

			case 'mag':
				if ( $zeroFill ) 
					return '05';
					
				return 5;
				break;
				
			case 'jun':

			case 'jui':

			case 'giu':
				if ( $zeroFill ) 
					return '06';
					
				return 6;
				break;
				
			case 'jul':

			case 'jui':

			case 'lug':
				if ( $zeroFill ) 
					return '07';
					
				return 7;
				break;
				
			case 'aug':

			case 'aoû':

			case 'aou':

			case 'ago':
				if ( $zeroFill ) 
					return '08';
					
				return 8;
				break;
				
			case 'sep':

			case 'set':
				if ( $zeroFill ) 
					return '09';
					
				return 9;
				break;
				
			case 'oct':

			case 'okt':

			case 'ott':

			case 'out':
				if ( $zeroFill ) 
					return '10';
					
				return 10;
				break;
				
			case 'nov':
				if ( $zeroFill ) 
					return '11';
					
				return 11;
				break;
				
			case 'dec':

			case 'déc':

			case 'dez':

			case 'dic':
				if ( $zeroFill ) 
					return '12';
					
				return 12;
				break;
				
			default:
				return 0;
		}
	}
} // END OF FTPClient

?>
