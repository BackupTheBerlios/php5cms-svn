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
 * @package util_text
 */
 
class EightBitString extends PEAR
{
 	/**
	 * This function returns an array that includes the ascii codes for
	 * all 8-bit chars and their replacement 7-bit ascii codes.  It's used to set 
	 * the constants EIGHT_BIT_STRING and SEVEN_BIT_STRING for translating
	 * strings that might have 8-bit chars to all 7-bit chars.
	 *
	 * @return	$replacement_chars, array of 8-bit ascii codes and
	 *			their replacement 7-bit ascii codes
	 * @access  public
	 */
	function get_replacement_chars()
	{
		$replacement_chars = array(
			128 => 42, 
			129 => 42,
			130 => 44,
			131 => 102,
			132 => 34,
			133 => 46,
			134 => 116,
			135 => 116,
			136 => 94,
			137 => 42,
			138 => 83,
			139 => 60,
			140 => 42,
			141 => 42,
			142 => 90,
			143 => 42,
			144 => 42,
			145 => 39,
			146 => 39,
			147 => 34,
			148 => 34,
			149 => 42,
			150 => 45,
			151 => 45,
			152 => 126,
			153 => 42,
			154 => 115,
			155 => 62,
			156 => 42,
			157 => 42,
			158 => 122,
			159 => 89,
			160 => 42,
			161 => 105,
			162 => 42,
			163 => 42,
			164 => 42,
			165 => 42,
			166 => 42,
			167 => 42,
			168 => 42,
			169 => 42,
			170 => 42,
			171 => 42, 
			172 => 42,
			173 => 42,
			174 => 42,
			175 => 42,
			176 => 42,
			177 => 42,
			178 => 42,
			179 => 42,
			180 => 39,
			181 => 42,
			182 => 42,
			183 => 42,
			184 => 42,
			185 => 42,
			186 => 42,
			187 => 42, 
			188 => 42,
			189 => 42,
			190 => 42,
			191 => 42,
			192 => 65,
			193 => 65,
			194 => 65,
			195 => 65,
			196 => 65,
			197 => 65,
			198 => 42,
			199 => 67,
			200 => 69,
			201 => 69,
			202 => 69,
			203 => 69,
			204 => 73,
			205 => 73,
			206 => 73,
			207 => 73,
			208 => 68,
			209 => 78,
			210 => 79,
			211 => 79,
			212 => 79,
			213 => 79,
			214 => 79,
			215 => 120,
			216 => 79,
			217 => 85,
			218 => 85,
			219 => 85,
			220 => 85,
			221 => 89,
			222 => 42,
			223 => 42,
			224 => 97,
			225 => 97,
			226 => 97,
			227 => 97,
			228 => 97, 
			229 => 97,
			230 => 42,
			231 => 42,
			232 => 101,
			233 => 101,
			234 => 101,
			235 => 101,
			236 => 105,
			237 => 105,
			238 => 105,
			239 => 105,
			240 => 42,
			241 => 110,
			242 => 111,
			243 => 111,
			244 => 111,
			245 => 111,
			246 => 111,
			247 => 47,
			248 => 42,
			249 => 117,
			250 => 117,
			251 => 117,
			252 => 117,
			253 => 121,
			254 => 42,	
			255 => 121
		);
  
		return $replacement_chars;   
	}
 
	/**
	 * This function prints out a table of all the ascii chars and codes,
	 * and the replacement chars and codes as defined by the 
	 * $replacement_chars array (see set_7bit_constants function above).
	 *
	 * @param	$replacement_chars, array of 8-bit ascii codes and
	 *			their replacement 7-bit ascii codes
	 * @return	doesn't return anything--just prints the table
	 * @access  public
	 */
	function print_replacement_array( $replacement_chars )
	{
  		echo( "<table border=\"1\" cellpadding=\"1\" cellspacing=\"1\">" );
  		echo( "<tr valign=\"top\" align=\"left\">" );
  		echo( "<th>ascii<br>code</th>" );
  		echo( "<th>char</th>" );
  		echo( "<th>replacement code</th>" );
  		echo( "<th>replacement char</th>" );
  		echo( "</tr>\n" );
  
		for ( $i = 0; $i < 256; $i++ )
		{
			echo( "<tr valign=\"top\" align=\"left\">" );
			echo( "<td>$i</td>" );
			echo( "<td>" . chr( $i ) . "</td>" );
			echo( "<td>" );
   
   			if ( isset( $replacement_chars[$i] ) )
				echo( $replacement_chars[$i] );
			else
				echo( "&nbsp;" );
 
			echo( "</td>" );
			echo( "<td>" );
			
			if ( isset( $replacement_chars[$i] ) )
				echo( chr( $replacement_chars[$i] ) );
			else
				echo( "&nbsp;" );
   
			echo( "</td>" );
			echo( "</tr>" );
		}
 
		echo( "</table>" );
	}
 
 	/**
	 * This function translates a string that may have 8-bit ascii 
	 * chars in it to only have 7-bit chars.  It uses the constants
	 * defined in the set_7bit_constants function, unless you override
	 * them by setting the optional second and third arguments.
	 *
	 * Currently, the function is really a wrapper for the PHP function
	 * strtr, but I made the wrapper for the following reasons.  First, 
	 * you don't need to have the names of the constants sprinkled 
	 * through your code wherever you do the conversion.  Second, 
	 * we might want to add more to it later, such as replacing some
	 * 8-bit chars with more than one 7-bit char (ie, replace the 
	 * ellipsis char with three periods or something).
	 *
	 * @author	Vicky Atkinson
	 * @param	$start_string to translate from 8-bit to 7-bit only
	 *			$from_string (optional) and $to_string (optional) if you wish
	 *			to override the use of the constants defined
	 *			by set_7bit_constants
	 * @return	$translated_string
	 * @access  public
	 */
 	function translate_8bit_string( $start_string, $from_string = EIGHT_BIT_STRING, $to_string = SEVEN_BIT_STRING )
	{
		$translated_string = strtr( $start_string, $from_string, $to_string );
		return $translated_string;
	}
 
	/**
	 * This function runs a 2D array through the translate_8bit_string 
	 * function.  It's simple, but since we usually get form submission
	 * data in an array for input into the database, it is nice to have
	 * this function.
	 *
	 * It uses foreach, so it shouldn't matter if the array is associative
	 * or how it's indexed.
	 *
	 * Be careful, since the array is passed by reference.  This 
	 * is done to speed things up since in foreseeable cases we
	 * won't need to keep a copy of the old array.
	 *
	 * @param	$eight_bit_array, a 2D array of strings to be translated 
	 *			by the translate_8bit_string function.  It doesn't matter
	 *			how the array is indexed.
	 * @return	nothing is returned, since the array is passed by
	 *			reference and is itself translated.
	 * @access  public
	 */
	function translate_8bit_array( &$eight_bit_array )
	{
		foreach ( $eight_bit_array as $key => $eight_bit_string )
   			$eight_bit_array[$key] = $this->translate_8bit_string( $eight_bit_string );
  
		return;
	}
} // END OF EightBitString

?>
