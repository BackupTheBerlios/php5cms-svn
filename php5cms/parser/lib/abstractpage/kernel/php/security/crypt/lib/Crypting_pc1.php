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


using( 'security.crypt.lib.Crypting' );


/**
 * PC1 is 100% compatible with RC4.
 *
 * @package security_crypt_lib
 */

class Crypting_pc1 extends Crypting
{
	/**
	 * Indicates if algorithm is reversible.
	 *
	 * @var	   boolean
	 * @access private
	 */
	var $_is_reversible = true;

	
	/**
	 * Constructor
	 *
	 * @param array
	 * 		o key        string
	 *
	 * @access public
	 */
	function Crypting_pc1( $options = array() )
	{
		$this->Crypting( $options );
		
		if ( isset( $options['key'] ) )
			$this->setKey( $options['key'] );
	}
	
	
	/**
	 * Encrypt text.
	 *
	 * @param  string $plaintext
	 * @param  array  $params
	 * @return string
	 * @access public
	 */
	function encrypt( $plaintext, $params = array() )
	{
		if ( empty( $params['key'] ) )
			$params['key'] = $this->getKey();
			
		$keys[] = '';
		$box[]  = '';

		$key_length = strlen( $params['key'] );
		$plaintext_length = strlen( $plaintext );

		for ( $i = 0; $i < 256; $i++ )
		{
			$keys[$i] = ord( $params['key'][$i % $key_length] );
			$box[$i]  = $i;
		}

		for ( $j = $i = 0; $i < 256; $i++ )
		{
			$j = ( $j + $box[$i] + $keys[$i] ) % 256;
			$box[$i] ^= $box[$j];
			$box[$j] ^= $box[$i];
			$box[$i] ^= $box[$j];
		}

		for ( $a = $j = $i = 0; $i < $plaintext_length; $i++ )
		{
			$a = ( $a + 1 ) % 256;
			$j = ( $j + $box[$a] ) % 256;

			$box[$a] ^= $box[$j];
			$box[$j] ^= $box[$a];
			$box[$a] ^= $box[$j];

			$k = $box[( ( $box[$a] + $box[$j] ) % 256 )];
			$cipher .= chr( ord( $plaintext[$i]) ^ $k );
		}

		return $cipher;
	}
	
	/**
	 * Decrypt text.
	 *
	 * @param  string $ciphertext
	 * @param  array  $params
	 * @return string
	 * @access public
	 */
	function decrypt( $ciphertext, $params = array() )
	{
		if ( empty( $params['key'] ) )
			$params['key'] = $this->getKey();
			
		return $this->encrypt( $ciphertext, $params );
	}
} // END OF Crypting_pc1

?>
