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


using( 'util.text.StringUtil' );


/**
 * Static helper functions.
 *
 * @package peer
 */
 
class NetUtil
{
	/**
	 * This function will enable you to check network services of hosts.
	 *
	 * @access public
	 * @static
	 */
	function lookup( $hport, $Something, $who )
	{
		$fp = fsockopen( $who, $hport, &$errno, &$errstr, 4 );
		return $fp;
	}

	/**
	 * This function does a portscan on a defined range of ports.
	 *
	 * @access public
	 * @static
	 */
	function portscan( $portscan_address, $portscan_from, $portscan_to )
	{
		$host = gethostbyaddr( $portscan_address );
	
		for ( $i = $portscan_from; $i <= $portscan_to; $i++ )
		{
			$sock = fsockopen( $host, $i, &$num, &$error, 1 );

			if ( $sock )
			{
				if ( getservbyport( $i, "tcp" ) != null )
					$serv = getservbyport( $i, "tcp" );
				else
					$serv = "undefined";

				$port_result[$i] = "Port: $i/tcp open Service: $serv\n";
			}
			else
			{
				unset( $sock );
			}
		}
		
		return $port_result;
	}
	
	/**
	 * This function does an nslookup on a specific IP or DNS name.
	 *
	 * @access public
	 * @static
	 */
	function nslookup( $nslookup_str )
	{
		$nslookup_str = strtolower( $nslookup_str );
		list( $a, $b, $c, $d ) = explode( ".", $nslookup_str );

		if ( ereg( "([0-9]{3})", $a, $regs ) )
			$return_str = gethostbyaddr( $nslookup_str );
		else if ( ereg( "([a-z])", $a, $regs ) )
			$return_str = gethostbyname( $nslookup_str );
		else
			return false;
		
		return $return_str;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function finger( $a_server, $a_query = "" )
	{
		$sock = fsockopen( $a_server, 79, &$errno, &$errstr, 10 );
	
   		if ( !$sock )
   		{
       		$ret_str = "$errstr ($errno)";
   		}
		else
		{
			fputs( $sock, "$a_query\n" );
		
        	while ( !feof( $sock ) )
				$ret_str .= fgets( $sock, 128 );
			
        	fclose( $sock );
    	}
	
    	return $ret_str;
	}
	
	/**
	 * @access public
	 * @static
	 */	
	function traceroute( $a_query )
	{
		$res = "";
		
		exec( "traceroute $a_query", $ret_strs );
    	$str_count = count( $ret_strs );
	
    	for ( $count = 0; $count < $str_count; $count++ )
        	$res .= "$count/$str_count" . $ret_strs[$count];
			
		return $res;
	}
	
	/**
	 * Match IP number with list of numbers with wildcard
	 * $baseIP is the current remote IP address for instance and $list is a comma-list 
	 * of IP-addresses to match with. *-wildcard allowed instead of number, plus 
	 * leaving out parts in the IP number is accepted as wildcard (eg. 192.168.*.* equals 192.168)
	 *
	 * @access public
	 * @static
	 */
	function cmpIP( $baseIP, $list )	
	{
		$IPpartsReq = explode( ".", $baseIP );
		
		if ( count( $IPpartsReq ) == 4 )	
		{
			$values = StringUtil::trimExplode( ",", $list, 1 );
			
			reset( $values );
			while ( list(,$test) = each( $values ) )	
			{
				$IPparts = explode( ".", $test );
				$yes     = 1;
				
				reset( $IPparts );
				while ( list( $index, $val ) = each( $IPparts ) )	
				{
					$val = trim( $val );
					
					if ( strcmp( $val, "*" ) && strcmp( $IPpartsReq[$index], $val ) )	
						$yes = 0;
				}
				
				if ( $yes ) 
					return true;
			}
		}
	}
	
	/**
	 * Function to convert a dotted IP Address to its corresponding IP Number.
	 *
	 * @access public
	 * @static
	 */
	function address2Number( $dotted ) 
	{
		$dotted = preg_split( "/[.]+/", $dotted );
		$ip = (double)( $dotted[0] * 16777216 ) + ( $dotted[1] * 65536 ) + ( $dotted[2] * 256 ) + ( $dotted[3] );
        
		return $ip;
	}

	/**
	 * Function to convert IP Number to its corresponding dotted IP Address.
	 *
	 * @access public
	 * @static
 	 */
    function number2Address( $number ) 
	{
        $a = ( $number / 16777216 ) % 256;
        $b = ( $number / 65536    ) % 256;
        $c = ( $number / 256      ) % 256;
        $d = ( $number ) % 256;
		
        $dotted = $a . "." . $b . "." . $c . "." . $d;
		return $dotted;
    }
	
	/**
	 * Internet hostnames must be made up of "." seperated tokens, each 
	 * which must begin with a letter, end with a number or letter, and 
	 * contain only digits, letters and hyphens. This function checks for 
	 * those conditions and returns false if any of them are not met.
	 *
	 * @access public
	 * @static
	 */
	function isHostname( $hostname )
	{   
    	$tokens = explode( ".", $hostname ); 
  
		for ( $error = $j = 0; $j < sizeof( $tokens ); $j++ )
		{
        	if ( ( !ereg( "^[a-zA-Z]", $tokens[$j] ) ) || ereg( "[^a-zA-Z0-9\-]", $tokens[$j] ) || ( !ereg( "[a-zA-Z0-9]$", $tokens[$j] ) ) )
			{ 
            	$error = 1; 
            	break; 
        	}
    	} 
  
    	return ( $error == 0 )? true : false;
	}

	/**
	 * @access public
	 * @static
	 */
    function isHostname2( $url )
	{
        //             (type of stream                         )(domain             ).(tld       )
        $reg_exp  = "/^(http:\/\/|https:\/\/|ftp:\/\/|ftps:\/\/)*([a-z]{1,}[\w-.]{0,}).([a-z]{2,6})$/i";
        
		if ( !preg_match( $reg_exp, $url, $result ) )
            return false;
        
        return true;
    }
	
	/**
	 * Returns true if the argument is a valid Solaris disk device. e.g. c0t0d0.
	 *
	 * @access public
	 * @static
	 */
	function isDevice( $device = "null" )
	{  
    	return ( ereg( "^c[0-9]+t[0-9]+d[0-9]+$", $device ) )? true : false; 
	} 

	/**
	 * @access public
	 * @static
	 */
    function isURL( $url )
	{
        //             (type of stream                         )(domain             ).(tld       )(scriptname or dirs     )
        $reg_exp  = "/^(http:\/\/|https:\/\/|ftp:\/\/|ftps:\/\/)([a-z]{1,}[\w-.]{0,}).([a-z]{2,6})(\/{1}[\w_]{1}[\/\w-&?=_%]{0,}(.{1}[\/\w-&?=_%]{0,})*)*$/i";
        
		if ( !preg_match( $reg_exp, $url, $result ) )
            return false;
        
        return true;
    }
	
	/**
	 * Validate the syntax of the given IP adress.
	 * This function splits the IP adress in 4 pieces (separated by ".")
	 * and checks for each piece if it's and integer value between 0 and 255.
	 * If all 4 parameters pass this test, the function returns true.
	 *
	 * @access public
	 * @static
	 */
    function checkIP( $ip )
    {
        $count = 0;
        $x     = explode( ".", $ip );
        $max   = count( $x );

        for ( $i = 0; $i < $max; $i++ )
		{
			if ( $x[$i] >= 0 && $x[$i] <= 255 && preg_match( "/^\d{1,3}$/", $x[$i] ) )
                $count++;
        }

        if ( $count == 4 && $max == 4 )
			return true;
		else
			return false;   
    }

	/**
	 * Returns true if the argument is a valid IP address, false otherwise. 
	 * Looks for four dot separated positive decimal integers less than or 
	 * equal to  255 (The +ve check should be redundant, but you never know.) 
	 *
	 * @access public
	 * @static
	 */
	function isIPAddress( $ip_addr = "0" )
	{
    	$ip_numbers = explode( ".", $ip_addr );
    	$ret_code   = ( sizeof( $ip_numbers ) == 4 )? true : false; 
  
 	   for ( $j = 0; $j < sizeof( $ip_numbers ); $j++ )
		{ 
        	if ( $ip_numbers[$j] > 255 || $ip_numbers[$j] < 0 || ereg( '[^0-9]', $ip_numbers[$j] ) || $ip_numbers[$j] == "" || $ret_code == false )
			{ 
            	$ret_code = false; 
            	break; 
        	}
    	} 
  
    	return $ret_code;
	}

	/**
	 * Function to determine network characteristics
  	 * $host = IP address or hostname of target host (string)
  	 * $mask = Subnet mask of host in dotted decimal (string)
  	 *
	 * returns array with
  	 * "cidr"      => host and mask in CIDR notation
  	 * "network"   => network address
  	 * "broadcast" => broadcast address
  	 *
  	 * Example: findnet("192.168.37.215","255.255.255.224")
  	 * returns:
  	 * "cidr"      => 192.168.37.215/27
  	 * "network"   => 192.168.37.192
  	 * "broadcast" => 192.168.37.223
	 *
	 * @access public
	 * @static
	 */
	function findnet( $host,$mask ) 
	{
  		$bits = strpos( decbin( ip2long( $mask ) ), "0" );
  		
		$net["cidr"]    = gethostbyname( $host ) . "/" . $bits;
  		$net["network"] = long2ip( bindec( decbin( ip2long( gethostbyname( $host ) ) ) & decbin( ip2long( $mask ) ) ) );
  		
		$binhost = str_pad( decbin( ip2long( gethostbyname( $host ) ) ), 32, "0", STR_PAD_LEFT );
  		$binmask = str_pad( decbin( ip2long( $mask ) ), 32, "0", STR_PAD_LEFT);
  
  		for ( $i = 0; $i < 32; $i++ ) 
		{
     		if ( substr( $binhost, $i, 1 ) == "1" || substr( $binmask, $i, 1 ) == "0" ) 
        		$broadcast .= "1";
     		else 
        		$broadcast .= "0";
  		}
  
  		$net["broadcast"] = long2ip( bindec( $broadcast ) );
  		return $net;
	}

	/**
	 * Returns true if the argument is a valid MAC address, false otherwise. 
	 * Looks for six colon separated positive hex numbers with values less 
	 * than or eqaul to 255 (the +ve check should be redundant, but you never know).
	 *
	 * @access public
	 * @static
	 */
	function isMacAddress( $mac_addr = "0" )
	{
    	$eth_numbers = explode( ":", $mac_addr );
    	$ret_code    = ( sizeof( $eth_numbers ) == 6 )? true : false; 
  
    	for ( $j = 0; $j < sizeof( $eth_numbers ); $j++ )
		{
        	if ( ( hexdec( $eth_numbers[$j] ) > 255 ) || ( $eth_numbers[$j] < 0 ) || ereg( "[^0-9a-fA-F]", $eth_numbers[$j] ) || ( $eth_numbers[$j] == "" ) || ( $ret_code == false ) )
			{ 
            	$ret_code = false; 
            	break;
			}
    	} 
  
		return $ret_code;
	}

	/**
	 * Matches:
 	 * xxx.xxx.xxx.xxx        (exact)
 	 * xxx.xxx.xxx.[yyy-zzz]  (range)
 	 * xxx.xxx.xxx.xxx/nn     (nn = # bits, cisco style -- i.e. /24 = class C)
 	 *
 	 * Does not match:
 	 * xxx.xxx.xxx.xx[yyy-zzz]  (range, partial octets not supported)
	 *
	 * @access public
	 * @static
	 */	
	function testip( $range, $ip ) 
	{
 		$result = 1;

 		if ( ereg( "([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)/([0-9]+)", $range, $regs ) ) 
		{
     		// perform a mask match
     		$ipl    = ip2long( $ip );
     		$rangel = ip2long( $regs[1] . "." . $regs[2] . "." . $regs[3] . "." . $regs[4] );
     		$maskl  = 0;
     
	 		for ( $i = 0; $i< 31; $i++ ) 
			{
         		if ( $i < $regs[5] - 1 ) 
             		$maskl = $maskl + pow( 2, ( 30 - $i ) );
     		}
     
	 		if ( ( $maskl & $rangel ) == ( $maskl & $ipl ) )
       			return 1;
     		else
         		return 0;
  		}
		// range based 
		else 
		{
     		$maskocts = split( "\.", $range );
     		$ipocts   = split( "\.", $ip    );
     
	 		// perform a range match
     		for ( $i = 0; $i < 4; $i++ ) 
			{
         		if ( ereg( "\[([0-9]+)\-([0-9]+)\]", $maskocts[$i], $regs ) ) 
				{
           			if ( ( $ipocts[$i] > $regs[2]) || ( $ipocts[$i] < $regs[1] ) ) 
                 		$result = 0;
         		}
         		else
         		{
             		if ( $maskocts[$i] <> $ipocts[$i] ) 
                 		$result = 0;
         		}
     		}
 		}
 
 		return $result;
	}

	/**
	 * Check if an IP is within the specified mask.
	 *
	 * @access public
	 * @static
	 */	
	function match( $network, $mask, $ip ) 
	{
   		$ip_long   = ip2long( $ip );
   		$mask_long = ip2long( $network );
   
   		// Convert mask to divider.
   		if ( is_integer( $network ) ) 
		{
       		// 212.50.13.0/27 style mask (Cisco style)
       		$divider = 2 ^ ( 32 - $mask );
   		} 
		else 
		{
       		// 212.50.13.0/255.255.255.0 style mask
       		$divider = ( 2 ^ 32 - ip2long( $mask ) );
   		}
   
   		// test is IP within specified mask
  	 	if ( floor( $ip_long / $divider ) == floor( $mask_long / $divider ) )
		{
       		// match - this IP is within specified mask
       		return true;
   		} 
		else 
		{
       		// fail - this IP is NOT within specified mask
       		return false;
   		}
	}
} // END OF NetUtil

?>
