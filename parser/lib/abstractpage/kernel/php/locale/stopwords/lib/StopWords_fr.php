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
 * French stopwords.
 *
 * Charset:   latin1
 * Language:  fr
 *
 * @package locale_stopwords_lib
 */
 
class StopWords_fr extends StopWords
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function StopWords_fr()
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
			"abord",
			"certain",
			"de",
			"or",
			"un",
			"us",
			"plus",
			"point",
			"alors",
			"au",
			"aucuns",
			"aussi",
			"autre",
			"avant",
			"avec",
			"avoir",
			"bon",
			"car",
			"ce",
			"cela",
			"ces",
			"ceux",
			"chaque",
			"ci",
			"comme",
			"comment",
			"dans",
			"des",
			"du",
			"dedans",
			"dehors",
			"depuis",
			"deux",
			"devrait",
			"doit",
			"donc",
			"dos",
			"droite",
			"début",
			"elle",
			"elles",
			"en",
			"encore",
			"essai",
			"est",
			"et",
			"eu",
			"fait",
			"faites",
			"fois",
			"font",
			"force",
			"haut",
			"hors",
			"ici",
			"il",
			"ils",
			"je",
			"juste",
			"la",
			"le",
			"les",
			"leur",
			"là",
			"ma",
			"maintenant",
			"mais",
			"mes",
			"mine",
			"moins",
			"mon",
			"mot",
			"même",
			"ni",
			"nommés",
			"notre",
			"nous",
			"nouveaux",
			"ou",
			"où",
			"par",
			"parce",
			"parole",
			"pas",
			"personnes",
			"peut",
			"peu",
			"pièce",
			"plupart",
			"pour",
			"pourquoi",
			"quand",
			"que",
			"quel",
			"quelle",
			"quelles",
			"quels",
			"qui",
			"sa",
			"sans",
			"ses",
			"seulement",
			"si",
			"sien",
			"son",
			"sont",
			"sous",
			"soyez",
			"sujet",
			"sur",
			"ta",
			"tandis",
			"tellement",
			"tels",
			"tes",
			"ton",
			"tous",
			"tout",
			"trop",
			"très",
			"tu",
			"valeur",
			"voie",
			"voient",
			"vont",
			"votre",
			"vous",
			"vu",
			"ça",
			"étaient",
			"état",
			"étions",
			"été",
			"être"
		);
	}
} // END OF StopWords_fr

?>
