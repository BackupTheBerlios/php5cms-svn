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
|Authors: Michael Wallner <mike@php.net>                               |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'security.crypt.lib.Crypting' );


/**
 * Allowed 64 characters
 */
$GLOBALS['CRYPTING_APRMD5_64'] = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';


/** 
 * Emulates MD5 encrytion from Apache Portable Runtime (APR)
 * as used by htpasswd binary for generating Authbasic passwd files.
 *
 * Based upon Perl's Crypt::PasswdMD5 by Luis Munoz.
 *
 * @package security_crypt_lib
 */
 
class Crypting_aprmd5 extends Crypting
{
	/**
	 * Indicates if algorithm is reversible.
	 *
	 * @var	   boolean
	 * @access private
	 */
	var $_is_reversible = false;

	
	/**
	 * Constructor
	 *
	 * @param array
	 *      o salt   string
	 *
	 * @access public
	 */
	function Crypting_aprmd5( $options = array() )
	{
		$this->Crypting( $options );
		
		if ( isset( $options['salt'] ) )
			$this->setSalt( $options['salt'] );
	}
	
	
    /**
     * Encrypt string (with given salt).
     *
     * @param    string  $plaintext     the sting to encrypt
     * @param    array   $params        (salt - the salt to use for encryption)
	 * @return   string  encrypted passwd
	 * @access   public
     */
    function encrypt( $plaintext, $params = array() )
	{
		if ( empty( $params['salt'] ) )
			$params['salt'] = $this->getSalt();	
			
        if ( is_null( $params['salt'] ) )
            $params['salt'] = Crypting_aprmd5::_genSalt();
        else if ( preg_match( '/^\$apr1\$/', $params['salt'] ) )
            $params['salt'] = preg_replace( '/^\$apr1\$(.{8}).*/', '\\1', $params['salt'] );
        else
            $params['salt'] = substr( $params['salt'], 0, 8 );
        
        $length  = strlen( $plaintext );
        $context = $plaintext . '$apr1$' . $params['salt'];
        $binary  = Crypting_aprmd5::_bin( md5( $plaintext . $params['salt'] . $plaintext ) );
        
        for ( $i = $length; $i > 0; $i -= 16 )
            $context .= substr( $binary, 0, ( $i > 16? 16 : $i ) );
        
        for ( $i = $length; $i > 0; $i >>= 1 )
            $context .= ( $i & 1 )? chr( 0 ) : $plaintext[0];
        
        $binary = Crypting_aprmd5::_bin( md5( $context ) );
        
        for ( $i = 0; $i < 1000; $i++ ) 
		{
            $new = ( $i & 1 )? $plaintext : substr( $binary, 0, 16 );
			
            if ( $i % 3 )
                $new .= $params['salt'];
            
            if ( $i % 7 )
                $new .= $plaintext;
            
            $new .= ( $i & 1 )? substr( $binary, 0,16 ) : $plaintext;
            $binary = Crypting_aprmd5::_bin( md5( $new ) );
        }
        
        $p = array();
        for ( $i = 0; $i < 5; $i++ ) 
		{
            $k = $i + 6;
            $j = $i + 12;
            
			if ( $j == 16 )
                $j = 5;
            
            $p[] = Crypting_aprmd5::_to64(
                ( ord( $binary[$i] ) << 16 ) |
                ( ord( $binary[$k] ) <<  8 ) |
                ( ord( $binary[$j] ) ),
                5
            );
        }
        
        return '$apr1$' . $params['salt'] . '$' . implode( $p ) . Crypting_aprmd5::_to64( ord( $binary[11] ), 3 );
    }
	
	
	// private methods
	
    /**
     * Convert to allowed 64 characters.
     *
     * @param    string  $value
     * @param    int     $count
	 * @return   string
	 * @access   private
     */
    function _to64( $value, $count )
	{
        $result = '';
        $count  = abs( $count );
		
        while ( --$count ) 
		{
            $result  .= $GLOBALS['CRYPTING_APRMD5_64'][$value & 0x3f];
            $value  >>= 6;
        }
		
        return $result;
    }
    
    /**
     * Convert hexadecimal string to binary data.
     *
     * @param    string  $hex
	 * @return   mixed
	 * @access   private
     */
    function _bin( $hex )
	{
        $rs = '';
        $ln = strlen( $hex );
		
        for ( $i = 0; $i < $ln; $i += 2 )
            $rs .= chr( array_shift( sscanf( substr( $hex, $i, 2 ), '%x' ) ) );
        
        return $rs;
    }
        
    /**
     * Generate salt.
     *
     * @return   string
	 * @access   private
     */
    function _genSalt()
	{
        $rs = '';
		
        for ( $i = 0; $i < 8; $i++ )
            $rs .= $GLOBALS['CRYPTING_APRMD5_64'][rand( 0, 63 )];
        
        return $rs;
    }
} // END OF Crypting_aprmd5

?>
