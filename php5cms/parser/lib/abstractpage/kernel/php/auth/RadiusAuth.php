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
 

define( "RADIUS_GRANTED", 2 );


/**
 * Radius Class
 *
 * @link http://www.mavetju.org/programming/php.php
 * @package auth
 */

class RadiusAuth extends Base
{
    /**
     * @access public
     */
    public function encrypt( $password, $key, $RA ) 
    {
        $keyRA       = $key . $RA;
        $md5checksum = md5( $keyRA );
        $output      = "";
    
        for ( $i = 0; $i <= 15; $i++ ) 
        {
            $m = ( 2 * $i > strlen( $md5checksum ) )? 0 : hexdec( substr( $md5checksum, 2 * $i, 2 ) );
            $k = ( $i > strlen( $keyRA    ) )? 0 : ord( substr( $keyRA,    $i, 1 ) );
            $p = ( $i > strlen( $password ) )? 0 : ord( substr( $password, $i, 1 ) );
            $c = $m ^ $p;
            $output .= chr( $c );
        }
    
        return $output;
      }

    /**
     * @access public
     */
    public function auth( $username, $password, $sharedsecret = "", $src = "", $suffix = "", $radiushost = "localhost", $radiusport = 1645 )
    {
        $nasIP = explode( ".", $src );
        $ip = gethostbyname( $radiushost );
    
        // 17 is UDP, formerly known as PROTO_UDP
        $sock   = socket_create( AF_INET,SOCK_DGRAM, 17 );
        $retval = socket_connect( $sock, $ip, $radiusport );
      
        if ( !preg_match( "/@/", $username ) )
            $username .= $suffix;
      
        $RA = pack( "CCCCCCCCCCCCCCCC", // auth code
            1 + rand()%255, 1+rand()%255, 1+rand()%255, 1+rand()%255,
            1 + rand()%255, 1+rand()%255, 1+rand()%255, 1+rand()%255,
            1 + rand()%255, 1+rand()%255, 1+rand()%255, 1+rand()%255,
            1 + rand()%255, 1+rand()%255, 1+rand()%255, 1+rand()%255 );
      
        $encryptedpassword = Radius::encrypt( $password,$sharedsecret, $RA );
      
        $length = 4 +                           // header
            16 +                                // auth code
            6  +                                // service type
            2  + strlen( $username ) +          // username
            2  + strlen( $encryptedpassword ) + // password
            6  +                                // nasIP
            6;                                  // nasPort
      
        $thisidentifier = rand() % 256;
   
        //            v   v v     v   v   v     v     v
        $data = pack( "CCCCa*CCCCCCCCa*CCa*CCCCCCCCCCCC",
            1, $thisidentifier, $length / 256, $length % 256,           // header
            $RA,                                                        // authcode
            6, 6, 0, 0, 0, 1,                                           // service
            1, 2 + strlen( $username ), $username,                      // username
            2, 2 + strlen( $encryptedpassword ), $encryptedpassword,    // password
            4, 6, $nasIP[0], $nasIP[1], $nasIP[2], $nasIP[3],           // nasIP
            5, 3, 0, 0, 0, 0                                            // nasPort
        );
      
        socket_write( $sock, $data, $length );
      
        // wait 5 seconds at most
        $sock_array = array( $sock );
        $null_var   = null;
        socket_select( $sock_array, $null_var, $null_var, 5 );
      
        $readdata = @socket_read( $sock, 1 ) || $bad_socket = true;
      
        if ( $bad_socket ) 
            return false;
      
        socket_close( $sock );
      
        return ord( $readdata );
        // 2 -> Access-Accept
        // 3 -> Access-Reject
        // See RFC2138 for this.
    }
} // END OF RadiusAuth

?>
