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
using( 'security.checksum.MD4' );


/**
 * @package security_checksum_lib
 */
 
class Checksum_md4 extends Checksum
{
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	function Checksum_md4( $value = '' )
	{
		$this->Checksum( $value );
	}
	
	
    /**
     * Create a new checksum from a string.
     *
     * @access  public
     * @param   string str
     * @return  Checksum_md4
     */
    function &fromString( $str ) 
	{
		$cmd4 = new MD4;
		$hash = bin2hex( $cmd4->hash( $str ) );
		
      	return new Checksum_md4( $hash );
    }

    /**
     * Create a new checksum from a file.
     *
     * @access  public
     * @param   file
     * @return  Checksum_md4
     */
    function &fromFile( $file ) 
	{
		$data = Checksum::_getFile( $file );
      	return Checksum_md4::fromString( $data );
    }
} // END OF Checksum_md4

?>
