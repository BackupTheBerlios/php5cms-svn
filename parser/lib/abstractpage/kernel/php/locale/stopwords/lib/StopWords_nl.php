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
 * Dutch stopwords.
 *
 * Charset:   latin1
 * Language:  nl
 *
 * @package locale_stopwords_lib
 */
 
class StopWords_nl extends StopWords
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function StopWords_nl()
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
			"de",
			"het",
			"van",
			"en",
			"een",
			"in",
			"dat",
			"te",
			"ik",
			"hij",
			"die",
			"is",
			"met",
			"ze",
			"was",
			"als",
			"aan",
			"er",
			"je",
			"ook",
			"dan",
			"of",
			"had",
			"bij",
			"wat",
			"uit",
			"nog",
			"hem",
			"tot",
			"zo",
			"zij",
			"zou",
			"we",
			"al",
			"dit",
			"wel",
			"kan",
			"hun",
			"nu",
			"zei",
			"men",
			"me",
			"mij",
			"zal",
			"heb",
			"hoe",
			"ons",
			"wij",
			"af"
		);
	}
} // END OF StopWords_nl

?>
