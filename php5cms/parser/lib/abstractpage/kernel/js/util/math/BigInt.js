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
|Authors: Dave Shapiro <dave@ohdave.com>                               |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * BigInt, a suite of routines for performing multiple-precision arithmetic in
 * JavaScript.
 *
 * Max number = 10^16 - 2 = 9999999999999998;
 *              2^53      = 9007199254740992;
 *
 * @package util_math
 */

/**
 * Constructor
 *
 * @access public
 */
BigInt = function( s )
{
	this.Base = Base;
	this.Base();
	
	if ( s == "" ) 
	{
		// This is hard-wired for speed. If BigInt.maxDigits changes, this will
		// have to be changed as well.
		this.digits = new Array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 );
		this.isNeg  = false;
		
		return;
	}
	
	this.isNeg = s.charAt( 0 ) == '-';
	var i  = Number( this.isNeg );
	var ls = s.length;

	// Skip leading zeros.
	while ( i < ls && s.charAt( i ) == '0' ) 
		++i;
	
	if ( i == ls ) 
		return;
	
	var numDigits = ls - i;
	var fgl = numDigits % BigInt.dpl10;
	
	if ( fgl == 0 ) 
		fgl = BigInt.dpl10;
	
	var result = BigInt.fromNumber( Number( s.substr( i, fgl ) ) );

	i += fgl;
	while ( i < ls ) 
	{
		result = BigInt.add( BigInt.multiply( result, BigInt.lr10 ), BigInt.fromNumber( Number( s.substr( i, BigInt.dpl10 ) ) ) );
		i += BigInt.dpl10;
	}
	
	this.digits = result.digits;
};


BigInt.prototype = new Base();
BigInt.prototype.constructor = BigInt;
BigInt.superclass = Base.prototype;


/**
 * @access public
 * @static
 */
BigInt.fromArray = function( a )
{
	var result = new BigInt( "" );
	result.digits = a.slice( 0 );
	
	return result;
};

/**
 * @access public
 * @static
 */
BigInt.fromNumber = function( i )
{
	var result = new BigInt( "" );
	result.isNeg = i < 0;
	i = Math.abs( i );
	var j = 0;
	
	while ( i > 0 ) 
	{
		result.digits[j++] = i & BigInt.maxDigitVal;
		i = Math.floor( i / BigInt.radix );
	}
	
	return result;
};

/**
 * @access public
 * @static
 */
BigInt.reverseStr = function( s )
{
	var result = "";
	
	for ( var i = s.length - 1; i > -1; --i )
		result += s.charAt( i );
	
	return result;
};

/**
 * @access public
 * @static
 */
BigInt.toDecimal = function( x )
{
	var a = BigInt.fromArray( x.digits );
	var result = "";
	var b = new BigInt( "" );
	b.digits[0] = 10;
	var qr  = BigInt.divideModulo( a, b );
	result += String( qr[1].digits[0] );
	
	while ( BigInt.compare( qr[0], BigInt.bigZero ) == 1 ) 
	{
		qr = BigInt.divideModulo( qr[0], b );
		result += String( qr[1].digits[0] );
	}
	
	return ( x.isNeg? "-" : "" ) + BigInt.reverseStr( result );
};

/**
 * @access public
 * @static
 */
BigInt.digitToHex = function( n )
{
	var hexToChar = new Array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f' );
	var mask      = 0xf;
	var result    = "";
	
	for ( i = 0; i < 4; ++i ) 
	{
		result += hexToChar[n & mask];
		n >>>= 4;
	}
	
	return BigInt.reverseStr( result );
};

/**
 * @access public
 * @static
 */
BigInt.toHex = function( x )
{
	var result = "";
	var n = BigInt.numDigits( x );
	
	for ( var i = BigInt.numDigits( x ); i > -1; --i )
		result += BigInt.digitToHex( x.digits[i] );
	
	return result;
};

/**
 * @access public
 * @static
 */
BigInt.charToHex = function( c )
{
	var ZERO    = 48;
	var NINE    = ZERO + 9;
	var littleA = 97;
	var littleZ = littleA + 25;
	var bigA    = 65;
	var bigZ    = 65 + 25;
	var result;

	if ( c >= ZERO && c <= NINE )
		result = c - ZERO;
	else if ( c >= bigA && c <= bigZ ) 
		result = 10 + c - bigA;
	else if ( c >= littleA && c <= littleZ ) 
		result = 10 + c - littleA;
	else 
		result = 0;
	
	return result;
};

/**
 * @access public
 * @static
 */
BigInt.hexToDigit = function( s )
{
	var result = 0;
	var sl = Math.min( s.length, 4 );
	
	for ( var i = 0; i < sl; ++i ) 
	{
		result <<= 4;
		result  |= BigInt.charToHex( s.charCodeAt( i ) );
	}
	return result;
};

/**
 * @access public
 * @static
 */
BigInt.fromHex = function( s )
{
	var result = new BigInt( "" );
	var sl = s.length;
	
	for ( var i = sl, j = 0; i > 0; i -= 4, ++j )
		result.digits[j] = BigInt.hexToDigit ( s.substr( Math.max( i - 4, 0 ), Math.min( i, 4 ) ) );
	
	return result;
};

/**
 * @access public
 * @static
 */
BigInt.dump = function( b )
{
	return ( b.isNeg? "-" : "" ) + b.digits.join( " " );
};

/**
 * @access public
 * @static
 */
BigInt.add = function( x, y )
{
	var result;

	if ( x.isNeg != y.isNeg ) 
	{
		var tmp   = BigInt.fromArray( y.digits );
		tmp.isNeg = x.isNeg;
		result    = BigInt.subtract( x, tmp );
	} 
	else 
	{
		result = new BigInt( "" );
		var c = 0;
		var n;
		
		for ( var i = 0; i < x.digits.length; ++i ) 
		{
			n = x.digits[i] + y.digits[i] + c;
			result.digits[i] = n % BigInt.radix;
			c = Number( n >= BigInt.radix );
		}
		
		result.isNeg = x.isNeg;
	}
	
	return result;
};

/**
 * @access public
 * @static
 */
BigInt.subtract = function( x, y )
{
	var result;
	
	if ( x.isNeg != y.isNeg ) 
	{
		var tmp = BigInt.fromArray( y.digits );
		tmp.isNeg = x.isNeg;
		result = BigInt.add( x, tmp );
	} 
	else 
	{
		result = new BigInt( "" );
		var n, c;
		c = 0;
		
		for ( var i = 0; i < x.digits.length; ++i ) 
		{
			n = x.digits[i] - y.digits[i] + c;
			result.digits[i] = n % BigInt.radix;
			
			// Stupid non-conforming modulus operation.
			if ( result.digits[i] < 0 ) 
				result.digits[i] += BigInt.radix;
			
			c = 0 - Number( n < 0 );
		}
		
		// Fix up the negative sign, if any.
		if ( c == -1 ) 
		{
			c = 0;
			for ( var i = 0; i < x.digits.length; ++i ) 
			{
				n = 0 - result.digits[i] + c;
				result.digits[i] = n % BigInt.radix;
				
				// Stupid non-conforming modulus operation.
				if ( result.digits[i] < 0 ) 
					result.digits[i] += BigInt.radix;
				
				c = 0 - Number( n < 0 );
			}
			
			// Result is opposite sign of arguments.
			result.isNeg = !x.isNeg;
		}
		else 
		{
			// Result is same sign.
			result.isNeg = x.isNeg;
		}
	}
	
	return result;
};

/**
 * @access public
 * @static
 */
BigInt.numDigits = function( x )
{
	var result = x.digits.length - 1;
	
	while ( x.digits[result] == 0 && result > 0 ) 
		--result;
	
	return result;
};

/**
 * @access public
 * @static
 */
BigInt.numBits = function( x )
{
	var n = BigInt.numDigits( x );
	var d = x.digits[n];
	var m = ( n + 1 ) * BigInt.bitsPerDigit;
	var result;
	
	for ( result = m; result > m - BigInt.bitsPerDigit; --result ) 
	{
		if ( ( d & 0x8000 ) != 0 ) 
			break;
		
		d <<= 1;
	}
	
	return result;
};

/**
 * @access public
 * @static
 */
BigInt.multiply = function( x, y )
{
	var result = new BigInt( "" );
	var c;
	var n = BigInt.numDigits( x );
	var t = BigInt.numDigits( y );
	var u, uv, k;

	for ( var i = 0; i <= t; ++i ) 
	{
		c = 0;
		k = i;
		
		for ( j = 0; j <= n; ++j, ++k ) 
		{
			uv = result.digits[k] + x.digits[j] * y.digits[i] + c;
			result.digits[k] = uv & BigInt.maxDigitVal;
			c = uv >>> BigInt.radixBits;
			// c = Math.floor( uv / BigInt.radix );
		}
		
		result.digits[i + n + 1] = c;
	}
	
	// Someone give me a logical xor, please.
	result.isNeg = x.isNeg != y.isNeg;
	return result;
};

/**
 * @access public
 * @static
 */
BigInt.multiplyDigit = function( x, y )
{
	var n, c, uv;
	result = new BigInt( "" );
	n = BigInt.numDigits( x );
	c = 0;
	
	for ( var j = 0; j <= n; ++j ) 
	{
		uv = result.digits[j] + x.digits[j] * y + c;
		result.digits[j] = uv & BigInt.maxDigitVal;
		c = uv >>> BigInt.radixBits;
	}
	
	result.digits[1 + n] = c;
	return result;
};

/**
 * @access public
 * @static
 */
BigInt.arrayCopy = function( src, srcStart, dest, destStart, n )
{
	var m = Math.min( srcStart + n, src.length );

	for ( var i = srcStart, j = destStart; i < m; ++i, ++j )
		dest[j] = src[i];
};

/**
 * @access public
 * @static
 */
BigInt.shiftLeft = function( x, n )
{
	var highBitMasks = new Array(
		0x0000, 0x8000, 0xC000, 0xE000, 0xF000, 0xF800,
		0xFC00, 0xFE00, 0xFF00, 0xFF80, 0xFFC0, 0xFFE0,
		0xFFF0, 0xFFF8, 0xFFFC, 0xFFFE, 0xFFFF
	);

	var digits = Math.floor( n / BigInt.bitsPerDigit );
	var result = new BigInt( "" );
	BigInt.arrayCopy( x.digits, 0, result.digits, digits, BigInt.maxDigits - digits );
	var bits = n % BigInt.bitsPerDigit;
	var rightBits = BigInt.bitsPerDigit - bits;
	
	for ( var i = BigInt.maxDigits - 1, i1 = i - 1; i > 0; --i, --i1 ) 
	{
		result.digits[i] = ( ( result.digits[i] << bits ) & BigInt.maxDigitVal ) |
		                   ( ( result.digits[i1] & highBitMasks[bits] ) >>> ( rightBits ) );
	}
	
	result.digits[0] = ( ( result.digits[i] << bits ) & BigInt.maxDigitVal );
	result.isNeg = x.isNeg;
	
	return result;
};

/**
 * @access public
 * @static
 */
BigInt.shiftRight = function( x, n )
{
	var lowBitMasks = new Array(
		0x0000, 0x0001, 0x0003, 0x0007, 0x000F, 0x001F,
		0x003F, 0x007F, 0x00FF, 0x01FF, 0x03FF, 0x07FF,
		0x0FFF, 0x1FFF, 0x3FFF, 0x7FFF, 0xFFFF
	);

	var digits = Math.floor( n / BigInt.bitsPerDigit );
	var result = new BigInt( "" );
	BigInt.arrayCopy( x.digits, digits, result.digits, 0, BigInt.maxDigits - digits );
	var bits = n % BigInt.bitsPerDigit;
	var leftBits = BigInt.bitsPerDigit - bits;
	
	for ( var i = 0, i1 = i + 1; i < BigInt.maxDigits - 1; ++i, ++i1 ) 
	{
		result.digits[i] = ( result.digits[i] >>> bits ) |
		                   ( ( result.digits[i1] & lowBitMasks[bits] ) << leftBits );
	}
	
	result.digits[BigInt.maxDigits - 1] >>>= bits;
	result.isNeg = x.isNeg;
	
	return result;
};

/**
 * @access public
 * @static
 */
BigInt.multiplyByRadixPower = function( x, n )
{
	var result = new BigInt( "" );
	BigInt.arrayCopy( x.digits, 0, result.digits, n, BigInt.maxDigits - n );
	
	return result;
};

/**
 * @access public
 * @static
 */
BigInt.divideByRadixPower = function( x, n )
{
	var result = new BigInt( "" );
	BigInt.arrayCopy( x.digits, n, result.digits, 0, BigInt.maxDigits - n );
	
	return result;
};

/**
 * @access public
 * @static
 */
BigInt.compare = function( x, y )
{
	if ( x.isNeg != y.isNeg )
		return 1 - 2 * Number( x.isNeg );
	
	for ( var i = BigInt.maxDigits - 1; i >= 0; --i ) 
	{
		if ( x.digits[i] != y.digits[i] ) 
		{
			if ( x.isNeg )
				return 1 - 2 * Number( x.digits[i] > y.digits[i] );
			else
				return 1 - 2 * Number( x.digits[i] < y.digits[i] );
		}
	}
	
	return 0;
};

/**
 * @access public
 * @static
 */
BigInt.divideModulo = function( x, yorig )
{
	var nb = BigInt.numBits( x );
	var tb = BigInt.numBits( yorig );
	
	if ( nb < tb ) 
	{
		// |x| < |y|
		var q, r;
		
		if ( x.isNeg ) 
		{
			q = BigInt.fromArray( BigInt.bigOne.digits );
			q.isNeg = !yorig.isNeg;
			x.isNeg = false;
			yorig.isNeg = false;
			r = BigInt.subtract( yorig, x );
			
			// Restore signs, 'cause they're passed by reference.
			x.isNeg = true;
			yorig.isNeg = !q.isNeg
		} 
		else 
		{
			q = new BigInt( "" );
			r = BigInt.fromArray( x.digits );
			r.isNeg = x.isNeg;
		}
		
		return new Array( q, r );
	}

	// var n = BigInt.numDigits( x );
	// var t = BigInt.numDigits( y );
	
	var q = new BigInt( "" );
	var r = BigInt.fromArray( x.digits );
	var y = BigInt.fromArray( yorig.digits );

	// Normalize Y.
	var t = Math.ceil( tb / BigInt.bitsPerDigit ) - 1;
	var lambda = 0;

	while ( y.digits[t] < BigInt.halfRadix ) 
	{
		y = BigInt.shiftLeft( y, 1 );
		++lambda;
		++tb;
		t = Math.ceil( tb / BigInt.bitsPerDigit ) - 1;
	}
	
	// Shift r over to keep the quotient constant. We'll shift the
	// remainder back at the end.
	r = BigInt.shiftLeft( r, lambda );
	nb += lambda; // Update the bit count for x.
	var n = Math.ceil( nb / BigInt.bitsPerDigit ) - 1;
	var b = BigInt.multiplyByRadixPower( y, n - t );

	while ( BigInt.compare( r, b ) != -1 ) 
	{
		++q.digits[n - t];
		r = BigInt.subtract( r, b );
	}
	
	for ( var i = n; i > t; --i ) 
	{
		if ( r.digits[i] == y.digits[t] )
			q.digits[i - t - 1] = BigInt.maxDigitVal;
		else
			q.digits[i - t - 1] = Math.floor( ( r.digits[i] * BigInt.radix + r.digits[i - 1] ) / y.digits[t] );

		var c1 = q.digits[i - t - 1] * ( ( y.digits[t] * BigInt.radix ) + y.digits[t - 1] );
		var c2 = ( r.digits[i] * BigInt.radixSquared ) + ( ( r.digits[i - 1] * BigInt.radix ) + r.digits[i - 2] );
		
		while ( c1 > c2 ) 
		{
			--q.digits[i - t - 1];
			
			c1 = q.digits[i - t - 1] * ( ( y.digits[t] * BigInt.radix ) | y.digits[t - 1] );
			c2 = ( r.digits[i] * BigInt.radix * BigInt.radix ) + ( ( r.digits[i - 1] * BigInt.radix ) + r.digits[i - 2] );
		}

		b = BigInt.multiplyByRadixPower( y, i - t - 1 );
		r = BigInt.subtract( r, BigInt.multiplyDigit( b, q.digits[i - t - 1] ) );

		if ( r.isNeg ) 
		{
			r = BigInt.add( r, b );
			--q.digits[i - t - 1];
		}
	}
	
	r = BigInt.shiftRight( r, lambda );
	
	// Fiddle with the signs and stuff to make sure that 0 <= r < y.
	q.isNeg = x.isNeg != yorig.isNeg;
	
	if ( x.isNeg ) 
	{
		if ( yorig.isNeg )
			q = BigInt.add( q, BigInt.bigOne );
		else
			q = BigInt.subtract( q, BigInt.bigOne );
		
		y = BigInt.shiftRight( y, lambda );
		r = BigInt.subtract( y, r );
	}
	
	// Check for the unbelievably stupid degenerate case of r == -0.
	if ( r.digits[0] == 0 && BigInt.numDigits( r ) == 0 ) 
		r.isNeg = false;

	return new Array( q, r );
};

/**
 * @access public
 * @static
 */
BigInt.divide = function( x, y )
{
	return BigInt.divideModulo( x, y )[0];
};

/**
 * @access public
 * @static
 */
BigInt.modulo = function( x, y )
{
	return BigInt.divideModulo( x, y )[1];
};

/**
 * @access public
 * @static
 */
BigInt.multiplyMod = function( x, y, m )
{
	return BigInt.modulo( BigInt.multiply( x, y ), m );
};

/**
 * @access public
 * @static
 */
BigInt.pow = function( x, y )
{
	var result = BigInt.fromArray( BigInt.bigOne.digits );
	var a = BigInt.fromArray( x.digits );
	a.isNeg = x.isNeg;

	while ( true ) 
	{
		if ( ( y & 1 ) != 0 ) 
			result = BigInt.multiply( result, a );
			
		y >>= 1;
		
		if ( y == 0 ) 
			break;
		
		a = BigInt.multiply( a, a );
	}
	
	return result;
};

/**
 * @access public
 * @static
 */
BigInt.powMod = function( x, y, m )
{
	var result = new BigInt( "" );
	result.digits[0] = 1;
	var a = BigInt.fromArray( x.digits );
	a.isNeg = x.isNeg;
	var k = BigInt.fromArray( y.digits );
	
	while ( true ) 
	{
		if ( ( k.digits[0] & 1 ) != 0 ) 
			result = BigInt.multiplyMod( result, a, m );
			
		k = BigInt.shiftRight( k, 1 );

		if ( k.digits[0] == 0 && BigInt.numDigits( k ) == 0 ) 
			break;
		
		a = BigInt.multiplyMod( a, a, m );
	}
	
	return result;
};


/**
 * @access public
 * @static
 */
BigInt.radixBase = 2;

/**
 * @access public
 * @static
 */
BigInt.radixBits = 16;

/**
 * @access public
 * @static
 */
BigInt.bitsPerDigit = BigInt.radixBits;

/**
 * @access public
 * @static
 */
BigInt.radix = 1 << 16; // = 2^16 = 65536

/**
 * @access public
 * @static
 */
BigInt.halfRadix = BigInt.radix >>> 1;

/**
 * @access public
 * @static
 */
BigInt.radixSquared = BigInt.radix * BigInt.radix;

/**
 * @access public
 * @static
 */
BigInt.maxDigitVal = BigInt.radix - 1;

/**
 * @access public
 * @static
 */
BigInt.maxInteger = 9999999999999998; 

/**
 * @access public
 * @static
 */
BigInt.maxDigits = 20;

/**
 * @access public
 * @static
 */
BigInt.bigZero = new BigInt( "" );

/**
 * @access public
 * @static
 */
BigInt.bigOne = new BigInt( "" );
BigInt.bigOne.digits[0] = 1;

/**
 * The maximum number of digits in base 10 you can convert to an
 * integer without JavaScript throwing up on you.
 *
 * @access public
 * @static
 */
BigInt.dpl10 = 15;

/**
 * @access public
 * @static
 */
BigInt.lr10 = BigInt.fromNumber( 1000000000000000 );
