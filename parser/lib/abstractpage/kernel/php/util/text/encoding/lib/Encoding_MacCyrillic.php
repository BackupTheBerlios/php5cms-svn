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
 
class Encoding_MacCyrillic extends Encoding
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encoding_MacCyrillic()
	{
		$this->Encoding( "MacCyrillic" );
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populateEncodingMap()
	{
		$this->encoding_map = array(
			0x80 => 0x0410, // CYRILLIC CAPITAL LETTER A
			0x81 => 0x0411, // CYRILLIC CAPITAL LETTER BE
			0x82 => 0x0412, // CYRILLIC CAPITAL LETTER VE
			0x83 => 0x0413, // CYRILLIC CAPITAL LETTER GHE
			0x84 => 0x0414, // CYRILLIC CAPITAL LETTER DE
			0x85 => 0x0415, // CYRILLIC CAPITAL LETTER IE
			0x86 => 0x0416, // CYRILLIC CAPITAL LETTER ZHE
			0x87 => 0x0417, // CYRILLIC CAPITAL LETTER ZE
			0x88 => 0x0418, // CYRILLIC CAPITAL LETTER I
			0x89 => 0x0419, // CYRILLIC CAPITAL LETTER SHORT I
			0x8a => 0x041a, // CYRILLIC CAPITAL LETTER KA
			0x8b => 0x041b, // CYRILLIC CAPITAL LETTER EL
			0x8c => 0x041c, // CYRILLIC CAPITAL LETTER EM
			0x8d => 0x041d, // CYRILLIC CAPITAL LETTER EN
			0x8e => 0x041e, // CYRILLIC CAPITAL LETTER O
			0x8f => 0x041f, // CYRILLIC CAPITAL LETTER PE
			0x90 => 0x0420, // CYRILLIC CAPITAL LETTER ER
			0x91 => 0x0421, // CYRILLIC CAPITAL LETTER ES
			0x92 => 0x0422, // CYRILLIC CAPITAL LETTER TE
			0x93 => 0x0423, // CYRILLIC CAPITAL LETTER U
			0x94 => 0x0424, // CYRILLIC CAPITAL LETTER EF
			0x95 => 0x0425, // CYRILLIC CAPITAL LETTER HA
			0x96 => 0x0426, // CYRILLIC CAPITAL LETTER TSE
			0x97 => 0x0427, // CYRILLIC CAPITAL LETTER CHE
			0x98 => 0x0428, // CYRILLIC CAPITAL LETTER SHA
			0x99 => 0x0429, // CYRILLIC CAPITAL LETTER SHCHA
			0x9a => 0x042a, // CYRILLIC CAPITAL LETTER HARD SIGN
			0x9b => 0x042b, // CYRILLIC CAPITAL LETTER YERU
			0x9c => 0x042c, // CYRILLIC CAPITAL LETTER SOFT SIGN
			0x9d => 0x042d, // CYRILLIC CAPITAL LETTER E
			0x9e => 0x042e, // CYRILLIC CAPITAL LETTER YU
			0x9f => 0x042f, // CYRILLIC CAPITAL LETTER YA
			0xa0 => 0x2020, // DAGGER
			0xa1 => 0x00b0, // DEGREE SIGN
			0xa4 => 0x00a7, // SECTION SIGN
			0xa5 => 0x2022, // BULLET
			0xa6 => 0x00b6, // PILCROW SIGN
			0xa7 => 0x0406, // CYRILLIC CAPITAL LETTER BYELORUSSIAN-UKRAINIAN I
			0xa8 => 0x00ae, // REGISTERED SIGN
			0xaa => 0x2122, // TRADE MARK SIGN
			0xab => 0x0402, // CYRILLIC CAPITAL LETTER DJE
			0xac => 0x0452, // CYRILLIC SMALL LETTER DJE
			0xad => 0x2260, // NOT EQUAL TO
			0xae => 0x0403, // CYRILLIC CAPITAL LETTER GJE
			0xaf => 0x0453, // CYRILLIC SMALL LETTER GJE
			0xb0 => 0x221e, // INFINITY
			0xb2 => 0x2264, // LESS-THAN OR EQUAL TO
			0xb3 => 0x2265, // GREATER-THAN OR EQUAL TO
			0xb4 => 0x0456, // CYRILLIC SMALL LETTER BYELORUSSIAN-UKRAINIAN I
			0xb6 => 0x2202, // PARTIAL DIFFERENTIAL
			0xb7 => 0x0408, // CYRILLIC CAPITAL LETTER JE
			0xb8 => 0x0404, // CYRILLIC CAPITAL LETTER UKRAINIAN IE
			0xb9 => 0x0454, // CYRILLIC SMALL LETTER UKRAINIAN IE
			0xba => 0x0407, // CYRILLIC CAPITAL LETTER YI
			0xbb => 0x0457, // CYRILLIC SMALL LETTER YI
			0xbc => 0x0409, // CYRILLIC CAPITAL LETTER LJE
			0xbd => 0x0459, // CYRILLIC SMALL LETTER LJE
			0xbe => 0x040a, // CYRILLIC CAPITAL LETTER NJE
			0xbf => 0x045a, // CYRILLIC SMALL LETTER NJE
			0xc0 => 0x0458, // CYRILLIC SMALL LETTER JE
			0xc1 => 0x0405, // CYRILLIC CAPITAL LETTER DZE
			0xc2 => 0x00ac, // NOT SIGN
			0xc3 => 0x221a, // SQUARE ROOT
			0xc4 => 0x0192, // LATIN SMALL LETTER F WITH HOOK
			0xc5 => 0x2248, // ALMOST EQUAL TO
			0xc6 => 0x2206, // INCREMENT
			0xc7 => 0x00ab, // LEFT-POINTING DOUBLE ANGLE QUOTATION MARK
			0xc8 => 0x00bb, // RIGHT-POINTING DOUBLE ANGLE QUOTATION MARK
			0xc9 => 0x2026, // HORIZONTAL ELLIPSIS
			0xca => 0x00a0, // NO-BREAK SPACE
			0xcb => 0x040b, // CYRILLIC CAPITAL LETTER TSHE
			0xcc => 0x045b, // CYRILLIC SMALL LETTER TSHE
			0xcd => 0x040c, // CYRILLIC CAPITAL LETTER KJE
			0xce => 0x045c, // CYRILLIC SMALL LETTER KJE
			0xcf => 0x0455, // CYRILLIC SMALL LETTER DZE
			0xd0 => 0x2013, // EN DASH
			0xd1 => 0x2014, // EM DASH
			0xd2 => 0x201c, // LEFT DOUBLE QUOTATION MARK
			0xd3 => 0x201d, // RIGHT DOUBLE QUOTATION MARK
			0xd4 => 0x2018, // LEFT SINGLE QUOTATION MARK
			0xd5 => 0x2019, // RIGHT SINGLE QUOTATION MARK
			0xd6 => 0x00f7, // DIVISION SIGN
			0xd7 => 0x201e, // DOUBLE LOW-9 QUOTATION MARK
			0xd8 => 0x040e, // CYRILLIC CAPITAL LETTER SHORT U
			0xd9 => 0x045e, // CYRILLIC SMALL LETTER SHORT U
			0xda => 0x040f, // CYRILLIC CAPITAL LETTER DZHE
			0xdb => 0x045f, // CYRILLIC SMALL LETTER DZHE
			0xdc => 0x2116, // NUMERO SIGN
			0xdd => 0x0401, // CYRILLIC CAPITAL LETTER IO
			0xde => 0x0451, // CYRILLIC SMALL LETTER IO
			0xdf => 0x044f, // CYRILLIC SMALL LETTER YA
			0xe0 => 0x0430, // CYRILLIC SMALL LETTER A
			0xe1 => 0x0431, // CYRILLIC SMALL LETTER BE
			0xe2 => 0x0432, // CYRILLIC SMALL LETTER VE
			0xe3 => 0x0433, // CYRILLIC SMALL LETTER GHE
			0xe4 => 0x0434, // CYRILLIC SMALL LETTER DE
			0xe5 => 0x0435, // CYRILLIC SMALL LETTER IE
			0xe6 => 0x0436, // CYRILLIC SMALL LETTER ZHE
			0xe7 => 0x0437, // CYRILLIC SMALL LETTER ZE
			0xe8 => 0x0438, // CYRILLIC SMALL LETTER I
			0xe9 => 0x0439, // CYRILLIC SMALL LETTER SHORT I
			0xea => 0x043a, // CYRILLIC SMALL LETTER KA
			0xeb => 0x043b, // CYRILLIC SMALL LETTER EL
			0xec => 0x043c, // CYRILLIC SMALL LETTER EM
			0xed => 0x043d, // CYRILLIC SMALL LETTER EN
			0xee => 0x043e, // CYRILLIC SMALL LETTER O
			0xef => 0x043f, // CYRILLIC SMALL LETTER PE
			0xf0 => 0x0440, // CYRILLIC SMALL LETTER ER
			0xf1 => 0x0441, // CYRILLIC SMALL LETTER ES
			0xf2 => 0x0442, // CYRILLIC SMALL LETTER TE
			0xf3 => 0x0443, // CYRILLIC SMALL LETTER U
			0xf4 => 0x0444, // CYRILLIC SMALL LETTER EF
			0xf5 => 0x0445, // CYRILLIC SMALL LETTER HA
			0xf6 => 0x0446, // CYRILLIC SMALL LETTER TSE
			0xf7 => 0x0447, // CYRILLIC SMALL LETTER CHE
			0xf8 => 0x0448, // CYRILLIC SMALL LETTER SHA
			0xf9 => 0x0449, // CYRILLIC SMALL LETTER SHCHA
			0xfa => 0x044a, // CYRILLIC SMALL LETTER HARD SIGN
			0xfb => 0x044b, // CYRILLIC SMALL LETTER YERU
			0xfc => 0x044c, // CYRILLIC SMALL LETTER SOFT SIGN
			0xfd => 0x044d, // CYRILLIC SMALL LETTER E
			0xfe => 0x044e, // CYRILLIC SMALL LETTER YU
			0xff => 0x00a4  // CURRENCY SIGN
		);
	}
} // END OF Encoding_MacCyrillic

?>
