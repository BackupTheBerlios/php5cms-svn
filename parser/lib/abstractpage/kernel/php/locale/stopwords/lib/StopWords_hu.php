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


using( 'locale.stopwords.lib.StopWords' );


/**
 * Hungarian stopwords.
 *
 * Charset:   windows-1250
 * Language:  hu
 *
 * @package locale_stopwords_lib
 */
 
class StopWords_hu extends StopWords
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function StopWords_hu()
	{
		$this->_populate();
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */	
	function _populate()
	{
		$this->words = array(
			"a",
			"az",
			"egy",
			"be",
			"ki",
			"le",
			"fel",
			"meg",
			"el",
			"át",
			"rá",
			"ide",
			"oda",
			"szét",
			"össze",
			"vissza",
			"de",
			"hát",
			"és",
			"vagy",
			"hogy",
			"van",
			"lesz",
			"volt",
			"csak",
			"nem",
			"igen",
			"mint",
			"én",
			"te",
			"õ",
			"mi",
			"ti",
			"õk",
			"ön"
		);
	}
} // END OF StopWords_hu

?>
