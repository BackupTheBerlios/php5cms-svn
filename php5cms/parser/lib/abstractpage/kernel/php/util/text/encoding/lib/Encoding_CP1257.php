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
 
class Encoding_CP1257 extends Encoding
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encoding_CP1257()
	{
		$this->Encoding( "CP1257" );
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populateEncodingMap()
	{
		$this->encoding_map = array(
			0x80 => 0x20ac, // EURO SIGN
			0x81 => null,   // UNDEFINED
			0x82 => 0x201a, // SINGLE LOW-9 QUOTATION MARK
			0x83 => null,   // UNDEFINED
			0x84 => 0x201e, // DOUBLE LOW-9 QUOTATION MARK
			0x85 => 0x2026, // HORIZONTAL ELLIPSIS
			0x86 => 0x2020, // DAGGER
			0x87 => 0x2021, // DOUBLE DAGGER
			0x88 => null,   // UNDEFINED
			0x89 => 0x2030, // PER MILLE SIGN
			0x8a => null,   // UNDEFINED
			0x8b => 0x2039, // SINGLE LEFT-POINTING ANGLE QUOTATION MARK
			0x8c => null,   // UNDEFINED
			0x8d => 0x00a8, // DIAERESIS
			0x8e => 0x02c7, // CARON
			0x8f => 0x00b8, // CEDILLA
			0x90 => null,   // UNDEFINED
			0x91 => 0x2018, // LEFT SINGLE QUOTATION MARK
			0x92 => 0x2019, // RIGHT SINGLE QUOTATION MARK
			0x93 => 0x201c, // LEFT DOUBLE QUOTATION MARK
			0x94 => 0x201d, // RIGHT DOUBLE QUOTATION MARK
			0x95 => 0x2022, // BULLET
			0x96 => 0x2013, // EN DASH
			0x97 => 0x2014, // EM DASH
			0x98 => null,   // UNDEFINED
			0x99 => 0x2122, // TRADE MARK SIGN
			0x9a => null,   // UNDEFINED
			0x9b => 0x203a, // SINGLE RIGHT-POINTING ANGLE QUOTATION MARK
			0x9c => null,   // UNDEFINED
			0x9d => 0x00af, // MACRON
			0x9e => 0x02db, // OGONEK
			0x9f => null,   // UNDEFINED
			0xa1 => null,   // UNDEFINED
			0xa5 => null,   // UNDEFINED
			0xa8 => 0x00d8, // LATIN CAPITAL LETTER O WITH STROKE
			0xaa => 0x0156, // LATIN CAPITAL LETTER R WITH CEDILLA
			0xaf => 0x00c6, // LATIN CAPITAL LETTER AE
			0xb8 => 0x00f8, // LATIN SMALL LETTER O WITH STROKE
			0xba => 0x0157, // LATIN SMALL LETTER R WITH CEDILLA
			0xbf => 0x00e6, // LATIN SMALL LETTER AE
			0xc0 => 0x0104, // LATIN CAPITAL LETTER A WITH OGONEK
			0xc1 => 0x012e, // LATIN CAPITAL LETTER I WITH OGONEK
			0xc2 => 0x0100, // LATIN CAPITAL LETTER A WITH MACRON
			0xc3 => 0x0106, // LATIN CAPITAL LETTER C WITH ACUTE
			0xc6 => 0x0118, // LATIN CAPITAL LETTER E WITH OGONEK
			0xc7 => 0x0112, // LATIN CAPITAL LETTER E WITH MACRON
			0xc8 => 0x010c, // LATIN CAPITAL LETTER C WITH CARON
			0xca => 0x0179, // LATIN CAPITAL LETTER Z WITH ACUTE
			0xcb => 0x0116, // LATIN CAPITAL LETTER E WITH DOT ABOVE
			0xcc => 0x0122, // LATIN CAPITAL LETTER G WITH CEDILLA
			0xcd => 0x0136, // LATIN CAPITAL LETTER K WITH CEDILLA
			0xce => 0x012a, // LATIN CAPITAL LETTER I WITH MACRON
			0xcf => 0x013b, // LATIN CAPITAL LETTER L WITH CEDILLA
			0xd0 => 0x0160, // LATIN CAPITAL LETTER S WITH CARON
			0xd1 => 0x0143, // LATIN CAPITAL LETTER N WITH ACUTE
			0xd2 => 0x0145, // LATIN CAPITAL LETTER N WITH CEDILLA
			0xd4 => 0x014c, // LATIN CAPITAL LETTER O WITH MACRON
			0xd8 => 0x0172, // LATIN CAPITAL LETTER U WITH OGONEK
			0xd9 => 0x0141, // LATIN CAPITAL LETTER L WITH STROKE
			0xda => 0x015a, // LATIN CAPITAL LETTER S WITH ACUTE
			0xdb => 0x016a, // LATIN CAPITAL LETTER U WITH MACRON
			0xdd => 0x017b, // LATIN CAPITAL LETTER Z WITH DOT ABOVE
			0xde => 0x017d, // LATIN CAPITAL LETTER Z WITH CARON
			0xe0 => 0x0105, // LATIN SMALL LETTER A WITH OGONEK
			0xe1 => 0x012f, // LATIN SMALL LETTER I WITH OGONEK
			0xe2 => 0x0101, // LATIN SMALL LETTER A WITH MACRON
			0xe3 => 0x0107, // LATIN SMALL LETTER C WITH ACUTE
			0xe6 => 0x0119, // LATIN SMALL LETTER E WITH OGONEK
			0xe7 => 0x0113, // LATIN SMALL LETTER E WITH MACRON
			0xe8 => 0x010d, // LATIN SMALL LETTER C WITH CARON
			0xea => 0x017a, // LATIN SMALL LETTER Z WITH ACUTE
			0xeb => 0x0117, // LATIN SMALL LETTER E WITH DOT ABOVE
			0xec => 0x0123, // LATIN SMALL LETTER G WITH CEDILLA
			0xed => 0x0137, // LATIN SMALL LETTER K WITH CEDILLA
			0xee => 0x012b, // LATIN SMALL LETTER I WITH MACRON
			0xef => 0x013c, // LATIN SMALL LETTER L WITH CEDILLA
			0xf0 => 0x0161, // LATIN SMALL LETTER S WITH CARON
			0xf1 => 0x0144, // LATIN SMALL LETTER N WITH ACUTE
			0xf2 => 0x0146, // LATIN SMALL LETTER N WITH CEDILLA
			0xf4 => 0x014d, // LATIN SMALL LETTER O WITH MACRON
			0xf8 => 0x0173, // LATIN SMALL LETTER U WITH OGONEK
			0xf9 => 0x0142, // LATIN SMALL LETTER L WITH STROKE
			0xfa => 0x015b, // LATIN SMALL LETTER S WITH ACUTE
			0xfb => 0x016b, // LATIN SMALL LETTER U WITH MACRON
			0xfd => 0x017c, // LATIN SMALL LETTER Z WITH DOT ABOVE
			0xfe => 0x017e, // LATIN SMALL LETTER Z WITH CARON
			0xff => 0x02d9  // DOT ABOVE
		);
	}
} // END OF Encoding_CP1257

?>
