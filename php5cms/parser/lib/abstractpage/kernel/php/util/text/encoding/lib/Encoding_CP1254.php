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
 
class Encoding_CP1254 extends Encoding
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encoding_CP1254()
	{
		$this->Encoding( "CP1254" );
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
			0x8a => 0x0160, // LATIN CAPITAL LETTER S WITH CARON
			0x8b => 0x2039, // SINGLE LEFT-POINTING ANGLE QUOTATION MARK
			0x8c => 0x0152, // LATIN CAPITAL LIGATURE OE
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
			0x9a => 0x0161, // LATIN SMALL LETTER S WITH CARON
			0x9b => 0x203a, // SINGLE RIGHT-POINTING ANGLE QUOTATION MARK
			0x9c => 0x0153, // LATIN SMALL LIGATURE OE
			0x9d => null,   // UNDEFINED
			0x9e => null,   // UNDEFINED
			0x9f => 0x0178, // LATIN CAPITAL LETTER Y WITH DIAERESIS
			0xd0 => 0x011e, // LATIN CAPITAL LETTER G WITH BREVE
			0xdd => 0x0130, // LATIN CAPITAL LETTER I WITH DOT ABOVE
			0xde => 0x015e, // LATIN CAPITAL LETTER S WITH CEDILLA
			0xf0 => 0x011f, // LATIN SMALL LETTER G WITH BREVE
			0xfd => 0x0131, // LATIN SMALL LETTER DOTLESS I
			0xfe => 0x015f  // LATIN SMALL LETTER S WITH CEDILLA
		);
	}
} // END OF Encoding_CP1254

?>
