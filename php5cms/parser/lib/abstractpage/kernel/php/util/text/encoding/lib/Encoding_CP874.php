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
 
class Encoding_CP874 extends Encoding
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encoding_CP874()
	{
		$this->Encoding( "CP874" );
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
			0x82 => null,   // UNDEFINED
			0x83 => null,   // UNDEFINED
			0x84 => null,   // UNDEFINED
			0x85 => 0x2026, // HORIZONTAL ELLIPSIS
			0x86 => null,   // UNDEFINED
			0x87 => null,   // UNDEFINED
			0x88 => null,   // UNDEFINED
			0x89 => null,   // UNDEFINED
			0x8a => null,   // UNDEFINED
			0x8b => null,   // UNDEFINED
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
			0x99 => null,   // UNDEFINED
			0x9a => null,   // UNDEFINED
			0x9b => null,   // UNDEFINED
			0x9c => null,   // UNDEFINED
			0x9d => null,   // UNDEFINED
			0x9e => null,   // UNDEFINED
			0x9f => null,   // UNDEFINED
			0xa1 => 0x0e01, // THAI CHARACTER KO KAI
			0xa2 => 0x0e02, // THAI CHARACTER KHO KHAI
			0xa3 => 0x0e03, // THAI CHARACTER KHO KHUAT
			0xa4 => 0x0e04, // THAI CHARACTER KHO KHWAI
			0xa5 => 0x0e05, // THAI CHARACTER KHO KHON
			0xa6 => 0x0e06, // THAI CHARACTER KHO RAKHANG
			0xa7 => 0x0e07, // THAI CHARACTER NGO NGU
			0xa8 => 0x0e08, // THAI CHARACTER CHO CHAN
			0xa9 => 0x0e09, // THAI CHARACTER CHO CHING
			0xaa => 0x0e0a, // THAI CHARACTER CHO CHANG
			0xab => 0x0e0b, // THAI CHARACTER SO SO
			0xac => 0x0e0c, // THAI CHARACTER CHO CHOE
			0xad => 0x0e0d, // THAI CHARACTER YO YING
			0xae => 0x0e0e, // THAI CHARACTER DO CHADA
			0xaf => 0x0e0f, // THAI CHARACTER TO PATAK
			0xb0 => 0x0e10, // THAI CHARACTER THO THAN
			0xb1 => 0x0e11, // THAI CHARACTER THO NANGMONTHO
			0xb2 => 0x0e12, // THAI CHARACTER THO PHUTHAO
			0xb3 => 0x0e13, // THAI CHARACTER NO NEN
			0xb4 => 0x0e14, // THAI CHARACTER DO DEK
			0xb5 => 0x0e15, // THAI CHARACTER TO TAO
			0xb6 => 0x0e16, // THAI CHARACTER THO THUNG
			0xb7 => 0x0e17, // THAI CHARACTER THO THAHAN
			0xb8 => 0x0e18, // THAI CHARACTER THO THONG
			0xb9 => 0x0e19, // THAI CHARACTER NO NU
			0xba => 0x0e1a, // THAI CHARACTER BO BAIMAI
			0xbb => 0x0e1b, // THAI CHARACTER PO PLA
			0xbc => 0x0e1c, // THAI CHARACTER PHO PHUNG
			0xbd => 0x0e1d, // THAI CHARACTER FO FA
			0xbe => 0x0e1e, // THAI CHARACTER PHO PHAN
			0xbf => 0x0e1f, // THAI CHARACTER FO FAN
			0xc0 => 0x0e20, // THAI CHARACTER PHO SAMPHAO
			0xc1 => 0x0e21, // THAI CHARACTER MO MA
			0xc2 => 0x0e22, // THAI CHARACTER YO YAK
			0xc3 => 0x0e23, // THAI CHARACTER RO RUA
			0xc4 => 0x0e24, // THAI CHARACTER RU
			0xc5 => 0x0e25, // THAI CHARACTER LO LING
			0xc6 => 0x0e26, // THAI CHARACTER LU
			0xc7 => 0x0e27, // THAI CHARACTER WO WAEN
			0xc8 => 0x0e28, // THAI CHARACTER SO SALA
			0xc9 => 0x0e29, // THAI CHARACTER SO RUSI
			0xca => 0x0e2a, // THAI CHARACTER SO SUA
			0xcb => 0x0e2b, // THAI CHARACTER HO HIP
			0xcc => 0x0e2c, // THAI CHARACTER LO CHULA
			0xcd => 0x0e2d, // THAI CHARACTER O ANG
			0xce => 0x0e2e, // THAI CHARACTER HO NOKHUK
			0xcf => 0x0e2f, // THAI CHARACTER PAIYANNOI
			0xd0 => 0x0e30, // THAI CHARACTER SARA A
			0xd1 => 0x0e31, // THAI CHARACTER MAI HAN-AKAT
			0xd2 => 0x0e32, // THAI CHARACTER SARA AA
			0xd3 => 0x0e33, // THAI CHARACTER SARA AM
			0xd4 => 0x0e34, // THAI CHARACTER SARA I
			0xd5 => 0x0e35, // THAI CHARACTER SARA II
			0xd6 => 0x0e36, // THAI CHARACTER SARA UE
			0xd7 => 0x0e37, // THAI CHARACTER SARA UEE
			0xd8 => 0x0e38, // THAI CHARACTER SARA U
			0xd9 => 0x0e39, // THAI CHARACTER SARA UU
			0xda => 0x0e3a, // THAI CHARACTER PHINTHU
			0xdb => null,   // UNDEFINED
			0xdc => null,   // UNDEFINED
			0xdd => null,   // UNDEFINED
			0xde => null,   // UNDEFINED
			0xdf => 0x0e3f, // THAI CURRENCY SYMBOL BAHT
			0xe0 => 0x0e40, // THAI CHARACTER SARA E
			0xe1 => 0x0e41, // THAI CHARACTER SARA AE
			0xe2 => 0x0e42, // THAI CHARACTER SARA O
			0xe3 => 0x0e43, // THAI CHARACTER SARA AI MAIMUAN
			0xe4 => 0x0e44, // THAI CHARACTER SARA AI MAIMALAI
			0xe5 => 0x0e45, // THAI CHARACTER LAKKHANGYAO
			0xe6 => 0x0e46, // THAI CHARACTER MAIYAMOK
			0xe7 => 0x0e47, // THAI CHARACTER MAITAIKHU
			0xe8 => 0x0e48, // THAI CHARACTER MAI EK
			0xe9 => 0x0e49, // THAI CHARACTER MAI THO
			0xea => 0x0e4a, // THAI CHARACTER MAI TRI
			0xeb => 0x0e4b, // THAI CHARACTER MAI CHATTAWA
			0xec => 0x0e4c, // THAI CHARACTER THANTHAKHAT
			0xed => 0x0e4d, // THAI CHARACTER NIKHAHIT
			0xee => 0x0e4e, // THAI CHARACTER YAMAKKAN
			0xef => 0x0e4f, // THAI CHARACTER FONGMAN
			0xf0 => 0x0e50, // THAI DIGIT ZERO
			0xf1 => 0x0e51, // THAI DIGIT ONE
			0xf2 => 0x0e52, // THAI DIGIT TWO
			0xf3 => 0x0e53, // THAI DIGIT THREE
			0xf4 => 0x0e54, // THAI DIGIT FOUR
			0xf5 => 0x0e55, // THAI DIGIT FIVE
			0xf6 => 0x0e56, // THAI DIGIT SIX
			0xf7 => 0x0e57, // THAI DIGIT SEVEN
			0xf8 => 0x0e58, // THAI DIGIT EIGHT
			0xf9 => 0x0e59, // THAI DIGIT NINE
			0xfa => 0x0e5a, // THAI CHARACTER ANGKHANKHU
			0xfb => 0x0e5b, // THAI CHARACTER KHOMUT
			0xfc => null,   // UNDEFINED
			0xfd => null,   // UNDEFINED
			0xfe => null,   // UNDEFINED
			0xff => null    // UNDEFINED
		);
	}
} // END OF Encoding_CP874

?>
