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
 
class Encoding_ISO8859_15 extends Encoding
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encoding_ISO8859_15()
	{
		$this->Encoding( "ISO8859-15" );
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populateEncodingMap()
	{
		$this->encoding_map = array(
			0xa4 => 0x20ac, // EURO SIGN
			0xa6 => 0x0160, // LATIN CAPITAL LETTER S WITH CARON
			0xa8 => 0x0161, // LATIN SMALL LETTER S WITH CARON
			0xb4 => 0x017d, // LATIN CAPITAL LETTER Z WITH CARON
			0xb8 => 0x017e, // LATIN SMALL LETTER Z WITH CARON
			0xbc => 0x0152, // LATIN CAPITAL LIGATURE OE
			0xbd => 0x0153, // LATIN SMALL LIGATURE OE
			0xbe => 0x0178  // LATIN CAPITAL LETTER Y WITH DIAERESIS
		);
	}
} // END OF Encoding_ISO8859_15

?>
