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

 
using( 'security.checksum.lib.Checksum' );
  

/**
 * A php implementation of the Secure Hash Algorithm, SHA-1, as defined
 * in FIPS PUB 180-1. Uses mhash functions if available.
 *
 * @package security_checksum_lib
 */

class Checksum_sha1 extends Checksum
{
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	function Checksum_sha1( $value = '' )
	{
		$this->Checksum( $value );
	}
	
	
    /**
     * Create a new checksum from a string.
     *
     * @access  public
     * @param   string str
     * @return  Checksum_sha1
     */
    function &fromString( $str ) 
	{
		if ( function_exists( 'sha1' ) )
      		return new Checksum_sha1( sha1( $str ) );
		else if ( Checksum::useMHash() )
			return new Checksum_sha1( bin2hex( mhash( MHASH_SHA1, $str ) ) );
		else
			return new Checksum_sha1( Checksum_sha1::_hash( $str ) );
    }

    /**
     * Create a new checksum from a file.
     *
     * @access  public
     * @param   file
     * @return  Checksum_sha1
     */
    function &fromFile( $file ) 
	{
		if ( function_exists( 'sha1_file' ) )
		{
			return new Checksum_sha1( sha1_file( $file ) );
		}
		else
		{
			$data = Checksum::_getFile( $file );			
			return Checksum_sha1::fromString( $data );
		}
    }
	
	
	// private methods
	
	/**
	 * Hashes a string using SHA-1 und returns the hexadecimal conversion of it.
	 * Uses mhash library if avaible.
	 *
	 * @param  str string
	 * @return sha1_hash string
	 * @access private
	 * @static 
	 */
	function _hash($str)
	{
		$x = Checksum_sha1::_str2blks_SHA1( $str );
		$w = array( 80 );
			
		$a =  1732584193;
		$b = -271733879;
		$c = -1732584194;
		$d =  271733878;
		$e = -1009589776;
			
		for ( $i = 0; $i < count( $x ); $i += 16 )
		{
			$olda = $a;
			$oldb = $b;
			$oldc = $c;
			$oldd = $d;
			$olde = $e;
				
			for ( $j = 0; $j < 80; $j++ )
			{
				if ( $j < 16 ) 
					$w[$j] = $x[( $i + $j )];
				else 
					$w[$j] = Checksum_sha1::_rol( ( $w[( $j - 3 )] ^ $w[( $j - 8 )] ^ $w[( $j - 14 )] ^ $w[( $j - 16 )] ), 1 );
					
				$t = Checksum_sha1::_add( Checksum_sha1::_add( Checksum_sha1::_rol( $a, 5 ), Checksum_sha1::_ft( $j, $b, $c, $d ) ), Checksum_sha1::_add( Checksum_sha1::_add( $e, $w[$j] ), Checksum_sha1::_kt( $j ) ) );
				$e = $d;
				$d = $c;
				$c = Checksum_sha1::_rol( $b, 30 );
				$b = $a;
				$a = $t;
			}
				
			$a = Checksum_sha1::_add( $a, $olda );
			$b = Checksum_sha1::_add( $b, $oldb );
			$c = Checksum_sha1::_add( $c, $oldc );
			$d = Checksum_sha1::_add( $d, $oldd );
			$e = Checksum_sha1::_add( $e, $olde );
		}
			
		return Checksum_sha1::_hex( $a ) . Checksum_sha1::_hex( $b ) . Checksum_sha1::_hex( $c ) . Checksum_sha1::_hex( $d ) . Checksum_sha1::_hex( $e );
	}
	
	/**
	 * Alternative to the zero fill shift right operator.
	 *
	 * @access private
	 * @static 
	 */
	function _zeroFill( $a, $b )
	{
		$z = hexdec( 80000000 );
		
		if ( $z & $a )
		{
			$a >>= 1;
			$a  &= (~ $z);
			$a  |= 0x40000000;
			$a >>= ( $b - 1 );
		}
		else
		{
			$a >>= $b;
		}
		
		return $a;
	}
	
	/**
	 * Conversion decimal to hexadecimal.
	 *
	 * @param  decnum integer
	 * @return hexstr string
	 * @access private
	 * @static 
	 */
	function _hex( $decnum )
	{
		$hexstr = dechex( $decnum );
		
		if ( strlen( $hexstr ) < 8 ) 
			$hexstr = str_repeat( "0", 8 - strlen( $hexstr ) ) . $hexstr;
		
		return $hexstr;
	}

	/**
	 * Divides a string into 16-word blocks.
	 *
	 * @param  str string
	 * @return blocks array
	 * @access private
	 * @static 
	 */
	function _str2blks_SHA1( $str )
	{
		$nblk = ( ( strlen( $str ) + 8 ) >> 6 ) + 1;
		$blks = array( $nblk * 16 );
		
		for ( $i = 0; $i < $nblk * 16; $i++ ) 
			$blks[$i] = 0;
			
		for ( $i = 0; $i < strlen( $str ); $i++ ) 
			$blks[($i>>2)] |= ord( substr( $str, $i, 1 ) ) << ( 24 - ( $i % 4 ) * 8 );
			
		$blks[( $i >> 2 )] |= 0x80 << ( 24 - ( $i % 4 ) * 8 );
		$blks[( $nblk * 16 - 1 )] = strlen( $str ) * 8;
		
		return $blks;
	}

	/**
	 * Add integers, wrapping at 2^32. This uses 16-bit operations internally.
	 *
	 * @access private
	 * @static 
	 */
	function _add( $x, $y )
	{
		$lsw = ( $x & 0xFFFF ) + ( $y & 0xFFFF );
		$msw = ( $x >> 16 ) + ( $y >> 16 ) + ( $lsw >> 16 );
		
		return ( $msw << 16 ) | ( $lsw & 0xFFFF );
	}

	/**
	 * Bitwise rotate a 32-bit number to the left.
	 *
	 * @access private
	 * @static 
	 */
	function _rol( $num, $cnt )
	{
		return ( $num << $cnt ) | Checksum_sha1::_zeroFill( $num, ( 32 - $cnt ) );
	}

	/**
	 * Perform the appropriate triplet combination function for the current iteration.
	 *
	 * @access private
	 * @static 
	 */
	function _ft( $t, $b, $c, $d )
	{
		if ( $t < 20 ) 
			return ( $b & $c ) | ( ( ~$b ) & $d );
		else if ( $t < 40 ) 
			return $b ^ $c ^ $d;
		else if ( $t < 60 ) 
			return ( $b & $c ) | ( $b & $d ) | ( $c & $d );
		else 
			return $b ^ $c ^ $d;
	}

	/**
	 * Determine the appropriate additive constant for the current iteration.
	 *
	 * @access private
	 * @static 
	 */
	function _kt( $t )
	{
		if ( $t < 20 ) 
			return 1518500249;
		else if ( $t < 40 ) 
			return 1859775393;
		else if ( $t < 60 ) 
			return -1894007588;
		else 
			return -899497514;
	}
} // END OF Checksum_sha1

?>
