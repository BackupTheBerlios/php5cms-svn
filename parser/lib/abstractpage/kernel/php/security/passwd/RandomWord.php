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
 * Makes a random 'readable' word (currently english-only).
 * This is useful if you want to randomly generate passwords
 * that your user can read and remember (instead of giving
 * them a bunch of numbers).
 *
 * Usage:
 *
 * $word = new RandomWord();
 * $word->set( "shoutIt", 1 );
 * $word->buildWord( 7 );
 * $word->addNumbers( 3 );
 * print $word->word;
 *	
 * Examples:
 *
 * Huchafo, Pequizu, Zequiju, Chomoxy ...
 *
 * @package security_passwd
 */

class RandomWord extends PEAR
{	
	/**
	 * it takes a little longer
	 * @access public
	 */	
	var $makeMoreRandom = true;
	
	/**
	 * @access public
	 */	
	var $isLowerCase = true;
	
	/**
	 * first letter upper-case, the rest lower
	 * @access public
	 */	
	var $isUCFirst = true;
	
	/**
	 * @access public
	 */	
	var $isUpperCase = false;
	
	/**
	 * example: chivoy!
	 * @access public
	 */	
	var $shoutIt = false;
	
	/**
	 * @access private
	 */	
	var $vowels = array( "a", "e", "i", "o", "u", "y" );
	
	/**
	 * @access private
	 */	
	var $consonants = array( array() );
	
	/**
	 * @access private
	 */	
	var $word = "";
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function RandomWord()
	{
		$this->consonants[0] = array( "b", "c", "d", "f", "g", "h", "j", "k", "l", "m", "n", "p", "r", "s", "t", "v", "w", "z" );
		$this->consonants[1] = array( "ch", "qu", "th", "xy" );
	}

	
	/**
	 * @access public
	 */
	function set( $var, $value )
	{
		$this->$var = $value;
	}
	
	/**
	 * @access public
	 */
	function buildWord( $length = 10 )
	{
		$done = false;
		$cons_or_vowel = 1;
		
		// makes the word
		while ( !$done )
		{
			$this->_seed();
			
			// 1 adds a consonant
			if ( 1 == $cons_or_vowel )
			{
				$i = rand( 0, 1 );
				$add = $this->consonants[$i][array_rand( $this->consonants[$i] )];
				$cons_or_vowel = 2;
			}
			// 2 adds a vowell:
			else if ( 2 == $cons_or_vowel )
			{
				$add = $this->vowels[array_rand( $this->vowels )];
				$cons_or_vowel = 1;
			}
			
			$this->word .= $add;
			
			if ( strlen( $this->word ) >= $length )
				$done = true;
		}
		
		// truncate word to fit desired length 
		// (in case a double-consonant was added for the last char build)
		$this->word = substr( $this->word, 0, $length );

		// change case according to var prefs
		$this->_formatCase();
		
		// shout it
		if ( $this->shoutIt )
			$this->word .= "!";
		
		return $this->word;
	}
	
	/**
	 * @access public
	 */
	function addNumbers( $length = 4 )
	{
		for ( $i = 1; $i <= $length; $i++ )
		{
			$this->_seed();
			$this->word .= (string)rand( 0, 9 );
		}
		
		return $this->word;
	}

	
	// private methods

	/**
	 * @access private
	 */	
	function _formatCase()
	{
		if ( $this->isLowerCase )
			$this->word = strtolower( $this->word );
			
		if ( $this->isUCFirst)
			$this->word = ucfirst( strtolower( $this->word ) );
			
		if ( $this->isUpperCase )
			$this->word = strtoupper( $this->word );
		
		return $this->word;
	}
	
	/**
	 * @access private
	 */	
	function _seed()
	{
		if ( $this->makeMoreRandom )
			usleep( 1 );
			
		srand( (double)microtime() * 1000000 );
	}	
} // END OF RandomWord

?>
