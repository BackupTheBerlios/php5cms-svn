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
 
class Crypting_azdg extends Crypting
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
	 * @param array
	 *      o key    string
	 *
	 * @access public
	 */
	function Crypting_azdg( $options = array() )
	{
		$this->Crypting( $options );
		
		if ( isset( $options['key'] ) )
			$this->setKey( $options['key'] );
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
      
	  	$r = md5( rand( 0, 32000 ) ); 
      	$c = 0; 
      	$v = ""; 
      
	  	for ( $i = 0; $i < strlen( $plaintext ); $i++ )
		{ 
         	if ( $c == strlen( $r ) )
				$c = 0;
				 
         	$v .= substr( $r, $c, 1 ) . ( substr( $plaintext, $i, 1 ) ^ substr( $r, $c, 1 ) ); 
			$c++; 
		} 
		
		return base64_encode( $this->_ed( $v, $params['key'] ) ); 
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
			
      	$ciphertext = $this->_ed( base64_decode( $ciphertext ), $params['key'] ); 
      	$v = ""; 
		
		for ( $i = 0; $i < strlen( $ciphertext ); $i++ )
		{ 
         	$md5 = substr( $ciphertext, $i, 1 ); 
         	$i++; 
         	$v .= ( substr( $ciphertext, $i, 1 ) ^ $md5 ); 
      	} 
      
	  	return $v; 
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _ed( $t, $key )
	{ 
      	$r = md5( $key ); 
      	$c = 0; 
      	$v = ""; 
      
	  	for ( $i = 0; $i < strlen( $t ); $i++ )
		{ 
         	if ( $c == strlen( $r ) )
				$c = 0;
				 
         	$v .= substr( $t, $i, 1 ) ^ substr( $r, $c, 1 ); 
         	$c++; 
      	} 
      
	  	return $v; 
   	} 
} // END OF Crypting_azdg

?>
