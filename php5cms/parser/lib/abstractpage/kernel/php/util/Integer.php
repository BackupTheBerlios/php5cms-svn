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


/**
 * Integer type that supports large numbers, conversion to number formats 
 * up to base 36, and arithmitic in up to 36 base numbers.
 *
 * @package util
 */
 
class Integer extends PEAR 
{
    /**
	 * The value of the integer.
     * @access private
     */
    var $_val;
    
	/**
	 * The numeric base of the integer.
     * @access private
     */
    var $_base;
    
	/**
	 * Is this a negative number?
     * @access private
     */
    var $_negative;
    
	
    /**
     * Constructor. 
	 * Sets the integer value.
	 *
     * @param int/String $val  The Integer value.
     * @param int        $base The base of this number.
     */
    function Integer( $val = 0, $base = 10 ) 
	{
        $this->set( $val, $base );
    }
    
    /**
     * Sets the integer value.
	 *
	 * @access public
     * @param int/String $val  The Integer value.
     * @param int        $base The base of this number.
     */
    function set( $val, $base = 10 ) 
	{
        if ( $val[0] == '-' )
            $this->_negative = true;
        
        $this->_base = $base;
        
        if ( is_int( $val ) )
            $val = '' . $val;
        
        $this->_val = $val;
    }
    
    /**
	 * Gets the value in decimal format.
	 *
     * @access public
     * @return String The decimal value.
     */
    function get() 
	{
        return $this->_val;
    }
    
    /**
	 * Gets the base the integer is in.
	 *
     * @access public
     * @return int The base the integer is in.
     */
    function getBase() 
	{
        return $this->_base;
    }
    
    /**
	 * Convert the integer into binary format.
	 *
     * @access public
     * @param int $len The minimum length of the integer. If it is longer than the number's actual length, zeros are prepended to the number.
     * @return The Integer in binary format.
     */
    function toBin( $len = 0 )  
	{
        return str_pad( Integer::convert( $this->_val, $this->_base, 2 ), $len, '0', STR_PAD_LEFT );
    }
    
    /**
	 * Convert the integer into octal format.
	 *
     * @access public
     * @param int $len The minimum length of the integer. If it is longer than the number's actual length, zeros are prepended to the number.
     * @return The Integer in octal format.
     */
    function toOct( $len = 0 ) 
	{
        return str_pad( Integer::convert( $this->_val, $this->_base, 8 ), $len, '0', STR_PAD_LEFT );
    }
    
    /**
	 * Convert the integer into decimal format.
	 *
     * @access public
     * @param int $len The minimum length of the integer. If it is longer than the number's actual length, zeros are prepended to the number.
     * @return The Integer in decimal format.
     */
    function toDec( $len = 0 ) 
	{
        // Convert to string.
        return  str_pad( Integer::convert( $this->_val, $this->_base, 10 ), $len, '0', STR_PAD_LEFT );
    }
    
    /**
	 * Convert the integer into hex format.
	 *
     * @access public
     * @param int $len The minimum length of the integer. If it is longer than the number's actual length, zeros are prepended to the number.
     * @return The Integer in hex format.
     */
    function toHex( $len = 0 ) 
	{
        return str_pad( Integer::convert( $this->_val, $this->_base, 16 ), $len, '0', STR_PAD_LEFT );
    }
    
    /**
	 * Convert the number from one number base to another up to 36 base. 
	 * This is a Class method. An object does not need to be instantiated 
	 * for this to be used. Usage is Integer::convert($val, $inSys, $outSys)
     *
     * @access public
     * @param  int/String $input      The number to be converted.
     * @param  int        $inputBase  The Input base system.
     * @param  int        $outputBase The Output base system.
     * @return String                 The converted number.
     */
    function convert( $input, $inputBase = 10, $outputBase = 10 ) 
	{
        if ( $inputBase == $outputBase )
            return $input;
        
        // If '0', return val.
        if ( $input == '0' )
            return $input;
        
        $output = '';
        $divmod = array();
        $outBaseInInBase = ltrim( Integer::_convertSingle( Integer::_baseVal( $outputBase ), $inputBase ), '0' );
        
        while ( 1 ) 
		{
            if  (Integer::compare( $input, $outBaseInInBase ) < 0 ) 
			{    
                $output = Integer::_convertSingle( Integer::_baseVal( $input ), $outputBase ) . $output;
                break;
            }
            
            $divmod = Integer::divmod( $input, $outBaseInInBase, $inputBase );
            $r      = $divmod['mod'];
            $input  = $divmod['div'];
            $output = Integer::_convertSingle( $r, $outputBase ) . $output;
        }
        
        return $output;
    }
        
    /**
	 * Compares two Integers to see which is greater or if they are equal.
	 *
     * @access public
     * @param  String $a The first number to be compared.
     * @param  String $b The second number to be compared.
     * @return int       If $a > $b return 1.<br>If $a < $b return -1.<br>If $a == $b return 0.
     */
    function compare( $a, $b ) 
	{
        $lenA = strlen( $a );
        $lenB = strlen( $b );
        $len  = ( $lenA > $lenB )? $lenA : $lenB;
        
        $a = str_pad( $a, $len, '0', STR_PAD_LEFT );
        $b = str_pad( $b, $len, '0', STR_PAD_LEFT );
        
        if ( $a < $b )
            return -1;
        else if ( $a > $b )
            return 1;
        else if ( $a == $b )
            return 0;
    }
    
    /**
	 * Adds two numbers. The numbers must be in the same base system.
	 *
     * @access public
     * @param  int/String $a    The number on the left side of the add.
     * @param  int/String $b    The number on the right side of the add.
     * @param  int        $base The base system where the addition will take place.
     * @return int               The sum.
     */
    function add( $a, $b, $base = 10 ) 
	{
        $negA = ( $a[0] == '-' )? true : false;
        $negB = ( $b[0] == '-' )? true : false;
        
        $a = strtoupper( $a );
        $b = strtoupper( $b );
        
        // Get rid of nonvalid characters.
        $a = Integer::_trimString( $a, $base );
        $b = Integer::_trimString( $b, $base );
        
        // Handle negative numbers.
        if ( $negA === true && $negB === false )
            return Integer::sub( $b, $a, $base );
        else if ( $negA === false && $negB === true )
            return Integer::sub( $a, $b, $base );
        else if ( $negA === true && $negB === true )
            return '-'.Integer::add( $a, $b, $base );
        
        $lenA = strlen( $a );
        $lenB = strlen( $b );
        $len  = ( $lenA > $lenB )? $lenA : $lenB;
        
        $a = str_pad( $a, $len, '0', STR_PAD_LEFT );
        $b = str_pad( $b, $len, '0', STR_PAD_LEFT );
        
        $i = $len - 1;
        $c = 0;
        
		$sum = '';
        
        // Add up all the numbers.
        while ( $i >= 0 || $c > 0 ) 
		{
            if ( $i >= 0 ) 
			{
                // Get the current number.
                $valA = Integer::_decVal( $a[$i] );
                $valB = Integer::_decVal( $b[$i] );
            } 
			else 
			{
                // We are past the range of the two added numbers.
                $valA = 0;
                $valB = 0;
            }
            
            $r = $valA + $valB + $c;
			
            if ( $r < $base ) 
			{
                $c = 0;
            } 
			else 
			{
                $c = 1;
                $r = ( $r - $base );
            }
			
            // Convert to base.
            $r = Integer::_baseVal( $r );
            
            $sum = $r . $sum;
            $i--;
        }
		
        return $sum;
    }
    
    /**
	 * Subtract two numbers. The numbers must be in the same base system.
	 *
     * @access public
     * @param  int/String $a    The number to be subtracted from.
     * @param  int/String $b    The subtractor.
     * @param  int        $base The base system where the subtraction will take place.
     * @return int              The difference.
     */
    function sub( $a, $b, $base = 10 ) 
	{
        $negA = ( $a[0] == '-' )? true : false;
        $negB = ( $b[0] == '-' )? true : false;
        
        $a = strtoupper( $a );
        $b = strtoupper( $b );
        
        // Get rid of nonvalid characters.
        $a = Integer::_trimString( $a, $base );
        $b = Integer::_trimString( $b, $base );
        
        // Handle negative numbers.
        if ( $negA === true && $negB === false )
            return '-' . Integer::add( $a, $b, $base );
        else if ( $negA === false && $negB === true )
            return Integer::add( $a, $b, $base );
        else if ( $negA === true && $negB === true )
            return Integer::sub( $b, $a, $base );
        
        $lenA = strlen( $a );
        $lenB = strlen( $b );
        $len  = ( $lenA > $lenB )? $lenA : $lenB;
        
        $a = str_pad( $a, $len, '0', STR_PAD_LEFT );
        $b = str_pad( $b, $len, '0', STR_PAD_LEFT );
        
        // Make sure first arg is a larger number.
        if ( $b > $a )
            return '-' . Integer::sub( $b, $a, $base );
        
        $c = false;
        $difference = '';
		
        for ( $i = $len - 1; $i >= 0; $i-- ) 
		{
            if ( $c === false )
                $valA = Integer::_decVal( $a[$i] );
            
            $valB = Integer::_decVal( $b[$i] );
            $r = $valA - $valB;
            
            // Is $r not negative?
            if ( $r >= 0 ) 
			{
                // $r is not negative. Set carry to false.
                $c = false;
            } 
			else 
			{
                // $r is negative. Carry down the number
                $valA = Integer::_decVal( $a[$i-1] ) - 1;
                
                // Carry increases $r by the system value.
                $r += $base;
                
                // Set carry to true;
                $c = true;
            }
            
            $r = Integer::_baseVal( $r );
            $difference = $r . $difference;
        }
		
        $difference = ltrim( $difference, '0' );
        return ( $difference != '' )? $difference : '0';
    }
    
    /**
	 * Multiplies two numbers. The numbers must be in the same base system.
	 *
     * @access public
     * @param  int/String $a    The number on the left side of the multiplication.
     * @param  int/String $b    The number on the right side of the multiplication.
     * @param  int        $base The base system where the multiplication will take place.
     * @return int              The product.
     */
    function mul( $a, $b, $base = 10 ) 
	{
        $negA = ( $a[0] == '-' )? true : false;
        $negB = ( $b[0] == '-' )? true : false;
        
        $a = strtoupper( $a );
        $b = strtoupper( $b );
        
        // Get rid of nonvalid characters.
        $a = Integer::_trimString( $a, $base );
        $b = Integer::_trimString( $b, $base );
        
        if ( $negA == '-' xor $negB == '-' )
            return '-'.Integer::mul( $a, $b, $base );
        
        $lenA = strlen( $a );
        $lenB = strlen( $b );
        
        // $b is supposed to be shorter
        if ( $lenB > $lenA )
            return Integer::mul( $b, $a, $base );
        
        // the total product
        $prod = '0';
        
		// cycle through all $b numbers
        for ( $i = 0; $i < $lenB; $i++ ) 
		{
            $valB = Integer::_decVal( $b[( $lenB - 1 ) - $i] );
            $val  = '';
            $c    = 0;
            $j    = $lenA - 1;
            
			// Multiply $b cycled through all $a numbers.
            while ( $j >= 0 || $c > 0 ) 
			{
                // If $a still has characters, get one.
                $valA = ( $j < 0 )? 0 : Integer::_decVal( $a[$j] );
                $res  = $valA * $valB + $c;
                
                if ( $res < $base ) 
				{
                    $c   = 0;
                    $val = Integer::_baseVal( $res ) . $val;
                } 
				else 
				{
                    $c   = (int)( $res / $base );
                    $val = Integer::_baseVal( $res % $base ) . $val;
                }
				
                $j--;
            }
			
            // Pad $val with 0's behind it. Then Add $val to $prod.
            $prod = Integer::add( $val . str_pad( '', $i, 0 ), $prod, $base );
        }
		
        return $prod;
    }
    
    /**
	 * Divides two numbers. Returns the quotient. The numbers must be in the same base system.
	 *
     * @access public
     * @param  int/String $a    The numerator.
     * @param  int/String $b    The denominator.
     * @param  int        $base The base system where the addition will take place.
     * @return int              The Quotient.
     */
    function div( $a, $b, $base = 10 ) 
	{
        $d = Integer::divmod( $a, $b, $base );
        return $d['div'];
    }
    
    /**
	 * Divides two numbers. Returns the remainder. The numbers must be in the same base system.
     *
	 * @access public
     * @param  int/String $a    The numerator.
     * @param  int/String $b    The denominator.
     * @param  int        $base The base system where the addition will take place.
     * @return int              The Remainder.
     */
    function mod( $a, $b, $base = 10 ) 
	{
        $d = Integer::divmod( $a, $b, $base );
        return $d['mod'];
    }
    
    /**
	 * Divides two numbers. Returns an array of the quotient and the remainder.
	 *
     * @access public
     * @param  int/String $a    The numerator.
     * @param  int/String $b    The denominator.
     * @param  int        $base The base system where the addition will take place.
     * @return int[]            The array containing the quotient and remainder. Array is array('div' => quotient, 'mod' => remainder).
     */
    function divmod( $a, $b, $base ) 
	{
        $negA = ( $a[0] == '-' )? true : false;
        $negB = ( $b[0] == '-' )? true : false;
        
        $a = strtoupper( $a );
        $b = strtoupper( $b );
        
        // Get rid of nonvalid characters.
        $a = Integer::_trimString( $a, $base );
        $b = Integer::_trimString( $b, $base );
        
        if ( Integer::compare( $a, $b ) == -1 )
            return '0';
        
        $len  = strlen( $a );
        $quot = '';
        $r    = '';
        
        for ( $i = 0; $i < $len; $i++ ) 
		{
            $r .= $a[$i];    
            $cVal = 0;
            
            if ( Integer::compare( $r, $b ) >= 0 ) 
			{
                // Subtract until $r < $b
                do 
				{
                    $cVal++;
                    
                    // See if the next subtr
                    $r = Integer::sub( $r, $b, $base );
                } while ( Integer::compare( $r, $b ) >= 0 );
            }
			
            $quot .= Integer::_baseVal( $cVal );
        }
        
        $quot = ltrim( $quot, '0' );
        $r    = ltrim( $r,    '0' );
        
        if ( $quot == '' )
            $quot = '0';
        
        if ( $r == '' )
            $r = '0';
        
        // Is this a negative product?
        if ( $negA xor $negB )
            $quot = '-'.$quot;
        
        return array(
			'div' => $quot,
			'mod' => $r
		);
    }
    
	
	// private methods
	
    /**
	 * Removes characters that are not part of the base number system from the string.
	 *
     * @access private
     * @param  String $string The number.
     * @param  int    $base   The base number system.
     * @return String         The converted number.
     */
    function _trimString( $string, $base ) 
	{
        if ( $base <= 10 )
            $rep = '/[^0-'.($base-1).']/';
        else if ( $base > 10 && $base <= 36 )
            $rep = '/[^0-9A-' . chr( ord( 'A' ) + ( $base - 11 ) ) . ']/';
        else
            return false;
        
        return preg_replace( $rep, '', $string );
    }
    
    /**
	 * Gets the decimal value of the character in a certain number format.
	 *
     * @access private
     * @param  char $chr The number in its original base format.
     * @return int       The base10 equivalent.
     */
    function _decVal( $chr ) 
	{
        if ( $chr >= '0' && $chr <= '9' )
            return ord( $chr ) - ord('0');
        else if ( $chr >= 'A' && $chr <= 'Z' )
            return 10 + ( ord( $chr ) - ord( 'A' ) );
        else
            return false;
    }
    
    /**
	 * Gets the equivalent character from a base10 number.
	 *
     * @access private
     * @param  int $val The number to be converted.
     * @return char     The converted number in up to base36.
     */
    function _baseVal( $val ) 
	{
        if ( $val >= 0 && $val <= 9 )
            return '' . $val;
        else if ( $val >= 10 && $val <= 36 )
            return chr( ord( 'A' ) + ( $val - 10 ) );
        else
            return false;
    }
	
    /**
	 * Converts a single integer digit into the appropriate number base system..
	 *
     * @access private
     * @param char $in     The Integer Digit in any base system.
     * @param int  $base The system that the Integer digit will be converted to.
     * @return String      The converted digit.
     */
    function _convertSingle( $in, $base ) 
	{
        $in    = $in[0];
        $inVal = Integer::_decVal( $in );
        
        if ( $inVal < $base )
            return $in;
        
        $outVal = '';
        while ( $inVal > 0 ) 
		{
            $r      = $inVal % $base;
            $outVal = Integer::_baseVal( $r ) . $outVal;
            $inVal  = (int)$inVal / $base;
        }
        
        return $outVal;
    }
} // END OF Integer

?>
