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


using( 'util.Util' );


/**
 * Example usage
 *
 * $scan = new SimpleScan( "127.0.0.1", 0.2 );
 * echo "Now scanning IP address " . $scan->ip . "<br>";
 *
 * for ( $i = 20 ; $i <= 80 ; $i++ )
 * {
 *		$scan->portinfo( $i );
 *		echo $scan->port . ", " . $scan->portresponse . ", " . round( $scan->actualtime, 2 ) . "s<br>";
 * }
 *
 * echo "Number of scanned ports: " . $scan->numports  . "<br>";
 * echo "Number of open ports: "    . $scan->openports . "<br>";
 * echo "Total time: " . round( $scan->totlatime, 2 )  . "<br>";
 *
 * @package peer
 */

class SimpleScan extends PEAR
{
	/**
	 * scanned IP adderess
	 * @access public
	 */
	var $ip;
	
	/**
	 * currently scanned port
	 * @access public
	 */
	var $port;
	
	/**
	 * port connection timeout 
	 * @access public
	 */
	var $timeout;
	
	/**
	 * number of scanned ports
	 * @access public
	 */
	var $numports;
	
	/**
	 * number of open ports
	 * @access public
	 */
	var $openports;
	
	/**
	 * current port status (true/false)
	 * @access public
	 */
	var $portstatus;
	
	/**
	 * current port info (errorstring or fgets output)
	 * @access public
	 */
	var $portresponse;
	
	/**
	 * start timestamp
	 * @access public
	 */
	var $starttime;
	
	/**
	 * current port scanning time 
	 * @access public
	 */
	var $actualtime;
	
	/**
	 * total time of scanning in s
	 * @access public
	 */
	var $totaltime;
	
	
	/**
	 * Constructor
	 * 
	 * @access public
	 */
	function SimpleScan( $ip, $timeout )
	{
		$this->ip = $ip;
		
		if ( $timeout > 0.01 && $timeout < 10 ) 
			$this->timeout = $timeout;
		else 
			$this->timeout = 0.5;
		
		$this->numports   = 0;
		$this->openports  = 0;
		$this->starttime  = Util::getMicrotime();
		$this->actualtime = 0;
		$this->totaltime  = 0;
	}
	
	
	/**
	 * Scan given port and store results in object variables.
	 *
	 * @access public
	 */
	function portInfo( $port )
	{
		flush();
		$this->port = $port;
		$time = Util::getMicrotime();
		$fp   = fsockopen( "$this->ip", $this->port, $errno, $errstr, $this->timeout );
		
		if ( !$fp )
		{
			$this->portstatus   = false;
			$this->portresponse = $errstr . " (" . $errno . ")";
		}
		else
		{
			if ( $this->port == 80 )
			{
				fputs( $fp, "GET / HTTP/1.0\r\nHost: " . $this->ip . "\r\n\r\n" );

				for ( $i = 0; $i < 5; $i++ )
				{ 
					$tmpresponse = fgets( $fp, 1024 );
					
					if ( ereg( "Server", $tmpresponse ) ) 
						$this->portresponse = $tmpresponse;
				}
			}
			else
			{
				$this->portresponse = fgets( $fp, 1024 );
			}
			
			$this->portstatus = true;
			$this->openports++;
			fclose( $fp );
		}
		
		$this->actualtime = Util::getMicrotime() - $time;
		$this->totaltime  = Util::getMicrotime() - $this->starttime;
		$this->numports++;
	}
} // END OF SimpleScan

?>
