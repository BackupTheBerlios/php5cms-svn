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
 * Catalan stopwords.
 *
 * Charset:   iso-8859-1
 * Language:  ct
 *
 * @package locale_stopwords_lib
 */
 
class StopWords_ct extends StopWords
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function StopWords_ct()
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
			"es",
			"i",
			"a",
			"o",
			"un",
			"una",
			"unes",
			"uns",
			"un",
			"tot",
			"també",
			"altre",
			"algun",
			"alguna",
			"alguns",
			"algunes",
			"ser",
			"és",
			"soc",
			"ets",
			"som",
			"estic",
			"està",
			"estem",
			"esteu",
			"estan",
			"com",
			"en",
			"per",
			"perquè",
			"per que",
			"estat",
			"estava",
			"ans",
			"abans",
			"éssent",
			"ambdós",
			"però",
			"per",
			"poder",
			"potser",
			"puc",
			"podem",
			"podeu",
			"poden",
			"vaig",
			"va",
			"van",
			"fer",
			"faig",
			"fa",
			"fem",
			"feu",
			"fan",
			"cada",
			"fi",
			"inclòs",
			"primer",
			"des de",
			"conseguir",
			"consegueixo",
			"consigueix",
			"consigueixes",
			"conseguim",
			"consigueixen",
			"anar",
			"haver",
			"tenir",
			"tinc",
			"te",
			"tenim",
			"teniu",
			"tene",
			"el",
			"la",
			"les",
			"els",
			"seu",
			"aquí",
			"meu",
			"teu",
			"ells",
			"elles",
			"ens",
			"nosaltres",
			"vosaltres",
			"si",
			"dins",
			"sols",
			"solament",
			"saber",
			"saps",
			"sap",
			"sabem",
			"sabeu",
			"saben",
			"últim",
			"llarg",
			"bastant",
			"fas",
			"molts",
			"aquells",
			"aquelles",
			"seus",
			"llavors",
			"sota",
			"dalt",
			"ús",
			"molt",
			"era",
			"eres",
			"erem",
			"eren",
			"mode",
			"bé",
			"quant",
			"quan",
			"on",
			"mentre",
			"qui",
			"amb",
			"entre",
			"sense",
			"jo",
			"aquell"
		);
	}
} // END OF StopWords_ct

?>
