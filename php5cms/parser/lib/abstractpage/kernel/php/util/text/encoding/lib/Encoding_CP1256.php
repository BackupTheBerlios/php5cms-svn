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
 
class Encoding_CP1256 extends Encoding
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encoding_CP1256()
	{
		$this->Encoding( "CP1256" );
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populateEncodingMap()
	{
		$this->encoding_map = array(
			0x80 => 0x20ac, // EURO SIGN
			0x81 => 0x067e, // ARABIC LETTER PEH
			0x82 => 0x201a, // SINGLE LOW-9 QUOTATION MARK
			0x83 => 0x0192, // LATIN SMALL LETTER F WITH HOOK
			0x84 => 0x201e, // DOUBLE LOW-9 QUOTATION MARK
			0x85 => 0x2026, // HORIZONTAL ELLIPSIS
			0x86 => 0x2020, // DAGGER
			0x87 => 0x2021, // DOUBLE DAGGER
			0x88 => 0x02c6, // MODIFIER LETTER CIRCUMFLEX ACCENT
			0x89 => 0x2030, // PER MILLE SIGN
			0x8a => 0x0679, // ARABIC LETTER TTEH
			0x8b => 0x2039, // SINGLE LEFT-POINTING ANGLE QUOTATION MARK
			0x8c => 0x0152, // LATIN CAPITAL LIGATURE OE
			0x8d => 0x0686, // ARABIC LETTER TCHEH
			0x8e => 0x0698, // ARABIC LETTER JEH
			0x8f => 0x0688, // ARABIC LETTER DDAL
			0x90 => 0x06af, // ARABIC LETTER GAF
			0x91 => 0x2018, // LEFT SINGLE QUOTATION MARK
			0x92 => 0x2019, // RIGHT SINGLE QUOTATION MARK
			0x93 => 0x201c, // LEFT DOUBLE QUOTATION MARK
			0x94 => 0x201d, // RIGHT DOUBLE QUOTATION MARK
			0x95 => 0x2022, // BULLET
			0x96 => 0x2013, // EN DASH
			0x97 => 0x2014, // EM DASH
			0x98 => 0x06a9, // ARABIC LETTER KEHEH
			0x99 => 0x2122, // TRADE MARK SIGN
			0x9a => 0x0691, // ARABIC LETTER RREH
			0x9b => 0x203a, // SINGLE RIGHT-POINTING ANGLE QUOTATION MARK
			0x9c => 0x0153, // LATIN SMALL LIGATURE OE
			0x9d => 0x200c, // ZERO WIDTH NON-JOINER
			0x9e => 0x200d, // ZERO WIDTH JOINER
			0x9f => 0x06ba, // ARABIC LETTER NOON GHUNNA
			0xa1 => 0x060c, // ARABIC COMMA
			0xaa => 0x06be, // ARABIC LETTER HEH DOACHASHMEE
			0xba => 0x061b, // ARABIC SEMICOLON
			0xbf => 0x061f, // ARABIC QUESTION MARK
			0xc0 => 0x06c1, // ARABIC LETTER HEH GOAL
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
			0xd8 => 0x0637, // ARABIC LETTER TAH
			0xd9 => 0x0638, // ARABIC LETTER ZAH
			0xda => 0x0639, // ARABIC LETTER AIN
			0xdb => 0x063a, // ARABIC LETTER GHAIN
			0xdc => 0x0640, // ARABIC TATWEEL
			0xdd => 0x0641, // ARABIC LETTER FEH
			0xde => 0x0642, // ARABIC LETTER QAF
			0xdf => 0x0643, // ARABIC LETTER KAF
			0xe1 => 0x0644, // ARABIC LETTER LAM
			0xe3 => 0x0645, // ARABIC LETTER MEEM
			0xe4 => 0x0646, // ARABIC LETTER NOON
			0xe5 => 0x0647, // ARABIC LETTER HEH
			0xe6 => 0x0648, // ARABIC LETTER WAW
			0xec => 0x0649, // ARABIC LETTER ALEF MAKSURA
			0xed => 0x064a, // ARABIC LETTER YEH
			0xf0 => 0x064b, // ARABIC FATHATAN
			0xf1 => 0x064c, // ARABIC DAMMATAN
			0xf2 => 0x064d, // ARABIC KASRATAN
			0xf3 => 0x064e, // ARABIC FATHA
			0xf5 => 0x064f, // ARABIC DAMMA
			0xf6 => 0x0650, // ARABIC KASRA
			0xf8 => 0x0651, // ARABIC SHADDA
			0xfa => 0x0652, // ARABIC SUKUN
			0xfd => 0x200e, // LEFT-TO-RIGHT MARK
			0xfe => 0x200f, // RIGHT-TO-LEFT MARK
			0xff => 0x06d2  // ARABIC LETTER YEH BARREE
		);
	}
} // END OF Encoding_CP1256

?>
