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
 * This encryption method is from 1586, so please don't
 * use that in production environments ;-)
 *
 * @package security_crypt_lib
 */
 
class Crypting_vigenere extends Crypting
{
	/**
	 * Indicates if algorithm is reversible.
	 *
	 * @var	   boolean
	 * @access private
	 */
	var $_is_reversible = true;

	/**
	 * @var    array
	 * @access private
	 */
    var $_square = array();
	
	/**
	 * @var    array
	 * @access private
	 */
    var $_characters = array(
		'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
		'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
		'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
		'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
		' ', ',', '.', ':', ';',"'", "\n",
		'0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
	);
						  
						  
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Crypting_vigenere( $options = array() )
	{
		$this->Crypting( $options );
		
		if ( isset( $options['key'] ) )
			$this->setKey( $options['key'] );
			
		$this->_populateSquare();
	}
	
	
	/**
	 * Encrypt text.
	 *
	 * @param  string $plaintext
	 * @param  array  $params (key)
	 * @return string
	 * @access public
	 */
	function encrypt( $plaintext, $params = array() )
	{
		if ( empty( $params['key'] ) )
			$params['key'] = $this->getKey();
			
        $encrypted_text = '';
        $plain_length   = strlen( $plaintext );

        $diff = 0;
        
		for ( $i = 0; $i < $plain_length; $i++ )
		{
            $j = $i - $diff;
            
			if ( $j == strlen( $params['key'] ) ) 
			{
                $diff = $j + $diff;
                $j = 0;
            }

            $encrypted_text .= $this->_encryptSubstitute( $plaintext[$i], $params['key'][$j] );
        }
		
        return $encrypted_text;
    }
	
	/**
	 * Decrypt text.
	 *
	 * @param  string $ciphertext
	 * @param  array  $params (key)
	 * @return string
	 * @access public
	 */
	function decrypt( $ciphertext, $params = array() )
	{
		if ( empty( $params['key'] ) )
			$params['key'] = $this->getKey();
			
        $decrypted_text = '';
        $length_crypt   = strlen( $ciphertext );
        
        $diff = 0;
        
		for ( $i = 0; $i < $length_crypt; $i++ )
		{
            $j = $i - $diff;
            
			if ( $j == strlen( $params['key'] ) ) 
			{
                $diff = $j + $diff;
                $j = 0;
            }
            
            $decrypted_text .= $this->_decryptSubstitute( $ciphertext[$i], $params['key'][$j] );
        }
		
        return $decrypted_text;
    }
	
	
	// private methods
	
    /**
	 * Populates Vigenère Square. e.g.
     *
     *    1234567
     *
     * 1  abcdABC
     * 2  bcdABCa
     * 3  cdABCab
     * 4  dABCabc
     * 5  ABCabcd
     * 6  BCabcdA
     * 7  CabcdAB
	 *
	 * @access private
	 */
    function _populateSquare()
	{
		$clear_length = count( $this->_characters );
        $new_alphabet = $this->_characters;

        for ( $i = 0; $i < $clear_length; $i++ )
		{
            $new_alphabet = $this->_arrayTurn( $new_alphabet );
            $this->_square[$i] = $new_alphabet;
        }
    }

	/**
	 * Encrypts a single character.
	 *
	 * @access private
	 */	
    function _encryptSubstitute( $letter_plain, $letter_code ) 
	{
        $bool = true;

        $i = 0;
		while ( $bool ) 
		{
            if ( $this->_square[$i][0] == $letter_code )
                $bool = false;
            else
                $i++;
        }

        $pos = array_search( $letter_plain, $this->_characters );
        return $this->_square[$i][$pos];
    }

	/**
	 * Decrypts a single character.
	 *
	 * @access private
	 */	
    function _decryptSubstitute( $letter_crypt, $letter_code ) 
	{
        $pos  = array_search( $letter_code, $this->_characters );
        $bool = true;
        
		$i = 0;
        while ( $bool ) 
		{
            if ( $this->_square[$i][$pos] == $letter_crypt )
                $bool = false;
            else
                $i++;
        }
		
        return $this->_square[$i][0];
    }
	
	/**
	 * @access private
	 */	
    function _arrayTurn( $array ) 
	{
        $new_array = array_slice( $array, 1 );
        $new_array[] = $array[0];
		
        return $new_array;
    }
} // END OF Crypting_vigenere

?>
