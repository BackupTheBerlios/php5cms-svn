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
 
class Encoding_ISO8859_9 extends Encoding
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Encoding_ISO8859_9()
	{
		$this->Encoding( "ISO8859-9" );
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populateEncodingMap()
	{
		$this->encoding_map = array(
			0xd0 => 0x011e, // LATIN CAPITAL LETTER G WITH BREVE
			0xdd => 0x0130, // LATIN CAPITAL LETTER I WITH DOT ABOVE
			0xde => 0x015e, // LATIN CAPITAL LETTER S WITH CEDILLA
			0xf0 => 0x011f, // LATIN SMALL LETTER G WITH BREVE
			0xfd => 0x0131, // LATIN SMALL LETTER DOTLESS I
			0xfe => 0x015f  // LATIN SMALL LETTER S WITH CEDILLA
		);
	}
} // END OF Encoding_ISO8859_9

?>
