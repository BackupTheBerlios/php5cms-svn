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
 
class Encoding_ISO8859_3 extends Encoding
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encoding_ISO8859_3()
	{
		$this->Encoding( "ISO8859-3" );
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populateEncodingMap()
	{
		$this->encoding_map = array(
			0xa1 => 0x0126, // LATIN CAPITAL LETTER H WITH STROKE
			0xa2 => 0x02d8, // BREVE
			0xa5 => null,
			0xa6 => 0x0124, // LATIN CAPITAL LETTER H WITH CIRCUMFLEX
			0xa9 => 0x0130, // LATIN CAPITAL LETTER I WITH DOT ABOVE
			0xaa => 0x015e, // LATIN CAPITAL LETTER S WITH CEDILLA
			0xab => 0x011e, // LATIN CAPITAL LETTER G WITH BREVE
			0xac => 0x0134, // LATIN CAPITAL LETTER J WITH CIRCUMFLEX
			0xae => null,
			0xaf => 0x017b, // LATIN CAPITAL LETTER Z WITH DOT ABOVE
			0xb1 => 0x0127, // LATIN SMALL LETTER H WITH STROKE
			0xb6 => 0x0125, // LATIN SMALL LETTER H WITH CIRCUMFLEX
			0xb9 => 0x0131, // LATIN SMALL LETTER DOTLESS I
			0xba => 0x015f, // LATIN SMALL LETTER S WITH CEDILLA
			0xbb => 0x011f, // LATIN SMALL LETTER G WITH BREVE
			0xbc => 0x0135, // LATIN SMALL LETTER J WITH CIRCUMFLEX
			0xbe => null,
			0xbf => 0x017c, // LATIN SMALL LETTER Z WITH DOT ABOVE
			0xc3 => null,
			0xc5 => 0x010a, // LATIN CAPITAL LETTER C WITH DOT ABOVE
			0xc6 => 0x0108, // LATIN CAPITAL LETTER C WITH CIRCUMFLEX
			0xd0 => null,
			0xd5 => 0x0120, // LATIN CAPITAL LETTER G WITH DOT ABOVE
			0xd8 => 0x011c, // LATIN CAPITAL LETTER G WITH CIRCUMFLEX
			0xdd => 0x016c, // LATIN CAPITAL LETTER U WITH BREVE
			0xde => 0x015c, // LATIN CAPITAL LETTER S WITH CIRCUMFLEX
			0xe3 => null,
			0xe5 => 0x010b, // LATIN SMALL LETTER C WITH DOT ABOVE
			0xe6 => 0x0109, // LATIN SMALL LETTER C WITH CIRCUMFLEX
			0xf0 => null,
			0xf5 => 0x0121, // LATIN SMALL LETTER G WITH DOT ABOVE
			0xf8 => 0x011d, // LATIN SMALL LETTER G WITH CIRCUMFLEX
			0xfd => 0x016d, // LATIN SMALL LETTER U WITH BREVE
			0xfe => 0x015d, // LATIN SMALL LETTER S WITH CIRCUMFLEX
			0xff => 0x02d9  // DOT ABOVE
		);
	}
} // END OF Encoding_ISO8859_3

?>
