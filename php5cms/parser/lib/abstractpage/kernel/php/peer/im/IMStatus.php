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
|Authors: Setec Astronomy <setec@freemail.it>                          |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


define( "IM_ONLINE",  1 );
define( "IM_OFFLINE", 2 );
define( "IM_UNKNOWN", 3 );

define( "IM_ICQ",    "icq"    );
define( "IM_AIM",    "aim"    );
define( "IM_JABBER", "jabber" );
define( "IM_MSN",    "msn"    );
define( "IM_YAHOO",  "yahoo"  );


// Configuration section //
$GLOBALS["IM_SERVERS"][] = "http://www.the-server.net:8000/";
$GLOBALS["IM_SERVERS"][] = "http://www.the-server.net:8000/";
$GLOBALS["IM_SERVERS"][] = "http://www.the-server.net:8001/";
$GLOBALS["IM_SERVERS"][] = "http://www.the-server.net:8002/";
$GLOBALS["IM_SERVERS"][] = "http://www.the-server.net:8003/";
/*
$GLOBALS["IM_SERVERS"][] = "http://turdinc.kicks-ass.net:6969/";
$GLOBALS["IM_SERVERS"][] = "http://status.galaxyradioaustria.com:8080/";
$GLOBALS["IM_SERVERS"][] = "http://osi.hshh.org:8088/";
$GLOBALS["IM_SERVERS"][] = "http://snind.gotdns.com:8080/";
$GLOBALS["IM_SERVERS"][] = "http://www.eliott-ness.com:2324/";
$GLOBALS["IM_SERVERS"][] = "http://mightymichelob.tcworks.net:8080/";
$GLOBALS["IM_SERVERS"][] = "http://www.electrocity.ca:81/";
$GLOBALS["IM_SERVERS"][] = "http://4.11.204.17/";
$GLOBALS["IM_SERVERS"][] = "http://www.nextstepcomputers.ath.cx:8080/";
*/


/**
 * @package peer_im
 */
 
class IMStatus extends PEAR
{
	/**
	 * @access public
	 */
	var $timeout = 20;
	
	/**
	 * @access public
	 */
	var $medium;
	
	/**
	 * @access public
	 */
	var $account = "";

	/**
	 * Constructor
	 *
	 * @access public
	 */		
	function IMStatus( $account = "", $medium = IM_ICQ )
	{
		$this->medium  = $medium;
		$this->account = $account;
	}

	
	/**
	 * @access public
	 */
	function test()
	{
		$raw_headers = "";
		
		if ( empty( $this->account ) )
			return PEAR::raiseError( "Account ID not specified." );
		
		srand( (float)microtime() * 10000000 ); 
		$server = $GLOBALS["IM_SERVERS"][array_rand( $GLOBALS["IM_SERVERS"] )];
		$url = parse_url( $server );
		$this->_safe_set( $url["host"], "localhost" );
		$this->_safe_set( $url["port"], "80" );
		$this->_safe_set( $url["path"], "/" );
		
		$url["path"] = trim( $url["path"] );
		
		if ( substr( $url["path"], -1 ) != "/" )
			$url["path"] .= "/";
		
		$url["path"] .= $this->medium . "/" . $this->account . "/onurl=online/offurl=offline";
		
		if ( !function_exists( "fsockopen" ) )
		{
			return PEAR::raiseError( "Function fsockopen not found." );
		}
		else
		{
			$fp = fsockopen( $url["host"], $url["port"], $errno, $errstr, $this->timeout ); 
			
			if ( !$fp ) 
			{ 
				return false;
			} 
			else 
			{ 
				fputs( $fp, "GET "   . $url["path"] . " HTTP/1.1\r\n" ); 
				fputs( $fp, "HOST: " . $url["host"] . ":" . $url["port"] . "\r\n" ); 
				fputs( $fp, "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)\r\n" ); 
				fputs( $fp, "Connection: close\r\n\r\n" ); 
			
				while ( !feof( $fp ) ) 
					$raw_headers .= fgets( $fp, 128 );
			}
			
			fclose( $fp );
			
			$headers = array();
			$tmp_headers = explode( "\n", $raw_headers );

			foreach ( $tmp_headers as $header )
			{ 
				$tokens = explode( ":", $header, 2 );
				
				if ( isset( $tokens[0] ) && ( trim( $tokens[0] ) != "" ) )
				{ 
					if ( !isset( $tokens[1] ) ) 
						$tokens[1] = "";
						
					$headers[] = array( $tokens[0] => trim( $tokens[1] ) ); 
				}
			}
			
			$location = "";
			
			foreach ( $headers as $header )
			{ 
				if ( isset( $header["Location"] ) )
				{ 
					$location = $header["Location"]; 
					break;
				}
			}

 			$parse_location = parse_url( $location );
			$this->_safe_set( $parse_location["host"], "unknown" );
			
			switch ( $parse_location["host"] )
			{ 
				case "online": 
					return IM_ONLINE;		
					break; 
				
				case "offline": 
					return IM_OFFLINE;
					break; 
				
				default: 
					return IM_UNKNOWN;
					break; 
			} 	
		}
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _safe_set( &$var_true, $var_false = "" )
	{
		if( !isset( $var_true ) ) 
			$var_true = $var_false;
	}
} // END OF IMStatus

?>
