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
|         Thomas Stauffer <thomas.stauffer@deepsource.ch>              |
+----------------------------------------------------------------------+
*/


using( 'security.crypt.lib.Crypting' );


/**
 * A conversion of Paul Tero's javascript implementation of DES algorithm.
 *
 * @package security_crypt_lib
 */

class Crypting_des extends Crypting
{
	/**
	 * Indicates if algorithm is reversible.
	 *
	 * @var	   boolean
	 * @access private
	 */
	var $_is_reversible = true;

	/**
	 * @access private
	 */
	var $_keys = array();
	
	/**
	 * @access private
	 */
	var $_mode = 1;
	
	/**
	 * @access private
	 */
	var $_iv = "";

	/**
	 * @access private
	 */
	var $_base64 = 1;
	
	
	/**
	 * Constructor
	 *
	 * @param array
	 *      o key    string
	 *  	o base64 integer (1 means ciphertext is base64 encoded)
	 * 		o mode   integer (0 => EBC Mode, 1 => CBC Mode)
	 * 	 	o iv     string
	 *	
	 * @access public
	 */
	function Crypting_des( $options = array() )
	{
		$this->Crypting( $options );
		
		$this->_mode   = $options['mode']?   $options['mode']   : 1;
		$this->_iv     = $options['iv']?     $options['iv']     : "";
		$this->_base64 = $options['base64']? $options['base64'] : 1;

		$this->_keys   = $this->_createKeys( $options['key'] );
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
		if ( $this->_base64 == 1 )
			return base64_encode( $this->_des_crypt( $plaintext, 1 ) );
		else 
			return $this->_des_crypt( $plaintext, 1 );
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
		if ( $this->_base64 == 1 )
			return $this->_des_crypt( base64_decode( $ciphertext ), 0 );
		else 
			return $this->_des_crypt( $ciphertext, 0 );
	}
	
	/**
	 * Set key.
	 *
	 * @param  string  $key
	 * @access public
	 */
	function setKey( $key )
	{
		$this->_key  = $key;
		$this->_keys = $this->_createKeys( $this->_key );
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _des_crypt( $message, $encrypt = 1 )
	{
		// declaring this locally speeds things up a bit
		$spfunction1 = array( 0x1010400, 0, 0x10000, 0x1010404, 0x1010004, 0x10404, 0x4, 0x10000, 0x400, 0x1010400, 0x1010404, 0x400, 0x1000404, 0x1010004, 0x1000000, 0x4, 0x404, 0x1000400, 0x1000400, 0x10400, 0x10400, 0x1010000, 0x1010000, 0x1000404, 0x10004, 0x1000004, 0x1000004, 0x10004, 0, 0x404, 0x10404, 0x1000000, 0x10000, 0x1010404, 0x4, 0x1010000, 0x1010400, 0x1000000, 0x1000000, 0x400, 0x1010004, 0x10000, 0x10400, 0x1000004, 0x400, 0x4, 0x1000404, 0x10404, 0x1010404, 0x10004, 0x1010000, 0x1000404, 0x1000004, 0x404, 0x10404, 0x1010400, 0x404, 0x1000400, 0x1000400, 0, 0x10004, 0x10400, 0, 0x1010004 );
		$spfunction2 = array( 0x80108020, 0x80008000, 0x8000, 0x108020, 0x100000, 0x20, 0x80100020, 0x80008020, 0x80000020, 0x80108020, 0x80108000, 0x80000000, 0x80008000, 0x100000, 0x20, 0x80100020, 0x108000, 0x100020, 0x80008020, 0, 0x80000000, 0x8000, 0x108020, 0x80100000, 0x100020, 0x80000020, 0, 0x108000, 0x8020, 0x80108000, 0x80100000, 0x8020, 0, 0x108020, 0x80100020, 0x100000, 0x80008020, 0x80100000, 0x80108000, 0x8000, 0x80100000, 0x80008000, 0x20, 0x80108020, 0x108020, 0x20, 0x8000, 0x80000000, 0x8020, 0x80108000, 0x100000, 0x80000020, 0x100020, 0x80008020, 0x80000020, 0x100020, 0x108000, 0, 0x80008000, 0x8020, 0x80000000, 0x80100020, 0x80108020, 0x108000 );
		$spfunction3 = array( 0x208, 0x8020200, 0, 0x8020008, 0x8000200, 0, 0x20208, 0x8000200, 0x20008, 0x8000008, 0x8000008, 0x20000, 0x8020208, 0x20008, 0x8020000, 0x208, 0x8000000, 0x8, 0x8020200, 0x200, 0x20200, 0x8020000, 0x8020008, 0x20208, 0x8000208, 0x20200, 0x20000, 0x8000208, 0x8, 0x8020208, 0x200, 0x8000000, 0x8020200, 0x8000000, 0x20008, 0x208, 0x20000, 0x8020200, 0x8000200, 0, 0x200, 0x20008, 0x8020208, 0x8000200, 0x8000008, 0x200, 0, 0x8020008, 0x8000208, 0x20000, 0x8000000, 0x8020208, 0x8, 0x20208, 0x20200, 0x8000008, 0x8020000, 0x8000208, 0x208, 0x8020000, 0x20208, 0x8, 0x8020008, 0x20200 );
		$spfunction4 = array( 0x802001, 0x2081, 0x2081, 0x80, 0x802080, 0x800081, 0x800001, 0x2001, 0, 0x802000, 0x802000, 0x802081, 0x81, 0, 0x800080, 0x800001, 0x1, 0x2000, 0x800000, 0x802001, 0x80, 0x800000, 0x2001, 0x2080, 0x800081, 0x1, 0x2080, 0x800080, 0x2000, 0x802080, 0x802081, 0x81, 0x800080, 0x800001, 0x802000, 0x802081, 0x81, 0, 0, 0x802000, 0x2080, 0x800080, 0x800081, 0x1, 0x802001, 0x2081, 0x2081, 0x80, 0x802081, 0x81, 0x1, 0x2000, 0x800001, 0x2001, 0x802080, 0x800081, 0x2001, 0x2080, 0x800000, 0x802001, 0x80, 0x800000, 0x2000, 0x802080 );
		$spfunction5 = array( 0x100, 0x2080100, 0x2080000, 0x42000100, 0x80000, 0x100, 0x40000000, 0x2080000, 0x40080100, 0x80000, 0x2000100, 0x40080100, 0x42000100, 0x42080000, 0x80100, 0x40000000, 0x2000000, 0x40080000, 0x40080000, 0, 0x40000100, 0x42080100, 0x42080100, 0x2000100, 0x42080000, 0x40000100, 0, 0x42000000, 0x2080100, 0x2000000, 0x42000000, 0x80100, 0x80000, 0x42000100, 0x100, 0x2000000, 0x40000000, 0x2080000, 0x42000100, 0x40080100, 0x2000100, 0x40000000, 0x42080000, 0x2080100, 0x40080100, 0x100, 0x2000000, 0x42080000, 0x42080100, 0x80100, 0x42000000, 0x42080100, 0x2080000, 0, 0x40080000, 0x42000000, 0x80100, 0x2000100, 0x40000100, 0x80000, 0, 0x40080000, 0x2080100, 0x40000100 );
		$spfunction6 = array( 0x20000010, 0x20400000, 0x4000, 0x20404010, 0x20400000, 0x10, 0x20404010, 0x400000, 0x20004000, 0x404010, 0x400000, 0x20000010, 0x400010, 0x20004000, 0x20000000, 0x4010, 0, 0x400010, 0x20004010, 0x4000, 0x404000, 0x20004010, 0x10, 0x20400010, 0x20400010, 0, 0x404010, 0x20404000, 0x4010, 0x404000, 0x20404000, 0x20000000, 0x20004000, 0x10, 0x20400010, 0x404000, 0x20404010, 0x400000, 0x4010, 0x20000010, 0x400000, 0x20004000, 0x20000000, 0x4010, 0x20000010, 0x20404010, 0x404000, 0x20400000, 0x404010, 0x20404000, 0, 0x20400010, 0x10, 0x4000, 0x20400000, 0x404010, 0x4000, 0x400010, 0x20004010, 0, 0x20404000, 0x20000000, 0x400010, 0x20004010 );
		$spfunction7 = array( 0x200000, 0x4200002, 0x4000802, 0, 0x800, 0x4000802, 0x200802, 0x4200800, 0x4200802, 0x200000, 0, 0x4000002, 0x2, 0x4000000, 0x4200002, 0x802, 0x4000800, 0x200802, 0x200002, 0x4000800, 0x4000002, 0x4200000, 0x4200800, 0x200002, 0x4200000, 0x800, 0x802, 0x4200802, 0x200800, 0x2, 0x4000000, 0x200800, 0x4000000, 0x200800, 0x200000, 0x4000802, 0x4000802, 0x4200002, 0x4200002, 0x2, 0x200002, 0x4000000, 0x4000800, 0x200000, 0x4200800, 0x802, 0x200802, 0x4200800, 0x802, 0x4000002, 0x4200802, 0x4200000, 0x200800, 0, 0x2, 0x4200802, 0, 0x200802, 0x4200000, 0x800, 0x4000002, 0x4000800, 0x800, 0x200002 );
		$spfunction8 = array( 0x10001040, 0x1000, 0x40000, 0x10041040, 0x10000000, 0x10001040, 0x40, 0x10000000, 0x40040, 0x10040000, 0x10041040, 0x41000, 0x10041000, 0x41040, 0x1000, 0x40, 0x10040000, 0x10000040, 0x10001000, 0x1040, 0x41000, 0x40040, 0x10040040, 0x10041000, 0x1040, 0, 0, 0x10040040, 0x10000040, 0x10001000, 0x41040, 0x40000, 0x41040, 0x40000, 0x10041000, 0x1000, 0x40, 0x10040040, 0x1000, 0x41040, 0x10001000, 0x40, 0x10000040, 0x10040000, 0x10040040, 0x10000000, 0x40000, 0x10001040, 0, 0x10041040, 0x40040, 0x10000040, 0x10040000, 0x10001000, 0x10001040, 0, 0x10041040, 0x41000, 0x41000, 0x1040, 0x1040, 0x40040, 0x10000000, 0x10041000 );
		
		// create the 16 subkeys we will need
		$m         = 0;
		$i         = 0;
		$temp      = "";
		$temp2     = "";
		$right1    = "";
		$right2    = "";
		$left      = "";
		$right     = "";
		$cbcleft   = "";
		$cbcleft2  = "";
		$cbcright  = "";
		$cbcright2 = "";
		$kcount    = 0;
		$kbcount   = 0;
		$startloop = $encrypt?  0 : 30;
		$endloop   = $encrypt? 32 : -2;
		$loopinc   = $encrypt?  2 : -2;
		$len       = strlen( $message );
		$chunk     = 0;
		
		// pad the message out with spaces
		$message .= "        ";

		// store the result here
		$result = "";
		$tempresult = "";
		
		// CBC mode
		if ( $this->_mode == 1 )
		{ 
			$cbcleft  = ( ord( substr( $this->_iv, $m++, 1 ) ) << 24 ) | ( ord( substr( $this->_iv, $m++, 1 ) ) << 16 ) | ( ord( substr( $this->_iv, $m++, 1 ) ) << 8 ) | ord( substr( $this->_iv, $m++, 1 ) );
			$cbcright = ( ord( substr( $this->_iv, $m++, 1 ) ) << 24 ) | ( ord( substr( $this->_iv, $m++, 1 ) ) << 16 ) | ( ord( substr( $this->_iv, $m++, 1 ) ) << 8 ) | ord( substr( $this->_iv, $m++, 1 ) );
			
			$m = 0;
		}
		
		// loop through each 64 bit chunk of the message
		while ( $m < $len )
		{
			$left  = ( ord( substr( $message, $m++, 1 ) ) << 24 ) | ( ord( substr( $message, $m++, 1 ) ) << 16 ) | ( ord( substr( $message, $m++, 1 ) ) << 8 ) | ord( substr( $message, $m++, 1 ) );
			$right = ( ord( substr( $message, $m++, 1 ) ) << 24 ) | ( ord( substr( $message, $m++, 1 ) ) << 16 ) | ( ord( substr( $message, $m++, 1 ) ) << 8 ) | ord( substr( $message, $m++, 1 ) );
			
			// for Cipher Block Chaining mode, xor the message with the previous result
			if ( $this->_mode == 1 )
			{
				if ( $encrypt )
				{
					$left  ^= $cbcleft;
					$right ^= $cbcright;
				}
				else
				{
					$cbcleft2  = $cbcleft;
					$cbcright2 = $cbcright; 
					$cbcleft   = $left;
					$cbcright  = $right;
				}
			}
			
			// for Cipher Feedback mode, get k bits from the input
			if ( $this->_mode == 2 )
			{
				// get k bits from kcount, which is within kbcount
			}
			
			// first each 64 but chunk of the message must be permuted according to IP
			
			$temp   = ( $this->_zeroFill( $left, 4 ) ^ $right ) & 0x0f0f0f0f;
			$right ^= $temp;
			$left  ^= ( $temp << 4 );
		
			$temp   = ( $this->_zeroFill( $left, 16 ) ^ $right ) & 0x0000ffff;
			$right ^= $temp; 
			$left  ^= ( $temp << 16 );

			$temp   = ( $this->_zeroFill( $right, 2 ) ^ $left ) & 0x33333333; 
			$left  ^= $temp; 
			$right ^= ( $temp << 2 );

			$temp   = ( $this->_zeroFill( $right, 8 ) ^ $left ) & 0x00ff00ff; 
			$left  ^= $temp; 
			$right ^= ( $temp << 8 );
			
			$temp   = ( $this->_zeroFill( $left, 1 ) ^ $right ) & 0x55555555; 
			$right ^= $temp; 
			$left  ^= ( $temp << 1 );
			
			$left   = ( ( $left  << 1 ) | $this->_zeroFill( $left,  31 ) ); 
			$right  = ( ( $right << 1 ) | $this->_zeroFill( $right, 31 ) ); 
			
			
			// now go through and perform the encryption or decryption  
			for ( $i = $startloop; $i != $endloop; $i += $loopinc )
			{
				$right1 = $right ^ $this->_keys[$i]; 
				$right2 = ( $this->_zeroFill( $right, 4 ) | ( $right << 28 ) ) ^ $this->_keys[$i + 1];
				
				// the result is attained by passing these bytes through the S selection functions
				$temp  = $left;
				$left  = $right;
				$right = $temp ^ ( $spfunction2[$this->_zeroFill( $right1, 24 ) & 0x3f] | 
								   $spfunction4[$this->_zeroFill( $right1, 16 ) & 0x3f] | 
								   $spfunction6[$this->_zeroFill( $right1,  8 ) & 0x3f] | 
								   $spfunction8[$right1 & 0x3f] | 
								   $spfunction1[$this->_zeroFill( $right2, 24 ) & 0x3f] | 
								   $spfunction3[$this->_zeroFill( $right2, 16 ) & 0x3f] | 
								   $spfunction5[$this->_zeroFill( $right2,  8 ) & 0x3f] | 
								   $spfunction7[$right2 & 0x3f] );
			}
			
			// move then each one bit to the right
			$right = ( $this->_zeroFill( $right, 1 ) | ( $right << 31 ) ); 
			$left  = ( $this->_zeroFill( $left,  1 ) | ( $left  << 31 ) ); 
			
			// now perform IP-1, which is IP in the opposite direction
			$temp   = ( $this->_zeroFill( $right, 1 ) ^ $left ) & 0x55555555;
			$left  ^= $temp; 
			$right ^= ( $temp << 1 );
			
			$temp   = ( $this->_zeroFill( $left, 8 ) ^ $right ) & 0x00ff00ff; 
			$right ^= $temp; 
			$left  ^= ( $temp << 8 );
			
			$temp   = ( $this->_zeroFill( $left, 2 ) ^ $right ) & 0x33333333; 
			$right ^= $temp; 
			$left  ^= ( $temp << 2 );
			
			$temp   = ( $this->_zeroFill( $right, 16 ) ^ $left ) & 0x0000ffff; 
			$left  ^= $temp; 
			$right ^= ( $temp << 16 );
			
			$temp   = ( $this->_zeroFill( $right, 4 ) ^ $left ) & 0x0f0f0f0f; 
			$left  ^= $temp; 
			$right ^= ( $temp << 4 );
			
			// for Cipher Block Chaining mode, xor the message with the previous result
			if ( $this->_mode == 1 )
			{
				if ( $encrypt )
				{
					$cbcleft  = $right; 
					$cbcright = $left;
				}
				else
				{
					$right ^= $cbcleft2; 
					$left  ^= $cbcright2;
				}
			}
			
			$tempresult .= $this->_fromCharCode( array(
				  $this->_zeroFill( $right, 24 ), 
				( $this->_zeroFill( $right, 16 ) & 0xff ), 
				( $this->_zeroFill( $right,  8 ) & 0xff ), 
				( $right & 0xff ), 
				  $this->_zeroFill( $left,  24 ), 
				( $this->_zeroFill( $left,  16 ) & 0xff ), 
				( $this->_zeroFill( $left,   8 ) & 0xff ), 
				( $left & 0xff )
			) );
			
			$chunk += 8;
			
			if ( $chunk == 512 )
			{
				$result .= $tempresult;
				$tempresult = "";
				$chunk = 0;
			}
		} // for every 8 characters, or 64 bits in the message
		
		// return the result as an array
		return $result . $tempresult;
	}
	
	/**
	 * This takes as input a 64 bit key (even though only 56 bits are used)
	 * as an array of 2 integers, and returns 16 48 bit keys.
	 *
	 * @access private
	 */
	function _createKeys( $key )
	{
		//declaring this locally speeds things up a bit
		$pc2bytes0  = array( 0, 0x4, 0x20000000, 0x20000004, 0x10000, 0x10004, 0x20010000, 0x20010004, 0x200, 0x204, 0x20000200, 0x20000204, 0x10200, 0x10204, 0x20010200, 0x20010204 );
		$pc2bytes1  = array( 0, 0x1, 0x100000, 0x100001, 0x4000000, 0x4000001, 0x4100000, 0x4100001, 0x100, 0x101, 0x100100, 0x100101, 0x4000100, 0x4000101, 0x4100100, 0x4100101 );
		$pc2bytes2  = array( 0, 0x8, 0x800, 0x808, 0x1000000, 0x1000008, 0x1000800, 0x1000808, 0, 0x8, 0x800, 0x808, 0x1000000, 0x1000008, 0x1000800, 0x1000808 );
		$pc2bytes3  = array( 0, 0x200000, 0x8000000, 0x8200000, 0x2000, 0x202000, 0x8002000, 0x8202000, 0x20000, 0x220000, 0x8020000, 0x8220000, 0x22000, 0x222000, 0x8022000, 0x8222000 );
		$pc2bytes4  = array( 0, 0x40000, 0x10, 0x40010, 0, 0x40000, 0x10, 0x40010, 0x1000, 0x41000, 0x1010, 0x41010, 0x1000, 0x41000, 0x1010, 0x41010 );
		$pc2bytes5  = array( 0, 0x400, 0x20, 0x420, 0, 0x400, 0x20, 0x420, 0x2000000, 0x2000400, 0x2000020, 0x2000420, 0x2000000, 0x2000400, 0x2000020, 0x2000420 );
		$pc2bytes6  = array( 0, 0x10000000, 0x80000, 0x10080000, 0x2, 0x10000002, 0x80002, 0x10080002, 0, 0x10000000, 0x80000, 0x10080000, 0x2, 0x10000002, 0x80002, 0x10080002 );
		$pc2bytes7  = array( 0, 0x10000, 0x800, 0x10800, 0x20000000, 0x20010000, 0x20000800, 0x20010800, 0x20000, 0x30000, 0x20800, 0x30800, 0x20020000, 0x20030000, 0x20020800, 0x20030800 );
		$pc2bytes8  = array( 0, 0x40000, 0, 0x40000, 0x2, 0x40002, 0x2, 0x40002, 0x2000000, 0x2040000, 0x2000000, 0x2040000, 0x2000002, 0x2040002, 0x2000002, 0x2040002 );
		$pc2bytes9  = array( 0, 0x10000000, 0x8, 0x10000008, 0, 0x10000000, 0x8, 0x10000008, 0x400, 0x10000400, 0x408, 0x10000408, 0x400, 0x10000400, 0x408, 0x10000408 );
		$pc2bytes10 = array( 0, 0x20, 0, 0x20, 0x100000, 0x100020, 0x100000, 0x100020, 0x2000, 0x2020, 0x2000, 0x2020, 0x102000, 0x102020, 0x102000, 0x102020 );
		$pc2bytes11 = array( 0, 0x1000000, 0x200, 0x1000200, 0x200000, 0x1200000, 0x200200, 0x1200200, 0x4000000, 0x5000000, 0x4000200, 0x5000200, 0x4200000, 0x5200000, 0x4200200, 0x5200200 );
		$pc2bytes12 = array( 0, 0x1000, 0x8000000, 0x8001000, 0x80000, 0x81000, 0x8080000, 0x8081000, 0x10, 0x1010, 0x8000010, 0x8001010, 0x80010, 0x81010, 0x8080010, 0x8081010 );
		$pc2bytes13 = array( 0, 0x4, 0x100, 0x104, 0, 0x4, 0x100, 0x104, 0x1, 0x5, 0x101, 0x105, 0x1, 0x5, 0x101, 0x105 );
		
		// stores the return keys
		$keys = array( 32 );
		
		// now define the left shifts which need to be done
		$shifts = array( 0, 0, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 0 );
		
		$lefttemp  = "";
		$righttemp = "";
		
		$m     = 0;
		$temp  = "";
		
		$left  = ( ord( substr( $key, $m++ ) ) << 24 ) | ( ord( substr( $key, $m++ ) ) << 16 ) | ( ord( substr( $key, $m++ ) ) << 8 ) | ord( substr( $key, $m++ ) );
		$right = ( ord( substr( $key, $m++ ) ) << 24 ) | ( ord( substr( $key, $m++ ) ) << 16 ) | ( ord( substr( $key, $m++ ) ) << 8 ) | ord( substr( $key, $m++ ) );
		
		
		$temp   = ( $this->_zeroFill( $left, 4 ) ^ $right ) & 0x0f0f0f0f; 
		$right ^= $temp; 
		$left  ^= ( $temp << 4 );
		
		$temp   = ( $this->_zeroFill( $right, -16 ) ^ $left ) & 0x0000ffff; 
		$left  ^= $temp; 
		$right ^= ( $temp << -16 );
		
		$temp   = ( $this->_zeroFill( $left, 2 ) ^ $right ) & 0x33333333; 
		$right ^= $temp; 
		$left  ^= ( $temp << 2 );
		
		$temp   = ( $this->_zeroFill( $right, -16 ) ^ $left ) & 0x0000ffff; 
		$left  ^= $temp; 
		$right ^= ( $temp << -16 );
		
		$temp   = ( $this->_zeroFill( $left, 1 ) ^ $right ) & 0x55555555; 
		$right ^= $temp; 
		$left  ^= ( $temp << 1 );
		
		$temp   = ( $this->_zeroFill( $right, 8 ) ^ $left ) & 0x00ff00ff; 
		$left  ^= $temp; 
		$right ^= ( $temp << 8 );
		
		$temp   = ( $this->_zeroFill( $left, 1 ) ^ $right ) & 0x55555555; 
		$right ^= $temp; 
		$left  ^= ( $temp << 1 );
		
		// the right side needs to be shifted and to get the last four bits of the left side
		$temp = ( $left << 8 ) | ( $this->_zeroFill( $right, 20 ) & 0x000000f0 );
		
		// left needs to be put upside down
		$left  = ( $right << 24 ) | ( ( $right << 8 ) & 0xff0000 ) | ( $this->_zeroFill( $right, 8 ) & 0xff00 ) | ( $this->_zeroFill( $right, 24 ) & 0xf0 );
		$right = $temp;
		
		//now go through and perform these shifts on the left and right keys
		$m = 0;
		
		for ( $i = 0; $i < count( $shifts ); $i++ )
		{
			// shift the keys either one or two bits to the left
			if ( $shifts[$i] )
			{
				$left  = ( $left  << 2 ) | $this->_zeroFill( $left,  26 );
				$right = ( $right << 2 ) | $this->_zeroFill( $right, 26 );
			}
			else
			{
				$left  = ( $left  << 1 ) | $this->_zeroFill( $left,  27 ); 
				$right = ( $right << 1 ) | $this->_zeroFill( $right, 27 );
			}
			
			$left  &= 0xfffffff0; 
			$right &= 0xfffffff0;
			
			// now apply PC-2, in such a way that E is easier when encrypting or decrypting
			// this conversion will look like PC-2 except only the last 6 bits of each byte are used
			// rather than 48 consecutive bits and the order of lines will be according to 
			// how the S selection functions will be applied: S2, S4, S6, S8, S1, S3, S5, S7
			$lefttemp  = $pc2bytes0[  $this->_zeroFill( $left,  28 ) ] | 
						 $pc2bytes1[  $this->_zeroFill( $left,  24 ) & 0xf ] | 
						 $pc2bytes2[  $this->_zeroFill( $left,  20 ) & 0xf ] | 
						 $pc2bytes3[  $this->_zeroFill( $left,  16 ) & 0xf ] | 
						 $pc2bytes4[  $this->_zeroFill( $left,  12 ) & 0xf ] | 
						 $pc2bytes5[  $this->_zeroFill( $left,   8 ) & 0xf ] | 
						 $pc2bytes6[  $this->_zeroFill( $left,   4 ) & 0xf ];
			
			$righttemp = $pc2bytes7[  $this->_zeroFill( $right, 28 ) ] | 
						 $pc2bytes8[  $this->_zeroFill( $right, 24 ) & 0xf ] | 
						 $pc2bytes9[  $this->_zeroFill( $right, 20 ) & 0xf ] | 
						 $pc2bytes10[ $this->_zeroFill( $right, 16 ) & 0xf ] | 
						 $pc2bytes11[ $this->_zeroFill( $right, 12 ) & 0xf ] | 
						 $pc2bytes12[ $this->_zeroFill( $right,  8 ) & 0xf ] | 
						 $pc2bytes13[ $this->_zeroFill( $right,  4 ) & 0xf ];
			
			$temp = ( $this->_zeroFill( $righttemp, 16 ) ^ $lefttemp ) & 0x0000ffff; 
			$keys[$m++] = $lefttemp  ^ $temp; 
			$keys[$m++] = $righttemp ^ ( $temp << 16 );
		}
		
		return $keys;
	}

	/**
	 * Creates a string specified in param.
	 *
	 * @param  array  $param Array of ascii codes
	 * @return string
	 * @access private
	 */
	function _fromCharCode( $param )
	{
		$return = "";
		
		if ( is_array( $param ) )
		{
			foreach ( $param as $ord )
				$return .= chr( $ord );
		}
		else 
		{
			$return = chr( $param );
		}
		
		return $return;
	}
	
	/**
	 * Alternative to the zero fill shift right operator in js.
	 *
	 * @access private
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
} // END OF Crypting_des

?>
