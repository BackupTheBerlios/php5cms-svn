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
 * Slovak stopwords.
 *
 * Charset:   latin2
 * Language:  sk
 *
 * @package locale_stopwords_lib
 */
 
class StopWords_sk extends StopWords
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function StopWords_sk()
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
			"dnes",
			"sk",
			"tymto",
			"týmto",
			"budes",
			"bude¹",
			"budem",
			"boli",
			"si",
			"moj",
			"môj",
			"svoj",
			"ta",
			"»a",
			"tomto",
			"toto",
			"tuto",
			"tieto",
			"jej",
			"ci",
			"èi",
			"preco",
			"preèo",
			"mate",
			"máte",
			"tato",
			"táto",
			"kam",
			"tohoto",
			"kto",
			"ktori",
			"ktorí",
			"mi",
			"nam",
			"nám",
			"tom",
			"tomuto",
			"mat",
			"ma»",
			"nic",
			"niè",
			"preto",
			"ktorou",
			"bola",
			"toho",
			"pretoze",
			"preto¾e",
			"asi",
			"ho",
			"nasu",
			"na¹u",
			"napiste",
			"napí¹te",
			"re",
			"co",
			"èo",
			"tym",
			"tým",
			"takze",
			"tak¾e",
			"svojich",
			"jej",
			"svojimi",
			"ste",
			"aj",
			"tu",
			"teda",
			"tejto",
			"tieto",
			"bolo",
			"kde",
			"ku",
			"prave",
			"práve",
			"ju",
			"nad",
			"ci",
			"èi",
			"pod",
			"tymito",
			"týmito",
			"medzi",
			"cez",
			"ty",
			"potom",
			"vam",
			"ani",
			"ked",
			"keï",
			"avsak",
			"av¹ak",
			"nie",
			"som",
			"tento",
			"clanku",
			"èlánku",
			"clankov",
			"èlánkov",
			"clánky",
			"èlanky",
			"aby",
			"sme",
			"pred",
			"pyta",
			"pýta",
			"ich",
			"bol",
			"ste",
			"az",
			"a¾",
			"bez",
			"tiez",
			"tie¾",
			"len",
			"prvy",
			"prvý",
			"prvi",
			"prví",
			"vase",
			"va¹e",
			"ktora",
			"ktorá",
			"nas",
			"nás",
			"nas",
			"ná¹",
			"novy",
			"nový",
			"tipy",
			"pokial",
			"pokiaµ",
			"moze",
			"mô¾e",
			"strana",
			"jeho",
			"svoje",
			"ine",
			"iné",
			"spravy",
			"správy",
			"nove",
			"nové",
			"vas",
			"vás",
			"vas",
			"vá¹",
			"len",
			"podla",
			"podµa",
			"tu",
			"clanok",
			"èlánok",
			"uz",
			"u¾",
			"email",
			"byt",
			"by»",
			"viac",
			"bude",
			"uz",
			"u¾",
			"nez",
			"ne¾",
			"ktory",
			"ktorý",
			"by",
			"ktore",
			"ktoré",
			"co",
			"èo",
			"lebo",
			"ten",
			"tak",
			"ma",
			"pri",
			"od",
			"po",
			"su",
			"sú",
			"ako",
			"dalsi",
			"ïal¹í",
			"ale",
			"si",
			"vo",
			"v",
			"to",
			"ako",
			"za",
			"spat",
			"spä»",
			"ze",
			"¾e",
			"do",
			"pre",
			"je",
			"na"
		);
	}
} // END OF StopWords_sk

?>
