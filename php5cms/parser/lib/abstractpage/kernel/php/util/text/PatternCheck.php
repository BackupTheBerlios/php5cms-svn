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
 * Pattern Check Class
 * A simple way to check strings against patterns.
 *
 * Usage:
 *
 * $check = new PatternCheck();
 * $check->add( "www.www.www", "." );
 * $check->add( "%.www.www", "." );
 * $check->add( "-*", "." );
 *
 * if ( ( $test = $check->test( $_SERVER['HTTP_HOST'], false, "." ) ) !== false ) 
 * {
 *   	if ( $test < 0 )
 *			echo "The server does not accept this address";
 *		else if ( $test > 0 )
 *   		echo "The address is accepted by the server";
 *		else
 *   		echo "The server uses easyTest and has accepted the address without any matching found";
 * } 
 * else 
 * {
 * 		echo "Failed to match the address";
 * }
 *
 * The validation of a pattern is made in the added order. First added is first checked.
 * If you use the Add function then the added pattern vill be tested against previously added patterns. 
 * If the new pattern is redundant then it will not be added.
 * 
 * Patterns
 * 
 * * can be matched against any string
 * % can be matched against any string that doesn't contain the delimiter used
 * ? can be matched against one character that isn't equal to the delimiter
 * 
 * patterns that starts with *.zzz.zzz match zzz.zzz
 * patterns that starts with %.zzz.zzz doesn't match zzz.zzz
 * patterns like zzz*.zzz match zzzzz.zzz and zzz.zzz.zzz
 * patterns like zzz%.zzz match zzzzz.zzz but not zzz.zzz.zzz
 * patterns like *%.zzz.zzz match zzz.zzz.zzz and zzz.zzz.zzz.zzz 
 *                                and .zzz.zzz but not zzz.zzz
 * patterns like *?.zzz.zzz match zzz.zzz.zzz and zzz.zzz.zzz.zzz 
 *                                but not .zzz.zzz and zzz.zzz
 * 
 * Cleaning
 * ========
 * ** is redundant and replaced with *
 * %% is redundant and replaced with %
 * .*.* is redundant and replaced with .*
 * *.*. is redundant and replaced with *.
 * 
 * ?* is reordered to *?
 * ?% is reordered to %?
 * %* is reordered to *%
 *
 * @package util_text
 */

class PatternCheck extends PEAR
{
	/**
	 * @access public
	 */
  	var $patterns;
  
  
	/**
	 * Constructor
	 *
	 * @access public
	 */
  	function PatternCheck() 
	{
    	$this->patterns = array();
  	}

	
	/**
	 * Test a pattern against the registered patterns in the object.
	 *
	 * @param $check      	the pattern to test
	 * @param $easyTest   	if set to false the test will return false if no matching found
	 * @param $delimiter  	the delimiter used	
	 * @return     			returns a positive number for any accepted match
     *       				returns a negative number for any rejected match
     *       				returns 0 for any any test that doesn't match anything unless 
     *       				$easyTest is set to false, then it returns false for any 
     *       				unmatched test
	 * @access public
	 */
  	function test( $check, $easyTest = true, $delimiter = "." ) 
	{
    	$result = ( $easyTest === true )? 0 : false;
    
		for ( $i = 0; $i < count( $this->patterns ); $i++ ) 
		{
      		$pattern = $this->patterns[$i];
      
	  		if ( preg_match( "/^[+-]/U", $pattern, $match ) ) 
			{
        		if ( $match[0] == "-" )
          			$sign = -1;
        		else
          			$sign = 1;
      		} 
			else 
			{
        		$sign = 1;
      		}
      
	  		$pattern = preg_replace( "/^[+-]/U", "", $pattern, 1 );
      
	  		if ( $this->check( $pattern, $check, $delimiter ) )
        		return $sign * ( $i + 1 );
    	}
    
		return $result;
  	}
    
	/**
	 * Clean up a pattern. Removes redundant information and reorders the pattern.
	 *
	 * @param 	$pattern    the pattern to clean
	 * @param 	$delimiter  the delimiter used
	 * @return    	 		a clean pattern
	 * @access  public
	 */
  	function clean( $pattern, $delimiter = "." ) 
	{
    	$check = preg_quote( "*$delimiter*$delimiter,$delimiter*$delimiter*,%*,?*,?%,**,%%,%*" );
    	$check = str_replace( ",", "|", $check );
    	
		while ( preg_match( "/$check/U", $pattern, $matches ) ) 
		{
      		switch ( $matches[0] ) 
			{
        		case "$delimiter*$delimiter*": 
          			$pattern = str_replace( "$delimiter*$delimiter*", "$delimiter*", $pattern );
          			break;
					
        		case "*$delimiter*$delimiter" : 
          			$pattern = str_replace( "*$delimiter*$delimiter", "*$delimiter", $pattern );
          			break;
        
				case "%*" : 
          			$pattern = str_replace( "%*", "*%", $pattern );
         	 		break;
        
				case "?*" : 
          			$pattern = str_replace( "?*", "*?", $pattern );
          			break;
					
        		case "?%" : 
          			$pattern = str_replace( "?%", "%?", $pattern );
          			break;
        
				case "**" : 
          			$pattern = str_replace( "**", "*",  $pattern );
          			break;
        
				case "%%" : 
          			$pattern = str_replace( "%%", "%",  $pattern );
          			break;
        
				case "%*" : 
          			$pattern = str_replace( "%*", "*%", $pattern );
          			break;
        
				default :
					return PEAR::raiseError( serialize( $matches ) . " " . $pattern );
      		}
    	}
    
		return $pattern;
  	}
  
 	/**
 	 * Check $pattern2 can be matched against $pattern1. Any wildcard in $pattern2 
	 * will be ignored.
	 *
	 * @access public
 	 */
  	function check( $p1, $p2, $delimiter = "." ) 
	{
    	$p1 = preg_replace( "/^[+-]/U", "", $p1, 1 );
    	$p2 = preg_replace( "/^[+-]/U", "", $p2, 1 );
    
		if ( preg_match_all( "/(^\*\.)|[\*%\?]/U", $p1, $p1keys, PREG_PATTERN_ORDER ) ) 
		{
      		$p1 = str_replace( ".", "\.",  $p1 );
      		$p1 = str_replace( "?", "(.)", $p1 );
      		$p1 = preg_replace( "/^\*\\\.|\*/U", "(.*?)", $p1 );
      		$p1 = str_replace( "%", "(.*?)", $p1 );
      
	  		if ( preg_match_all( "/$p1/U", $p2, $matches, PREG_PATTERN_ORDER ) ) 
			{
        		if ( count( $p1keys[0] ) + 1 == count( $matches ) ) 
				{
          			for ( $i = 0; $i < count( $p1keys ); $i++ ) 
					{
            			switch ( $p1keys[0][$i] ) 
						{
              				case "*": 
                				break;
              
			  				case "*$delimiter":
                				if ( preg_match( "/^(?:.*)" . preg_quote( $delimiter ) . "(.*?)$/U", $matches[$i + 1][0], $m ) ) 
								{
                  					if ( preg_match( "/^(?:.*)" . preg_quote( $delimiter ) . "(.*?)$/U", $m[1], $z ) ) 
									{
                    					if ( strlen( $z[1] ) > 0 )
                      						return false;
                  					}
                				}
                
								break;
              
			  				case "%":
               	 				if ( preg_match( "/" . preg_quote( $delimiter ) . "/U", $matches[$i + 1][0],$m ) )
                  					return false;
                
                				break;
              
			  				case "?":
                				if ( $matches[$i + 1][0] == $delimiter )
                  					return false;
                				else if ( strlen( $matches[$i + 1][0] ) === 0 )
                  					return false;
                
                				break;
              
			  				case "":
                				if ( strlen( $matches[$i + 1][0] ) != 0 ) 
									return false;
                
                				break;
              
			  				default:
                				echo "[" . $p1keys[0][$i] . " / " . $matches[$i + 1][0] . " Argyle]";
            			}
          			}
        		} 
				else 
				{
          			return false;
        		}
        
				return true;
      		} 
			else 
			{
        		return false;
      		}
    	} 
		else 
		{
      		if ( $p1 == $p2 )
        		return true;
      		else
        		return false;
      	}
 	}
  
  	/**
 	 * Set a pre-defined array of patterns.
	 *
	 * @access public
  	 */
  	function setPatternArray( $patterns ) 
	{
    	$this->patterns = $patterns;
  	}
  
  	/**
 	 * Set a string of patterns.
	 *
	 * @access public
  	 */
  	function setPatternString( $patterns, $delimiter = ";" ) 
	{
    	$delimiter = preg_quote( $delimiter );
    	$this->patterns = split( $delimiter, $patterns );
  	}
  
  	/**
	 * Clear the internal pattern array.
	 *
	 * @access public
  	 */
  	function clearPatterns() 
	{
    	unset( $this->patterns );
    	$this->patterns = array();
  	}
  
  	/**
 	 * Add a pattern to the internal array. All patterns that are added will be cleaned
	 * and tested for redundancy.
	 *
	 * @access public
  	 */
  	function add( $pattern, $delimiter = "." ) 
	{
    	$pattern = $this->clean( $pattern, $delimiter );
    
		foreach ( $this->patterns as $p ) 
		{
      		if ( $this->check( $p, $pattern, $delimiter ) )
        		return false;
    	}
    
		$this->patterns[] = $pattern;
    	return true;
  	}
} // END OF PatternCheck

?>
