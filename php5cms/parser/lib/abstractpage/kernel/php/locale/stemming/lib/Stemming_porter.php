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


using( 'locale.stemming.lib.Stemming' );


/**
 * This is the infamous "Porter Stemming Algorithm" - lots
 * of search engines use something similar to relate words. An example
 * would be to turn "running" into "run" but "kissing" into "kiss".
 *
 * The "Porter Stemming Algorithm" can be found at the following URL:
 * http://open.muscat.com/developer/docs/porterstem.html
 *
 * @package locale_stemming_lib
 */
 
class Stemming_porter extends Stemming
{
	/**
	 * Constructor
	 *
	 * @param  array  $options  Array of options
	 * @access public
	 */
	function Stemming_porter( $options = array() )
	{
		$this->Stemming( $options );
	}

	
    /**
     *  Takes a word and returns it reduced to its stem.
     *
     *  Non-alphanumerics and hyphens are removed, and if the word is less than
     *  three characters in length, it will be stemmed according to the five-step
     *  Porter stemming algorithm.
     *
     *  Note special cases here: hyphenated words (such as half-life) will only
     *  have the base after the last hyphen stemmed (so half-life would only have
     *  "life" subject to stemming).
     *
     *  @param  string $word Word to reduce
     *  @access public
     *  @return string Stemmed word
     */
    function stem( $word = "" )
    {
        if ( empty( $word ) ) 
            return $word;

        $result = '';
        $word = strtolower( $word );

        // Strip punctuation, etc.
        if ( substr( $word, -2 ) == "'s" )
            $word = substr( $word, 0, -2 );
        
        $word  = preg_replace( '/[^a-z0-9-]/', '', $word );
        $first = '';
		
        if ( strpos( $word, '-' ) !== false ) 
		{
            list( $first, $word ) = explode( '-', $word );
            $first .= '-';
        }
		
        if ( strlen( $word ) > 3 ) 
		{
            $word = $this->_step1( $word );
            $word = $this->_step2( $word );
            $word = $this->_step3( $word );
            $word = $this->_step4( $word );
            $word = $this->_step5( $word );
        }

        $result = $first . $word;
        return $result;
    }

	
	// private methods
	
    /**
     *  Performs the functions of steps 1a and 1b of the Porter Stemming Algorithm.
     *
     *  First, if the word is in plural form, it is reduced to singular form.
     *  Then, any -ed or -ing endings are removed as appropriate, and finally,
     *  words ending in "y" with a vowel in the stem have the "y" changed to "i".
     *
     *  @param  string $word Word to reduce
     *  @access private
     *  @return string Reduced word
     */
    function _step1( $word )
    {
        if ( substr( $word, -1 ) == 's' ) 
		{
            if ( substr( $word, -4 ) == 'sses' )
			{
                $word = substr( $word, 0, -2 );
            } 
			else if ( substr( $word, -3 ) == 'ies' ) 
			{
                $word = substr( $word, 0, -2 );
            } 
			else if ( substr( $word, -2, 1 ) != 's' ) 
			{
                // If second-to-last character is not "s"
                $word = substr( $word, 0, -1 );
            }
        }
		
        if ( substr( $word, -3 ) == 'eed' && $this->_countVC( substr( $word, -3 ) ) > 0 ) 
		{
            // Convert '-eed' to '-ee'
            $word = substr( $word, 0, -1 );
        } 
		else 
		{
			// vowel in stem
            if ( preg_match( '/[aeiou][^aeiou]+(ed|ing)$/', $word) ) 
			{ 
                // Strip '-ed' or '-ing'
                if ( substr( $word, -2 ) == 'ed' )
                    $word = substr( $word, 0, -2 );
                else
                    $word = substr( $word, 0, -3 );
                
                if ( substr( $word, -2 ) == 'at' || substr( $word, -2 ) == 'bl' || substr( $word, -2 ) == 'iz' ) 
				{
                    $word .= 'e';
                } 
				else 
				{
                    $last_char    = substr( $word, -1, 1 );
                    $next_to_last = substr( $word, -2, 1 );
					
                    // Strip ending double consonants to single, unless "l", "s" or "z"
                    if ( $this->_isConsonant( $word, -1 ) && $last_char == $next_to_last && $last_char != 'l' && $last_char != 's' && $last_char != 'z' )
					{
                        $word = substr( $word, 0, -1 );
                    } 
					else 
					{
                        // If VC, and cvc (but not w,x,y)
                        if ( $this->_countVC( $word ) == 1 && $this->_o( $word ) )
                            $word .= 'e';
                    }
                }
            }
        }
		
        // Turn y into i when another vowel in stem
		
		// vowel in stem
        if ( preg_match( '/[aeiou][^aeiou]+y$/', $word ) )
            $word = substr( $word, 0, -1 ) . 'i';
        
        return $word;
    }

    /**
     *  Performs the function of step 2 of the Porter Stemming Algorithm.
     *
     *  Step 2 maps double suffixes to single ones when the second-to-last character
     *  matches the given letters. So "-ization" (which is "-ize" plus "-ation"
     *  becomes "-ize". Mapping to a single character occurence speeds up the script
     *  by reducing the number of possible string searches.
     *
     *  Note: for this step (and steps 3 and 4), the algorithm requires that if
     *  a suffix match is found (checks longest first), then the step ends, regardless
     *  if a replacement occurred. Some (or many) implementations simply keep
     *  searching though a list of suffixes, even if one is found.
     *
     *  @param  string $word Word to reduce
     *  @access private
     *  @return string Reduced word
     */
    function _step2( $word )
    {
        switch ( substr( $word, -2, 1 ) )
		{
            case 'a':
                if ( $this->_replace( $word, 'ational', 'ate', 0 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'tional', 'tion', 0 ) )
                    return $word;
                
                break;
            
			case 'c':
                if ( $this->_replace( $word, 'enci', 'ence', 0 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'anci', 'ance', 0 ) )
                    return $word;
                
                break;
            
			case 'e':
                if ( $this->_replace( $word, 'izer', 'ize', 0 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'iser', 'ize', 0 ) )
                    return $word;
                
                break;
            
			case 'l':
                if ( $this->_replace( $word, 'bli', 'ble', 0 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'alli', 'al', 0 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'entli', 'ent', 0 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'eli', 'e', 0 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'ousli', 'ous', 0 ) )
                    return $word;
                
                break;
            
			case 'o':
                if ( $this->_replace( $word, 'ization', 'ize', 0 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'isation', 'ize', 0 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'ation', 'ate', 0 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'ator', 'ate', 0 ) )
                    return $word;
                
                break;
            
			case 's':
                if ( $this->_replace( $word, 'alism', 'al', 0 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'iveness', 'ive', 0 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'fulness', 'ful', 0 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'ousness', 'ous', 0 ) )
                    return $word;
                
                break;
            
			case 't':
                if ( $this->_replace( $word, 'aliti', 'al', 0 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'iviti', 'ive', 0 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'biliti', 'ble', 0 ) )
                    return $word;
                
                break;
            
			case 'g':
                if ( $this->_replace( $word, 'logi', 'log', 0 ) )
                    return $word;
                
                break;
        }
		
        return $word;
    }

    /**
     *  Performs the function of step 3 of the Porter Stemming Algorithm.
     *
     *  Step 3 works in a similar stragegy to step 2, though checking the
     *  last character.
     *
     *  @param  string $word Word to reduce
     *  @access private
     *  @return string Reduced word
     */
    function _step3( $word )
    {
        switch ( substr( $word, -1 ) ) 
		{
            case 'e':
                if ( $this->_replace( $word, 'icate', 'ic', 0 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'ative', '', 0 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'alize', 'al', 0 ) )
                    return $word;
                
                break;
            
			case 'i':
                if ( $this->_replace( $word, 'iciti', 'ic', 0 ) )
                    return $word;
                
                break;
            
			case 'l':
                if ( $this->_replace( $word, 'ical', 'ic', 0 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'ful', '', 0 ) )
                    return $word;
                
                break;
            
			case 's':
                if ( $this->_replace( $word, 'ness', '', 0 ) )
                    return $word;
                
                break;
        }
		
        return $word;
    }

    /**
     *  Performs the function of step 4 of the Porter Stemming Algorithm.
     *
     *  Step 4 works similarly to steps 3 and 2, above, though it removes
     *  the endings in the context of VCVC (vowel-consonant-vowel-consonant
     *  combinations).
     *
     *  @param  string $word Word to reduce
     *  @access private
     *  @return string Reduced word
     */
    function _step4( $word )
    {
        switch ( substr( $word, -2, 1 ) ) 
		{
            case 'a':
                if ( $this->_replace( $word, 'al', '', 1 ) )
                    return $word;
                
                break;
            
			case 'c':
                if ( $this->_replace( $word, 'ance', '', 1 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'ence', '', 1 ) )
                    return $word;
                
                break;

            case 'e':
                if ( $this->_replace( $word, 'er', '', 1 ) )
                    return $word;
				
                break;
            
			case 'i':
                if ( $this->_replace( $word, 'ic', '', 1 ) )
                    return $word;
                
                break;
				
            case 'l':
                if ( $this->_replace( $word, 'able', '', 1 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'ible', '', 1 ) )
                    return $word;
                
                break;
				
            case 'n':
                if ( $this->_replace( $word, 'ant', '', 1 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'ement', '', 1 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'ment', '', 1 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'ent', '', 1 ) )
                    return $word;
                
                break;
				
            case 'o':
                // special cases
                if ( substr( $word, -4 ) == 'sion' || substr( $word, -4 ) == 'tion' )
				{
                    if ( $this->_replace( $word, 'ion', '', 1 ) )
                        return $word;
                }
				
                if ( $this->_replace( $word, 'ou', '', 1 ) )
                    return $word;
                
                break;
				
            case 's':
                if ( $this->_replace( $word, 'ism', '', 1 ) )
                    return $word;
                
                break;
            
			case 't':
                if ( $this->_replace( $word, 'ate', '', 1 ) )
                    return $word;
                
                if ( $this->_replace( $word, 'iti', '', 1 ) )
                    return $word;
                
                break;
				
            case 'u':
                if ( $this->_replace( $word, 'ous', '', 1 ) )
                    return $word;
                
                break;
				
            case 'v':
                if ( $this->_replace( $word, 'ive', '', 1 ) )
                    return $word;
                
                break;
				
            case 'z':
                if ( $this->_replace( $word, 'ize', '', 1 ) )
                    return $word;
                
                break;
        }
		
        return $word;
    }

    /**
     *  Performs the function of step 5 of the Porter Stemming Algorithm.
     *
     *  Step 5 removes a final "-e" and changes "-ll" to "-l" in the context
     *  of VCVC (vowel-consonant-vowel-consonant combinations).
     *
     *  @param  string $word Word to reduce
     *  @access private
     *  @return string Reduced word
     */
    function _step5( $word )
    {
        if ( substr( $word, -1 ) == 'e' ) 
		{
            $short = substr( $word, 0, -1 );
			
            // Only remove in vcvc context...
            if ( $this->_countVC( $short ) > 1 )
                $word = $short;
			else if ( $this->_countVC( $short ) == 1 && !$this->_o( $short ) ) 
                $word = $short;
        }
		
        if ( substr( $word, -2 ) == 'll' ) 
		{
            // Only remove in vcvc context...
            if ( $this->_countVC( $word ) > 1 )
                $word = substr( $word, 0, -1 );
        }
		
        return $word;
    }
} // END OF Stemming_porter

?>
