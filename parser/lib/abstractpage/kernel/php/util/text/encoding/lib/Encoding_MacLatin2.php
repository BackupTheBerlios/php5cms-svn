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
 
class Encoding_MacLatin2 extends Encoding
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encoding_MacLatin2()
	{
		$this->Encoding( "MacLatin2" );
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populateEncodingMap()
	{
		$this->encoding_map = array(
			0x80 => 0x00c4, // LATIN CAPITAL LETTER A WITH DIAERESIS
			0x81 => 0x0100, // LATIN CAPITAL LETTER A WITH MACRON
			0x82 => 0x0101, // LATIN SMALL LETTER A WITH MACRON
			0x83 => 0x00c9, // LATIN CAPITAL LETTER E WITH ACUTE
			0x84 => 0x0104, // LATIN CAPITAL LETTER A WITH OGONEK
			0x85 => 0x00d6, // LATIN CAPITAL LETTER O WITH DIAERESIS
			0x86 => 0x00dc, // LATIN CAPITAL LETTER U WITH DIAERESIS
			0x87 => 0x00e1, // LATIN SMALL LETTER A WITH ACUTE
			0x88 => 0x0105, // LATIN SMALL LETTER A WITH OGONEK
			0x89 => 0x010c, // LATIN CAPITAL LETTER C WITH CARON
			0x8a => 0x00e4, // LATIN SMALL LETTER A WITH DIAERESIS
			0x8b => 0x010d, // LATIN SMALL LETTER C WITH CARON
			0x8c => 0x0106, // LATIN CAPITAL LETTER C WITH ACUTE
			0x8d => 0x0107, // LATIN SMALL LETTER C WITH ACUTE
			0x8e => 0x00e9, // LATIN SMALL LETTER E WITH ACUTE
			0x8f => 0x0179, // LATIN CAPITAL LETTER Z WITH ACUTE
			0x90 => 0x017a, // LATIN SMALL LETTER Z WITH ACUTE
			0x91 => 0x010e, // LATIN CAPITAL LETTER D WITH CARON
			0x92 => 0x00ed, // LATIN SMALL LETTER I WITH ACUTE
			0x93 => 0x010f, // LATIN SMALL LETTER D WITH CARON
			0x94 => 0x0112, // LATIN CAPITAL LETTER E WITH MACRON
			0x95 => 0x0113, // LATIN SMALL LETTER E WITH MACRON
			0x96 => 0x0116, // LATIN CAPITAL LETTER E WITH DOT ABOVE
			0x97 => 0x00f3, // LATIN SMALL LETTER O WITH ACUTE
			0x98 => 0x0117, // LATIN SMALL LETTER E WITH DOT ABOVE
			0x99 => 0x00f4, // LATIN SMALL LETTER O WITH CIRCUMFLEX
			0x9a => 0x00f6, // LATIN SMALL LETTER O WITH DIAERESIS
			0x9b => 0x00f5, // LATIN SMALL LETTER O WITH TILDE
			0x9c => 0x00fa, // LATIN SMALL LETTER U WITH ACUTE
			0x9d => 0x011a, // LATIN CAPITAL LETTER E WITH CARON
			0x9e => 0x011b, // LATIN SMALL LETTER E WITH CARON
			0x9f => 0x00fc, // LATIN SMALL LETTER U WITH DIAERESIS
			0xa0 => 0x2020, // DAGGER
			0xa1 => 0x00b0, // DEGREE SIGN
			0xa2 => 0x0118, // LATIN CAPITAL LETTER E WITH OGONEK
			0xa4 => 0x00a7, // SECTION SIGN
			0xa5 => 0x2022, // BULLET
			0xa6 => 0x00b6, // PILCROW SIGN
			0xa7 => 0x00df, // LATIN SMALL LETTER SHARP S
			0xa8 => 0x00ae, // REGISTERED SIGN
			0xaa => 0x2122, // TRADE MARK SIGN
			0xab => 0x0119, // LATIN SMALL LETTER E WITH OGONEK
			0xac => 0x00a8, // DIAERESIS
			0xad => 0x2260, // NOT EQUAL TO
			0xae => 0x0123, // LATIN SMALL LETTER G WITH CEDILLA
			0xaf => 0x012e, // LATIN CAPITAL LETTER I WITH OGONEK
			0xb0 => 0x012f, // LATIN SMALL LETTER I WITH OGONEK
			0xb1 => 0x012a, // LATIN CAPITAL LETTER I WITH MACRON
			0xb2 => 0x2264, // LESS-THAN OR EQUAL TO
			0xb3 => 0x2265, // GREATER-THAN OR EQUAL TO
			0xb4 => 0x012b, // LATIN SMALL LETTER I WITH MACRON
			0xb5 => 0x0136, // LATIN CAPITAL LETTER K WITH CEDILLA
			0xb6 => 0x2202, // PARTIAL DIFFERENTIAL
			0xb7 => 0x2211, // N-ARY SUMMATION
			0xb8 => 0x0142, // LATIN SMALL LETTER L WITH STROKE
			0xb9 => 0x013b, // LATIN CAPITAL LETTER L WITH CEDILLA
			0xba => 0x013c, // LATIN SMALL LETTER L WITH CEDILLA
			0xbb => 0x013d, // LATIN CAPITAL LETTER L WITH CARON
			0xbc => 0x013e, // LATIN SMALL LETTER L WITH CARON
			0xbd => 0x0139, // LATIN CAPITAL LETTER L WITH ACUTE
			0xbe => 0x013a, // LATIN SMALL LETTER L WITH ACUTE
			0xbf => 0x0145, // LATIN CAPITAL LETTER N WITH CEDILLA
			0xc0 => 0x0146, // LATIN SMALL LETTER N WITH CEDILLA
			0xc1 => 0x0143, // LATIN CAPITAL LETTER N WITH ACUTE
			0xc2 => 0x00ac, // NOT SIGN
			0xc3 => 0x221a, // SQUARE ROOT
			0xc4 => 0x0144, // LATIN SMALL LETTER N WITH ACUTE
			0xc5 => 0x0147, // LATIN CAPITAL LETTER N WITH CARON
			0xc6 => 0x2206, // INCREMENT
			0xc7 => 0x00ab, // LEFT-POINTING DOUBLE ANGLE QUOTATION MARK
			0xc8 => 0x00bb, // RIGHT-POINTING DOUBLE ANGLE QUOTATION MARK
			0xc9 => 0x2026, // HORIZONTAL ELLIPSIS
			0xca => 0x00a0, // NO-BREAK SPACE
			0xcb => 0x0148, // LATIN SMALL LETTER N WITH CARON
			0xcc => 0x0150, // LATIN CAPITAL LETTER O WITH DOUBLE ACUTE
			0xcd => 0x00d5, // LATIN CAPITAL LETTER O WITH TILDE
			0xce => 0x0151, // LATIN SMALL LETTER O WITH DOUBLE ACUTE
			0xcf => 0x014c, // LATIN CAPITAL LETTER O WITH MACRON
			0xd0 => 0x2013, // EN DASH
			0xd1 => 0x2014, // EM DASH
			0xd2 => 0x201c, // LEFT DOUBLE QUOTATION MARK
			0xd3 => 0x201d, // RIGHT DOUBLE QUOTATION MARK
			0xd4 => 0x2018, // LEFT SINGLE QUOTATION MARK
			0xd5 => 0x2019, // RIGHT SINGLE QUOTATION MARK
			0xd6 => 0x00f7, // DIVISION SIGN
			0xd7 => 0x25ca, // LOZENGE
			0xd8 => 0x014d, // LATIN SMALL LETTER O WITH MACRON
			0xd9 => 0x0154, // LATIN CAPITAL LETTER R WITH ACUTE
			0xda => 0x0155, // LATIN SMALL LETTER R WITH ACUTE
			0xdb => 0x0158, // LATIN CAPITAL LETTER R WITH CARON
			0xdc => 0x2039, // SINGLE LEFT-POINTING ANGLE QUOTATION MARK
			0xdd => 0x203a, // SINGLE RIGHT-POINTING ANGLE QUOTATION MARK
			0xde => 0x0159, // LATIN SMALL LETTER R WITH CARON
			0xdf => 0x0156, // LATIN CAPITAL LETTER R WITH CEDILLA
			0xe0 => 0x0157, // LATIN SMALL LETTER R WITH CEDILLA
			0xe1 => 0x0160, // LATIN CAPITAL LETTER S WITH CARON
			0xe2 => 0x201a, // SINGLE LOW-9 QUOTATION MARK
			0xe3 => 0x201e, // DOUBLE LOW-9 QUOTATION MARK
			0xe4 => 0x0161, // LATIN SMALL LETTER S WITH CARON
			0xe5 => 0x015a, // LATIN CAPITAL LETTER S WITH ACUTE
			0xe6 => 0x015b, // LATIN SMALL LETTER S WITH ACUTE
			0xe7 => 0x00c1, // LATIN CAPITAL LETTER A WITH ACUTE
			0xe8 => 0x0164, // LATIN CAPITAL LETTER T WITH CARON
			0xe9 => 0x0165, // LATIN SMALL LETTER T WITH CARON
			0xea => 0x00cd, // LATIN CAPITAL LETTER I WITH ACUTE
			0xeb => 0x017d, // LATIN CAPITAL LETTER Z WITH CARON
			0xec => 0x017e, // LATIN SMALL LETTER Z WITH CARON
			0xed => 0x016a, // LATIN CAPITAL LETTER U WITH MACRON
			0xee => 0x00d3, // LATIN CAPITAL LETTER O WITH ACUTE
			0xef => 0x00d4, // LATIN CAPITAL LETTER O WITH CIRCUMFLEX
			0xf0 => 0x016b, // LATIN SMALL LETTER U WITH MACRON
			0xf1 => 0x016e, // LATIN CAPITAL LETTER U WITH RING ABOVE
			0xf2 => 0x00da, // LATIN CAPITAL LETTER U WITH ACUTE
			0xf3 => 0x016f, // LATIN SMALL LETTER U WITH RING ABOVE
			0xf4 => 0x0170, // LATIN CAPITAL LETTER U WITH DOUBLE ACUTE
			0xf5 => 0x0171, // LATIN SMALL LETTER U WITH DOUBLE ACUTE
			0xf6 => 0x0172, // LATIN CAPITAL LETTER U WITH OGONEK
			0xf7 => 0x0173, // LATIN SMALL LETTER U WITH OGONEK
			0xf8 => 0x00dd, // LATIN CAPITAL LETTER Y WITH ACUTE
			0xf9 => 0x00fd, // LATIN SMALL LETTER Y WITH ACUTE
			0xfa => 0x0137, // LATIN SMALL LETTER K WITH CEDILLA
			0xfb => 0x017b, // LATIN CAPITAL LETTER Z WITH DOT ABOVE
			0xfc => 0x0141, // LATIN CAPITAL LETTER L WITH STROKE
			0xfd => 0x017c, // LATIN SMALL LETTER Z WITH DOT ABOVE
			0xfe => 0x0122, // LATIN CAPITAL LETTER G WITH CEDILLA
			0xff => 0x02c7  // CARON
		);
	}
} // END OF Encoding_MacLatin2

?>
