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


/**
 * Static helper functions.
 *
 * @package db_mem
 */
 
class MemDBUtil
{
	/**
	 * @access public
	 * @static
	 */
  	function encrypt( $text ) 
	{
    	if ( function_exists( "mcrypt_ecb" ) ) 
      		return mcrypt_ecb( MCRYPT_3DES, $this->cryptKey, $text, MCRYPT_ENCRYPT );
		else if ( function_exists( "md5" ) ) 
	  		return MemDBUtil::_simpleCrypt( $text, $this->cryptKey );
	
		return strrev( $text );
  	}
  
 	/**
	 * @access public
	 * @static
	 */
  	function decrypt( $text ) 
	{
    	if ( function_exists( "mcrypt_ecb" ) ) 
      		return mcrypt_ecb( MCRYPT_3DES, $this->cryptKey, $text, MCRYPT_DECRYPT );
		else if ( function_exists( "md5" ) ) 
	  		return MemDBUtil::_simpleDecrypt( $text, $this->cryptKey );
	
		return strrev( $text );
  	}
	
	
	// private methods
	
	/**
	 * @access	private
	 * @static
	 */
	function _simpleCrypt( $text, $key ) 
	{
  		$key = md5( $key );
  		$result = "";
  
  		for ( $i = 0; $i < strlen( $text ); $i += strlen( $key ) ) 
		{
    		$block = substr( $text, $i, strlen( $key ) );
    		$crypt = str_pad( " ", strlen( $block ), " " );
    
			for ( $j = 0; $j < strlen( $block ); $j++ )
      			$crypt[$j] = $block[$j] ^ $key[$j];
    
    		$result .= $crypt;
    		$key = md5( $crypt );
  		}
  
  		return base64_encode( $result );
	}

	/**
	 * @access	private
	 * @static
	 */
	function _simpleDecrypt( $text, $key ) 
	{
  		$text   = base64_decode( $text );
  		$key    = md5( $key );
  		$result = "";
  
  		for ( $i = 0; $i < strlen( $text ); $i += strlen( $key ) ) 
		{
    		$block   = substr( $text, $i, strlen( $key ) );
    		$decrypt = str_pad( "", strlen( $block ), " " );
    
			for ( $j = 0; $j < strlen( $block ); $j++ )
      			$decrypt[$j] = $block[$j] ^ $key[$j];
    
	    	$result .= $decrypt;
    		$key = md5( $block );
  		}
  
  		return $result;
	}
} // END OF MemDBUtil

?>
