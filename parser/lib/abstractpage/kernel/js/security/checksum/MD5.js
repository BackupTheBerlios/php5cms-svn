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
 * Javascript implementation of the RSA Data Security, Inc. MD5
 * Message-Digest Algorithm.
 *
 * @package security_checksum
 */
 
 
/**
 * Little helper
 *
 * @access public
 */
function array( n )
{
	for ( i = 0; i < n; i++ )
		this[i] = 0;
  	
	this.length = n;
};


/**
 * Constructor
 *
 * @access public
 */
MD5 = function()
{
	this.Base = Base;
	this.Base();
};


MD5.prototype = new Base();
MD5.prototype.constructor = MD5;
MD5.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
MD5.state = new array( 4 ); 

/**
 * @access public
 * @static
 */
MD5.count = new array( 2 );

/**
 * @access public
 * @static
 */
MD5.count[0] = 0;

/**
 * @access public
 * @static
 */
MD5.count[1] = 0;                     

/**
 * @access public
 * @static
 */
MD5.buffer = new array( 64 ); 

/**
 * @access public
 * @static
 */
MD5.transformBuffer = new array( 16 );

/**
 * @access public
 * @static
 */
MD5.digestBits = new array( 16 );

/**
 * @access public
 * @static
 */
MD5.S11 = 7;

/**
 * @access public
 * @static
 */
MD5.S12 = 12;

/**
 * @access public
 * @static
 */
MD5.S13 = 17;

/**
 * @access public
 * @static
 */
MD5.S14 = 22;

/**
 * @access public
 * @static
 */
MD5.S21 = 5;

/**
 * @access public
 * @static
 */
MD5.S22 = 9;

/**
 * @access public
 * @static
 */
MD5.S23 = 14;

/**
 * @access public
 * @static
 */
MD5.S24 = 20;

/**
 * @access public
 * @static
 */
MD5.S31 = 4;

/**
 * @access public
 * @static
 */
MD5.S32 = 11;

/**
 * @access public
 * @static
 */
MD5.S33 = 16;

/**
 * @access public
 * @static
 */
MD5.S34 = 23;

/**
 * @access public
 * @static
 */
MD5.S41 = 6;

/**
 * @access public
 * @static
 */
MD5.S42 = 10;

/**
 * @access public
 * @static
 */
MD5.S43 = 15;

/**
 * @access public
 * @static
 */
MD5.S44 = 21;

/**
 * @access public
 * @static
 */
MD5.ascii = "01234567890123456789012345678901" +
	" !\"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ" +
	"[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~";


/**
 * @access public
 * @static
 */
MD5.get = function( entree ) 
{
	var l,s,k,ka,kb,kc,kd;

	MD5.init();

	for ( k = 0; k < entree.length; k++ )
	{
   		l = entree.charAt( k );
   		MD5.update( MD5.ascii.lastIndexOf( l ) );
 	}
 
 	MD5.finish();
 	ka = kb = kc = kd = 0;
 
 	for ( i = 0 ; i < 4 ;i++ )
		ka += MD5.shl( MD5.digestBits[15-i], ( i * 8 ) );
 	
	for ( i = 4 ; i < 8 ;i++ )
		kb += MD5.shl( MD5.digestBits[15-i], ( ( i-4 ) * 8 ) );
 	
	for ( i = 8 ; i < 12;i++ )
		kc += MD5.shl( MD5.digestBits[15-i], ( ( i-8 ) * 8 ) );
 	
	for ( i = 12; i < 16;i++ )
		kd += MD5.shl( MD5.digestBits[15-i], ( ( i-12 ) * 8 ) );
	
 	s = MD5.hexa( kd ) + MD5.hexa( kc ) + MD5.hexa( kb ) + MD5.hexa( ka );
	return s; 
};

/**
 * @access public
 * @static
 */
MD5.transform = function( buf, offset )
{ 
	var a = 0;
	var b = 0;
	var c = 0;
	var d = 0; 
 	var x = MD5.transformBuffer;
 
 	a = MD5.state[0];
 	b = MD5.state[1];
 	c = MD5.state[2];
 	d = MD5.state[3];
 
 	for ( i = 0; i < 16; i++ )
	{
    	x[i] = MD5.and( buf[i*4+offset], 0xff );
     
	 	for (j = 1; j < 4; j++ )
			x[i] += MD5.shl( MD5.and( buf[i * 4 + j + offset], 0xff ), j * 8 );
	}

	// round 1
 	a = MD5.FF( a, b, c, d, x[ 0], MD5.S11, 0xd76aa478 ); /* 1 */
 	d = MD5.FF( d, a, b, c, x[ 1], MD5.S12, 0xe8c7b756 ); /* 2 */
 	c = MD5.FF( c, d, a, b, x[ 2], MD5.S13, 0x242070db ); /* 3 */
 	b = MD5.FF( b, c, d, a, x[ 3], MD5.S14, 0xc1bdceee ); /* 4 */
 	a = MD5.FF( a, b, c, d, x[ 4], MD5.S11, 0xf57c0faf ); /* 5 */
 	d = MD5.FF( d, a, b, c, x[ 5], MD5.S12, 0x4787c62a ); /* 6 */
 	c = MD5.FF( c, d, a, b, x[ 6], MD5.S13, 0xa8304613 ); /* 7 */
 	b = MD5.FF( b, c, d, a, x[ 7], MD5.S14, 0xfd469501 ); /* 8 */
 	a = MD5.FF( a, b, c, d, x[ 8], MD5.S11, 0x698098d8 ); /* 9 */
 	d = MD5.FF( d, a, b, c, x[ 9], MD5.S12, 0x8b44f7af ); /* 10 */
 	c = MD5.FF( c, d, a, b, x[10], MD5.S13, 0xffff5bb1 ); /* 11 */
 	b = MD5.FF( b, c, d, a, x[11], MD5.S14, 0x895cd7be ); /* 12 */
 	a = MD5.FF( a, b, c, d, x[12], MD5.S11, 0x6b901122 ); /* 13 */
 	d = MD5.FF( d, a, b, c, x[13], MD5.S12, 0xfd987193 ); /* 14 */
 	c = MD5.FF( c, d, a, b, x[14], MD5.S13, 0xa679438e ); /* 15 */
 	b = MD5.FF( b, c, d, a, x[15], MD5.S14, 0x49b40821 ); /* 16 */

 	// round 2
 	a = MD5.GG( a, b, c, d, x[ 1], MD5.S21, 0xf61e2562 ); /* 17 */
 	d = MD5.GG( d, a, b, c, x[ 6], MD5.S22, 0xc040b340 ); /* 18 */
 	c = MD5.GG( c, d, a, b, x[11], MD5.S23, 0x265e5a51 ); /* 19 */
 	b = MD5.GG( b, c, d, a, x[ 0], MD5.S24, 0xe9b6c7aa ); /* 20 */
 	a = MD5.GG( a, b, c, d, x[ 5], MD5.S21, 0xd62f105d ); /* 21 */
 	d = MD5.GG( d, a, b, c, x[10], MD5.S22,  0x2441453 ); /* 22 */
 	c = MD5.GG( c, d, a, b, x[15], MD5.S23, 0xd8a1e681 ); /* 23 */
 	b = MD5.GG( b, c, d, a, x[ 4], MD5.S24, 0xe7d3fbc8 ); /* 24 */
 	a = MD5.GG( a, b, c, d, x[ 9], MD5.S21, 0x21e1cde6 ); /* 25 */
 	d = MD5.GG( d, a, b, c, x[14], MD5.S22, 0xc33707d6 ); /* 26 */
 	c = MD5.GG( c, d, a, b, x[ 3], MD5.S23, 0xf4d50d87 ); /* 27 */
 	b = MD5.GG( b, c, d, a, x[ 8], MD5.S24, 0x455a14ed ); /* 28 */
 	a = MD5.GG( a, b, c, d, x[13], MD5.S21, 0xa9e3e905 ); /* 29 */
 	d = MD5.GG( d, a, b, c, x[ 2], MD5.S22, 0xfcefa3f8 ); /* 30 */
 	c = MD5.GG( c, d, a, b, x[ 7], MD5.S23, 0x676f02d9 ); /* 31 */
 	b = MD5.GG( b, c, d, a, x[12], MD5.S24, 0x8d2a4c8a ); /* 32 */

 	// round 3
 	a = MD5.HH( a, b, c, d, x[ 5], MD5.S31, 0xfffa3942 ); /* 33 */
 	d = MD5.HH( d, a, b, c, x[ 8], MD5.S32, 0x8771f681 ); /* 34 */
 	c = MD5.HH( c, d, a, b, x[11], MD5.S33, 0x6d9d6122 ); /* 35 */
 	b = MD5.HH( b, c, d, a, x[14], MD5.S34, 0xfde5380c ); /* 36 */
 	a = MD5.HH( a, b, c, d, x[ 1], MD5.S31, 0xa4beea44 ); /* 37 */
 	d = MD5.HH( d, a, b, c, x[ 4], MD5.S32, 0x4bdecfa9 ); /* 38 */
 	c = MD5.HH( c, d, a, b, x[ 7], MD5.S33, 0xf6bb4b60 ); /* 39 */
 	b = MD5.HH( b, c, d, a, x[10], MD5.S34, 0xbebfbc70 ); /* 40 */
 	a = MD5.HH( a, b, c, d, x[13], MD5.S31, 0x289b7ec6 ); /* 41 */
 	d = MD5.HH( d, a, b, c, x[ 0], MD5.S32, 0xeaa127fa ); /* 42 */
 	c = MD5.HH( c, d, a, b, x[ 3], MD5.S33, 0xd4ef3085 ); /* 43 */
 	b = MD5.HH( b, c, d, a, x[ 6], MD5.S34,  0x4881d05 ); /* 44 */
 	a = MD5.HH( a, b, c, d, x[ 9], MD5.S31, 0xd9d4d039 ); /* 45 */
 	d = MD5.HH( d, a, b, c, x[12], MD5.S32, 0xe6db99e5 ); /* 46 */
 	c = MD5.HH( c, d, a, b, x[15], MD5.S33, 0x1fa27cf8 ); /* 47 */
 	b = MD5.HH( b, c, d, a, x[ 2], MD5.S34, 0xc4ac5665 ); /* 48 */

 	// round 4
 	a = MD5.II( a, b, c, d, x[ 0], MD5.S41, 0xf4292244 ); /* 49 */
 	d = MD5.II( d, a, b, c, x[ 7], MD5.S42, 0x432aff97 ); /* 50 */
 	c = MD5.II( c, d, a, b, x[14], MD5.S43, 0xab9423a7 ); /* 51 */
 	b = MD5.II( b, c, d, a, x[ 5], MD5.S44, 0xfc93a039 ); /* 52 */
 	a = MD5.II( a, b, c, d, x[12], MD5.S41, 0x655b59c3 ); /* 53 */
 	d = MD5.II( d, a, b, c, x[ 3], MD5.S42, 0x8f0ccc92 ); /* 54 */
 	c = MD5.II( c, d, a, b, x[10], MD5.S43, 0xffeff47d ); /* 55 */
 	b = MD5.II( b, c, d, a, x[ 1], MD5.S44, 0x85845dd1 ); /* 56 */
 	a = MD5.II( a, b, c, d, x[ 8], MD5.S41, 0x6fa87e4f ); /* 57 */
 	d = MD5.II( d, a, b, c, x[15], MD5.S42, 0xfe2ce6e0 ); /* 58 */
 	c = MD5.II( c, d, a, b, x[ 6], MD5.S43, 0xa3014314 ); /* 59 */
 	b = MD5.II( b, c, d, a, x[13], MD5.S44, 0x4e0811a1 ); /* 60 */
 	a = MD5.II( a, b, c, d, x[ 4], MD5.S41, 0xf7537e82 ); /* 61 */
 	d = MD5.II( d, a, b, c, x[11], MD5.S42, 0xbd3af235 ); /* 62 */
 	c = MD5.II( c, d, a, b, x[ 2], MD5.S43, 0x2ad7d2bb ); /* 63 */
 	b = MD5.II( b, c, d, a, x[ 9], MD5.S44, 0xeb86d391 ); /* 64 */

 	MD5.state[0] += a;
 	MD5.state[1] += b;
 	MD5.state[2] += c;
 	MD5.state[3] += d;
};

/**
 * @access public
 * @static
 */
MD5.init = function()
{
	MD5.count[0] = MD5.count[1] = 0;
	MD5.state[0] = 0x67452301;
	MD5.state[1] = 0xefcdab89;
	MD5.state[2] = 0x98badcfe;
	MD5.state[3] = 0x10325476;

	for ( i = 0; i < MD5.digestBits.length; i++ )
    	MD5.digestBits[i] = 0;
};

/**
 * @access public
 * @static
 */
MD5.update = function( b )
{ 
	var index;
	var i;
 
 	index = MD5.and( MD5.shr( MD5.count[0],3 ) , 0x3f );
 
 	if ( MD5.count[0] < 0xffffffff - 7 )
	{
   		MD5.count[0] += 8;
    }
	else
	{
   		MD5.count[1]++;
   		MD5.count[0] -= 0xffffffff + 1;
        MD5.count[0] += 8;
	}
	
	MD5.buffer[index] = MD5.and( b, 0xff );
	
	if ( index >= 63 )
		MD5.transform( MD5.buffer, 0 );
};

/**
 * @access public
 * @static
 */
MD5.finish = function()
{
	var bits = new array( 8 );
 	var padding;
	 
 	var i      = 0;
	var index  = 0;
	var padLen = 0;

 	for ( i = 0; i < 4; i++ )
		bits[i] = MD5.and( MD5.shr( MD5.count[0], ( i * 8 ) ), 0xff );
    
	for ( i = 0; i < 4; i++ )
		bits[i+4] = MD5.and( MD5.shr( MD5.count[1], ( i * 8 ) ), 0xff );
 
 	index      = MD5.and( MD5.shr( MD5.count[0], 3 ), 0x3f );
 	padLen     = ( index < 56 )? ( 56 - index ) : ( 120 - index );
 	padding    = new array( 64 ); 
 	padding[0] = 0x80;
        
	for ( i = 0; i < padLen; i++ )
   		MD5.update( padding[i] );

	for ( i = 0; i < 8; i++ ) 
   		MD5.update( bits[i] );

 	for ( i = 0; i < 4; i++ )
	{
    	for ( j = 0; j < 4; j++ )
			MD5.digestBits[i * 4 + j] = MD5.and( MD5.shr( MD5.state[i], ( j * 8 ) ) , 0xff );
 	} 
};

/**
 * @access public
 * @static
 */
MD5.hexa = function( n )
{
	var hexa_h = "0123456789abcdef";
	var hexa_c = ""; 
	var hexa_m = n;

	for ( hexa_i = 0; hexa_i < 8; hexa_i++ )
	{
   		hexa_c = hexa_h.charAt( Math.abs( hexa_m ) % 16 ) + hexa_c;
   		hexa_m = Math.floor( hexa_m / 16 );
 	}
	
	return hexa_c;
};

/**
 * @access public
 * @static
 */
MD5.integer = function( n )
{
	return n%( 0xffffffff + 1 );
};

/**
 * @access public
 * @static
 */
MD5.shr = function( a, b )
{
	a = MD5.integer( a );
	b = MD5.integer( b );

	if ( a - 0x80000000 >= 0 )
	{
    	a = a % 0x80000000;
   	 	a >>= b;
    	a += 0x40000000 >> ( b - 1);
  	}
	else
	{
		a>>=b;
	}
	
	return a;
};

/**
 * @access public
 * @static
 */
MD5.shl1 = function( a )
{
	a = a % 0x80000000;

	if ( a&0x40000000 == 0x40000000 )
  	{
    	a -= 0x40000000;  
    	a *= 2;
    	a += 0x80000000;
  	}
	else
	{
		a *= 2;
	}

	return a;
};

/**
 * @access public
 * @static
 */
MD5.shl = function( a, b )
{
	a = MD5.integer( a );
	b = MD5.integer( b );

	for ( var i = 0; i < b; i++ )
		a = MD5.shl1( a );

	return a;
};

/**
 * @access public
 * @static
 */
MD5.and = function( a, b )
{
	a = MD5.integer( a );
	b = MD5.integer( b );

	var t1 = ( a - 0x80000000 );
  	var t2 = ( b - 0x80000000 );
  
  	if ( t1 >= 0 )
	{
    	if ( t2 >= 0 ) 
      		return ( ( t1&t2 ) + 0x80000000 );
    	else
    		return (t1&b);
  	}
	else
    {
		if ( t2 >= 0 )
      		return ( a&t2) ;
    	else
    		return ( a&b );
	}
};

/**
 * @access public
 * @static
 */
MD5.or = function( a, b )
{
	a = MD5.integer( a );
	b = MD5.integer( b );

	var t1 = ( a - 0x80000000 );
	var t2 = ( b - 0x80000000 );
  
  	if ( t1 >= 0 )
	{
    	if ( t2 >= 0 ) 
      		return ( ( t1|t2 ) + 0x80000000 );
    	else
      		return ( ( t1|b ) + 0x80000000 );
  	}
	else
	{
    	if ( t2 >= 0 )
      		return ( (a|t2 ) + 0x80000000 );
    	else
      		return ( a|b );  
	}
};

/**
 * @access public
 * @static
 */
MD5.xor = function( a, b )
{
	a = MD5.integer( a );
	b = MD5.integer( b );

	var t1 = ( a - 0x80000000 );
	var t2 = ( b - 0x80000000 );
  
	if ( t1 >= 0 ) 
	{
    	if ( t2 >= 0 ) 
      		return ( t1^t2 );
    	else
      		return ( ( t1^b ) + 0x80000000 );
  	}
	else
    {
		if ( t2 >= 0 )
      		return ( ( a^t2 ) + 0x80000000 );
    	else
      		return ( a^b );
	}
};

/**
 * @access public
 * @static
 */
MD5.not = function( a )
{
	a = MD5.integer( a );
	return ( 0xffffffff - a );
};

/**
 * @access public
 * @static
 */
MD5.F = function( x, y, z )
{
	return MD5.or( MD5.and( x, y ), MD5.and( MD5.not( x ), z ) );
};

/**
 * @access public
 * @static
 */
MD5.G = function( x, y, z )
{
	return MD5.or( MD5.and( x, z ), MD5.and( y, MD5.not( z ) ) );
};

/**
 * @access public
 * @static
 */
MD5.H = function( x, y, z )
{
	return MD5.xor( MD5.xor( x, y ), z );
};

/**
 * @access public
 * @static
 */
MD5.I = function( x, y, z )
{
	return MD5.xor( y ,MD5.or( x, MD5.not( z ) ) );
};

/**
 * @access public
 * @static
 */
MD5.rotateLeft = function( a, n )
{
	return MD5.or( MD5.shl( a, n ), ( MD5.shr( a, ( 32 - n ) ) ) );
};

/**
 * @access public
 * @static
 */
MD5.FF = function( a, b, c, d, x, s, ac )
{
	a = a + MD5.F( b, c, d ) + x + ac;
	a = MD5.rotateLeft( a, s );
	a = a + b;
	
	return a;
};

/**
 * @access public
 * @static
 */
MD5.GG = function( a, b, c, d, x, s, ac )
{
	a = a + MD5.G( b, c, d ) +x + ac;
	a = MD5.rotateLeft( a, s );
	a = a + b;

	return a;
};

/**
 * @access public
 * @static
 */
MD5.HH = function( a, b, c, d, x, s, ac )
{
	a = a + MD5.H( b, c, d ) + x + ac;
	a = MD5.rotateLeft( a, s );
	a = a + b;
	
	return a;
};

/**
 * @access public
 * @static
 */
MD5.II = function( a, b, c, d, x, s, ac )
{
	a = a + MD5.I( b, c, d ) + x + ac;
	a = MD5.rotateLeft( a, s );
	a = a + b;

	return a;
};
