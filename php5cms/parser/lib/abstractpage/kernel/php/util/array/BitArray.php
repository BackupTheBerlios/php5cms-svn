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
|Authors: Cornelius Bolten <c.bolten@grafiknews.de>                    |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * This is a PHP-port of the #.NET-BitArray Class.
 * This Class manages a compact array of bit values, which are represented as Booleans, 
 * where true indicates that the bit is on (1) and false indicates the bit is off (0).
 *
 * @package util_array
 */
	
class BitArray extends PEAR
{
	/**
	 * @access private         
	 * @var BitString       
	 */		
	var $_bitString;
		
	/**
	 * @access private         
	 * @var BitArrayLength       
	 */		
	var $_length = 0;		
		
		
	/**
	 * Constructor
	 *
	 * Initializes a new instance of the BitArray class that can hold the 
	 * specified number of bit values, which are initially set to false.
	 *
	 * @access public
	 * @param  integer number of bit values
	 */		
	function BitArray( $BitArrayLength ) 
	{
		if ( $BitArrayLength >= 33 )
		{
			$this = new PEAR_Error( "BitArrayLength can not be greater than 32." );
			return;
		}
		
		$this->_length = $BitArrayLength - 1;
		$this->_setupBitArray();
	}		
		
	
	/**
	 * @access public
	 */
	function setBitArray( $dec )
	{
		$this->_bitString = str_pad( (string)decbin( $dec ), $this->_length, "0", STR_PAD_LEFT );
	}
		
	/**
	 * @access public
	 */
	function getBitArray()
	{
		return bindec( $this->_bitString );
	}
		
	/**
	 * Sets the bit at a specific position in the BitArray to the specified value.
	 *
	 * @access public
	 * @param  integer index
	 * @param  boolean value
	 * @return boolean	
	 */			
	function set( $index, $value )
	{
		if ( $index >= $this->_length )
			return false;
				
		if ( !is_bool( $value ) )
			return false;
		else 
			$this->_bitString[$index] = $this->_bool2int( $value );
				
		return true;
	}
		
	/**
	 * Sets all bits in the BitArray to the specified value.
	 *
	 * @access public
	 * @param  boolean value
	 * @return boolean	
	 */			
	function setAll( $value )
	{
		if ( !is_bool( $value ) ) 
		{
			return false;
		} 
		else
		{
			for ( $i = 0; $i <= $this->_length; $i++ )
				$this->_bitString[$i] = $this->_bool2int( $value );
		}
			
		return true;
	}		
		
	/**
	 * Gets the value of the bit at a specific position in the BitArray.
	 *
	 * @access public
	 * @param  integer index
	 * @return boolean	
	 */			
	function get( $index ) 
	{
		if ( $index >= $this->_length )
			return false;
		else			
			return $this->_int2bool( $this->_bitString[$index] );
	}
		
	/**
	 * Gets all values of the bits in the BitArray.
	 *
	 * @access public
	 * @return array	
	 */			
	function getAll()
	{
		for ( $i = 0; $i <= $this->_length; $i++ )
			$return[] =	$this->get( $i );
			
		return $return;
	}		
		
	/**
	 * Performs the bitwise OR operation on the elements in the current 
	 * BitArray against the corresponding elements in the specified BitArray
	 *
	 * @access public
	 * @param BitArray 
	 * @return array	
	 */			
	function _or( $CompBitArray ) 
	{
		for ( $i = 0; $i <= $this->_length; $i++ )
			$result[$i]	= ( $this->get( $i ) or $CompBitArray->get( $i ) );
		
		return $result;
	}	
		
	/**
	 * Performs the bitwise eXclusive OR operation on the elements in the current 
	 * BitArray against the corresponding elements in the specified BitArray.
	 *
	 * @access public
	 * @param  BitArray 
	 * @return array	
	 */			
	function _xor( $CompBitArray ) 
	{
		for ( $i = 0; $i <= $this->_length; $i++ ) 
			$result[$i]	= ( $this->get( $i ) xor $CompBitArray->get( $i ) );
			
		return $result;
	}
		
	/**
	 * Inverts all the bit values in the current BitArray, so that elements set to 
	 * true are changed to false, and elements set to false are changed to true. 
	 *
	 * @access public
	 */			
	function _not()
	{
		for ( $i = 0; $i <= $this->_length; $i++ ) 
		{
			$val = !$this->get( $i );
			$this->set( $i, $val );
		}
		
		return true;
	}	
		
	/**
	 * Performs the bitwise AND operation on the elements in the current 
	 * BitArray against the corresponding elements in the specified BitArray 
	 *
	 * @access public
	 * @param  BitArray 
	 * @return array		
	 */			
	function _and( $CompBitArray )
	{
		for ( $i = 0; $i <= $this->_length; $i++ )
			$result[$i]	= ( $this->get( $i ) and $CompBitArray->get( $i ) );
				
		return $result;
	}							

	
	// private methods
	
	/**
	 * Converts a int to the equivalent bool.
	 *
	 * @access private
	 * @param  string value
	 * @param  boolean value	
	 */
	function _int2bool( $value ) 
	{
		if ( $value == '1' )
			return true;
		else
			return false;
	}
		
	/**
	 * Converts a bool to the equivalent integer value as string.
	 *
	 * @access private
	 * @param  boolean value
	 * @param  string value	
	 */		
	function _bool2int( $value ) 
	{
		if ( $value == true )
			return '1';
		else
			return '0';			
	}

	/**
	 * Sets up the BitArray with the length passed to the constructor.
	 *
	 * @access private
	 * @param  string [$libPath] Path to Library
	 * @param  array [$libPath] Paths to Library	
	 */		
	function _setupBitArray()
	{
		for ( $i = 0; $i <= $this->_length; $i++ )
			$this->_bitString .= $this->_bool2int( false );
	}
} // END OF BitArray

?>
