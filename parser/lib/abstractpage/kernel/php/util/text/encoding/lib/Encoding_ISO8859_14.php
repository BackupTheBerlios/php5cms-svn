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


using( 'util.text.encoding.lib.Encoding' );


/**
 * @package util_text_encoding_lib
 */
 
class Encoding_ISO8859_14 extends Encoding
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encoding_ISO8859_14()
	{
		$this->Encoding( "ISO8859-14" );
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populateEncodingMap()
	{
		$this->encoding_map = array(
			0xa1 => 0x1e02, // LATIN CAPITAL LETTER B WITH DOT ABOVE
			0xa2 => 0x1e03, // LATIN SMALL LETTER B WITH DOT ABOVE
			0xa4 => 0x010a, // LATIN CAPITAL LETTER C WITH DOT ABOVE
			0xa5 => 0x010b, // LATIN SMALL LETTER C WITH DOT ABOVE
			0xa6 => 0x1e0a, // LATIN CAPITAL LETTER D WITH DOT ABOVE
			0xa8 => 0x1e80, // LATIN CAPITAL LETTER W WITH GRAVE
			0xaa => 0x1e82, // LATIN CAPITAL LETTER W WITH ACUTE
			0xab => 0x1e0b, // LATIN SMALL LETTER D WITH DOT ABOVE
			0xac => 0x1ef2, // LATIN CAPITAL LETTER Y WITH GRAVE
			0xaf => 0x0178, // LATIN CAPITAL LETTER Y WITH DIAERESIS
			0xb0 => 0x1e1e, // LATIN CAPITAL LETTER F WITH DOT ABOVE
			0xb1 => 0x1e1f, // LATIN SMALL LETTER F WITH DOT ABOVE
			0xb2 => 0x0120, // LATIN CAPITAL LETTER G WITH DOT ABOVE
			0xb3 => 0x0121, // LATIN SMALL LETTER G WITH DOT ABOVE
			0xb4 => 0x1e40, // LATIN CAPITAL LETTER M WITH DOT ABOVE
			0xb5 => 0x1e41, // LATIN SMALL LETTER M WITH DOT ABOVE
			0xb7 => 0x1e56, // LATIN CAPITAL LETTER P WITH DOT ABOVE
			0xb8 => 0x1e81, // LATIN SMALL LETTER W WITH GRAVE
			0xb9 => 0x1e57, // LATIN SMALL LETTER P WITH DOT ABOVE
			0xba => 0x1e83, // LATIN SMALL LETTER W WITH ACUTE
			0xbb => 0x1e60, // LATIN CAPITAL LETTER S WITH DOT ABOVE
			0xbc => 0x1ef3, // LATIN SMALL LETTER Y WITH GRAVE
			0xbd => 0x1e84, // LATIN CAPITAL LETTER W WITH DIAERESIS
			0xbe => 0x1e85, // LATIN SMALL LETTER W WITH DIAERESIS
			0xbf => 0x1e61, // LATIN SMALL LETTER S WITH DOT ABOVE
			0xd0 => 0x0174, // LATIN CAPITAL LETTER W WITH CIRCUMFLEX
			0xd7 => 0x1e6a, // LATIN CAPITAL LETTER T WITH DOT ABOVE
			0xde => 0x0176, // LATIN CAPITAL LETTER Y WITH CIRCUMFLEX
			0xf0 => 0x0175, // LATIN SMALL LETTER W WITH CIRCUMFLEX
			0xf7 => 0x1e6b, // LATIN SMALL LETTER T WITH DOT ABOVE
			0xfe => 0x0177  // LATIN SMALL LETTER Y WITH CIRCUMFLEX
		);
	}
} // END OF Encoding_ISO8859_14

?>
