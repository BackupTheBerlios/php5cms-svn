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
 * German stopwords.
 *
 * Charset:   latin1
 * Language:  de
 *
 * @package locale_stopwords_lib
 */
 
class StopWords_de extends StopWords
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function StopWords_de()
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
			"aber",
			"als",
			"am",
			"an",
			"auch",
			"auf",
			"aus",
			"bei",
			"bin",
			"bis",
			"bist",
			"da",
			"dadurch",
			"daher",
			"darum",
			"das",
			"daß",
			"dass",
			"dein",
			"deine",
			"dem",
			"den",
			"der",
			"des",
			"dessen",
			"deshalb",
			"die",
			"dies",
			"dieser",
			"dieses",
			"doch",
			"dort",
			"du",
			"durch",
			"ein",
			"eine",
			"einem",
			"einen",
			"einer",
			"eines",
			"er",
			"es",
			"euer",
			"eure",
			"für",
			"hatte",
			"hatten",
			"hattest",
			"hattet",
			"hier",
			"hinter",
			"ich",
			"ihr",
			"ihre",
			"im",
			"in",
			"ist",
			"ja",
			"jede",
			"jedem",
			"jeden",
			"jeder",
			"jedes",
			"jener",
			"jenes",
			"jetzt",
			"kann",
			"kannst",
			"können",
			"könnt",
			"machen",
			"mein",
			"meine",
			"mit",
			"muß",
			"mußt",
			"musst",
			"müssen",
			"müßt",
			"nach",
			"nachdem",
			"nein",
			"nicht",
			"nun",
			"oder",
			"seid",
			"sein",
			"seine",
			"sich",
			"sie",
			"sind",
			"soll",
			"sollen",
			"sollst",
			"sollt",
			"sonst",
			"soweit",
			"sowie",
			"und",
			"unser",
			"unsere",
			"unter",
			"vom",
			"von",
			"vor",
			"wann",
			"warum",
			"was",
			"weiter",
			"weitere",
			"wenn",
			"wer",
			"werde",
			"werden",
			"werdet",
			"weshalb",
			"wie",
			"wieder",
			"wieso",
			"wir",
			"wird",
			"wirst",
			"wo",
			"woher",
			"wohin",
			"zu",
			"zum",
			"zur",
			"über"
		);
	}
} // END OF StopWords_de

?>
