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


using( 'util.array.ArrayUtil' );
using( 'util.Util' );


define( 'STRINGUTIL_HTML_PASSTHRU',       0 );
define( 'STRINGUTIL_HTML_SYNTAX',         1 );
define( 'STRINGUTIL_HTML_REDUCED',        2 );
define( 'STRINGUTIL_HTML_MICRO',          3 );
define( 'STRINGUTIL_HTML_NOHTML',         4 );
define( 'STRINGUTIL_HTML_NOHTML_NOBREAK', 5 );


$GLOBALS['AP_STRINGUTIL_CHARSET'] = 'iso-8859-1';


/**
 * Static helper functions.
 *
 * @package util_text
 */
 
class StringUtil
{
    /**
     * Sets a default charset that the String methods will use if none is
     * explicitely specified.
     *
     * @param  string $charset  The charset to use as the default one.
	 * @access public
	 * @static
     */
    function setDefaultCharset( $charset )
    {
        $GLOBALS['AP_STRINGUTIL_CHARSET'] = $charset;
    }

	/**
 	 * This function checks to see if the passed string is of the passed type and if
 	 * it is not, an error is set.
 	 *
 	 * The types that can be passed in are:
 	 * - integer (integer number)
 	 * - double (double number)
 	 * - string (character string)
 	 * - email (email address in user@server.tld format)
 	 * - numbers (integers)
 	 * - numeric (numbers in any format such as integers or decimals)
 	 * - loosenumbers (numbers within other types of specific characters)
 	 * - alpha (alphanumeric characters)
 	 * - phone_us (US phone number following XXX-XXX-XXXX standard)
 	 * - phone_int (international phone number)
 	 * - slash_date (date in mm-dd-yy format)
 	 * - dash_date (date in mm/dd/yy format)
 	 * - login_8 (8 characters consisting of alphanumeric characters or "_")
 	 * - passwd_8 (8 characters consisting of alphanumeric characters or "!@#$%^&*()_+=-")
	 *
	 * @access public
	 * @static
 	 */
	function evaluateStringType( $var, $type )
	{
 		switch ( $type )
		{
			case "integer":
				if ( is_integer( $var ) )
					return true;
				
				break;

			case "double":
				if ( is_double( $var ) )
					return true;

				break;

			case "string":
				if ( is_string( $var ) )
					return true;
					
				break;

			case "email":
				$email_common_tld = "^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,3}\$";
				
				if ( eregi( $email_common_tld, $var ) )
					return true;

				break;

			case "numbers":
				if ( !ereg( "[^0-9]", $var ) )
					return true;

				break;

			case "numeric":
				if ( is_numeric( $var ) )
					return true;

				break;

			case "loosenumbers":
				if ( !preg_match( "/[^0-9\$\!\@\#\$\%\^\&\*\(\)\+\/\<\>\{\}\[\-\]\\\]/", $var ) )
					return true;

				break;

			case "alpha":
				if ( !ereg( "[^a-zA-Z]", $var ) )
					return true;

				break;

			case "phone_us":
				if ( ereg( "^([2-9][0-9]{2})([2-9][0-9]{2})([0-9]{4})$", $var ) )
					return true;

				break;

			case "phone_int":
				if ( !preg_match( "/[^0-9\(\)\-\. ]/", $var ) )
					return true;

				break;

			case "slash_date":
				if ( preg_match( "/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2}$/", $var ) )
					return true;

				break;

			case "dash_date":
				if ( preg_match( "/^[0-9]{1,2}-[0-9]{1,2}-[0-9]{2}$/", $var ) )
					return true;

				break;

			case "login_8":
				if ( preg_match( "/^[0-9a-zA-Z_]{8,}$/", $var ) )
					return true;

				break;

			case "passwd_8":
				if ( preg_match( "/^[0-9a-zA-Z_\!\@\#\$\%\^\&\*\(\)\_\+\=\-]{8,}$/", $var ) )
					return true;

				break;

			default :
				return false;
		}
	}
	
	/**
	 * @access public
	 * @static
	 */
	function normalize( $param, $specialDouble = true ) 
	{
		if ( $specialDouble ) 
		{
			$ts = array( '/ƒ/', '/÷/', '/‹/', '/‰/', '/ˆ/', '/¸/' );
			$tn = array( 'AE',  'OE',  'UE',  'ae',  'oe',  'ue'  );
			
			$param = preg_replace( $ts, $tn, $param );
		}

		$ts = array( "/[¿-≈]/", "/∆/", "/«/", "/[»-À]/", "/[Ã-œ]/", "/–/", "/—/", "/[“-÷ÿ]/", "/◊/", "/[Ÿ-‹]/", "/›/", "/ﬂ/", "/[‡-Â]/", "/Ê/", "/Á/", "/[Ë-Î]/", "/[Ï-Ô]/", "//", "/Ò/", "/[Ú-ˆ¯]/", "/˜/", "/[˘-¸]/", "/[˝-ˇ]/" );
		$tn = array( "A",       "AE",  "C",   "E",       "I",       "D",   "N",   "O",        "X",   "U",       "Y",   "ss",  "a",       "ae",  "c",   "e",       "i",       "d",   "n",   "o",        "x",   "u",       "y"       );
		
		return preg_replace( $ts, $tn, $param );
	}
	
	/**
	 * @access public
	 * @static
	 */
	function longestCommonSubstring( $string1, $string2 ) 
	{
		$L = array();
		$length1 = strlen( $string1 );
		$length2 = strlen( $string2 );
		
		for ( $i = $length1; $i >= 0; $i-- )
		{
			for ( $j = $length2; $j >= 0; $j-- )
			{
				if ( $string1[$i] == '' || $string2[$j] == '' ) 
					$L[$i][$j] = 0;
				else if ( $string1[$i] == $string2[$j] ) 
					$L[$i][$j] = 1 + $L[$i + 1][$j + 1];
				else 
					$L[$i][$j] = max( $L[$i + 1][$j], $L[$i][$j + 1] );
			}
		}

		$substring = '';
		$i = 0;
		$j = 0;
		
		while ( $i < $length1 && $j < $length2 )
		{
			if ( $string1[$i] == $string2[$j] )
			{
				$substring .= $string1[$i];
				$i++; 
				$j++;
			}
			else if ( $L[$i + 1][$j] >= $L[$i][$j + 1] ) 
			{
				$i++;
			}
			else 
			{
				$j++;
			}
		}

		return $substring;
	}

	/**
	 * @access public
	 * @static
	 */
	function ordinal( $num = 1 ) 
	{
		$ords = array( "th", "st", "nd", "rd" );
		$val  = $num;
		
		if ( ( ( $num %= 100 ) > 9 && $num < 20 ) || ( $num %= 10 ) > 3 ) 
			$num = 0;
		
		return $val . $ords[$num];
	}
	
	/**
	 * Counts number of words in a string
	 * if $realwords == 1 then remove things like '-', '+', which
	 * are surrounded with white space
	 *
	 * @access public
	 * @static
	 */
	function countWords( $str, $realwords = 1 )
	{
		if ( is_array( $str ) )
			return false;
		
		if ( $realwords )
			$str = preg_replace( "/(\s+)[^a-zA-Z0-9](\s+)/", " ", $str );
		
		return ( count( split( "[[:space:]]+", $str ) ) );
	}

	/**
	 * @access public
	 * @static
	 */
	function lcwords( $p )
	{
		$c = explode( " ", $p );

		if ( $c )
		{
			$d = array();
			
			while ( list( $a, $b) = each( $c ) )
				array_push( $d, preg_replace("/^([¿-›]|[A-Z]){1,1}/e", "chr(ord('\\1')+32)", $b ) );

			return join( " ", $d );
		}
	}
	
	/**
	 * @access public
	 * @static
	 */	
	function countSentences( $str )
	{
		return preg_match_all( '/[^\s]\.(?!\w)/', $str, $blah );
	}

	/**
	 * @access public
	 * @static
	 */	
	function countParagraphs( $str )
	{
		return count( preg_split( '/[\r\n]+/', $str ) );
	}

	/**	
	 * Returns some information about a passed string
	 * if $realwords == 1 then remove things like '-', '+', which
	 * are surrounded with white space
	 *
	 * @access public
	 * @static
	 */	
	function stringInfo( $str, $realwords = 1 )
	{
		$info['characters'] = ( $realwords? preg_match_all( "/[^\s]/", $str, $blah ) : strlen( $str ) );
		$info['words']      = StringUtil::countWords( $str, $realwords );
		$info['sentences']  = StringUtil::countSentences( $str );
		$info['paragraphs'] = StringUtil::countParagraphs( $str );
		
		return $info;
	}
	
	/**
	 * Determine whether to print a plural 's' or not.
	 *
	 * @access public
	 * @static
	 */
	function pluralS( $count ) 
	{
		return ( ( $count == 1 )? "" : "s" );
	}
	
	/**
	 * Determine whether to print a plural 'es' or not.
	 *
	 * @access public
	 * @static
	 */
	function pluralES( $count )
	{
		return ( ( $count == 1 )? "" : "es" );
	}
	
	/**
	 * Function returns an array containing n number of entries (defaults to 2) 
	 * of 'equal' length (word wrap) from desired text. Created for columnizing 
	 * larger textblocks faciliating layout. Set return_formated flag to TRUE 
	 * if you'd like the columns returned within a prelayouted html table 
	 * (with="100%") (defaults to FALSE) and includes the css_class to all cells 
	 * (defaults to NULL).
	 *
	 * @access public
	 * @static
	 */ 
    function text2columns( $str, $cols = 2, $formated = false, $class = null )
	{ 
        $size   = strlen( $str ) / $cols; 
        $tmpstr = explode( " ", $str ); 
        $i = 0;
		
        for ( $j = 0; $j < $cols; $j++ )
		{ 
            while ( $i <= sizeof( $tmpstr ) )
			{ 
                if ( strlen( $col[$j] ) < $size )
				{ 
                    $col[$j] .= $tmpstr[$i] . " "; 
                    $i++; 
                } 
                else
				{
					break;
				}
            } 
            
			rtrim( $col[$j] ); 
		}
		
		if ( $formated != false )
		{ 
            if ( $class != null ) 
                $class = ' class="' . $class . '"'; 
			
			$form = '<table width="100%" cellspacing="0"><tr' . $class . ' valign="top">'; 
			
			for ( $i = 0; $i < $cols; $i++ ) 
                $form .= '<td>' . $col[$i] . '</td>'; 
            
            $form .= '</tr></table>'; 
            return $form; 
        }
		else
		{
			return $col; 
    	} 
	}
	
	/**
	 * @access public
	 * @static
	 */
	function percentUppercase( $string ) 
	{
		$lower = strtolower( $string );
		$lev   = levenshtein( $string, $lower );
		
		return (int)( $lev / strlen( $string ) * 100);
	}
	
	/**
	 * @access public
	 * @static
	 */
	function chomp( $p )
	{
		return preg_replace( "/[\n\r]/", "", $p );
	}

	/**
	 * @access public
	 * @static
	 */
	function barQuote( $s )
	{
		preg_replace( "/\//", "\/", $s );
	}
	
	/**
 	 * This function takes a string as a parameter, and returns a 
	 * numbered array of the possible anagrams of the string.
	 *
	 * @access public
	 * @static
 	 */
    function getAnagrams( $string ) 
    { 
        $ana = array(); 
        
		for ( $i = 0; $i < strlen( $string ); $i++ ) 
        { 
            for ( $j = 0; $j < strlen( $string ); $j++ ) 
            { 
                $temp     = $string; 
                $t        = $temp[$j]; 
                $temp[$j] = $temp[$i]; 
                $temp[$i] = $t; 
                
				if ( !in_array( $temp, $ana ) ) 
                    $ana[] = $temp; 
            }
        }
		
        return $ana; 
    }
	
	/**
	 * Find the closing punctuation in a text block.
	 *
	 * @param $str Text block
	 * @return String Returns the string up to the final punctuation.
	 * @access public
	 * @static
	 */
	function getClosingPunctuation( $str )
	{
		if ( preg_match( "/([,.?!\"]+)[^,.?!\"]*$/", $str, $regs ) )
			return strrpos( $str, $regs[1] );
		
		return strrpos( $str, " " );
	}
	
	/**
	 * Split a block of text into an array of paragraphs.
	 *
	 * @param $str The block of text
	 * @return Array of paragraphs
	 * @access public
	 * @static
	 */
	function getParagraphs( $str )
	{
		$pgs = explode( "\n", $str );
		$pg_list = array();
		
		for ( $i = 0; $i < count( $pgs ); $i++ )
		{
			$pgs[$i] = trim( $pgs[$i] );
			
			if ( strlen( $pgs[$i] ) > 1 )
				$pg_list[] = $pgs[$i];
		}
		
		return $pg_list;
	}
	
	/**
	 * Calculates the input with parenthesis levels.
	 *
	 * @access public
	 * @static
	 */
	function calcParenthesis( $string )	
	{
		$securC = 100;
		
		do 
		{
			$valueLenO = strcspn( $string, "(" );
			$valueLenC = strcspn( $string, ")" );
			
			if ( $valueLenC == strlen( $string ) || $valueLenC < $valueLenO )
			{
				$value  = StringUtil::calcPriority( substr( $string, 0, $valueLenC ) );
				$string = $value . substr( $string, $valueLenC + 1 );
				
				return $string;
			} 
			else 
			{
				$string = substr( $string, 0, $valueLenO ) . StringUtil::calcParenthesis( substr( $string, $valueLenO + 1 ) );
			}
			
			// security
			$securC--;

			if ( $securC <= 0 )	
				break;
		} while ( $valueLenO < strlen( $string ) );
		
		return $string;
	}
	
	/**
	 * Calculates the input by +,-,*,/,%,^ with priority to + and -
	 *
	 * @access public
	 * @static
	 */
	function calcPriority( $string )	
	{
		$string = ereg_replace( "[[:space:]]*", "", $string );	// removing all whitespace
		$string = "+" . $string;	// Ensuring an operator for the first entrance
		$qm     = "\*\/\+-^%";
		$regex  = "([$qm])([$qm]?[0-9\.]*)";
		
		// split the expression here:
		preg_match_all( "/" . $regex . "/", $string, $reg );
		
		reset( $reg[2] );
		$number = 0;
		$Msign  = "+";
		$err    = "";	
		$buffer = doubleval( current( $reg[2] ) );
		
		next( $reg[2] ); // Advance pointer
		while ( list( $k, $v ) = each( $reg[2] ) )	
		{
			$v = doubleval( $v );
			$sign = $reg[1][$k];
			
			if ( $sign == "+" || $sign == "-" )	
			{
				$number = ( $Msign == "-" )? $number -= $buffer : $number += $buffer;
				$Msign  = $sign;
				$buffer = $v;
			} 
			else 
			{
				if ( $sign == "/" )	
				{
					if ( $v ) 
						$buffer /= $v; 
					else 
						$err = "dividing by zero";
				}
				
				if ( $sign == "%" )	
				{
					if ( $v ) 
						$buffer %= $v; 
					else 
						$err = "dividing by zero";
				}
				
				if ( $sign == "*" )	
					$buffer *= $v;
				
				if ( $sign == "^" )	
					$buffer = pow( $buffer, $v );
			}
		}
		
		$number = $Msign == "-"? $number-=$buffer : $number+=$buffer;
		return $err? "ERROR: " . $err : $number;
	}
	
	/**
	 * Converts text surrounded by double carriage-returns into paragraphs.
	 *
	 * @param  $str Plain text string
	 * @return String HTML
	 * @access public
	 * @static
	 */
	function nl2p( $str ) 
	{
		return "<p>" . preg_replace( '/[\n\r]{3,}/', "</p>\n<p>", $str ) . "</p>";
	}
	
	/**
	 * @access public
	 * @static
	 */	
    function germanChars( $string )
	{
        $string = str_replace( "ƒ", "&Auml;",   $string );
        $string = str_replace( "÷", "&Ouml;",   $string );
        $string = str_replace( "‹", "&Uuml;",   $string );
        $string = str_replace( "‰", "&auml;",   $string );
        $string = str_replace( "ˆ", "&ouml;",   $string );
        $string = str_replace( "¸", "&uuml;",   $string );
        $string = str_replace( "ﬂ", "&szlig;",  $string );
        $string = str_replace( "È", "&eacute;", $string );
		
        return $string;
    }
	
	/**
	 * Takes a non-associative array of strings, finds the longest string and pads the rest
	 * out to that length using whatever character you specify. 
	 * Optionally can pad out to a specified length.
	 *
	 * @access public
	 * @static
	 */
	function padding( &$array, $character, $length = 0 )
	{ 
		if ( count( $array ) == 0 )
			return; 
		
		$longest = 0; 
		
		for ( $i = 0; $i < count( $array ); $i++ )
		{ 
			if ( strlen( $array[$i] ) > strlen( $array[$longest] ) )
				$longest = $i; 
		} 

		if ( $length == 0 )
			$length = strlen( $array[$longest] ); 

		for ( $i = 0; $i < count( $array ); $i++ )
		{ 
			$padding = $length - strlen( $array[$i] );
			 
			for ( $j = 0; $j < $padding; $j++ ) 
				$array[$i] .= $character; 
		} 
	}
	
	/**
	 * @access public
	 * @static
	 */	
	function permutate( $strDataIn, $length, &$permutateCount )
	{	
		for ( $i = 0; $i < strlen( $strDataIn ); $i++ )
		{
			$permArray[0][$i] = substr( $strDataIn, $i, 1 );
			$temp[$i]         = substr( $strDataIn, $i, 1 );
			$temp2[0][$i]     = substr( $strDataIn, $i, 1 );  
		}
		
		for ( $i = 1; $i < $length; $i++ )
		{
			for ( $k = 0; $k < strLen( $strDataIn ); $k++ )
			{
				for ( $j = 0; $j < sizeof( $temp2[$i - 1] ); $j++ )
				{
					$permArray[$i][( $k * sizeof( $temp2[$i - 1] ) ) + $j] = $temp[$k] . $temp2[$i - 1][$j];
					$temp2[$i][($k * sizeof( $temp2[$i - 1] ) ) + $j] = $temp[$k] . $temp2[$i - 1][$j];
				}		
			}
		}   
		
		$k = 0;

		for ( $i = 0; $i < $length; $i++ )
			$k += sizeof( $permArray[$i] );
		
		$permutateCount = $k;	
		return $permArray;
	}
	
	/**
	 * Inserts a string into another string every increment.
	 *
	 * @access public
	 * @static
	 */
	function sow( $string, $insert, $increment ) 
	{
		$insert_len = strlen( $insert );
		$string_len = strlen( $string );
		$string_len_ending = $string_len + intval( $insert_len * ( $string_len / $increment ) );
		$i = $increment - 1;
		
		while ( $string_len_ending > $i ) 
		{
			$string = substr( $string, 0, $i ) . $insert . substr( $string, $i );
			$i = $i + $increment + $insert_len;
		}

		return $string;
	}

	/**
	 * @access public
	 * @static
	 */	
	function containsBadWords( $string, $bad_words = array() )
	{ 
		for ( $i = 0; $i < count( $bad_words ); $i++ )
		{
			if ( strstr( strtoupper( $string ), strtoupper( $bad_words[$i] ) ) )
        		return true;
		}
	
		return false;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function replaceBadWords( $content, $bad_words = array(), $word_replace = array( "!", "@", "#", "%", "^", "&", "*", "~" ) )
	{
		$count       = count( $bad_words );
		$countfilter = count( $word_replace );
		
		// Loop through the badwords array.
		for ( $n = 0; $n < $count; ++$n, next( $bad_words ) )
		{
			// Create random replace characters.
			$x = 2;
			$y = rand( 3, 5 );
			$filter = "";
			
			while ( $x <= "$y" )
			{
				$f = rand( 0, $countfilter );
				$filter .= "$word_replace[$f]";
		      	
				$x++;
			}

			// Search for badwords in content.
			$search  = "$bad_words[$n]";
			$content = preg_replace( "'$search'i", "<i>$filter</i>", $content );
		}
		
		return $content;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function addLineNumbers( &$str, $start = 1, $indent = 3 ) 
	{
		$line   = explode( "\n", $str );
		$size   = sizeof( $line );
		$with   = strlen( (string)( $start + $size - 1 ) );
		$indent = max( $with, $indent );
		
		for ( $i = 0; $i < $size; $i++ ) 
			$line[$i] = str_pad( (string)( $i + $start ), $indent, ' ', STR_PAD_LEFT ) . ': ' . $line[$i];

		return implode( "\n", $line );
	}
	
	/**
	 * @access public
	 * @static
	 */
	function escapeForRegexp( $string, $escapeChar = '/' ) 
	{
		$search = array( 
			'\\',   
			'^',   
			'.',
			'[',
			'$',
			'(',
			')',
			'|',
			'*',
			'+',
			'?',
			'{'
		);
		
		$replace = array(
			'\\\\', 
			'\\^',  
			'\\.',  
			'\\[',  
			'\\$',  
			'\\(',  
			'\\)',  
			'\\|',  
			'\\*',  
			'\\+',  
			'\\?',  
			'\\{' 
		);
		
		$search[]  = $escapeChar;
		$replace[] = '\\' . $escapeChar;
		
		return str_replace( $search, $replace, $string );
	}
	
	/** 
	 * @access public
	 * @static
	 */
	function justifyText( $text, $width = 81 ) 
	{
	  	$size = strlen( $text );
	  
	  	if ( $size > 6000 )
	    	return false;
	
	  	// cleanup
	  	$text = ereg_replace( "\r", " ", $text );
	  	$text = ereg_replace( "\t", " ", $text );
	  	$text = ereg_replace( "\n", " ", $text );
	  
	  	while ( ereg( "  ", $text ) )
	    	$text = str_replace( "  ", " ", $text );
	    
	  	$size = strlen( $text );
	
	  	// put words in array
	  	$string = explode( " ", $text );
	  	$words  = count( $string );
	  	$a = 1;
	  
	  	// strip lines smaller than $width to an array
	  	for ( $i = 0 ; $i < $words ; $i++ ) 
		{
	    	if ( strlen( $output[$a] . $string[$i] ) >= $width )
	      		$a++;
	      
	    	$output[$a] .= $string[$i] . " ";
	  	}
	
	  	$i = -1;
	  	while ( $i < count( $output ) ) 
		{
	    	$v   = ltrim( chop( $output[$i] ) );
	    	$len = strlen( $v ) + 1;
	    	$i++;
	    
			$page .= StringUtil::justifyLine( $v, $width - 1 ) . "\n";
	
	  	}
  
	  	return $page . $output[count( $output )] . "\n";
	}

	/**
	 * This justifies only one line (provided strlen($text) is smaller than $width
	 * If the line is longer than expected, it is stripped and returned as is.
	 *
	 * @access public
	 * @static
	 */
	function justifyLine( $text, $width )
	{
  		$width++;
  
  		$text = ltrim( chop( $text ));
  		$len  = strlen( $text ); 

		// limitation to 132 cols
  		if ( $width <= 10 || width > 132 )
    		return false;

  		if ( $len >= $width )
    		return substr( $text, 0, $width );

  		$neededspaces    = $width - $len;
  		$availablespaces = explode( " ", $text );
  		$availablespaces = count( $availablespaces ) - 1;
  		$ratio           = $availablespaces / $neededspaces;
  		$usablespaces    = "";

  		if ( $ratio != 0 ) 
		{
    		if ( $neededspaces >= $availablespaces ) 
			{
      			while ( ( $width >= $len ) && ( $neededspaces >= 0 ) ) 
				{
        			$i = 0;// used as pointer inside the string
					
					// parse all string
        			while ( ( $i <= $len ) && ( $neededspaces >= 0 ) ) 
					{
          				$temp = substr( $text, $i, 1 ); // get one letter
          
		  				// is it a space?
		  				if ( $temp == " " ) 
						{
            				$text = substr( $text, 0, $i ) . "  " . substr( $text, $i + 1, $len );// insert another space
            				$neededspaces--; // one space less
            				$len = strlen( $text );
            				
							$i++;
            			}
          
		  				$i++;
          			}
        
					$len = strlen( $text );
        		}
      		}
    		else 
			{
      			while ( ( $width >= $len ) && ( $neededspaces >= 0 ) ) 
				{
        			$i = 0;// used as pointer inside the string
        
					// parse all string
					while ( ( $i <= $len ) && ( $neededspaces >= 0 ) ) 
					{
          				$temp = substr( $text, $i,1 ); // get one letter
          
		  				 // is it a space?
		  				if ( $temp == " " ) 
						{
            				$text = substr( $text, 0, $i ) . "  " . substr( $text, $i + 1, $len ); // insert another space
            				$neededspaces--; // one space less
            				$len = strlen( $text );
            				
							$i++;
            			}
          				
						$i++;
          			}
        
					$len = strlen( $text );
        		}
      		}
    	}
  
  		$out = $text;
  		return $out;
  	}
	
	/**
	 * Show php source with line numbers.
	 *
	 * @access public
	 * @static
	 */
	function showSource( $file, $numColor = '#5F5F5F' ) 
	{ 
  		ob_start(); 
		highlight_file( $file ); 
		$source = ob_get_contents(); 
    	ob_end_clean(); 
    
		$source = explode( "<br />", $source ); 
    	$count  = sizeof( $source ); 
    
		for ( $i = 1; $i <= $count; $i++ )
        	echo "<span style=\"color: $numColor;\">$i</span> " . $source[$i - 1] . "\n"; 
    
    	return; 
	}
	
	/**
	 * This splits a string by the chars in $operators (typical /+-*) and returns an array with them in.
	 *
	 * @access public
	 * @static
	 */
	function splitCalc( $string, $operators )	
	{
		$res  = array();
		$sign = "+";
		
		while ( $string )	
		{
			$valueLen = strcspn( $string, $operators   );
			$value    = substr( $string, 0, $valueLen  );
			$res[]    = array( $sign, trim( $value )   );
			$sign     = substr( $string, $valueLen, 1  );
			$string   = substr( $string, $valueLen + 1 );
		}
		
		reset( $res );
		return $res;
	}
	
	/**
	 * Returns true of the submitted text has meta characters in it
	 * . \\ + * ? [ ^ ] ( $ )
	 *
	 * @access public
	 * @static
	 */
	function hasMetas( $text = "" )
	{
		if ( empty( $text ) )
			return false;

		$New = quotemeta( $text );

		if ( $New == $text )
			return false;

		return true;
	}
	
	/**
	 * Strips "  . \\ + * ? [ ^ ] ( $ )  " from submitted string
	 * Metas are a virtual MINE FIELD for regular expressions.
	 *
	 * @access public
	 * @static
	 */
	function stripMetas( $text = "" )
	{
		if ( empty( $text ) )
			return false;

		$Metas = array( '.', '+', '*', '?', '[', '^', ']', '(', '$', ')' );
		$text  = stripslashes( $text );
		$New   = StringUtil::customStrip( $Metas, $text );
		
		return $New;
	}

	/**
	 * $Chars must be an array of characters to remove.
	 * This method is meta-character safe.
	 *
	 * @access public
	 * @static
	 */
	function customStrip( $chars, $text = "" )
	{
		if ( empty( $text ) )
			return false;

		if ( ( gettype( $chars ) ) != "array" )
			return PEAR::raiseError( "$chars must be of type array." );

		while ( list( $key, $val ) = each( $Chars ) )
		{
			if ( !empty( $val ) )
			{
				// str_replace is meta-safe, ereg_replace is not
				$text = str_replace( $val, "", $text );
			}
		}

		return $text;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function removeFromToInt( $string, $from, $to ) 
	{
		$t  = substr( $string, 0, $from );
		$t .= substr( $string, $to + 1);
		
		return $t;
	}
	
    /**
     * Converts a string from one charset to another.
     *
     * Works only if either the iconv or the mbstring extension
     * are present and best if both are available.
     * The original string is returned if conversion failed or none
     * of the extensions were available.
     *
     * @param  mixed $input  The data to be converted. If $input is an an
     *                       array, the array's values get converted recursively.
     * @param  string $from  The string's current charset.
     * @param  string $to    (optional) The charset to convert the string to.
     *
     * @return string        The converted string.
	 * @access public
	 * @static
     */
    function convertCharset( $input, $from, $to = null )
    {
        if ( is_array( $input ) ) 
		{
            $tmp = array();
            
			foreach ( $input as $key => $val )
                $tmp[StringUtil::convertCharset( $key, $from, $to )] = StringUtil::convertCharset( $val, $from, $to );
            
            return $tmp;
        }
		
        if ( is_object( $input ) ) 
		{
            $vars = get_object_vars( $input );
			
            foreach ( $vars as $key => $val )
                $input->$key = StringUtil::convertCharset( $val, $from, $to );
            
            return $input;
        }

        if ( !is_string( $input ) )
            return $input;

        global $nls;

        $output = false;

        /* Get the user's default character set if none passed in. */
        if ( is_null( $to ) )
            $to = $GLOBALS['AP_STRINGUTIL_CHARSET'];

        /* If the from and to chaacter sets are identical, return now. */
        $str_from = StringUtil::lower( $from );
        $str_to   = StringUtil::lower( $to );
		
        if ( $str_from == $str_to )
            return $input;

        /* Use utf8_[en|de]code() if possible. */
        $str_from_check = ( ( $str_from == 'iso-8859-1' ) || ( $str_from == 'us-ascii' ) );
		
        if ( $str_from_check && ( $str_to == 'utf-8' ) )
            return utf8_encode( $input );

        $str_to_check = ( ( $str_to == 'iso-8859-1' ) || ( $str_to == 'us-ascii' ) );
		
        if ( ( $str_from == 'utf-8' ) && $str_to_check )
            return utf8_decode( $input );

        /* First try iconv with transliteration. */
        if ( Util::extensionExists( 'iconv' ) ) 
		{
            ini_set( 'track_errors', 1 );
			
            /* We need to tack an extra character temporarily
               because of a bug in iconv() if the last character
               is not a 7 bit ASCII character. */
            $output = @iconv( $from, $to . '//TRANSLIT', $input . 'x' );
			
            if ( isset( $php_errormsg ) )
                $output = false;
            else
                $output = substr( $output, 0, -1 );
            
            ini_restore( 'track_errors' );
        }

        /* Next try mbstring. */
        if (!$output && Util::extensionExists('mbstring')) {
            $output = @mb_convert_encoding($input, $to, $from);
        }

        /* At last try imap_utf7_[en|de]code if appropriate. */
        if ( !$output && Util::extensionExists( 'imap' ) ) 
		{
            if ( $str_from_check && ( $str_to == 'utf7-imap' ) )
                return @imap_utf7_encode( $input );
            
            if ( ( $str_from == 'utf7-imap' ) && $str_to_check )
                return @imap_utf7_decode( $input );
        }

        return ( !$output )? $input : $output;
    }

    /**
     * Makes a string lowercase.
     *
     * @param  string $string  The string to be converted.
     * @param  bool   $locale  (optional) If true the string will be converted
     *                         based on a given charset, locale independent else.
     * @param  string $charset (optional) If $locale is true, the charset to use 
     *                         when converting. If not provided the current
     *                         charset.
     * @return string          The string with lowercase characters
	 * @access public
	 * @static
     */
    function lower( $string, $locale = false, $charset = null )
    {
        static $lowers;

        if ( $locale ) 
		{
            /* The existence of mb_strtolower() depends on the platform. */
            if ( Util::extensionExists( 'mbstring' ) && function_exists( 'mb_strtolower' ) ) 
			{
                if ( is_null( $charset ) )
                    $charset = $GLOBALS['AP_STRINGUTIL_CHARSET'];
                
                $ret = @mb_strtolower( $string, $charset );
				
                if ( !empty( $ret ) )
                    return $ret;
            }
			
            return strtolower( $string );
        }

        if ( !isset( $lowers ) )
            $lowers = array();
        
        if ( !isset( $lowers[$string] ) ) 
		{
            $language = setlocale( LC_CTYPE, 0 );
            setlocale( LC_CTYPE, 'en' );
            $lowers[$string] = strtolower( $string );
            setlocale( LC_CTYPE, $language );
        }

        return $lowers[$string];
    }

    /**
     * Makes a string uppercase.
     *
     * @param  string $string  The string to be converted.
     * @param  bool $locale    (optional) If true the string will be converted 
     *                         based on a given charset, locale independent else.
     * @param  string $charset (optional) If $locale is true, the charset to use 
     *                         when converting. If not provided the current 
     *                         charset.
     *
     * @return string          The string with uppercase characters
	 * @access public
	 * @static
     */
    function upper( $string, $locale = false, $charset = null )
    {
        static $uppers;

        if ( $locale ) 
		{
            /* The existence of mb_strtoupper() depends on the platform. */
            if ( Util::extensionExists( 'mbstring' ) && function_exists( 'mb_strtoupper' ) ) 
			{
                if ( is_null( $charset ) )
                    $charset = $GLOBALS['AP_STRINGUTIL_CHARSET'];
                
                $ret = @mb_strtoupper( $string, $charset );
				
                if ( !empty( $ret ) )
                    return $ret;
            }
			
            return strtoupper( $string );
        }

        if ( !isset( $uppers ) )
            $uppers = array();
        
        if ( !isset( $uppers[$string] ) ) 
		{
            $language = setlocale( LC_CTYPE, 0 );
            setlocale( LC_CTYPE, 'en' );
            $uppers[$string] = strtoupper( $string );
            setlocale( LC_CTYPE, $language );
        }

        return $uppers[$string];
    }

    /**
     * Returns part of a string.
     *
     * @param  string $string  The string to be converted.
     * @param  int $start      The part's start position, zero based.
     * @param  int $length     (optional) The part's length.
     * @param  string $charset (optional) The charset to use when calculating 
     *                         the part's position and length, defaults to 
     *                         current charset.
     *
     * @return string          The string's part.
	 * @access public
	 * @static
     */
    function substr( $string, $start, $length = null, $charset = null )
    {
        if ( Util::extensionExists( 'mbstring' ) ) 
		{
            if ( is_null( $charset ) )
                $charset = $GLOBALS['AP_STRINGUTIL_CHARSET'];
            
            if ( is_null( $length ) )
                $length = StringUtil::length( $string, $charset );
            
            $ret = @mb_substr( $string, $start, $length, $charset );
			
            if ( !empty( $ret ) )
                return $ret;
        }
		
        if ( is_null( $length ) )
            $length = StringUtil::length( $string );
        
        return substr( $string, $start, $length );
    }

    /**
     * Returns the character (not byte) length of a string.
     *
     * @param  string $string  The string to return the length of.
     * @param  string $charset (optional) The charset to use when calculating
     *                         the string's length.
     *
     * @return string          The string's part.
	 * @access public
	 * @static
     */
    function length( $string, $charset = null )
    {
        if ( Util::extensionExists( 'mbstring' ) ) 
		{
            if ( is_null( $charset ) )
                $charset = $GLOBALS['AP_STRINGUTIL_CHARSET'];
            
            $ret = @mb_strlen( $string, $charset );
			
            if ( !empty( $ret ) )
                return $ret;
        }
		
        return strlen( $string );
    }

    /**
     * Returns the numeric position of the first occurrence of $needle in
     * the $haystack string.
     *
     * @param  string $haystack  The string to search through.
     * @param  string $needle    The string to search for.
     * @param  int $offset       (optional) Allows to specify which character in 
     *                           haystack to start searching.
     * @param  string $charset   (optional) The charset to use when searching 
     *                           for the $needle string.
     *
     * @return int               The position of first occurrence.
	 * @access public
	 * @static
     */
    function pos( $haystack, $needle, $offset = 0, $charset = null )
    {
        if ( Util::extensionExists( 'mbstring' ) ) 
		{
            if ( is_null( $charset ) )
                $charset = $GLOBALS['AP_STRINGUTIL_CHARSET'];
            
            ini_set( 'track_errors', 1 );
            $ret = @mb_strpos( $haystack, $needle, $offset, $charset );
            ini_restore( 'track_errors' );
            
			if ( !isset( $php_errormsg ) )
                return $ret;
        }
		
        return strpos( $haystack, $needle, $offset );
    }

	/**
	 * @access public
	 * @static
	 */
    function isAlpha( $string, $charset = null )
    {
        return ctype_alpha( $string );
    }

    /**
     * Returns true if every character in the parameter is a lowercase
     * letter in the current locale.
     *
     * @param  $string   The string to test.
     * @param  $charset  (optional) The charset to use when testing the string.
     * @return bool      True if the parameter was lowercase.
	 * @access public
	 * @static
     */
    function isLower( $string, $charset = null )
    {
        return ( ( StringUtil::lower( $string, true, $charset ) === $string ) && StringUtil::isAlpha( $string, $charset ) );
    }

    /**
     * Returns true if every character in the parameter is an uppercase
     * letter in the current locale.
     *
     * @param  $string   The string to test.
     * @param  $charset  (optional) The charset to use when testing the string.
     * @return bool      True if the parameter was uppercase.
	 * @access public
	 * @static
     */
    function isUpper( $string, $charset = null )
    {
        return ( ( StringUtil::upper( $string, true, $charset) === $string ) && StringUtil::isAlpha( $string, $charset ) );
    }

    /**
     * Returns only these lines of string that match the specified
     * pattern.
     *
     * @param  string $array          A string to search for matches.
     * @param  string $pattern        A regular expression to search for.
     * @param  boolean $return_match  (optional) If true the matching line of the
     *                                string is returned, otherwise the lines
     *                                that do not match will be returned.
     * @return string  A string with the matching/not matching lines.
	 * @access public
	 * @static
     */
    function grep( $subject, $pattern, $return_match = true )
    {
        $array = preg_split( '/\r\n|\r|\n/', $subject );
        $array = ArrayUtil::grep( $array, $pattern, $return_match );
		
        return implode( "\n", $array );
    }
	
    /**
     * Returns a given string in reversed order.
	 *
     * @param  string $text
     * @return string
	 * @access public
	 * @static
     */
    function strReverse( $text ) 
	{
        $str1 = $text;
        $str2 = '';
        
		while ( strlen( $str1 ) > 0 ) 
		{
            $str2 .= substr( $str1, -1 );
            $str1  = substr( $str1, 0, -1 );
        }
		
        return $str2;
    }
	
	/**
     * Get longest common string.
	 *
     * @param  string $s1
	 * @param  string $s2
	 * @access public
	 * @static
     */
	function longestCommonString( $s1, $s2 )
	{
  		// ok, now replace all spaces with nothing
  		$s1 = strtolower( StringUtil::_lcsFix( $s1 ) );
  		$s2 = strtolower( StringUtil::_lcsFix( $s2 ) );
  
  		$lcs = StringUtil::_lcsLength( $s1, $s2 ); // longest common sub sequence
  		$ms  = ( strlen( $s1 ) + strlen( $s2 ) ) / 2;

  		return ( ( $lcs * 100 ) / $ms );
	}

	/**
 	 * Takes a string and looks for an integer at the end of it.
	 * It tries to increment this integer, if it can't find one, it appends "2".
	 * The option spacer only works if no int is already present - good for starting a trend.
	 *
	 * @access public
	 * @static
	 */
	function incrementName( $name, $spacer = "" ) 
	{
		for ( $i = strlen( $name ) - 1; $i > 0; $i-- ) 
		{
			if ( !ereg( "[^a-zA-Z0-9]", $name[$i] ) ) 
				break;
		} 
		
		$trailing_whitespace = substr( $name, $i + 1 );
	
		for ( $j = $i; $j > 0; $j-- ) 
		{
			if ( !ereg( "[0-9]", $name[$j] ) ) 
				break;
		} 
		
		$int = substr( $name, $j + 1, $i - $j );
	
		if ( !$int ) 
			return substr( $name, 0, $j + 1 ) . $spacer . ( 2 ) . $trailing_whitespace;
	
		return substr( $name, 0, $j + 1 ) . ( $int + 1 ) . $trailing_whitespace;
	}
	
    /**
     * Delete a specified amount of characters from a string as
     * of a specified position. The resulting string is copied to the 
     * parameter "string" and also returned as result.
     *
     * @static
     * @access  public
     * @param   &string string
     * @param   int pos
     * @param   int len default 1
     * @return  string
     */
    function delete( &$string, $pos, $len = 1 ) 
	{
      	$string = substr( $string, 0, $pos ) . substr( $string, $pos + 1 );
      	return $string;
    }
    
    /**
     * Insert a character into a string at a specified position. The 
     * resulting string is copied to the parameter "string" and also 
     * returned as result.
     *
     * @static
     * @access  public
     * @param   &string string
     * @param   Ónt pos
     * @param   char char
     * @return  string
     */
    function insert( &$string, $pos, $char ) 
	{
      	$string = substr( $string, 0, $pos ) . $char . substr( $string, $pos );
      	return $string;
    }
    
	/**
	 * @access public
	 * @static
	 */
	function hasSpecialChars( $myString, $charSet = 7, $myExceptions = null ) 
	{
		if ( $myString == '' ) 
			return false;
		
		$exceptionString = '';
		
		if ( ( is_array( $myExceptions ) ) && ( sizeof( $myExceptions ) > 0 ) ) 
		{
			$escapeChars = array(
				'^', 
				'.', 
				'[', 
				'$', 
				'(', 
				')', 
				'|', 
				'*', 
				'+', 
				'?', 
				'{', 
				'\\'
			);
			
			while ( list( $k ) = each( $myExceptions ) ) 
			{
				if ( in_array( $myExceptions[$k], $escapeChars ) ) 
					$exceptionString .= '\\' . $myExceptions[$k];
				else 
					$exceptionString .= $myExceptions[$k];
			}
		}

		$regExp = "^[a-zA-Z0-9" . $exceptionString . "]*$";
		return (bool)!ereg( $regExp, $myString );
	}
	
	/** 
	 * Returns $num chars from the left side of $haystack.
	 * 
	 * @param  string $haystack the string to be looked at
	 * @param  number $num      the number of chars to be returned. '' means all. 0 means none.
	 * @return string
	 * @access public
	 * @static
	 */
	function left( $haystack, $num = 0 ) 
	{
		if ( ( $haystack == '' ) || ( $num <= 0 ) ) 
			return '';
		
		return substr( $haystack, 0, $num );
	}

	/** 
	 * Returns $num chars from the right side of $haystack.
	 * 
	 * @param  string $haystack the string to be looked at
	 * @param  number $num      the number of chars to be returned. '' means all. 0 means none.
	 * @return string
	 * @access public
	 * @static
	 */
	function right( $haystack, $num = 0 ) 
	{
		if ( $num == '' ) 
			return $haystack;
		
		if ( ( $haystack == '' ) || ( $num == 0 ) ) 
			return '';
		
		return substr( $haystack, -$num );
	}

	/** 
	 * Returns $num chars from the middle of $haystack, beginning with $start (included). 
	 * note: $haystack begins with char 1, not with char 0. example:
	 * string "hello"
	 * number  12345
	 * 
	 * @param  string $haystack the string to be looked at.
	 * @param  number $start    the number of the character where to start. a string always begins at char 1 not 0!
	 * @param  number $num      the number of chars to be returned. '' means all to the end. 0 means none.
	 * @return string
	 * @access public
	 * @static
	 */
	function mid( $haystack, $start = 1, $num = 0 ) 
	{
		if ( $num == '' ) 
			return $haystack;
		
		if ( ( $haystack == '' ) || ( $num == 0 ) ) 
			return '';
		
		if ( $start == 0 ) 
			$start = 1;
		
		return substr( $haystack, $start -1, $num );
	}

	/**
	 * @access public
	 * @static
	 */
	function rTrim( $haystack, $replArray = '' ) 
	{
		if ( $haystack == '' ) 
			return '';
		
		if ( !( is_array( $replArray ) ) && ( $replArray == '' ) ) 
		{
			$replArray = array(
				" ", 
				"\n", 
				"\r", 
				"\t", 
				"\v", 
				"\0"
			);
		} 
		else if ( !( is_array( $replArray ) ) && ( $replArray != '' ) ) 
		{
			$replArray = array( $replArray );
		}
		
		$numChars = strlen( $haystack );
		
		for ( $i = $numChars -1; $i >= 0; $i-- ) 
		{
			if ( in_array( $haystack[$i], $replArray ) ) 
				;
			else 
				break;
		}

		if ( $numChars == $i + 1 ) 
			return $haystack;
		else 
			return StringUtil::left( $haystack, $i + 1 );
	}
	
	/**
	 * @access public
	 * @static
	 */	
	function cleanName( $str )	
	{
		// The $str is cleaned so that it contains alphanumerical characters only.
		return ereg_replace( "[^A-Za-z0-9]*", "", $str );
	}
	
    /**
     * Split a string into an array of blocks of equal length. Throws an
     * exception in a situation in which a length of less than or equal zero
     * was supplied.
     *
     * @static
     * @access  public
     * @param   string string
     * @param   int length
     * @return  array parts
     */
    function blocksplit( $string, $length ) 
	{
      // Catch bordercase in which this would result in and endless loop
      	if ( $length <= 0 ) 
			return PEAR::raiseError( sprintf( 'Paramater length (%s) must be greater than zero', var_export( $length, 1 ) ) );

		$r = array();
      
	  	do 
		{
        	$r[] = substr( $string, 0, $length );
        	$string = substr( $string, $length );
      	} while ( strlen( $string ) > 0 );

      	return $r;
    }
	
    /**
     * Filter the given text based on the words found in $words.
     *
     * @param  string  $text         The text to filter.
     * @param  string  $words_file   Filename containing the words to replace.
     * @param  string  $replacement  The replacement string.
     * @return string  The filtered version of $text.
	 * @access public
	 * @static
     */
    function filter( $text, $words_file, $replacement )
    {
        if ( @is_readable( $words_file ) ) 
		{
            /* Read the file and iterate through the lines. */
            $lines = file( $words_file );
			
            foreach ( $lines as $line ) 
			{
                /* Strip whitespace and comments. */
                $line = trim( $line );
                $line = preg_replace( '|#.*$|', '', $line );

                /* Filter the text. */
                if ( !empty( $line ) )
                    $text = preg_replace( "/(\b(\w*)$line\b|\b$line(\w*)\b)/i", $replacement, $text );
            }
        }

        return $text;
    }

    /**
     * Fixes incorrect wrappings which split double-byte gb2312 characters.
     *
     * @param  string $text  String containing wrapped gb2312 characters
     * @param  $break_char   Character used to break lines.
     * @return string        String containing fixed text.
	 * @access public
	 * @static
     */
    function trim_gb2312( $str, $break_char = "\n" )
    {
        $lines = explode( $break_char, $str );

        for ( $i = 0; $i < count( $lines ) - 1; $i++ ) 
		{
			$line = $lines[$i];
			$len  = strlen( $line );

			/* parse double-byte gb2312 characters */
			for ( $c = 0; $c < $len - 1; $c++ ) 
			{
				if ( ord( $line{$c} ) & 128 ) 
				{
					if ( ord( $line{$c + 1} ) & 128 ) 
						$c++;
				}
			}

			/* If the last character of the current line is the first byte
			   of a double-byte character, move it to the start of the
			   next line. */
			if ( ( $c == $len - 1 ) && ( ord( $line[$c] ) & 128 ) ) 
			{
				$lines[$i] = substr( $line, 0, -1 );
				$lines[$i + 1] = $line[$c] . $lines[$i + 1];
			}
        }
		
        return implode( $break_char, $lines );
    }

    /**
     * Wraps the text of a message.
     *
     * @param  string  $text        String containing the text to wrap.
     * @param  integer $length      Wrap $text at this number of characters.
     * @param  string  $break_char  Character to use when breaking lines.
     * @return string  String containing the wrapped text.
	 * @access public
	 * @static
     */
    function wrap( $text, $length = 80, $break_char = "\n", $charset = "" )
    {
        $paragraphs = explode( "\n", $text );
        $charset    = strtolower( $charset );
        
		switch ( $charset ) 
		{
            case "gb2312":
                for ( $i = 0; $i < count( $paragraphs ); $i++ ) 
				{
                    $paragraphs[$i] = wordwrap( $paragraphs[$i], $length, $break_char, 1 );
                    $paragraphs[$i] = StringUtil::trim_gb2312( $paragraphs[$i], $break_char );
                }
                break;
            
			default:
                for ( $i = 0; $i < count( $paragraphs ); $i++ )
                    $paragraphs[$i] = wordwrap( $paragraphs[$i], $length, $break_char );
                
                break;
        }
		
        return implode( $break_char, $paragraphs );
    }

	/**
	 * @access public
	 * @static
	 */
	function wrapLines( $str, $num, $breakString = "\n", $doHardBreak = true, $doBreakHtml = false ) 
	{
		$oldstr   = &$str;
		$wrap     = &$num;
		$newstr   =  '';
		$newline  =  '';
		$oldstr  .=  "\n";
		
		do 
		{
			$tmpStrPos = strpos( $oldstr, "\n" );
			
			if ( $tmpStrPos <= $wrap ) 
			{
				/* 
				If a linebreak is encountered earlier than the wrap limit, put $i there. 
				*/
				$i = $tmpStrPos;
			} 
			else 
			{
				/* 
				Otherwise, begin at the wrap limit, and then move backwards
				until it finds a blank space where we can break the line. 
				*/
				$i = $wrap;
				while ( !ereg( "[\n\t ]", substr( $oldstr, $i, 1 ) ) && $i > 0 ) 
					$i--;
			}
			
			if ( ( $i == 0 ) && ( $doHardBreak ) ) 
			{
				$doIt = false;
				
				if ( $doBreakHtml ) 
				{
					$doIt = true;
				} 
				else 
				{
					$t    = substr( $oldstr, 0, $wrap );
					$doIt = !( ( StringUtil::startsWith( $t, '<', false ) ) || ( StringUtil::endsWith( $t, '>', false ) ) );
				}

				if ( $doIt ) 
				{
					StringUtil::insert( $oldstr, "\n", $wrap );
					$i = $wrap;
				} 
				else 
				{
					$i = strpos( $oldstr, " " );
				}
			}

			$newline = substr( $oldstr, 0, $i + 1 );
			
			if ( $i != 0 ) 
				$newline[$i] = "\n";
			
			if ( $oldstr[0] != '' ) 
				$oldstr = substr( $oldstr, $i + 1 );
				
			$newstr .= $newline;
		} while ( strlen( $oldstr ) > 0 );
		
		$newstr = substr( $newstr, 0, -1 );
		return $newstr;
	}

	/**
	 * @access public
	 * @static
	 */
	function varDump( &$param ) 
	{
		ob_start();
		var_dump( $param );		
		$ret .= ob_get_contents();
		ob_end_clean();
	}
	
    /**
     * Turns all URLs in the text into hyperlinks.
     *
     * @param string $text               The text to be transformed.
     * @param optional boolean $capital  Sometimes it's useful to generate <A>
     *                                   and </A> so you can know which tags
     *                                   you just generated.
     * @param optional string $class     The CSS class the links should be
     *                                   displayed with.
     * @return string  The linked text.
	 * @access public
	 * @static
     */
    function linkUrls( $text, $capital = false, $class = '' )
    {
        if ( $capital ) 
		{
            $a = 'A';
            $text = str_replace( '</A>', '</a>', $text ); // make sure that the original message doesn't contain any capital </A> tags, so we can assume we generated them
            $text = str_replace( '<A',   '<a',   $text ); // dito for open <A> tags
        } 
		else 
		{
            $a = 'a';
        }
		
        if ( !empty( $class ) )
            $class = ' class="' . $class . '"';
        
        return preg_replace( '|(\w+)://([^\s"<]*[\w+#?/&=])|', '<' . $a . ' href="\1://\2" target="_blank"' . $class . '>\1://\2</' . $a . '>', $text );
    }

    /**
     * Re-convert links to working hrefs, after htmlspecialchars() has been 
	 * called on the text. This is an awkward chain, but necessary to filter out
     * other HTML.
     *
     * @param  string $text             The text to convert.
     * @param  optional string $target  The link target.
     * @return string  The converted text.
     */
    function enableCapitalLinks( $text, $target = '_blank' )
    {
        $text = str_replace( '&lt;A href=&quot;', '<a class="fixed" href="', $text );
        $text = str_replace( '&quot; target=&quot;_blank&quot;&gt;', '" target="' . $target . '">', $text );
        $text = str_replace( '&quot;&gt;','">', $text );
        $text = str_replace( '&lt;/A&gt;', '</a>', $text ); // only reconvert capital /A tags - the ones we generated

        return $text;
    }

    /**
     * Replace occurences of %VAR% with VAR, if VAR exists in the
     * webserver's environment. Ignores all text after a # character
     * (shell-style comments).
     *
     * @param  string $text  The text to expand.
     * @return string  The expanded text.
	 * @access public
	 * @static
     */
    function expandEnvironment( $text )
    {
        if ( preg_match( "|([^#]*)#.*|", $text, $regs ) ) 
		{
            $text = $regs[1];

            if ( strlen( $text ) > 0 )
                $text = $text . "\n";
        }

        while ( preg_match( "|%([A-Za-z_]+)%|", $text, $regs ) )
            $text = preg_replace( "|%([A-Za-z_]+)%|", getenv( $regs[1] ), $text );
        
        return $text;
    }

    /**
     * Convert a line of text to display properly in HTML.
     *
     * @param  string $text  The string of text to convert.
     * @return string  The HTML-compliant converted text.
	 * @access public
	 * @static
     */
    function htmlSpaces( $text = '' )
    {
        $text = htmlspecialchars( $text );
        $text = str_replace( "\t",  '&nbsp; &nbsp; &nbsp; &nbsp; ', $text );
        $text = str_replace( '  ',  '&nbsp; ', $text );
        $text = str_replace( '  ', ' &nbsp;',  $text );

        return $text;
    }

    /**
     * Same as htmlSpaces() but converts all spaces to &nbsp;
     *
     * @param  string $text  The string of text to convert.
     * @return string  The HTML-compliant converted text.
	 * @access public
	 * @static
     */
    function htmlAllSpaces( $text = '' )
    {
        $text = StringUtil::htmlSpaces( $text );
        $text = str_replace( ' ', '&nbsp;', $text );

        return $text;
    }

	/**
	 * Explodes a string and trims all values for whitespace in the ends. 
	 * If $onlyNonEmptyValues is set, then all blank ("") values are removed.
	 *
	 * @access public
	 * @static
	 */
	function trimExplode( $delim, $string, $onlyNonEmptyValues = 0 )
	{
		// This explodes a comma-list into an array where the values are parsed through trim();
		$temp    = explode( $delim, $string );
		$newtemp = array();
		
		while ( list( $key, $val ) = each( $temp ) )	
		{
			if ( !$onlyNonEmptyValues || strcmp( "", trim( $val ) ) )
				$newtemp[] = trim( $val );
		}
		
		reset( $newtemp );
		return $newtemp;
	}

	/**
	 * @access public
	 * @static
	 */
	function inStr( $haystack, $needle ) 
	{
		if ( strlen( $needle ) == 0 ) 
			return false;
		
		$pos = strpos( $haystack, $needle );
		return ( $pos !== false );
	}

	/**
	 * @access public
	 * @static
	 */
	function inStrI( $haystack, $needle ) 
	{
		return ( StringUtil::inStr( strtolower( $haystack ), strtolower( $needle ) ) );
	}

	/**
	 * @access public
	 * @static
	 */
	function startsWith( $haystack, $needle, $ignoreSpaces = true ) 
	{
		if ( $ignoreSpaces ) 
			$haystack = ltrim( $haystack );
		
		if ( strlen( $haystack ) < strlen( $needle ) ) 
			return false;
		
		if ( strlen( $needle ) == 0 ) 
			return false;
		
		$haystack = ' ' . $haystack;
		$pos = strpos( $haystack, $needle );
		
		if ( $pos == 1 ) 
			return true;
		else 
			return false;
	}

	/**
	 * @access public
	 * @static
	 */
	function startsWithI( $haystack, $needle, $ignoreSpaces = true ) 
	{
		return ( StringUtil::startsWith( strtolower( $haystack ), strtolower( $needle ), $ignoreSpaces ) );
	}

	/**
	 * @access public
	 * @static
	 */
	function endsWith( $haystack, $needle, $ignoreSpaces = true ) 
	{
		if ( $ignoreSpaces ) 
			$haystack = trim( $haystack );
		
		if ( strlen( $haystack ) < strlen( $needle ) ) 
			return false;
		
		if ( strlen( $needle ) == 0 ) 
			return false;
		
		$endOfString = substr( $haystack, -strlen( $needle ) );
		
		if ( $needle == $endOfString ) 	
			return true;
		else 
			return false;
	}

	/**
	 * @access public
	 * @static
	 */
	function endsWithI( $haystack, $needle, $ignoreSpaces = true ) 
	{
		return ( StringUtil::endsWith( strtolower( $haystack ), strtolower( $needle ), $ignoreSpaces ) );
	}
	
	/**
	 * @access public
	 * @static
	 */
	function oneOf() 
	{
		$numArgs = func_num_args();
		$argList = func_get_args();
		
		if ( $numArgs > 1 ) 
		{
			mt_srand( (double)microtime() * 1000000 );
			$randVal = mt_rand( 1, $numArgs );
			$ret = $argList[$randVal - 1];
		} 
		else if ( $numArgs == 1 ) 
		{
			$ret = $argList[0];
		} 
		else 
		{
			$ret = '';
		}

		return $ret;
	}
	
	/**
	 * This function strips off certain characters from the passed string based on
	 * the type specified.
	 *
	 * @access public
	 * @static
	 */
	function clean( $str, $type ) 
	{
		switch ( $type )
		{
			case 'alpha':
				return $str;
				break;
			
			case 'num':
				return $str;
				break;
			
			case 'noalpha':
				return ( ereg_replace( "[^a-zA-Z]", '', $str ) );
				break;
			
			case 'nonum':
				return ( ereg_replace( "[^0-9]", '', $str ) );
				break;
			
			case 'noalphanum':
				return ( ereg_replace( "[^0-9a-zA-Z]", '', $str ) );
				break;
			
			case 'nohtmlentities':
				return ( ereg_replace( "&[[:alnum:]]{0,};", '', $str ) );
				break;
			
			case "notags":
 				return( strip_tags( $str ) );
   				break;
  
			case "htmlentities":
   				return( htmlentities( $str ) );
   				break;
   
			case "nospecial":
   				$str = ereg_replace( "&[[:alnum:]]{0,};", "", $str );
   				$str = htmlentities( $str);
   				$str = ereg_replace( "&[[:alnum:]]{0,};", "", $str );
   				return $str;
   				
				break;

			default:
				return $str;
		}		
	}
	
	/**
	 * Inserts a "\n"-character (return-character) for each $interval character. Used for breaking up a base64 encoded string.
	 *
	 * @param  string $inputstr
	 * @param  char $interval 	character to replace with "\n"
	 * @return string 	new string
	 * @access public
	 * @static
	 */
	function breakText( $inputstr, $interval )	
	{
		$returncode = "\n";
		return chunk_split( $inputstr, $interval, $returncode );
	}
	
    /**
     * Removes some common entities and high-ascii or otherwise
     * nonstandard characters common in text pasted from Microsoft
     * Word into a browser.
     *
     * @param  string $text  The text to be cleaned.
     * @return string  The cleaned text.
	 * @access public
	 * @static
     */
    function cleanEntities( $text )
    {
        /* The 'í' entry may look wrong, depending on your editor,
           but it's not - that's not really a single quote. */
        $from = array( 'Ö', 'ë', 'í', 'ì', 'î', 'ï', 'ñ', 'ó', 'ü', '∑', chr( 167 ), '&#61479;', '&#61572;', '&#61594;', '&#61640;', '&#61623;', '&#61607;', '&#61558;', '&#9658;' );
        $to   = array( '...', "'", "'", '"', '"', '*', '-', '-', '*', '*', '*', '.', '*', '*', '-', '-', '*', '*', '>' );

        return str_replace( $from, $to, $text );
    }

    /**
     * Turn text into HTML with varying levels of parsing.
     *
     * @access public
     *
     * @param string $input            An url-decoded string, \n-separated for
     *                                 lines.
     * @param int $parselevel
     *  STRINGUTIL_HTML_PASSTHRU        =  No action. Pass-through. Included for
     *                                     completeness.
     *  STRINGUTIL_HTML_SYNTAX          =  Allow full html, also do line-breaks,
     *                                     in-lining, syntax-parsing.
     *  STRINGUTIL_HTML_REDUCED         =  Reduced html (bold, links, etc. by syntax
     *                                     array).
     *  STRINGUTIL_HTML_MICRO           =  Micro html (only line-breaks, in-line
     *                                     linking).
     *  STRINGUTIL_HTML_NOHTML          =  No html (all stripped, only line-breaks)
     *  STRINGUTIL_HTML_NOHTML_NOBREAK  =  No html whatsoever, no line breaks added.
     *                                     Included for completeness.
     * For no html whatsoever, use htmlspecialchars()
     *
     * @return string  The converted HTML.
	 * @access public
	 * @static
     */
    function toHTML( $text, $parselevel )
    {
        $syntax = array(
			'B'     => '<b>',
			'/B'    => '</b>',
			'I'     => '<i>',
			'/I'    => '</i>',
			'U'     => '<u>',
			'/U'    => '</u>',
			'Q'     => '<blockquote>',
			'/Q'    => '</blockquote>',
			'LIST'  => '<ul>',
			'/LIST' => '</ul>',
			'*'     => '<li>'
		);

        /* Abort out on simple cases. */
        if ( $parselevel == STRINGUTIL_HTML_PASSTHRU )
            return $text;
        
        if ( $parselevel == STRINGUTIL_HTML_NOHTML_NOBREAK )
            return htmlspecialchars( $text );

        /* Tack on spaces so that we can count on whitespace coming before
           and after URLs and email addresses. */
        $text = ' ' . $text . ' ';

        /* Find tags we recognize with this parselevel and subst them to
           <tag> ==> [tag]
           and then subst the rest < --> &lt; > --> &gt; */
        if ( $parselevel == STRINGUTIL_HTML_REDUCED ) 
		{
            foreach ( $syntax as $k => $val ) 
			{
                $text = str_replace( '<' . $k . '>', '[' . $k . ']', $text );
                $k    = strtolower( $k );
                $text = str_replace( '<' . $k . '>', '[' . $k . ']', $text );
            }
			
            $input = htmlspecialchars( $input );
        }

        /* Interpret tags for parse levels STRINGUTIL_HTML_SYNTAX and
           STRINGUTIL_HTML_REDUCED. */
        if ( $parselevel <= STRINGUTIL_HTML_REDUCED ) 
		{
            foreach ( $syntax as $k => $v ) 
			{
                $text = str_replace( '[' . $k . ']', $v, $text );
                $text = str_replace( '<' . $k . '>', $v, $text );
                $k    = strtolower( $k );
                $text = str_replace( '[' . $k . ']', $v, $text );
                $text = str_replace( '<' . $k . '>', $v, $text );
            }
        }

        /* For level STRINGUTIL_HTML_MICRO, STRINGUTIL_HTML_NOHTML, start with
           htmlspecialchars(). */
        if ( $parselevel >= STRINGUTIL_HTML_MICRO )
            $text = htmlspecialchars( $text );

        /* Do in-lining of http://xxx.xxx to link, xxx@xxx.xxx to email,
           part two. */
        if ( $parselevel < STRINGUTIL_HTML_NOHTML ) 
		{
            // mailto
            $text = preg_replace( '|(\s+)([\w\.\-]+\@[\w\-]+\.[\.\w]+)([^\.\w])|', '\1<a href="mailto:\2">\2</a>\3', $text );

            // urls
            $text = preg_replace( '|(\s+)(\w+)://([^\s"<]*)([\w#?/&=])|', '\1<a href="\2://\3\4">\2://\3\4</a>', $text );
        }

        /* Do the blank-line ---> <br /> substitution.
           Everybody gets this; if you don't want even that, just save
           the htmlspecialchars() version of the input. */
        $text = nl2br( $text );

        return trim( $text );
    }

	/**
	 * Function for doing case-insensitive search and replace
	 * found on php.net under str_replace()
	 *
	 * @access public
	 * @static
	 */
	function highlight( $needle, $haystack )
	{
		$parts = explode( strtolower( $needle ), strtolower( $haystack ) );
		$pos   = 0;

		foreach( $parts as $key => $part )
		{
			$parts[ $key ] = substr( $haystack, $pos, strlen( $part ) );
			$pos += strlen( $part );

			$parts[ $key ] .= '<strong>' . substr( $haystack, $pos, strlen( $needle ) ) . '</strong>';
			$pos += strlen( $needle );
		}

		return( join( '', $parts ) );
	}
	
    /**
     * Highlights quoted messages with different colors for the different
     * quoting levels. CSS class names called "quoted1" .. "quoted$level" must
     * be present.
     *
     * @param  string  $text    The text to be highlighted.
     * @param  integer $level  The maximum numbers of different colors.
     * @return string  The highlighted text.
	 * @access public
	 * @static
     */
    function highlightQuotes( $text, $level = 5 )
    {
        $text   = implode( "\n", preg_replace( '|^(\s*&gt;.+)$|', '<span class="quoted1">\1</span>', explode( "\n", $text ) ) );
        $indent = 1;

        while ( preg_match( '|&gt;(\s?&gt;){' . $indent . '}|', $text ) ) 
		{
            $text = implode( "\n", preg_replace( '|^<span class="quoted' . ( ( ( $indent - 1 ) % $level ) + 1 ) . '">(\s*&gt;(\s?&gt;){' . $indent . '}.+)$|', '<span class="quoted' . ( ( $indent % $level ) + 1 ) . '">\1', explode( "\n", $text ) ) );
            $indent++;
        }

        return $text;
    }

    /**
     * Displays message signatures marked by a '-- ' in the style of the CSS
     * class "signature". Class names inside the signature are prefixed with
     * "signature-".
     *
     * @param  string $text  The text to be changed.
     * @return string  The changed text.
	 * @access public
	 * @static
     */
    function dimSignature( $text )
    {
        $parts = preg_split( '|(\n--\s*(<br />)?\n)|', $text, 2, PREG_SPLIT_DELIM_CAPTURE );
        $text  = array_shift( $parts );
		
        if ( count( $parts ) ) 
		{
            $text .= '<span class="signature">' . $parts[0];
            $text .= preg_replace('|class="([^"]+)"|', 'class="signature-\1"', $parts[2]);
            $text .= '</span>';
        }

        return $text;
    }
	
	/**
	 * Create text which is human readable but hard to catch for a spam filter
	 * (for use with all that "Get free Viagra" stuff).
	 *
	 * @param  string $text
	 * @return $string
	 * @access public
	 * @static
	 */
	function getAntiSpamfilterHeadline( $text, $density = 100 )
	{
		$from = "AAAAAACEEEEIIIIDNOOOOOUUUUYaaaaaaceeeeiiiinooooouuuyyg";
		$to   = "¿¡¬√ƒ≈«»… ÀÃÕŒœ–—“”‘’÷Ÿ⁄€‹›‡·‚„‰ÂÁËÈÍÎÏÌÓÔÒÚÛÙıˆ˘˙˚˝ˇ9";
		
		// Todo: handle density, maybe split string into words
		
		return strtr( $text, $from, $to );
	}
	
    /**
     * Newline2br.
     *
     * @param   string
     * @return  mixed
	 * @access  public
	 * @static
     */
    function nl2br( $string ) 
	{
        return str_replace( "\n", "<br />\n", $string );
    }

    /**
     * Cut off.
     *
     * @param   string
     * @param   integer
     * @param   string
     * @return  string
	 * @access  public
	 * @static
     */
    function cutOff( $string, $nMaxLen, $sSuffix = '...' )
	{
        if ( strlen( $string ) > $nMaxLen )
            $string = substr( $string, 0 , $nMaxLen ) . $sSuffix;
        
        return $string;
    }

    /**
     * Cut off at start.
     *
     * @param   string
     * @param   integer
     * @param   string
     * @return  string
	 * @access  public
	 * @static
     */
    function cutOffAtStart( $string, $nMaxLen, $sPrefix = '...' )
	{
        if ( strlen( $string ) > $nMaxLen )
            $string =  $sPrefix . substr( $string, -$nMaxLen );
        
        return $string;
    }

    /**
     * Cut off word.
     *
     * @param   string
     * @param   integer
     * @param   string
     * @return  string
	 * @access  public
	 * @static
     */
    function cutOffWord( $string, $nMaxLen, $sSuffix = '...' ) 
	{
        if ( strlen( $string ) > $nMaxLen ) 
		{
            $aDelimiter = array( ' ', '.', ',', ';', '!', '?', '-', ':', '_', '/' );
            $string = substr( $string, 0, $nMaxLen + 1 );
            $aPos = array();

            for ( $i = 0, $n = sizeof( $aDelimiter ); $i < $n; $i++ ) 
			{
                $nPos = strrpos( $string, $aDelimiter[$i] );
				
                if ( $nPos )
                    $aPos[] = $nPos;
            }

            if ( sizeof( $aPos ) > 0 ) 
			{
                rsort( $aPos );
                $string = substr( $string, 0, $aPos[0] );
            }

            $string .= $sSuffix;
        }
		
        return $string;
    }

	/**
	 * @access public
	 * @static
	 */
	function shortenString( $text, $length, $suffix = '...' ) 
	{
		$length_text   = strlen( $text   );
		$length_symbol = strlen( $suffix );
		
		if ( $length_text <= $length || $length_text <= $length_symbol || $length <= $length_symbol ) 
			return ( $text );
		else 
			return ( substr( $text, 0, $length - $length_symbol ) . $suffix );
	}

	/**
	 * @access public
	 * @static
	 */
	function abbreviateString( $text, $maxLength = 12 ) 
	{
		if ( $maxLength < 2 ) 
			$maxLength = 2;
		
		$words     = preg_split( '/\s+/', $text );
		$abbrWords = array();
		$sW        = sizeOf( $words );
		
		for ( $i = 0; $i < $sW; $i++ ) 
		{
			$firstChar =  $words[$i][0];
			
			if ( preg_match( '/^[A-Zƒ÷‹]/', $firstChar ) ) 
				$abbrWords[$i] = $firstChar . '.';
			else 
				$abbrWords[$i] = '';
		}

		$newWords = $words;
		
		for ( $i = $sW - 1; $i >= 0; $i-- ) 
		{
			if ( strlen( implode( ' ', $newWords ) ) > $maxLength ) 
			{
				if ( empty( $abbrWords[$i] ) ) 
					unset( $newWords[$i] );
				else 
					$newWords[$i] = $abbrWords[$i];
			} 
			else 
			{
				break;
			}
		}

		while ( strlen( implode( ' ', $newWords ) ) > $maxLength ) 
			array_pop( $newWords );

		return implode( ' ', $newWords );
	}
	
	/**
	 * Removes all \r's from a string, or all elements of an array
	 * this will work on any dimensional recursive array.
	 *
	 * @access public
	 * @static
	 */
	function stripRs( $foo )
	{
		if ( !is_array( $foo ) )
		{
			$foo = str_replace( "\r", "", $foo );
			return $foo;
		}
		else
		{
			foreach ( $foo as $k => $v )
			{
				if ( is_array( $foo[$k] ) )
					$foo[$k] = $this->stripRs( $foo[$k] );
				else
					$foo[$k] = str_replace( "\r", "", $foo[$k] );
			}
		}
		
		return $foo;
	}

	/**
	 * Removes all \n's from a string, or all elements of an array
	 * this will work on any dimensional recursive array.
	 *
	 * @access public
	 * @static
	 */
	function stripNs( $foo )
	{
		if ( !is_array( $foo ) )
		{
			$foo = str_replace( "\n", "", $foo );
			return $foo;
		}
		else
		{
			foreach ( $foo as $k => $v )
			{
				if ( is_array( $foo[$k] ) )
					$foo[$k] = $this->stripNs( $foo[$k] );
				else
					$foo[$k] = str_replace( "\n", "", $foo[$k] );
			}
		}
		
		return $foo;
	}
	
    /**
     * Clean word.
     *
     * @param   string
     * @return  mixed
	 * @access  public
	 * @static
     */
    function cleanWord( $string ) 
	{
        static $aTrans = array(
			'¿' => 'A',  '¡' => 'A',  '¬' => 'A',  '√' => 'A',  'ƒ' => 'Ae',
            '≈' => 'A',  '∆' => 'Ae', '«' => 'C',  '»' => 'E',  '…' => 'E',
            ' ' => 'E',  'À' => 'E',  'Ã' => 'I',  'Õ' => 'I',  'Œ' => 'I',
            'œ' => 'I',  '—' => 'N',  '“' => 'O',  '”' => 'O',  '‘' => 'O',
            '’' => 'O',  '÷' => 'Oe', 'ÿ' => 'O',  'Ÿ' => 'U',  '⁄' => 'U',
            '€' => 'U',  '‹' => 'Ue', '›' => 'Y',
            'ﬂ' => 'ss', '‡' => 'a',  '·' => 'a',  '‚' => 'a',  '„' => 'a',
            '‰' => 'ae', 'Â' => 'a',  'Ê' => 'ae', 'Á' => 'c',  'Ë' => 'e',
            'È' => 'e',  'Í' => 'e',  'Î' => 'e',  'Ï' => 'i',  'Ì' => 'i',
            'Ó' => 'i',  'Ô' => 'i',  'Ò' => 'n',  'Ú' => 'o',  'Û' => 'o',
            'Ù' => 'o',  'ı' => 'o',  'ˆ' => 'oe', '¯' => 'o',  '˘' => 'u',
            '˙' => 'u',  '˚' => 'u',  '¸' => 'ue', '˝' => 'y',  'ˇ' => 'y'
        );
		
        $string = strtr( $string, $aTrans );

        // Get rid of quotation mark
        $string = str_replace( "'", '', $string );

        // Mark delimiters
        $string = preg_replace(
			"#[\x20-\x2F\x3A-\x40\x5B-\x60\x7B-\x7F]#",
			"\x01",
			$string
		);

        // Get rid of special chars
        $string = preg_replace( "#[^a-zA-Z0-9\x01]#", "", $string );

        // Recover delimiters as spaces
        $string = str_replace( "\x01", " ", $string );

        // Capitalize the first character of each word
        return str_replace( " ", "", ucwords( $string ) );
    }

	/**
	 * @access public
	 * @static
	 */
	function ucWords( $string, $addChars = null ) 
	{
		if ( is_null( $addChars ) ) 
			return ucwords( $string );
			
		$defaultChars = array(
			chr( 32 ), 
			chr( 12 ), 
			chr( 10 ), 
			chr( 13 ), 
			chr(  9 ), 
			chr( 11 )
		);
		
		$chars  = array_merge( $addChars, $defaultChars );
		$chars  = array_flip( $chars );
		$length = strlen( $string );
		$string = strtoupper( substr( $string, 0, 1 ) ) . substr( $string, 1 );
		
		for ( $i = 1; $i < $length; $i++ ) 
		{
			$char = $string[$i - 1];
			
			if ( isset( $chars[$char] ) ) 
				$string = substr( $string, 0, $i ) . strtoupper( substr( $string, $i, 1 ) ) . substr( $string, $i + 1 );
		}
		
		return $string;
	}
	
    /**
     * Escape.
     *
     * @param   string
     * @return  string
	 * @access  public
	 * @static
     */
    function escape( $string )
	{
        $string = htmlspecialchars( $string );
        return $string;
    }

    /**
     * Unescape.
     *
     * @param   string
     * @return  string
	 * @access  public
	 * @static
     */
    function unescape( $string ) 
	{
        $string = strtr( $string, array_flip( get_html_translation_table( HTML_SPECIALCHARS ) ) );
        return $string;
    }

    /**
     * Escape backslashes.
     *
     * @param   string
     * @return  mixed
	 * @access  public
	 * @static
     */
    function escapeBackslashes( $string ) 
	{
        return str_replace( '\\', '\\\\', $string );
    }

    /**
     * Unescape backslashes.
     *
     * @param   string
     * @return  mixed
	 * @access  public
	 * @static
     */
    function unescapeBackslashes( $string ) 
	{
        return str_replace( '\\\\', '\\', $string );
    }

	/**
	 * @access  public
	 * @static
     */
	function remove( $p, $c )
	{
		return preg_replace( "/$c/", "", $p );
	}
	
	/**
	 * Removes the first word from a string (ie, anything up till a space ' ').
	 *
	 * @access  public
	 * @static
	 */
	function removeFirstWord( $str = "" )
	{
		return StringUtil::removeWord( $str, 0 );
	}

	/**
	 * Removes the last word from a string (ie, anything after the last space ' ').
	 *
	 * @access  public
	 * @static
	 */
	function removeLastWord( $str = "" )
	{
		return StringUtil::removeWord( $str, 1 );
	}

	/**
	 * Note: end: 0 = start of string, 1 = end of string
	 *
	 * @access  public
	 * @static
	 */
	function removeWord( $str = "", $end = 0 )
	{
		if ( $str == "" )
			return $str;
		
		if ( is_array( $str ) )
			return $str;
		
		$str = trim( $str );
		
		if ( !substr_count( $str, " " ) )
			return $str;
		
		return ( $end?
			substr( $str, 0, strrpos( $str, " " ) ) : 
			substr( $str, strpos( $str, " " ) + 1, strlen( $str ) ) );
	}
	
    /**
     * Expects warp=physical|hard.
     *
     * @param   string
     * @param   integer
     * @return  mixed
	 * @access  public
	 * @static
     */
    function quote( $string, $nLen = 78 ) 
	{
        $aTok = explode( "\n", $string );
        
        for ( $i = 0, $n = sizeof( $aTok ); $i < $n; $i++ ) 
		{
            if ( strlen( $aTok[$i] ) > $nLen ) 
			{
                // Already quoted?
                preg_match( '#^(>*)#', $aTok[$i], $aMatches );
                $aTok[$i] = '>' . $aTok[$i];
                $aTok[$i] = wordwrap( $aTok[$i], $nLen );
                $aTok[$i] = str_replace( "\n", "\n>" . $aMatches[1], $aTok[$i] );
            } 
			else 
			{
                $aTok[$i] = '>' . $aTok[$i];
            }
        }

        $string = join( "\n", $aTok );
        
        // Remove empty quoted lines
        return preg_replace( '#^>+$#m', '', $string );
    }
	
	/**
	 * Takes two names of pages, categories, directories etc
	 * and decides whether they are similar enough to be the
	 * same name.. e.g "Bit & Pieces" = "bitspieces".
	 *
	 * @access public
	 * @static
	 */
	function fuzzyNameCompare( $a, $b ) 
	{
		$a = ereg_replace( "[^a-z0-9]+", '', ereg_replace( "[^a-z0-9](and|or|of)[^a-z0-9]", '', strtolower( $a ) ) );
		$b = ereg_replace( "[^a-z0-9]+", '', ereg_replace( "[^a-z0-9](and|or|of)[^a-z0-9]", '', strtolower( $b ) ) );
	
		return $a == $b;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function map( $string, &$mapping, $function = "" )
 	{
  		for ( $mapped = "", $character = 0; $character < strlen( $string ); $character++ )
  		{
   			$code    = ord( $string[$character] );
   			$mapped .= ( isset( $mapping[$code] )? $mapping[$code] : ( strcmp( $function, "" )? $function( $string[$character] ) : $string[$character] ) );
  		}
  
  		return ( $mapped );
 	}
	
	/**
	 * @access public
	 * @static
	 */
	function stripDouble( $s )
	{
		return preg_replace( "/(htt|ft)p:\//", "\\1p://", preg_replace( '/\/{2,}/', '/', $s ) );
	}
	
	/**
	 * @access public
	 * @static
	 */
	function quotePHP( $text, $a = 1, $b = 1, $c = 0, $d = 0 )
	{
		if ( $a )
			$text = preg_replace( "/\<\?(php)/i", "<!?\\1", $text );
			
		if ( $b )
			$text = preg_replace( "/[^\!](\blanguage\b.*=.*php)/i", "!\\1", $text );
			
		if ( $c )
			$text = preg_replace( "/\<\?/", "<!?", $text );
			
		if ( $d )
			$text = preg_replace( "/\<\%/", "<!%", $text );
		
		return $text;
	}
	
	/**
	 * Removes Comments, Tabs, Spaces and CRLFs
     * Things not handeled:
	 * - mixed PHP and HTML code in one file
	 * - echo <<<EOT EOT; statements
	 *
	 * @access public
	 * @static
	 */	 
	function compactPHP( $sText )
	{
    	// search for PHP Block Begin
		$i = strpos( $sText, '<?' );
    
		if ( $i === false )
        	return PEAR::raiseError( "Invalid Buffer, need <? to find start." );
		
		$i = $i + 2;

		// search for PHP Block End
    	$iStop = strpos( $sText, '?>' );
    
		if ( $iStop === false )
        	return PEAR::raiseError( "Invalid Buffer, need ?> to find end." );

    	// Start > End?
    	if ( $i > $iStop )
        	return PEAR::raiseError( "Invalid Buffer, start > end." );

    	// copy start
    	$sBuffer = substr( $sText, 0, $i );

    	// compact and copy PHP Source Code.
    	$sChar   = '';
    	$sLast   = '';
    	$sWanted = '';
    	$fEscape = false;
		
		for ( $i = $i; $i < $iStop; $i++ )
    	{
        	$sLast = $sChar;
        	$sChar = substr( $sText, $i, 1 );

        	// \ in a string marks possible an escape sequence
        	if ( $sChar == '\\' )
			{
            	// are we in a string?
            	if ( $sWanted == '"' || $sWanted == "'" )
				{
                	// if we are not in an escape sequence, turn it on
                	// if we are in an escape sequence, turn it off
                	$fEscape = !$fEscape;
				}
			}

        	// " marks start or end of a string
        	if ( $sChar == '"' && !$fEscape )
			{
            	if ( $sWanted == '' )
				{
                	$sWanted = '"';
				}
            	else
				{
                	if ( $sWanted == '"' )
                    	$sWanted = '';
				}
			}

			// ' marks start or end of a string
        	if ( $sChar == "'" && !$fEscape )
			{
            	if ( $sWanted == '' )
				{
                	$sWanted = "'";
				}
            	else
				{
                	if ( $sWanted == "'" )
                    	$sWanted = '';
				}
			}

			// // marks start of a comment
			if ( $sChar == '/' && $sWanted == '' )
			{
            	if ( substr( $sText, $i + 1, 1 ) == '/' )
            	{
                	$sWanted = "\n";
                	$i++;
                
					continue;
            	}
			}

        	// \n marks possible end of comment
        	if ( $sChar == "\n" && $sWanted == "\n" )
        	{
            	$sWanted = '';
            	continue;
        	}

        	// /* marks start of a comment
        	if ( $sChar == '/' && $sWanted == '' )
			{
            	if ( substr( $sText, $i + 1, 1 ) == '*' )
            	{
                	$sWanted = "*/";
                	$i++;
                
					continue;
            	}
			}

			// */ marks possible end of comment
        	if ( $sChar == '*' && $sWanted == '*/' )
			{
            	if ( substr( $sText, $i + 1, 1 ) == '/' )
            	{
                	$sWanted = '';
                	$i++;
                	
					continue;
            	}
			}

        	// if we have a tab or a crlf replace it with a blank and continue if we had one recently
        	if ( ( $sChar == "\t" || $sChar == "\n" || $sChar == "\r" ) && $sWanted == '' )
        	{
            	$sChar = ' ';
            
				if ( $sLast == ' ' )
                	continue;
        	}

        	// skip blanks only if previous char was a blank or nothing
        	if ( $sChar == ' ' && ( $sLast == ' ' || $sLast == '' ) && $sWanted == '' )
            	continue;

        	// add char to buffer if we are not inside a comment
        	if ( $sWanted == '' || $sWanted == '"' || $sWanted == "'" )
            	$sBuffer .= $sChar;

        	// if we had an escape sequence and the actual char isn't the escape char, cancel escape sequence...
        	// since we are only interested in escape sequences of \' and \".
        	if ( $fEscape && $sChar != '\\' )
            	$fEscape = false;
    	}

    	// copy rest
    	$sBuffer .= substr( $sText, $iStop );

    	return( $sBuffer );
	}
	
	
	/**
	 * Takes a string, and does the reverse of the PHP 
	 * standard function htmlspecialchars().
	 *
	 * @access public
	 * @static
	 */
	function unHTMLSpecialChars( $string )
	{
		$string = preg_replace( "/&gt;/i",   ">",  $string );
		$string = preg_replace( "/&lt;/i",   "<",  $string );
		$string = preg_replace( "/&quot;/i", "\"", $string );
		$string = preg_replace( "/&amp;/i",  "&",  $string );

		return $string;
	}
	
	
	// private methods
	
	/**
	 * @access private
	 * @static
	 */
	function _lcsLength( $s1, $s2 )
	{
		$m = strlen( $s1 );
		$n = strlen( $s2 );

  		// this table will be used to compute the LCS-Length, only 128 chars per string are considered
  		$lcs_length_table = array(
			array( 128 ),
			array( 128 )
		); 
  
  		// reset the 2 cols in the table
  		for ( $i = 1; $i < $m; $i++ ) 
			$lcs_length_table[$i][0] = 0;
  
  		for ( $j = 0; $j < $n; $j++ ) 
			$lcs_length_table[0][$j] = 0;

  		for ( $i = 1; $i <= $m; $i++ ) 
		{
    		for ( $j = 1; $j <= $n; $j++ ) 
			{
      			if ( $s1[$i-1] == $s2[$j-1] )
        			$lcs_length_table[$i][$j] = $lcs_length_table[$i - 1][$j - 1] + 1;
      			else if ( $lcs_length_table[$i - 1][$j] >= $lcs_length_table[$i][$j - 1] )
        			$lcs_length_table[$i][$j] = $lcs_length_table[$i - 1][$j];
      			else
        			$lcs_length_table[$i][$j] = $lcs_length_table[$i][$j - 1];
    		}
  		}
  
  		return $lcs_length_table[$m][$n];
	}

	function _lcsFix( $s )
	{
  		$s = str_replace( " ", "", $s );
  		$s = ereg_replace( "[ÈËÍÎÀ …»]",     "e", $s );
  		$s = ereg_replace( "[‡·‚„‰Âƒ≈√¬¡¿]", "a", $s );
  		$s = ereg_replace( "[ÏÌÓÔœŒÕÃ]",     "i", $s );
  		$s = ereg_replace( "[ÚÛÙıˆ÷’‘”]",    "o", $s );
  		$s = ereg_replace( "[‹€⁄Ÿ˘˙˚¸]",     "u", $s );
  		$s = ereg_replace( "[«]",            "c", $s );
  
  		return $s;
	}
} // END OF StringUtil

?>
