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
 
class Encoding_MacIceland extends Encoding
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encoding_MacIceland()
	{
		$this->Encoding( "MacIceland" );
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populateEncodingMap()
	{
		$this->encoding_map = array(
			0x80 => 0x00c4, // LATIN CAPITAL LETTER A WITH DIAERESIS
			0x81 => 0x00c5, // LATIN CAPITAL LETTER A WITH RING ABOVE
			0x82 => 0x00c7, // LATIN CAPITAL LETTER C WITH CEDILLA
			0x83 => 0x00c9, // LATIN CAPITAL LETTER E WITH ACUTE
			0x84 => 0x00d1, // LATIN CAPITAL LETTER N WITH TILDE
			0x85 => 0x00d6, // LATIN CAPITAL LETTER O WITH DIAERESIS
			0x86 => 0x00dc, // LATIN CAPITAL LETTER U WITH DIAERESIS
			0x87 => 0x00e1, // LATIN SMALL LETTER A WITH ACUTE
			0x88 => 0x00e0, // LATIN SMALL LETTER A WITH GRAVE
			0x89 => 0x00e2, // LATIN SMALL LETTER A WITH CIRCUMFLEX
			0x8a => 0x00e4, // LATIN SMALL LETTER A WITH DIAERESIS
			0x8b => 0x00e3, // LATIN SMALL LETTER A WITH TILDE
			0x8c => 0x00e5, // LATIN SMALL LETTER A WITH RING ABOVE
			0x8d => 0x00e7, // LATIN SMALL LETTER C WITH CEDILLA
			0x8e => 0x00e9, // LATIN SMALL LETTER E WITH ACUTE
			0x8f => 0x00e8, // LATIN SMALL LETTER E WITH GRAVE
			0x90 => 0x00ea, // LATIN SMALL LETTER E WITH CIRCUMFLEX
			0x91 => 0x00eb, // LATIN SMALL LETTER E WITH DIAERESIS
			0x92 => 0x00ed, // LATIN SMALL LETTER I WITH ACUTE
			0x93 => 0x00ec, // LATIN SMALL LETTER I WITH GRAVE
			0x94 => 0x00ee, // LATIN SMALL LETTER I WITH CIRCUMFLEX
			0x95 => 0x00ef, // LATIN SMALL LETTER I WITH DIAERESIS
			0x96 => 0x00f1, // LATIN SMALL LETTER N WITH TILDE
			0x97 => 0x00f3, // LATIN SMALL LETTER O WITH ACUTE
			0x98 => 0x00f2, // LATIN SMALL LETTER O WITH GRAVE
			0x99 => 0x00f4, // LATIN SMALL LETTER O WITH CIRCUMFLEX
			0x9a => 0x00f6, // LATIN SMALL LETTER O WITH DIAERESIS
			0x9b => 0x00f5, // LATIN SMALL LETTER O WITH TILDE
			0x9c => 0x00fa, // LATIN SMALL LETTER U WITH ACUTE
			0x9d => 0x00f9, // LATIN SMALL LETTER U WITH GRAVE
			0x9e => 0x00fb, // LATIN SMALL LETTER U WITH CIRCUMFLEX
			0x9f => 0x00fc, // LATIN SMALL LETTER U WITH DIAERESIS
			0xa0 => 0x00dd, // LATIN CAPITAL LETTER Y WITH ACUTE
			0xa1 => 0x00b0, // DEGREE SIGN
			0xa4 => 0x00a7, // SECTION SIGN
			0xa5 => 0x2022, // BULLET
			0xa6 => 0x00b6, // PILCROW SIGN
			0xa7 => 0x00df, // LATIN SMALL LETTER SHARP S
			0xa8 => 0x00ae, // REGISTERED SIGN
			0xaa => 0x2122, // TRADE MARK SIGN
			0xab => 0x00b4, // ACUTE ACCENT
			0xac => 0x00a8, // DIAERESIS
			0xad => 0x2260, // NOT EQUAL TO
			0xae => 0x00c6, // LATIN CAPITAL LIGATURE AE
			0xaf => 0x00d8, // LATIN CAPITAL LETTER O WITH STROKE
			0xb0 => 0x221e, // INFINITY
			0xb2 => 0x2264, // LESS-THAN OR EQUAL TO
			0xb3 => 0x2265, // GREATER-THAN OR EQUAL TO
			0xb4 => 0x00a5, // YEN SIGN
			0xb6 => 0x2202, // PARTIAL DIFFERENTIAL
			0xb7 => 0x2211, // N-ARY SUMMATION
			0xb8 => 0x220f, // N-ARY PRODUCT
			0xb9 => 0x03c0, // GREEK SMALL LETTER PI
			0xba => 0x222b, // INTEGRAL
			0xbb => 0x00aa, // FEMININE ORDINAL INDICATOR
			0xbc => 0x00ba, // MASCULINE ORDINAL INDICATOR
			0xbd => 0x2126, // OHM SIGN
			0xbe => 0x00e6, // LATIN SMALL LIGATURE AE
			0xbf => 0x00f8, // LATIN SMALL LETTER O WITH STROKE
			0xc0 => 0x00bf, // INVERTED QUESTION MARK
			0xc1 => 0x00a1, // INVERTED EXCLAMATION MARK
			0xc2 => 0x00ac, // NOT SIGN
			0xc3 => 0x221a, // SQUARE ROOT
			0xc4 => 0x0192, // LATIN SMALL LETTER F WITH HOOK
			0xc5 => 0x2248, // ALMOST EQUAL TO
			0xc6 => 0x2206, // INCREMENT
			0xc7 => 0x00ab, // LEFT-POINTING DOUBLE ANGLE QUOTATION MARK
			0xc8 => 0x00bb, // RIGHT-POINTING DOUBLE ANGLE QUOTATION MARK
			0xc9 => 0x2026, // HORIZONTAL ELLIPSIS
			0xca => 0x00a0, // NO-BREAK SPACE
			0xcb => 0x00c0, // LATIN CAPITAL LETTER A WITH GRAVE
			0xcc => 0x00c3, // LATIN CAPITAL LETTER A WITH TILDE
			0xcd => 0x00d5, // LATIN CAPITAL LETTER O WITH TILDE
			0xce => 0x0152, // LATIN CAPITAL LIGATURE OE
			0xcf => 0x0153, // LATIN SMALL LIGATURE OE
			0xd0 => 0x2013, // EN DASH
			0xd1 => 0x2014, // EM DASH
			0xd2 => 0x201c, // LEFT DOUBLE QUOTATION MARK
			0xd3 => 0x201d, // RIGHT DOUBLE QUOTATION MARK
			0xd4 => 0x2018, // LEFT SINGLE QUOTATION MARK
			0xd5 => 0x2019, // RIGHT SINGLE QUOTATION MARK
			0xd6 => 0x00f7, // DIVISION SIGN
			0xd7 => 0x25ca, // LOZENGE
			0xd8 => 0x00ff, // LATIN SMALL LETTER Y WITH DIAERESIS
			0xd9 => 0x0178, // LATIN CAPITAL LETTER Y WITH DIAERESIS
			0xda => 0x2044, // FRACTION SLASH
			0xdb => 0x00a4, // CURRENCY SIGN
			0xdc => 0x00d0, // LATIN CAPITAL LETTER ETH
			0xdd => 0x00f0, // LATIN SMALL LETTER ETH
			0xdf => 0x00fe, // LATIN SMALL LETTER THORN
			0xe0 => 0x00fd, // LATIN SMALL LETTER Y WITH ACUTE
			0xe1 => 0x00b7, // MIDDLE DOT
			0xe2 => 0x201a, // SINGLE LOW-9 QUOTATION MARK
			0xe3 => 0x201e, // DOUBLE LOW-9 QUOTATION MARK
			0xe4 => 0x2030, // PER MILLE SIGN
			0xe5 => 0x00c2, // LATIN CAPITAL LETTER A WITH CIRCUMFLEX
			0xe6 => 0x00ca, // LATIN CAPITAL LETTER E WITH CIRCUMFLEX
			0xe7 => 0x00c1, // LATIN CAPITAL LETTER A WITH ACUTE
			0xe8 => 0x00cb, // LATIN CAPITAL LETTER E WITH DIAERESIS
			0xe9 => 0x00c8, // LATIN CAPITAL LETTER E WITH GRAVE
			0xea => 0x00cd, // LATIN CAPITAL LETTER I WITH ACUTE
			0xeb => 0x00ce, // LATIN CAPITAL LETTER I WITH CIRCUMFLEX
			0xec => 0x00cf, // LATIN CAPITAL LETTER I WITH DIAERESIS
			0xed => 0x00cc, // LATIN CAPITAL LETTER I WITH GRAVE
			0xee => 0x00d3, // LATIN CAPITAL LETTER O WITH ACUTE
			0xef => 0x00d4, // LATIN CAPITAL LETTER O WITH CIRCUMFLEX
			0xf0 => null,   // UNDEFINED
			0xf1 => 0x00d2, // LATIN CAPITAL LETTER O WITH GRAVE
			0xf2 => 0x00da, // LATIN CAPITAL LETTER U WITH ACUTE
			0xf3 => 0x00db, // LATIN CAPITAL LETTER U WITH CIRCUMFLEX
			0xf4 => 0x00d9, // LATIN CAPITAL LETTER U WITH GRAVE
			0xf5 => 0x0131, // LATIN SMALL LETTER DOTLESS I
			0xf6 => 0x02c6, // MODIFIER LETTER CIRCUMFLEX ACCENT
			0xf7 => 0x02dc, // SMALL TILDE
			0xf8 => 0x00af, // MACRON
			0xf9 => 0x02d8, // BREVE
			0xfa => 0x02d9, // DOT ABOVE
			0xfb => 0x02da, // RING ABOVE
			0xfc => 0x00b8, // CEDILLA
			0xfd => 0x02dd, // DOUBLE ACUTE ACCENT
			0xfe => 0x02db, // OGONEK
			0xff => 0x02c7  // CARON
		);
	}
} // END OF Encoding_MacIceland

?>
