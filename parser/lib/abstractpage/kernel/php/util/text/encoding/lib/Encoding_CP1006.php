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
 
class Encoding_CP1006 extends Encoding
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encoding_CP1006()
	{
		$this->Encoding( "CP1006" );
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populateEncodingMap()
	{
		$this->encoding_map = array(
			0xa1 => 0x06f0, // EXTENDED ARABIC-INDIC DIGIT ZERO
			0xa2 => 0x06f1, // EXTENDED ARABIC-INDIC DIGIT ONE
			0xa3 => 0x06f2, // EXTENDED ARABIC-INDIC DIGIT TWO
			0xa4 => 0x06f3, // EXTENDED ARABIC-INDIC DIGIT THREE
			0xa5 => 0x06f4, // EXTENDED ARABIC-INDIC DIGIT FOUR
			0xa6 => 0x06f5, // EXTENDED ARABIC-INDIC DIGIT FIVE
			0xa7 => 0x06f6, // EXTENDED ARABIC-INDIC DIGIT SIX
			0xa8 => 0x06f7, // EXTENDED ARABIC-INDIC DIGIT SEVEN
			0xa9 => 0x06f8, // EXTENDED ARABIC-INDIC DIGIT EIGHT
			0xaa => 0x06f9, // EXTENDED ARABIC-INDIC DIGIT NINE
			0xab => 0x060c, // ARABIC COMMA
			0xac => 0x061b, // ARABIC SEMICOLON
			0xae => 0x061f, // ARABIC QUESTION MARK
			0xaf => 0xfe81, // ARABIC LETTER ALEF WITH MADDA ABOVE ISOLATED FORM
			0xb0 => 0xfe8d, // ARABIC LETTER ALEF ISOLATED FORM
			0xb1 => 0xfe8e, // ARABIC LETTER ALEF FINAL FORM
			0xb2 => 0xfe8e, // ARABIC LETTER ALEF FINAL FORM
			0xb3 => 0xfe8f, // ARABIC LETTER BEH ISOLATED FORM
			0xb4 => 0xfe91, // ARABIC LETTER BEH INITIAL FORM
			0xb5 => 0xfb56, // ARABIC LETTER PEH ISOLATED FORM
			0xb6 => 0xfb58, // ARABIC LETTER PEH INITIAL FORM
			0xb7 => 0xfe93, // ARABIC LETTER TEH MARBUTA ISOLATED FORM
			0xb8 => 0xfe95, // ARABIC LETTER TEH ISOLATED FORM
			0xb9 => 0xfe97, // ARABIC LETTER TEH INITIAL FORM
			0xba => 0xfb66, // ARABIC LETTER TTEH ISOLATED FORM
			0xbb => 0xfb68, // ARABIC LETTER TTEH INITIAL FORM
			0xbc => 0xfe99, // ARABIC LETTER THEH ISOLATED FORM
			0xbd => 0xfe9b, // ARABIC LETTER THEH INITIAL FORM
			0xbe => 0xfe9d, // ARABIC LETTER JEEM ISOLATED FORM
			0xbf => 0xfe9f, // ARABIC LETTER JEEM INITIAL FORM
			0xc0 => 0xfb7a, // ARABIC LETTER TCHEH ISOLATED FORM
			0xc1 => 0xfb7c, // ARABIC LETTER TCHEH INITIAL FORM
			0xc2 => 0xfea1, // ARABIC LETTER HAH ISOLATED FORM
			0xc3 => 0xfea3, // ARABIC LETTER HAH INITIAL FORM
			0xc4 => 0xfea5, // ARABIC LETTER KHAH ISOLATED FORM
			0xc5 => 0xfea7, // ARABIC LETTER KHAH INITIAL FORM
			0xc6 => 0xfea9, // ARABIC LETTER DAL ISOLATED FORM
			0xc7 => 0xfb84, // ARABIC LETTER DAHAL ISOLATED FORMN
			0xc8 => 0xfeab, // ARABIC LETTER THAL ISOLATED FORM
			0xc9 => 0xfead, // ARABIC LETTER REH ISOLATED FORM
			0xca => 0xfb8c, // ARABIC LETTER RREH ISOLATED FORM
			0xcb => 0xfeaf, // ARABIC LETTER ZAIN ISOLATED FORM
			0xcc => 0xfb8a, // ARABIC LETTER JEH ISOLATED FORM
			0xcd => 0xfeb1, // ARABIC LETTER SEEN ISOLATED FORM
			0xce => 0xfeb3, // ARABIC LETTER SEEN INITIAL FORM
			0xcf => 0xfeb5, // ARABIC LETTER SHEEN ISOLATED FORM
			0xd0 => 0xfeb7, // ARABIC LETTER SHEEN INITIAL FORM
			0xd1 => 0xfeb9, // ARABIC LETTER SAD ISOLATED FORM
			0xd2 => 0xfebb, // ARABIC LETTER SAD INITIAL FORM
			0xd3 => 0xfebd, // ARABIC LETTER DAD ISOLATED FORM
			0xd4 => 0xfebf, // ARABIC LETTER DAD INITIAL FORM
			0xd5 => 0xfec1, // ARABIC LETTER TAH ISOLATED FORM
			0xd6 => 0xfec5, // ARABIC LETTER ZAH ISOLATED FORM
			0xd7 => 0xfec9, // ARABIC LETTER AIN ISOLATED FORM
			0xd8 => 0xfeca, // ARABIC LETTER AIN FINAL FORM
			0xd9 => 0xfecb, // ARABIC LETTER AIN INITIAL FORM
			0xda => 0xfecc, // ARABIC LETTER AIN MEDIAL FORM
			0xdb => 0xfecd, // ARABIC LETTER GHAIN ISOLATED FORM
			0xdc => 0xfece, // ARABIC LETTER GHAIN FINAL FORM
			0xdd => 0xfecf, // ARABIC LETTER GHAIN INITIAL FORM
			0xde => 0xfed0, // ARABIC LETTER GHAIN MEDIAL FORM
			0xdf => 0xfed1, // ARABIC LETTER FEH ISOLATED FORM
			0xe0 => 0xfed3, // ARABIC LETTER FEH INITIAL FORM
			0xe1 => 0xfed5, // ARABIC LETTER QAF ISOLATED FORM
			0xe2 => 0xfed7, // ARABIC LETTER QAF INITIAL FORM
			0xe3 => 0xfed9, // ARABIC LETTER KAF ISOLATED FORM
			0xe4 => 0xfedb, // ARABIC LETTER KAF INITIAL FORM
			0xe5 => 0xfb92, // ARABIC LETTER GAF ISOLATED FORM
			0xe6 => 0xfb94, // ARABIC LETTER GAF INITIAL FORM
			0xe7 => 0xfedd, // ARABIC LETTER LAM ISOLATED FORM
			0xe8 => 0xfedf, // ARABIC LETTER LAM INITIAL FORM
			0xe9 => 0xfee0, // ARABIC LETTER LAM MEDIAL FORM
			0xea => 0xfee1, // ARABIC LETTER MEEM ISOLATED FORM
			0xeb => 0xfee3, // ARABIC LETTER MEEM INITIAL FORM
			0xec => 0xfb9e, // ARABIC LETTER NOON GHUNNA ISOLATED FORM
			0xed => 0xfee5, // ARABIC LETTER NOON ISOLATED FORM
			0xee => 0xfee7, // ARABIC LETTER NOON INITIAL FORM
			0xef => 0xfe85, // ARABIC LETTER WAW WITH HAMZA ABOVE ISOLATED FORM
			0xf0 => 0xfeed, // ARABIC LETTER WAW ISOLATED FORM
			0xf1 => 0xfba6, // ARABIC LETTER HEH GOAL ISOLATED FORM
			0xf2 => 0xfba8, // ARABIC LETTER HEH GOAL INITIAL FORM
			0xf3 => 0xfba9, // ARABIC LETTER HEH GOAL MEDIAL FORM
			0xf4 => 0xfbaa, // ARABIC LETTER HEH DOACHASHMEE ISOLATED FORM
			0xf5 => 0xfe80, // ARABIC LETTER HAMZA ISOLATED FORM
			0xf6 => 0xfe89, // ARABIC LETTER YEH WITH HAMZA ABOVE ISOLATED FORM
			0xf7 => 0xfe8a, // ARABIC LETTER YEH WITH HAMZA ABOVE FINAL FORM
			0xf8 => 0xfe8b, // ARABIC LETTER YEH WITH HAMZA ABOVE INITIAL FORM
			0xf9 => 0xfef1, // ARABIC LETTER YEH ISOLATED FORM
			0xfa => 0xfef2, // ARABIC LETTER YEH FINAL FORM
			0xfb => 0xfef3, // ARABIC LETTER YEH INITIAL FORM
			0xfc => 0xfbb0, // ARABIC LETTER YEH BARREE WITH HAMZA ABOVE ISOLATED FORM
			0xfd => 0xfbae, // ARABIC LETTER YEH BARREE ISOLATED FORM
			0xfe => 0xfe7c, // ARABIC SHADDA ISOLATED FORM
			0xff => 0xfe7d  // ARABIC SHADDA MEDIAL FORM
		);
	}
} // END OF Encoding_CP1006

?>
