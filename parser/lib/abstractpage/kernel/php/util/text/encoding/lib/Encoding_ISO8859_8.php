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
 
class Encoding_ISO8859_8 extends Encoding
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encoding_ISO8859_8()
	{
		$this->Encoding( "ISO8859-8" );
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populateEncodingMap()
	{
		$this->encoding_map = array(
			0xa1 => null,
			0xaa => 0x00d7, // MULTIPLICATION SIGN
			0xba => 0x00f7, // DIVISION SIGN
			0xbf => null,
			0xc0 => null,
			0xc1 => null,
			0xc2 => null,
			0xc3 => null,
			0xc4 => null,
			0xc5 => null,
			0xc6 => null,
			0xc7 => null,
			0xc8 => null,
			0xc9 => null,
			0xca => null,
			0xcb => null,
			0xcc => null,
			0xcd => null,
			0xce => null,
			0xcf => null,
			0xd0 => null,
			0xd1 => null,
			0xd2 => null,
			0xd3 => null,
			0xd4 => null,
			0xd5 => null,
			0xd6 => null,
			0xd7 => null,
			0xd8 => null,
			0xd9 => null,
			0xda => null,
			0xdb => null,
			0xdc => null,
			0xdd => null,
			0xde => null,
			0xdf => 0x2017, // DOUBLE LOW LINE
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
			0xfb => null,
			0xfc => null,
			0xfd => 0x200e, // LEFT-TO-RIGHT MARK
			0xfe => 0x200f, // RIGHT-TO-LEFT MARK
			0xff => null
		);
	}
} // END OF Encoding_ISO8859_8

?>
