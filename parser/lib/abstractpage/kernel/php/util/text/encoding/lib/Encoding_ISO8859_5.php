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
 
class Encoding_ISO8859_5 extends Encoding
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encoding_ISO8859_5()
	{
		$this->Encoding( "ISO8859-5" );
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populateEncodingMap()
	{
		$this->encoding_map = array(
			0xa1 => 0x0401, // CYRILLIC CAPITAL LETTER IO
			0xa2 => 0x0402, // CYRILLIC CAPITAL LETTER DJE
			0xa3 => 0x0403, // CYRILLIC CAPITAL LETTER GJE
			0xa4 => 0x0404, // CYRILLIC CAPITAL LETTER UKRAINIAN IE
			0xa5 => 0x0405, // CYRILLIC CAPITAL LETTER DZE
			0xa6 => 0x0406, // CYRILLIC CAPITAL LETTER BYELORUSSIAN-UKRAINIAN I
			0xa7 => 0x0407, // CYRILLIC CAPITAL LETTER YI
			0xa8 => 0x0408, // CYRILLIC CAPITAL LETTER JE
			0xa9 => 0x0409, // CYRILLIC CAPITAL LETTER LJE
			0xaa => 0x040a, // CYRILLIC CAPITAL LETTER NJE
			0xab => 0x040b, // CYRILLIC CAPITAL LETTER TSHE
			0xac => 0x040c, // CYRILLIC CAPITAL LETTER KJE
			0xae => 0x040e, // CYRILLIC CAPITAL LETTER SHORT U
			0xaf => 0x040f, // CYRILLIC CAPITAL LETTER DZHE
			0xb0 => 0x0410, // CYRILLIC CAPITAL LETTER A
			0xb1 => 0x0411, // CYRILLIC CAPITAL LETTER BE
			0xb2 => 0x0412, // CYRILLIC CAPITAL LETTER VE
			0xb3 => 0x0413, // CYRILLIC CAPITAL LETTER GHE
			0xb4 => 0x0414, // CYRILLIC CAPITAL LETTER DE
			0xb5 => 0x0415, // CYRILLIC CAPITAL LETTER IE
			0xb6 => 0x0416, // CYRILLIC CAPITAL LETTER ZHE
			0xb7 => 0x0417, // CYRILLIC CAPITAL LETTER ZE
			0xb8 => 0x0418, // CYRILLIC CAPITAL LETTER I
			0xb9 => 0x0419, // CYRILLIC CAPITAL LETTER SHORT I
			0xba => 0x041a, // CYRILLIC CAPITAL LETTER KA
			0xbb => 0x041b, // CYRILLIC CAPITAL LETTER EL
			0xbc => 0x041c, // CYRILLIC CAPITAL LETTER EM
			0xbd => 0x041d, // CYRILLIC CAPITAL LETTER EN
			0xbe => 0x041e, // CYRILLIC CAPITAL LETTER O
			0xbf => 0x041f, // CYRILLIC CAPITAL LETTER PE
			0xc0 => 0x0420, // CYRILLIC CAPITAL LETTER ER
			0xc1 => 0x0421, // CYRILLIC CAPITAL LETTER ES
			0xc2 => 0x0422, // CYRILLIC CAPITAL LETTER TE
			0xc3 => 0x0423, // CYRILLIC CAPITAL LETTER U
			0xc4 => 0x0424, // CYRILLIC CAPITAL LETTER EF
			0xc5 => 0x0425, // CYRILLIC CAPITAL LETTER HA
			0xc6 => 0x0426, // CYRILLIC CAPITAL LETTER TSE
			0xc7 => 0x0427, // CYRILLIC CAPITAL LETTER CHE
			0xc8 => 0x0428, // CYRILLIC CAPITAL LETTER SHA
			0xc9 => 0x0429, // CYRILLIC CAPITAL LETTER SHCHA
			0xca => 0x042a, // CYRILLIC CAPITAL LETTER HARD SIGN
			0xcb => 0x042b, // CYRILLIC CAPITAL LETTER YERU
			0xcc => 0x042c, // CYRILLIC CAPITAL LETTER SOFT SIGN
			0xcd => 0x042d, // CYRILLIC CAPITAL LETTER E
			0xce => 0x042e, // CYRILLIC CAPITAL LETTER YU
			0xcf => 0x042f, // CYRILLIC CAPITAL LETTER YA
			0xd0 => 0x0430, // CYRILLIC SMALL LETTER A
			0xd1 => 0x0431, // CYRILLIC SMALL LETTER BE
			0xd2 => 0x0432, // CYRILLIC SMALL LETTER VE
			0xd3 => 0x0433, // CYRILLIC SMALL LETTER GHE
			0xd4 => 0x0434, // CYRILLIC SMALL LETTER DE
			0xd5 => 0x0435, // CYRILLIC SMALL LETTER IE
			0xd6 => 0x0436, // CYRILLIC SMALL LETTER ZHE
			0xd7 => 0x0437, // CYRILLIC SMALL LETTER ZE
			0xd8 => 0x0438, // CYRILLIC SMALL LETTER I
			0xd9 => 0x0439, // CYRILLIC SMALL LETTER SHORT I
			0xda => 0x043a, // CYRILLIC SMALL LETTER KA
			0xdb => 0x043b, // CYRILLIC SMALL LETTER EL
			0xdc => 0x043c, // CYRILLIC SMALL LETTER EM
			0xdd => 0x043d, // CYRILLIC SMALL LETTER EN
			0xde => 0x043e, // CYRILLIC SMALL LETTER O
			0xdf => 0x043f, // CYRILLIC SMALL LETTER PE
			0xe0 => 0x0440, // CYRILLIC SMALL LETTER ER
			0xe1 => 0x0441, // CYRILLIC SMALL LETTER ES
			0xe2 => 0x0442, // CYRILLIC SMALL LETTER TE
			0xe3 => 0x0443, // CYRILLIC SMALL LETTER U
			0xe4 => 0x0444, // CYRILLIC SMALL LETTER EF
			0xe5 => 0x0445, // CYRILLIC SMALL LETTER HA
			0xe6 => 0x0446, // CYRILLIC SMALL LETTER TSE
			0xe7 => 0x0447, // CYRILLIC SMALL LETTER CHE
			0xe8 => 0x0448, // CYRILLIC SMALL LETTER SHA
			0xe9 => 0x0449, // CYRILLIC SMALL LETTER SHCHA
			0xea => 0x044a, // CYRILLIC SMALL LETTER HARD SIGN
			0xeb => 0x044b, // CYRILLIC SMALL LETTER YERU
			0xec => 0x044c, // CYRILLIC SMALL LETTER SOFT SIGN
			0xed => 0x044d, // CYRILLIC SMALL LETTER E
			0xee => 0x044e, // CYRILLIC SMALL LETTER YU
			0xef => 0x044f, // CYRILLIC SMALL LETTER YA
			0xf0 => 0x2116, // NUMERO SIGN
			0xf1 => 0x0451, // CYRILLIC SMALL LETTER IO
			0xf2 => 0x0452, // CYRILLIC SMALL LETTER DJE
			0xf3 => 0x0453, // CYRILLIC SMALL LETTER GJE
			0xf4 => 0x0454, // CYRILLIC SMALL LETTER UKRAINIAN IE
			0xf5 => 0x0455, // CYRILLIC SMALL LETTER DZE
			0xf6 => 0x0456, // CYRILLIC SMALL LETTER BYELORUSSIAN-UKRAINIAN I
			0xf7 => 0x0457, // CYRILLIC SMALL LETTER YI
			0xf8 => 0x0458, // CYRILLIC SMALL LETTER JE
			0xf9 => 0x0459, // CYRILLIC SMALL LETTER LJE
			0xfa => 0x045a, // CYRILLIC SMALL LETTER NJE
			0xfb => 0x045b, // CYRILLIC SMALL LETTER TSHE
			0xfc => 0x045c, // CYRILLIC SMALL LETTER KJE
			0xfd => 0x00a7, // SECTION SIGN
			0xfe => 0x045e, // CYRILLIC SMALL LETTER SHORT U
			0xff => 0x045f  // CYRILLIC SMALL LETTER DZHE
		);
	}
} // END OF Encoding_ISO8859_5

?>
