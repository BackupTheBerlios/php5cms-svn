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
 * Encodes/decodes for quoted printable data.
 *
 * @package  util_text_encoding_data_lib
 */

class Encode_quotedprintable extends Encode
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encode_quotedprintable( $params = array() )
	{
		$this->Encode( $params );
	}
	
	
    /**
     * Encode string.
     *
     * @param   string  $str
     * @param   string  $charset
     * @return  string
     */
    function encode( $str, $charset = 'iso-8859-1' ) 
	{ 
      	$r = array( ' ' => '_' );
      
	  	foreach ( QuotedPrintable::_getCharsToEncode() as $i )
        	$r[chr( $i )] = '=' . strtoupper( dechex( $i ) );
      
      	return sprintf( '=?%s?Q?%s?=', $charset, strtr( $str, $r ) );
    }
    
    /**
     * Decode QuotedPrintable encoded data.
     *
     * @param   string  $str
     * @return  string
     */
    function decode( $str ) 
	{ 
      	return strtr( quoted_printable_decode( $str ), '_', ' ' );
    }
	
	
	// private methods
	
    /**
     * Get ASCII values of characters that need to be encoded.
     *
     * Note: According to RFC 2045, the "@" need not be escaped
     * Exim has its problems though if an "@" sign appears in an 
     * name (even if it's encoded), such as:
     *
     * @access  private
     * @return  array
     */
    function _getCharsToEncode()
	{
      	return array_merge( array( 64, 61, 46 ), range( 0, 31 ), range( 127, 255 ) );
    }
} // END OF Encode_quotedprintable

?>
