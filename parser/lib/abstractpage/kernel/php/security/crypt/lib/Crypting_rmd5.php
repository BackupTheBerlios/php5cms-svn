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
 * RMD5 Class (Reversable Message Digest)
 * This is no public standard!
 *
 * @package security_crypt_lib
 */
 
class Crypting_rmd5 extends Crypting
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
	 * @access public
	 */
	function Crypting_rmd5( $options = array() )
	{
		$this->Crypting( $options );
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
			
		srand( (double)microtime() * 1000000 );
		$encrypt_key = md5( rand( 0, 32000 ) );
		$ctr = 0;
		$tmp = "";

		for ( $i = 0; $i < strlen( $plaintext ); $i++ )
		{
			if ( $ctr == strlen( $encrypt_key ) )
				$ctr = 0;
				
			$tmp .= substr( $encrypt_key, $ctr, 1 ) . ( substr( $plaintext, $i, 1 ) ^ substr( $encrypt_key, $ctr, 1 ) );
			$ctr++;
		}

		return $this->_ed( $tmp, $params['key'] );
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
			
		$ciphertext = $this->_ed( $ciphertext, $params['key'] );
		$tmp = "";

		for ( $i = 0; $i < strlen( $ciphertext ); $i++ )
		{
			$md5 = substr( $ciphertext, $i, 1 );
			$i++;
			$tmp .= ( substr( $ciphertext, $i, 1 ) ^ $md5 );
		}

		return $tmp;
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _ed( $txt, $encrypt_key )
	{
		$encrypt_key = md5( $encrypt_key );
		$ctr = 0;
		$tmp = "";

		for ( $i = 0; $i < strlen( $txt ); $i++ )
		{
			if ( $ctr == strlen( $encrypt_key ) )
				$ctr = 0;
				
			$tmp.= substr( $txt, $i, 1 ) ^ substr( $encrypt_key, $ctr, 1 );
			$ctr++;
		}

		return $tmp;
	}
} // END OF Crypting_rmd5

?>
