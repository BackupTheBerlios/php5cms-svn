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
using( 'util.math.Random' );
using( 'util.math.Primes' );
using( 'util.math.BCMath' );


/**
 * Implementation of RSA (Rivest, Shamir, Adleman)
 *
 * @package security_crypt_lib
 */
 
class Crypting_rsa extends Crypting
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
	 * @access public
	 */
	function Crypting_rsa( $options = array() )
	{
		$this->Crypting( $options );
		
		/*
		// Small Random Values
		// p = 6 Digits
		// q = 6 Digits
		// n = 12 Digits

		$this->p = '735373';
		$this->q = '861299';
		$this->n = '633376029527';
		$this->e = '5';
		$this->d = '190012329857';
		*/

		// Large Random Values
		// p = 156 Digits
		// q = 156 Digits
		// n = 311 Digits

		$this->p = '112122319636296693236328719169899838626328749276497698636428964892764987628947284689767896198627631362193628917631876861638713612863128736871367617231624021';
		$this->q = '230894773429857093247592384750894478907890078910789127890347089123470894230478123970148923780491237048923107489237048923702431071248930427891302479138079339';
		$this->n = '25888457588852741952488701308748538874089526050672952264258339115744304723418792934329113460307935536759676864246123133627758090849960267882953064728302994798904546591622037492292909029328108154021848243886870818415342910823139324658184437124585518989851994210609706251488247515828591268959942726595713716202119';
		$this->e = '7';
		$this->d = '11095053252365460836780871989463659517466939736002693827539288192461844881465196971855334340131972372897004370391195628697610610364268686235551313454986997623951765796629250432159345046514481694201294309971447553559695240537396056528332475946185231373278678468802842592526769503435960210100333413110978862785183';
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
		$iBlockSizeSource = $this->blockSize() - 1;
		$iBlockSizeDestination = $iBlockSizeSource + 1;

		for ( $i = 0; $i < strlen( $plaintext ); $i += $iBlockSizeSource )
			$aChunk[] = substr( $plaintext, $i, $iBlockSizeSource );

		for ( $i = 0; $i < count( $aChunk ); $i++ )
			$aChunk[$i] = Crypting::intToOctetString( Crypting_rsa::_encryptRSA( $this->n, $this->e, Crypting::octetStringToInt( $aChunk[$i] ) ), $iBlockSizeDestination );

		return implode( '', $aChunk );
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
		$iBlockSizeSource = $this->blockSize() - 1;
		$iBlockSizeDestination = $iBlockSizeSource + 1;

		for ( $i = 0; $i < strlen( $ciphertext ); $i += $iBlockSizeDestination )
			$aChunk[] = substr( $ciphertext, $i, $iBlockSizeDestination );

		for ( $i = 0; $i < count( $aChunk ); $i++ )
			$aChunk[$i] = Crypting::intToOctetString( Crypting_rsa::_decryptRSA( $this->n, $this->d, Crypting::octetStringToInt( $aChunk[$i] ) ), $iBlockSizeSource );

		return implode( '', $aChunk );
	}
	
	/**
	 * @access public
	 */
	function createPrimes( $sSeed, $iBits = 1024 )
	{
		$cRandom = new Random();		
		$cRandom->seed( $sSeed );

		$this->p = Primes::search( $cRandom->rndm( $iBits / 2 ) );
		$this->q = Primes::search( $cRandom->rndm( $iBits / 2 ) );
		
		if ( bccomp( $this->p, $this->q ) > 0 )
			Crypting::swap( $this->p, $this->q );
	}

	/**
	 * @access public
	 */
	function setPrimes( $aPrimes )
	{
		$this->p = $aPrimes['p'];
		$this->q = $aPrimes['q'];
	}

	/**
	 * @access public
	 */	
	function getPrimes()
	{
		$aPrimes['p'] = $this->p;
		$aPrimes['q'] = $this->q;

		return $aPrimes;
	}

	/**
	 * @access public
	 */
	function calcKeys()
	{
		Crypting_rsa::_keysRSA( $this->p, $this->q, $this->n, $this->e, $this->d );
	}

	/**
	 * @access public
	 */
	function testKeys( $sMessage = 'WhatsUp@ThisTime' )
	{
		return ( $sMessage == $this->decrypt( $this->encrypt( $sMessage ) ) );
	}

	/**
	 * @access public
	 */
	function setPublicKey( $aPublicKey )
	{
		$this->n = $aPublicKey['n'];
		$this->e = $aPublicKey['e'];
	}

	/**
	 * @access public
	 */
	function getPublicKey()
	{
		$aPublicKey['n'] = $this->n;
		$aPublicKey['e'] = $this->e;

		return $aPublicKey;
	}

	/**
	 * @access public
	 */
	function setPrivateKey( $aPrivateKey )
	{
		$this->n = $aPrivateKey['n'];
		$this->d = $aPrivateKey['d'];
	}

	/**
	 * @access public
	 */
	function getPrivateKey()
	{
		$aPrivateKey['n'] = $this->n;
		$aPrivateKey['d'] = $this->d;

		return $aPrivateKey;
	}

	/**
	 * @access public
	 */
	function blockSize()
	{
		return strlen( Crypting::intToOctetString( bcsub( $this->n, 1 ) ) );
	}

	/**
	 * @access public
	 */
	function hash( $sMessage )
	{
		return md5( $sMessage ) . ' ' . crc32( $sMessage );
	}

	/**
	 * @access public
	 */
	function signature( $sMessage )
	{
		$m = bcmod( Crypting::octetStringToInt( $this->hash( $sMessage ) ), $this->n );
		return Crypting::intToOctetString( Crypting_rsa::_signRSA( $this->n, $this->d, $m ) );
	}

	/**
	 * @access public
	 */
	function verification( $sMessage, $sSignature )
	{
		$s = Crypting::octetStringToInt( $sSignature );
		return bcmod( Crypting::octetStringToInt( $this->hash( $sMessage ) ), $this->n ) == Crypting_rsa::_verifyRSA( $this->n, $this->e, $s );
	}
	
	
	// private methods
	
	/**
	 * Encryption (Public Encryption)
	 *
	 * @access private
	 * @static
	 */
	function _encryptRSA( $n, $e, $m )
	{
  		// c = m ** e % n;
		$c = BCMath::powmod( $m, $e, $n );
		return $c;
	}

	/**
	 * Decryption (Private Decryption)
	 *
	 * @access private
	 * @static
	 */
	function _decryptRSA( $n, $d, $c )
	{
  		// m = c ** d % n;
		$m = BCMath::powmod( $c, $d, $n );
		return $m;
	}

	/**
	 * Signature (Private Encryption)
	 *
	 * @access private
	 * @static
	 */
	function _signRSA( $n, $d, $m )
	{
		// s = m ** d % n;
		$s = BCMath::powmod( $m, $d, $n );
		return $s;
	}

	/**
	 * Verification (Public Decryption)
	 *
	 * @access private
	 * @static
	 */
	function _verifyRSA( $n, $e, $s )
	{
		// m = s ** e % n;
		$m = BCMath::powmod( $s, $e, $n );
		return $m;
	}

	/**
	 * @access private
	 * @static
	 */
	function _keysRSA( $p, $q, &$n, &$e, &$d )
	{
		$n   = bcmul( $p, $q );
		$phi = bcmul( bcsub( $p, 1 ), bcsub( $q, 1 ) );
		$e   = '3';

		while ( BCMath::gcd( $e, $phi ) != '1' )
			$e = bcadd( $e, '1' );

		$t = bcdiv( $phi, BCMath::gcd( bcsub( $p, 1 ), bcsub( $q, 1 ) ) );
		$d = BCMath::inv( $e, $t );
	}
} // END OF Crypting_rsa

?>
