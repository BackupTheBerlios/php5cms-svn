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
Number.parseInt = function( s ) 
{
	s = s.replace(/ /, '');
	s = s.replace(/'/, '');
	
	return parseInt( s );
};

/**
 * @access public
 * @static
 */
Number.sign = function( y ) 
{ 
	return ( ( y < 0 )? '-' : '' ) 
};

/**
 * @access public
 * @static
 */
Number.prepend = function( Q, L, c ) 
{ 
	var S = Q + '';

	if ( c.length > 0 )
	{
		while ( S.length < L )
			S = c + S;
	}
			
	return S; 
};

/**
 * @access public
 * @static
 */
Number.strU = function( X, M, N ) 
{
	var T;
	var S = new String( Math.round( X * Number( "1e" + N ) ) );
	
	if (/\D/.test( S ) )
		return '' + X;
		
	with ( new String( Number.prepend( S, M + N, '0' ) ) )
	return substring( 0, T = ( length - N ) ) + '.' + substring( T ); 
};

/**
 * @access public
 * @static
 */
Number.strT = function( X, M, N ) 
{ 
	return Number.prepend( Number.strU( X, 1, N ), M + N + 2, ' ' ); 
};

/**
 * @access public
 * @static
 */
Number.strS = function( X, M, N ) 
{
	return Number.sign( X ) + Number.strU( Math.abs( X ), M, N );
};

/**
 * @access public
 * @static
 */
Number.strW = function( X, M, N ) 
{ 
	return Number.prepend( Number.strS( X, 1, N ), M + N + 2, ' ' ); 
};
