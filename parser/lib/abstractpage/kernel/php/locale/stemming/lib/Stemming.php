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
|         Jon Abernathy <jon@chuggnutt.com>                            |
+----------------------------------------------------------------------+
*/


/**
 * @link http://www.datenbank-anthroposophie.de/hilfe.html
 * @link http://snowball.tartarus.org/
 * @link http://www.kso.co.uk/de/tutorial/5-9.html
 * @link http://www.gerhard.de/hilfe/suchen_soif/index_de.html
 * @link http://www-student.informatik.uni-bonn.de/~wichmann/writings/webcrawlers/Merkmale.html
 * @link http://snowball.tartarus.org/ 
 * @link http://snowball.tartarus.org/german/stemmer.html
 * @link http://snowball.tartarus.org/french/stemmer.html
 * @link http://snowball.tartarus.org/french/stop.txt
 * @see https://www.zend.com/lists/php-dev/200202/msg01243.html
 * @link http://www.comp.lancs.ac.uk/computing/research/stemming/
 * @package locale_stemming_lib
 */
 
class Stemming extends PEAR
{
	/**
	 * array of options
	 * @access private
	 */
	var $_options = array();
	
	
	/**
	 * Constructor
	 */
	function Stemming( $options = array() )
	{
		$this->_options = $options;
	}
	
	
    /**
     * Attempts to return a concrete Stemming instance based on $driver.
     *
     * @param mixed $driver  The type of concrete Stemming subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included.
     * @param array $options (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object Stemming  The newly created concrete Stemming instance,
     *                       or false on an error.
     */
    function &factory( $driver, $options = array() )
    {	
        $driver = strtolower( $driver );
		
        if ( empty( $driver ) || ( strcmp( $driver, 'none' ) == 0 ) )
            return new Stemming( $options );
	
        $stemming_class = "Stemming_" . $driver;

		using( 'locale.stemming.lib.' . $stemming_class );
		
		if ( class_registered( $stemming_class ) )
	        return new $stemming_class( $options );
		else
			return PEAR::raiseError( 'Driver not supported.' );
    }

    /**
     * Attempts to return a reference to a concrete Stemming instance
     * based on $driver. It will only create a new instance if no
     * Stemming instance with the same parameters currently exists.
     *
     * This should be used if multiple types of file backends (and,
     * thus, multiple Stemming instances) are required.
     *
     * This method must be invoked as: $var = &Stemming::singleton()
     *
     * @param mixed $driver  The type of concrete Stemming subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included.
     * @param array $options (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object Stemming  The concrete Stemming reference, or false on an
     *                       error.
     */
    function &singleton( $driver, $options = array() )
    {
        static $instances;
        
		if ( !isset( $instances ) )
            $instances = array();

        if ( is_array( $driver ) )
            $drivertag = implode( ':', $driver );
        else
            $drivertag = $driver;
        
        $signature = md5( strtolower( $drivertag ) . '][' . implode('][', $options ) );
        
		if ( !isset( $instances[$signature] ) )
            $instances[$signature] = &Stemming::factory( $driver, $options );

        return $instances[$signature];
    }
	
    /**
     *  Takes a list of words and returns them reduced to their stems.
     *
     *  $words can be either a string or an array. If it is a string, it will
     *  be split into separate words on whitespace, commas, or semicolons. If
     *  an array, it assumes one word per element.
     *
     *  @param mixed $words String or array of word(s) to reduce
     *  @access public
     *  @return array List of word stems
     */
    function stemList( $words )
    {
        if ( empty( $words ) )
            return false;

        $results = array();

        if ( !is_array( $words ) )
            $words = split( "[ ,;\n\r\t]+", trim( $words ) );

        foreach ( $words as $word )
		{
            if ( $result = $this->stem( $word ) )
                $results[] = $result;
        }

        return $results;
    }
	
	/**
	 * Stem complete text.
	 *
	 * @param  string  Text
	 * @access public
	 * @return string
	 */
	function stemText( $text = "" )
	{
		$arr  = explode( " ", $text );
		$list = $this->stemList( $arr );
		
		return join( " ", $list );
	}
	
	/**
	 * @abstract
	 */
	function stem( $word = "" )
	{
		return $word;
	}
	
	
	// private methods
	
    /**
     *  Checks that the specified letter (position) in the word is a consonant.
     *
     *  Handy check adapted from the ANSI C program. Regular vowels always return
     *  FALSE, while "y" is a special case: if the prececing character is a vowel,
     *  "y" is a consonant, otherwise it's a vowel.
     *
     *  @param  string $word Word to check
     *  @param  integer $pos Position in the string to check
     *  @access private
     *  @return boolean
     */
    function _isConsonant( $word, $pos )
    {
        $char = substr( $word, $pos, 1 );
		
        switch ( $char ) 
		{
            case 'a':
            
			case 'e':
            
			case 'i':
            
			case 'o':
            
			case 'u':
                return false;
            
			case 'y':
                if ( $pos == 0 || strlen( $word ) == -$pos ) 
				{
					// check second letter of word
                    return !( $this->_isConsonant( $word, 1 ) );
                } 
				else 
				{
                    return !( $this->_isConsonant( $word, $pos - 1 ) );
                }
				
            default:
                return true;
        }
    }

    /**
     *  Counts (measures) the number of vowel-consonant occurences.
     *
     *  Based on the algorithm; this handy function counts the number of
     *  occurences of vowels (1 or more) followed by consonants (1 or more),
     *  ignoring any beginning consonants or trailing vowels. A legitimate
     *  VC combination counts as 1 (ie. VCVC = 2, VCVCVC = 3, etc.).
     *
     *  @param  string $word Word to measure
     *  @access private
     *  @return integer
     */
    function _countVC( $word )
    {
        $m      = 0;
        $length = strlen( $word );
        $prev_c = false;
		
        for ( $i = 0; $i < $length; $i++ ) 
		{
            $is_c = $this->_isConsonant( $word, $i );
			
            if ( $is_c ) 
			{
                if ( $m > 0 && !$prev_c )
                    $m += 0.5;
            } 
			else 
			{
                if ( $prev_c || $m == 0 )
                    $m += 0.5;
            }
			
            $prev_c = $is_c;
        }
		
        $m = floor( $m );
        return $m;
    }

    /**
     *  Checks for a specific consonant-vowel-consonant condition.
     *
     *  This function is named directly from the original algorithm. It
     *  looks the last three characters of the word ending as
     *  consonant-vowel-consonant, with the final consonant NOT being one
     *  of "w", "x" or "y".
     *
     *  @param  string $word Word to check
     *  @access private
     *  @return boolean
     */
    function _o( $word )
    {
        $last_char = substr( $word, -1 );
		
        if ( $last_char == 'w' || $last_char == 'x' || $last_char == 'y' )
            return false;
        
        if ( $this->_isConsonant( $word, -1 ) && !$this->_isConsonant( $word, -2 ) && $this->_isConsonant( $word, -3 ) )
            return true;
        
        return false;
    }

    /**
     *  Replaces suffix, if found and word measure is a minimum count.
     *
     *  @param string $word Word to check and modify
     *  @param string $suffix Suffix to look for
     *  @param string $replace Suffix replacement
     *  @param integer $m Word measure value that the word must be greater
     *                    than to replace
     *  @access private
     *  @return boolean
     */
    function _replace( &$word, $suffix, $replace, $m = 0 )
    {
        $sl = strlen( $suffix );
		
        if ( substr( $word, -$sl ) == $suffix ) 
		{
            $short = substr_replace( $word, '', -$sl );
			
            if ( $this->_countVC( $short ) > $m )
                $word = $short . $replace;
            
            // Found this suffix, doesn't matter if replacement succeeded
            return true;
        }
		
        return false;
    }
} // END OF Stemming

?>
