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
 * @package security_crypt_lib
 */
 
class Crypting_rot13 extends Crypting
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
	function Crypting_rot13( $options = array() )
	{
		$this->Crypting( $options );
	}
	
	
	/**
	 * Encrypt text.
	 *
	 * @param  string $plaintext
	 * @return string
	 * @access public
	 */
	function encrypt( $plaintext, $params = array() )
	{
		return $this->_rot13( $plaintext );
	}
	
	/**
	 * Decrypt text.
	 *
	 * @param  string $ciphertext
	 * @return string
	 * @access public
	 */
	function decrypt( $ciphertext, $params = array() )
	{
		return $this->_rot13( $ciphertext );
	}
	
	
	// private methods
	
	/**
	 * Rot13 algorithm.
	 *
	 * @param  string $text
	 * @access private
	 */
	function _rot13( $text ) 
	{
		$text_rotated = "";
		
		for ( $i = 0; $i <= strlen( $text ); $i++ ) 
		{
			$k = ord( substr( $text, $i, 1 ) );
			
			if ( $k >= 97 && $k <= 109 ) 
				$k = $k + 13;
			else if ( $k >= 110 && $k <= 122 ) 
				$k = $k - 13;
			else if ( $k >= 65 && $k <= 77 ) 
				$k = $k + 13;
			else if ( $k >= 78 && $k <= 90 ) 
				$k = $k - 13;

			$text_rotated = $text_rotated . chr( $k );
		} 

		return $text_rotated;
	}
} // END OF Crypting_rot13

?>
