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


using( 'util.text.encoding.data.lib.Encode' );


/**
 * Encodes/decodes data with MIME base64.
 *
 * @package  util_text_encoding_data_lib
 */

class Encode_base64 extends Encode
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encode_base64( $params = array() )
	{
		$this->Encode( $params );
	}
	
	
    /**
     * Encode string.
     *
     * @param   string  $str
     * @return  string
     */
    function encode( $str ) 
	{ 
      	return base64_encode( $str );
    }
    
    /**
     * Decode base64 encoded data.
     *
     * @param   string  $str
     * @return  string
     */
    function decode( $str ) 
	{ 
      	return base64_decode( $str );
    }
} // END OF Encode_base64

?>
