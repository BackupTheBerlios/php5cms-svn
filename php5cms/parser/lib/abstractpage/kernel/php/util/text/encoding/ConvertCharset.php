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
|Authors: Mikolaj Jedrzejak <mikolajj@op.pl>                           |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * I have made this class because I had a lot of problems with diferent charsets. First because people
 * from Microsoft wanted to have thair own encoding, second because people from Macromedia didn't
 * thought about other languages, third because sometimes I need to use text written on MAC, and of course
 * it has its own encoding :)
 * 
 * Notice & remember:
 * - When I'm saying 1 byte string I mean 1 byte per char.
 * - When I'm saying multibyte string I mean more than one byte per char.
 * 
 * So, this are main FEATURES of this class:
 * - conversion between 1 byte charsets
 * - conversion from 1 byte to multi byte charset (utf-8)
 * - conversion from multibyte charset (utf-8) to 1 byte charset
 * - every conversion output can be save with numeric entities (browser charset independent - not a full truth)
 * 
 * This is a list of charsets you can operate with, the basic rule is that a char have to be in both charsets,
 * otherwise you'll get an error.
 * 
 * - WINDOWS
 * - windows-1250 - Central Europe
 * - windows-1251 - Cyrillic
 * - windows-1252 - Latin I
 * - windows-1253 - Greek
 * - windows-1254 - Turkish
 * - windows-1255 - Hebrew
 * - windows-1256 - Arabic
 * - windows-1257 - Baltic
 * - windows-1258 - Viet Nam
 * - cp874 - Thai - this file is also for DOS
 * 
 * - DOS
 * - cp437 - Latin US
 * - cp737 - Greek
 * - cp775 - BaltRim
 * - cp850 - Latin1
 * - cp852 - Latin2
 * - cp855 - Cyrylic
 * - cp857 - Turkish
 * - cp860 - Portuguese
 * - cp861 - Iceland
 * - cp862 - Hebrew
 * - cp863 - Canada
 * - cp864 - Arabic
 * - cp865 - Nordic
 * - cp866 - Cyrylic Russian (this is the one, used in IE "Cyrillic (DOS)" )
 * - cp869 - Greek2
 * 
 * - MAC (Apple)
 * - x-mac-cyrillic
 * - x-mac-greek
 * - x-mac-icelandic
 * - x-mac-ce
 * - x-mac-roman
 * 
 * - ISO (Unix/Linux)
 * - iso-8859-1
 * - iso-8859-2
 * - iso-8859-3
 * - iso-8859-4
 * - iso-8859-5
 * - iso-8859-6
 * - iso-8859-7
 * - iso-8859-8
 * - iso-8859-9
 * - iso-8859-10
 * - iso-8859-11
 * - iso-8859-12
 * - iso-8859-13
 * - iso-8859-14
 * - iso-8859-15
 * - iso-8859-16
 * 
 * - MISCELLANEOUS
 * - gsm0338 (ETSI GSM 03.38)
 * - cp037
 * - cp424
 * - cp500 
 * - cp856
 * - cp875
 * - cp1006
 * - cp1026
 * - koi8-r (Cyrillic)
 * - koi8-u (Cyrillic Ukrainian)
 * - nextstep
 * - us-ascii
 * - us-ascii-quotes
 * 
 * - DSP implementation for NeXT
 * - stdenc
 * - symbol
 * - zdingbat
 * 
 * - And specially for old Polish programs
 * - mazovia
 *
 * @link http://www.unicode.org
 * @package util_text_encoding
 */
 
class ConvertCharset extends PEAR
{
	/**
	 * This value keeps information if string contains multibyte chars.
	 * @access private
	 */
	var $_recognized_encoding;
	
	/**
	 * This value keeps information if output should be with numeric entities.
	 * @access private
	 */
	var $_entities;

	/**
	 * Path to charset tables.
	 * @access private
	 */
	var $_tables_dir = null;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function ConvertCharset( $tables_dir = null )
	{
		if ( !$tables_dir )
			$this->_tables_dir = AP_ROOT_PATH . ap_ini_get( "path_encodings", "path" );
	}
	
	
	/**
	 * Unicode encoding bytes, bits representation.
	 * Each b represents a bit that can be used to store character data.
	 * - bytes, bits, binary representation
	 * - 1,   7,  0bbbbbbb
	 * - 2,  11,  110bbbbb 10bbbbbb
	 * - 3,  16,  1110bbbb 10bbbbbb 10bbbbbb
	 * - 4,  21,  11110bbb 10bbbbbb 10bbbbbb 10bbbbbb
	 * 
	 * This function is written in a "long" way, for everyone who woluld like to analize
	 * the process of unicode encoding and understand it. All other functions like hexToUtf
	 * will be written in a "shortest" way I can write tham :) it does'n mean thay are short
	 * of course. You can chech it in hexToUtf() (link below) - very similar function.
	 * 
	 * IMPORTANT: Remember that $unicode_string input CANNOT have single byte upper half
	 * extended ASCII codes, why? Because there is a posibility that this function will eat
	 * the following char thinking its miltibyte unicode char.
	 * 
	 * @param string $unicode_string Input Unicode string (1 char can take more than 1 byte)
	 * @return string This is an input string olso with unicode chars, bus saved as entities
	 * @see hexToUtf()
	 * @access public
	 */
	function unicodeEntity( $unicode_string ) 
	{
	  	$out_string    = "";
	  	$string_lenght = strlen( $unicode_string );
		
	  	for ( $char_position = 0; $char_position < $string_lenght; $char_position++ ) 
		{
	    	$char = $unicode_string[$char_position];
	    	$ascii_char = ord( $char );

	  	 	if ( $ascii_char < 128 ) // 1 7 0bbbbbbb (127)
	   		{
		   		$out_string .= $char; 
	   		}
	   		else if ( $ascii_char >> 5 == 6 ) // 2 11 110bbbbb 10bbbbbb (2047)
	   		{
		   		$first_byte   = ( $ascii_char & 31 );

				$char_position++;

				$char         = $unicode_string[$char_position];
				$ascii_char   = ord( $char );
				$second_byte  = ( $ascii_char & 63 );
				$ascii_char   = ( $first_byte * 64 ) + $second_byte;
				$entity       = sprintf( "&#%d;", $ascii_char );
				$out_string  .= $entity;
	   		}
	   		else if ( $ascii_char >> 4  == 14 ) //3 16 1110bbbb 10bbbbbb 10bbbbbb
	   		{
				$first_byte   = ( $ascii_char & 31 );

				$char_position++;

				$char         = $unicode_string[$char_position];
				$ascii_char   = ord( $char );
				$second_byte  = ( $ascii_char & 63 );

				$char_position++;

				$char         = $unicode_string[$char_position];
				$ascii_char   = ord( $char );
				$third_byte   = ( $ascii_char & 63 );
				$ascii_char   = ( ( ( $first_byte * 64 ) + $second_byte ) * 64 ) + $third_byte;
				$entity       = sprintf( "&#%d;", $ascii_char );
				$out_string  .= $entity;
	    	}
			else if ( $ascii_char >> 3 == 30 ) // 4 21 11110bbb 10bbbbbb 10bbbbbb 10bbbbbb
			{
				$first_byte   = ( $ascii_char & 31 );

				$char_position++;

				$char         = $unicode_string[$char_position];
				$ascii_char   = ord( $char );
				$second_byte  = ( $ascii_char & 63 );

				$char_position++;

				$char         = $unicode_string[$char_position];
				$ascii_char   = ord( $char );
				$third_byte   = ( $ascii_char & 63 );

				$char_position++;

				$char         = $unicode_string[$char_position];
				$ascii_char   = ord( $char );
				$fourth_byte  = ( $ascii_char & 63 );
				$ascii_char   = ( ( ( ( ( $first_byte * 64 ) + $second_byte ) * 64 ) + $third_byte ) * 64 ) + $fourth_byte;
				$entity       = sprintf( "&#%d;", $ascii_char );
				$out_string  .= $entity;
	    	}
	  	}
	  
	  	return $out_string;
	} 
	
	/**
	 * This simple function gets unicode  char up to 4 bytes and return it as a regular char.
	 * It is very similar to  unicodeEntity function (link below). There is one difference 
	 * in returned format. This time it's a regular char(s), in most cases it will be one or two chars. 
	 * 
	 * @param string $utf_char_in_hex Hexadecimal value of a unicode char.
	 * @return string Encoded hexadecimal value as a regular char.
	 * @see unicodeEntity()
	 * @access public
	 */
	function hexToUtf( $utf_char_in_hex )
	{
		$output_char = "";
		$utf_char_in_dec = hexdec( $utf_char_in_hex );
		
		if ( $utf_char_in_dec < 128 ) 
			$output_char .= chr( $utf_char_in_dec );
    	else if ( $utf_char_in_dec < 2048 )
			$output_char .= chr( ( $utf_char_in_dec >> 6 ) + 192 ) . chr( ( $utf_char_in_dec & 63 ) + 128 );
    	else if ( $utf_char_in_dec < 65536 )
			$output_char .= chr( ( $utf_char_in_dec >> 12 ) + 224 ) . chr( ( ( $utf_char_in_dec >> 6 ) & 63 ) + 128 ) . chr( ( $utf_char_in_dec & 63 ) + 128 );
    	else if ( $utf_char_in_dec < 2097152 )
			$output_char .= chr( $utf_char_in_dec >> 18 + 240 ) . chr( ( ( $utf_char_in_dec >> 12 ) & 63 ) + 128 ) . chr( ( $utf_char_in_dec >> 6 ) & 63 + 128 ) . chr( $utf_char_in_dec & 63 + 128 );
	
		return $output_char;
	}

	/**
	 * This function creates table with two SBCS (Single Byte Character Set). Every conversion
	 * is through this table.
	 *  
	 * - The file with encoding tables have to be save in "Format A" of unicode.org charset table format! This is usualy writen in a header of every charset file.
	 * - BOTH charsets MUST be SBCS
	 * - The files with encoding tables have to be complet (Non of chars can be missing, unles you are sure you are not going to use it)
	 * 
	 * "Format A" encoding file, if you have to build it by yourself should aplly these rules:
	 * - you can comment everything with #
	 * - first column contains 1 byte chars in hex starting from 0x..
	 * - second column contains unicode equivalent in hex starting from 0x....
	 * - then every next column is optional, but in "Format A" it should contain unicode char name or/and your own comment
	 * - the columns can be splited by "spaces", "tabs", "," or any combination of these
	 * - below is an example
	 * 
	 * <code>
	 * #
	 * #	The entries are in ANSI X3.4 order.
	 * #
	 * 0x00	0x0000	#	NULL end extra comment, if needed
	 * 0x01	0x0001	#	START OF HEADING
	 * # Oh, one more thing, you can make comments inside of a rows if you like.
	 * 0x02	0x0002	#	START OF TEXT
	 * 0x03	0x0003	#	END OF TEXT
	 * next line, and so on...
	 * </code>
	 * 
	 * You can get full tables with encodings from http://www.unicode.org
	 * 
	 * @param string $first_encoding Name of first encoding and first encoding filename (thay have to be the same)
	 * @param string $second_encoding Name of second encoding and second encoding filename (thay have to be the same). Optional for building a joined table.
	 * @return array Table necessary to change one encoding to another.
	 * @access public
	 */
	function makeConvertTable( $first_encoding, $second_encoding = "" ) 
	{
		$convert_table = array();
		
		for ( $i = 0; $i < func_num_args(); $i++ )
		{
			/*
			 * Because func_*** can't be used inside of another function call
			 * we have to save it as a separate value.
			 */
			$filename = func_get_arg( $i );
			
			if ( !is_file( $this->_tables_dir . $filename ) ) 
				return PEAR::raiseError( "Cannot read file " . $this->_tables_dir . $filename );

			$file_with_enc_table = @fopen( $this->_tables_dir . $filename, "r" );
			
			if ( !$file_with_enc_table )
				return PEAR::raiseError( "Cannot open " . $this->_tables_dir . $filename );
		  
		  	while ( !feof( $file_with_enc_table ) )
			{
				/*
				 * We asume that line is not longer
				 * than 1024 which is the default value for fgets function 
				 */
		   		if ( $one_line = trim( fgets( $file_with_enc_table ) ) )
			 	{
					/*
				 	 * We don't need all comment lines. I check only for "#" sign, because
				 	 * this is a way of making comments by unicode.org in thair encoding files
				 	 * and that's where the files are from :-)
				 	 */
		   			if ( substr( $one_line, 0, 1 ) != "#" ) 
					{
						/*
					 	 * Sometimes inside the charset file the hex walues are separated by
					 	 * "space" and sometimes by "tab", the below preg_split can also be used
					 	 * to split files where separator is a ",", "\r", "\n" and "\f"
					 	 */
						$hex_value = preg_split( "/[\s,]+/", $one_line, 3 ); // We need only the first 2 values
						
						/*
						 * Sometimes char is UNDEFINED, or missing so we can't use it for convertion
						 */
						if ( substr( $hex_value[1], 0, 1) != "#" ) 
						{
							$array_key   = strtoupper( str_replace( strtolower( "0x" ), "", $hex_value[1] ) );
							$array_value = strtoupper( str_replace( strtolower( "0x" ), "", $hex_value[0] ) );
							$convert_table[func_get_arg( $i )][$array_key] = $array_value;
						}
					}
		   		}
		  	}
		}
	
		/*
	 	 * The last thing is to check if by any reason both encoding tables are not the same.
	 	 * For example, it will happen when you save the encoding table file with a wrong name
	 	 *  - of another charset. 
	 	 */
		if ( ( func_num_args() > 1 ) && 
			 ( count( $convert_table[$first_encoding] ) == count($convert_table[$second_encoding] ) ) && 
			 ( count( array_diff_assoc( $convert_table[$first_encoding], $convert_table[$second_encoding] ) ) == 0 ) )
		{ 
			return PEAR::raiseError( "Both charsets are identical." );
		}
		
		return $convert_table;
	}
	
	/**
	 * This is a basic function you are using.
	 * 
	 * @param string $string_to_change The string you want to change :)
	 * @param string $from_charset Name of $string_to_change encoding, you have to know it.
	 * @param string $to_charset Name of a charset you want to get for $string_to_change.
	 * @param boolean $turn_on_entities Set to true or 1 if you want to use numeric entities insted of regular chars.
	 * @return string Converted string in brand new encoding :)
	 * @access public
	 */
	function convert( $string_to_change, $from_charset, $to_charset, $turn_on_entities = false )
	{
		/*
		 * Check are there all variables 
		 */
		if ( $string_to_change == "" || $from_charset == "" || $to_charset == "" ) 
			return PEAR::raiseError( "Missing arguments." );
		 
		/*
		 * Now a few variables need to be set. 
		 */
		$new_string = "";
		$this->_entities = $turn_on_entities;
		
		/*
		 * For all people who like to use uppercase for charset encoding names
		 */
		$from_charset = strtolower( $from_charset );
		$to_charset   = strtolower( $to_charset );

		/*
		 * Of course you can make a conversion from one charset to the same one
		 * but I feel obligate to let you know about it. 
		 */
		if ( $from_charset == $to_charset ) 
			return PEAR::raiseError( $from_charset . " and " . $to_charset . " are identical charsets." );
			
		if ( ( $from_charset == $to_charset ) && ( $from_charset == "utf-8" ) ) 
			return PEAR::raiseError( "Impossible conversion." );
		
		/*
		 * This divison was made to prevent errors during convertion to/from utf-8 with
		 * "entities" enabled, because we need to use proper destination(to)/source(from)
		 * encoding table to write proper entities.
		 * 
		 * This is the first case. We are converting from 1byte chars...
		 */
		if ( $from_charset != "utf-8" ) 
		{
			/*
			 * Now build table with both charsets for encoding change. 
			 */
			if ( $to_charset != "utf-8" ) 
			{
				$charset_table = $this->makeConvertTable( $from_charset, $to_charset );
					
				if ( PEAR::isError( $charset_table ) )
					return $charset_table;
			}
			else
			{
				$charset_table = $this->makeConvertTable( $from_charset );
					
				if ( PEAR::isError( $charset_table ) )
					return $charset_table;
			}
			
			/*
			 * For each char in a string... 
			 */
			for ( $i = 0; $i < strlen( $string_to_change ); $i++ )
			{
				$hex_char = "";
				$unicode_hex_char = "";
				$hex_char = strtoupper( dechex( ord( $string_to_change[$i] ) ) );
				
				if ( $to_charset != "utf-8" ) 
				{
					if ( in_array( $hex_char, $charset_table[$from_charset] ) )
					{
						$unicode_hex_char  = array_search( $hex_char, $charset_table[$from_charset] );
						$unicode_hex_chars = explode( "+", $unicode_hex_char );
						
						for ( $unicode_hex_char_element = 0; $unicode_hex_char_element < count( $unicode_hex_chars ); $unicode_hex_char_element++ )
						{
						  	if ( array_key_exists( $unicode_hex_chars[$unicode_hex_char_element], $charset_table[$to_charset] ) ) 
							{
								if ($this->_entities == true) 
									$new_string .= $this->unicodeEntity( $this->hexToUtf( $unicode_hex_chars[$unicode_hex_char_element] ) );
								else
									$new_string .= chr( hexdec( $charset_table[$to_charset][$unicode_hex_chars[$unicode_hex_char_element]] ) );
							}
							else
							{
								return PEAR::raiseError( "Cannot find matching char '" . $string_to_change[$i] . "' in destination encoding table." );
							}
						}
					}
					else
					{
						return PEAR::raiseError( "Cannot find matching char '" . $string_to_change[$i] . "' in source encoding table." );
					}
				}
				else
				{
					if ( in_array( "$hex_char", $charset_table[$from_charset] ) ) 
					{
						$unicode_hex_char = array_search( $hex_char, $charset_table[$from_charset] );
						
						/*
					     * Sometimes there are two or more utf-8 chars per one regular char.
						 * Extream, example is polish old Mazovia encoding, where one char contains
						 * two lettes 007a (z) and 0142 (l slash), we need to figure out how to
						 * solve this problem.
						 * The letters are merge with "plus" sign, there can be more than two chars.
						 * In Mazowia we have 007A+0142, but sometimes it can look like this
						 * 0x007A+0x0142+0x2034 (that string means nothing, it just shows the possibility...)
					     */
						$unicode_hex_chars = explode( "+", $unicode_hex_char );
						
						for ( $unicode_hex_char_element = 0; $unicode_hex_char_element < count( $unicode_hex_chars ); $unicode_hex_char_element++ )
						{
							if ($this->_entities == true) 
								$new_string .= $this->unicodeEntity( $this->hexToUtf( $unicode_hex_chars[$unicode_hex_char_element] ) );
							else
								$new_string .= $this->hexToUtf( $unicode_hex_chars[$unicode_hex_char_element] );
						}							
					}
					else
					{
						return PEAR::raiseError( "Cannot find matching char '" . $string_to_change[$i] . "' in source encoding table." );
					}
				}					
			}
		}
		/*
		 * This is second case. We are encoding from multibyte char string. 
		 */
		else if ( $from_charset == "utf-8" )
		{
			$hex_char = "";
			$unicode_hex_char = "";
			$charset_table   = $this->makeConvertTable( $to_charset );
			
			if ( PEAR::isError( $charset_table ) )
				return $charset_table;
						
			foreach ( $charset_table[$to_charset] as $unicode_hex_char => $hex_char )
			{
				if ( $this->_entities == true ) 
					$EntitieOrChar = $this->unicodeEntity( $this->hexToUtf( $unicode_hex_char ) );
				else
					$EntitieOrChar = chr( hexdec( $hex_char ) );
				
				$string_to_change = str_replace( $this->hexToUtf( $unicode_hex_char ), $EntitieOrChar, $string_to_change );
			}
			
			$new_string = $string_to_change;
		}
	
		return $new_string;
	}
} // END OF ConvertCharset

?>
