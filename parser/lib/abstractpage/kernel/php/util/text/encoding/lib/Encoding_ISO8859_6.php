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
 
class Encoding_ISO8859_6 extends Encoding
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encoding_ISO8859_6()
	{
		$this->Encoding( "ISO8859-6" );
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populateEncodingMap()
	{
		$this->encoding_map = array(
			0xa1 => null,
			0xa2 => null,
			0xa3 => null,
			0xa5 => null,
			0xa6 => null,
			0xa7 => null,
			0xa8 => null,
			0xa9 => null,
			0xaa => null,
			0xab => null,
			0xac => 0x060c, // ARABIC COMMA
			0xae => null,
			0xaf => null,
			0xb0 => null,
			0xb1 => null,
			0xb2 => null,
			0xb3 => null,
			0xb4 => null,
			0xb5 => null,
			0xb6 => null,
			0xb7 => null,
			0xb8 => null,
			0xb9 => null,
			0xba => null,
			0xbb => 0x061b, // ARABIC SEMICOLON
			0xbc => null,
			0xbd => null,
			0xbe => null,
			0xbf => 0x061f, // ARABIC QUESTION MARK
			0xc0 => null,
			0xc1 => 0x0621, // ARABIC LETTER HAMZA
			0xc2 => 0x0622, // ARABIC LETTER ALEF WITH MADDA ABOVE
			0xc3 => 0x0623, // ARABIC LETTER ALEF WITH HAMZA ABOVE
			0xc4 => 0x0624, // ARABIC LETTER WAW WITH HAMZA ABOVE
			0xc5 => 0x0625, // ARABIC LETTER ALEF WITH HAMZA BELOW
			0xc6 => 0x0626, // ARABIC LETTER YEH WITH HAMZA ABOVE
			0xc7 => 0x0627, // ARABIC LETTER ALEF
			0xc8 => 0x0628, // ARABIC LETTER BEH
			0xc9 => 0x0629, // ARABIC LETTER TEH MARBUTA
			0xca => 0x062a, // ARABIC LETTER TEH
			0xcb => 0x062b, // ARABIC LETTER THEH
			0xcc => 0x062c, // ARABIC LETTER JEEM
			0xcd => 0x062d, // ARABIC LETTER HAH
			0xce => 0x062e, // ARABIC LETTER KHAH
			0xcf => 0x062f, // ARABIC LETTER DAL
			0xd0 => 0x0630, // ARABIC LETTER THAL
			0xd1 => 0x0631, // ARABIC LETTER REH
			0xd2 => 0x0632, // ARABIC LETTER ZAIN
			0xd3 => 0x0633, // ARABIC LETTER SEEN
			0xd4 => 0x0634, // ARABIC LETTER SHEEN
			0xd5 => 0x0635, // ARABIC LETTER SAD
			0xd6 => 0x0636, // ARABIC LETTER DAD
			0xd7 => 0x0637, // ARABIC LETTER TAH
			0xd8 => 0x0638, // ARABIC LETTER ZAH
			0xd9 => 0x0639, // ARABIC LETTER AIN
			0xda => 0x063a, // ARABIC LETTER GHAIN
			0xdb => null,
			0xdc => null,
			0xdd => null,
			0xde => null,
			0xdf => null,
			0xe0 => 0x0640, // ARABIC TATWEEL
			0xe1 => 0x0641, // ARABIC LETTER FEH
			0xe2 => 0x0642, // ARABIC LETTER QAF
			0xe3 => 0x0643, // ARABIC LETTER KAF
			0xe4 => 0x0644, // ARABIC LETTER LAM
			0xe5 => 0x0645, // ARABIC LETTER MEEM
			0xe6 => 0x0646, // ARABIC LETTER NOON
			0xe7 => 0x0647, // ARABIC LETTER HEH
			0xe8 => 0x0648, // ARABIC LETTER WAW
			0xe9 => 0x0649, // ARABIC LETTER ALEF MAKSURA
			0xea => 0x064a, // ARABIC LETTER YEH
			0xeb => 0x064b, // ARABIC FATHATAN
			0xec => 0x064c, // ARABIC DAMMATAN
			0xed => 0x064d, // ARABIC KASRATAN
			0xee => 0x064e, // ARABIC FATHA
			0xef => 0x064f, // ARABIC DAMMA
			0xf0 => 0x0650, // ARABIC KASRA
			0xf1 => 0x0651, // ARABIC SHADDA
			0xf2 => 0x0652, // ARABIC SUKUN
			0xf3 => null,
			0xf4 => null,
			0xf5 => null,
			0xf6 => null,
			0xf7 => null,
			0xf8 => null,
			0xf9 => null,
			0xfa => null,
			0xfb => null,
			0xfc => null,
			0xfd => null,
			0xfe => null,
			0xff => null
		);
	}
} // END OF Encoding_ISO8859_6

?>
