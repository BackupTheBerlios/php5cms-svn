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


define( 'INI_UNQUOTE_NONE',   0 );
define( 'INI_UNQUOTE_DOUBLE', 1 );
define( 'INI_UNQUOTE_SINGLE', 2 );
define( 'INI_UNQUOTE_ALL',    3 );


/**
 * @package io_config
 */
 
class INI extends PEAR 
{
	/**
	 * @access public
	 */
	var $comments;
	
	/**
	 * @access public
	 */
	var $commentChars = array(
		'#', 
		'/', 
		';'
	);
	
	/**
	 * @access public
	 */
	var $unQuote = INI_UNQUOTE_ALL;

	/**
	 * @access public
	 */
	var $_sections;
	
	/**
	 * @access public
	 */
	var $_params;
	
	/**
	 * @access public
	 */
	var $_fileFullPath;
	
	/**
	 * @access public
	 */
	var $_lastError;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function INI( $fileFullPath = '' ) 
	{
		if ( !empty( $fileFullPath ) ) 
			$this->loadFile( $fileFullPath );
	}

	
	/**
	 * @access public
	 */
	function loadFile( $fileFullPath ) 
	{
		$this->reset();
		
		if ( !file_exists( $fileFullPath ) ) 
		{
			$this->_lastError = "File doesn't exists: '{$fileFullPath}'";
			return false;
		}

		if ( !is_readable( $fileFullPath ) ) 
		{
			$this->_lastError = "File is not readable: '{$fileFullPath}'";
			return false;
		}

		$this->_fileFullPath = $fileFullPath;
		$fileContent = file( $fileFullPath);
		$this->_parseFromArray( $fileContent );
		
		return true;
	}

	/**
	 * @access public
	 */
	function loadString( $str ) 
	{
		$this->reset();
		$arr = explode( "\n", $str );
		$this->_parseFromArray( $arr );
		
		return true;
	}

	/**
	 * @access public
	 */
	function setQuoteHandling( $mode = INI_UNQUOTE_ALL ) 
	{
		$this->unQuote = $mode;
	}

	/**
	 * @access public
	 */
	function toString() 
	{
		$outStr = "";
		
		foreach ( $this->_params as $section => $params ) 
		{
			if ( isset( $this->comments[$section] ) ) 
			{
				foreach ( $this->comments[$section] as $comment ) 
					$outStr .= "{$comment}\n";
			}

			$outStr .= "[" . $section . "]\n";
			
			foreach ( $params as $key => $value ) 
			{
				if ( isset( $this->comments[$section . '__' . $key] ) ) 
				{
					foreach ( $this->comments[$section . '__' . $key] as $comment ) 
						$outStr .= "  {$comment}\n";
				}

				$outStr .= "  " . $key . " = " . $value . "\n";
			}
			
			$outStr .= "\n";
		}

		if ( isset( $this->comments['__LastComment__'] ) ) 
		{
			foreach ( $this->comments['__LastComment__'] as $comment ) 
				$outStr .= "{$comment}\n";
		}

		return $outStr;
	}

	/**
	 * @access public
	 */
	function saveFile( $fileFullPath ) 
	{
		$outStr = $this->toString();
		
		if ( !$fp = fopen( $this->_fileFullPath, 'wb' ) ) 
		{
			$this->_lastError = "Failed open the file for writing: '{$fileFullPath}'";
			return false;
		}

		if ( !fwrite( $fp, $outStr ) )
		{
			$this->_lastError = "Failed to write (but was able to open) the file: '{$fileFullPath}'";
			return false;
		}

		@fclose( $fp );
		return true;
	}

	/**
	 * @access public
	 */
	function reset() 
	{
		unset( $this->_sections     );
		unset( $this->_params       );
		unset( $this->_fileFullPath );
		unset( $this->_lastError    );
	}

	/**
	 * @access public
	 */
	function get( $section = null, $key = null ) 
	{
		if ( is_null( $section ) ) 
			return $this->_params;
			
		if ( !isset( $this->_params[$section] ) ) 
			return null;
		
		if ( is_null( $key ) )     
			return $this->_params[$section];
			
		if ( !isset( $this->_params[$section][$key] ) ) 
			return null;
		
		return $this->_params[$section][$key];
	}

	/**
	 * @access public
	 */
	function has( $section, $key = null ) 
	{
		if ( is_null( $key ) ) 
			return ( isset( $this->_params[$section] ) );
		else 
			return ( isset( $this->_params[$section] ) && isset( $this->_params[$section][$key] ) );
	}

	/**
	 * @access public
	 */
	function getLastError() 
	{
		if ( is_null( $this->_lastError ) ) 
			return null;
		
		return $this->_lastError;
	}
	
	
	// private methods
	

	/**
	 * @access private
	 */	
	function _parseFromArray( $arr ) 
	{
		$this->comments = array();
		
		$comment = array();
		$section = '';
		
		foreach ( $arr as $line ) 
		{
			$sectionFound = $valueFound = false;
			
			$param = array(
				'key' => '', 
				'val' => ''
			);
			
			do 
			{
				$line = trim( $line );
				
				if ( empty( $line ) ) 
					break;
				
				if ( in_array( $line[0], $this->commentChars ) ) 
				{
					$comment[] = $line;
					break;
				}

				if ( preg_match( '/\[(.*)\]/', $line, $ar ) ) 
				{
					$section = $ar[1];
					$sectionFound = true;
					
					break;
				}

				$tmp = explode( '=', $line );
				
				if ( !is_array( $tmp ) ) 
					break;
				
				if ( sizeof( $tmp ) < 2 ) 
				{
					$comment[] = @$tmp[0];
					break;
				}

				$param['key'] = trim( $tmp[0] );
				array_shift( $tmp );
				
				if ( sizeof( $tmp ) > 1 ) 
					$tmp[0] = implode( '=', $tmp );
				
				$param['val'] = isset( $tmp[0] )? trim( $tmp[0] ) : '';
				
				if ( empty( $param['val'] ) ) 
				{
					$valueFound = true;
					break;
				}

				$unQuote = '';
				
				if ( $this->unQuote & INI_UNQUOTE_DOUBLE ) 
					$unQuote .= '"';
				
				if ( $this->unQuote & INI_UNQUOTE_SINGLE ) 
					$unQuote .= "'";
					
				if ( empty( $unQuote ) ) 
				{
					$valueFound = true;
					break;
				}

				$regEx = '/^([' . $unQuote . ']?)(.*)\1$/';
				
				if ( preg_match( $regEx, $param['val'], $ar ) ) 
				{
					$param['val'] = $ar[2];
					$valueFound = true;
					
					break;
				} 
				else 
				{
					break;
				}
			} while ( false );
			
			if ( $sectionFound ) 
			{
				$this->_sections[] = $section;
				
				if ( !empty( $comment ) ) 
					$this->comments[$section] = $comment;
				
				$comment = array();
			} 
			else if ( $valueFound ) 
			{
				$this->_params[$section][$param['key']] = $param['val'];
				
				if ( !empty( $comment ) ) 
					$this->comments[$section . '__' . $param['key']] = $comment;
				
				$comment = array();
			}
		}

		if ( !empty( $comment ) ) 
			$this->comments['__LastComment__'] = $comment;
	}
} // END OF INI

?>
