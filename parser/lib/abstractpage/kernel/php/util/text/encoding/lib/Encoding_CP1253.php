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
 
class Encoding_CP1253 extends Encoding
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encoding_CP1253()
	{
		$this->Encoding( "CP1253" );
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
			0x83 => 0x0192, // LATIN SMALL LETTER F WITH HOOK
			0x84 => 0x201e, // DOUBLE LOW-9 QUOTATION MARK
			0x85 => 0x2026, // HORIZONTAL ELLIPSIS
			0x86 => 0x2020, // DAGGER
			0x87 => 0x2021, // DOUBLE DAGGER
			0x88 => null,   // UNDEFINED
			0x89 => 0x2030, // PER MILLE SIGN
			0x8a => null,   // UNDEFINED
			0x8b => 0x2039, // SINGLE LEFT-POINTING ANGLE QUOTATION MARK
			0x8c => null,   // UNDEFINED
			0x8d => null,   // UNDEFINED
			0x8e => null,   // UNDEFINED
			0x8f => null,   // UNDEFINED
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
			0x9d => null,   // UNDEFINED
			0x9e => null,   // UNDEFINED
			0x9f => null,   // UNDEFINED
			0xa1 => 0x0385, // GREEK DIALYTIKA TONOS
			0xa2 => 0x0386, // GREEK CAPITAL LETTER ALPHA WITH TONOS
			0xaa => null,   // UNDEFINED
			0xaf => 0x2015, // HORIZONTAL BAR
			0xb4 => 0x0384, // GREEK TONOS
			0xb8 => 0x0388, // GREEK CAPITAL LETTER EPSILON WITH TONOS
			0xb9 => 0x0389, // GREEK CAPITAL LETTER ETA WITH TONOS
			0xba => 0x038a, // GREEK CAPITAL LETTER IOTA WITH TONOS
			0xbc => 0x038c, // GREEK CAPITAL LETTER OMICRON WITH TONOS
			0xbe => 0x038e, // GREEK CAPITAL LETTER UPSILON WITH TONOS
			0xbf => 0x038f, // GREEK CAPITAL LETTER OMEGA WITH TONOS
			0xc0 => 0x0390, // GREEK SMALL LETTER IOTA WITH DIALYTIKA AND TONOS
			0xc1 => 0x0391, // GREEK CAPITAL LETTER ALPHA
			0xc2 => 0x0392, // GREEK CAPITAL LETTER BETA
			0xc3 => 0x0393, // GREEK CAPITAL LETTER GAMMA
			0xc4 => 0x0394, // GREEK CAPITAL LETTER DELTA
			0xc5 => 0x0395, // GREEK CAPITAL LETTER EPSILON
			0xc6 => 0x0396, // GREEK CAPITAL LETTER ZETA
			0xc7 => 0x0397, // GREEK CAPITAL LETTER ETA
			0xc8 => 0x0398, // GREEK CAPITAL LETTER THETA
			0xc9 => 0x0399, // GREEK CAPITAL LETTER IOTA
			0xca => 0x039a, // GREEK CAPITAL LETTER KAPPA
			0xcb => 0x039b, // GREEK CAPITAL LETTER LAMDA
			0xcc => 0x039c, // GREEK CAPITAL LETTER MU
			0xcd => 0x039d, // GREEK CAPITAL LETTER NU
			0xce => 0x039e, // GREEK CAPITAL LETTER XI
			0xcf => 0x039f, // GREEK CAPITAL LETTER OMICRON
			0xd0 => 0x03a0, // GREEK CAPITAL LETTER PI
			0xd1 => 0x03a1, // GREEK CAPITAL LETTER RHO
			0xd2 => null,   // UNDEFINED
			0xd3 => 0x03a3, // GREEK CAPITAL LETTER SIGMA
			0xd4 => 0x03a4, // GREEK CAPITAL LETTER TAU
			0xd5 => 0x03a5, // GREEK CAPITAL LETTER UPSILON
			0xd6 => 0x03a6, // GREEK CAPITAL LETTER PHI
			0xd7 => 0x03a7, // GREEK CAPITAL LETTER CHI
			0xd8 => 0x03a8, // GREEK CAPITAL LETTER PSI
			0xd9 => 0x03a9, // GREEK CAPITAL LETTER OMEGA
			0xda => 0x03aa, // GREEK CAPITAL LETTER IOTA WITH DIALYTIKA
			0xdb => 0x03ab, // GREEK CAPITAL LETTER UPSILON WITH DIALYTIKA
			0xdc => 0x03ac, // GREEK SMALL LETTER ALPHA WITH TONOS
			0xdd => 0x03ad, // GREEK SMALL LETTER EPSILON WITH TONOS
			0xde => 0x03ae, // GREEK SMALL LETTER ETA WITH TONOS
			0xdf => 0x03af, // GREEK SMALL LETTER IOTA WITH TONOS
			0xe0 => 0x03b0, // GREEK SMALL LETTER UPSILON WITH DIALYTIKA AND TONOS
			0xe1 => 0x03b1, // GREEK SMALL LETTER ALPHA
			0xe2 => 0x03b2, // GREEK SMALL LETTER BETA
			0xe3 => 0x03b3, // GREEK SMALL LETTER GAMMA
			0xe4 => 0x03b4, // GREEK SMALL LETTER DELTA
			0xe5 => 0x03b5, // GREEK SMALL LETTER EPSILON
			0xe6 => 0x03b6, // GREEK SMALL LETTER ZETA
			0xe7 => 0x03b7, // GREEK SMALL LETTER ETA
			0xe8 => 0x03b8, // GREEK SMALL LETTER THETA
			0xe9 => 0x03b9, // GREEK SMALL LETTER IOTA
			0xea => 0x03ba, // GREEK SMALL LETTER KAPPA
			0xeb => 0x03bb, // GREEK SMALL LETTER LAMDA
			0xec => 0x03bc, // GREEK SMALL LETTER MU
			0xed => 0x03bd, // GREEK SMALL LETTER NU
			0xee => 0x03be, // GREEK SMALL LETTER XI
			0xef => 0x03bf, // GREEK SMALL LETTER OMICRON
			0xf0 => 0x03c0, // GREEK SMALL LETTER PI
			0xf1 => 0x03c1, // GREEK SMALL LETTER RHO
			0xf2 => 0x03c2, // GREEK SMALL LETTER FINAL SIGMA
			0xf3 => 0x03c3, // GREEK SMALL LETTER SIGMA
			0xf4 => 0x03c4, // GREEK SMALL LETTER TAU
			0xf5 => 0x03c5, // GREEK SMALL LETTER UPSILON
			0xf6 => 0x03c6, // GREEK SMALL LETTER PHI
			0xf7 => 0x03c7, // GREEK SMALL LETTER CHI
			0xf8 => 0x03c8, // GREEK SMALL LETTER PSI
			0xf9 => 0x03c9, // GREEK SMALL LETTER OMEGA
			0xfa => 0x03ca, // GREEK SMALL LETTER IOTA WITH DIALYTIKA
			0xfb => 0x03cb, // GREEK SMALL LETTER UPSILON WITH DIALYTIKA
			0xfc => 0x03cc, // GREEK SMALL LETTER OMICRON WITH TONOS
			0xfd => 0x03cd, // GREEK SMALL LETTER UPSILON WITH TONOS
			0xfe => 0x03ce, // GREEK SMALL LETTER OMEGA WITH TONOS
			0xff => null    // UNDEFINED
		);
	}
} // END OF Encoding_CP1253

?>
