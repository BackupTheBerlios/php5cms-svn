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
 * RSA Class (RSA public key en/decryption and messages)
 *
 * @package security_crypt_rsa
 */
 
/**
 * Constructor
 *
 * @access public
 */
RSAUtil = function()
{
	this.Base = Base;
	this.Base();
};


RSAUtil.prototype = new Base();
RSAUtil.prototype.constructor = RSAUtil;
RSAUtil.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
RSAUtil.JT_RSA_TESTS = 3;

/**
 * Returns random Bignum size 0..n-1
 *
 * @access public
 * @static
 */
RSAUtil.rsaGetRandom = function( n )
{
	var bnRet = new Bignum( "", 1 );

    var i = 0;
    var j = 0;
    var k = 0;
    var m = 0;

    var key = new String( "" );
    var tmp;
    var ms;
    var ms2 = 0;

	while ( i < 8 )
	{
		tmp = new Date();
		ms  = Math.floor( tmp.getMilliseconds() * 256 / 1000 );
		
		if ( ms != ms2 )
		{
			key += String.fromCharCode( ms );
			ms2  = ms;
			
			i++;
		}
    }

	i = 0;
	var e = new ARC4( key );

	do
	{
		while ( i < n.byteString.length )
		{
			k = e.next();

			if ( j == 0 )
			{
				m = n.byteString.charCodeAt( i );
				
				if ( m < k )
				{
					i = 0;
					bnRet.byteString = "";
					
					continue;
				}
				else if ( m > k )
				{
					j = 1;
				}
			}

			bnRet.byteString += String.fromCharCode( k );
			i++;
		}
	} while ( k < n.byteString.charCodeAt( i ) )

    return bnRet;
};

/**
 * @access public
 * @static
 */
RSAUtil.rsaWitness = function( a, n )
{
    var i = 0;
    var d = new Bignum( "", 1 );
    var x = new Bignum( "", 1 );
	var nMinus1 = n.clone();

    nMinus1.decrement();

    d.hexSet( "01", 1 );
    var bArr = nMinus1.toBitArray();

    while ( bArr[i] == 0 )
		i++;

	for (;i < bArr.length; i++ )
	{
		x.copy( d );
		d = d.multiply( d, d );
		d = d.mod( n );

		if ( d.isOne() == 1 )
		{
			if ( ( x.isOne() == 0 ) && ( x.absCompare( nMinus1 ) != 0 ) )
				return 1; // composite
		}

		if ( bArr[i] == 1 )
		{
			d = d.multiply( d, a );
			d = d.mod( n );
		}
	}

    if ( d.isOne() == 0 )
		return 1; // composite

	return 0; // prime
};

/**
 * @access public
 * @static
 */
RSAUtil.rsaIsPrime = function( n, s )
{
	n.strip();
	var i = 0;

	if ( n.isEven() == 1 ) 
		return 0;

	for ( i = 1; i <= s; i++ )
	{
		a = RSAUtil.rsaGetRandom( n );

		if ( RSAUtil.rsaWitness( a, n ) == 1 )
			return 0;
	}

    return 1;
};

/**
 * @access public
 * @static
 */
RSAUtil.rsaRandomPrime = function( n )
{
	var st    = new String();
	var tmp   = new String;
	var bnTmp = new Bignum( "", 1 );
	var i = 0;

    tmp = String.fromCharCode( 1 );

	for (;i < n; i++ )
		tmp += String.fromCharCode( 0 );

    bnTmp.set( tmp, 1 );
    bnTmp = RSAUtil.rsaGetRandom( bnTmp );

	if ( bnTmp.isEven() == 1 )
		bnTmp.increment();

	i = 0;

    for (;;)
	{
		if ( bnTmp.pseudoprime() == 0 )
		{
			if ( RSAUtil.rsaIsPrime( bnTmp, RSAUtil.JT_RSA_TESTS ) == 1 )
				break;
		}
		
		bnTmp.increment();
		bnTmp.increment();
	}

	return bnTmp;
};
