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


if ( !defined( "APACHELOG_FORMAT_COMBINED" ) )
{
	define( "APACHELOG_FORMAT_COMBINED", "%h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\"",  true );
	define( "APACHELOG_FORMAT_COMMON",   "%h %l %u %t \"%r\" %>s %b",  true );
	define( "APACHELOG_FORMAT_REFERER",  "%{Referer}i -> %U",  true );
	define( "APACHELOG_FORMAT_AGENT",    "%{User-agent}i",  true );
}


/**
 * ApacheLogsReader is the base class to read an Apache log file.
 * You may extends this class for extract your own statistics.
 *
 * First parameter in Constructor is the filename with path for the log file.
 * Second parameter [optional] is the LogFormat used for the log file 
 * (look for LogFormat in httpd.conf)
 * 
 * This is 4 pre-defined constants for <<standard>> formats used by default 
 * in the httpd.conf
 *
 * 		APACHELOG_FORMAT_COMBINED => "%h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\""
 * 		APACHELOG_FORMAT_COMMON   => "%h %l %u %t \"%r\" %>s %b"
 * 		APACHELOG_FORMAT_REFERER  => "%{Referer}i -> %U"
 * 		APACHELOG_FORMAT_AGENT    => "%{User-agent}i"
 * 
 * ApacheLogVisits is an example of extended class for counting viewed page and visitors
 *
 * You must specify what page you want analysis:
 *   	"" (empty string) => all pages
 *   	"/" => The home page
 *   	"/a_page_name.php" => the page named a_page_name.php
 *
 * You can also looking from a start date:
 *   	"" (empty string) => from the beginning of the log file
 *   	mktime(0,0,0,1,1,2004) => from the 01/01/2004
 *
 * You can also looking from an end date:
 *   	"" (empty string) => to the end of the log file
 *   	mktime(0,0,0,1,5,2004) => to the 01/05/2004
 *
 * @package org_apache
 */
 
class ApacheLogReader extends PEAR
{
	/**
	 * @access public
	 */
	var $logfile;
	
	/**
	 * @access private
	 */
	var $_logformat;
	
	/**
	 * @access private
	 */
	var $_phpformat;
	
	/**
	 * @access private
	 */
	var $_infos = array();
	
	/**
	 * @access private
	 */
	var $_logs = array();
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function ApacheLogReader( $logfile, $logformat = "" )
	{
		$this->logfile    = $logfile;
		$this->_phpformat = "";
		
		if ( $logformat != "" )
			$this->setLogFormat( $logformat );
		else 
			$this->setLogFormat( APACHELOG_FORMAT_COMMON );
	}
	

	/**
	 * @access public
	 */
	function setLogFile( $logfile )
	{
		$this->logfile = $logfile;	
	}

	/**
	 * @access public
	 */
	function setLogFormat( $logformat )
	{
		$this->_logformat = $logformat;
		$this->_logFormatToPHPFormat( $this->_logformat );
		$this->_loadLogFile();
	}

	
	// private methods
	
	/**
	 * @access private
	 */
	function _replaceCharInsideQuote( $str, $char, $replace )
	{
		$ret  = ""; 
		$open = false;
		
		for ( $i = 0; $i < strlen( $str ); $i++ )
		{
			if ( $str{$i} == '"' )
			{
				$open  = !$open;
				$ret  .= $str{$i};
			}
			else if ( $str{$i} == $char )
			{
				if ( $open ) 
					$ret .= $replace; 
				else 
					$ret .= $str{$i}; 
			}
			else 
			{
				$ret .= $str{$i};
			}
		}
		
		return $ret;
	}
	
	/**
	 * @access private
	 */
	function _logFormatToPHPFormat( $logformat )
	{
		$lf_array = explode( " ", $logformat );
		
		foreach ( $lf_array as $value )
		{
			$value = ApacheLogReader::_unquote( $value );
			$elem  = substr( $value, 0, 1 ) . substr( $value, -1 );

			switch ( $elem )
			{
				case "%b":
					$this->_phpformat .= "%s ";
					$this->_infos['Octets'] = 0;
					break;
				
				case "%f":
					$this->_phpformat .= "%s ";
					$this->_infos['filename'] = "";
					break;
				
				case "%e":
				    $this->_phpformat .= "%s ";
					$this->_infos[substr( $value, strpos( $value, "{" ) + 1, strpos( $value, "}" ) - strpos( $value, "{" ) - 1 )] = "";
					break;
				
				case "%h":
					$this->_phpformat .= "%s ";
					$this->_infos['host'] = "";
					break;
				
				case "%i":
					$this->_phpformat .= "%s ";
					$this->_infos[substr( $value, strpos( $value, "{" ) + 1, strpos( $value, "}" ) - strpos( $value, "{" ) - 1 )] = "";
					break;
				
				case "%l":
					$this->_phpformat .= "%s ";
					$this->_infos['identity'] = "";
					break;
				
				case "%n":
					$this->_phpformat .= "%s ";
					$this->_infos[substr( $value, strpos( $value, "{" ) + 1, strpos( $value, "}" ) - strpos( $value, "{" ) - 1 )] = "";
					break;
				
				case "%o":
					$this->_phpformat .= "%s ";
					$this->_infos[substr( $value, strpos( $value, "{" ) + 1, strpos( $value, "}" ) - strpos( $value, "{") - 1 )] = "";
					break;
				
				case "%p":
					$this->_phpformat .= "%s ";
					$this->_infos['Port'] = 0;
					break;
				
				case "%P":
					$this->_phpformat .= "%s ";
					$this->_infos['PID'] = 0;
					break;
				
				case "%r":
				    $this->_phpformat .= "%s ";
					$this->_infos['Request'] = "";
					break;
				
				case "%s":
					$this->_phpformat .= "%s ";
					$this->_infos['CodeHTTP'] = "";
					break;
				
				case "%t":
					$this->_phpformat .= "%s %s";
					
					if ( strpos( $value, "{" ) ) 
					   	$this->_infos['DateTimeF'] = substr( $value, strpos( $value, "{" ) + 1, strpos( $value, "}" ) - strpos( $value, "{" ) - 1 );
					else 
						$this->_infos['DateTime'] = "";
					
					$this->_infos['GMT'] = "";
					break;
				
				case "%T":
					$this->_phpformat .= "%s ";
					$this->_infos['ExecutionTime'] = 0;
					break;
				
				case "%u":
					$this->_phpformat .= "%s ";
					$this->_infos['RemoteUser'] = "";
					break;
				
				case "%U":
					$this->_phpformat .= "%s ";
					$this->_infos['URL'] = 0;
					break;
				
				case "%v":
					$this->_phpformat .= "%s ";
					$this->_infos['VirtualHost'] = 0;
					break;
			}
		}
		
		$this->_phpformat .= "\n";
	}

	/**
	 * @access private
	 */
	function _formatData( $key, $data )
	{
		switch ( $key )
		{
			case "DateTime" :
				if ( substr( $data, 0, 1 ) == "[" )
					 $data = substr( $data, 1 );
					 
				list( $d, $M, $y, $h, $m, $s ) = sscanf( $data, "%2d/%3s/%4d:%2d:%2d:%2d" );
				$date['day'] = $d;
				
				switch ( $M )
				{
					case 'Jan': 
						$date['month'] = 1;
						break;
					
					case 'Feb': 
						$date['month'] = 2;
						break;
					
					case 'Mar': 
						$date['month'] = 3;
						break;
					
					case 'Apr': 
						$date['month'] = 4;
						break;
					
					case 'May': 
						$date['month'] = 5;
						break;
					
					case 'Jun': 
						$date['month'] = 6;
						break;
					
					case 'Jul': 
						$date['month'] = 7;
						break;
					
					case 'Aug':
						$date['month'] = 8;
						break;
					
					case 'Sep': 
						$date['month'] = 9;
						break;
					
					case 'Oct': 
						$date['month'] = 10;
						break;
					
					case 'Nov': 
						$date['month'] = 11;
						break;
					
					case 'Dec': 
						$date['month'] = 12;
						break;
				}
				
				$date['year']   = $y;
				$date['hour']   = $h;
				$date['minute'] = $m;
				$date['second'] = $s;
				$data = $date;
				
				break;
				
			case "DateTimeF":
				if ( substr( $data, 0, 1 ) == "[" )
					$data = strftime( $this->_infos[$key], strtotime( substr( $data, 1 ) ) );
			    else 
					$data = strftime( $this->_infos[$key], strtotime( $data ) );
					
				break;
			
			case "GMT":
				if ( substr( $data, -1 ) == "]" )
					$data = substr( $data, 0, strlen( $data ) - 1 );
						
				break;
			
			case "Method" :
				if ( substr( $data, 0, 1 ) == "\"" )
					 $data = substr( $data, 1 );
					 
				break;
			
			case "http":
				if ( substr( $data, -1 ) == "\"" )
					$data = substr( $data, 0, strlen( $data ) - 1 );
				
				break;
			
			default:
			    if ( ( substr( $data, 0, 1 ) == "\"" ) && ( substr( $data, -1 ) == "\"" ) )
					$data = substr( $data, 1, strlen( $data ) - 2 );
					
				$data = str_replace( '§', ' ',$data );
				break;
		}
		
		return $data;
	}

	/**
	 * @access private
	 */
	function _loadLogFile()
	{
		unset( $this->_logs );
		$this->_logs = array();

		$hFile = fopen( $this->logfile, "r" );
		$line  = fgets( $hFile, 4096 );
		
		while ( !feof( $hFile ) ) 
		{
			$line = $this->_replaceCharInsideQuote( $line, ' ','§' );
			$line = sscanf( $line, $this->_phpformat ); 
			
			$i = 0;
			
			foreach ( array_keys( $this->_infos ) as $key )
			{
				$inf[$key] = $this->_formatData( $key, $line[$i] );
				$i++;
			}
			
			array_push( $this->_logs, $inf );
			$line = fgets( $hFile, 4096 );
		}
		
		fclose( $hFile );
		reset( $this->_logs );
		array_walk( $this->_logs,
		"_expanddatas" );
	}
	
	/**
	 * @static
	 * @access private
	 */
	function _unquote( $str )
	{
		$str = trim( $str );
	
		if ( ( $str[0] == '"' ) && ( $str[strlen( $str ) - 1] == '"' ) )
			$str = substr( $str, 1, strlen( $str ) - 2 );
			
		return $str;
	}
	
	/**
	 * @static
	 * @access private
	 */
	function _reindexArray( $array, $base = 0 )	
	{
		$arraykey = array_keys( $array );
		$tmparray = array( $base => $array[$arraykey[0]] );

		for ( $i = 1; $i < count( $arraykey ); $i++ )
	 		array_push( $tmparray, $array[$arraykey[$i]] );
	
		$array = $tmparray;
	
		unset( $tmparray );
		unset( $arraykey );
		
		return $array;
	}
} // END OF ApacheLogReader


function _expanddatas( &$datas )
{
	if ( array_key_exists( 'Request', $datas ) )
	{
		list( $method, $request, $http ) = explode( " ", $datas['Request'] );
		
		$datas['Request'] = $request;
		$datas['Method']  = $method;
		$datas['http']    = $http;
	}
}

?>
