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
 
class Encoding_ISO8859_4 extends Encoding
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encoding_ISO8859_4()
	{
		$this->Encoding( "ISO8859-4" );
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populateEncodingMap()
	{
		$this->encoding_map = array(
			0xa1 => 0x0104, // LATIN CAPITAL LETTER A WITH OGONEK
			0xa2 => 0x0138, // LATIN SMALL LETTER KRA
			0xa3 => 0x0156, // LATIN CAPITAL LETTER R WITH CEDILLA
			0xa5 => 0x0128, // LATIN CAPITAL LETTER I WITH TILDE
			0xa6 => 0x013b, // LATIN CAPITAL LETTER L WITH CEDILLA
			0xa9 => 0x0160, // LATIN CAPITAL LETTER S WITH CARON
			0xaa => 0x0112, // LATIN CAPITAL LETTER E WITH MACRON
			0xab => 0x0122, // LATIN CAPITAL LETTER G WITH CEDILLA
			0xac => 0x0166, // LATIN CAPITAL LETTER T WITH STROKE
			0xae => 0x017d, // LATIN CAPITAL LETTER Z WITH CARON
			0xb1 => 0x0105, // LATIN SMALL LETTER A WITH OGONEK
			0xb2 => 0x02db, // OGONEK
			0xb3 => 0x0157, // LATIN SMALL LETTER R WITH CEDILLA
			0xb5 => 0x0129, // LATIN SMALL LETTER I WITH TILDE
			0xb6 => 0x013c, // LATIN SMALL LETTER L WITH CEDILLA
			0xb7 => 0x02c7, // CARON
			0xb9 => 0x0161, // LATIN SMALL LETTER S WITH CARON
			0xba => 0x0113, // LATIN SMALL LETTER E WITH MACRON
			0xbb => 0x0123, // LATIN SMALL LETTER G WITH CEDILLA
			0xbc => 0x0167, // LATIN SMALL LETTER T WITH STROKE
			0xbd => 0x014a, // LATIN CAPITAL LETTER ENG
			0xbe => 0x017e, // LATIN SMALL LETTER Z WITH CARON
			0xbf => 0x014b, // LATIN SMALL LETTER ENG
			0xc0 => 0x0100, // LATIN CAPITAL LETTER A WITH MACRON
			0xc7 => 0x012e, // LATIN CAPITAL LETTER I WITH OGONEK
			0xc8 => 0x010c, // LATIN CAPITAL LETTER C WITH CARON
			0xca => 0x0118, // LATIN CAPITAL LETTER E WITH OGONEK
			0xcc => 0x0116, // LATIN CAPITAL LETTER E WITH DOT ABOVE
			0xcf => 0x012a, // LATIN CAPITAL LETTER I WITH MACRON
			0xd0 => 0x0110, // LATIN CAPITAL LETTER D WITH STROKE
			0xd1 => 0x0145, // LATIN CAPITAL LETTER N WITH CEDILLA
			0xd2 => 0x014c, // LATIN CAPITAL LETTER O WITH MACRON
			0xd3 => 0x0136, // LATIN CAPITAL LETTER K WITH CEDILLA
			0xd9 => 0x0172, // LATIN CAPITAL LETTER U WITH OGONEK
			0xdd => 0x0168, // LATIN CAPITAL LETTER U WITH TILDE
			0xde => 0x016a, // LATIN CAPITAL LETTER U WITH MACRON
			0xe0 => 0x0101, // LATIN SMALL LETTER A WITH MACRON
			0xe7 => 0x012f, // LATIN SMALL LETTER I WITH OGONEK
			0xe8 => 0x010d, // LATIN SMALL LETTER C WITH CARON
			0xea => 0x0119, // LATIN SMALL LETTER E WITH OGONEK
			0xec => 0x0117, // LATIN SMALL LETTER E WITH DOT ABOVE
			0xef => 0x012b, // LATIN SMALL LETTER I WITH MACRON
			0xf0 => 0x0111, // LATIN SMALL LETTER D WITH STROKE
			0xf1 => 0x0146, // LATIN SMALL LETTER N WITH CEDILLA
			0xf2 => 0x014d, // LATIN SMALL LETTER O WITH MACRON
			0xf3 => 0x0137, // LATIN SMALL LETTER K WITH CEDILLA
			0xf9 => 0x0173, // LATIN SMALL LETTER U WITH OGONEK
			0xfd => 0x0169, // LATIN SMALL LETTER U WITH TILDE
			0xfe => 0x016b, // LATIN SMALL LETTER U WITH MACRON
			0xff => 0x02d9  // DOT ABOVE
		);
	}
} // END OF Encoding_ISO8859_4

?>
