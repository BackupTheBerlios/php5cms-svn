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


using( 'util.math.MathUtil' );


/**
 * @package util_math
 */
 
class ComplexNumber extends PEAR
{
	/**
	 * @access public
	 */
	var $real;
	
	/**
	 * @access public
	 */
	var $im;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function ComplexNumber( $r, $i ) 
	{
		$this->real = $r;
		$this->im   = $i;
	}
	

	/**
	 * @access public
	 */	
	function tostr() 
	{
		return ( $this->real . " + " . $this->im . "j" );
	}

	/**
	 * @access public
	 */
	function tohtml() 
	{
		return ( "<CODE>" . $this->real . " + " . $this->im . "<B>j</B></CODE>" );
	}

	/**
	 * @access public
	 */	
	function conjugate() 
	{
		return new ComplexNumber( $this->real, -1 * $this->im );
	}

	/**
	 * @access public
	 */	
	function negate() 
	{
		return new ComplexNumber( -1 * $this->real, -1 * $this->im );
	}

	/**
	 * @access public
	 */
	function inverse() 
	{
		$r = $this->real / ( pow( $this->real, 2 ) + pow( $this->im, 2 ) );
		$i = $this->im   / ( pow( $this->real, 2 ) + pow( $this->im, 2 ) );
		
		return new ComplexNumber( $r, $i );
	}

	/**
	 * @access public
	 */
	function abs() 
	{
		$r = $this->real; 
		$i = $this->im;
		
		return sqrt( $r * $r + $i * $i );
	}

	/**
	 * @access public
	 */	
	function arg() 
	{
		$r = $this->real; 
		$i = $this->im;
		
		return atan( $i / $r );
	}

	/**
	 * @access public
	 */
	function angle() 
	{
		return $this->arg();
	}

	/**
	 * @access public
	 */
	function getreal() 
	{
		return $this->real;
	}

	/**
	 * @access public
	 */
	function getim() 
	{
		return $this->im;
	}

	/**
	 * @access public
	 */
	function sine() 
	{
		$a = $this->real; 
		$b = $this->im;
		$r = sin( $a ) * MathUtil::cosh( $b );
		$i = cos( $a ) * MathUtil::sinh( $b );
		
		return new ComplexNumber( $r, $i );
	}

	/**
	 * @access public
	 */
	function cosine() 
	{
		$a = $this->real; 
		$b = $this->im;
		$r = cos( $a ) * MathUtil::cosh( $b );
		$i = sin( $a ) * MathUtil::sinh( $b );
		
		return new ComplexNumber( $r, $i );
	}

	/**
	 * @access public
	 */
	function tangent() 
	{
		$a   = $this->real; 
		$b   = $this->im;
		$den = 1 + pow( tan( $a ), 2 ) * pow( MathUtil::tanh( $b ), 2 );
		$r   = pow( MathUtil::sech( $b ), 2 ) * tan( $a ) / $den;
		$i   = pow( MathUtil::sec( $a ),  2 ) * MathUtil::tanh( $b ) / $den;
		
		return new ComplexNumber( $r, $i );
	}	

	/**
	 * @access public
	 */
	function equal( $c2 ) 
	{
		return ( $this->real == $c2->real && $this->im == $c2->im );
	}

	
	// methods below need a valid ComplexNumber object as parameter

	/**
	 * @access public
	 */
	function add( $c2 ) 
	{
		return new ComplexNumber( $this->real + $c2->real, $this->im + $c2->im );
	}

	/**
	 * @access public
	 */
	function sub( $c2 ) 
	{
		return $this->add( $c2->negate() );
	}

	/**
	 * @access public
	 */
	function mult( $c2 ) 
	{
		$r = ( $this->real * $c2->real ) - ( $this->im * $c2->im );
		$i = $this->im + $c2->im;
		
		return new ComplexNumber( $r, $i );
	}

	/**
	 * @access public
	 */
	function div( $c2 ) 
	{
		$a = $this->real; 
		$b = $this->im;
		$c = $c2->real; 
		$d = $c2->im;
		$r = ( $a * $c + $b * $d ) / ( $c * $c + $d * $d );
		$i = ( $b * $c - $a * $d ) / ( $c * $c + $d * $d );
		
		return new ComplexNumber( $r, $i );
	}
} // END OF ComplexNumber

?>
