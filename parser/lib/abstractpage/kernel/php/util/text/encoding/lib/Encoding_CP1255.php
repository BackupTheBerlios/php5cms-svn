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
 
class Encoding_CP1255 extends Encoding
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encoding_CP1255()
	{
		$this->Encoding( "CP1255" );
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
			0x88 => 0x02c6, // MODIFIER LETTER CIRCUMFLEX ACCENT
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
			0x98 => 0x02dc, // SMALL TILDE
			0x99 => 0x2122, // TRADE MARK SIGN
			0x9a => null,   // UNDEFINED
			0x9b => 0x203a, // SINGLE RIGHT-POINTING ANGLE QUOTATION MARK
			0x9c => null,   // UNDEFINED
			0x9d => null,   // UNDEFINED
			0x9e => null,   // UNDEFINED
			0x9f => null,   // UNDEFINED
			0xa4 => 0x20aa, // NEW SHEQEL SIGN
			0xaa => 0x00d7, // MULTIPLICATION SIGN
			0xba => 0x00f7, // DIVISION SIGN
			0xc0 => 0x05b0, // HEBREW POINT SHEVA
			0xc1 => 0x05b1, // HEBREW POINT HATAF SEGOL
			0xc2 => 0x05b2, // HEBREW POINT HATAF PATAH
			0xc3 => 0x05b3, // HEBREW POINT HATAF QAMATS
			0xc4 => 0x05b4, // HEBREW POINT HIRIQ
			0xc5 => 0x05b5, // HEBREW POINT TSERE
			0xc6 => 0x05b6, // HEBREW POINT SEGOL
			0xc7 => 0x05b7, // HEBREW POINT PATAH
			0xc8 => 0x05b8, // HEBREW POINT QAMATS
			0xc9 => 0x05b9, // HEBREW POINT HOLAM
			0xca => null,   // UNDEFINED
			0xcb => 0x05bb, // HEBREW POINT QUBUTS
			0xcc => 0x05bc, // HEBREW POINT DAGESH OR MAPIQ
			0xcd => 0x05bd, // HEBREW POINT METEG
			0xce => 0x05be, // HEBREW PUNCTUATION MAQAF
			0xcf => 0x05bf, // HEBREW POINT RAFE
			0xd0 => 0x05c0, // HEBREW PUNCTUATION PASEQ
			0xd1 => 0x05c1, // HEBREW POINT SHIN DOT
			0xd2 => 0x05c2, // HEBREW POINT SIN DOT
			0xd3 => 0x05c3, // HEBREW PUNCTUATION SOF PASUQ
			0xd4 => 0x05f0, // HEBREW LIGATURE YIDDISH DOUBLE VAV
			0xd5 => 0x05f1, // HEBREW LIGATURE YIDDISH VAV YOD
			0xd6 => 0x05f2, // HEBREW LIGATURE YIDDISH DOUBLE YOD
			0xd7 => 0x05f3, // HEBREW PUNCTUATION GERESH
			0xd8 => 0x05f4, // HEBREW PUNCTUATION GERSHAYIM
			0xd9 => null,   // UNDEFINED
			0xda => null,   // UNDEFINED
			0xdb => null,   // UNDEFINED
			0xdc => null,   // UNDEFINED
			0xdd => null,   // UNDEFINED
			0xde => null,   // UNDEFINED
			0xdf => null,   // UNDEFINED
			0xe0 => 0x05d0, // HEBREW LETTER ALEF
			0xe1 => 0x05d1, // HEBREW LETTER BET
			0xe2 => 0x05d2, // HEBREW LETTER GIMEL
			0xe3 => 0x05d3, // HEBREW LETTER DALET
			0xe4 => 0x05d4, // HEBREW LETTER HE
			0xe5 => 0x05d5, // HEBREW LETTER VAV
			0xe6 => 0x05d6, // HEBREW LETTER ZAYIN
			0xe7 => 0x05d7, // HEBREW LETTER HET
			0xe8 => 0x05d8, // HEBREW LETTER TET
			0xe9 => 0x05d9, // HEBREW LETTER YOD
			0xea => 0x05da, // HEBREW LETTER FINAL KAF
			0xeb => 0x05db, // HEBREW LETTER KAF
			0xec => 0x05dc, // HEBREW LETTER LAMED
			0xed => 0x05dd, // HEBREW LETTER FINAL MEM
			0xee => 0x05de, // HEBREW LETTER MEM
			0xef => 0x05df, // HEBREW LETTER FINAL NUN
			0xf0 => 0x05e0, // HEBREW LETTER NUN
			0xf1 => 0x05e1, // HEBREW LETTER SAMEKH
			0xf2 => 0x05e2, // HEBREW LETTER AYIN
			0xf3 => 0x05e3, // HEBREW LETTER FINAL PE
			0xf4 => 0x05e4, // HEBREW LETTER PE
			0xf5 => 0x05e5, // HEBREW LETTER FINAL TSADI
			0xf6 => 0x05e6, // HEBREW LETTER TSADI
			0xf7 => 0x05e7, // HEBREW LETTER QOF
			0xf8 => 0x05e8, // HEBREW LETTER RESH
			0xf9 => 0x05e9, // HEBREW LETTER SHIN
			0xfa => 0x05ea, // HEBREW LETTER TAV
			0xfb => null,   // UNDEFINED
			0xfc => null,   // UNDEFINED
			0xfd => 0x200e, // LEFT-TO-RIGHT MARK
			0xfe => 0x200f, // RIGHT-TO-LEFT MARK
			0xff => null    // UNDEFINED
		);
	}
} // END OF Encoding_CP1255

?>
