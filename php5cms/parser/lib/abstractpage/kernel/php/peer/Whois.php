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
 * @package peer
 */
 
class Whois extends PEAR
{
	/**
	 * @access public
	 */
	var $whois_server;
	
	/**
	 * @access public
	 */
	var $timeout = 30;
	
	
	/**
	 * @access public
	 */	
	function lookup( $domain )
	{
		$result = "";
		$parts  = array();
		$host   = "";
		
		// .tv don't allow access to their whois
		if ( strstr( $domain, ".tv" ) )
		{
			$result = "'.tv' domain names require you to have an account to do whois searches.";
		}
		// New domains fix (half work, half don't)
		else if ( strstr( $domain, ".name" ) || strstr( $domain, ".pro" ) >0 )
		{
			$result = "'.name', '.pro' require you to have an account to do whois searches.";
		}
		else
		{
			if ( empty( $this->whois_server ) )
			{
				$parts       = explode( ".", $domain );
				$testhost    = $parts[sizeof( $parts )-1];
				$whoisserver = $testhost . ".whois-servers.net";
				$this->host  = gethostbyname( $whoisserver );
				$this->host  = gethostbyaddr( $this->host  );
			
				if ( $this->host == $testhost )
					$this->host = "whois.internic.net";

				flush();
			}
			
			$whoisSocket = fsockopen( $this->host, 43, $errno, $errstr, $this->timeout );
			
			if ( $whoisSocket )
			{
				fputs( $whoisSocket, $domain . "\015\012" );
				
				while ( !feof( $whoisSocket ) )
					$result .= fgets( $whoisSocket, 128 ) . "<br>";

				fclose( $whoisSocket );
			}
		}
		
		return $result;
	}
} // END OF Whois

?>
