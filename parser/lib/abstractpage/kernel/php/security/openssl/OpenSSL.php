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


define( "OPENSSL_SUCCESS",    0 );
define( "PUBLIC_KEY_ERROR",  -1 );
define( "PRIVATE_KEY_ERROR", -2 );
define( "ENCRYPTION_ERROR",  -3 );
define( "DECRYPTION_ERROR",  -4 );


/**
 * Encrypts and Decrypts data using the OpenSSL extension
 *
 * I included a function that verifies the encryption by
 * decrypting the data and comparing against the original
 * string. I feel this is necessary to ensure retrieval
 * of sensitive data. This class allows for private keys that
 * have passphrases and those that do not. I recommend ALWAYS
 * creating a private key that uses a passphrase. But it's up
 * to you.
 *
 * Note:
 *   I've also made it compatible with localization. The english version
 *   is in file english_encrypt.php. To use other languages just save the
 *   english_encrypt.php file as [language]_encrypt.php. Translate the defines
 *   to the language of choice and change the require_once at the top of this page.
 *   If you do we would appreciate it if you emailed us a copy of the new translation.
 *     Thanks <dev@terraaccess.com>
 *
 * Requires:   OpenSSL Extension Installed and working
 *             PHP 4.1 or higher ( Tested on 4.3.1, 4.1.2, 4.3.3 )
 *             Localization File: english_encrypt.php or translated file
 *             RSA Certificate and Key File
 *
 * Creating a Private Key:
 *   openssl genrsa -des3 -out privkey.pem 2048
 *   Note: this was taken straight from http://www.openssl.org/docs/HOWTO/keys.txt
 *         to create a key file without a passphrase remove the -des3 param
 *   Key Size: In the above example the key size is 2048 bits. The size of your data
 *         to encrypt is limited by this number. You can only encrypt data of the
 *         length:
 *               bytes - 11
 *               2048 bits / 8 bits per byte = 256 bytes
 *               256 - 11 = 245 byte Maximum size of data to encrypt
 *               If you are going to encrypt larger chunks of data
 *               ust split up the data and encrypt. I will work on that:)
 *
 * Creating a Certificate:
 *   openssl req -new -x509 -key privkey.pem -out cacert.pem -days 1095
 *   Note: this was taken straight from http://www.openssl.org/docs/HOWTO/certificates.txt
 *
 * @link http://www.openssl.org/docs/HOWTO/keys.txt Create Key HOWTO
 * @link http://www.openssl.org/docs/HOWTO/certificates.txt Create Certificat HOWTO
 * @package security_openssl
 */

class OpenSSL extends PEAR
{
	/**
   	 * Full Path with filename to
   	 * the public key file.
   	 *
   	 * @access public
   	 */
  	var $public_key_path = "";
  
	/**
   	 * Full Path with filename to
   	 * the private key file.
   	 *
   	 * @access public
   	 */
  	var $private_key_path = "";
  
  	/**
  	 * Passphrase for private key if required
  	 * ensure this is set to "" if you don't
  	 * use a passphrase for your private key.
  	 *
  	 * @access public
  	 */
  	var $passphrase = "";
  
  	/**
  	 * Holds the string to encrypt for
  	 * verification and processing.
  	 *
  	 * @access public
  	 */
  	var $string_to_encrypt = "";
  
  	/**
 	 * Holds the encrypted data for retrieval
 	 * and testing.
 	 *
 	 * @access public
 	 */
  	var $encrypted_data = "";
  
  	/**
 	 * Holds decrypted data for retrieval.
 	 *
 	 * @access public
 	 */
  	var $decrypted_data = "";
  
  	/**
 	 * Set to the last error number
 	 * encountered.
 	 *
 	 * @access public
 	 */
  	var $errno = "";
  
 	/**
 	 * Set to the text for the last
 	 * error encountered.
 	 *
 	 * @access public
 	 */
  	var $error = "";
	
	
  	/**
 	 * Sets the path to the public key file.
 	 *
 	 * @param string $public_key_path_in Path to Public Key
 	 * @return none
 	 * @access public
 	 */
  	function set_public_key( $public_key_path_in )
  	{
    	$this->public_key_path = $public_key_path_in;
  	}

  	/**
 	 * Sets the path to the private key file.
 	 *
 	 * @param string $private_key_path_in Path to Private Key
 	 * @return none
 	 * @access public
 	 */
  	function set_private_key( $private_key_path_in )
  	{
    	$this->private_key_path = $private_key_path_in;
  	}

  	/**
 	 * Sets the passphrase for the private key
 	 * ensure this is "" if passphrase not used.
 	 *
 	 * @param string $passphrase_in Private Key Passphrase
 	 * @return none
 	 * @access public
 	 */
  	function set_passphrase( $passphrase_in )
  	{
    	$this->passphrase = $passphrase_in;
  	}

  	/**
 	 * Uses the public key to encrypt data
 	 * This can't be decrypted without the private key.
 	 *
 	 * @param string $data_to_encrypt String to Encrypt
 	 * @return string Encrypted data or error if failed
 	 * @access public
 	 */
  	function encrypt_data_public( $data_to_encrypt )
  	{
    	$this->clear_error();

    	// set the class variable to data passed in
    	$this->string_to_encrypt = $data_to_encrypt;

    	// check to see if th public key path is valid
    	if ( !file_exists( $this->public_key_path ) )
    	{
      		$this->set_error( PUBLIC_KEY_ERROR, "Invalid Public Key Path." );
      		return PUBLIC_KEY_ERROR;
    	}
    
		// open and read the public key
    	$fp = fopen( $this->public_key_path, "r" );
    	$public_key_tmp = fread( $fp, 8192 );
    	fclose( $fp );

    	// generate a public key resource
    	$public_key = openssl_get_publickey( $public_key_tmp );

    	// if getting private key failed then kick out error
    	if ( !$public_key )
    	{
      		$this->set_error( PUBLIC_KEY_ERROR, "Get Public Key Failed." );
      		openssl_free_key( $public_key );
      		
			return PUBLIC_KEY_ERROR;
    	}
    
    	// encrypt the data
    	openssl_public_encrypt( $this->string_to_encrypt, $encrypted_data_tmp, $public_key );

    	// check to make sure data is returned and decryption succeeds
    	if ( empty( $encrypted_data_tmp ) )
    	{
      		$this->set_error( ENCRYPTION_ERROR, "OpenSSL returned an empty encrypted result." );
      		openssl_free_key( $public_key );
      
	  		return ENCRYPTION_ERROR;
    	}
    
		// verify able to properly decrypt data
    	$ret = $this->verify_encryption( $encrypted_data_tmp );
    
		if ( $ret != OPENSSL_SUCCESS )
    	{
      		// $this->set_error( $ret, "Failed while verifying encrypted data." );
      		openssl_free_key( $public_key );
      
	  		return $ret;
    	}
    
    	$this->encrypted_data = $encrypted_data_tmp;
    	openssl_free_key( $public_key );
    	
		return OPENSSL_SUCCESS;
  	}

  	/**
 	 * Decrypts data using the private key.
 	 *
 	 * @param string $encrypted_data Data to Decrypt
 	 * @return string $decrypted Plain Text Decryption
 	 * @access public
 	 */
  	function decrypt_data_private( $encrypted_data )
  	{
    	$this->clear_error();

    	// check to see if th private key path is valid
    	if( !file_exists( $this->private_key_path ) )
    	{
      		$this->set_error( PRIVATE_KEY_ERROR, "Invalid Private Key Path." );
      		return PRIVATE_KEY_ERROR;
    	}

    	$fp = fopen( $this->private_key_path, "r" );
    	$private_key_tmp = fread( $fp, 8192 );
    	fclose( $fp );
    
    	// check for passphrase and decrypt appropriately
    	// I don't know if passing an empty string is the
    	// same as not setting the variable. I need to test this still
    	if ( $this->passphrase == "" )
      		$private_key = openssl_get_privatekey( $private_key_tmp );
    	else
      		$private_key = openssl_get_privatekey( $private_key_tmp, $this->passphrase );

    	// if getting private key failed then kick out error
    	if ( !$private_key )
    	{
      		$this->set_error(PRIVATE_KEY_ERROR, "Get Private Key Failed, Please check passphrase for accuracy." );
      		return PRIVATE_KEY_ERROR;
    	}
    
    	$ret = openssl_private_decrypt( $encrypted_data, $decrypted, $private_key );

    	// test to ensure data was decrypted
    	if ( !$ret )
    	{
      		$this->set_error( DECRYPTION_ERROR, "Decryption of data failed." );
      		openssl_free_key( $private_key );
      		
			return DECRYPTION_ERROR;
    	}
    
		$this->decrypted_data = $decrypted;
    	openssl_free_key( $private_key );
    
		return OPENSSL_SUCCESS;
  	}

  	/**
 	 * Verifies Encrypted data by decrypting and
 	 * comparing against the original data.
 	 *
 	 * @param string $encrypted_data Encryption to Verify
 	 * @return OPENSSL_SUCCESS or DECRYPTION_ERROR
 	 * @access public
 	 */
  	function verify_encryption( $encrypted_data )
  	{
    	$ret = $this->decrypt_data_private( $encrypted_data );
		
    	if ( $ret != OPENSSL_SUCCESS )
      		return $ret;
    
    	if ( $this->decrypted_data == $this->string_to_encrypt )
      		return OPENSSL_SUCCESS;
    	else
      		return DECRYPTION_ERROR;
  	}

  	/**
 	 * Returns the last encrypted data.
 	 *
 	 * @return string Encrypted Data
 	 *
 	 * @access public
 	 */
  	function get_encrypted_data()
  	{
    	return $this->encrypted_data;
  	}
  
  	/**
 	 * Returns the last decrypted data.
 	 *
 	 * @return string Decrypted Data
 	 *
 	 * @access public
 	 */
  	function get_decrypted_data()
  	{
    	return $this->decrypted_data;
  	}
  
  	/**
 	 * Sets the error number and error string values
 	 * to the specified error.
 	 *
 	 * @param integer $err_number Error Number
 	 * @param string $error_string Error String
 	 * @return none
 	 * @access public
 	 */
  	function set_error( $err_number, $error_string )
  	{
    	$this->errno = $err_number;
    	$this->error = $error_string;
  	}

  	/**
 	 * Returns the last error number set.
 	 *
 	 * @return integer Error Number
 	 * @access public
 	 */
  	function get_error_number()
  	{
    	return $this->errno;
  	}

  	/**
 	 * Kicks out the errors stored in the
 	 * openssl error handler. Only place
 	 * I use html in this module.
 	 *
 	 * @return
 	 */
  	function kick_openssl_errors()
  	{
    	while ( $msg = openssl_error_string() )
      		echo $msg . "<br />\n";
  	}
  
  	/**
 	 * Returns the last error string set.
 	 *
 	 * @return string Error String
 	 * @access public
 	 */
 	function get_error_string()
  	{
    	return $this->error;
  	}

  	/**
 	 * Clears the last error set.
 	 *
 	 * @return none
 	 * @access public
 	 */
  	function clear_error()
  	{
    	$this->error = "";
    	$this->errno = "";
  	}
	
    /**
     * Retrieve errors.
     *
     * @static
     * @access  public
     * @return  string[] error
     */
    function get_errors()
	{
      	$e = array();
      
	  	while ( $msg = openssl_error_string() )
        	$e[]= $msg;
      
      	return $e;
    }
    
    /**
     * Get OpenSSL configuration file environment value.
     *
     * @access  public
     * @return  string
     */
    function get_configuration()
	{
      	return getenv( 'OPENSSL_CONF' );
    }
} // END OF OpenSSL

?>
