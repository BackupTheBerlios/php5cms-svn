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


using( 'util.Util' );


/**
 * HTTP Storage class
 *
 * PHP class, used to serialize and store information to disk.
 * This class will be used if storage features are activated
 * in the main HTTPNavigator class.
 * You do not have to include it if you will not be saving
 * info to disk.
 *
 * @package peer_http
 */

class HTTPStorage extends PEAR
{
	/**
	 * Takes filename as argument, returns contents of the file
	 *
	 * @param	string	$filepath	full path to file
	 * @return	string				contents of $filepath, false on error
	 */
	function read_file( $filepath )
	{
		if ( file_exists( $filepath ) && is_readable( $filepath ) )
		{
			$fp = @fopen( $filepath, 'r' );
			
			if ( !$fp )
				return false;

			flock( $fp, LOCK_SH );
			$content = fread( $fp, filesize( $filepath ) );
			flock( $fp, LOCK_UN );
			fclose( $fp );
			
			return $content;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Writes $string to file $filepath
	 *
	 * @param	string	$filepath	full path to file
	 * @param	string	$string		string to write
	 * @return	bool				true if write succeeded, false otherwise
	 */
	function write_file( $filepath, $string )
	{
		$fp = @fopen( $filepath, 'w' );
		
		if ( !$fp )
			return false;
			
		flock( $fp, LOCK_EX );
		$result = @fwrite( $fp, $string );
		
		if ( !$result )
			return false;
			
		flock( $fp, LOCK_UN );
		fclose( $fp );
		chmod( $filepath, 0600 );

		return true;
	}

	/**
	 * Will read files, unserialize content, and store in appropriate var
	 *
	 * @param	object	$obj	instance of http_navigator passed by reference
	 * @return	bool
	 */
	function load_files( &$obj )
	{
		if ( $obj->store_name == '' )
			return false;
		
		$file_cookies    = $obj->store_path . 'cookies.'    . $obj->store_name;
		$file_basic_auth = $obj->store_path . 'basic_auth.' . $obj->store_name;

		// cookies
		if ( $obj->store_cookies && file_exists( $file_cookies ) && is_readable( $file_cookies ) )
		{
			$content = $this->read_file( $file_cookies );
			
			if ( $content === false )
				return false;
			
			if ( ( $obj->store_cookies == 'encrypt' )  && ( !empty( $obj->crypt_key ) ) )
			{
				$content = $this->decrypt( $obj, $content );
				
				if ( $content === false )
					return false;
			}
			
			if ( trim( $content ) != '' )
			{
				$content = @unserialize( $content );
				
				if ( is_array( $content ) )
					$obj->cookie = $content;
				else
					return false;
			}
			else
			{
				return false;
			}
		}

		// basic auth
		if ( $obj->store_basic_auth && file_exists( $file_basic_auth ) && is_readable( $file_basic_auth ) )
		{
			$content = $this->read_file( $file_basic_auth );
			
			if ( $content === false )
				return false;
			
			if ( ( $obj->store_basic_auth == 'encrypt' ) && ( !empty( $obj->crypt_key ) ) )
			{
				$content = $this->decrypt( $obj, $content );
				
				if ( $content === false )
					return false;
			}
			
			if ( trim( $content ) != '' )
			{
				$content = @unserialize( $content );
				
				if ( is_array( $content ) )
					$obj->basic_auth = $content;
				else
					return false;
			}
			else
			{
				return false;
			}
		}
		
		return true;
	}

	/**
	 * Will serialize (and encrypt) content, and store on disk
	 *
	 * @param	object	$obj	instance of http_navigator passed by reference
	 * @return	bool
	 */
	function save_files( &$obj )
	{
		if ( $obj->store_name == '' )
			return false;

		$file_cookies    = $obj->store_path . 'cookies.'    . $obj->store_name;
		$file_basic_auth = $obj->store_path . 'basic_auth.' . $obj->store_name;

		// cookies
		if ( $obj->store_cookies )
		{
			$content = serialize( $obj->cookie );
			
			if ( ( $obj->store_cookies == 'encrypt' ) && ( !empty( $obj->crypt_key ) ) )
			{
				$content = $this->encrypt( $obj, $content );
				
				if ( $content === false )
					return false;
			}
			
			$result = $this->write_file( $file_cookies, $content );
			
			if ( $result === false )
				return false;
		}

		// basic auth
		if ( $obj->store_basic_auth )
		{
			$content = serialize( $obj->basic_auth );
			
			if ( ( $obj->store_basic_auth == 'encrypt' ) && ( !empty( $obj->crypt_key ) ) )
			{
				$content = $this->encrypt( $obj, $content );
				
				if ( $content === false )
					return false;
			}
			
			$result = $this->write_file( $file_basic_auth, $content );
			
			if ( $result === false )
				return false;
		}
		
		return true;
	}

	/**
	 * Will delete storage files from disk
	 *
	 * @param	object	$obj	instance of http_navigator passed by reference
	 * @return	bool
	 */
	function del_files( &$obj )
	{
		if ( $obj->store_name == '' )
			return false;

		$file_cookies    = $obj->store_path . 'cookies.'    . $obj->store_name;
		$file_basic_auth = $obj->store_path . 'basic_auth.' . $obj->store_name;

		@unlink( $file_cookies    );
		@unlink( $file_basic_auth );
		
		return true;
	}

	/**
	 * Encrypts string with $obj->crypt_key
	 *
	 * @param	object	$obj	instance of http_navigator passed by reference
	 * @param	string	$string	string to encrypt
	 * @return	string			encrypted string
	 * @access	private
	 */
	function encrypt( &$obj, $string )
	{
		if ( !Util::extensionExists( 'mcrypt' ) )
			return false;
		
		$iv = @mcrypt_create_iv( @mcrypt_get_iv_size( MCRYPT_TripleDES, MCRYPT_MODE_ECB ), MCRYPT_RAND );
		
		if ( !$iv )
			return false;
		
		$encrypted = @mcrypt_encrypt( MCRYPT_TripleDES, $obj->crypt_key, $string, MCRYPT_MODE_ECB, $iv );
		
		if ( !$encrypted )
			return false;
		
		return $encrypted;
	}

	/**
	 * Decrypt encrypted string with $obj->crypt_key
	 *
	 * @param	object	$obj	instance of http_navigator passed by reference
	 * @param	string	$string	encrypted string to decrypt
	 * @return	string			decrypted string
	 * @access	private
	 */
	function decrypt( &$obj, $string )
	{
		if ( !Util::extensionExists( 'mcrypt' ) )
			return false;
		
		$iv = @mcrypt_create_iv( @mcrypt_get_iv_size( MCRYPT_TripleDES, MCRYPT_MODE_ECB ), MCRYPT_RAND );
		
		if ( !$iv )
			return false;
		
		$decrypted = @mcrypt_decrypt( MCRYPT_TripleDES, $obj->crypt_key, $string, MCRYPT_MODE_ECB, $iv );
		
		if ( !$iv )
			return false;
		
		return trim( $decrypted );
	}
} // END OF HTTPStorage

?>
