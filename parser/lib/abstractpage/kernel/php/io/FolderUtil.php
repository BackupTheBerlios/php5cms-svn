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


using( 'io.Folder' );
using( 'io.File' );
using( 'io.FileUtil' );


define( "FOLDERUTIL_UNLIM_DEPTH", -99 );


/**
 * Static helper functions.
 *
 * @package io
 */
 
class FolderUtil
{
	/**
	 * Get unique file name for folder.
	 *
	 * @access public
	 * @static
	 */
	function getUniqueFilename( $file, $dir = "", $suffix_format = "_%d" )
	{	
		if ( !file_exists( $dir . $file ) )
			return $file;
	
		$count = 0;
	
		do
		{
			$file_name    = substr( $file, 0, strrpos( $file, '.' ) );
			$file_suffix  = substr( $file, strpos( $file, '.' ), strlen( $file ) );
		
			$count_suffix = sprintf( $suffix_format, $count );
		
			if ( !file_exists( $dir . $file_name . $count_suffix . $file_suffix ) )
				return $file_name . $count_suffix . $file_suffix;
		
			$count++;
		} while ( true );
	}
	
	/**
	 * @access public
	 * @static
	 */
	function removeFoldersRecursivelly( $userPath )
	{
		$location = $userPath;
		$all = opendir( $location );
		 
        if ( substr( $location, -1 ) != "/" ) 
			$location = $location . "/";
			
        $all = opendir( $location );
        
		while ( $file = readdir( $all ) ) 
		{
			if ( is_dir( $location . $file ) && ( $file != ".." ) && ( $file != "." ) ) 
			{
				FolderUtil::removeFoldersRecursivelly( $location . $file );
				unset( $file );
        	} 
			else if ( !is_dir( $location . $file ) ) 
			{
				unlink( $location . $file );
				unset( $file );
			}
		}
		
        closedir( $all );
        unset( $all );
        rmdir( $location );
	}
	
	/**
	 * @access public
	 * @static
	 */
	function updir( $path )
	{
		$last = strrchr( $path, DIRECTORY_SEPARATOR );
		$n1   = strlen( $last );
		$n2   = strlen( $path );

		return substr( $path, 0, $n2 - $n1 );
	}
	
	/**
	 * @access public
	 * @static
	 */
	function mkpath( $path ) 
	{
		if ( empty( $path ) ) 
			return false;
			
		if ( strlen( $path ) < 3 ) 
			return true;
		else if ( is_dir( $path ) ) 
			return true;
		else if ( dirname( $path ) == $path ) 
			return true;

		return ( FolderUtil::mkpath( dirname( $path ) ) && @mkdir( $path, 0775 ) );
	}
	
	/**
	 * @access public
	 * @static
	 */	
	function getFileList( $params = array() ) 
	{
		static $defaultFileDirLink = array(
			'file'     => true, 
			'dir'      => true, 
			'filelink' => true, 
			'dirlink'  => true
		);
		
		static $defaultSettings = array(
			'regFunction' => 'ereg',
			'regEx'       => '', 
			'regWhere'    => 'file', 
			'depth'       => FOLDERUTIL_UNLIM_DEPTH, 
			'followLinks' => false, 
			'sort'        => false,
			'returnType'  => 'fullpath'
		);
		
		$files = array();
		$dirs  = array();
		$currentPath = '';
		
		if ( !isset( $params['_defaults_OK_'] ) ) 
		{
			if ( isset( $params['fileDirLink'] ) && is_array( $params['fileDirLink'] ) ) 
				$fileDirLink = array_merge( $defaultFileDirLink, $params['fileDirLink'] );
			else 
				$fileDirLink = $defaultFileDirLink;
			
			unset( $params['fileDirLink'] );
			
			if ( isset( $params ) && is_array( $params ) ) 
				$t = array_merge( $defaultSettings, $params );
			else 
				$t = $defaultSettings;
			
			$t['fileDirLink'] = $fileDirLink;
			
			if ( empty( $t['fullPath'] ) ) 
				return PEAR::raiseError( "Folder not specified." );

			$tmp = $t['fullPath'];
			
			if ( ( $t['fullPath'] = FileUtil::getRealPath( $tmp ) ) === false ) 
				return PEAR::raiseError( "[{$tmp}] is not an exsisting File nor a Folder." );

			$t['_currentSubPath_'] = '';
			$t['_currentDir_']     = '';
			$t['_currentDepth_']   = 0;
			$t['_useRegEx_']       = ( !empty( $t['regFunction'] ) && !empty( $t['regEx'] ) );
			$t['_defaults_OK_']    = true;
		} 
		else 
		{
			$t = $params;
			$t['_currentDepth_']++;
			$t['_currentSubPath_'] = empty( $t['_currentSubPath_'] )? $t['_currentDir_'] . '/' : $t['_currentSubPath_'] . $t['_currentDir_'] . '/';
		}

		$currentPath = $t['fullPath'];
		
		if ( !$dirObj = @dir( $currentPath ) ) 
			return PEAR::raiseError( "Cannot open connection to PHP's dirhandler for path: [{$t['fullPath']}]." );

		while ( ( $file = $dirObj->read() ) !== false ) 
		{
			if ( ( $file == '.' ) || ( $file == '..' ) ) 
				continue;

			if ( is_dir( $currentPath . $file ) ) 
				$dirs[] = $file;
			else if ( is_file( $currentPath . $file ) ) 
				$files[] = $file;
			
			if ( $t['sort'] ) 
			{
				sort( $dirs  );
				sort( $files );
			}
		}
		
		@$dirObj->close();
		$t['_fileList_'] = array();
		
		foreach ( $files as $file ) 
		{
			$fileFullPath = $currentPath . $file;
			
			if ( $t['fileDirLink']['file'] && ( $t['fileDirLink']['filelink'] || ( !FileUtil::isLink( $fileFullPath ) ) ) ) 
			{
				$doIt = true;
				
				if ( $t['_useRegEx_'] ) 
				{
					$matchStr = ( $t['regWhere'] == 'dir' )? $fileFullPath : $file;
					$doIt = $t['regFunction']( $t['regEx'], $matchStr );
				}

				if ( $doIt ) 
				{
					switch ( $t['returnType'] ) 
					{
						case 'object':
							$t['_fileList_'][] =& new File( $fileFullPath );
							break;
						
						case 'subpath':
							$t['_fileList_'][] = !empty( $t['_currentSubPath_'] )? $t['_currentSubPath_'] . $file : $file;
							break;
						
						case 'fulldir/file':
							$t['_fileList_'][] = array( 
								'dir'  => $currentPath, 
								'file' => $file
							);
							
							break;
						
						case 'subdir/file':
						
						case 'subdir/file2':
							$t['_fileList_'][] = array(
								'dir'  => ( !empty( $t['_currentSubPath_'] ) )? $t['_currentSubPath_'] : '', 
								'file' => $file
							);
						
							break;
							
						case 'nested':
							$t['_fileList_'][$file] = false;
							break;
						
						case 'nested2':
							$t['_fileList_'][] = $file;
							break;
						
						default:
							$t['_fileList_'][] = $fileFullPath;
					}
				}
			}
		}

		foreach ( $dirs as $dir ) 
		{
			$dirFullPath = $currentPath . $dir;
			
			if ( substr( $dirFullPath, -1 ) !== '/' ) 
				$dirFullPath .= '/';
			
			$t['_currentDir_'] = $dir;
			
			if ( $t['fileDirLink']['dir'] && ( $t['fileDirLink']['dirlink'] || ( !FileUtil::isLink( $dirFullPath ) ) ) ) 
			{
				$doIt = true;
				
				if ( $t['_useRegEx_'] ) 
				{
					$matchStr = ( $t['regWhere'] == 'dir' )? $dirFullPath : $dir;
					$doIt = $t['regFunction']( $t['regEx'], $matchStr );
				}

				if ( $doIt ) 
				{
					switch ( $t['returnType'] ) 
					{
						case 'object':
							$t['_fileList_'][] =& new Folder( $dirFullPath );
							break;
						
						case 'subpath':
							$t['_fileList_'][] = !empty( $t['_currentSubPath_'] )? $t['_currentSubPath_'] . $dir . '/' : $dir .'/';
							break;
						
						case 'fulldir/file':
							$t['_fileList_'][] = array(
								'dir'  => $dirFullPath, 
								'file' => ''
							);
							
							break;
						
						case 'subdir/file':
							$t['_fileList_'][] = array( 
								'dir'  => ( isset( $t['_currentSubPath_'] ) )? $t['_currentSubPath_'] . $dir . '/' : $dir, 
								'file' => ''
							);
							
							break;
							
						case 'subdir/file2':
							$t['_fileList_'][] = array(
								'dir'  => ( isset( $t['_currentSubPath_'] ) )? $t['_currentSubPath_']  : '', 
								'file' => $dir
							);
							
							break;
							
						case 'nested':
						
						case 'nested2':
							$t['_fileList_'][$dir] = array();
							break;
						
						default:
							$t['_fileList_'][] = $dirFullPath;
					}
				}
			}

			if ( ( ( $t['depth'] == FOLDERUTIL_UNLIM_DEPTH ) || ( $t['depth'] >= $t['_currentDepth_'] ) ) && ( is_readable( $dirFullPath ) ) && ( ( $t['followLinks'] ) || ( !FileUtil::isLink( $dirFullPath ) ) ) ) 
			{
				$t['fullPath'] = $dirFullPath;
				$dirList2 = FolderUtil::getFileList( $t );
				
				if ( PEAR::isError( $dirList2 ) ) 
				{
					return $dirList2;
				} 
				else 
				{
					if ( $t['returnType'] == 'nested' || $t['returnType'] == 'nested2' ) 
						$t['_fileList_'][$dir] = $dirList2;
					else 
						$t['_fileList_'] = array_merge( $t['_fileList_'], $dirList2 );
				}
			}
		}

		return $t['_fileList_'];
	}
	
	/**
	 * @access public
	 * @static
	 */	
	function getFilenames( $directory )
	{  
		// load directory into array  
  		$handle = opendir( $directory );  
  
  		while ( $file = readdir( $handle ) ) 
  		{ 
    		if ( $file != "." && $file != ".." && !is_dir( $file ) )  
      			$retVal[count( $retVal)] = $file;  
  		}

  		// clean up and sort  
  		closedir( $handle );  
  		sort( $retVal );  

		return $retVal;  
	}
	
	/**
	 * @access public
	 * @static
	 */
	function emptyFolder( $path, $recursive = false, $regExp = null ) 
	{
		$depth = ( $recursive )? FOLDERUTIL_UNLIM_DEPTH : 0;
		
		$params = array(
			'depth'    => $depth,
			'fullPath' => $path
		);
		
		if ( !is_null( $regExp ) ) 
		{
			$params['regEx']       = $regExp;
			$params['regFunction'] = 'preg';
			$params['regWhere']    = 'file';
		}

		$fileList = FolderUtil::getFileList( $params );
		
		if ( PEAR::isError( $fileList ) ) 
			return $fileList;

		while ( list(,$fullPath) = each( $fileList ) ) 
			$status = @unlink( $fullPath );

		return true;
	}
} // END OF FolderUtil

?>
