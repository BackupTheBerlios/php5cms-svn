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
 * Encrypts and decrypts strings based on public/private RSA keypair.
 * It requires PHP with OpenSSL support (configure --with-openssl). 
 *
 * Usage:
 *
 * $options = array(
 * 		'public_key_path'  => "/usr/web/mycert.crt";
 * 		'private_key_path' => "/usr/web/mykey.key";
 * 		'passphrase'       => "salmon4568";
 * );
 * $crypt = new Crypting_openssl( $options );
 *
 * $string = "1234 5678 9012 3456"; // credit card number
 * $encrypted_string = $crypt->encrypt( $string );
 * $decrypted_string = $crypt->decrypt( $encrypted_string );
 * 
 * echo "Encrypted String: $encrypted_string<br>";
 * echo "Decrypted String: $decrypted_string";
 *
 * How to generate key pairs:
 *
 * [foo@bar home]$ /usr/local/ssl/bin/openssl req -x509 -newkey rsa:1025 -days 10950 -keyout mykey.key -out mycert.crt
 *
 * @package security_crypt_lib
 */
 
class Crypting_openssl extends Crypting
{	
	/**
	 * Indicates if algorithm is reversible.
	 *
	 * @var	   boolean
	 * @access private
	 */
	var $_is_reversible = true;

	/**
	 * Path to public key.
	 *
	 * @var    string
	 * @access private
	 */
	var $_public_key_path = "";
	
	/**
	 * Path to private key.
	 *
	 * @var    string
	 * @access private
	 */
	var $_private_key_path = "";
	
	/**
	 * The passphrase.
	 *
	 * @var    string
	 * @access private
	 */
	var $_passphrase = "";
	
		
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Crypting_openssl( $options = array() )
	{
		$this->Crypting( $options );
		
		if ( isset( $options['public_key_path'] ) )
			$this->setPublicKeyPath( $options['public_key_path'] );

		if ( isset( $options['private_key_path'] ) )
			$this->setPrivateKeyPath( $options['private_key_path'] );
			
		if ( isset( $options['passphrase'] ) )
			$this->setPassphrase( $options['passphrase'] );
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
		$fp = fopen( $this->_public_key_path, "r" );
		$public_key = fread( $fp, 8192 );
		fclose( $fp );
		openssl_get_publickey( $public_key );
		openssl_public_encrypt( $plaintext, $encrypted_string, $public_key );
		
		return $encrypted_string;
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
		$result = openssl_get_privatekey( array( "file://" . $this->_private_key_path, $this->_passphrase ) );
		openssl_private_decrypt( $ciphertext, $decrypted_string, $result );

		return $decrypted_string;
	}
	
	/**
	 * Set public key path.
	 *
	 * @param  string  $key
	 * @access public
	 */
	function setPublicKeyPath( $path = "" )
	{
		$this->_public_key_path = $path;
	}
	
	/**
	 * Get public key path.
	 *
	 * @return string
	 * @access public
	 */
	function getPublicKey()
	{
		return $this->_public_key_path;
	}
	
	/**
	 * Set private key path.
	 *
	 * @param  string  $key
	 * @access public
	 */
	function setPrivateKeyPath( $key = "" )
	{
		$this->_private_key_path = $key;
	}
	
	/**
	 * Get private key path.
	 *
	 * @return string
	 * @access public
	 */
	function getPrivateKey()
	{
		return $this->_private_key_path;
	}
	
	/**
	 * Set passphrase.
	 *
	 * @param  string  $key
	 * @access public
	 */
	function setPassphrase( $phrase = "" )
	{
		$this->_passphrase = $phrase;
	}
	
	/**
	 * Get passphrase.
	 *
	 * @return string
	 * @access public
	 */
	function getPassphrase()
	{
		return $this->_passphrase;
	}
	
    /**
     * Retrieve errors.
     *
     * @access public
     * @return string[] error
	 * @static
     */
    function getErrors()
	{
      	$e = array();
      
	  	while ( $msg = openssl_error_string() )
        	$e[] = $msg;
      
      	return $e;
    }
    
    /**
     * Get OpenSSL configuration file environment value.
     *
     * @access public
     * @return string
	 * @static
     */
    function getConfiguration()
	{
      	return getenv( 'OPENSSL_CONF' );
    }
} // END OF Crypting_openssl

?>
