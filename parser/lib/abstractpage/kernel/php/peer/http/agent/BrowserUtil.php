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


define( "BROWSERUTIL_BROWSER_UNKNOWN", 0 );
define( "BROWSERUTIL_BROWSER_IE",      1 );
define( "BROWSERUTIL_BROWSER_MOZILLA", 2 );
define( "BROWSERUTIL_BROWSER_OPERA",   3 );

define( "BROWSERUTIL_OS_UNKNOWN",      0 );
define( "BROWSERUTIL_OS_WIN",          1 );
define( "BROWSERUTIL_OS_MAC",          2 );
define( "BROWSERUTIL_OS_LINUX",        3 );
define( "BROWSERUTIL_OS_UNIX",         4 );


/**
 * Static utility functions.
 *
 * @package peer_http_agent
 */
 
class BrowserUtil
{
	/**
	 * @access public
	 * @static
	 */
	function getBrowser()
	{ 
    	$browsers = array(
      		// regex.. 2nd match returns version
      		"(Konqueror)/(.*)\;"	=> "Konqueror",
      		"(Opera)[ ](.*)[ ]"		=> "Opera",
      		"(MSIE)[ ](.*)\;"		=> "Internet Explorer",
      		"(Galeon)/(.*)[ ]"		=> "Galeon",
      		"(Chimera)/(.*)"		=> "Chimera",
      		"(K-Meleon)[ ](.*)\;"	=> "K-Meleon",  
      		"(Lynx)"				=> "Lynx",
      		"(Links)"				=> "Links",
      		"(Dillo)/(.*)"			=> "Dillo",
      		"(Netscape)"			=> "Netscape",
      		"(Mosaic)/(.*)\("		=> "NCSA Mosaic",
      		"(Phoenix)/(.*)"		=> "Phoenix",
      		"(Mozilla)*rv:(.*)\)"	=> "Mozilla"
    	);

    	$agent = $_SERVER['HTTP_USER_AGENT'];

    	foreach ( $browsers as $regex => $browser )
		{ 
      		if ( eregi( $regex, $agent, $regs ) ) 
				return $browser . ' ' . $regs[2];
		}
	}
  
	/**
	 * @access public
	 * @static
	 */
    function getOS()
	{
    	$sys = array(
      		"(win|windows)[ ](95)"								=> "Windows 95",
      		"(win|windows)[ ](98)"								=> "Windows 98",
      		"(win|windows)[ ]((ME)|(9x)[ ](4.90))"				=> "Windows ME",
      		"(win|windows)[ ]((NT)[ ](5.1)|(XP))"				=> "Windows XP",
      		"(win|windows)[ ]((NT)[ ](5.0)|(2000))"				=> "Windows 2000",
      		"(win|windows)[ ](NT)"								=> "Windows NT",
      		"(win|windows)[ ]*((NT)*[ /]*([0-9]+(.?[0-9]+))*)"	=> "Windows",
      		"(linux)"											=> "Linux",
      		"(freebsd)"											=> "FreeBSD", 
      		"(netbsd)"											=> "NetBSD",
      		"(openbsd)"											=> "OpenBSD",
      		"(sunos|solaris)"									=> "SunOS",
      		"(mac)[ ]OS[ ]X"									=> "Mac OS X",
      		"(mac)[ ]OS[ ]"										=> "Mac OS"
    	);

		$agent = $_SERVER['HTTP_USER_AGENT']; 
  
		foreach ( $sys as $regex => $os )
		{
			if ( eregi( $regex, $agent ) )
				return $os;
		}
	}
	
	/**
	 * @access public
	 * @static
	 */
	function getClientInfo()
	{
		$userAgent  = $_SERVER["HTTP_USER_AGENT"];
		$logversion = null;
		
		if ( ereg( 'MSIE ([0-9].[0-9]{1,2})', $userAgent, $log_version ) )
		{
    		$version = $log_version[1];
    		$browser = BROWSERUTIL_BROWSER_IE;
		}
		else if ( ereg( 'Opera ([0-9].[0-9]{1,2})', $userAgent, $log_version ) )
		{
    		$version = $log_version[1];
    		$browser = BROWSERUTIL_BROWSER_OPERA;
		}
		else if ( ereg( 'Mozilla/([0-9].[0-9]{1,2})', $userAgent, $log_version ) )
		{
    		$version = $log_version[1];
			$browser = BROWSERUTIL_BROWSER_MOZILLA;
		}
		else
		{
    		$version = 0;
			$browser = BROWSERUTIL_BROWSER_UNKNOWN;
		}
		
		if ( strstr( $userAgent, 'Win' ) )
    		$os = BROWSERUTIL_OS_WIN;
		else if ( strstr( $userAgent, 'Mac' ) )
			$os = BROWSERUTIL_OS_MAC;
		else if ( strstr( $userAgent, 'Linux' ) )
			$os = BROWSERUTIL_OS_LINUX;
		else if ( strstr( $userAgent, 'Unix' ) )
			$os = BROWSERUTIL_OS_UNIX;
		else
			$os = BROWSERUTIL_OS_UNKNOWN;

		return array(
			"browser"   => $browser,
			"version"   => $version,
			"os"        => $os,
			"ip"        => $_SERVER["REMOTE_ADDR"],
			"accept"    => explode( ",", $_SERVER["HTTP_ACCEPT"] ),
			"encoding"  => explode( ",", $_SERVER["HTTP_ACCEPT_ENCODING"] ),
			"userAgent" => $userAgent
		);
	}
} // END OF BrowserUtil

?>
