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
 * @package util
 */
 
class Barcode128 extends PEAR
{
	/**
	 * @access public
	 */
	var $data;
	
	/**
	 * @access private
	 */
	var $_b_codeset_table;
	
	/**
	 * @access private
	 */
	var $_a_codeset_table;
	
	/**
	 * @access private
	 */
	var $_pattern_table;
	
	/**
	 * @access private
	 */
	var $_codeset;
	
	/**
	 * @access private
	 */
	var $_pattern;

		
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Barcode128( $data, $code_set = 'B' )
	{
		$code_set = strtoupper( $code_set );
			
		if ( ( $code_set != 'A' ) && ( $code_set != 'B' ) )
		{
			$this = new PEAR_Error( "Codeset not found " . $code_set );
			return;
		}

		$this->_pattern = array();
		$this->_codeset = strtoupper( $code_set );
		$this->data     = $data;
			
		$this->_a_codeset_table = array(
			' '		  => 0,
			'!'		  => 1,
			'"'		  => 2,
			'#'		  => 3,
			'$'		  => 4,
			'%'		  => 5,
			'&'		  => 6,
			'\''	  => 7,
			'('		  => 8,
			')'		  => 9,
			'*'		  => 10,
			'+'		  => 11,
			','		  => 12,
			'-'		  => 13,
			'.'		  => 14,
			'/'		  => 15,
			'0'		  => 16,
			'1'		  => 17,
			'2'		  => 18,
			'3'		  => 19,
			'4'		  => 20,
			'5'		  => 21,
			'6'		  => 22,
			'7'		  => 23,
			'8'		  => 24,
			'9'		  => 25,
			':'		  => 26,
			';'		  => 27,
			'<'		  => 28,
			'='		  => 29,
			'>'		  => 30,
			'?'		  => 31,
			'@'		  => 32,
			'A'		  => 33,
			'B'		  => 34,
			'C'		  => 35,
			'D'		  => 36,
			'E'		  => 37,
			'F'		  => 38,
			'G'		  => 39,
			'H'		  => 40,
			'I'		  => 41,
			'J'		  => 42,
			'K'		  => 43,
			'L'		  => 44,
			'M'		  => 45,
			'N'		  => 46,
			'O'		  => 47,
			'P'		  => 48,
			'Q'		  => 49,
			'R'		  => 50,
			'S'		  => 51,
			'T'		  => 52,
			'U'		  => 53,
			'V'		  => 54,
			'W'		  => 55,
			'X'		  => 56,
			'Y'		  => 57,
			'Z'		  => 58,
			'['		  => 59,
			'\\'	  => 60,
			']'		  => 61,
			'^'		  => 62,
			'_'		  => 63,
			'Start A' => 103,
			'Start B' => 104,
			'Start C' => 105,
			'Stop'	  => 106
		);

		$this->_b_codeset_table = array(
			' '		  => 0,
			'!'		  => 1,
			'"'		  => 2,
			'#'		  => 3,
			'$'		  => 4,
			'%'		  => 5,
			'&'		  => 6,
			'\''	  => 7,
			'('		  => 8,
			')'		  => 9,
			'*'		  => 10,
			'+'		  => 11,
			','		  => 12,
			'-'		  => 13,
			'.'		  => 14,
			'/'		  => 15,
			'0'		  => 16,
			'1'		  => 17,
			'2'		  => 18,
			'3'		  => 19,
			'4'		  => 20,
			'5'		  => 21,
			'6'		  => 22,
			'7'		  => 23,
			'8'		  => 24,
			'9'		  => 25,
			':'		  => 26,
			';'		  => 27,
			'<'		  => 28,
			'='		  => 29,
			'>'		  => 30,
			'?'		  => 31,
			'@'		  => 32,
			'A'		  => 33,
			'B'		  => 34,
			'C'		  => 35,
			'D'		  => 36,
			'E'		  => 37,
			'F'		  => 38,
			'G'		  => 39,
			'H'		  => 40,
			'I'		  => 41,
			'J'		  => 42,
			'K'		  => 43,
			'L'		  => 44,
			'M'		  => 45,
			'N'		  => 46,
			'O'		  => 47,
			'P'		  => 48,
			'Q'		  => 49,
			'R'		  => 50,
			'S'		  => 51,
			'T'		  => 52,
			'U'		  => 53,
			'V'		  => 54,
			'W'		  => 55,
			'X'		  => 56,
			'Y'		  => 57,
			'Z'		  => 58,
			'['		  => 59,
			'\\'	  => 60,
			']'		  => 61,
			'^'		  => 62,
			'_'		  => 63,
			'`'		  => 64,
			'a'		  => 65,
			'b'		  => 66,
			'c'		  => 67,
			'd'		  => 68,
			'e'		  => 69,
			'f'		  => 70,
			'g'		  => 71,
			'h'		  => 72,
			'i'		  => 73,
			'j'		  => 74,
			'k'		  => 75,
			'l'		  => 76,
			'm'		  => 77,
			'n'		  => 78,
			'o'		  => 79,
			'p'		  => 80,
			'q'		  => 81,
			'r'		  => 82,
			's'		  => 83,
			't'		  => 84,
			'u'		  => 85,
			'v'		  => 86,
			'w'		  => 87,
			'x'		  => 88,
			'y'		  => 89,
			'z'		  => 90,
			'{'		  => 91,
			'|'		  => 92,
			'}'		  => 93,
			'~'		  => 94,
			'Start A' => 103,
			'Start B' => 104,
			'Start C' => 105,
			'Stop'	  => 106
		);

		$this->_pattern_table = array(
			'2 1 2 2 2 2',
			'2 2 2 1 2 2',
			'2 2 2 2 2 1',
			'1 2 1 2 2 3',
			'1 2 1 3 2 2',
			'1 3 1 2 2 2',
			'1 2 2 2 1 3',
			'1 2 2 3 1 2',
			'1 3 2 2 1 2',
			'2 2 1 2 1 3',
			'2 2 1 3 1 2',
			'2 3 1 2 1 2',
			'1 1 2 2 3 2',
			'1 2 2 1 3 2',
			'1 2 2 2 3 1',
			'1 1 3 2 2 2',
			'1 2 3 1 2 2',
			'1 2 3 2 2 1',
			'2 2 3 2 1 1',
			'2 2 1 1 3 2',
			'2 2 1 2 3 1',
			'2 1 3 2 1 2',
			'2 2 3 1 1 2',
			'3 1 2 1 3 1',
			'3 1 1 2 2 2',
			'3 2 1 1 2 2',
			'3 2 1 2 2 1',
			'3 1 2 2 1 2',
			'3 2 2 1 1 2',
			'3 2 2 2 1 1',
			'2 1 2 1 2 3',
			'2 1 2 3 2 1',
			'2 3 2 1 2 1',
			'1 1 1 3 2 3',
			'1 3 1 1 2 3',
			'1 3 1 3 2 1',
			'1 1 2 3 1 3',
			'1 3 2 1 1 3',
			'1 3 2 3 1 1',
			'2 1 1 3 1 3',
			'2 3 1 1 1 3',
			'2 3 1 3 1 1',
			'1 1 2 1 3 3',
			'1 1 2 3 3 1',
			'1 3 2 1 3 1',
			'1 1 3 1 2 3',
			'1 1 3 3 2 1',
			'1 3 3 1 2 1',
			'3 1 3 1 2 1',
			'2 1 1 3 3 1',
			'2 3 1 1 3 1',
			'2 1 3 1 1 3',
			'2 1 3 3 1 1',
			'2 1 3 1 3 1',
			'3 1 1 1 2 3',
			'3 1 1 3 2 1',
			'3 3 1 1 2 1',
			'3 1 2 1 1 3',
			'3 1 2 3 1 1',
			'3 3 2 1 1 1',
			'3 1 4 1 1 1',
			'2 2 1 4 1 1',
			'4 3 1 1 1 1',
			'1 1 1 2 2 4',
			'1 1 1 4 2 2',
			'1 2 1 1 2 4',
			'1 2 1 4 2 1',
			'1 4 1 1 2 2',
			'1 4 1 2 2 1',
			'1 1 2 2 1 4',
			'1 1 2 4 1 2',
			'1 2 2 1 1 4',
			'1 2 2 4 1 1',
			'1 4 2 1 1 2',
			'1 4 2 2 1 1',
			'2 4 1 2 1 1',
			'2 2 1 1 1 4',
			'4 1 3 1 1 1',
			'2 4 1 1 1 2',
			'1 3 4 1 1 1',
			'1 1 1 2 4 2',
			'1 2 1 1 4 2',
			'1 2 1 2 4 1',
			'1 1 4 2 1 2',
			'1 2 4 1 1 2',
			'1 2 4 2 1 1',
			'4 1 1 2 1 2',
			'4 2 1 1 1 2',
			'4 2 1 2 1 1',
			'2 1 2 1 4 1',
			'2 1 4 1 2 1',
			'4 1 2 1 2 1',
			'1 1 1 1 4 3',
			'1 1 1 3 4 1',
			'1 3 1 1 4 1',
			'1 1 4 1 1 3',
			'1 1 4 3 1 1',
			'4 1 1 1 1 3',
			'4 1 1 3 1 1',
			'1 1 3 1 4 1',
			'1 1 4 1 3 1',
			'3 1 1 1 4 1',
			'4 1 1 1 3 1',
			'2 1 1 4 1 2',
			'2 1 1 2 1 4',
			'2 1 1 2 3 2',
			'2 3 3 1 1 1 2'
		);
	}
	

	/**
	 * @access public
	 */	
	function get_width( $char_width )
	{
		return ceil( ( strlen( $this->data ) + 5 ) * $char_width );
	}
	
	/**
	 * @access public
	 */
	function get_pattern()
	{
		return $this->_pattern;
	}
	

	// private methods

	/**
	 * @access private
	 */		
	function _compute_checkdigit()
	{
		$codeset_table = $this->{'_' . strtolower( $this->_codeset ) . '_codeset_table'};
		$sum = $codeset_table['Start ' . $this->_codeset];
		
		for ( $i = 0; $i < strlen( $this->data ); $i++ )
			$sum += ( $i + 1 ) * $codeset_table[$this->data[$i]];
			
		return $sum % 103;
	}

	/**
	 * @access private
	 */
	function _compute_pattern()
	{
		for ( $i = 0; $i < count( $this->_pattern ); $i++ )
			array_shift( $this->_pattern );
			
		$codeset_table = $this->{'_' . strtolower( $this->_codeset ) . '_codeset_table'};
		$this->_pattern[] = $this->_pattern_table[$codeset_table['Start ' . $this->_codeset]];
		
		for ( $i = 0; $i < strlen( $this->data ); $i++ )
			$this->_pattern[] = $this->_pattern_table[$codeset_table[$this->data[$i]]];
			
		$this->_pattern[] = $this->_pattern_table[$this->_compute_checkdigit()];
		$this->_pattern[] = $this->_pattern_table[$codeset_table['Stop']];
	}
		
	/**
	 * @access private
	 */
	function _dump_pattern()
	{
		header( 'Content-Type: text/plain' );
		print_r( $this->_pattern );
	}
} // END OF Barcode128

?>
