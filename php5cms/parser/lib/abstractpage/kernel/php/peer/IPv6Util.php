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
|Authors: Alexander Merz <alexander.merz@web.de>                       |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * Static helper functions.
 *
 * @package peer
 */
 
class IPv6Util
{
    /**
     * Uncompresses an IPv6 adress.
     * 
     * RFC 2373 allows you to compress zeros in an adress to '::'. This
     * function expects an valid IPv6 adress and expands the '::' to
     * the required zeros.
     * 
     * Example:  FF01::101	->  FF01:0:0:0:0:0:0:101
     *           ::1        ->  0:0:0:0:0:0:0:1 
     *
     * @access public
     * @static
     * @param  string $ip	a valid IPv6-adress (hex format)
     * @return string	    the uncompressed IPv6-adress (hex format)
	 */
    function uncompress( $ip ) 
	{
        if ( strstr( $ip, '::' ) ) 
		{
            $ipComp = str_replace( '::', ':', $ip );
			
            if ( ':' == $ipComp{0} )
                $ipComp = substr( $ipComp, 1 );

            $ipParts = count( explode( ':', $ipComp ) );
			
            if ( strstr( $ip, '.' ) )
                $ipParts++;

            $ipMiss = "";
			
            for ( $i = 0; ( 8 - $ipParts ) > $i; $i++ )
                $ipMiss = $ipMiss . '0:';
            
            if ( 0 != strpos($ip, '::') )
                $ipMiss = ':' . $ipMiss;

            $ip = str_replace( '::', $ipMiss, $ip );
        }

        return $ip;		
    }

    /**
     * Compresses an IPv6 adress.
     * 
     * RFC 2373 allows you to compress zeros in an adress to '::'. This
     * function expects an valid IPv6 adress and compresses successive zeros
     * to '::'
     * 
     * Example:  FF01:0:0:0:0:0:0:101 	-> FF01::101
     *           0:0:0:0:0:0:0:1        -> ::1 
     *
     * @access public
     * @static	
     * @param  string $ip	a valid IPv6-adress (hex format)
     * @return string	    the compressed IPv6-adress (hex format)	
     */
    function compress( $ip )
	{
        if ( !strstr( $ip, "::" ) ) 
		{
            $ipPart = explode( ":", $ip );
            $ipComp = "";
            $flag   = true;
            
			for ( $i = 0; $i < count( $ipPart ); $i++ )
			{
                if ( !$ipPart[$i] && !$ipPart[$i + 1] )
                    break;
                else
                    $ipComp = $ipComp . $ipPart[$i] . ":";
            }
			
            $ipComp = substr( $ipComp, 0, -1 );
			
            for ( ; $i < count( $ipPart ); $i++ ) 
			{
                if( $flag ) 
				{
                    $flag   = !$flag;
                    $ipComp = $ipComp . "::";
                }
				
                if ( 0 != $ipPart[$i] )
                    break;
            }

            for ( ; $i < count( $ipPart ); $i++ )
                $ipComp = $ipComp . $ipPart[$i] . ":";
        }
		
        if ( '::' == substr( $ipCom, strlen( $ipcom ) - 2 ) )
            $ip = substr( $ipComp, 0, -1 );
        else
            $ip = $ipComp;
        
        return $ip;

    }

    /**
     * Splits an IPv6 adress into the IPv6 and a possible IPv4 part.
     *
     * RFC 2373 allows you to note the last two parts of an IPv6 adress as
     * an IPv4 compatible adress.
     *
     * Example:  0:0:0:0:0:0:13.1.68.3
     *           0:0:0:0:0:FFFF:129.144.52.38
     *
     * @access public
     * @static
     * @param string $ip	a valid IPv6-adress (hex format)
     * @return array		[0] contains the IPv6 part, [1] the IPv4 part (hex format)
     */
    function splitV64( $ip )
	{
        $ip = IPv6Util::uncompress( $ip );
		
        if ( strstr( $ip, '.' ) ) 
		{
            $pos = strrpos( $ip, ':' );
            $ip{$pos} = '_';
            $ipPart = explode( '_', $ip );

            return $ipPart;
        } 
		else 
		{
            return array( $ip, "" );
        }
    }

    /**
     * Checks an IPv6 adress.
     *
     * Checks if the given IP is IPv6-compatible.
     *
     * @access public
     * @static
     * @param  string  $ip	a valid IPv6-adress
     * @return boolean	    true if $ip is an IPv6 adress
     */
    function checkIPv6( $ip )
	{
        $ipPart = IPv6Util::splitV64( $ip );
        $count  = 0;
		
        if ( !empty( $ipPart[0] ) )
		{
            $ipv6 = explode( ':', $ipPart[0] );
			
            for ( $i = 0; $i < count( $ipv6 ); $i++ ) 
			{
                $dec = hexdec( $ipv6[$i] );
				
                if ( $ipv6[$i] >= 0 && $dec <= 65535 && $ipv6[$i] == strtoupper( dechex( $dec ) ) )
                    $count++;
            }
			
            if ( 8 == $count ) 
			{
                return true;
            } 
			else if ( 6 == $count && !empty( $ipPart[1] ) ) 
			{
                $ipv4  = explode( '.', $ipPart[1] );
                $count = 0;
				
                for ( $i = 0; $i < count( $ipv4 ); $i++ ) 
				{
                    if ( $ipv4[$i] >= 0 && (int)$ipv4[$i] <= 255 && preg_match( "/^\d{1,3}$/", $ipv4[$i] ) )
                        $count++;
                }
				
                if ( 4 == $count )
                    return true;
            } 
			else 
			{
                return false;
            }
        } 
		else 
		{
            return false;
        }
    }
} // END OF IPv6Util

?>
