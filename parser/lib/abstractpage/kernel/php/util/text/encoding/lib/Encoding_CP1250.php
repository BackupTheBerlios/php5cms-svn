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
 
class Encoding_CP1250 extends Encoding
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encoding_CP1250()
	{
		$this->Encoding( "CP1250" );
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
			0x8a => 0x0160, // LATIN CAPITAL LETTER S WITH CARON
			0x8b => 0x2039, // SINGLE LEFT-POINTING ANGLE QUOTATION MARK
			0x8c => 0x015a, // LATIN CAPITAL LETTER S WITH ACUTE
			0x8d => 0x0164, // LATIN CAPITAL LETTER T WITH CARON
			0x8e => 0x017d, // LATIN CAPITAL LETTER Z WITH CARON
			0x8f => 0x0179, // LATIN CAPITAL LETTER Z WITH ACUTE
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
			0x9a => 0x0161, // LATIN SMALL LETTER S WITH CARON
			0x9b => 0x203a, // SINGLE RIGHT-POINTING ANGLE QUOTATION MARK
			0x9c => 0x015b, // LATIN SMALL LETTER S WITH ACUTE
			0x9d => 0x0165, // LATIN SMALL LETTER T WITH CARON
			0x9e => 0x017e, // LATIN SMALL LETTER Z WITH CARON
			0x9f => 0x017a, // LATIN SMALL LETTER Z WITH ACUTE
			0xa1 => 0x02c7, // CARON
			0xa2 => 0x02d8, // BREVE
			0xa3 => 0x0141, // LATIN CAPITAL LETTER L WITH STROKE
			0xa5 => 0x0104, // LATIN CAPITAL LETTER A WITH OGONEK
			0xaa => 0x015e, // LATIN CAPITAL LETTER S WITH CEDILLA
			0xaf => 0x017b, // LATIN CAPITAL LETTER Z WITH DOT ABOVE
			0xb2 => 0x02db, // OGONEK
			0xb3 => 0x0142, // LATIN SMALL LETTER L WITH STROKE
			0xb9 => 0x0105, // LATIN SMALL LETTER A WITH OGONEK
			0xba => 0x015f, // LATIN SMALL LETTER S WITH CEDILLA
			0xbc => 0x013d, // LATIN CAPITAL LETTER L WITH CARON
			0xbd => 0x02dd, // DOUBLE ACUTE ACCENT
			0xbe => 0x013e, // LATIN SMALL LETTER L WITH CARON
			0xbf => 0x017c, // LATIN SMALL LETTER Z WITH DOT ABOVE
			0xc0 => 0x0154, // LATIN CAPITAL LETTER R WITH ACUTE
			0xc3 => 0x0102, // LATIN CAPITAL LETTER A WITH BREVE
			0xc5 => 0x0139, // LATIN CAPITAL LETTER L WITH ACUTE
			0xc6 => 0x0106, // LATIN CAPITAL LETTER C WITH ACUTE
			0xc8 => 0x010c, // LATIN CAPITAL LETTER C WITH CARON
			0xca => 0x0118, // LATIN CAPITAL LETTER E WITH OGONEK
			0xcc => 0x011a, // LATIN CAPITAL LETTER E WITH CARON
			0xcf => 0x010e, // LATIN CAPITAL LETTER D WITH CARON
			0xd0 => 0x0110, // LATIN CAPITAL LETTER D WITH STROKE
			0xd1 => 0x0143, // LATIN CAPITAL LETTER N WITH ACUTE
			0xd2 => 0x0147, // LATIN CAPITAL LETTER N WITH CARON
			0xd5 => 0x0150, // LATIN CAPITAL LETTER O WITH DOUBLE ACUTE
			0xd8 => 0x0158, // LATIN CAPITAL LETTER R WITH CARON
			0xd9 => 0x016e, // LATIN CAPITAL LETTER U WITH RING ABOVE
			0xdb => 0x0170, // LATIN CAPITAL LETTER U WITH DOUBLE ACUTE
			0xde => 0x0162, // LATIN CAPITAL LETTER T WITH CEDILLA
			0xe0 => 0x0155, // LATIN SMALL LETTER R WITH ACUTE
			0xe3 => 0x0103, // LATIN SMALL LETTER A WITH BREVE
			0xe5 => 0x013a, // LATIN SMALL LETTER L WITH ACUTE
			0xe6 => 0x0107, // LATIN SMALL LETTER C WITH ACUTE
			0xe8 => 0x010d, // LATIN SMALL LETTER C WITH CARON
			0xea => 0x0119, // LATIN SMALL LETTER E WITH OGONEK
			0xec => 0x011b, // LATIN SMALL LETTER E WITH CARON
			0xef => 0x010f, // LATIN SMALL LETTER D WITH CARON
			0xf0 => 0x0111, // LATIN SMALL LETTER D WITH STROKE
			0xf1 => 0x0144, // LATIN SMALL LETTER N WITH ACUTE
			0xf2 => 0x0148, // LATIN SMALL LETTER N WITH CARON
			0xf5 => 0x0151, // LATIN SMALL LETTER O WITH DOUBLE ACUTE
			0xf8 => 0x0159, // LATIN SMALL LETTER R WITH CARON
			0xf9 => 0x016f, // LATIN SMALL LETTER U WITH RING ABOVE
			0xfb => 0x0171, // LATIN SMALL LETTER U WITH DOUBLE ACUTE
			0xfe => 0x0163, // LATIN SMALL LETTER T WITH CEDILLA
			0xff => 0x02d9  // DOT ABOVE
		);
	}
} // END OF Encoding_CP1250

?>
