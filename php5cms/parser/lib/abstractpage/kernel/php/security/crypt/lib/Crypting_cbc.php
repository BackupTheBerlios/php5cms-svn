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
|         Colin Viebrock <colin@easydns.com>                           |
+----------------------------------------------------------------------+
*/


using( 'security.crypt.lib.Crypting' );


/**
 * Class to emulate Perl's Crypt::CBC module
 *
 * Blowfish support is not completely working, mainly because of a bug
 * discovered in libmcrypt (version 2.4.8 and earlier). If you are running
 * a later version of libmcrypt > 2.4.8, you can do Blowfish encryption
 * that is compatable with Perl. However, check the libmcrypt documenation
 * as to whether you should use 'BLOWFISH' or 'BLOWFISH-COMPAT' when
 * specifying the cipher.
 *
 * If you are using libmcrypt <= 2.4.8, Blowfish encryption will work,
 * but your data will not be readable by Perl scripts. It will work
 * "internally" .. i.e. this class will be able to encode/decode the data.
 *
 * This class will no longer work with libmcrypt 2.2.x versions.
 *
 * NOTE: the cipher names in this class may change depending on how
 * the author of libcrypt decides to name things internally.
 *
 * @package security_crypt_lib
 */
 
class Crypting_cbc extends Crypting
{
	/**
	 * Indicates if algorithm is reversible.
	 *
	 * @var	   boolean
	 * @access private
	 */
	var $_is_reversible = true;

    /**
     * supported procedures
	 *
     * @var    array
	 * @access private
     */
    var $_known_ciphers = array (
        'DES'             => MCRYPT_DES,
        'BLOWFISH'        => MCRYPT_BLOWFISH,
        'BLOWFISH-COMPAT' => MCRYPT_BLOWFISH_COMPAT,
    );

    /**
     * used cipher
	 *
     * @var    string
	 * @access private
     */
    var $_cipher;

    /**
     * crypt resource, for 2.4.x
	 *
     * @var    string
	 * @access private
     */  
    var $_td;

    /**
     * blocksize of cipher
	 *
     * @var    string
	 * @access private
     */    
    var $_blocksize;

    /**
     * keysize of cipher
	 *
     * @var    int
	 * @access private
     */    
    var $_keysize;

    /**
     * mangled key
	 *
     * @var    string
	 * @access private
     */        
    var $_keyhash;

    /**
     * source type of the initialization vector for creation  
     * possible types are MCRYPT_RAND or MCRYPT_DEV_URANDOM or MCRYPT_DEV_RANDOM
	 *
     * @var    int
	 * @access private
     */            
    var $_rand_source = MCRYPT_RAND;

    /**
     * header
	 *
     * @var    string
	 * @access private
     */           
    var $_header_spec = 'RandomIV';

    /**
     * debugging
	 *
     * @var    string
	 * @access private
     */           
    var $_last_clear;

    /**
     * debugging
	 *
     * @var    string
	 * @access private
     */              
    var $_last_crypt;
	
	
	/**
	 * Constructor
	 *
	 * @param array
	 *      o key    string encryption key
	 *      o cipher string which algorithm to use, defaults to DES
	 *
	 * @access public
	 */
	function Crypting_cbc( $options = array() )
	{
		$this->Crypting( $options );
		
        if ( !Crypting::useMCrypt() )
		{
			$this = new PEAR_Error( 'Mcrypt module is not compiled into PHP. Compile PHP using "--with-mcrypt".' );
			return;
		}
        
        if ( !function_exists( 'mcrypt_module_open' ) )
		{
			$this = new PEAR_Error( 'This class only works with libmcrypt 2.4.x and later.' );
			return;
		}

        // seed randomizer
        srand ( (double)microtime() * 1000000 );

        // initialize
        $this->_header_spec = 'RandomIV';

        // check for key
        if ( !$options['key'] )
		{
			$this = new PEAR_Error( 'No key specified.' );
			return;
		}

        // check for cipher
        $cipher = strtoupper( $options['cipher']);
		
        if ( !isset( $this->_known_ciphers[$cipher] ) )
			$cipher = 'DES';

        $this->_cipher = $this->known_ciphers[$cipher];

        // initialize cipher
        $this->_blocksize = mcrypt_get_block_size( $this->_cipher, 'cbc' );
        $this->_keysize   = mcrypt_get_key_size( $this->_cipher, 'cbc' );
        $this->_td        = mcrypt_module_open( $this->_cipher, '', 'ecb', '' );

        // mangle key with MD5
        $this->_keyhash = $this->_md5perl( $options['key'] );
		
        while ( strlen( $this->_keyhash ) < $this->_keysize )
            $this->_keyhash .= $this->_md5perl( $this->_keyhash );

		$this->setKey( substr( $this->_keyhash, 0, $this->_keysize ) );
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
        $this->last_clear = $plaintext;

        // new IV for each message
        $iv = mcrypt_create_iv( $this->_blocksize, $this->_rand_source );

        // create the message header
        $crypt = $this->_header_spec . $iv;

        // pad the cleartext
        $padsize    = $this->_blocksize - ( strlen( $plaintext ) % $this->_blocksize );
        $plaintext .= str_repeat( pack ( 'C*', $padsize ), $padsize );

        // do the encryption
        $start = 0;
		
        while ( $block = substr( $plaintext, $start, $this->_blocksize ) )
		{
            $start += $this->_blocksize;
			
            if ( mcrypt_generic_init( $this->_td, $this->_key, $iv ) < 0 )
                return false;
            
            $cblock  = mcrypt_generic( $this->_td, $iv ^ $block );
            $iv      = $cblock;
            $crypt  .= $cblock;
        }

        $this->last_crypt = $crypt;
        return $crypt;
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
        $this->last_crypt = $ciphertext;

        // get the IV from the message header
        $iv_offset = strlen( $this->_header_spec );
        $header    = substr( $ciphertext, 0, $iv_offset );
        $iv        = substr( $ciphertext, $iv_offset, $this->_blocksize );
		
        if ( $header != $this->_header_spec )
            return false;

        $ciphertext = substr( $ciphertext, $iv_offset + $this->_blocksize );

        // decrypt the message
        $start = 0;
        $clear = '';

        while ( $cblock = substr( $ciphertext, $start, $this->_blocksize ) )
		{
            $start += $this->_blocksize;
			
            if ( mcrypt_generic_init( $this->_td, $this->_key, $iv ) < 0 )
                return false;
            
            $block  = $iv ^ mdecrypt_generic( $this->_td, $cblock );
            $iv     = $cblock;
            $clear .= $block;
        }

        // remove the padding from the end of the cleartext
        $padsize = ord(substr( $clear, -1 ) );
        $clear   = substr( $clear, 0, -$padsize );

        $this->last_clear = $clear;
        return $clear;
	}
	
    /**
     * @access public
     */
    function finalize()
    {
        @mcrypt_generic_end( $this->_td );
        @mcrypt_module_close( $this->_td );
    }
	
	
	// private methods
	
    /**
     * Emulate Perl's MD5 function, which returns binary data
     *
     * @param    $string     string to MD5
     * @return   $hash       binary hash
     * @access private
     */
    function _md5perl( $string )
    {
        return pack( 'H*', md5( $string ) );
    }
} // END OF Crypting_cbc

?>
