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


/**
 * TCP PortList class.
 * See http://www.iana.org/assignments/port-numbers for more information.
 *
 * @package peer
 */

class PortList extends PEAR
{
	/**
	 * @access public
	 */
	var $domain;
	
	/**
	 * @access public
	 */
	var $timelimit;
	
	/**
	 * @access public
	 */
	var $ports = array();
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function PortList( $domain = '', $time_limit_each = 5 )
	{
		$this->debug = new Debug();
		$this->debug->Off();
		
		$this->domain    = $domain;
		$this->timelimit = $time_limit_each;
		
		$this->_populate();
	}
	
	/**
	 * @access public
	 */
	function debugDump()
	{
		foreach ( $this->ports as $key => $value )
		{
			$this->debug->Message(
				"Name: " . $value["name"] . " - " . 
				"Open: " . $value["open"] . " - " .
				"Description: " . $value["desc"]
			);
		}
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populate()
	{
		$this->ports = array(
			"11" => array(
				"name"   => "systat",
				"open"   => $this->_check( "11" ),
				"desc"   => "Active Users",
				"length" => ""
			),
			"13" => array(
				"name"   => "daytime",
				"open"   => $this->_check( "13" ),
				"desc"   => "Daytime",
				"length" => "26"
			),
			"17" => array(
				"name"   => "qoth",
				"open"   => $this->_check( "13" ),
				"desc"   => "Quote of the Day",
				"length" => ""
			),
			"18" => array(
				"name"   => "msp",
				"open"   => $this->_check( "18" ),
				"desc"   => "Message Send Protocol",
				"length" => ""
			),
			"20" => array(
				"name"   => "ftp-data",
				"open"   => $this->_check( "20" ),
				"desc"   => "File Transfer [Default Data]",
				"length" => ""
			),
			"21" => array(
				"name"   => "ftp",
				"open"   => $this->_check( "21" ),
				"desc"   => "File Transfer [Control]",
				"length" => ""
			),
			"22" => array(
				"name"   => "ssh",
				"open"   => $this->_check( "22" ),
				"desc"   => "SSH Remote Login Protocol",
				"length" => ""
			),
			"23" => array(
				"name"   => "telnet",
				"open"   => $this->_check( "23" ),
				"desc"   => "Telnet",
				"length" => ""
			),
			"25" => array(
				"name"   => "smtp",
				"open"   => $this->_check( "25" ),
				"desc"   => "Simple Mail Transfer",
				"length" => ""
			),
			"27" => array(
				"name"   => "nsw-fe",
				"open"   => $this->_check( "27" ),
				"desc"   => "NSW User System FE",
				"length" => ""
			),
			"29" => array(
				"name"   => "msg-icp",
				"open"   => $this->_check( "29" ),
				"desc"   => "MSG ICP",
				"length" => ""
			),
			"31" => array(
				"name"   => "msg-auth",
				"open"   => $this->_check( "31" ),
				"desc"   => "MSG Authentication",
				"length" => ""
			),
			"33" => array(
				"name"   => "dsp",
				"open"   => $this->_check( "33" ),
				"desc"   => "Display Support Protocol",
				"length" => ""
			),
			"37" => array(
				"name"   => "time",
				"open"   => $this->_check( "37" ),
				"desc"   => "Time",
				"length" => ""
			),
			"38" => array(
				"name"   => "rap",
				"open"   => $this->_check( "38" ),
				"desc"   => "Route Access Protocol",
				"length" => ""
			),
			"39" => array(
				"name"   => "rlp",
				"open"   => $this->_check( "39" ),
				"desc"   => "Resource Location Protocol",
				"length" => ""
			),
			"42" => array(
				"name"   => "nameserver",
				"open"   => $this->_check( "42" ),
				"desc"   => "Host Name Server",
				"length" => ""
			),
			"43" => array(
				"name"   => "nicname",
				"open"   => $this->_check( "43" ),
				"desc"   => "Who Is",
				"length" => ""
			),
			"44" => array(
				"name"   => "mpm-flags",
				"open"   => $this->_check( "44" ),
				"desc"   => "MPM FLAGS Protocol",
				"length" => ""
			),
			"45" => array(
				"name"   => "mpm",
				"open"   => $this->_check( "45" ),
				"desc"   => "Message Processing Module [recv]",
				"length" => ""
			),
			"46" => array(
				"name"   => "mpm-snd",
				"open"   => $this->_check( "46" ),
				"desc"   => "MPM [default send]",
				"length" => ""
			),
			"47" => array(
				"name"   => "ni-ftp",
				"open"   => $this->_check( "47" ),
				"desc"   => "NI FTP",
				"length" => ""
			),
			"49" => array(
				"name"   => "tacacs",
				"open"   => $this->_check( "49" ),
				"desc"   => "Login Host Protocol (TACACS)",
				"length" => ""
			),
			"50" => array(
				"name"   => "re-mail-ck",
				"open"   => $this->_check( "50" ),
				"desc"   => "Remote Mail Checking Protocol",
				"length" => ""
			),
			"52" => array(
				"name"   => "xns-time",
				"open"   => $this->_check( "52" ),
				"desc"   => "XNS Time Protocol",
				"length" => ""
			),
			"53" => array(
				"name"   => "domain",
				"open"   => $this->_check( "53" ),
				"desc"   => "Domain Name Server",
				"length" => ""
			),
			"63" => array(
				"name"   => "whois++",
				"open"   => $this->_check( "63" ),
				"desc"   => "whois++",
				"length" => ""
			),
			"66" => array(
				"name"   => "sql*net",
				"open"   => $this->_check( "66" ),
				"desc"   => "Oracle SQL*NET",
				"length" => ""
			),
			"67" => array(
				"name"   => "bootps",
				"open"   => $this->_check( "67" ),
				"desc"   => "Bootstrap Protocol Server",
				"length" => ""
			),
			"69" => array(
				"name"   => "tftp",
				"open"   => $this->_check( "69" ),
				"desc"   => "Trivial File Transfer",
				"length" => ""
			),
			"70" => array(
				"name"   => "gopher",
				"open"   => $this->_check( "70" ),
				"desc"   => "Gopher",
				"length" => ""
			),
			"79" => array(
				"name"   => "finger",
				"open"   => $this->_check( "79" ),
				"desc"   => "Finger",
				"length" => ""
			),
			"80" => array(
				"name"   => "http",
				"open"   => $this->_check( "80" ),
				"desc"   => "World Wide Web HTTP",
				"length" => ""
			),
			"92" => array(
				"name"   => "npp",
				"open"   => $this->_check( "92" ),
				"desc"   => "Network Printing Protocol",
				"length" => ""
			),
			"93" => array(
				"name"   => "dcp",
				"open"   => $this->_check( "93" ),
				"desc"   => "Device Control Protocol",
				"length" => ""
			),
			"101" => array(
				"name"   => "hostname",
				"open"   => $this->_check( "101" ),
				"desc"   => "NIC Host Name Server",
				"length" => ""
			),
			"109" => array(
				"name"   => "pop2",
				"open"   => $this->_check( "109" ),
				"desc"   => "Post Office Protocol - Version 2",
				"length" => ""
			),
			"110" => array(
				"name"   => "pop3",
				"open"   => $this->_check( "110" ),
				"desc"   => "Post Office Protocol - Version 3",
				"length" => ""
			),
			"113" => array(
				"name"   => "auth",
				"open"   => $this->_check( "113" ),
				"desc"   => "Authentication Service",
				"length" => ""
			),
			"115" => array(
				"name"   => "sftp",
				"open"   => $this->_check( "115" ),
				"desc"   => "Simple File Transfer Protocol",
				"length" => ""
			),
			"118" => array(
				"name"   => "sqlserv",
				"open"   => $this->_check( "118" ),
				"desc"   => "SQL Services",
				"length" => ""
			),
			"119" => array(
				"name"   => "nntp",
				"open"   => $this->_check( "119" ),
				"desc"   => "Network News Transfer Protocol",
				"length" => ""
			),
			"123" => array(
				"name"   => "ntp",
				"open"   => $this->_check( "123" ),
				"desc"   => "Network Time Protocol",
				"length" => ""
			),
			"133" => array(
				"name"   => "statsrv",
				"open"   => $this->_check( "133" ),
				"desc"   => "Statistics Service",
				"length" => ""
			),
			"143" => array(
				"name"   => "imap",
				"open"   => $this->_check( "143" ),
				"desc"   => "Internet Message Access Protocol",
				"length" => ""
			),
			"150" => array(
				"name"   => "sql-net",
				"open"   => $this->_check( "150" ),
				"desc"   => "SQL-NET",
				"length" => ""
			),
			"152" => array(
				"name"   => "bftp",
				"open"   => $this->_check( "152" ),
				"desc"   => "Background File Transfer Program",
				"length" => ""
			),
			"161" => array(
				"name"   => "snmp",
				"open"   => $this->_check( "161" ),
				"desc"   => "SNMP",
				"length" => ""
			),
			"169" => array(
				"name"   => "send",
				"open"   => $this->_check( "169" ),
				"desc"   => "SEND",
				"length" => ""
			),
			"194" => array(
				"name"   => "irc",
				"open"   => $this->_check( "194" ),
				"desc"   => "Internet Relay Chat Protocol",
				"length" => ""
			),
			"209" => array(
				"name"   => "qmtp",
				"open"   => $this->_check( "209" ),
				"desc"   => "The Quick Mail Transfer Protocol",
				"length" => ""
			),
			"213" => array(
				"name"   => "ipx",
				"open"   => $this->_check( "213" ),
				"desc"   => "IPX",
				"length" => ""
			),
			"217" => array(
				"name"   => "dbase",
				"open"   => $this->_check( "217" ),
				"desc"   => "dBASE Unix",
				"length" => ""
			),
			"220" => array(
				"name"   => "imap3",
				"open"   => $this->_check( "220" ),
				"desc"   => "Interactive Mail Access Protocol v3",
				"length" => ""
			),
			"322" => array(
				"name"   => "rtsps",
				"open"   => $this->_check( "322" ),
				"desc"   => "RTSPS",
				"length" => ""
			),
			"389" => array(
				"name"   => "ldap",
				"open"   => $this->_check( "389" ),
				"desc"   => "Lightweight Directory Access Protocol",
				"length" => ""
			),
			"406" => array(
				"name"   => "imsp",
				"open"   => $this->_check( "406" ),
				"desc"   => "Interactive Mail Support Protocol",
				"length" => ""
			),
			"418" => array(
				"name"   => "hyper-g",
				"open"   => $this->_check( "418" ),
				"desc"   => "Hyper-G",
				"length" => ""
			),
			"433" => array(
				"name"   => "nnsp",
				"open"   => $this->_check( "433" ),
				"desc"   => "NNSP",
				"length" => ""
			),
			"443" => array(
				"name"   => "https",
				"open"   => $this->_check( "443" ),
				"desc"   => "http protocol over TLS/SSL",
				"length" => ""
			),
			"444" => array(
				"name"   => "snpp",
				"open"   => $this->_check( "444" ),
				"desc"   => "Simple Network Paging Protocol",
				"length" => ""
			),
			"515" => array(
				"name"   => "printer",
				"open"   => $this->_check( "515" ),
				"desc"   => "spooler",
				"length" => ""
			),
			"523" => array(
				"name"   => "ibm-db2",
				"open"   => $this->_check( "523" ),
				"desc"   => "IBM-DB2",
				"length" => ""
			),
			"537" => array(
				"name"   => "nmsp",
				"open"   => $this->_check( "537" ),
				"desc"   => "Networked Media Streaming Protocol",
				"length" => ""
			),
			"551" => array(
				"name"   => "cybercash",
				"open"   => $this->_check( "551" ),
				"desc"   => "cybercash",
				"length" => ""
			),
			"554" => array(
				"name"   => "rtsp",
				"open"   => $this->_check( "554" ),
				"desc"   => "Real Time Stream Control Protocol",
				"length" => ""
			),
			"563" => array(
				"name"   => "nntps",
				"open"   => $this->_check( "563" ),
				"desc"   => "nntp protocol over TLS/SSL (was snntp)",
				"length" => ""
			),
			"990" => array(
				"name"   => "ftps",
				"open"   => $this->_check( "990" ),
				"desc"   => "ftp protocol, control, over TLS/SSL",
				"length" => ""
			),
			"992" => array(
				"name"   => "telnets",
				"open"   => $this->_check( "992" ),
				"desc"   => "telnet protocol over TLS/SSL",
				"length" => ""
			),
			"993" => array(
				"name"   => "imaps",
				"open"   => $this->_check( "993" ),
				"desc"   => "imap4 protocol over TLS/SSL",
				"length" => ""
			),
			"994" => array(
				"name"   => "ircs",
				"open"   => $this->_check( "994" ),
				"desc"   => "irc protocol over TLS/SSL",
				"length" => ""
			),
			"995" => array(
				"name"   => "pop3s",
				"open"   => $this->_check( "995" ),
				"desc"   => "pop3 protocol over TLS/SSL (was spop3)",
				"length" => ""
			),
			"1498" => array(
				"name"   => "sybase-sqlany",
				"open"   => $this->_check( "1498" ),
				"desc"   => "Sybase SQL Any",
				"length" => ""
			),
			"1525" => array(
				"name"   => "orasrv",
				"open"   => $this->_check( "1525" ),
				"desc"   => "oracle",
				"length" => ""
			),
			"2628" => array(
				"name"   => "dict",
				"open"   => $this->_check( "2628" ),
				"desc"   => "DICT",
				"length" => ""
			),
			"2638" => array(
				"name"   => "sybaseanywhere",
				"open"   => $this->_check( "2638" ),
				"desc"   => "Sybase Anywhere",
				"length" => ""
			),
			"3255" => array(
				"name"   => "semaphore",
				"open"   => $this->_check( "3255" ),
				"desc"   => "Semaphore Connection Port",
				"length" => ""
			),
			"3306" => array(
				"name"   => "mysql",
				"open"   => $this->_check( "3306" ),
				"desc"   => "MySQL",
				"length" => ""
			),
			"5432" => array(
				"name"   => "postgresql",
				"open"   => $this->_check( "5432" ),
				"desc"   => "PostgreSQL Database",
				"length" => ""
			),
			"5757" => array(
				"name"   => "x500ms",
				"open"   => $this->_check( "5757" ),
				"desc"   => "OpenMail X.500 Directory Server",
				"length" => ""
			),
			"5771" => array(
				"name"   => "netagent",
				"open"   => $this->_check( "5771" ),
				"desc"   => "NetAgent",
				"length" => ""
			),
			"5999" => array(
				"name"   => "cvsup",
				"open"   => $this->_check( "5999" ),
				"desc"   => "CVSup",
				"length" => ""
			)
		);
	}
	
	/**
	 * @access private
	 */
	function _check( $port )
	{
		$p = fsockopen( $this->domain, $port, &$errno, &$errstr, $this->timelimit );
		
		if ( $p )
			return "true";
		else
			return "false";
	}
} // END OF PortList

?>
