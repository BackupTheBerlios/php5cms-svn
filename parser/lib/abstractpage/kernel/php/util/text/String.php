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


define( 'CR',   "\r"   );
define( 'LF',   "\n"   );
define( 'CRLF', "\r\n" );


/**
 * Represents a string. 
 *
 * This class is useful in two situations:
 * <ul>
 *  <li>You have very large strings. The overhead is thus not 
 *      noticeable and as objects are passed by reference instead
 *      of by value, it will actually save memory!
 *  </li>
 *  <li>You want an object-oriented API</li>
 * </ul>
 *
 * @package util_text
 */

class String extends PEAR
{
	/**
	 * @access public
	 */
    var $buffer = '';

	
    /**
     * Constructor
     *
     * @access  public
     * @param   string initial default ''
     */
    function String( $initial = '' ) 
	{
      	$this->buffer = $initial;
    }
    
	
    /**
     * Retrieve string's length.
     *
     * @access  public
     * @return  int
     */
    function length()
	{
      	return strlen( $this->buffer );
    }

    /**
     * Set Buffer.
     *
     * @access  public
     * @param   string buffer
     */
    function setBuffer( $buffer ) 
	{
      	$this->buffer = $buffer;
    }

    /**
     * Get Buffer.
     *
     * @access  public
     * @return  string
     */
    function getBuffer()
	{
      	return $this->buffer;
    }
    
    /**
     * Returns the character at the specified index. Index counting starts
     * at 0 and ends at length() - 1. Use -1 as value for the pos argument
     * to retrieve the last character in this string.
     *
     * @access  public
     * @param   int pos
     * @return  string character
     */
    function charAt( $pos ) 
	{
      	if ( -1 == $pos )
        	$pos =  strlen( $this->buffer ) - 1;
		else if ( $pos < 0 || $pos >= strlen( $this->buffer ) ) 
			return PEAR::raiseError( $pos . " is not a valid string offset." );

      	return $this->buffer{$pos};
    }
    
	/**
	 * Returns the position of the last occurance of $char.  If it does not
	 * exist, the function returns false.
	 *
	 * @access  public
	 * @param   $char string. A single character to search for
	 * @return int
	 */
	function lastCharAt( $char ) 
	{
		$position = strrpos( $this->buffer, $char );
		
		if ( $position === false )
			return false;

		return $position;
	}
	
    /**
     * Compares two strings lexicographically.
     *
     * @access  public
     * @param   &util.text.String string
     * @param   bool cs default true whether to compare case-sensitively
     * @return  int
     * @see     php://strcmp for case-sensitive comparison
     * @see     php://strcasecmp for case-insensitive comparison
     */
    function compareTo( &$string, $cs = true ) 
	{
      	return ( $cs? strcmp( $string->buffer, $this->buffer ) : strcasecmp( $string->buffer, $this->buffer ) );
    }
 
    /**
     * Compares two strings lexicographically using a "natural order" 
     * algorithm.
     *
     * @access  public
     * @param   &util.text.String string
     * @param   bool cs default true whether to compare case-sensitively
     * @return  int
     * @see     php://strnatcmp for case-sensitive comparison
     * @see     php://strnatcasecmp for case-insensitive comparison
     */
    function compareToNat( &$string, $cs = true )
	{
      	return ( $cs? strnatcmp( $string->buffer, $this->buffer ) : strnatcasecmp( $string->buffer, $this->buffer ) );
    }
   
    /**
     * Tests if this string starts with the specified prefix beginning 
     * a specified index.
     *
     * @access  public
     * @param   string prefix
     * @param   int offset default 0 where to begin looking in the string
     * @return  bool
     */
    function startsWith( $prefix, $offset = 0 ) 
	{
      	return substr( $this->buffer, $offset, strlen( $prefix ) ) == $prefix;
    }
    
    /**
     * Tests if this string ends with the specified suffix.
     *
     * @access  public
     * @param   string suffix
     * @return  bool
     */
    function endsWith( $suffix ) 
	{
      	return substr( $this->buffer, -1 * strlen( $suffix ) ) == $suffix;
    }
    
    /**
     * Returns the index within this string of the first occurrence of the 
     * specified substring.
     *
     * @access  public
     * @param   string substr
     * @param   int offset default 0 the index to start the search from
     * @return  int the index of the first occurrence of the substring or false
     * @see     php://strpos
     */
    function indexOf( $substr, $offset= 0 ) 
	{
      	return strpos( $this->buffer, $substr, $offset );
    }
    
    /**
     * Returns the index within this string of the last occurrence of the 
     * specified substring.
     *
     * @access  public
     * @param   string substr
     * @return  int the index of the first occurrence of the substring or false
     * @see     php://strrpos
     */
    function lastIndexOf( $substr ) 
	{
      	return strrpos( $this->buffer, $substr );
    }
    
    /**
     * Returns whether the specified substring is contained in this string.
     *
     * @access  public
     * @param   string substr
     * @param   bool cs default true whether to check case-sensitively
     * @return  bool
     */
    function contains( $substr, $cs = true ) 
	{
      	return ( $cs? false !== strpos( $this->buffer, $substr ) : false !== strpos( strtolower( $this->buffer ), strtolower( $substr ) ) );
    }
    
    /**
     * Find first occurrence of a string. Returns part of haystack string 
     * from the first occurrence of needle to the end of haystack. 
     *
     * Example:
     * <code>
     *   $s = &new String( 'foo@bar.de' );
     *   if ( $portion = $s->substrAfter( '@' ) ) {
     *     echo $portion; // php3.de
     *   }
     * </code>
     *
     * @access  public
     * @param   string substr
     * @param   bool cs default true whether to check case-sensitively
     * @return  string or false if substr is not found
     * @see     php://strstr
     */
    function substrAfter( $substr, $cs = true ) 
	{
      	return ( $cs? strstr( $this->buffer, $substr ) : stristr( $this->buffer, $substr ) );
    }

    /**
     * Find first occurrence of a string. Returns part of haystack string 
     * from the first occurrence of needle to the end of haystack. 
     *
     * @access  public
     * @param   string substr
     * @param   bool cs default true whether to check case-sensitively
     * @return  &util.text.String or null if substr is not found
     * @see     php://strstr
     */
    function &substringAfter( $substr, $cs = true ) 
	{
      	if ( ( $s = ( $cs? strstr( $this->buffer, $substr ) : stristr( $this->buffer, $substr ) ) ) === false ) 
			return null;

      	return new String( $s );
    }
    
    /**
     * Returns a new String object that holds substring of this string.
     *
     * @access  public
     * @param   int begin
     * @param   int end default -1
     * @return  &util.text.String
     * @see     php://substr
     */
    function &substring( $begin, $end = -1 ) 
	{
      	return new String( substr( $this->buffer, $begin, $end ) );
    }

    /**
     * Returns a new string that is a substring of this string.
     *
     * @access  public
     * @param   int begin
     * @param   int end default -1
     * @return  string
     * @see     php://substr
     */
    function substr( $begin, $end = -1 ) 
	{
      	return substr( $this->buffer, $begin, $end );
    }
    
    /**
     * Concatenates the specified string to the end of this string
     * and returns a new string containing the result.
     *
     * @access  public
     * @param   &util.text.String
     * @return  &util.text.String a new string
     */
    function &concat( &$string ) 
	{
      	return new String( $this->buffer . $string->buffer );
    }
    
    /**
     * Concatenates the specified string to the end of this string,
     * changing this string.
     *
     * @access  public
     * @param   &util.text.String
     */
    function append( &$string ) 
	{
      	$this->buffer .= $string->buffer;
    }
    
    /**
     * Replaces search value(s) with replacement value(s) in this string.
     *
     * @access  public
     * @param   mixed search
     * @param   mixed replace
     * @see     php://str_replace
     */
    function replace( $search, $replace ) 
	{
      	$this->buffer = str_replace( $search, $replace, $this->buffer );
    }
    
    /**
     * Replaces pairs in this this string.
     *
     * @access  public
     * @param   array pairs an associative array, where keys are replaced by values
     * @see     php://strtr
     */
    function replacePairs( $pairs ) 
	{
      	$this->buffer = strtr( $search, $pairs );
    }
    
    /**
     * Delete a specified amount of characters from this string as
     * of a specified position.
     *
     * @access  public
     * @param   int pos
     * @param   int len default 1
     */
    function delete( $pos, $len = 1 ) 
	{
      	$this->buffer = substr( $this->buffer, 0, $pos ) . substr( $this->buffer, $pos + 1 );
    }
	
	/**
	 * Removes the any number of characters from the end of the string.
	 *
	 * @access  public
	 * @param   $length int.  The length in characters to chop of the string.
	 * @return void
	 */
	function deleteFromEnd( $length ) 
	{
		$this->buffer = substr( $this->buffer, 0, $this->length() - $length );
	}
    
    /**
     * Insert a substring into this string at a specified position. 
     *
     * @access  public
     * @param   înt pos
     * @param   string substring
     */
    function insert( $pos, $substring ) 
	{
      	$this->buffer = substr( $this->buffer, 0, $pos ) . $substring . substr( $this->buffer, $pos );
    }
    
    /**
     * Tells whether or not this string matches the given regular expression.
     *
     * @access  public
     * @param   string regex
     * @return  bool
     * @see     php://preg_match
     */
    function matches( $regex ) 
	{
      	return preg_match( $regex, $this->buffer );
    }
    
	/**
	 * Splits the string into equal size chunks given by $value. The last 
	 * element in the array will be what is left over.
	 *
	 * @access  public
	 * @param   $value int 
	 * @return void
	 */
	function &split( $value ) 
	{
		$textToSplit = $this->buffer;
		$stringParts = array();
		
		while ( strlen( $textToSplit ) > $value ) 
		{
			$stringParts[] = substr( $textToSplit, 0, $value );
			$textToSplit   = substr( $textToSplit, $value, strlen( $textToSplit ) );
		}

		$stringParts[] = $textToSplit;
		return $stringParts;
	}
	
    /**
     * Split this string into portions delimited by separator.
     *
     * @access  public
     * @param   string separator
     * @param   int limit default 0
     * @return  &util.text.String[]
     * @see     php://explode
     */
    function explode( $separator, $limit = 0 ) 
	{
      	for ( $a = ( $limit? explode( $separator, $this->buffer ) : explode( $separator, $this->buffer, $limit ) ), $s = sizeof( $a ), $i = 0; $i < $s; $i++ )
        	$a[$i] = &new String( $a[$i] );
      
      	return $a;
    }

    /**
     * Split this string into portions delimited by separator regex.
     *
     * @access  public
     * @param   string separator
     * @param   int limit default 0
     * @return  &util.text.String[]
     * @see     php://preg_split
     */
    function split( $separator, $limit = 0 ) 
	{
      	for ( $a = ( $limit? preg_split( $separator, $this->buffer ) : preg_split( $separator, $this->buffer, $limit ) ), $s = sizeof( $a ), $i = 0; $i < $s; $i++ )
        	$a[$i] = &new String( $a[$i] );
      
      	return $a;
    }
    
    /**
     * Pad this string to a certain length with another string.
     *
     * @access  public
     * @param   int length
     * @param   string str default ' '
     * @param   int type default STR_PAD_RIGHT
     * @see     php://str_pad
     */
    function pad( $length, $str = ' ', $type = STR_PAD_RIGHT ) 
	{
      	$this->buffer = str_pad( $this->buffer, $length, $str, $type );
    }
    
    /**
     * Strip whitespace from the beginning and end of this string.
     *
     * If the parameter charlist is omitted, these characters will
     * be stripped:
     * <ul>
     *   <li>" " (ASCII 32 (0x20)), an ordinary space.</li>
     *   <li>"\t" (ASCII 9 (0x09)), a tab.</li>
     *   <li>"\n" (ASCII 10 (0x0A)), a new line (line feed).</li>
     *   <li>"\r" (ASCII 13 (0x0D)), a carriage return.</li>
     *   <li>"\0" (ASCII 0 (0x00)), the NUL-byte.</li>
     *   <li>"\x0B" (ASCII 11 (0x0B)), a vertical tab. </li>
     * </ul>
     *
     * @access  public
     * @param   string charlist default null
     * @see     php://trim
     */
    function trim( $charlist = null ) 
	{
      	if ( $charlist )
        	$this->buffer = trim( $this->buffer, $charlist );
      	else
        	$this->buffer = trim( $this->buffer );
    }

    /**
     * Strip whitespace from the beginning of this string.
     *
     * @access  public
     * @param   string charlist default null
     * @see     php://ltrim
     */
    function ltrim( $charlist= null ) 
	{
      	if ( $charlist )
        	$this->buffer = ltrim( $this->buffer, $charlist );
      	else
        	$this->buffer = ltrim( $this->buffer );
    }

    /**
     * Strip whitespace from the end of this string.
     *
     * @access  public
     * @param   string charlist default null
     * @see     php://ltrim
     */
    function rtrim( $charlist= null ) 
	{
      	if ( $charlist )
        	$this->buffer = rtrim( $this->buffer, $charlist );
      	else
        	$this->buffer = rtrim( $this->buffer );
    }
    
    /**
     * Converts all of the characters in this string to upper case using 
     * the rules of the current locale.
     *
     * @access  public
     * @see     php://strtoupper
     * @return  &util.text.String this string
     */
    function &toUpperCase()
	{
      	$this->buffer = strtoupper( $this->buffer );
      	return $this;
    }

    /**
     * Converts all of the characters in this string to lower case using 
     * the rules of the current locale.
     *
     * @access  public
     * @see     php://strtolower
     * @return  &util.text.String this string
     */
    function &toLowerCase() 
	{
      	$this->buffer = strtolower( $this->buffer );
      	return $this;
    }
    
	/**
	 * Uppercases the first letter of this string.
	 *
	 * @access  public
	 * @return void
	 */
	function upperCaseFirst() 
	{
		$this->buffer = ucfirst( $this->buffer );
	}
	
    /**
     * Parses input from this string according to a format:
     *
     * @access  public
     * @param   string format
     * @return  array
     * @see     php://sscanf
     */
    function scan( $format ) 
	{
      	return sscanf( $this->buffer, $format );
    }
    
    /**
     * Returns an array of strings.
     *
     * Examples:
     * <code>
     *   $s= &new String('Hello');
     *   $a= $s->toArray();         // array('H', 'e', 'l', 'l', 'o')
     *
     *   $s= &new String( 'Foo,Bar' );
     *   $a= $s->toArray( ',' ); // array( 'Foo', 'Bar')
     * </code>
     *
     * @access  public
     * @param   string delim default ''
     * @return  string[]
     */
    function toArray( $delim = '' ) 
	{
      	if ( $delim ) 
			return explode( $delim, $this->buffer );
      
      	$a = array();
      
	  	for ( $i = 0, $s = strlen( $this->buffer ); $i < $s; $i++ )
        	$a[] = $this->buffer{$i};
      
      	return $a;
    }
    
    /**
     * Creates a new string from an array, imploding it using the 
     * specified delimiter.
     *
     * Examples:
     * <code>
     *   $s= &String::fromArray(array('a', 'b', 'c'));  // "abc"
     *   $s= &String::fromArray(array(1, 2, 3), ',');   // "1,2,3"
     * </code>
     *
     * @static
     * @access  public
     * @param   string delim default ''
     * @return  &util.text.String string
     */
    function &fromArray( $arr, $delim = '' ) 
	{
      	return new String( implode( $delim, $arr ) );
    }
    
	/**
	 * Adds slashes to all characters that can be escaped.
	 *
	 * @access  public
	 * @return void
	 */
	function addSlashes() 
	{
		$this->buffer = addslashes( $this->buffer );
	}

	/**
	 * Removes slashes on all escapable characters.
	 *
	 * @access  public
	 * @return void
	 */
	function removeSlashes() 
	{
		$this->buffer = stripslashes( $this->buffer );
	}
	
	/**
	 * Returns the hash index for a key and a capacity for a hash table.
	 *
	 * @access  public
	 * @param   $delimiter string. Restricts formatting to characters not
	 *					 found between the specified delimiter
	 * @static
	 * @return void
	 */
	function hashCode( $key, $capacity ) 
	{
		$hashValue = 0;
		$key = new String( $key );

		for ( $i = 0; $i < $key->length(); $i++ )
			$hashValue = 37 * $hashValue * $key->charAt( $i );

		$hashValue %= $capacity;
		
		if ( $hashValue < $capacity ) 
			$hashValue += $capacity;
	}
	
	/**
	 * Replaces every occurance of $stringFrom with $stringTo.
	 *
	 * @access  public
	 * @param $stringFrom string. The string to replace
	 * @param $stringTo string. The string it will be replaced by
	 * @return void
	 */
	function replaceAll( $stringFrom, $stringTo ) 
	{
		$this->buffer = ereg_replace( $stringFrom, $stringTo, $this->buffer );
	}

	/**
	 * Bolds every occurance of the given word.
	 *
	 * @access  public
	 * @param   $word string. The string to bold
	 * @return void
	 */
	function boldAll( $word ) 
	{
		$this->formatAll( $word, '<b>', '</b>' );
	}

	/**
	 * Italicizes every occurance of the given word.
	 *
	 * @access  public
	 * @param   $word string. The string to italizcize
	 * @return void
	 */
	function italicizeAll( $word ) 
	{
		$this->formatAll( $word, '<i>', '</i>' );
	}

	/**
	 * Modifies a word to have an html tag around it.
	 *
	 * @access  public
	 */
	function formatAll( $word, $startTag, $endTag ) 
	{
		$this->buffer = ereg_replace( $word, $startTag . $word . $endTag, $this->buffer );
	}
	
	/**
	 * Displays a teaser of the complete string and appends '...' to it. 
	 * The maximum amount of characters is defined as $numberOfChars. If this 
	 * string does not contain up to $numberOfChars, then the string is 
	 * displayed as normal.
	 *
	 * @access  public
	 * @param   $numberOfChars int. The maximum number of characters to display
	 * @return void
	 */
	function displayUpto( $numberOfChars ) 
	{
		assert( $numberOfChars );

		if ( $this->length() > $numberOfChars ) 
		{
			$string = $this->substr( 0, $numberOfChars );
			echo trim( $string->toString() ) . '...';
		} 
		else 
		{
			$this->display();
		}
	}
	
	/**
	 * Displays this string to the screen.
	 *
	 * @access  public
	 * @return void
	 */
	function display() 
	{
		echo $this->buffer;
	}
	
	/**
	 * Transforms special characters such a <, >, ', " and & to the their 
	 * special character html equivalents in a string of text. The function
	 * will also add breaks where newlines occur. The function will
	 * not apply these changes to the text if there are 2 special characters,
	 * given as $delimiter, that are reached anywhere within the 
	 * string.
	 *
	 * @access  public
	 * @param   $delimiter string. Restricts formatting to characters not
	 *				     found between the specified delimiter
	 * @return void
	 */
	function toHtml( $delimiter = '~' ) 
	{
		$parsedText    = explode( $delimiter, $this->buffer );
		$isNormalText  = true;
		$formattedText = '';
		
		foreach ( $parsedText as $parsedSection ) 
		{
			if ( $isNormalText )
				$formattedText .= nl2br( htmlspecialchars( $parsedSection ) );
			else
				$formattedText .= $parsedSection;

			$isNormalText = !$isNormalText;
		}

		return $formattedText;
	}
	
	/**
	 * Returns the string.
	 *
	 * @access  public
	 * @return string
	 */
	function toString() 
	{
		return $this->buffer;
	}
} // END OF String

?>
