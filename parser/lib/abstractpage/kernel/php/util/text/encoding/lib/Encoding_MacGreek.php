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
 
class Encoding_MacGreek extends Encoding
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encoding_MacGreek()
	{
		$this->Encoding( "MacGreek" );
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populateEncodingMap()
	{
		$this->encoding_map = array(
			0x80 => 0x00c4, // LATIN CAPITAL LETTER A WITH DIAERESIS
			0x81 => 0x00b9, // SUPERSCRIPT ONE
			0x82 => 0x00b2, // SUPERSCRIPT TWO
			0x83 => 0x00c9, // LATIN CAPITAL LETTER E WITH ACUTE
			0x84 => 0x00b3, // SUPERSCRIPT THREE
			0x85 => 0x00d6, // LATIN CAPITAL LETTER O WITH DIAERESIS
			0x86 => 0x00dc, // LATIN CAPITAL LETTER U WITH DIAERESIS
			0x87 => 0x0385, // GREEK DIALYTIKA TONOS
			0x88 => 0x00e0, // LATIN SMALL LETTER A WITH GRAVE
			0x89 => 0x00e2, // LATIN SMALL LETTER A WITH CIRCUMFLEX
			0x8a => 0x00e4, // LATIN SMALL LETTER A WITH DIAERESIS
			0x8b => 0x0384, // GREEK TONOS
			0x8c => 0x00a8, // DIAERESIS
			0x8d => 0x00e7, // LATIN SMALL LETTER C WITH CEDILLA
			0x8e => 0x00e9, // LATIN SMALL LETTER E WITH ACUTE
			0x8f => 0x00e8, // LATIN SMALL LETTER E WITH GRAVE
			0x90 => 0x00ea, // LATIN SMALL LETTER E WITH CIRCUMFLEX
			0x91 => 0x00eb, // LATIN SMALL LETTER E WITH DIAERESIS
			0x92 => 0x00a3, // POUND SIGN
			0x93 => 0x2122, // TRADE MARK SIGN
			0x94 => 0x00ee, // LATIN SMALL LETTER I WITH CIRCUMFLEX
			0x95 => 0x00ef, // LATIN SMALL LETTER I WITH DIAERESIS
			0x96 => 0x2022, // BULLET
			0x97 => 0x00bd, // VULGAR FRACTION ONE HALF
			0x98 => 0x2030, // PER MILLE SIGN
			0x99 => 0x00f4, // LATIN SMALL LETTER O WITH CIRCUMFLEX
			0x9a => 0x00f6, // LATIN SMALL LETTER O WITH DIAERESIS
			0x9b => 0x00a6, // BROKEN BAR
			0x9c => 0x00ad, // SOFT HYPHEN
			0x9d => 0x00f9, // LATIN SMALL LETTER U WITH GRAVE
			0x9e => 0x00fb, // LATIN SMALL LETTER U WITH CIRCUMFLEX
			0x9f => 0x00fc, // LATIN SMALL LETTER U WITH DIAERESIS
			0xa0 => 0x2020, // DAGGER
			0xa1 => 0x0393, // GREEK CAPITAL LETTER GAMMA
			0xa2 => 0x0394, // GREEK CAPITAL LETTER DELTA
			0xa3 => 0x0398, // GREEK CAPITAL LETTER THETA
			0xa4 => 0x039b, // GREEK CAPITAL LETTER LAMBDA
			0xa5 => 0x039e, // GREEK CAPITAL LETTER XI
			0xa6 => 0x03a0, // GREEK CAPITAL LETTER PI
			0xa7 => 0x00df, // LATIN SMALL LETTER SHARP S
			0xa8 => 0x00ae, // REGISTERED SIGN
			0xaa => 0x03a3, // GREEK CAPITAL LETTER SIGMA
			0xab => 0x03aa, // GREEK CAPITAL LETTER IOTA WITH DIALYTIKA
			0xac => 0x00a7, // SECTION SIGN
			0xad => 0x2260, // NOT EQUAL TO
			0xae => 0x00b0, // DEGREE SIGN
			0xaf => 0x0387, // GREEK ANO TELEIA
			0xb0 => 0x0391, // GREEK CAPITAL LETTER ALPHA
			0xb2 => 0x2264, // LESS-THAN OR EQUAL TO
			0xb3 => 0x2265, // GREATER-THAN OR EQUAL TO
			0xb4 => 0x00a5, // YEN SIGN
			0xb5 => 0x0392, // GREEK CAPITAL LETTER BETA
			0xb6 => 0x0395, // GREEK CAPITAL LETTER EPSILON
			0xb7 => 0x0396, // GREEK CAPITAL LETTER ZETA
			0xb8 => 0x0397, // GREEK CAPITAL LETTER ETA
			0xb9 => 0x0399, // GREEK CAPITAL LETTER IOTA
			0xba => 0x039a, // GREEK CAPITAL LETTER KAPPA
			0xbb => 0x039c, // GREEK CAPITAL LETTER MU
			0xbc => 0x03a6, // GREEK CAPITAL LETTER PHI
			0xbd => 0x03ab, // GREEK CAPITAL LETTER UPSILON WITH DIALYTIKA
			0xbe => 0x03a8, // GREEK CAPITAL LETTER PSI
			0xbf => 0x03a9, // GREEK CAPITAL LETTER OMEGA
			0xc0 => 0x03ac, // GREEK SMALL LETTER ALPHA WITH TONOS
			0xc1 => 0x039d, // GREEK CAPITAL LETTER NU
			0xc2 => 0x00ac, // NOT SIGN
			0xc3 => 0x039f, // GREEK CAPITAL LETTER OMICRON
			0xc4 => 0x03a1, // GREEK CAPITAL LETTER RHO
			0xc5 => 0x2248, // ALMOST EQUAL TO
			0xc6 => 0x03a4, // GREEK CAPITAL LETTER TAU
			0xc7 => 0x00ab, // LEFT-POINTING DOUBLE ANGLE QUOTATION MARK
			0xc8 => 0x00bb, // RIGHT-POINTING DOUBLE ANGLE QUOTATION MARK
			0xc9 => 0x2026, // HORIZONTAL ELLIPSIS
			0xca => 0x00a0, // NO-BREAK SPACE
			0xcb => 0x03a5, // GREEK CAPITAL LETTER UPSILON
			0xcc => 0x03a7, // GREEK CAPITAL LETTER CHI
			0xcd => 0x0386, // GREEK CAPITAL LETTER ALPHA WITH TONOS
			0xce => 0x0388, // GREEK CAPITAL LETTER EPSILON WITH TONOS
			0xcf => 0x0153, // LATIN SMALL LIGATURE OE
			0xd0 => 0x2013, // EN DASH
			0xd1 => 0x2015, // HORIZONTAL BAR
			0xd2 => 0x201c, // LEFT DOUBLE QUOTATION MARK
			0xd3 => 0x201d, // RIGHT DOUBLE QUOTATION MARK
			0xd4 => 0x2018, // LEFT SINGLE QUOTATION MARK
			0xd5 => 0x2019, // RIGHT SINGLE QUOTATION MARK
			0xd6 => 0x00f7, // DIVISION SIGN
			0xd7 => 0x0389, // GREEK CAPITAL LETTER ETA WITH TONOS
			0xd8 => 0x038a, // GREEK CAPITAL LETTER IOTA WITH TONOS
			0xd9 => 0x038c, // GREEK CAPITAL LETTER OMICRON WITH TONOS
			0xda => 0x038e, // GREEK CAPITAL LETTER UPSILON WITH TONOS
			0xdb => 0x03ad, // GREEK SMALL LETTER EPSILON WITH TONOS
			0xdc => 0x03ae, // GREEK SMALL LETTER ETA WITH TONOS
			0xdd => 0x03af, // GREEK SMALL LETTER IOTA WITH TONOS
			0xde => 0x03cc, // GREEK SMALL LETTER OMICRON WITH TONOS
			0xdf => 0x038f, // GREEK CAPITAL LETTER OMEGA WITH TONOS
			0xe0 => 0x03cd, // GREEK SMALL LETTER UPSILON WITH TONOS
			0xe1 => 0x03b1, // GREEK SMALL LETTER ALPHA
			0xe2 => 0x03b2, // GREEK SMALL LETTER BETA
			0xe3 => 0x03c8, // GREEK SMALL LETTER PSI
			0xe4 => 0x03b4, // GREEK SMALL LETTER DELTA
			0xe5 => 0x03b5, // GREEK SMALL LETTER EPSILON
			0xe6 => 0x03c6, // GREEK SMALL LETTER PHI
			0xe7 => 0x03b3, // GREEK SMALL LETTER GAMMA
			0xe8 => 0x03b7, // GREEK SMALL LETTER ETA
			0xe9 => 0x03b9, // GREEK SMALL LETTER IOTA
			0xea => 0x03be, // GREEK SMALL LETTER XI
			0xeb => 0x03ba, // GREEK SMALL LETTER KAPPA
			0xec => 0x03bb, // GREEK SMALL LETTER LAMBDA
			0xed => 0x03bc, // GREEK SMALL LETTER MU
			0xee => 0x03bd, // GREEK SMALL LETTER NU
			0xef => 0x03bf, // GREEK SMALL LETTER OMICRON
			0xf0 => 0x03c0, // GREEK SMALL LETTER PI
			0xf1 => 0x03ce, // GREEK SMALL LETTER OMEGA WITH TONOS
			0xf2 => 0x03c1, // GREEK SMALL LETTER RHO
			0xf3 => 0x03c3, // GREEK SMALL LETTER SIGMA
			0xf4 => 0x03c4, // GREEK SMALL LETTER TAU
			0xf5 => 0x03b8, // GREEK SMALL LETTER THETA
			0xf6 => 0x03c9, // GREEK SMALL LETTER OMEGA
			0xf7 => 0x03c2, // GREEK SMALL LETTER FINAL SIGMA
			0xf8 => 0x03c7, // GREEK SMALL LETTER CHI
			0xf9 => 0x03c5, // GREEK SMALL LETTER UPSILON
			0xfa => 0x03b6, // GREEK SMALL LETTER ZETA
			0xfb => 0x03ca, // GREEK SMALL LETTER IOTA WITH DIALYTIKA
			0xfc => 0x03cb, // GREEK SMALL LETTER UPSILON WITH DIALYTIKA
			0xfd => 0x0390, // GREEK SMALL LETTER IOTA WITH DIALYTIKA AND TONOS
			0xfe => 0x03b0, // GREEK SMALL LETTER UPSILON WITH DIALYTIKA AND TONOS
			0xff => null    // UNDEFINED
		);
	}
} // END OF Encoding_MacGreek

?>
