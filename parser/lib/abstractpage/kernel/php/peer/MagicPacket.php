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
 * PHP Class for UDP Magic Packet Wake-on-Lan        
 *
 * This class exports 2 functions you would most probably want to use:                             
 *
 * Usage:
 * $wol = new MagicPacket();                  
 * $wol -> wake( '192.168.1.201','001122334455', 9 );
 *
 * @package peer
 */

class MagicPacket extends PEAR
{
	/**
	 * Constructor
	 *
	 * @access public
	 */ 
    function MagicPacket() 
    { 
        $this->$ff = chr( 0xFF ); 
    } 

	
	/**
	 * @access public
	 */
    function wake( $ip, $mac, $port ) 
    { 
        $this->$nic = fsockopen( "udp://" . $ip, $port );
		 
        if ( !$this->$nic ) 
        { 
            fclose( $this->$nic ); 
            return false; 
        } 
        else 
        { 
            fwrite( $this->$nic, $this->generate_magic_packet( $mac ) ); 
            fclose( $this->$nic ); 
            
			return true; 
        } 
    } 

	/**
	 * @access public
	 */
    function generate_magic_packet( $dest_mac ) 
    { 
        $packet = ""; 
        
		for ( $i = 0; $i < 6; $i++ ) 
			$packet .= $this->$ff; 
        
        for ( $i = 0; $i < 6; $i++ ) 
        	$packet .= chr( (int)substr( $dest_mac, $i, $i + 2 ) ); 
        
        return $packet; 
    } 
} // END OF MagicPacket

?>
