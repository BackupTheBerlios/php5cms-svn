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
 * Unicode Class
 * Be sure you use charset=utf-8 in your html header if you use this.
 *
 * @package util_text_encoding
 */

class UTF8 extends PEAR
{
	/** 
	 * loaded charset mappings. You can obtain them at ftp://ftp.unicode.org/Public/MAPPINGS/
	 *
	 * @access public
	 */
	var $map;
	
	
	/**
	 * Load table with mapping into array for later use.
	 * Pass alias to cp2utf function.
	 *
	 * @access public
	 */
	function loadmap( $filename, $alias )
	{
 		$f = fopen( $filename, 'r' );
		
		if ( $f )
		{
  			while ( !feof( $f ) )
			{
   				if ( $s = chop( fgets( $f, 1023 ) ) )
				{
    				list( $x, $a, $b ) = split( '0x', $s );
    				$a = hexdec( substr( $a, 0, 2 ) );
    				$b = hexdec( substr( $b, 0, 4 ) );
					
					if ( $a && $b )
						$this->map[$alias][$a] = $b;
   				}
  			}
			
			return true;
 		}
		else
		{
			return false;
		}
	}

	/**
	 * Translate string ($str) to UTF-8 from given charset ($xcp)
	 * if charset is not present, ISO-8859-1 will be used.
	 *
	 * @access public
	 */
	function cp2utf( $str, $alias = '' )
	{
		if ( $alias == '' )
		{
   			for ( $x = 0; $x < strlen( $str ); $x++ )
				$xstr .= $this->code2utf( ord( substr( $str, $x, 1 ) ) );
   
   			return $xstr;
  		}
  
  		for ( $x = 0; $x < strlen( $str ); $x++ )
   			$xstr .= $this->code2utf( $this->map[$alias][ord( substr( $str, $x, 1 ) )] );
  
  		return $xstr;
 	}

	/**
	 * Translate numeric code of UTF-8 character code to corresponding
	 * character sequence. Refer to www.unicode.org for info.
	 *
	 * @access public
	 */
	function code2utf( $num )
	{
		if ( $num < 128 )
			return chr( $num ); // ASCII
  
  		if ( $num < 1024 )
			return chr( ( $num >> 6 ) + 192 ). chr( ( $num & 63 ) + 128 );
  
  		if ( $num < 32768 ) 
			return chr( ( $num >> 12 ) + 240 ) . chr( ( ($num >> 6 ) & 63 ) + 128 ) . chr( ( $num & 63 ) + 128 );
			
  		if ( $num < 2097152 )
			return chr( $num >> 18 + 240 ) . chr( ( ( $num >> 12 ) & 63 ) + 128 ) . chr( ( $num >> 6 ) & 63 + 128 ) . chr( $num & 63 + 128 );

		return '';
	}
	
	/** 
	 * Takes a string of utf-8 encoded characters and converts it to a string of unicode entities 
	 * each unicode entitiy has the either the form &#nnnnn; or &#nnn; n={0..9} and
	 * can be displayed by utf-8 supporting browsers. 
	 * If the character passed maps as lower ascii it stays as such (a single char)
	 * instead of being presented as a unicode entity
	 *
	 * @param  $source string encoded using utf-8 [STRING] 
	 * @return string of unicode entities [STRING] 
	 * @access public
	 * @static
	 */
	function utf8ToUnicodeEntities( $source )
	{
		// array used to figure what number to decrement from character order value 
		// according to number of characters used to map unicode to ascii by utf-8 
		$decrement[4] = 240; 
		$decrement[3] = 224; 
		$decrement[2] = 192; 
		$decrement[1] = 0; 
     
		// the number of bits to shift each charNum by 
		$shift[1][0] = 0; 
		$shift[2][0] = 6; 
		$shift[2][1] = 0; 
		$shift[3][0] = 12; 
		$shift[3][1] = 6; 
		$shift[3][2] = 0; 
		$shift[4][0] = 18; 
		$shift[4][1] = 12; 
		$shift[4][2] = 6; 
		$shift[4][3] = 0; 
     
		$pos = 0; 
		$len = strlen( $source ); 
		$encodedString = ''; 
		
		while ( $pos < $len )
		{ 
        	$asciiPos = ord( substr( $source, $pos, 1 ) ); 
			
			if ( ( $asciiPos >= 240 ) && ( $asciiPos <= 255 ) )
			{ 
            	// 4 chars representing one unicode character 
            	$thisLetter = substr( $source, $pos, 4 ); 
            	$pos += 4; 
        	} 
        	else if ( ( $asciiPos >= 224 ) && ( $asciiPos <= 239 ) )
			{ 
            	// 3 chars representing one unicode character 
            	$thisLetter = substr( $source, $pos, 3 ); 
            	$pos += 3; 
			} 
			else if ( ( $asciiPos >= 192 ) && ( $asciiPos <= 223 ) )
			{ 
            	// 2 chars representing one unicode character 
            	$thisLetter = substr( $source, $pos, 2 ); 
            	$pos += 2; 
        	} 
        	else
			{ 
            	// 1 char (lower ascii) 
            	$thisLetter = substr( $source, $pos, 1 ); 
            	$pos += 1; 
        	} 

			$thisLen = strlen( $thisLetter );
			 
        	if ( $thisLen > 1 )
			{ 
            	// process the string representing the letter to a unicode entity 
            	$thisPos = 0; 
            	$decimalCode = 0; 
            
				while ( $thisPos < $thisLen )
				{ 
					$thisCharOrd = ord( substr( $thisLetter, $thisPos, 1 ) ); 
                	
					if ( $thisPos == 0 )
					{ 
                    	$charNum = intval( $thisCharOrd - $decrement[$thisLen] ); 
                    	$decimalCode += ( $charNum << $shift[$thisLen][$thisPos] ); 
                	} 
                	else
					{ 
                    	$charNum = intval( $thisCharOrd - 128 ); 
                    	$decimalCode += ( $charNum << $shift[$thisLen][$thisPos] ); 
                	} 

                	$thisPos++; 
            	} 

            	if ( $thisLen == 1 ) 
                	$encodedLetter = "&#" . str_pad( $decimalCode, 3, "0", STR_PAD_LEFT ) . ';'; 
            	else 
                	$encodedLetter = "&#" . str_pad( $decimalCode, 5, "0", STR_PAD_LEFT ) . ';'; 

            	$encodedString .= $encodedLetter; 
			} 
			else
			{ 
            	$encodedString .= $thisLetter; 
			} 
		} 
		
		return $encodedString; 
	}
	
	/** 
	 * Takes a string of unicode entities and converts it to a utf-8 encoded string 
	 * each unicode entitiy has the form &#nnnnn; n={0..9} and can be displayed by utf-8 supporting 
	 * browsers.  Ascii will not be modified. 
	 * <br>UTF-8 encoding: 
	 * <br> bytes    bits    representation 
	 * <br> 1        7       0bbbbbbb 
	 * <br> 2        11      110bbbbb 10bbbbbb 
	 * <br> 3        16      1110bbbb 10bbbbbb 10bbbbbb 
	 * <br> 4        21      11110bbb 10bbbbbb 10bbbbbb 10bbbbbb
	 *
	 * @param  $source string of unicode entities [STRING] 
	 * @return a utf-8 encoded string [STRING] 
	 * @access public
	 * @static
	 */ 
	function utf8Encode( $source )
	{ 
    	$utf8Str = ''; 
    	$entityArray = explode( "&#", $source ); 
    	$size = count( $entityArray ); 
    
		for ( $i = 0; $i < $size; $i++ )
		{ 
        	$subStr = $entityArray[$i]; 
        	$nonEntity = strstr( $subStr, ';' ); 
        
			if ( $nonEntity !== false )
			{ 
            	$unicode = intval( substr( $subStr, 0, ( strpos( $subStr, ';' ) + 1 ) ) ); 
            
				// determine how many chars are needed to reprsent this unicode character 
            	if ( $unicode < 128 )
				{ 
                	$utf8Substring = chr( $unicode ); 
            	} 
           	 	else if ( $unicode >= 128 && $unicode < 2048 )
				{ 
                	$binVal   = str_pad( decbin( $unicode ), 11, "0", STR_PAD_LEFT ); 
                	$binPart1 = substr( $binVal, 0, 5 ); 
                	$binPart2 = substr( $binVal, 5 ); 
					$char1 = chr (192 + bindec ($binPart1)); 
                	$char2 = chr (128 + bindec ($binPart2)); 
					$utf8Substring = $char1 . $char2; 
				} 
				else if ( $unicode >= 2048 && $unicode < 65536 )
				{ 
                	$binVal = str_pad( decbin( $unicode ), 16, "0", STR_PAD_LEFT ); 
                	$binPart1 = substr( $binVal, 0, 4 ); 
                	$binPart2 = substr( $binVal, 4, 6 ); 
                	$binPart3 = substr( $binVal, 10 ); 
                	$char1 = chr( 224 + bindec( $binPart1 ) ); 
                	$char2 = chr( 128 + bindec( $binPart2 ) ); 
                	$char3 = chr( 128 + bindec( $binPart3 ) ); 
					$utf8Substring = $char1 . $char2 . $char3; 
            	} 
				else
				{ 
                	$binVal = str_pad( decbin( $unicode ), 21, "0", STR_PAD_LEFT ); 
                	$binPart1 = substr( $binVal, 0, 3 ); 
                	$binPart2 = substr( $binVal, 3, 6 ); 
                	$binPart3 = substr( $binVal, 9, 6 ); 
                	$binPart4 = substr( $binVal, 15 ); 
                	$char1 = chr( 240 + bindec( $binPart1 ) ); 
                	$char2 = chr( 128 + bindec( $binPart2 ) ); 
                	$char3 = chr( 128 + bindec( $binPart3 ) ); 
                	$char4 = chr( 128 + bindec( $binPart4 ) ); 
                	$utf8Substring = $char1 . $char2 . $char3 . $char4; 
            	} 
             
				if ( strlen( $nonEntity ) > 1 ) 
					$nonEntity = substr( $nonEntity, 1 ); // chop the first char (';') 
				else  
					$nonEntity = ''; 

				$utf8Str .= $utf8Substring . $nonEntity; 
			} 
			else
			{ 
            	$utf8Str .= $subStr; 
        	} 
    	} 

		return $utf8Str; 
	}
	
	/** 
	 * RFC1738 compliant replacement to PHP's rawurldecode - which actually works with unicode (using utf-8 encoding) 
	 *
	 * @param  $source [STRING] 
	 * @return unicode safe rawurldecoded string [STRING] 
	 * @access public 
	 * @static
	 */ 
	function utf8RawUrlDecode( $source )
	{ 
    	$decodedStr = ''; 
    	$pos = 0; 
    	$len = strlen( $source ); 

    	while ( $pos < $len )
		{ 
        	$charAt = substr( $source, $pos, 1 );
			 
        	if ( $charAt == '%' )
			{ 
            	$pos++; 
            	$charAt = substr( $source, $pos, 1 );
				 
				if ( $charAt == 'u' )
				{ 
                	// we got a unicode character 
                	$pos++; 
                	$unicodeHexVal = substr( $source, $pos, 4 ); 
                	$unicode = hexdec( $unicodeHexVal ); 
                	$entity = "&#". $unicode . ';'; 
                	$decodedStr .= UTF8::utf8Encode( $entity ); 
                	$pos += 4; 
				} 
				else
				{ 
                	// we have an escaped ascii character 
                	$hexVal = substr( $source, $pos, 2 ); 
                	$decodedStr .= chr( hexdec( $hexVal ) ); 
                	$pos += 2; 
            	} 
        	} 
        	else
			{ 
            	$decodedStr .= $charAt; 
            	$pos++; 
        	} 
    	} 

    	return $decodedStr; 
	}
} // END OF UTF8

?>
