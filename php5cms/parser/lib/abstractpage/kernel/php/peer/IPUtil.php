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
 * Static helper functions.
 *
 * @package peer
 */
 
class IPUtil extends PEAR
{	
	/**
	 * @access public
	 * @static
	 */
	function is_ip( $ip, $strict = false )
	{
		$valid_ip = preg_match( "/^([01]?\d\d?|2[0-4]\d|25[0-4])\.([01]?\d\d?|2[0-4]\d|25[0-4])\.([01]?\d\d?|2[0-4]\d|25[0-4])\.([01]?\d\d?|2[0-4]\d|25[0-4])$/", $ip );
		
		if ( $valid_ip && $strict )
		{
			$bad = eregi_replace( "([0-9\.]+)", "", $ip );

			if ( !empty( $bad ) )
				return false;
		
			$chunks = explode( ".", $ip );
			$count  = count( $chunks );

			while ( list( $key, $val ) = each( $chunks ) )
			{
				// invalid ip segment
				if ( ereg( "^0", $val ) )
					return false;
			
				$num = $val;
				settype( $num, "integer" );
			
				if ( $num > 255 )
					return false;
			}

			return true;	
		}
		else
		{
			return $valid_ip;
		}
	}
	
	/**
	 * @access public
	 * @static
	 */
	function get_real( $ip_address )
	{
		$dotted = preg_split( "/[.]+/", $ip_address );
		$ip = (double)( $dotted[0] * 16777216 ) + ( $dotted[1] * 65536 ) + ( $dotted[2] * 256 ) + ( $dotted[3] );

		return $ip;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function ip_check( $ip_chk )
	{ 
		$parts = explode( ".", $ip_chk ); 

		if ( IPUtil::is_ip( $ip_chk ) ) 
		{ 
			if ( $parts[0] == 0 || $parts[0] >= 255 ||$parts[1] >= 255 || $parts[2] >= 255 || $parts[3] >= 255 )
				$ip_state = "0"; 
			else if ( $parts[0] < 255 && $parts[1] < 255 && $parts[2] < 255 && $parts[3] < 255 )
				$ip_state = "1"; 
		} 
		else 
		{ 
			$ip_state = 0; 
		} 

		return $ip_state; 
	} 

	/**
	 * @access public
	 * @static
	 */
	function net_check( $ip_2_chk )
	{ 
		$parts = explode( ".", $ip_2_chk );

		if ( $parts[0] == 10 )
			$network = "internal"; 
		else if ( $parts[0] == 172 && $parts[1] >= 16 || $parts[1] <= 31 ) 
			$network = "internal";  
		else if ( $parts[0] == 192 && $parts[1] == 168 ) 
			$network = "internal"; 
		else
			$network = "external";

		return $network; 
	}
	
	/**
	 * IP Pattern Matcher. Compare two IP addresses.
	 *
	 * Matches: 
	 * 
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
	function test_ip( $range, $ip ) 
	{
 		$result = true; 

	 	if ( ereg( "([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)/([0-9]+)", $range, $regs ) ) 
		{ 
     		// perform a mask match 
     		$ipl    = ip2long( $ip ); 
     		$rangel = ip2long( $regs[1] . "." . $regs[2] . "." . $regs[3] . "." . $regs[4] ); 

    		$maskl = 0; 

     		for ( $i = 0; $i < 31; $i++ ) 
			{ 
        		if ( $i < $regs[5] - 1 ) 
             		$maskl = $maskl + pow( 2, ( 30 - $i ) ); 
     		} 

     		if ( ( $maskl & $rangel ) == ( $maskl & $ipl ) ) 
         		return true; 
			else 
         		return false; 
  		} 
		else 
		{ 
    		// range based 
     		$maskocts = split( "\.", $range ); 
   			$ipocts   = split( "\.", $ip ); 

     		// perform a range match 
     		for ( $i = 0; $i < 4; $i++ ) 
			{ 
         		if ( ereg( "\[([0-9]+)\-([0-9]+)\]", $maskocts[$i], $regs ) ) 
				{ 
         			if ( ( $ipocts[$i] > $regs[2] ) || ( $ipocts[$i] < $regs[1] ) ) 
                 		$result = false;
        		} 
         		else 
         		{ 
             		if ( $maskocts[$i] <> $ipocts[$i] ) 
                 		$result = false; 
         		} 
     		} 
 		} 
 
 		return $result; 
	}
	
	/**
	 * Function to check if an IP is within the specified mask.
	 *
	 * @access public
	 * @static
	 */
	function match( $network, $mask, $ip ) 
	{
		bcscale( 3 );
		
		$ip_long   = ip2long( $ip );
		$mask_long = ip2long( $network );

 		// convert mask to divider
 		if ( ereg( "^[0-9]+$", $mask ) ) 
		{
 			// 212.50.13.0/27 style mask (Cisco style)
  			$divider = bcpow( 2, ( 32 - $mask ) );
 		} 
		else 
		{
 			// 212.50.13.0/255.255.255.0 style mask
   			$xmask = ip2long( $mask );
  
  			if ( $xmask < 0 ) 
				$xmask = bcadd( bcpow( 2, 32 ), $xmask );
  
  			$divider = bcsub( bcpow( 2, 32 ), $xmask );
 		}
 
 		// test is IP within specified mask
 		if ( floor( bcdiv( $ip_long, $divider ) ) == floor( bcdiv( $mask_long, $divider ) ) )
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
	
	/**
	 * Determine network characteristics.
	 *
	 * $host = IP address or hostname of target host (string) 
	 * $mask = Subnet mask of host in dotted decimal (string) 
	 * returns array with 
	 * 		"cidr"      => host and mask in CIDR notation 
	 * 		"network"   => network address 
	 * 		"broadcast" => broadcast address 
	 * 
	 * Example: find_net( "192.168.37.215", "255.255.255.224" ) 
	 * returns: 
	 * 		"cidr"      => 192.168.37.215/27 
	 * 		"network"   => 192.168.37.192 
	 * 		"broadcast" => 192.168.37.223 
  	 *
	 * @access public
	 * @static
	 */
	function find_net( $host, $mask )
	{ 
  		$bits = strpos( decbin( ip2long( $mask ) ), "0" );
		 
  		$net["cidr"] = gethostbyname( $host ) . "/" . $bits; 
		$net["network"] = long2ip( bindec( decbin( ip2long( gethostbyname( $host ) ) ) & decbin( ip2long( $mask ) ) ) ); 

  		$binhost = str_pad( decbin( ip2long( gethostbyname( $host ) ) ), 32, "0", STR_PAD_LEFT ); 
		$binmask = str_pad( decbin( ip2long( $mask ) ), 32, "0", STR_PAD_LEFT ); 
 
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
	 * @access public
	 * @static
	 */    
    function client_ip()
    {
        if ( getenv( "HTTP_X_FORWARDED_FOR" ) != '' )
        {
            $client_ip = $_SERVER["REMOTE_ADDR"];

            if ( preg_match( "/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", getenv( 'HTTP_X_FORWARDED_FOR' ), $ip_list ) )
            {
                $private_ip = array( '/^0\./', '/^127\.0\.0\.1/', '/^192\.168\..*/', '/^172\.16\..*/', '/^10.\.*/', '/^224.\.*/', '/^240.\.*/' );
                $client_ip  = preg_replace( $private_ip, $client_ip, $ip_list[1] );
            }
        }
        else
        {
            $client_ip = $_SERVER["REMOTE_ADDR"];
        }
        
        return $client_ip;
    }
	
	/**
	 * Check if a given ip is valid.
	 *
	 * @access public
	 * @static
	 */
	function valid_ip( $ip )
	{
 		if ( !is_string( $ip ) )
   			return false;

		$ip_long    = ip2long( $ip );
 		$ip_reverse = long2ip( $ip_long );
  
  		if ( $ip == $ip_reverse )
     		return true;
   		else
     		return false;
	}
	
	/**
	 * Check if ip is a valid public ipv4 address.
	 *
	 * @access public
	 * @static
	 */
	function valid_ipv4( $ip ) 
	{
      	if ( !is_string( $ip ) )
			return false;

		$ip_long    = ip2long( $ip );
		$ip_reverse = long2ip( $ip_long );

       	if ( $ip != $ip_reverse )
			return false;

		// reserved IANA IPv4 addresses
		// http://www.iana.org/assignments/ipv4-address-space

		$reserved_ips = array (
			array( '10.0.0.0',    '10.255.255.255'  ),
			array( '127.0.0.0',   '127.255.255.255' ),
			array( '176.16.0.0',  '176.16.255.255'  ),
			array( '192.168.0.0', '192.168.255.255' ),
			array( '0.0.0.0',     '2.255.255.255'   ),
			array( '23.0.0.0',    '23.255.255.255'  ),
			array( '27.0.0.0',    '27.255.255.255'  ),
			array( '31.0.0.0',    '31.255.255.255'  ),
			array( '36.0.0.0',    '37.255.255.255'  ),
			array( '39.0.0.0',    '39.255.255.255'  ),
			array( '41.0.0.0',    '42.255.255.255'  ),
			array( '58.0.0.0',    '60.255.255.255'  ),
			array( '82.0.0.0',    '95.255.255.255'  ),
			array( '96.0.0.0',    '126.255.255.255' ),
			array( '169.254.0.0', '169.254.255.255' ),
			array( '192.0.2.0',   '192.0.2.255'     ),
			array( '197.0.0.0',   '197.255.255.255' ),
			array( '221.0.0.0',   '223.255.255.255' ),
			array( '240.0.0.0',   '255.255.255.255' )
		);

       	foreach ( $reserved_ips as $r )
       	{
			$min = ip2long( $r[0] );
			$max = ip2long( $r[1] );

			if ( ( $ip_long >= $min ) && ( $ip_long <= $max ) )
				return false;
		}

		return true;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function up_to_int( $ipstring ) 
	{
 		$a = explode( ".", $ipstring );
 		
		for ( $i = 0; $i < 3; $i++ ) 
		{
  			if ( $a[$i] < 16 )
       			$hex .= "0" . dechex( $a[$i] );
    		else
       			$hex .= dechex( $a[$i] );
 		}
 
 		// made the hex, now to decimal and times 100 hex (=256, or shift left 8 bits), then add the last bit
 		$dec = ( hexdec( $hex ) << 8 ) + $a[3];
 		return $dec;
	}
	
	/**
	 * Expands a CIDR notation address into an array of address ranges.
	 *
	 * Example:
	 * $ip_range = expand_cidr( "111.111.111.10/28" );
	 * print_r( $ip_range );
	 *
	 * @access public
	 * @static
	 */
	function expand_cidr( $ip )
	{
        // validate IP address
        $num   = "([0-9]|1?\d\d|2[0-4]\d|25[0-5])";
        $range = "([1-9]|1\d|2\d|3[0-2])";

		/*
        if ( preg_match( "/^$num\.$num\.$num\.$num\/$range$/", $ip ) )
			return false;
		*/
		
        // Separate CIDR structure into network-IP and netmask
        $ip_arr = explode( "/", $ip );

        // Calculate number of hosts in the subnet
        $mask_bits = $ip_arr[1];
        
		if ( $mask_bits > 31 || $mask_bits < 0 ) 
			return PEAR::raiseError( "Invalid mask." );

        $host_bits = 32 - $mask_bits;
        $num_hosts = pow( 2, $host_bits ) - 1;

        // Netmask in decimal for use later: (hack around PHP always using signed ints)
        $netmask = ip2long( "255.255.255.255" ) - $num_hosts;

        // Calculate start and end
        // Store IP-addresses internally as longs, to ease compare of two
        // addresses in a sorted structure.
        $ip_start = ip2long( $ip_arr[0] );
		
        if ( $ip_start != ( $ip_start & $netmask ) )
			return PEAR::raiseError( "Address $ip not on network boundary." );
			
        $ip_end = $ip_start + $num_hosts;

        for ( $i = 0; $i <= $num_hosts; $i++ )
			$ip_range[] = long2ip( $ip_start + $i );
        
        return $ip_range;
	}
	
	/**
	 * Get the interface of an IP, based on the local route table, use this.
	 *
	 * @access public
	 * @static
	 */
	function get_interface_for_ip( $user_ip )
	{
    	$route = "/bin/netstat -rn";
    	exec( $route, $aoutput );
    
		foreach ( $aoutput as $key => $line )
    	{
        	if ( $key > 1 )
        	{
            	$line = ereg_replace( "[[:space:]]+", ",", $line );
            	list( $network, $gateway, $mask, $flags, $mss, $window, $irtt, $iface ) = explode( ",", $line );
            
				if ( ( ip2long( $user_ip ) & ip2long( $mask ) ) == ip2long( $network ) )
                	return $iface;
        	}
    	}
	}
	
	
	// binary methods
	
	/*
	 * For use with ip octets. Converts to binary from an ip octct, and visa versa, 
	 * and includes add and subtract binary octet.
 	 *
	 * Basically, if you are going to use this, you prolly know about what this 
	 * does in the first place, but for thoes who don't already know heres a 
	 * brief explanation.
	 *
	 * When working with IP addresses (127.0.0.1), and Subnet Masks 
	 * (255.255.255.0), etc, to calculate each you will need beautiful binary to 
	 * do it. Here is the makeup of bits for one Octet of and ip address (Octet 
	 * is like: octet1.octet2.octet3.octet4 (0.0.0.0, each zero is an octet):
	 * 00000000 = 0
	 * 11111111 = 255
	 *
	 * By now you are wondering if I am on crack. Maybe. So anways, to get that 
	 * 255, heres how it works:
	 * Each bit (single-number in that binary number) represents a number. 8th 
	 * bit equals 1. From there up (or to the left), you take the last number and 
	 * times by 2...so in order of left to right:
	 * 128, 64, 32, 16, 8, 4, 2, 1
	 *
	 * The final binary number (11111111) is gotten by adding each bit's value 
	 * together to get the final number going from left to right. So in the 
	 * instance of the number 255, add each number up, and which numbers you add 
	 * are written as a 1 in the binary number...so you will have something like 
	 * this:
	 * 00000000 = 0
	 * 00000001 = 1
	 * 00001010 = 10 (8+2 = 10)
	 * 10000000 = 128, and so on and soo forth up till:
	 * 11111111 = 255
	 */

	/**
	 * Returns array of the bit values (first bit = 128, etc...).
	 *
	 * @access public
	 * @static
	 */
	function loadipbits()
	{
		// Load numbers to $binbuf
		// global $binbuf;

		$i = 0;
		while ( $i != 8 ) 
		{
			if ( $i == 0 )
				$binbuf[$i] = 128;
			else
				$binbuf[$i] = $binbuf[$i - 1] / 2;

			$i++;
		}

		return $binbuf;
	}
	
	/**
	 * Returns normal octet from binary octet.
	 *
	 * @access public
	 * @static
	 */
	function frombinary( $binary_in ) 
	{
		$binbuf = IPUtil::loadipbits();

		// now comes the part to get the value of each bit
		$i = 0;
		while ( $i != 8 ) 
		{
			$bits[$i] = substr( $binary_in, $i, 1 );

			if ( $bits[$i] == 1 )
				$binfinal = $binfinal + $binbuf[$i];

			$i++;
		}

		return $binfinal;
	}

	/**
	 * Subtracts number1 from number2.
	 *
	 * @access public
	 * @static
	 */
	function subbin( $bin1, $bin2 ) 
	{
		$binbuf = IPUtil::loadipbits();

		// subtract $bin1 from $bin2
		return IPUtil::frombinary( $bin1 ) - IPUtil::frombinary( $bin2 );
	}
	
	/**
	 * Adds number1 to number2.
	 *
	 * @access public
	 * @static
	 */
	function addbin( $bin1, $bin2 ) 
	{
		$binbuf = IPUtil::loadipbits();

		// Add 2 Numbers together in binary form
		// And return in normal form
		// This can be used to return binary or normal numbers
		// By doing this:
		// $joe = IPUtil::ip_binary_tobinary( IPUtil::addbin( "00000000", "11111111" ) );
		// will return a binary number, or
		// $joe = IPUtil::addbin( "00000000", "11111111" );
		// will return a normal number
		return IPUtil::frombinary( $bin1 ) + IPUtil::frombinary( $bin2 );
	}

	/**
	 * Returns a binary octet from an octet given.
	 *
	 * @access public
	 * @static
	 */
	function tobinary( $innum ) 
	{
		$binbuf = IPUtil::loadipbits();

		// turns normal number into binary form
		$bitplace  = "";
		$bitplace2 = "";

		$i = 0;
		$binnum = 0;

		while ( $i != 8 ) 
		{
			if ( $binnum + $binbuf[$i] <= $innum ) 
			{
				$binnum = $binnum + $binbuf[$i];
				$bitplace[$i] = "1";
			} 
			else 
			{
				$bitplace[$i] = "0";
			}

			$i++;
		}

		$bitplace2 = "$bitplace[0]$bitplace[1]$bitplace[2]$bitplace[3]$bitplace[4]$bitplace[5]$bitplace[6]$bitplace[7]";
		return $bitplace2;
	}
} // END OF IPUtil

?>
