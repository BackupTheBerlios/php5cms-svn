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
|Authors: Manon Goo <manon@passionet.de>                               |
|         Chuck Hagenbuch <chuck@horde.org>                            |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'security.crypt.lib.Crypting' );
using( 'util.Util' );


/**
 * Class to emulate Perl's Crypt::HCE_MD5 module
 * 
 * The MIME Functions are tested and work symmetrically with the
 * Crypt::HCE_MD5 package (0.45) (without the KEYBUG Flag ..).
 * 
 * Shamelessly stolen from Eric Estabrooks, eric@urbanrage.com
 * Crypt::HCE_MD5 package:
 * 
 * This package implements a chaining block cipher using a one way
 * hash. This method of encryption is the same that is used by radius
 * (RFC2138) and is also described in Applied Cryptography by Bruce
 * Schneider (p. 353 / "Karn").
 * 
 * Two interfaces are provided in the package. The first is straight
 * block encryption/decryption the second does base64 mime
 * encoding/decoding of the encrypted/decrypted blocks.
 * 
 * The idea is the the two sides have a shared secret that supplies one
 * of the keys and a randomly generated block of bytes provides the
 * second key. The random key is passed in cleartext between the two
 * sides.
 * 
 * Usage:
 *
 * $key = 'my secret key';
 * srand( (double)microtime() * 32767 );
 * $rand = rand( 1, 32767 );
 * $rand = pack( 'i*', $rand );
 * $message = 'text to encrypt';
 * $hcemd5  = new Crypting_hcemd5( array( 'key' => $key, 'rand' => $rand ) );
 * 
 * // These Functions work with mime decoded Data
 * $ciphertext = $hcemd5->encodeMime( $message );
 * $cleartext  = $hcemd5->decodeMime( $ciphertext );
 * 
 * // These Functions work with binary Data
 * $ciphertext = $hcemd5->encrypt( $message );
 * $cleartext  = $hcemd5->decrypt( $ciphertext );
 * 
 * // These Functions work with mime decoded Data the selfrand
 * // functions put the random value infront of the encrypted data to
 * // be restored later
 * $ciphertext = $hcemd5->encodeMimeSelfRand( $message );
 * $new_hcemd5 = new Crypting_hcemd5( array( 'key' => $key ) );
 * $cleartext  = $new_hcemd5->decodeMimeSelfRand( $ciphertext );
 *
 * @package security_crypt_lib
 */
 
class Crypting_hcemd5 extends Crypting
{
	/**
	 * Indicates if algorithm is reversible.
	 *
	 * @var	   boolean
	 * @access private
	 */
	var $_is_reversible = true;
	
	/**
     * The second key to use. This should be a randomly generated
     * block of bytes.
	 *
     * @var    long
	 * @access private
     */
	var $_rand;
	
	
	/**
	 * Constructor
	 *
	 * @param array
	 * 		o key        string
	 *		o rand       long
	 *
	 * @access public
	 */
	function Crypting_hcemd5( $options = array() )
	{
		$this->Crypting( $options );
		
		if ( isset( $options['key'] ) )
			$this->setKey( $options['key'] );
			
		if ( !isset( $options['rand'] ) )
		{
            srand( (double)microtime() * 32767 );
            $rand = rand( 1, 32767 );
            $rand = pack( 'i*', $rand );
			
			$this->_rand = $rand;
        }
		else
		{
			$this->_rand = $options['key'];
		}
	}
	
	
    /**
     * Encrypt a block of data.
     *
     * @param  string $data The data to encrypt.
     * @return string       The encrypted binary data.
     * @access public
     */
    function encrypt( $data, $params = array() )
    {
	    $data      = unpack( 'C*', $data );
        $ans       = array();
        $ans1      = array(); 
        $eblock    = 1;
        $e_block   = $this->_newKey( $this->_rand );
        $data_size = count( $data );
		
        for ( $i = 0; $i < $data_size; $i++ )
		{
            $mod = $i % 16;
			
			if ( ( $mod == 0 ) && ( $i > 15 ) )
			{
       			$tmparr  = array( $ans[$i - 15], $ans[$i - 14], $ans[$i - 13], $ans[$i - 12], $ans[$i - 11], $ans[$i - 10], $ans[$i - 9], $ans[$i - 8], $ans[$i - 7], $ans[$i - 6], $ans[$i - 5], $ans[$i - 4], $ans[$i - 3], $ans[$i - 2], $ans[$i - 1], $ans[$i] );
                $tmparr  = $this->_arrayToPack( $tmparr );
                $tmparr  = implode( '', $tmparr );
                $e_block = $this->_newKey( $tmparr );
            }
            
            $mod++;
            $i++;
            $ans[$i]  = $e_block[$mod] ^ $data[$i];
            $ans1[$i] = pack( 'C*', $ans[$i] );
            $i--;
            $mod--;
        }
		
	    return implode( '', $ans1 );
    }
	
    /**
     * Decrypt a block of data.
     *
     * @param  string $data The data to decrypt.
     * @return string       The decrypted binary data.
     * @access public
     */
    function decrypt( $data, $params = array() )
    {
	    $data      = unpack( 'C*', $data );
        $ans       = array();
        $ans1      = array(); 
        $eblock    = 1;
        $e_block   = $this->_newKey( $this->_rand );
        $data_size = count( $data );
		
        for ( $i = 0; $i < $data_size; $i++ )
		{
            $mod = $i % 16;
            if ( ( $mod == 0 ) && ( $i > 15 ) )
			{
       			$tmparr  = array( $data[$i - 15], $data[$i - 14], $data[$i - 13], $data[$i - 12], $data[$i - 11], $data[$i - 10], $data[$i - 9], $data[$i - 8], $data[$i - 7], $data[$i - 6], $data[$i - 5], $data[$i - 4], $data[$i - 3], $data[$i - 2], $data[$i - 1], $data[$i] );
                $tmparr  = $this->_arrayToPack( $tmparr );
                $tmparr  = implode( '', $tmparr );
                $e_block = $this->_newKey( $tmparr );
            }
            
            $mod++;
            $i++;
            $ans[$i]  = $e_block[$mod] ^ $data[$i];
            $ans1[$i] = pack( 'C*', $ans[$i] );
            $i--;
        }
		
	    return implode( '', $ans1 );
    }
	
    /**
     * Encrypt a block of data after MIME-encoding it.
     *
     * @param  string $data The data to encrypt.
     * @return string       The encrypted mime-encoded data.
     * @access public
     */
    function encodeMime( $data )
    {
        return base64_encode( $this->encrypt( $data ) );
    }
    
    /**
     * Decrypt a block of data and then MIME-decode it.
     *
     * @param  string $data The data to decrypt.
     * @return string       The decrypted mime-decoded data.
     * @access public
     */
    function decodeMime( $data )
    {
        return $this->decrypt( base64_decode( $data ) );
    }
    
    /**
     * Encrypt a block of data after MIME-encoding it, and include the
     * random hash in the final output in plaintext so it can be
     * retrieved and decrypted with only the secret key by
     * decodeMimeSelfRand().
     *
     * @param  string $data The data to encrypt.
     * @param  string       The encrypted mime-encoded data, in the format: randkey#encrypted_data.
     * @access public
     */
    function encodeMimeSelfRand( $data )
	{
		return base64_encode( $this->_rand ) . '#' . $this->encodeMime( $data );
    }
    
    /**
     * Decrypt a block of data and then MIME-decode it, using the
     * random key stored in beginning of the ciphertext generated by
     * encodeMimeSelfRand().
     *
     * @param  string $data The data to decrypt, in the format: randkey#encrypted_data.
     * @return string       The decrypted, mime-decoded data.
     * @access public
     */
    function decodeMimeSelfRand( $data )
    {
        if ( strpos( $data, '#' ) === false )
            return false;
        
        list( $rand, $data_crypt ) = explode( '#', $data );
		
        if ( isset( $data_crypt ) )
		{
            $rand = base64_decode( $rand );
            $this->_rand = $rand;
            
			return $this->decodeMime( $data_crypt );
        }
		else
		{
            return false;
        }
    }


	// private methods
	
    /**
     * Implement md5 hashing in php, though use the mhash() function if it is available.
     *
     * @param  string $string The string to hash.
     * @return string         The md5 mhash of the string.
     * @access private
     */
    function _binmd5( $string )
    {
        if ( Util::extensionExists( 'mhash' ) )
            return mhash( MHASH_MD5, $string );

        return pack( 'H*', md5( $string ) );
    }
    
    /**
     * Turn an array into a binary packed string.
     *
     * @param  array  $array The array to pack.
     * @return string        The binary packed representation of the array.
     * @access private
     */
    function _arrayToPack( $array )
    {
        $pack = array();
		
        foreach ( $array as $val )
            $pack[] = pack( 'C*', $val );
        
        return $pack;
    }
	
	/**
     * Generate a new key for a new encryption block.
     *
     * @param  string $round The basis for the key.
     * @param  string        The new key.
     * @access private
     */
	function _newKey( $round )
    {
        $digest = $this->_binmd5( $this->_key . $round );
        return unpack( 'C*', $digest );
    }
} // END OF Crypting_hcemd5

?>
