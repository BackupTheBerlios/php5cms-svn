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
 * @access public
 * @static
 */
Math.square = function( x )
{
	return ( x * x );
};

/**
 * @access public
 * @static
 */
Math.degtorad = function( x )
{
	return ( x / ( 360 / ( 2 * Math.PI ) ) );
};

/**
 * @access public
 * @static
 */
Math.radtodeg = function( x )
{
	return ( x * ( 360 / ( 2 * Math.PI ) ) );
};

/**
 * @access public
 * @static
 */
Math.bintodec = function( s )
{
  	return parseInt( s, 2 );
};

/**
 * @access public
 * @static
 */
Math.dectobin = function( i )
{
  	return parseInt( i ).toString( 2 );
};

/**
 * @access public
 * @static
 */
Math.distance = function( x1, y1, x2, y2 )
{
	var d =  Math.round( Math.sqrt( ( ( x2 - x1 ) * ( x2 - x1 ) ) + ( ( y2 - y1 ) * ( y2 - y1 ) ) ) );
	
	if ( isNaN( d ) )
		d = 0;

	return d;
};

/**
 * @access public
 * @static
 */
Math.extatan = function( s, t )
{
	if ( s == 0.0 && t > 0.0)
	{
		angle = 90.0;
	}
	else if ( s == 0.0 && t < 0.0 )
	{ 
		angle = 270.0;
	}
	else if ( s < 0.0 ) 
	{
		angle = 180.0 + Math.radtodeg( Math.atan( t / s ) );
	}
	else if ( s > 0.0 && t < 0.0 )
	{
		angle = 360.0 + Math.radtodeg( Math.atan( t / s ) );
	}
	else
	{
		if ( s == 0.0 )
			s = 0.00001;
		
		angle = Math.radtodeg( Math.atan( t / s ) );
	}

	if ( angle < 0.0 )
		angle += 360.0;
        
	return angle;
};

/**
 * @access public
 * @static
 */
Math.angle = function( x1, y1, x2, y2 )
{
	var diffH = ( x1 - x2 );
	var diffV = ( y2 - y1 );

	if ( diffH )
	{
		var slope = diffV / diffH ;
		var angle = Math.atan( slope );
		var dgrs  = ( angle * 180 ) / Math.PI;
		
		if ( diffH < 0 )
			dgrs += 180;
	}
	else if ( diffV < 0 )
	{
		dgrs = 270;
	}
	else if ( diffV > 0 )
	{
		dgrs = 90;
	}
	else
	{
		dgrs = 0;
	}

	if ( dgrs < 0 )
		dgrs = 360 + dgrs;

	return Math.round( dgrs );
};

/**
 * Finds if a given point is within a polygon.
 *
 * @param  array  poly  array containing x/y coordinate pairs that describe
 *                      the vertices of the polygon. Format is identical to
 * 	                    that of HTML image maps, i.e. [x1,y1,x2,y2,...]
 * @param  int    px    the x-coordinate of the target point.
 * @param  int    py    the y-coordinate of the target point.
 *
 * @access public
 * @static
 */
Math.inpoly = function( poly, px, py )
{
	var npoints = poly.length; // number of points in polygon
	var xnew, ynew, xold, yold, x1, y1, x2, y2, i;
	var inside = false;

	if ( npoints / 2 < 3 )
	{
		// points don't describe a polygon
		return false;
	}
	
	xold = poly[npoints-2];
	yold = poly[npoints-1];
     
	for ( i = 0 ; i < npoints ; i = i + 2 )
	{
		xnew = poly[i];
		ynew = poly[i+1];
		
		if ( xnew > xold )
		{
			x1 = xold;
			x2 = xnew;
			y1 = yold;
			y2 = ynew;
		}
		else
		{
			x1 = xnew;
			x2 = xold;
			y1 = ynew;
			y2 = yold;
		}
		
		if ( ( xnew < px ) == ( px <= xold ) && ( ( py - y1 ) * ( x2 - x1 ) < ( y2 - y1 ) * ( px - x1 ) ) )
			inside=!inside;

		xold = xnew;
		yold = ynew;
	}

	return inside;
};

/**
 * @access public
 * @static
 */
Math.matrixmultiply = function( A, B )
{
	var retVal = [0,0,0];
	
	for ( var i = 0; i < 4; i++ )
	{
		retVal[i] = 0;
	
		for ( var j = 0; j < 4; j++ )
			retVal[i] += A[j] * B[i][j]
	}
	
	return retVal;
};

/**
 * @access public
 * @static
 */
Math.randomnumber = function( value1, value2 )
{
	if ( value2 > value1 )
		return ( Math.round( Math.random() * ( value2 - value1 ) ) + value1 );
	else
		return ( Math.round( Math.random() * ( value1 - value2 ) ) + value2 );
};

/**
 * @access public
 * @static
 */
Math.randomiwv = function()
{
	var rand = ( "" + Math.random() );
	var len  = rand.length;

	return rand.substr( len - 10, 10 );
};

/**
 * @access public
 * @static
 */
Math.binomial = function( n, format )
{
	var tmp;
	var pow1;
	var pow2;
		
	var out = "";
		
	if ( format == "txt" )
	{
	    pow1 = "^";
		pow2 = "";
	}
	else
	{
   	 	pow1 = "<sup>";
		pow2 = "</sup>";
	}
		
	if ( !Math.isinteger( n ) || n < 0 )
		return;
		
	for ( var i = 0; i <= n; i++ )
	{
	   	tmp = Math.choose( n, i );
			
		if ( tmp > 1 )
			out += tmp;
				
		if ( i > 1 )
			out += "x" + pow1 + i + pow2;
				
	   	if ( i == 1 )
			out += "x";
				
		if ( n - i > 1 )
			out += "y" + pow1 + ( n - i ) + pow2;
				
		if ( n - i == 1 )
			out += "y";
				
		if ( i < n )
			out += " + ";
	}
		
	return out;
};

/**
 * @access public
 * @static
 */
Math.choose = function( n, r )
{
	if ( !Math.isinteger( n ) || !Math.isinteger( r ) )
		return;
			
  	if ( r > n )
		return;
		
	return Math.factorial( n ) / ( Math.factorial( n - r ) * Math.factorial( r ) )
};

/**
 * @access public
 * @static
 */
Math.factorial = function( n )
{
	if ( n < 0 || !Math.isinteger( n ) )
		return;
			
	if ( n == 0 )
		return 1;
			
	if ( n == 1 )
		return n;
		
	return ( n * Math.factorial( n - 1 ) );
};

/**
 * @access public
 * @static
 */
Math.fibonacci = function( n )
{
	if ( n < 1 || !Math.isinteger( n ) )
		return;
			
	if ( n == 1 || n == 2 )
		return 1;
		
	return Math.fibonacci( n - 1 ) + Math.fibonacci( n - 2 );
};

/**
 * @access public
 * @static
 */
Math.iseven = function( n )
{
	if ( !Math.isinteger( n ) )
		return;
			
	return ( n % 2 == 0 );
};

/**
 * @access public
 * @static
 */
Math.isinteger = function( n )
{
	if ( isNaN( n ) )
		return false;
			
	return ( parseInt( "" + n ) == n );
};

/**
 * @access public
 * @static
 */
Math.isprime = function( n )
{
	if ( !Math.isinteger( n ) || n < 1 )
		return;
		
	for ( var i = 2; i < n - 1; i++ )
	{
		if ( n % i == 0 )
			return false;
	}
		
	return true;
};

/**
 * @access public
 * @static
 */
Math.isodd = function( n )
{
	if ( !Math.isinteger( n ) )
		return;
			
	return ( !Math.iseven( n ) );
};

/**
 * Round p to nearest n.
 *
 * @access public
 * @static
 */
Math.roundto = function( p, n )
{
	return ( Math.round( n / p ) ) * p;
};

/**
 * @access public
 * @static
 */
Math.roundoff = function( n, digits )
{
	var tmp;

	if ( isNaN( n ) || !Math.isinteger( digits ) )
		return;
			
	if ( digits < 0 )
		return null;
			
	if ( digits == 0 )
		return Math.round( n );
			
	tmp = Math.pow( 10, digits );
	return ( Math.round( n * tmp) / tmp );
};

/**
 * @access public
 * @static
 */
Math.tobinary = function( n )
{
	var tmp;
 	var out = "";

	if ( !Math.isinteger( n ) || n < 0 )
		return;
			
	if ( n == 0 )
		return 0;
			
	tmp = n;
	while ( tmp > 0 )
	{
		out = ( tmp % 2 ) + out;
		tmp = parseInt( tmp / 2 );
	}
		
	return out;
};

/**
 * @access public
 * @static
 */
Math.todecimal = function( n )
{
	var out = 0;
	var tmp = "";
	var startFound = false;
		
	tmp += n;
		
	if ( !Math.validbinarynumber( tmp ) )
		return;
		
	for ( var i = 0; i < tmp.length; i++ )
	{
		if ( tmp.charAt( i ) == '1' )
			startFound = true;
				
		if ( startFound )
			out += parseInt( tmp.charAt( i ) ) * Math.pow( 2, tmp.length - ( i + 1 ) );
	}
		
	return out;
};

/**
 * @access public
 * @static
 */
Math.validbinarynumber = function( n )
{
	for ( var i = 0; i< n.length; i++ )
	{
		if ( ( n.charAt( i ) != '0' ) && ( n.charAt( i ) != '1' ) )
			return false;
	}
		
	return true;
};

/**
 * @access public
 * @static
 */
Math.sq = function( x ) 
{
	return Math.pow( x, 2 );
};

/**
 * @access public
 * @static
 */
Math.csc = function( x ) 
{
	return 1 / Math.sin( x );
};

/**
 * @access public
 * @static
 */
Math.sec = function( x ) 
{
	return 1 / Math.cos( x );
};

/**
 * @access public
 * @static
 */
Math.cot = function( x ) 
{
	return 1 / Math.tan( x );
};

/**
 * @access public
 * @static
 */
Math.arccsc = function( x ) 
{
	return arcsin( 1 / x );
};

/**
 * @access public
 * @static
 */
Math.arcsec = function( x ) 
{
	return Math.acos( 1 / x);
};

/**
 * @access public
 * @static
 */
Math.arccot = function( x ) 
{
	return Math.atan( 1 / x );
};

/**
 * @access public
 * @static
 */
Math.sinh = function( x ) 
{
	return ( Math.exp( x ) - Math.exp( -x ) ) / 2; 
};

/**
 * @access public
 * @static
 */
Math.cosh = function( x ) 
{
	return ( Math.exp( x ) + Math.exp( -x ) ) / 2;
};

/**
 * @access public
 * @static
 */
Math.tanh = function( x ) 
{
	return Math.sinh( x ) / Math.cosh( x );
};

/**
 * @access public
 * @static
 */
Math.csch = function( x ) 
{
	return 1 / Math.sinh( x );
};

/**
 * @access public
 * @static
 */
Math.sech = function( x ) 
{
	return 1 / Math.cosh( x );
};

/**
 * @access public
 * @static
 */
Math.coth = function( x ) 
{
	return 1 / Math.tanh( x );
};

/**
 * @access public
 * @static
 */
Math.arcsinh = function( x ) 
{
	return Math.log( x + Math.sqrt( x * x + 1 ) );
};

/**
 * @access public
 * @static
 */
Math.arccosh = function( x ) 
{
	if ( x >= 1 ) 
		return Math.log( x + Math.sqrt( x * x - 1 ) );
};

/**
 * @access public
 * @static
 */
Math.arctanh = function( x ) 
{
	if ( x > 1 ) 
		return Math.log( ( 1 + x ) / ( 1 - x ) ) / 2;
};

/**
 * @access public
 * @static
 */
Math.arccsch = function( x ) 
{
	return Math.arcsinh( 1 / x );
};

/**
 * @access public
 * @static
 */
Math.arcsech = function( x ) 
{
	return Math.arccosh( 1 / x );
};

/**
 * @access public
 * @static
 */
Math.arccoth = function( x ) 
{
	return Math.arctanh( 1 / x );
};

/**
 * @access public
 * @static
 */
Math.base = function( x, a, b ) 
{
	if ( b == null ) 
	{
		b = a;
		a = 10;
	}
	
	if ( typeof( x ) == "number" ) 
	{
		j = new Array( Math.floor( Math.log2( x, 10 ) ) + 1 );
		
		for ( i = j.length; i > 0; i-- ) 
		{
			j[j.length - i] = Math.floor( x / Math.pow( 10, i - 1 ) );
			x %= Math.pow( 10, i - 1 );
		}
		
		x = j;
	}
	
	k = 0;
	
	for ( i = x.length; i > 0; i-- ) 
		k += x[x.length - i] * Math.pow( a, i - 1 );
		
	x = k;
	j = new Array( Math.floor( Math.log2( x, b ) ) + 1 );
	
	for ( i = j.length; i > 0; i-- ) 
	{
		j[j.length - i] = Math.floor( x / Math.pow( b, i - 1 ) );
		x %= Math.pow( b, i - 1 );
	}
	
	x = j;
	k = 1;
	
	for ( i = 0; i < x.length; i++ ) 
		k *= ( x[i] < 10 );
		
	if ( k ) 
	{
		k = 0;
		
		for ( i = x.length; i > 0; i-- ) 
			k += x[x.length - i] * Math.pow( 10, i - 1 );

		x = k;
	}
	
	return x;
};

/**
 * @access public
 * @static
 */
Math.factor = function( x ) 
{
	k = 0;
	d = new Array();
	
	for ( i = 2; i <= Math.sqrt( x ); i += k ) 
	{
		if ( k < 2 ) 
			k++;
		
		for ( j = 0; x % i == 0; j++ )  
		{
			x /= i;
			d[d.length] = i;
		}
	}
	
	if ( x != 1 || d.length == 0 ) 
		d[d.length] = x;
		
	return d;
};

/**
 * @access public
 * @static
 */
Math.gcd = function( a ) 
{
	r = a[0];
	
	for ( i = 1; i < a.length; i++ ) 
	{
		c = a[i];
		
		while ( c != 0 ) 
		{
			r -= c * Math.floor( r / c );
			k  = c;
			c  = r;
			r  = k;
		}
	}
	
	return r;
};

/**
 * @access public
 * @static
 */
Math.lcm = function( a ) 
{
	s = a[0];
	
	for ( j = 1; j < a.length; j++ )
		s *= a[j] / Math.gcd( [s, a[j]] );
	
	return s;
};

/**
 * @access public
 * @static
 */
Math.log2 = function( x, b ) 
{
	if ( b == null ) 
		b = e;
	
	return Math.log( x ) / Math.log( b );
};

/**
 * @access public
 * @static
 */
Math.pfactorial = function( x ) 
{
	k = 1;
	
	for ( j = 3; j <= x; j += 2 ) 
	{
		if ( Math.isprime( j ) ) 
			k *= j;
	}
	
	if ( x > 2 ) 
		k *= 2;
		
	return k;
};

/**
 * @access public
 * @static
 */
Math.product = function( a ) 
{
	k = 1;
	
	for ( i = 0; i < a.length; i++ ) 
		k *=a [i];
		
	return k;
};

/**
 * @access public
 * @static
 */
Math.max2 = function( a ) 
{
	return a.sort( function( b, c ) 
	{
		return c - b;
	} )[0];
};

/**
 * @access public
 * @static
 */
Math.min2 = function( a ) 
{
	return a.sort( function( b, c ) 
	{
		return b - c;
	} )[0];
};

/**
 * @access public
 * @static
 */
Math.totient = function( x ) 
{
	k = 1;
	
	for ( j = 2; j < x; j++ )
	{
		if ( Math.gcd( [x, j] ) == 1 ) 
			k++;
	}
		
	return k;
};

/**
 * @access public
 * @static
 */
Math.primepi = function( x ) 
{
	k = 1;
	
	if ( x % 2 == 0 ) 
		j++;
	
	for ( j = 3; j <= x; j += 2 ) 
	{
		if ( Math.isprime( j ) ) 
			k++;
	}
		
	return k;
};

/**
 * @access public
 * @static
 */
Math.sum = function( a ) 
{
	k = 0;
	
	for ( i = 0; i < a.length; i++ ) 
		k += a[i];
		
	return k;
};

/**
 * @access public
 * @static
 */
Math.sign = function( x ) 
{
	if ( x == 0 ) 
		return 0;
	else 
		return x / Math.abs( x );
};
