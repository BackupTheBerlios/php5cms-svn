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
|Authors: Eric Kilfoil <eric@ypass.net>                                |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * Class to provide IPv4 calculations.
 *
 * Provides methods for validating IP addresses, calculating netmasks,
 * broadcast addresses, network addresses, conversion routines, etc.
 *
 * @package peer
 */

class IPv4 extends PEAR
{
	/**
	 * @access public
	 */
    var $ip = "";
	
	/**
	 * @access public
	 */
    var $bitmask = false;
	
	/**
	 * @access public
	 */
    var $netmask = "";
	
	/**
	 * @access public
	 */
    var $network = "";
	
	/**
	 * @access public
	 */
    var $broadcast = "";
	
	/**
	 * @access public
	 */
    var $long = 0;

	/**
 	 * Map of bitmasks to subnets
 	 *
 	 * This array contains every valid netmask.  The index of the dot quad
 	 * netmask value is the corresponding CIDR notation (bitmask).
 	 */
	var $netmask_map = array(
		0  => "0.0.0.0",
		1  => "128.0.0.0",
		2  => "192.0.0.0",
		3  => "224.0.0.0",
		4  => "240.0.0.0",
		5  => "248.0.0.0",
		6  => "252.0.0.0",
		7  => "254.0.0.0",
		8  => "255.0.0.0",
		9  => "255.128.0.0",
		10 => "255.192.0.0",
		11 => "255.224.0.0",
		12 => "255.240.0.0",
		13 => "255.248.0.0",
		14 => "255.252.0.0",
		15 => "255.254.0.0",
		16 => "255.255.0.0",
		17 => "255.255.128.0",
		18 => "255.255.192.0",
		19 => "255.255.224.0",
		20 => "255.255.240.0",
		21 => "255.255.248.0",
		22 => "255.255.252.0",
		23 => "255.255.254.0",
		24 => "255.255.255.0",
		25 => "255.255.255.128",
		26 => "255.255.255.192",
		27 => "255.255.255.224",
		28 => "255.255.255.240",
		29 => "255.255.255.248",
		30 => "255.255.255.252",
		31 => "255.255.255.254",
		32 => "255.255.255.255"
	);
	
	
    /**
     * Validate the syntax of the given IP adress.
     *
     * Using the PHP long2ip() and ip2long() functions, convert the IP
     * address from a string to a long and back.  If the original still
     * matches the converted IP address, it's a valid address.  This
     * function does not allow for IP addresses to be formatted as long
     * integers.
     *
     * @param  string $ip IP address in the format x.x.x.x
     * @return bool       true if syntax is valid, otherwise false
	 * @access public
     */
    function validateIP( $ip )
    {
        if ( $ip == long2ip( ip2long( $ip ) ) )
            return true;
        else
            return false;
    }

    /**
     * Validate the syntax of the given IP address (compatibility).
     *
     * This function is identical to IPv4::validateIP().  It is included
     * merely for compatibility reasons.
     *
     * @param  string $ip IP address
     * @return bool       true if syntax is valid, otherwise false
	 * @access public
     */
    function checkIP( $ip )
    {
        return ( $this->validateIP( $ip ) );
    }

    /**
     * Validate the syntax of a four octet netmask
     *
     * There are 33 valid netmask values.  This function will compare the
     * string passed as $netmask to the predefined 33 values and return
     * true or false.  This is most likely much faster than performing the
     * calculation to determine the validity of the netmask.
     *
     * @param  string $netmask Netmask
     * @return bool       true if syntax is valid, otherwise false
	 * @access public
     */
    function validateNetmask( $netmask )
    {
        if ( !in_array( $netmask, $this->netmask_map ) )
            return false;
        
        return true;
    }

    /**
     * Parse a formatted IP address.
     *
     * Given a network qualified IP address, attempt to parse out the parts
     * and calculate qualities of the address.
     *
     * The following formats are possible:
     *
     * [dot quad ip]/[ bitmask ]
     * [dot quad ip]/[ dot quad netmask ]
     * [dot quad ip]/[ hex string netmask ]
     *
     * The first would be [IP Address]/[BitMask]:
     * 192.168.0.0/16
     *
     * The second would be [IP Address] [Subnet Mask in quad dot notation]:
     * 192.168.0.0/255.255.0.0
     *
     * The third would be [IP Address] [Subnet Mask as Hex string]
     * 192.168.0.0/ffff0000
     *
     * @param  string $ip IP address netmask combination
     * @return object     true if syntax is valid, otherwise false
	 * @access public
     */
    function parseAddress( $address )
    {
        $myself = new IPv4;
        
		if ( strchr( $address, "/" ) ) 
		{
            $parts = explode( "/", $address );
			
            if ( !$myself->validateIP( $parts[0] ) )
                return PEAR::raiseError( "Invalid IP address." );
            
            $myself->ip = $parts[0];

            // Check the style of netmask that was entered.
            
			// a hexadecimal string was entered
            if ( eregi( "^([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$", $parts[1], $regs ) ) 
			{
                // hexadecimal string
                $myself->netmask = hexdec( $regs[1] ) . "." .  hexdec( $regs[2] ) . "." . hexdec( $regs[3] ) . "." .  hexdec( $regs[4] );
            } 
			// a standard dot quad netmask was entered
			else if ( strchr( $parts[1], "." ) ) 
			{
                if ( !$myself->validateNetmask( $parts[1] ) )
                    return PEAR::raiseError( "Invalid netmask value." );
                
                $myself->netmask = $parts[1];
            } 
			// a CIDR bitmask type was entered
			else if ( $parts[1] > 0 && $parts[1] <= 32 ) 
			{
                // bitmask was entered
                $myself->bitmask = $parts[1];
            } 
			// some unknown format of netmask was entered
			else 
			{
                return PEAR::raiseError( "Invalid netmask value." );
            }
			
            $myself->calculate();
            return ( $myself );
        } 
		else if ( $myself->validateIP( $address ) ) 
		{
            $myself->ip = $address;
            return ( $myself );
        } 
		else 
		{
            return PEAR::raiseError( "Invalid IP address." );
        }
    }
    
    /**
     * Calculates network information based on an IP address and netmask.
     *
     * Fully populates the object properties based on the IP address and
     * netmask/bitmask properties.  Once these two fields are populated,
     * calculate() will perform calculations to determine the network and
     * broadcast address of the network.
     *
     * @return mixed     true if no errors occured, otherwise Error object
	 * @access public
     */
    function calculate()
	{
        $validNM = $this->netmask_map;

        if ( !is_a( $this, "ipv4" ) ) 
		{
            $myself = new IPv4;
            return PEAR::raiseError( "Cannot calculate on uninstantiated IPv4 class." );
        }

        // Find out if we were given an ip address in dot quad notation or
        // a network long ip address.  Whichever was given, populate the
        // other field.
        if ( strlen( $this->ip ) ) 
		{
            if ( !$this->validateIP( $this->ip ) )
                return PEAR::raiseError( "Invalid IP address." );
            
            $this->long = ip2long( $this->ip );
        } 
		else if ( is_numeric( $this->long ) ) 
		{
            $this->ip = long2ip( $this->long );
        } 
		else 
		{
           return PEAR::raiseError( "IP address not specified." );
        }

		// Check to see if we were supplied with a bitmask or a netmask.
		// Populate the other field as needed.
        if ( strlen( $this->bitmask ) ) 
		{
            $this->netmask = $validNM[$this->bitmask];
        } 
		else if ( strlen( $this->netmask ) ) 
		{
            $validNM_rev   = array_flip( $validNM );
            $this->bitmask = $validNM_rev[$this->netmask];
        } 
		else 
		{
            return PEAR::raiseError( "Netmask or bitmask are required for calculation." );
        }
		
        $this->network   = long2ip( ip2long( $this->ip ) & ip2long( $this->netmask ) );
        $this->broadcast = long2ip( ip2long( $this->ip ) | ( ip2long( $this->netmask ) ^ ip2long( "255.255.255.255" ) ) );
        
		return true;
    }

    /**
     * Converts a dot-quad formmated IP address into a hexadecimal string.
	 *
	 * @access public
     */
    function atoh( $addr )
    {
        if ( !IPv4::validateIP( $addr ) )
            return false;
        
        $ap = explode( ".", $addr );
        return ( sprintf( "%02x%02x%02x%02x", $ap[0], $ap[1], $ap[2], $ap[3] ) );
    }

    /**
     * Converts a hexadecimal string into a dot-quad formatted IP address.
	 *
	 * @access public
     */
    function htoa( $addr )
    {
        if ( eregi( "^([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$", $addr, $regs ) ) 
			return ( hexdec($regs[1] ) . "." .  hexdec( $regs[2] ) . "." . hexdec( $regs[3] ) . "." .  hexdec( $regs[4] ) );
        
        return false;
    }
} // END OF IPv4

?>
