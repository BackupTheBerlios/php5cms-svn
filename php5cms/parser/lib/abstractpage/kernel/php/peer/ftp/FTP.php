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


using( 'util.Debug' );


define( "FTP_BINARY", 1 );
define( "FTP_ASCII",  0 );


/**
 * @package peer_ftp
 */
 
class FTP extends PEAR
{
	/**
	 * @access public
	 */
	var $debug;
	
	/**
	 * @access public
	 */
	var $umask;
	
	/**
	 * @access public
	 */
	var $timeout;

	/**
	 * @access private
	 */
	var $ftp_sock;
	
	/**
	 * @access private
	 */
	var $ftp_resp;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function FTP( $port = 21, $timeout = 30 )
	{
		$this->debug = new Debug();
		$this->debug->Off();
		
		$this->port     = $port;
		$this->timeout  = $timeout;
		
		$this->maxline  = 4096;
		$this->umask    = 0022;
		$this->ftp_resp = "";
	}

	
	/**
	 * @access public
	 */
	function ftp_connect( $server )
	{
		$this->debug->Message( "Trying to " . $server . ":" . $this->port . " ..." );
		$this->ftp_sock = @fsockopen( $server, $this->port, $errno, $errstr, $this->timeout );

		if ( !$this->ftp_sock || !$this->_ftp_ok() )
		{
			$this->debug->Message( "Cannot connect to remote host \"" . $server . ":" . $this->port . "\"" );
			$this->debug->Message( "fsockopen() " . $errstr . " (" . $errno . ")" );
			
			return false;
		}
		
		$this->debug->Message( "Connected to remote host \"".$server.":".$this->port."\"" );
		return true;
	}

	/**
	 * @access public
	 */
	function ftp_login( $user, $pass )
	{
		$this->_ftp_putcmd( "USER", $user );
		
		if ( !$this->_ftp_ok() )
		{
			$this->debug->Message( "USER command failed" );
			return false;
		}

		$this->_ftp_putcmd( "PASS", $pass );
		
		if ( !$this->_ftp_ok() )
		{
			$this->debug->Message( "PASS command failed" );
			return false;
		}
		
		$this->debug->Message( "Authentication succeeded" );
		return true;
	}

	/**
	 * @access public
	 */
	function ftp_pwd()
	{
		$this->_ftp_putcmd( "PWD" );
		
		if ( !$this->_ftp_ok() )
		{
			$this->debug->Message( "PWD command failed" );
			return false;
		}

		return ereg_replace( "^[0-9]{3} \"(.+)\" .+\r\n", "\\1", $this->ftp_resp );
	}

	/**
	 * @access public
	 */
	function ftp_size( $pathname )
	{
		$this->_ftp_putcmd( "SIZE", $pathname );
		
		if ( !$this->_ftp_ok() )
		{
			$this->debug->Message( "SIZE command failed" );
			return -1;
		}

		return ereg_replace( "^[0-9]{3} ([0-9]+)\r\n", "\\1", $this->ftp_resp );
	}

	/**
	 * @access public
	 */
	function ftp_mdtm( $pathname )
	{
		$this->_ftp_putcmd( "MDTM", $pathname );
		
		if ( !$this->_ftp_ok() )
		{
			$this->debug->Message( "MDTM command failed" );
			return -1;
		}
		
		$mdtm = ereg_replace( "^[0-9]{3} ([0-9]+)\r\n", "\\1", $this->ftp_resp );
		$date = sscanf( $mdtm, "%4d%2d%2d%2d%2d%2d" );
		$timestamp = mktime( $date[3], $date[4], $date[5], $date[1], $date[2], $date[0] );

		return $timestamp;
	}

	/**
	 * @access public
	 */
	function ftp_systype()
	{
		$this->_ftp_putcmd( "SYST" );
		
		if ( !$this->_ftp_ok() )
		{
			$this->debug->Message( "SYST command failed" );
			return false;
		}
		
		$DATA = explode( " ", $this->ftp_resp );
		return $DATA[1];
	}

	/**
	 * @access public
	 */
	function ftp_cdup()
	{
		$this->_ftp_putcmd( "CDUP" );
		$response = $this->_ftp_ok();
		
		if ( !$response )
			$this->debug->Message( "CDUP command failed" );
		
		return $response;
	}

	/**
	 * @access public
	 */
	function ftp_chdir( $pathname )
	{
		$this->_ftp_putcmd( "CWD", $pathname );
		$response = $this->_ftp_ok();
		
		if ( !$response )
			$this->debug->Message( "CWD command failed" );
		
		return $response;
	}

	/**
	 * @access public
	 */
	function ftp_delete( $pathname )
	{
		$this->_ftp_putcmd( "DELE", $pathname );
		$response = $this->_ftp_ok();
		
		if ( !$response )
			$this->debug->Message( "DELE command failed" );
		
		return $response;
	}

	/**
	 * @access public
	 */
	function ftp_rmdir( $pathname )
	{
		$this->_ftp_putcmd( "RMD", $pathname );
		$response = $this->_ftp_ok();
		
		if ( !$response )
			$this->debug->Message( "RMD command failed" );
		
		return $response;
	}

	/**
	 * @access public
	 */
	function ftp_mkdir( $pathname )
	{
		$this->_ftp_putcmd( "MKD", $pathname );
		$response = $this->_ftp_ok();
		
		if ( !$response )
			$this->debug->Message( "MKD command failed" );
		
		return $response;
	}

	/**
	 * @access public
	 */
	function ftp_file_exists( $pathname )
	{
		if ( !( $remote_list = $this->ftp_nlist( "-a" ) ) )
		{
			$this->debug->Message( "Cannot get remote file list" );
			return -1;
		}
		
		reset( $remote_list );
		while ( list(,$value) = each( $remote_list ) )
		{
			if ( $value == $pathname )
			{
				$this->debug->Message( "Remote file " . $pathname . " exists" );
				return true;
			}
		}
		
		$this->debug->Message( "Remote file " . $pathname . " does not exist" );
		return false;
	}

	/**
	 * @access public
	 */
	function ftp_rename( $from, $to )
	{
		$this->_ftp_putcmd( "RNFR", $from );
		
		if ( !$this->_ftp_ok() )
		{
			$this->debug->Message( "RNFR command failed" );
			return false;
		}
		
		$this->_ftp_putcmd( "RNTO", $to );
		$response = $this->_ftp_ok();
		
		if ( !$response )
			$this->debug->Message( "RNTO command failed" );
		
		return $response;
	}

	/**
	 * @access public
	 */
	function ftp_nlist( $arg = "", $pathname = "" )
	{
		if ( !( $string = $this->_ftp_pasv() ) )
			return false;

		if ( $arg == "" )
			$nlst = "NLST";
		else
			$nlst = "NLST " . $arg;
	
		$this->_ftp_putcmd( $nlst, $pathname );
		$sock_data = $this->_ftp_open_data_connection( $string );
		
		if ( !$sock_data || !$this->_ftp_ok() )
		{
			$this->debug->Message( "Cannot connect to remote host" );
			$this->debug->Message( "NLST command failed" );
			
			return false;
		}
		
		$this->debug->Message( "Connected to remote host" );

		while ( !feof( $sock_data ) )
			$list[] = ereg_replace( "[\r\n]", "", fgets( $sock_data, 512 ) );
		
		$this->_ftp_close_data_connection( $sock_data );
		$this->debug->Message( implode( "\n", $list ) );

		if ( !$this->_ftp_ok() )
		{
			$this->debug->Message( "NLST command failed" );
			return false;
		}

		return $list;
	}

	/**
	 * @access public
	 */
	function ftp_rawlist( $pathname = "" )
	{
		if ( !( $string = $this->_ftp_pasv() ) )
			return false;

		$this->_ftp_putcmd( "LIST", $pathname );
		$sock_data = $this->_ftp_open_data_connection( $string );
		
		if ( !$sock_data || !$this->_ftp_ok() )
		{
			$this->debug->Message( "Cannot connect to remote host" );
			$this->debug->Message( "LIST command failed" );
			
			return false;
		}

		$this->debug->Message( "Connected to remote host" );

		while ( !feof( $sock_data ) )
			$list[] = ereg_replace( "[\r\n]", "", fgets( $sock_data, 512 ) );
		
		$this->debug->Message( implode( "\n", $list ) );
		$this->_ftp_close_data_connection( $sock_data );

		if ( !$this->_ftp_ok() )
		{
			$this->debug->Message( "LIST command failed" );
			return false;
		}

		return $list;
	}

	/**
	 * @access public
	 */
	function ftp_get( $localfile, $remotefile, $mode = 1 )
	{
		umask( $this->umask );

		if ( @file_exists( $localfile ) )
			$this->debug->Message( "Warning : local file will be overwritten" );

		$fp = @fopen( $localfile, "w" );
		
		if ( !$fp )
		{
			$this->debug->Message( "Cannot create \"".$localfile."\"" );
			$this->debug->Message( "GET command failed" );
			
			return false;
		}

		if ( !$this->_ftp_type( $mode ) )
		{
			$this->debug->Message( "GET command failed" );
			return false;
		}

		if ( !( $string = $this->_ftp_pasv() ) )
		{
			$this->debug->Message( "GET command failed" );
			return false;
		}

		$this->_ftp_putcmd( "RETR", $remotefile );
		$sock_data = $this->_ftp_open_data_connection( $string );
		
		if ( !$sock_data || !$this->_ftp_ok() )
		{
			$this->debug->Message( "Cannot connect to remote host" );
			$this->debug->Message( "GET command failed" );
			
			return false;
		}
		
		$this->debug->Message( "Connected to remote host" );
		$this->debug->Message( "Retrieving remote file \"".$remotefile."\" to local file \"".$localfile."\"" );
		
		while ( !feof( $sock_data ) )
			fputs( $fp, fread( $sock_data, $this->maxline ) );
		
		fclose( $fp );
		$this->_ftp_close_data_connection( $sock_data );
		$response = $this->_ftp_ok();
		
		if ( !$response )
			$this->debug->Message( "GET command failed" );
		
		return $response;
	}

	/**
	 * @access public
	 */
	function ftp_put( $remotefile, $localfile, $mode = 1 )
	{		
		if ( !@file_exists( $localfile ) )
		{
			$this->debug->Message( "No such file or directory \"".$localfile."\"" );
			$this->debug->Message( "PUT command failed" );
			
			return false;
		}

		$fp = @fopen( $localfile, "r" );
		
		if ( !$fp )
		{
			$this->debug->Message( "Cannot read file \"".$localfile."\"" );
			$this->debug->Message( "PUT command failed" );
			
			return false;
		}

		if ( !$this->_ftp_type( $mode ) )
		{
			$this->debug->Message( "PUT command failed" );
			return false;
		}

		if ( !( $string = $this->_ftp_pasv() ) )
		{
			$this->debug->Message( "PUT command failed" );
			return false;
		}

		$this->_ftp_putcmd( "STOR", $remotefile );
		$sock_data = $this->_ftp_open_data_connection( $string );
		
		if ( !$sock_data || !$this->_ftp_ok() )
		{
			$this->debug->Message( "Cannot connect to remote host" );
			$this->debug->Message( "PUT command failed" );
			
			return false;
		}
		
		$this->debug->Message( "Connected to remote host" );
		$this->debug->Message( "Storing local file \"".$localfile."\" to remote file \"".$remotefile."\"" );
		
		while ( !feof( $fp ) )
			fputs( $sock_data, fread( $fp, $this->maxline ) );
		
		fclose( $fp );
		$this->_ftp_close_data_connection( $sock_data );
		$response = $this->_ftp_ok();
		
		if ( !$response )
			$this->debug->Message( "PUT command failed" );
		
		return $response;
	}

	/**
	 * @access public
	 */
	function ftp_site( $command )
	{
		$this->_ftp_putcmd( "SITE", $command );
		$response = $this->_ftp_ok();
		
		if ( !$response )
			$this->debug->Message( "SITE command failed" );
		
		return $response;
	}

	/**
	 * @access public
	 */
	function ftp_quit()
	{
		$this->_ftp_putcmd( "QUIT" );
		
		if ( !$this->_ftp_ok() || !fclose( $this->ftp_sock ) )
		{
			$this->debug->Message( "QUIT command failed" );
			return false;
		}
		
		$this->debug->Message( "Disconnected from remote host" );
		return true;
	}

	
	// private methods

	/**
	 * @access private
	 */
	function _ftp_type( $mode )
	{
		if ( $mode )
			$type = "I"; // Binary mode
		else
			$type = "A"; // ASCII mode
		
		$this->_ftp_putcmd( "TYPE", $type );
		$response = $this->_ftp_ok();
		
		if ( !$response )
			$this->debug->Message( "TYPE command failed" );
		
		return $response;
	}

	/**
	 * @access private
	 */
	function _ftp_port( $ip_port )
	{
		$this->_ftp_putcmd( "PORT", $ip_port );
		$response = $this->_ftp_ok();
		
		if ( !$response )
			$this->debug->Message( "PORT command failed" );
		
		return $response;
	}

	/**
	 * @access private
	 */
	function _ftp_pasv()
	{
		$this->_ftp_putcmd( "PASV" );
		
		if ( !$this->_ftp_ok() )
		{
			$this->debug->Message( "PASV command failed" );
			return false;
		}

		$ip_port = ereg_replace( "^.+ \\(?([0-9]{1,3},[0-9]{1,3},[0-9]{1,3},[0-9]{1,3},[0-9]+,[0-9]+)\\)?.*\r\n$", "\\1", $this->ftp_resp );
		return $ip_port;
	}

	/**
	 * @access private
	 */
	function _ftp_putcmd( $cmd, $arg = "" )
	{
		if ( $arg != "" )
			$cmd = $cmd . " " . $arg;

		fputs( $this->ftp_sock, $cmd . "\r\n" );
		return true;
	}

	/**
	 * @access private
	 */
	function _ftp_ok()
	{
		$this->ftp_resp = "";
		
		do
		{
			$res = fgets( $this->ftp_sock, 512 );
			$this->ftp_resp .= $res;
		} while ( substr( $res, 3, 1 ) != " " );

		$this->debug->Message( str_replace( "\r\n", "\n", $this->ftp_resp ) );

		if ( !ereg( "^[123]", $this->ftp_resp ) )
			return false;

		return true;
	}

	/**
	 * @access private
	 */
	function _ftp_close_data_connection( $sock )
	{
		$this->debug->Message( "Disconnected from remote host" );
		return fclose( $sock );
	}

	/**
	 * @access private
	 */
	function _ftp_open_data_connection( $ip_port )
	{
		if ( !ereg( "[0-9]{1,3},[0-9]{1,3},[0-9]{1,3},[0-9]{1,3},[0-9]+,[0-9]+", $ip_port ) )
		{
			$this->debug->Message( "Illegal ip-port format(" . $ip_port . ")" );
			return false;
		}

		$DATA   = explode( ",", $ip_port );
		$ipaddr = $DATA[0] . "." . $DATA[1] . "." . $DATA[2] . "." . $DATA[3];
		$port   = $DATA[4] * 256 + $DATA[5];
		
		$this->debug->Message( "Trying to " . $ipaddr . ":" . $port . " ..." );
		$data_connection = @fsockopen( $ipaddr, $port, $errno, $errstr );
		
		if ( !$data_connection )
		{
			$this->debug->Message( "Cannot open data connection to " . $ipaddr . ":" . $port );
			$this->debug->Message( $errstr . " (" . $errno . ")" );
			
			return false;
		}

		return $data_connection;
	}
} // END OF FTP

?>
