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
 * @package util_math
 */
 
/**
 * Constructor
 *
 * @access public
 */
Bignum = function( s, d )
{
	this.Base = Base;
	this.Base();
	
    this.byteString = "";
	this.sign = 1;

	this.set( s || "", d || 1 );
};


Bignum.prototype = new Base();
Bignum.prototype.constructor = Bignum;
Bignum.superclass = Base.prototype;

/**
 * @access public
 */
Bignum.prototype.pseudoprime = function()
{
	var d = new Bignum( "", 1 );
	var bnTmp;

    d.hexSet( "015469a7", 1 );
	bnTmp = this.gcd( d );
	
    if ( bnTmp.isOne() != 1 )
		return 1;

    return 0;
};

/**
 * Set the member variable values.
 *
 * @access public
 */
Bignum.prototype.set = function( s, d )
{
    this.byteString = s;
	this.sign = d;

    this.strip();
};

/**
 * @access public
 */
Bignum.prototype.clone = function()
{    
	return new Bignum( this.byteString, this.sign );
};

/**
 * @access public
 */
Bignum.prototype.copy = function( a )
{
    this.byteString = new String( a.byteString );
    this.sign = a.sign;
};

/**
 * Strip leading zeros.
 *
 * @access public
 */
Bignum.prototype.strip = function()
{
    var i = 0;
    
    for ( i = 0; i < this.byteString.length; i++ )
	{
		if ( this.byteString.charCodeAt( i ) != 0 )
			break;
	}
	
    if ( i > 0 )
		this.byteString = this.byteString.substring( i );
};

/**
 * @return  boolean
 * @access public
 */
Bignum.prototype.isZero = function()
{
    this.strip();

    if ( this.byteString.length == 0 )
		return 1;

    return 0;
};

/**
 * @return  boolean
 * @access public
 */
Bignum.prototype.isOne = function()
{
    this.strip();

    if ( ( this.byteString.length == 1 ) && ( this.byteString.charCodeAt( 0 ) == 1 ) )
		return 1;

    return 0;
};

/**
 * @return  boolean
 * @access public
 */
Bignum.prototype.isEven = function()
{
    if ( ( this.byteString.charCodeAt( this.byteString.length - 1 ) % 2 ) == 0 )
		return 1;

    return 0;
};

/**
 * @access public
 */
Bignum.prototype.hexSet = function( s, d )
{
    var tmpStr = new String( s );

    // there should be even number of hex digits
    // to make correct octet stream
    if ( ( tmpStr.length % 2 ) != 0 )
		tmpStr = "0" + tmpStr;

    this.byteString = Bignum.hex2str( tmpStr );
    this.strip();

    if ( d < 0 )
		this.sign = -1;
    else
		this.sign = 1;
};

/**
 * @access public
 */
Bignum.prototype.absCompare = function( a )
{
    this.strip();
    a.strip();

    // a is obviously larger
    if ( this.byteString.length > a.byteString.length )
		return -1;

    // b is obviously larger
    if ( a.byteString.length > this.byteString.length )
		return 1;

    // a and b are equal
    if ( this.byteString.toString() == a.byteString.toString() )
		return 0;

    var sLen = this.byteString.length - 1;
    var i = 0;

    for (; i <= sLen; i++ )
	{
		if ( this.byteString.charCodeAt( i ) > a.byteString.charCodeAt( i ) )
	    	return -1;
		else if ( a.byteString.charCodeAt( i ) > this.byteString.charCodeAt( i ) )
			return 1;
	}
	
    // How this could happen?
    return NaN;
}

/**
 * Increment the Bignum by one.
 *
 * @access public
 */
Bignum.prototype.increment = function()
{
    if ( this.isZero() == 1 )
	{
		this.byteString = String.fromCharCode( 1 );
		return;
    }

    var i = this.byteString.length - 1;
    
    var d = 0;
    var k = 0;
    var retStr = new String();

    for (;i >= 0; i-- )
	{
		d = this.byteString.charCodeAt( i );
		d++;

		if ( d <= 0xff )
		{
	    	retStr = String.fromCharCode( d ) + retStr;
	    	break;
		}

		retStr = String.fromCharCode( 0 ) + retStr;
    }

    if ( retStr.charCodeAt( 0 ) == 0 )
		this.byteString = String.fromCharCode( 1 ) + retStr;
	else
		this.byteString = new String( this.byteString.substring( 0, i ) + retStr );
};

/**
 * Decrement the Bignum by one.
 *
 * @access public
 */
Bignum.prototype.decrement = function()
{
    var i = this.byteString.length - 1;
    var d = 0;
    var k = 0;
    var retStr = new String();

    for (;i >= 0; i-- )
	{
		d = this.byteString.charCodeAt( i );

		if ( d != 0 )
		{
	    	d--;
	    	retStr = String.fromCharCode( d % 0x100 ) + retStr;
	    
			break;
		}

		retStr = String.fromCharCode( 0xff ) + retStr;
    }

    this.byteString = new String( this.byteString.substring( 0, i ) + retStr );
};

/**
 * Calculates the sum of absolute values of two Bignums.
 *
 * @access public
 */
Bignum.prototype.sum = function( a, b )
{
    if ( a.isZero() == 1 )
		return new Bignum( b.byteString, b.sign );

    if ( b.isZero() == 1 )
		return new Bignum( a.byteString, b.sign );

    var tmpStr = "";
    var aStr = new String( a.byteString );
    var bStr = new String( b.byteString );

    var aLen = aStr.length;
    var bLen = bStr.length;

    var i = aLen - bLen;;
    var j = 0;
    var k = 0;

    // leading zeroes for shorter number
    for ( j = Math.abs( i ); j > 0; j-- )
		tmpStr += String.fromCharCode( 0 );

	if ( i < 0 )
		aStr = tmpStr + aStr;
    else if ( i > 0 )
		bStr = tmpStr + bStr;

    // sum loop
    tmpStr = "";
    j = 0;

    for ( i = aStr.length - 1; i >= 0; i-- )
	{
		j  = aStr.charCodeAt( i ) + bStr.charCodeAt( i );
		j += k;

		if ( j >= 0x100 )
		    k = 1;
		else
	    	k = 0;

		tmpStr = String.fromCharCode( j % 0x100 ) + tmpStr;
    }

    if ( k == 1 )
		tmpStr = String.fromCharCode( 1 ) + tmpStr;

    return new Bignum( tmpStr, 1 );
};

/**
 * Calculates the sum of absolute values of two Bignums.
 *
 * @access public
 */
Bignum.prototype.add = function( a )
{
    if ( this.isZero() == 1 )
	{
		this.set( a.byteString, a.sign );
		return;
    }

    if ( a.isZero() == 1 )
		return; 

    var n = this.byteString.length - a.byteString.length;

    if ( n > 0 )
	{
		aStr = this.byteString;
		bStr = a.byteString;
    }
	else
	{
		aStr = a.byteString;
		bStr = this.byteString;
    }

    var tmpStr = "";
    var j = 0;
    var k = 0;

    var i = aStr.length - 1;
    n = bStr.length - 1;

    for (;i >= 0; i--, n-- )
	{
		j = k + aStr.charCodeAt( i );

		if ( n >= 0 )
		{
		    j += bStr.charCodeAt( n );
		}
		else if ( k == 0 )
		{
	    	tmpStr = aStr.substr( 0, i + 1 ) + tmpStr;
	    	break;
		}

		if ( j >= 0x100 )
			k = 1;
		else
			k = 0;

		tmpStr = String.fromCharCode( j % 0x100 ) + tmpStr;
    }

    if ( k == 1 )
		tmpStr = String.fromCharCode( 1 ) + tmpStr;

    this.set( tmpStr, 1 );
};

/**
 * @access public
 */
Bignum.prototype.subtract = function( a, b )
{
    if ( a.isZero() == 1 )
		return new Bignum( b.byteString, b.sign );

    if ( b.isZero() == 1 )
		return new Bignum( a.byteString, b.sign );

    var tmpStr = "";

    // as in school, only smaller number can be
    // subtracted from bigger one
    var j = a.absCompare( b );

    if ( j == 0 )
		return new Bignum( "", 1 );

    if ( j < 0 )
	{
		aStr = a.byteString;
		bStr = b.byteString;
    }
	else if ( j > 0 )
	{
		aStr = b.byteString;
		bStr = a.byteString;
    }

    // leading zeroes for shorter number
    var i = aStr.length - bStr.length;;

    j = Math.abs( i )

    for (;j > 0; j-- )
		tmpStr += String.fromCharCode( 0 );

    if ( i < 0 )
		aStr = tmpStr + aStr;
    else if ( i > 0 )
		bStr = tmpStr + bStr;

    var k = 0;
    var n = 0;
    var o = 0;
    var p = 0;
	
    var sLen   = aStr.length;
    var tmpStr = "";

    for ( i = sLen - 1; i >= 0; i-- )
	{
		// k is borrowing flag
		o = aStr.charCodeAt( i ) - k;
		
		p  = bStr.charCodeAt( i );
		k  = ( o >= p )? 0 : 1;
		o += k * 0x100;
		n  = ( ( o - p ) % 0x100 );
	
		tmpStr =  String.fromCharCode( n ) + tmpStr;
    }

    return new Bignum( tmpStr, 1 );   
};

/**
 * @access public
 */
Bignum.prototype.multiply = function( a, b )
{
    var i = a.byteString.length - 1;
    var k = b.byteString.length;

    var zStr  = new String( "", 1 );
    var bnTmp = b.clone();
    var bnRet = new Bignum( "", 1 );

    for (;i >= 0; i-- )
	{
		bnTmp._oneDigitMultiply( a.byteString.charCodeAt( i ) );
		bnTmp.byteString += zStr;
	
		bnRet.add( bnTmp );

		zStr += String.fromCharCode( 0 );
		bnTmp.copy( b );
    }

    return bnRet;
};

/**
 * @access public
 */
Bignum.prototype.multiplyBy2 = function()
{
    this._oneDigitMultiply( 2 );
};

/**
 * Russian peasant's multiplication for a * b mod c looks good, but is not
 * sometimes base 256 is better than base 2 ;)
 *
 * @access public
 */
Bignum.prototype.multiplyRus = function( a, b )
{
    var bArr;
    var bnTmp;
	
    var bnRet = new Bignum( "", 1 );

    var n = 1;
    var i = 0;
    var k = 0;

    bArr  = a._toBitArray();
    bnTmp = b.clone();

    for ( i = bArr.length - 1; i >= 0; i-- )
	{
		if ( bArr[i] != 0 )
		{
	    	bnTmp._oneDigitMultiply( n );
	    	bnRet.add( bnTmp );
	    	n = 1;
		}
    
		n *= 2;
	
		if ( n >= 0xff )
		{
	    	bnTmp._oneDigitMultiply( n );
	    	n = 1;
		}
    }

    return bnRet;
};

/**
 * @access public
 */
Bignum.prototype.divideBy2 = function()
{
    if ( this.isZero() == 1 )
		return;

    var sLen   = this.byteString.length;
    var retStr = new String();
    
	var i = 0;
    var n = 0;
    var d = 0;

    for ( i = 0; i < sLen; i++ )
	{
		d = this.byteString.charCodeAt( i );
		retStr += String.fromCharCode( ( d / 2 ) + n );
		n = ( d % 2 ) * 0x80;
    }

    this.byteString = retStr;
};

/**
 * @access public
 */
Bignum.prototype.division = function( a, b )
{
    a.strip();
    b.strip();

    var tmp1   = new Bignum( "", 1 );
    var tmp2   = new Bignum( "", 1 );
    var tmpStr = new String( "" );
    
    var i = b.byteString.length;
    var j = 0;

    tmp1.byteString = a.byteString.substr( 0, i );

    for (;;)
	{
		tmp2.copy( b );
		j = Bignum.divisionHelper( tmp1, tmp2 );
		tmpStr += String.fromCharCode( j );

		if ( j != 0 )
		{
	    	tmp2._oneDigitMultiply( j );
	    	tmp1 = tmp1.subtract( tmp1, tmp2 );
		}

		if ( i >= a.byteString.length )
	    	break;

		tmp1.byteString += a.byteString.substr( i, 1 );
		i++;
    }

    tmp1.set( tmpStr, 1 );
    return tmp1;
};

/**
 * Modulus of the Bignum to a.
 *
 * @access public
 */
Bignum.prototype.mod = function( a )
{
    this.strip();
    a.strip();

    var tmp1 = new Bignum( "", 1 );
    var tmp2 = new Bignum( "", 1 );
    
    var i = a.byteString.length;
    var j = 0;

    tmp1.byteString = this.byteString.substr( 0, i );

    for (;;)
	{
		tmp2.copy( a );
		j = Bignum.divisionHelper( tmp1, tmp2 );

		if ( j != 0 )
		{
	    	tmp2._oneDigitMultiply( j );
	    	tmp1 = tmp1.subtract( tmp1, tmp2 );
		}

		if ( i >= this.byteString.length )
	    	break;

		tmp1.byteString += this.byteString.substr( i, 1 );
		i++;
    }

    return tmp1;
};

/**
 * Modular exponentiation of Bignum to a mod n.
 *
 * @access public
 */
Bignum.prototype.modExp = function( a, n )
{
    var i = 0;
    var d = new Bignum( "", 1 );

    d.hexSet( "01", 1 );
    var bArr = a._toBitArray();

    while ( bArr[i] == 0 )
		i++;

    for (;i < bArr.length; i++ )
	{
		d = d.multiply( d, d );
  		d = d.mod( n );

		if ( bArr[i] != 0 )
		{
  	    	d = d.multiply( d, this );
  	    	d = d.mod( n );
  		}

	}

    return d;
};

/**
 * @access public
 */
Bignum.prototype.sqrt = function()
{
    this.strip();

    var low  = new Bignum( "", 1 );
    var high = new Bignum( "", 1 );

    var c = 0;
    
    c = Math.ceil( this.byteString.length / 2 );

    while ( c-- >= 0 )
	{
		high.byteString += String.fromCharCode( 0xff );
	
		if ( c > 0 )
	    	low.byteString += String.fromCharCode( 0x00 );
    }

    low.byteString = String.fromCharCode( 1 ) + low.byteString;

    var mid;
    var tmp;

    while ( low.absCompare( high ) != 0 )
	{
		mid = low.sum( low, high );
		mid.increment();
		mid.divideBy2();
	
		tmp = mid.multiply( mid, mid );
		c = tmp.absCompare( this );

		if ( c < 0 )
		{
	    	high.copy( mid );
	    	high.decrement();
		}
		else
		{
	    	low.copy( mid );
		}
    }

    return low;
};

/**
 * @access public
 */
Bignum.prototype.gcd = function( a )
{
    var g0 = this.clone();
    var g1 = a.clone();
    var g2;

    while ( g1.isZero() == 0 )
	{
		g2 = g0.mod(g1 );
		g0.copy( g1 );
		g1.copy( g2 );
    }

    return g0;
};

/**
 * Return unsigned hexadecimal representation of the bignum.
 * Note: overwrites method in Base.
 *
 * @access public
 */
Bignum.prototype.toString = function()
{
    this.strip();

    if ( this.byteString.length == 0 )
		return( "00" );

    return Bignum.str2hex( this.byteString );
};


// private methods

/**
 * Returns array containing each individual bits of Bignum.
 *
 * @access private
 */
Bignum.prototype._toBitArray = function()
{
    var retArray = new Array( this.byteString.length * 8 );
    var i = 0;
    var j = 0;

    for ( i = 0; i < this.byteString.length; i++ )
	{
		retArray[j++] = ( ( this.byteString.charCodeAt( i ) & 0x80 ) == 0 )? 0 : 1;
		retArray[j++] = ( ( this.byteString.charCodeAt( i ) & 0x40 ) == 0 )? 0 : 1;
		retArray[j++] = ( ( this.byteString.charCodeAt( i ) & 0x20 ) == 0 )? 0 : 1;
		retArray[j++] = ( ( this.byteString.charCodeAt( i ) & 0x10 ) == 0 )? 0 : 1;
		retArray[j++] = ( ( this.byteString.charCodeAt( i ) &  0x8 ) == 0 )? 0 : 1;
		retArray[j++] = ( ( this.byteString.charCodeAt( i ) &  0x4 ) == 0 )? 0 : 1;
		retArray[j++] = ( ( this.byteString.charCodeAt( i ) &  0x2 ) == 0 )? 0 : 1;
		retArray[j++] = ( ( this.byteString.charCodeAt( i ) &  0x1 ) == 0 )? 0 : 1;
	}

    return retArray;
};

/**
 * @access private
 */
Bignum.prototype._oneDigitMultiply = function( n )
{
    if ( n == 0 )
	{
		this.byteString = new String( "" );
		return;
    }

    if ( n == 1 )
		return;

    var o = 0;
    var p = 0;
    var k = 0;
    var retStr = new String();

    var i = this.byteString.length - 1;

    for (;i >= 0; i-- )
	{
		o = this.byteString.charCodeAt( i );
		p = n * o + k;
		k = Math.floor( p / 0x100 );
		p = p % 0x100;

		retStr = String.fromCharCode( p ) + retStr;
    }

    if ( k != 0 )
		retStr = String.fromCharCode( k ) + retStr;

    this.set( retStr, 1 );
};


/**
 * @access public
 * @static
 */
Bignum.divisionHelper = function( a, b )
{
    var mid;
    var n;
	
    var low  = 0;
    var high = 0xff;
    var tmp  = b.clone();

    while ( low != high )
	{
		tmp.copy( b );
		mid = Math.floor( ( low + high + 1 ) / 2 );
		tmp._oneDigitMultiply( mid );

		n = tmp.absCompare( a );

		if ( n < 0 )
		    high = mid - 1;
		else
	    	low = mid;
    }

    return low;
};

/**
 * @access public
 * @static
 */
Bignum.str2hex = function( inStr )
{
	var l = inStr.length;
	var i = 0;
	var retStr = new String;

	for ( i = 0; i < l; i++ )
		retStr = retStr + Bignum.twoCharHex( inStr.charCodeAt( i ) );

   return retStr;
};

/**
 * @access public
 * @static
 */
Bignum.hex2str = function( inStr )
{
	var l = inStr.length;
	var retStr = new String;
	var i = 0;

	for ( i = 0; i < l; i += 2 )
		retStr = retStr + String.fromCharCode( "0x" + inStr.substr( i, 2 ) );

	return retStr;
};

/**
 * Returns string representation of i in hex.
 * 0xFF --> "FF"
 * 0x01 --> "01"
 *
 * @return string
 * @access public
 * @static
 */
Bignum.twoCharHex = function( i )
{
	if ( i == 0 )
		return "00";
	else if ( i < 16 )
		return "0" + i.toString( 16 );

	return i.toString( 16 );
};
