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
 
class Encoding_CP856 extends Encoding
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encoding_CP856()
	{
		$this->Encoding( "CP856" );
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populateEncodingMap()
	{
		$this->encoding_map = array(
			0x80 => 0x05d0, // HEBREW LETTER ALEF
			0x81 => 0x05d1, // HEBREW LETTER BET
			0x82 => 0x05d2, // HEBREW LETTER GIMEL
			0x83 => 0x05d3, // HEBREW LETTER DALET
			0x84 => 0x05d4, // HEBREW LETTER HE
			0x85 => 0x05d5, // HEBREW LETTER VAV
			0x86 => 0x05d6, // HEBREW LETTER ZAYIN
			0x87 => 0x05d7, // HEBREW LETTER HET
			0x88 => 0x05d8, // HEBREW LETTER TET
			0x89 => 0x05d9, // HEBREW LETTER YOD
			0x8a => 0x05da, // HEBREW LETTER FINAL KAF
			0x8b => 0x05db, // HEBREW LETTER KAF
			0x8c => 0x05dc, // HEBREW LETTER LAMED
			0x8d => 0x05dd, // HEBREW LETTER FINAL MEM
			0x8e => 0x05de, // HEBREW LETTER MEM
			0x8f => 0x05df, // HEBREW LETTER FINAL NUN
			0x90 => 0x05e0, // HEBREW LETTER NUN
			0x91 => 0x05e1, // HEBREW LETTER SAMEKH
			0x92 => 0x05e2, // HEBREW LETTER AYIN
			0x93 => 0x05e3, // HEBREW LETTER FINAL PE
			0x94 => 0x05e4, // HEBREW LETTER PE
			0x95 => 0x05e5, // HEBREW LETTER FINAL TSADI
			0x96 => 0x05e6, // HEBREW LETTER TSADI
			0x97 => 0x05e7, // HEBREW LETTER QOF
			0x98 => 0x05e8, // HEBREW LETTER RESH
			0x99 => 0x05e9, // HEBREW LETTER SHIN
			0x9a => 0x05ea, // HEBREW LETTER TAV
			0x9b => null,   // UNDEFINED
			0x9c => 0x00a3, // POUND SIGN
			0x9d => null,   // UNDEFINED
			0x9e => 0x00d7, // MULTIPLICATION SIGN
			0x9f => null,   // UNDEFINED
			0xa0 => null,   // UNDEFINED
			0xa1 => null,   // UNDEFINED
			0xa2 => null,   // UNDEFINED
			0xa3 => null,   // UNDEFINED
			0xa4 => null,   // UNDEFINED
			0xa5 => null,   // UNDEFINED
			0xa6 => null,   // UNDEFINED
			0xa7 => null,   // UNDEFINED
			0xa8 => null,   // UNDEFINED
			0xa9 => 0x00ae, // REGISTERED SIGN
			0xaa => 0x00ac, // NOT SIGN
			0xab => 0x00bd, // VULGAR FRACTION ONE HALF
			0xac => 0x00bc, // VULGAR FRACTION ONE QUARTER
			0xad => null,   // UNDEFINED
			0xae => 0x00ab, // LEFT-POINTING DOUBLE ANGLE QUOTATION MARK
			0xaf => 0x00bb, // RIGHT-POINTING DOUBLE ANGLE QUOTATION MARK
			0xb0 => 0x2591, // LIGHT SHADE
			0xb1 => 0x2592, // MEDIUM SHADE
			0xb2 => 0x2593, // DARK SHADE
			0xb3 => 0x2502, // BOX DRAWINGS LIGHT VERTICAL
			0xb4 => 0x2524, // BOX DRAWINGS LIGHT VERTICAL AND LEFT
			0xb5 => null,   // UNDEFINED
			0xb6 => null,   // UNDEFINED
			0xb7 => null,   // UNDEFINED
			0xb8 => 0x00a9, // COPYRIGHT SIGN
			0xb9 => 0x2563, // BOX DRAWINGS DOUBLE VERTICAL AND LEFT
			0xba => 0x2551, // BOX DRAWINGS DOUBLE VERTICAL
			0xbb => 0x2557, // BOX DRAWINGS DOUBLE DOWN AND LEFT
			0xbc => 0x255d, // BOX DRAWINGS DOUBLE UP AND LEFT
			0xbd => 0x00a2, // CENT SIGN
			0xbe => 0x00a5, // YEN SIGN
			0xbf => 0x2510, // BOX DRAWINGS LIGHT DOWN AND LEFT
			0xc0 => 0x2514, // BOX DRAWINGS LIGHT UP AND RIGHT
			0xc1 => 0x2534, // BOX DRAWINGS LIGHT UP AND HORIZONTAL
			0xc2 => 0x252c, // BOX DRAWINGS LIGHT DOWN AND HORIZONTAL
			0xc3 => 0x251c, // BOX DRAWINGS LIGHT VERTICAL AND RIGHT
			0xc4 => 0x2500, // BOX DRAWINGS LIGHT HORIZONTAL
			0xc5 => 0x253c, // BOX DRAWINGS LIGHT VERTICAL AND HORIZONTAL
			0xc6 => null,   // UNDEFINED
			0xc7 => null,   // UNDEFINED
			0xc8 => 0x255a, // BOX DRAWINGS DOUBLE UP AND RIGHT
			0xc9 => 0x2554, // BOX DRAWINGS DOUBLE DOWN AND RIGHT
			0xca => 0x2569, // BOX DRAWINGS DOUBLE UP AND HORIZONTAL
			0xcb => 0x2566, // BOX DRAWINGS DOUBLE DOWN AND HORIZONTAL
			0xcc => 0x2560, // BOX DRAWINGS DOUBLE VERTICAL AND RIGHT
			0xcd => 0x2550, // BOX DRAWINGS DOUBLE HORIZONTAL
			0xce => 0x256c, // BOX DRAWINGS DOUBLE VERTICAL AND HORIZONTAL
			0xcf => 0x00a4, // CURRENCY SIGN
			0xd0 => null,   // UNDEFINED
			0xd1 => null,   // UNDEFINED
			0xd2 => null,   // UNDEFINED
			0xd3 => null,   // UNDEFINEDS
			0xd4 => null,   // UNDEFINED
			0xd5 => null,   // UNDEFINED
			0xd6 => null,   // UNDEFINEDE
			0xd7 => null,   // UNDEFINED
			0xd8 => null,   // UNDEFINED
			0xd9 => 0x2518, // BOX DRAWINGS LIGHT UP AND LEFT
			0xda => 0x250c, // BOX DRAWINGS LIGHT DOWN AND RIGHT
			0xdb => 0x2588, // FULL BLOCK
			0xdc => 0x2584, // LOWER HALF BLOCK
			0xdd => 0x00a6, // BROKEN BAR
			0xde => null,   // UNDEFINED
			0xdf => 0x2580, // UPPER HALF BLOCK
			0xe0 => null,   // UNDEFINED
			0xe1 => null,   // UNDEFINED
			0xe2 => null,   // UNDEFINED
			0xe3 => null,   // UNDEFINED
			0xe4 => null,   // UNDEFINED
			0xe5 => null,   // UNDEFINED
			0xe6 => 0x00b5, // MICRO SIGN
			0xe7 => null,   // UNDEFINED
			0xe8 => null,   // UNDEFINED
			0xe9 => null,   // UNDEFINED
			0xea => null,   // UNDEFINED
			0xeb => null,   // UNDEFINED
			0xec => null,   // UNDEFINED
			0xed => null,   // UNDEFINED
			0xee => 0x00af, // MACRON
			0xef => 0x00b4, // ACUTE ACCENT
			0xf0 => 0x00ad, // SOFT HYPHEN
			0xf1 => 0x00b1, // PLUS-MINUS SIGN
			0xf2 => 0x2017, // DOUBLE LOW LINE
			0xf3 => 0x00be, // VULGAR FRACTION THREE QUARTERS
			0xf4 => 0x00b6, // PILCROW SIGN
			0xf5 => 0x00a7, // SECTION SIGN
			0xf6 => 0x00f7, // DIVISION SIGN
			0xf7 => 0x00b8, // CEDILLA
			0xf8 => 0x00b0, // DEGREE SIGN
			0xf9 => 0x00a8, // DIAERESIS
			0xfa => 0x00b7, // MIDDLE DOT
			0xfb => 0x00b9, // SUPERSCRIPT ONE
			0xfc => 0x00b3, // SUPERSCRIPT THREE
			0xfd => 0x00b2, // SUPERSCRIPT TWO
			0xfe => 0x25a0, // BLACK SQUARE
			0xff => 0x00a0  // NO-BREAK SPACE
		);
	}
} // END OF Encoding_CP856

?>
