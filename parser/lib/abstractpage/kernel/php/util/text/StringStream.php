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


define ( "STRINGSTREAM_S", "\n\t\v\r "  );
define ( "STRINGSTREAM_OPENBR",  "<([{" ); 
define ( "STRINGSTREAM_CLOSEBR", ">)]}" );

define ( "STRINGSTREAM_FETCH", 0 ); // returning value(s) is NOT removed from the $buffer
define ( "STRINGSTREAM_GET",   1 ); // returning value(s) IS removed from the $buffer

define ( "STRINGSTREAM_TRUSTED", true ); 


/**
 * @package util_text
 */
 
class StringStream extends PEAR
{
	/**
	 * string buffer; $buffer (below) refer to it
	 * @access public
	 */
	var $buffer = '';
	
	/**
	 * indicates whether the $buffer is normalised
	 * @access public
	 */
	var $normalised = false;
	
	/**
	 * indicates whether the the whitespaces are stripped around the characters product $delimited
	 * @access public
	 */
	var	$delimited = '';


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function StringStream( $str = '' )
	{
		if  ( !empty( $str ) )
			$this->put( $str );
	}

	
	/**
	 * Append new content (string $str) to the $buffer, indicators are cleared.
	 *
	 * @access public
	 */
	function put( $str )
	{
		if ( empty( $str ) )
			return false;
		
		$this->buffer     .= $str;
		$this->delimited   = '';
		$this->normalized  = false;
		
		return strlen( $str );
	}

	/**
	 * Clears the $buffer and additional indicators.
	 *
	 * @access public
	 */
	function done()
	{
		$this->buffer     = '';
		$this->normalised = false;
		$this->delimited  = '';
	}

	
	// BUFFER status checking methods
	
	/**
	 * Returns whether $internal count of character is in the $buffer or not.
	 *
	 * @access public
	 */
	function enough( $internal = 1 )
	{
		return ( strlen( $this->buffer ) >= $internal );
	}

	/**
	 * Returns true if $buffer is empty or no characters of $what is in $buffer.
	 *
	 * @access public
	 */
	function eos( $what, $whole = false )
	{
		if ( !empty( $what ) )
		{
			// find a word delimited by one of $dlmtrs
			$what = $this->_quotemeta( $what );
			
			if ( !$whole )
				$what = '[' . $what . ']';
				
			if ( !ereg( $what, $this->buffer ) )
				return true;
		}

		return ( empty( $this->buffer ) );
	}

	
	// DATA retrieving methods
	
	/**
	 * Returns with $count pieces of characters from $buffer,
	 * or false if there's not enough characters.
	 *
	 * @access public
	 */
	function getc( $count = 1, $mode = STRINGSTREAM_GET )
	{
		if ( !$this->enough( $count ) )
			return false;

		if ( STRINGSTREAM_FETCH == $mode )
			return substr( $this->buffer, 0, $count );

		list( $s1, $s2 ) = $this->_divide( $count );
		$this->buffer = $s2;
		
		return $s1;
	}

	/**
	 * Returns with a 'word' enclosed by one of the $dlmtrs
	 * or false if no word found, this may signify the $buffer is empty.
	 *
	 * @access public
	 */
	function getw( $dlmtrs = STRINGSTREAM_S, $mode = STRINGSTREAM_GET,  $skipquote = true )
	{
		if ( $this->eos( '' ) )
			return false; 

		// find a word delimited by one of $dlmtrs
		$dlmtrs = $this->_quotemeta( $dlmtrs );
		$regexp = sprintf( "([^%s]+)(.*)", $dlmtrs, $dlmtrs );

		if ( !ereg( $regexp, $this->buffer, $regs ) )
			return false;

		if ( STRINGSTREAM_GET == $mode )
			$this->buffer = $regs[2]; // content behind the word
		
		return $regs[1]; // word found
	}

	/**
	 * Returns with the largest subsequent 'block' from $buffer or false
	 * 'block' means charaters enclosed by a GIVEN pair of brackets, braces, 
	 * parentheses, etc:<> () [] {} 
	 *
	 * @access public
	 */
	function getp( $op = "(", $mode = STRINGSTREAM_GET, $skipquote = true )
	{
		if ( $this->eos( $op ) )
			return false; 

		$pos = strpos( STRINGSTREAM_OPENBR, $op ); 
		
		if ( $pos === false )
			return -1; // non-valid opening bracket
		
		$cl = substr( STRINGSTREAM_CLOSEBR, $pos, 1 ); // the closing bracket

		// position of the opening bracket
		$pos = strpos( $this->buffer, $op );
		
		if ( $pos === false )
			return false; // no opening tag found

		for ( $i = $pos + 1, $level = 1; $level && $this->enough( $i + 1 ); $i++ ) 
		{
			if ( $skipquote && ( "'" == $this->buffer[$i] || '"' == $this->buffer[$i] ) )
			{
				// skips standalone quote too, no quote-balance checking
				list( $d, $s) = $this->_divide( $i + 1 );
				$i += strpos( $s, $this->buffer[$i] );
				
				continue;
			}
			
			if ( $op == $this->buffer[$i] )
				$level++; // opening bracket
			else if ( $cl == $this->buffer[$i] )
				$level--; // closing bracket
		}
		
		// brackets balance OK
		if ( $level == 0 )
		{ 
			$skipped = ( $pos? $this->getc( $pos, $mode ): '' );
			
			// in STRINGSTREAM_GET $mode preceding string is popped above
			if ( STRINGSTREAM_GET == $mode )
				$i -= $pos; 
			
			$block = substr( $this->getc( $i, $mode ), 1, $i - 2 );
			return array( $block, $skipped );
		}
		else
		{
			return -2;
		}
	}

	/**
	 * Returns with the first parenthesed 'block' from $buffer if possible 
	 * otherwise false
	 * 'block' means charaters enclosed by ANY pair of brackets braces , etc
	 * (in default priority order) <> () [] {}, overridable by subset of STRINGSTREAM_OPENBR
	 *
	 * @access public
	 */
	function geta( $ops = "<([{", $mode = STRINGSTREAM_GET, $skipquote = true )
	{
		for ( $i = 0; $i < strlen( $ops ); $i++ )
		{
			$ret = $this->getp( $ops[$i], $mode, $skipquote );
			
			if ( is_array( $ret ) )
				return array_push( $ret, $ops[$i] );
				
			if ( $ret == -1 )
				return -1;
		}
	}

	/**
	 * @access public
	 */
	function getb( $op, $cl, $mode = STRINGSTREAM_GET )
	{
		// not valid $tags specified
		if ( empty( $op ) || empty( $cl ) )
			return -1;

		$regexp = sprintf( "(.*)%s(.*)%s(.*)", $this->_quotemeta( $op ), $this->_quotemeta( $cl ) );
		
		if ( ereg( $regexp, $this->buffer, $regs ) )
		{
			if ( STRINGSTREAM_GET == $mode )
				$this->buffer = $regs[3];

			return array( $regs[2], $regs[1] );
		}
		else
		{
			return false;
		}
	}

	/**
	 * Returns with the whole content of $buffer.
	 *
	 * @access public
	 */
	function get( $mode = STRINGSTREAM_GET )
	{
		if ( STRINGSTREAM_FETCH == $mode )
			return $this->buffer;

		$s = $this->buffer;
		$this->buffer = '';
		
		return $s;
	}


	// BUFFER manipulation methods
	
	/**
	 * Push $str at the beginnig of $buffer
	 * setting $trusted indicates the $str is in the same state as $buffer
	 *
	 * @access public
	 */
	function unget( $str, $trusted = false )
	{
		if ( !$trusted )
		{
			$this->delimited  = '';
			$this->normalized = false;
		}

		$this->buffer = $str . $this->buffer;
		return true;
	}

	/**
	 * Strips leading and trailing whitespace and 
	 * replaces sequences of whitespace characters by a single space in $buffer.
	 *
	 * @access public
	 */
	function normalize_space()
	{
		if ( !$this->normalised ) 
		{
			$this->buffer = trim( ereg_replace( sprintf( "[%s]+", STRINGSTREAM_S ), ' ',$this->buffer ) );
			$this->normalised = true;
		}

		return $this->buffer;
	}

	/**
	 * Removes whitespaces around delimiters in $buffer.
	 *
	 * @access public
	 */
	function strip_space( $dlmtrs = "|,()?*+" ) 
	{
		if ( $this->delimited != $dlmtrs ) 
		{
			$dlmtrs = $this->_quotemeta( $dlmtrs );
			$regexp = sprintf( "[%s]*([%s])[%s]*", STRINGSTREAM_S, $dlmtrs, STRINGSTREAM_S );

			$this->buffer = trim( ereg_replace( $regexp, "\\1", $this->buffer ) );
			$this->delimited = $dlmtrs;
		}

		return $this->buffer;
	}

	
	// private methods
	
	/**
	 * Divides into 2 parts of buffer at the position $pos.
	 *
	 * @access private
	 */
	function _divide( $pos )
	{
		return array( substr( $this->buffer, 0, $pos ), substr( $this->buffer, $pos ) );
	}

	/**
	 * Original quotemeta and add slash before '|', too.
	 *
	 * @access private
	 */
	function _quotemeta( $dlmtrs )
	{
		$dlmtrs = quotemeta( $dlmtrs );
		$dlmtrs = str_replace( '|', '\\|', $dlmtrs );
		
		return $dlmtrs;
	}
} // END OF StringStream

?>
