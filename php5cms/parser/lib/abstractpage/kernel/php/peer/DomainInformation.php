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
 * This class allows you to query the whois server and obtain information
 * about who owns a particular domain, when that domain expires, and other
 * information.
 *
 * Usage:
 *
 * $domain = new DomainInformation("goclick.com");
 * $domain->lookup();
 * $info = $domain->get();
 * foreach ( $info as $key => $val )
 * 		print "$key = $val<br>";
 *
 * if ( isset( $info['whoisserver'] ) )
 * {
 * 		$domain->setWhois( $info['whoisserver'] );
 *		$domain->lookup();
 *	
 *		$info = $domain->get();
 *
 * 		foreach ( $info as $key => $val )
 * 			print "$key = $val<br>";
 * }
 * else
 * {
 * 		foreach ( $info as $key => $val )
 * 			print "$key = $val<br>";
 * }
 *
 * @package peer
 */

class DomainInformation extends PEAR
{
	/**
	 * @access private
	 */
	var $_whois_server;
	
	/**
	 * @access private
	 */
 	var $_ip_address;
	
	/**
	 * @access private
	 */
 	var $_time_out;
	
	/**
	 * @access private
	 */
 	var $_port_num;
	
	/**
	 * @access private
	 */
 	var $_results;
 
 	
	/**
	 * Constructor
	 *
	 * @access public
	 */
 	function DomainInformation( $ip_address, $whois_server = "" )
 	{
  		$this->_domain_name = $ip_address;
		$this->_port_num    = 43;
		$this->_time_out    = 30;
	
		if ( $whois_server == "" )
			$this->_whois_server = "whois.internic.net";
		else
			$this->_whois_server = $whois_server;
 	}

 
 	/**
	 * @access public
	 */
 	function setWhois( $server )
 	{
  		$this->_whois_server = trim( $server );
 	}
 
  	/**
 	 * @access public
	 */
 	function lookup()
 	{
  		$host      = $this->_whois_server;
		$port_num  = $this->_port_num;
		$time_out  = $this->_time_out;
		$domain    = $this->_domain_name;
		$feed_back = "";
	
  		if ( $who_sock = fsockopen( $host, $port_num, $errno, $errstr, $time_out ) )
		{
	 		$feeder = "$domain\015\012";
	 		fputs( $who_sock, $feeder );
	 
	 		while ( !feof( $who_sock ) )
	  			$feed_back .= fgets($who_sock, 128) . "\n";
	 
	 		fclose( $who_sock );
		}
		else
		{
	 		// print "Cannot connect to [$host] for query.";
	 		// exit;
		}
		
		$this->_results = $feed_back;
 	}
 
  	/**
 	 * @access public
	 */
 	function get()
 	{
  		$res     = $this->_results;
		$results = array();
	
		$fn = preg_match( '/domain.+?:(.+?)\\n/i',   $res, $full_name );
		$cr = preg_match( '/creat.+?:(.+?)\\n/i',    $res, $created   );
		$ex = preg_match( '/expir.+?:(.+?)\\n/i',    $res, $expires   );
		$up = preg_match( '/updated.+?:(.+?)\\n/i',  $res, $updated   );
		$wh = preg_match( '/whois.+?:(.+?)\\n/i',    $res, $whois     );
		$re = preg_match( '/referral.+?:(.+?)\\n/i', $res, $referral  );
		
		$ns = preg_match_all( '/name ser.+?:(.+?)\\n/i', $res, $name_server );
	
		if ( $fn ) 
			$results['fullname']    = $full_name[1];
		
		if ( $cr ) 
			$results['created']     = $created[1];
		
		if ( $ex ) 
			$results['expires']     = $expires[1];
		
		if ( $up ) 
			$results['updated']     = $updated[1];
		
		if ( $wh ) 
			$results['whoisserver'] = $whois[1];
		
		if ( $re ) 
			$results['nameserver']  = $name_server[1];
		
		if ( $ns ) 
			$results['referral']    = $referral[1];
	
		return $results;
 	}
} // END OF DomainInformation

?>
