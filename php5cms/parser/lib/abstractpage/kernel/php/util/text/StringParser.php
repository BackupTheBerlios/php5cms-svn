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
 * The StringParser can be used to split up a string into it's elements.
 * The StringParser uses an array of whitespace chars ($whitespaceChars)
 * to split up the string. Escape chars, quotes and special elements 
 * (elements which aren't separeted trought whitespaces) are handled.
 * For more infos see the comments of the Member Variables and functions.
 * 
 * HOWTO use the StringParser (Step 2 is optional, you may also use the default parsing-config):
 * 1. Get an instance: $sp=new StringParser();
 * 2. Set the parsing-config: $sp->setConfig(array("'","\"") ,"\\", array(" ") ,array(),false,true);
 * 3. Set the string: $sp->setString("parse me");
 * 4. Parse the string with one of the functions, for example parseNextElement()
 * NOTE: always call setString() or restart after setConfig, or the new specialElements wont be used!
 * The other vars of the parsing-config can be changed on the fly.
 * NOTE: the special elements are always case insensitive, and made to UPPERCASE before compared
 *
 * @package util_text
 */
 
class StringParser extends PEAR
{
	/**
	 * array of 1 char strings with quote chars
	 * @access public
	 */
	var $quoteChars = array();
	
	/**
	 * 1 char string whith the escape char
	 * @access public
	 */
	var $escapeChar = "";
	
	/**
	 * array of 1 char strings with whitespace chars
	 * whitespaces do split up a String into single Elements
	 * @access public
	 */
	var $whitespaceChars = array();

	/**
	 * array of strings with elements which have to be parsed but are
	 * not in all cases separated trough whitespaces. for example . or > in SQL.
	 * The order of the elements is critical, for example add <= before <, 
	 * because else if a <= is in a string, it will be matched as <
	 * ONLY set specialElements if you use the functions parse/peek/skipNextElement()!!
	 * @access public
	 */	
	var $specialElements = array();

	/**
	 * @access public
	 */	                                
    var $removeQuotes = false;
	
	/**
	 * @access public
	 */
    var $removeEscapeChars = true;

	/**
	 * current parsing position (index into workingStr)
	 * @access public
	 */    
    var $currentPos = -1;

	/**
	 * current char
	 * @access public
	 */
    var $currentChar = "";

	/**
	 * in which Quotes are we?
	 * @access public
	 */
    var $inQuotes = array();

	/**
	 * the last parsed char was an escape char
	 * @access public
	 */
    var $lastWasEscape = false;

	/**
	 * the current char is an escape char
	 * @access public
	 */
    var $currentIsEscape = false;

	/**
	 * the current element
	 * @access public
	 */
    var $currentElement = "";

	/**
	 * contains currentElement a finished element?
	 * @access public
	 */
    var $elementFinished = false;

	/**
	 * @access public
	 */                   
    var $originalStr;

	/**
	 * @access public
	 */
    var $workingStr;

	/**
	 * @access public
	 */	
    var $specialElementsMaxLen = 0;
	
	/**
	 * @access public
	 */
    var $peekCache = "";
    

	/**
	 * Constructor
	 *
	 * @access public
	 */
	function StringParser()
	{
	    // default config
	    $this->quoteChars      = array( "'", "\"" );
	    $this->escapeChar      = "\\";
	    $this->whitespaceChars = array( " ", "\n", "\r", "\t" ); 
	    $this->specialElements = array();
	    $this->peekCache       = "";
	}
	
	
	/**
	 * Set's the current parsing configuration
     * ATTENTION: always call setConfig() before setString() !!!
	 *
	 * @access public
	 */
    function setConfig( $arrQuoteChars = array( "'", "\"" ), $escapeChar = "\\", $arrWhiteSpaceChars = array( " " ), $arrSpecialElements = array(), $removeQuotes = false, $removeEscapeChars = true )
	{
		$this->quoteChars        = $arrQuoteChars;
        $this->escapeChar        = $escapeChar;
        $this->whitespaceChars   = $arrWhiteSpaceChars;
        $this->specialElements   = $arrSpecialElements;
        $this->removeQuotes      = $removeQuotes;
        $this->removeEscapeChars = $removeEscapeChars;
	}
	
	/**
	 * Set's the String which has to be parsed
	 * If you want another parsing-config then the default config, call
	 * setConfig() before setString(), or the specialElements won't be 
	 * correctly handled.
	 *
	 * @access public
	 */
    function setString( $str ) 
	{
        $this->originalStr = $str;
        $this->restart();
    }
    
    /**   
     * (Re)starts parsing, $workingStr is replaced with $originalStr	
     * calculates specialElementsMaxLen and reset's the parseNextChar()
     * state vars.
	 *
	 * @access public
	 */
    function restart()
	{
        $this->workingStr = $this->originalStr; 
        
        // calc specialElementsMaxLen
        $this->specialElementsMaxLen = 0;
		
        for ( $i = 0; $i < count( $this->specialElements ); ++$i )
		{
            $len = strlen( $this->specialElements[$i] );
			
            if ( $len > $this->specialElementsMaxLen )
                $this->specialElementsMaxLen = $len;
        }
  
        // reset parseNextChar() state vars
        $this->currentPos      = -1;
        $this->currentChar     = "";
        $this->inQuotes        = StringParser::create_array_fill( count( $this->quoteChars ), 0 );
        $this->lastWasEscape   = false;
        $this->currentIsEscape = false;
        $this->currentElement  = "";
        $this->elementFinished = false;
        $this->peekCache       = "";
    }
    
	/**
	 * Parses the next char and appends it to $this->currentElement and updates the 
	 * parseNextChar() state vars.
	 *
	 * Returns false if the end of the string is reached, if all went ok true is returned
	 * this function is a helper for all other parsing functions.
	 * use it directly ONLY if you have NO specialElements defined!
	 *
	 * @access public
	 */
	function parseNextChar()
	{   
        if ( !( ++$this->currentPos < strlen( $this->workingStr ) ) )
	        return false;
	        
        $this->currentChar = $this->workingStr{$this->currentPos};
        $c = $this->currentChar;

		// update escape char tracking vars
		if ( $this->currentIsEscape ) 
		{
		    $this->lastWasEscape   = true;
		    $this->currentIsEscape = false;
		} 
		else 
		{
		    $this->lastWasEscape   = false;
		    $this->currentIsEscape = false;
		}
		
		// escape char:
		if ( $c == $this->escapeChar ) 
		{
		    // last was escape: 2 escape chars => the char is used, and the escapement meaning is lost
		    if ( $this->lastWasEscape ) 
			{
		        $this->currentIsEscape  = false;
		        $this->lastWasEscape    = false;
		        $this->currentElement  .= $c;
		    }  
			// last was not escape, so the current has escape meaning
			else 
			{
		        $this->currentIsEscape = true;
				
		        // add only if we don't remove escape chars
		        if ( !$this->removeEscapeChars )
			        $this->currentElement .= $c;
            }
			
		    return true;
		}
		
		// handle quote chars (only if the last was no escape char)
		if ( !$this->lastWasEscape ) 
		{
		    for ( $j = 0; $j < count( $this->quoteChars ); ++$j ) 
			{
			    if ( $c == $this->quoteChars[$j] ) 
				{    
		            // are we in this quotes OR not in other quotes => swap quote var
		            if ( $this->inQuotes[$j] || is_false( in_array( 1, $this->inQuotes ) ) ) 
					{
		                $this->inQuotes[$j] = !$this->inQuotes[$j];
		                
						// add only if $this->removeQuotes isn't set
		                if ( !$this->removeQuotes )
		                    $this->currentElement .= $c;
		            }
					// else ignore the quotes meaning, but add it anyway 
					else 
					{
		                $this->currentElement .= $c;
		            }
					
		            return true; 			   
		        }
            }
        }
        
        // handle whitespace chars (if we are not in quotes)
        if ( is_false( in_array( 1, $this->inQuotes ) ) ) 
		{
            for ( $j = 0; $j < count( $this->whitespaceChars ); ++$j ) 
			{
                if ( $c == $this->whitespaceChars[$j] ) 
				{
                    // whitespace found, return element if the strlen() is > 0
                    if ( strlen( $this->currentElement ) > 0 ) 
					{
                       // ++$this->currentPos; // skip the whitespace
                       // break all for's an return $element:
                       // break 2;
                       $this->elementFinished = true;
                       return true;
                    }
					
                    // ignore the whitespace => continue
                    return true;
              	}
            }
        } 
        
        // search for specialElements, but only if we are not in quotes
        if ( is_false( in_array( 1, $this->inQuotes ) ) ) 
		{
            $testStr = substr( $this->workingStr, $this->currentPos, $this->specialElementsMaxLen );
            
            if ( !is_false( $specialElem = StringParser::array_search_stri_start( $testStr, $this->specialElements ) ) ) 
			{
                // specialElement found!
                // strlen(element)>0 ? return current element 
                if ( strlen( $this->currentElement ) > 0 ) 
				{
                    $this->elementFinished = true;
                    --$this->currentPos;
					
                    return true;
			    }
				// make the specialElement the current element and return it 
				else 
				{
					$this->currentElement = $specialElem;
					$this->currentPos += strlen( $specialElem );
					--$this->currentPos;
				    $this->elementFinished = true;
					
				    return true;
			    }
            }
        }
            
        // none of the previous tests matches, add the current char to the element
        $this->currentElement.=$c;
        return true;        
    }
			
    /**
	 * Returns the next Element and remove's it.
	 *
	 * @access public
	 */
    function parseNextElement()
	{               
        if ( !is_empty_str( $this->peekCache ) ) 
		{
        	$tmp = $this->peekCache;
        	$this->peekCache = "";
			
        	return $tmp;
        }
        
        $this->currentElement = "";
		
        while ( $this->parseNextChar() && !$this->elementFinished )
		{    
        }

        // remove
		$this->workingStr = substr( $this->workingStr, $this->currentPos );
		$this->currentPos = 0;
		$this->elementFinished = false;
		
		return $this->currentElement;
    }
    
    /**
	 * Returns the next Element but doesn't remove it.
	 *
	 * @access public
	 */
    function peekNextElement()
	{
    	if ( !is_empty_str( $this->peekCache ) ) 
			return $this->peekCache;
    	
    	$this->peekCache = $this->parseNextElement();
        return $this->peekCache;
    }
    
    /**
	 * Skips the next Element.
	 *
	 * @access public
	 */
    function skipNextElement()
	{
    	if ( !is_empty_str( $this->peekCache ) ) 
		{
    		$this->peekCache = "";
    		return;
    	}
		
        $this->parseNextElement();
    }
    
    /**
	 * Parses the next Elements until $separatorElement or one of the $finishElements it found.
     * The parsed values are returned in the array $arrParsedElements
     * Returns true if elements were parsed, else false is returned.
	 *
	 * @access public
	 */
    function parseNextElements( $separatorElement, $finishElements, &$arrParsedElements ) 
	{
    	$arrParsedElements = array();
		
    	while ( $elem = $this->peekNextElement() )
		{	
    		if ( strtoupper( $elem ) == strtoupper( $separatorElement ) ) 
			{
    			$this->skipNextElement();
    			break;
    		}
			
    		for ( $i = 0; $i < count( $finishElements ); ++$i ) 
			{
    			if ( strtoupper( $elem ) == strtoupper( $finishElements[$i] ) )
    				break 2;
    		}
			
    		$arrParsedElements[] = $elem;
    		$this->skipNextElement();
    	}
		
    	if ( count( $arrParsedElements ) > 0 )
    		return true;
			
    	return false;
    }

    /**
	 * Returns a String where $searchChar is replaced with replaceStr,
     * expect places where $searchChar is escaped.
     * Make sure that NO specialElements are set, else this function will not work correctly.
	 *
	 * @access public
	 */
    function replaceCharWithStr( $searchChar, $replaceStr ) 
	{
        $this->restart();
        $resStr = "";
		
        while ( $this->parseNextChar() ) 
		{
            $c = $this->currentChar;
            
			if ( $searchChar == $c && !$this->lastWasEscape )
                $resStr .= $replaceStr;
            else
                $resStr .= $c;
        }
		
        return $resStr;     
    }

	/**
	 * Returns an array with the splitted strings.
     * The specialElements are used to split the string up.
     * You may also define quoteChars and escapeChar so no specielElements 
     * in Quotes are used. But do NOT set any whitespaceChars!
	 *
	 * @access public
	 */
    function splitWithSpecialElements()
	{
        $arr = array();
        $arrPos = 0;
 
        while ( $elem = $this->parseNextElement() )
		{
            $isSpecial = false;
          
            for ( $i = 0; $i < count( $this->specialElements ); ++$i ) 
			{
                if ( strtoupper( $elem ) == strtoupper( $this->specialElements[$i] ) ) 
				{
                    $isSpecial = true;
                    break;    
                }
            }
			
            if ( $isSpecial ) 
			{
                $arr[++$arrPos] = "";      
            }
			else 
			{
            	if ( !isset( $arr[$arrPos] ) ) // pos 0 bugfix...
            		$arr[$arrPos] = "";
            		
                $arr[$arrPos] .= $elem;  
            }
        }    
		    
        return $arr;
    }
    
    /**
     * same as splitWithSpecialElements, but you have to pass an array
     * as parameter, which is filled with the specialElements which
     * where found in the string and used to split (all are in UPPERCASES).
	 *
	 * @access public
	 */
    function splitWithSpecialElementsRetUsed( &$arrUsedSpecElements ) 
	{
        $arr = array();
        $arrPos = 0;
 
        while ( $elem = $this->parseNextElement() ) 
		{
            $isSpecial = false;
            
            for ( $i = 0; $i < count( $this->specialElements ); ++$i ) 
			{
                if ( strtoupper( $elem ) == strtoupper( $this->specialElements[$i] ) ) 
				{
                    $isSpecial = true;
					
                    if ( !in_array( strtoupper( $this->specialElements[$i] ), $arrUsedSpecElements ) )
                        $arrUsedSpecElements[] = strtoupper( $this->specialElements[$i] );
                    
                    break;    
                }
            }
			
            if ( $isSpecial ) 
			{
                $arr[++$arrPos] = "";      
            } 
			else 
			{
            	if ( !isset( $arr[$arrPos] ) ) // pos 0 bugfix...
            		$arr[$arrPos] = "";
					
                $arr[$arrPos] .= $elem;  
            }
        }
		    
        return $arr;
    }
	
	
	// helper functions

	/**
	 * @access public
	 */
	function create_array_fill( $size, $value ) 
	{
		$arr = array();
	
		for ( $i = 0; $i < $size; ++$i )
			$arr[] = $value;
	
		return $arr;
	}

	/**
	 * Searches the first n chars of $string in $array
	 * where n is the length of reach $array element
	 * returns the value of $array if found or false.
	 *
	 * @access public
	 */
	function array_search_str_start( $string, $array ) 
	{
		for ( $i = 0; $i < count( $array ); ++$i ) 
		{
			if ( strncmp( $array[$i], $string, strlen( $array[$i] ) ) == 0 )
				return $array[$i];
		}
	
		return false;
	}

	/**
	 * @access public
	 */
	function array_search_stri_start( $string, $array ) 
	{
    	for ( $i = 0; $i < count( $array ); ++$i ) 
		{
			if ( strncmp( strtoupper( $array[$i] ), strtoupper( $string ), strlen( $array[$i] ) ) == 0 )
				return $array[$i];
		}
	
		return false;
	}

	/**
	 * @access public
	 */
	function array_walk_trim( &$value, &$key ) 
	{
		$value = trim( $value );
	}
} // END OF StringParser
		
?>
