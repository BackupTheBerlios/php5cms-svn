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
 * Encodes/decodes uuencode.
 *
 * @link     http://foldoc.hld.c64.org/foldoc.cgi?uuencode
 * @link     http://www.opengroup.org/onlinepubs/007908799/xcu/uuencode.html
 * @package  util_text_encoding_data_lib
 */

class Encode_uucode extends Encode
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encode_uucode( $params = array() )
	{
		$this->Encode( $params );
	}
	
	
	/**
     * Encode string.
     *
     * @access  public
     * @param   string  $str
     * @return  string
	 * @static
     */
	function encode( $str )
	{
		$out = '';
		$offset = 0;
		
		while ( $chunk = substr( $str, $offset, 0x2D ) )
		{
        	$out .= chr( ( strlen( $chunk ) & 0x3F ) + 0x20 );
			
			for ( $i = 0, $s = strlen( $chunk ); $i < $s; $i += 3 )
			{
				$out.= strtr( 
					chr( ( ( ord( $chunk{$i} ) >> 2 ) & 0x3F ) + 0x20 ) .
					chr( ( ( ( ( ord( $chunk{$i} ) << 4 ) & 0x30 ) | ( ( ord( $chunk{$i + 1} ) >> 4 ) & 0x0F ) ) & 0x3F ) + 0x20 ) .
					chr( ( ( ( ( ord( $chunk{$i + 1} ) << 2 ) & 0x3C ) | ( ( ord( $chunk{$i + 2} ) >> 6 ) & 0x03 ) ) & 0x3F ) + 0x20 ) .
					chr( ( ( ord( $chunk{$i + 2} ) & 0x3F ) & 0x3F ) + 0x20 ),
					' ', '`'
				);
			}
			
			$out .= "\n";
			$offset += 0x2D;
		}
		
		return $out.'`';
	}    
    
    /**
     * Decode uuencoded data.
     *
     * @access  public
     * @param   string  $str
     * @return  string
	 * @static
     */
    function decode( $str )
	{
		$chunk = strtok( $str, "\n" );
		$out = '';
		
		do
		{
        	if ( '`' == $chunk{0} )
				break;
        	
			for ( $i = 1, $s = strlen( $chunk ); $i < $s; $i += 4 ) 
			{
          		$out .= (
					chr( ( ( ( ord( $chunk{$i}) - 0x20 ) & 0x3F ) << 2 ) | ( ( ( ord( $chunk{$i + 1} ) - 0x20 ) & 0x3F ) >> 4 ) ) .
					chr( ( ( ( ord( $chunk{$i + 1} ) - 0x20 ) & 0x3F ) << 4 ) | ( ( ( ord( $chunk{$i + 2} ) - 0x20 ) & 0x3F ) >> 2 ) ) .
					chr( ( ( ( ord( $chunk{$i + 2} ) - 0x20 ) & 0x3F ) << 6 ) | ( ( ord( $chunk{$i + 3} ) - 0x20 ) & 0x3F ) )
				);
			}
		} while ( $chunk = strtok( "\n" ) );

		return rtrim( $out, "\0" );
	}
} // END OF Encode_uucode

?>
