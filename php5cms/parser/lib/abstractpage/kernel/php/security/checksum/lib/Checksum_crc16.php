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

 
using( 'security.checksum.lib.Checksum' );
  

/**
 * @package security_checksum_lib
 */
 
class Checksum_crc16 extends Checksum
{  
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	function Checksum_crc16( $value = '' )
	{
		$this->Checksum( $value );
	}
	
	
    /**
     * Create a new checksum from a string.
     *
     * @access  public
     * @param   string str
     * @return  Checksum_crc16
     */
    function &fromString( $str ) 
	{
      	$sum = 0xFFFF;
      
	  	for ( $x = 0, $s = strlen( $str ); $x < $s; $x++ ) 
		{
        	$sum = $sum ^ ord( $str{$x} );
        
			for ( $i = 0; $i < 8; $i++ )
          		$sum = ( 0x0001 == ( $sum & 0x0001 )? ( $sum >> 1 ) ^ 0xA001 : $sum >> 1 );
      	}
      
	  	return new Checksum_crc16( $sum );
    }

    /**
     * Create a new checksum from a file.
     *
     * @access  public
     * @param   file
     * @return  Checksum_crc16
     */
    function &fromFile( $file ) 
	{
        $data = Checksum::_getFile( $file );
      	return Checksum_crc16::fromString( $data );
    }
} // END OF Checksum_crc16

?>
