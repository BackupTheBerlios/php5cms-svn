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
|Authors: Jeroen Derks <contact@jeroenderks.com>                       |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'security.crypt.lib.Crypting' );


/**
 * Class that implements the xTEA encryption algorithm.
 * This enables you to encrypt data without requiring mcrypt.
 *
 * Original code: http://vader.brad.ac.uk/tea/source.shtml#new_ansi
 *
 * @package security_crypt_lib
 */
 
class Crypting_xtea extends Crypting
{
	/**
	 * Indicates if algorithm is reversible.
	 *
	 * @var	   boolean
	 * @access private
	 */
	var $_is_reversible = true;

    /**
     * Number of iterations.
	 *
     * @var    integer
     * @access private
     */
    var $_iterations = 32;
	
	
	/**
	 * Constructor
	 *
	 * @param array
	 * 		o key        string
	 *  	o iterations integer
	 *
	 * @access public
	 */
	function Crypting_xtea( $options = array() )
	{
		$this->Crypting( $options );

		if ( isset( $options['key'] ) )
			$this->setKey( $options['key'] );

		if ( isset( $options['iterations'] ) )
			$this->setIterations( $options['iterations'] );
	}
	
	
    /**
     * Encrypt text using a specific key.
     *
     * @param  string  $plaintext   Data to encrypt.
     * @param  array   $params      (key - Key to encrypt data with (binary string))
     * @return string               Binary encrypted character string.
     * @access public
     */
    function encrypt( $plaintext, $params = array() ) 
	{
		if ( empty( $params['key'] ) )
			$params['key'] = $this->getKey();
			
        // resize data to 32 bits (4 bytes)
        $n = $this->_resize( $plaintext, 4 );

        // convert data to long
        $plaintext_long[0] = $n;
        $n_data_long = $this->_str2long( 1, $plaintext, $plaintext_long );

        // resize data_long to 64 bits (2 longs of 32 bits)
        $n = count( $plaintext_long );
		
        if ( ( $n & 1 ) == 1 ) 
		{
            $plaintext_long[$n] = chr( 0 );
            $n_data_long++;
        }

        // resize key to a multiple of 128 bits (16 bytes)
        $this->_resize( $params['key'], 16, true );

        // convert key to long
        $n_key_long = $this->_str2long( 0, $params['key'], $key_long );

        // encrypt the long data with the key
        $enc_data = '';
        $w = array( 0, 0 );
        $j = 0;
        $k = array( 0, 0, 0, 0 );
		
        for ( $i = 0; $i < $n_data_long; ++$i ) 
		{
            // get next key part of 128 bits
            if ( $j + 4 <= $n_key_long ) 
			{
                $k[0] = $key_long[$j];
                $k[1] = $key_long[$j + 1];
                $k[2] = $key_long[$j + 2];
                $k[3] = $key_long[$j + 3];
            } 
			else 
			{
                $k[0] = $key_long[$j % $n_key_long];
                $k[1] = $key_long[( $j + 1 ) % $n_key_long];
                $k[2] = $key_long[( $j + 2 ) % $n_key_long];
                $k[3] = $key_long[( $j + 3 ) % $n_key_long];
            }
			
            $j = ( $j + 4 ) % $n_key_long;

            $this->_encipherLong( $plaintext_long[$i], $plaintext_long[++$i], $w, $k );

            // append the enciphered longs to the result
            $enc_data .= $this->_long2str( $w[0] );
            $enc_data .= $this->_long2str( $w[1] );
        }

        return $enc_data;
    }

    /**
     * Decrypt text using a specific key.
     *
     * @param  string  $ciphertext   Encrypted data to decrypt.
     * @param  array   $params       (key - Key to decrypt encrypted data with (binary string))
     * @return string                Binary decrypted character string.
     * @access public
     */
    function decrypt( $ciphertext, $params = array() ) 
	{
		if ( empty( $params['key'] ) )
			$params['key'] = $this->getKey();
			
        // convert data to long
        $n_enc_data_long = $this->_str2long( 0, $ciphertext, $ciphertext_long );

        // resize key to a multiple of 128 bits (16 bytes)
        $this->_resize( $params['key'], 16, true );

        // convert key to long
        $n_key_long = $this->_str2long( 0, $params['key'], $key_long );

        // decrypt the long data with the key
        $data = '';
        $w    = array( 0, 0 );
        $j    = 0;
        $len  = 0;
        $k    = array( 0, 0, 0, 0 );
        $pos  = 0;
        
		for ( $i = 0; $i < $n_enc_data_long; $i += 2 ) 
		{
            // get next key part of 128 bits
            if ( $j + 4 <= $n_key_long ) 
			{
                $k[0] = $key_long[$j];
                $k[1] = $key_long[$j + 1];
                $k[2] = $key_long[$j + 2];
                $k[3] = $key_long[$j + 3];
            } 
			else 
			{
                $k[0] = $key_long[$j % $n_key_long];
                $k[1] = $key_long[( $j + 1 ) % $n_key_long];
                $k[2] = $key_long[( $j + 2 ) % $n_key_long];
                $k[3] = $key_long[( $j + 3 ) % $n_key_long];
            }
			
            $j = ( $j + 4 ) % $n_key_long;

            $this->_decipherLong( $ciphertext_long[$i], $ciphertext_long[$i + 1], $w, $k );
 
            // append the deciphered longs to the result data (remove padding)
            if ( 0 == $i ) 
			{
                $len = $w[0];
                
				if ( 4 <= $len )
                    $data .= $this->_long2str( $w[1] );
                else
                    $data .= substr( $this->_long2str( $w[1] ), 0, $len % 4 );
            } 
			else 
			{
                $pos = ( $i - 1 ) * 4;
                
				if ( $pos + 4 <= $len ) 
				{
                    $data .= $this->_long2str( $w[0] );

                    if ( $pos + 8 <= $len )
                        $data .= $this->_long2str( $w[1] );
                    else if ( $pos + 4 < $len )
                        $data .= substr( $this->_long2str( $w[1] ), 0, $len % 4 );
                } 
				else 
				{
                    $data .= substr( $this->_long2str( $w[0] ), 0, $len % 4 );
                }
            }
        }
		
        return $data;
    }
	
    /**
     * Set the number of iterations to use.
     *
     * @param  integer $iterations Number of iterations to use.
     * @access public
     */
    function setIterations( $iterations )
	{
        $this->_iterations = $iterations;
	}
	
    /**
     * Get the number of iterations to use.
     *
     * @return integer Number of iterations to use.
     * @access public
     */
    function getIterations()
	{
        return $this->_iterations;
    }
	
	
	// private methods
	
    /**
     * Encipher a single long (32-bit) value.
     *
     * @param  integer $y  32 bits of data.
     * @param  integer $z  32 bits of data.
     * @param  array   &$w Placeholder for enciphered 64 bits (in w[0] and w[1]).
     * @param  array   &$k Key 128 bits (in k[0]-k[3]).
     * @access private
     */
    function _encipherLong( $y, $z, &$w, &$k ) 
	{
        $sum   = (int)0;
        $delta = (int)0x9E3779B9;
        $n     = (int)$this->_iterations;

        while ( $n-- > 0 ) 
		{
            $y   += ( $z << 4 ^ $z >> 5 ) + $z ^ $sum + $k[$sum & 3];
            $sum += $delta;
            $z   += ( $y << 4 ^ $y >> 5 ) + $y ^ $sum + $k[$sum >> 11 & 3];
        }

        $w[0] = $y & 0xffffffff;
        $w[1] = $z & 0xffffffff;
    }
	
    /**
     * Decipher a single long (32-bit) value.
     *
     * @param  integer $y  32 bits of enciphered data.
     * @param  integer $z  32 bits of enciphered data.
     * @param  array   &$w Placeholder for deciphered 64 bits (in w[0] and w[1]).
     * @param  array   &$k Key 128 bits (in k[0]-k[3]).
     * @access private
     */
    function _decipherLong( $y, $z, &$w, &$k ) 
	{
        // sum = delta<<5, in general sum = delta * n
        $sum    = (int)0xC6EF3720;
        $delta  = (int)0x9E3779B9;
        $n      = (int)$this->_iterations;

        while ( $n-- > 0 ) 
		{
            $z   -= ( $y << 4 ^ $y >> 5 ) + $y ^ $sum + $k[$sum >> 11 & 3];
            $sum -= $delta;
            $y   -= ( $z << 4 ^ $z >> 5 ) + $z ^ $sum + $k[$sum & 3];
        }

        $w[0] = $y & 0xffffffff;
        $w[1] = $z & 0xffffffff;
    }
	
    /**
     * Resize data string to a multiple of specified size.
     *
     * @param  string  $data   Data string to resize to specified size.
     * @param  integer $size   Size in bytes to align data to.
     * @param  boolean $nonull Set to true if padded bytes should not be zero.
     * @return integer         Length of supplied data string.
     * @access private
     */
    function _resize( &$data, $size, $nonull = false ) 
	{
        $n    = strlen( $data );
        $nmod = $n % $size;
		
        if ( $nmod > 0 ) 
		{
            if ( $nonull ) 
			{
                for ( $i = $n; $i < $n - $nmod + $size; ++$i )
                    $data[$i] = $data[$i % $n];
            } 
			else 
			{
                for ( $i = $n; $i < $n - $nmod + $size; ++$i )
                    $data[$i] = chr( 0 );
            }
        }
		
        return $n;
    }
	
    /**
     * Convert a hexadecimal string to a binary string (e.g. convert "616263" to "abc").
     *
     * @param  string  $str    Hexadecimal string to convert to binary string.
     * @return string          Binary string.
     * @access private
     */
    function _hex2bin( $str )
    {
        $len = strlen( $str );
        return pack( "H" . $len, $str );
    }
	
    /**
     * Convert string to array of long.
     *
     * @param  integer $start      Index into $data_long for output.
     * @param  string  &$data      Input string.
     * @param  array   &$data_long Output array of long.
     * @return integer             Index from which to optionally continue.
     * @access private
     */
    function _str2long( $start, &$data, &$data_long ) 
	{
        $n = strlen( $data );
		
        for ( $i = 0, $j = $start; $i < $n; $i = $i + 4, ++$j ) 
		{
            $data_long[$j] = ( ( ord( $data[$i] )     & 0xff ) << 24 ) +
                             ( ( ord( $data[$i + 1] ) & 0xff ) << 16 ) +
                             ( ( ord( $data[$i + 2] ) & 0xff ) <<  8 ) +
                             ( ( ord( $data[$i + 3] ) & 0xff ) );
        }
		
        return $j;
    }
	
    /**
     * Convert long to character string.
     *
     * @param  long    $l  Long to convert to character string.
     * @return string      Character string.
     * @access private
     */
    function _long2str( $l ) 
	{
        return pack( 'N', $l );
    }
} // END OF Crypting_xtea

?>
