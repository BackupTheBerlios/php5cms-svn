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
 
class Encoding_CP424 extends Encoding
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encoding_CP424()
	{
		$this->Encoding( "CP424" );
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populateEncodingMap()
	{
		$this->encoding_map = array(
			0x04 => 0x009c, // SELECT
			0x05 => 0x0009, // HORIZONTAL TABULATION
			0x06 => 0x0086, // REQUIRED NEW LINE
			0x07 => 0x007f, // DELETE
			0x08 => 0x0097, // GRAPHIC ESCAPE
			0x09 => 0x008d, // SUPERSCRIPT
			0x0a => 0x008e, // REPEAT
			0x14 => 0x009d, // RESTORE/ENABLE PRESENTATION
			0x15 => 0x0085, // NEW LINE
			0x16 => 0x0008, // BACKSPACE
			0x17 => 0x0087, // PROGRAM OPERATOR COMMUNICATION
			0x1a => 0x0092, // UNIT BACK SPACE
			0x1b => 0x008f, // CUSTOMER USE ONE
			0x20 => 0x0080, // DIGIT SELECT
			0x21 => 0x0081, // START OF SIGNIFICANCE
			0x22 => 0x0082, // FIELD SEPARATOR
			0x23 => 0x0083, // WORD UNDERSCORE
			0x24 => 0x0084, // BYPASS OR INHIBIT PRESENTATION
			0x25 => 0x000a, // LINE FEED
			0x26 => 0x0017, // END OF TRANSMISSION BLOCK
			0x27 => 0x001b, // ESCAPE
			0x28 => 0x0088, // SET ATTRIBUTE
			0x29 => 0x0089, // START FIELD EXTENDED
			0x2a => 0x008a, // SET MODE OR SWITCH
			0x2b => 0x008b, // CONTROL SEQUENCE PREFIX
			0x2c => 0x008c, // MODIFY FIELD ATTRIBUTE
			0x2d => 0x0005, // ENQUIRY
			0x2e => 0x0006, // ACKNOWLEDGE
			0x2f => 0x0007, // BELL
			0x30 => 0x0090, // <reserved>
			0x31 => 0x0091, // <reserved>
			0x32 => 0x0016, // SYNCHRONOUS IDLE
			0x33 => 0x0093, // INDEX RETURN
			0x34 => 0x0094, // PRESENTATION POSITION
			0x35 => 0x0095, // TRANSPARENT
			0x36 => 0x0096, // NUMERIC BACKSPACE
			0x37 => 0x0004, // END OF TRANSMISSION
			0x38 => 0x0098, // SUBSCRIPT
			0x39 => 0x0099, // INDENT TABULATION
			0x3a => 0x009a, // REVERSE FORM FEED
			0x3b => 0x009b, // CUSTOMER USE THREE
			0x3c => 0x0014, // DEVICE CONTROL FOUR
			0x3d => 0x0015, // NEGATIVE ACKNOWLEDGE
			0x3e => 0x009e, // <reserved>
			0x3f => 0x001a, // SUBSTITUTE
			0x40 => 0x0020, // SPACE
			0x41 => 0x05d0, // HEBREW LETTER ALEF
			0x42 => 0x05d1, // HEBREW LETTER BET
			0x43 => 0x05d2, // HEBREW LETTER GIMEL
			0x44 => 0x05d3, // HEBREW LETTER DALET
			0x45 => 0x05d4, // HEBREW LETTER HE
			0x46 => 0x05d5, // HEBREW LETTER VAV
			0x47 => 0x05d6, // HEBREW LETTER ZAYIN
			0x48 => 0x05d7, // HEBREW LETTER HET
			0x49 => 0x05d8, // HEBREW LETTER TET
			0x4a => 0x00a2, // CENT SIGN
			0x4b => 0x002e, // FULL STOP
			0x4c => 0x003c, // LESS-THAN SIGN
			0x4d => 0x0028, // LEFT PARENTHESIS
			0x4e => 0x002b, // PLUS SIGN
			0x4f => 0x007c, // VERTICAL LINE
			0x50 => 0x0026, // AMPERSAND
			0x51 => 0x05d9, // HEBREW LETTER YOD
			0x52 => 0x05da, // HEBREW LETTER FINAL KAF
			0x53 => 0x05db, // HEBREW LETTER KAF
			0x54 => 0x05dc, // HEBREW LETTER LAMED
			0x55 => 0x05dd, // HEBREW LETTER FINAL MEM
			0x56 => 0x05de, // HEBREW LETTER MEM
			0x57 => 0x05df, // HEBREW LETTER FINAL NUN
			0x58 => 0x05e0, // HEBREW LETTER NUN
			0x59 => 0x05e1, // HEBREW LETTER SAMEKH
			0x5a => 0x0021, // EXCLAMATION MARK
			0x5b => 0x0024, // DOLLAR SIGN
			0x5c => 0x002a, // ASTERISK
			0x5d => 0x0029, // RIGHT PARENTHESIS
			0x5e => 0x003b, // SEMICOLON
			0x5f => 0x00ac, // NOT SIGN
			0x60 => 0x002d, // HYPHEN-MINUS
			0x61 => 0x002f, // SOLIDUS
			0x62 => 0x05e2, // HEBREW LETTER AYIN
			0x63 => 0x05e3, // HEBREW LETTER FINAL PE
			0x64 => 0x05e4, // HEBREW LETTER PE
			0x65 => 0x05e5, // HEBREW LETTER FINAL TSADI
			0x66 => 0x05e6, // HEBREW LETTER TSADI
			0x67 => 0x05e7, // HEBREW LETTER QOF
			0x68 => 0x05e8, // HEBREW LETTER RESH
			0x69 => 0x05e9, // HEBREW LETTER SHIN
			0x6a => 0x00a6, // BROKEN BAR
			0x6b => 0x002c, // COMMA
			0x6c => 0x0025, // PERCENT SIGN
			0x6d => 0x005f, // LOW LINE
			0x6e => 0x003e, // GREATER-THAN SIGN
			0x6f => 0x003f, // QUESTION MARK
			0x70 => null,   // UNDEFINED
			0x71 => 0x05ea, // HEBREW LETTER TAV
			0x72 => null,   // UNDEFINED
			0x73 => null,   // UNDEFINED
			0x74 => 0x00a0, // NO-BREAK SPACE
			0x75 => null,   // UNDEFINED
			0x76 => null,   // UNDEFINED
			0x77 => null,   // UNDEFINED
			0x78 => 0x2017, // DOUBLE LOW LINE
			0x79 => 0x0060, // GRAVE ACCENT
			0x7a => 0x003a, // COLON
			0x7b => 0x0023, // NUMBER SIGN
			0x7c => 0x0040, // COMMERCIAL AT
			0x7d => 0x0027, // APOSTROPHE
			0x7e => 0x003d, // EQUALS SIGN
			0x7f => 0x0022, // QUOTATION MARK
			0x80 => null,   // UNDEFINED
			0x81 => 0x0061, // LATIN SMALL LETTER A
			0x82 => 0x0062, // LATIN SMALL LETTER B
			0x83 => 0x0063, // LATIN SMALL LETTER C
			0x84 => 0x0064, // LATIN SMALL LETTER D
			0x85 => 0x0065, // LATIN SMALL LETTER E
			0x86 => 0x0066, // LATIN SMALL LETTER F
			0x87 => 0x0067, // LATIN SMALL LETTER G
			0x88 => 0x0068, // LATIN SMALL LETTER H
			0x89 => 0x0069, // LATIN SMALL LETTER I
			0x8a => 0x00ab, // LEFT-POINTING DOUBLE ANGLE QUOTATION MARK
			0x8b => 0x00bb, // RIGHT-POINTING DOUBLE ANGLE QUOTATION MARK
			0x8c => null,   // UNDEFINED
			0x8d => null,   // UNDEFINED
			0x8e => null,   // UNDEFINED
			0x8f => 0x00b1, // PLUS-MINUS SIGN
			0x90 => 0x00b0, // DEGREE SIGN
			0x91 => 0x006a, // LATIN SMALL LETTER J
			0x92 => 0x006b, // LATIN SMALL LETTER K
			0x93 => 0x006c, // LATIN SMALL LETTER L
			0x94 => 0x006d, // LATIN SMALL LETTER M
			0x95 => 0x006e, // LATIN SMALL LETTER N
			0x96 => 0x006f, // LATIN SMALL LETTER O
			0x97 => 0x0070, // LATIN SMALL LETTER P
			0x98 => 0x0071, // LATIN SMALL LETTER Q
			0x99 => 0x0072, // LATIN SMALL LETTER R
			0x9a => null,   // UNDEFINED
			0x9b => null,   // UNDEFINED
			0x9c => null,   // UNDEFINED
			0x9d => 0x00b8, // CEDILLA
			0x9e => null,   // UNDEFINED
			0x9f => 0x00a4, // CURRENCY SIGN
			0xa0 => 0x00b5, // MICRO SIGN
			0xa1 => 0x007e, // TILDE
			0xa2 => 0x0073, // LATIN SMALL LETTER S
			0xa3 => 0x0074, // LATIN SMALL LETTER T
			0xa4 => 0x0075, // LATIN SMALL LETTER U
			0xa5 => 0x0076, // LATIN SMALL LETTER V
			0xa6 => 0x0077, // LATIN SMALL LETTER W
			0xa7 => 0x0078, // LATIN SMALL LETTER X
			0xa8 => 0x0079, // LATIN SMALL LETTER Y
			0xa9 => 0x007a, // LATIN SMALL LETTER Z
			0xaa => null,   // UNDEFINED
			0xab => null,   // UNDEFINED
			0xac => null,   // UNDEFINED
			0xad => null,   // UNDEFINED
			0xae => null,   // UNDEFINED
			0xaf => 0x00ae, // REGISTERED SIGN
			0xb0 => 0x005e, // CIRCUMFLEX ACCENT
			0xb1 => 0x00a3, // POUND SIGN
			0xb2 => 0x00a5, // YEN SIGN
			0xb3 => 0x00b7, // MIDDLE DOT
			0xb4 => 0x00a9, // COPYRIGHT SIGN
			0xb5 => 0x00a7, // SECTION SIGN
			0xb7 => 0x00bc, // VULGAR FRACTION ONE QUARTER
			0xb8 => 0x00bd, // VULGAR FRACTION ONE HALF
			0xb9 => 0x00be, // VULGAR FRACTION THREE QUARTERS
			0xba => 0x005b, // LEFT SQUARE BRACKET
			0xbb => 0x005d, // RIGHT SQUARE BRACKET
			0xbc => 0x00af, // MACRON
			0xbd => 0x00a8, // DIAERESIS
			0xbe => 0x00b4, // ACUTE ACCENT
			0xbf => 0x00d7, // MULTIPLICATION SIGN
			0xc0 => 0x007b, // LEFT CURLY BRACKET
			0xc1 => 0x0041, // LATIN CAPITAL LETTER A
			0xc2 => 0x0042, // LATIN CAPITAL LETTER B
			0xc3 => 0x0043, // LATIN CAPITAL LETTER C
			0xc4 => 0x0044, // LATIN CAPITAL LETTER D
			0xc5 => 0x0045, // LATIN CAPITAL LETTER E
			0xc6 => 0x0046, // LATIN CAPITAL LETTER F
			0xc7 => 0x0047, // LATIN CAPITAL LETTER G
			0xc8 => 0x0048, // LATIN CAPITAL LETTER H
			0xc9 => 0x0049, // LATIN CAPITAL LETTER I
			0xca => 0x00ad, // SOFT HYPHEN
			0xcb => null,   // UNDEFINED
			0xcc => null,   // UNDEFINED
			0xcd => null,   // UNDEFINED
			0xce => null,   // UNDEFINED
			0xcf => null,   // UNDEFINED
			0xd0 => 0x007d, // RIGHT CURLY BRACKET
			0xd1 => 0x004a, // LATIN CAPITAL LETTER J
			0xd2 => 0x004b, // LATIN CAPITAL LETTER K
			0xd3 => 0x004c, // LATIN CAPITAL LETTER L
			0xd4 => 0x004d, // LATIN CAPITAL LETTER M
			0xd5 => 0x004e, // LATIN CAPITAL LETTER N
			0xd6 => 0x004f, // LATIN CAPITAL LETTER O
			0xd7 => 0x0050, // LATIN CAPITAL LETTER P
			0xd8 => 0x0051, // LATIN CAPITAL LETTER Q
			0xd9 => 0x0052, // LATIN CAPITAL LETTER R
			0xda => 0x00b9, // SUPERSCRIPT ONE
			0xdb => null,   // UNDEFINED
			0xdc => null,   // UNDEFINED
			0xdd => null,   // UNDEFINED
			0xde => null,   // UNDEFINED
			0xdf => null,   // UNDEFINED
			0xe0 => 0x005c, // REVERSE SOLIDUS
			0xe1 => 0x00f7, // DIVISION SIGN
			0xe2 => 0x0053, // LATIN CAPITAL LETTER S
			0xe3 => 0x0054, // LATIN CAPITAL LETTER T
			0xe4 => 0x0055, // LATIN CAPITAL LETTER U
			0xe5 => 0x0056, // LATIN CAPITAL LETTER V
			0xe6 => 0x0057, // LATIN CAPITAL LETTER W
			0xe7 => 0x0058, // LATIN CAPITAL LETTER X
			0xe8 => 0x0059, // LATIN CAPITAL LETTER Y
			0xe9 => 0x005a, // LATIN CAPITAL LETTER Z
			0xea => 0x00b2, // SUPERSCRIPT TWO
			0xeb => null,   // UNDEFINED
			0xec => null,   // UNDEFINED
			0xed => null,   // UNDEFINED
			0xee => null,   // UNDEFINED
			0xef => null,   // UNDEFINED
			0xf0 => 0x0030, // DIGIT ZERO
			0xf1 => 0x0031, // DIGIT ONE
			0xf2 => 0x0032, // DIGIT TWO
			0xf3 => 0x0033, // DIGIT THREE
			0xf4 => 0x0034, // DIGIT FOUR
			0xf5 => 0x0035, // DIGIT FIVE
			0xf6 => 0x0036, // DIGIT SIX
			0xf7 => 0x0037, // DIGIT SEVEN
			0xf8 => 0x0038, // DIGIT EIGHT
			0xf9 => 0x0039, // DIGIT NINE
			0xfa => 0x00b3, // SUPERSCRIPT THREE
			0xfb => null,   // UNDEFINED
			0xfc => null,   // UNDEFINED
			0xfd => null,   // UNDEFINED
			0xfe => null,   // UNDEFINED
			0xff => 0x009f  // EIGHT ONES
		);
	}
} // END OF Encoding_CP424

?>
