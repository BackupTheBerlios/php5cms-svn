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
 * Retrieves the file size, type and last modifies of a file
 * that can be reached via a HTTP connection.
 *
 * Usage:
 *
 * $r = new RemoteFileStats( 'www.zend.com', 'images/logo.gif' );
 *
 * if ( $r->error )
 * {
 *		echo $r->getErrorStr(), '(', $r->getErrorNo(), ')<br>';
 * }
 * else
 * {
 *		echo $r->getRemoteServer(),       '<br>',
 *		 	 $r->getRemoteLastModified(), '<br>',
 *		 	 $r->getRemoteFileSize(),     '<br>',
 *		 	 $r->getRemoteFileType(),     '<br>';
 * }
 *
 * @package peer_http
 */

class RemoteFileStats extends PEAR
{
	/**
	 * @access public
	 */
	var $error;
	
	/**
	 * @access public
	 */
	var $errno;
	
	/**
	 * @access public
	 */
	var $errstr;
	
	/**
	 * @access public
	 */
	var $remoteSever;
	
	/**
	 * @access public
	 */
	var $remoteLastModified;
	
	/**
	 * @access public
	 */
	var $remoteFileSize;
	
	/**
	 * @access public
	 */
	var $remoteFileType;
	
	/**
	 * @access public
	 */
	var $httpTranslate = array(
		'Server'         => 'remoteSever',
		'Last-Modified'  => 'remoteLastModified',
		'Content-Length' => 'remoteFileSize',
		'Content-Type'   => 'remoteFileType'
	);
	
	/**
	 * @access public
	 */
	var $stats = array(
		'remoteSever'        => '',
		'remoteLastModified' => '',
		'remoteFileSize'     => '',
		'remoteFileType'     => ''
	);
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function RemoteFileStats( $host, $file, $port = 80 )
	{
		$file = preg_replace( "#^\/|^#i", "/", $file );
		$this->error = false;
		$fp = fsockopen( $host, $port, $this->errno, $this->errstr, 30 );
		
		if ( $fp )
		{
			fputs( $fp, "HEAD $file HTTP/1.0\r\n" );
			fputs( $fp, "Host: $host\r\n" );
			fputs( $fp, "\r\n" );
			
			while ( !feof( $fp ) )
			{
				$line = fgets( $fp, 4096 );
				
				if ( preg_match( "/^([a-zA-Z\-]+): ([[:ascii:]]+)$/", $line, $arr ) )
				{
					if ( isset( $this->httpTranslate[$arr[1]] ) )
						$this->stats[$this->httpTranslate[$arr[1]]] = $arr[2];
				}
			}
		}
		else
		{
			$this->error = true;
		}
	}
	

	/**
	 * @access public
	 */	
	function getRemoteServer()
	{
		return $this->stats['remoteSever'];
	}

	/**
	 * @access public
	 */
	function getRemoteLastModified()
	{
		return $this->stats['remoteLastModified'];
	}

	/**
	 * @access public
	 */	
	function getRemoteFileSize()
	{
		return $this->stats['remoteFileSize'];
	}

	/**
	 * @access public
	 */	
	function getRemoteFileType()
	{
		return $this->stats['remoteFileType'];		
	}

	/**
	 * @access public
	 */	
	function inError()
	{
		return $this->error;
	}

	/**
	 * @access public
	 */	
	function getErrorStr()
	{
		return $this->errstr;
	}

	/**
	 * @access public
	 */	
	function getErrorNo()
	{
		return $this->errno;
	}
} // END OF RemoteFileStats

?>
