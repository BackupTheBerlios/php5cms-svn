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
			"t�mto",
			"budes",
			"bude�",
			"budem",
			"boli",
			"si",
			"moj",
			"m�j",
			"svoj",
			"ta",
			"�a",
			"tomto",
			"toto",
			"tuto",
			"tieto",
			"jej",
			"ci",
			"�i",
			"preco",
			"pre�o",
			"mate",
			"m�te",
			"tato",
			"t�to",
			"kam",
			"tohoto",
			"kto",
			"ktori",
			"ktor�",
			"mi",
			"nam",
			"n�m",
			"tom",
			"tomuto",
			"mat",
			"ma�",
			"nic",
			"ni�",
			"preto",
			"ktorou",
			"bola",
			"toho",
			"pretoze",
			"preto�e",
			"asi",
			"ho",
			"nasu",
			"na�u",
			"napiste",
			"nap�te",
			"re",
			"co",
			"�o",
			"tym",
			"t�m",
			"takze",
			"tak�e",
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
			"pr�ve",
			"ju",
			"nad",
			"ci",
			"�i",
			"pod",
			"tymito",
			"t�mito",
			"medzi",
			"cez",
			"ty",
			"potom",
			"vam",
			"ani",
			"ked",
			"ke�",
			"avsak",
			"av�ak",
			"nie",
			"som",
			"tento",
			"clanku",
			"�l�nku",
			"clankov",
			"�l�nkov",
			"cl�nky",
			"�lanky",
			"aby",
			"sme",
			"pred",
			"pyta",
			"p�ta",
			"ich",
			"bol",
			"ste",
			"az",
			"a�",
			"bez",
			"tiez",
			"tie�",
			"len",
			"prvy",
			"prv�",
			"prvi",
			"prv�",
			"vase",
			"va�e",
			"ktora",
			"ktor�",
			"nas",
			"n�s",
			"nas",
			"n�",
			"novy",
			"nov�",
			"tipy",
			"pokial",
			"pokia�",
			"moze",
			"m��e",
			"strana",
			"jeho",
			"svoje",
			"ine",
			"in�",
			"spravy",
			"spr�vy",
			"nove",
			"nov�",
			"vas",
			"v�s",
			"vas",
			"v�",
			"len",
			"podla",
			"pod�a",
			"tu",
			"clanok",
			"�l�nok",
			"uz",
			"u�",
			"email",
			"byt",
			"by�",
			"viac",
			"bude",
			"uz",
			"u�",
			"nez",
			"ne�",
			"ktory",
			"ktor�",
			"by",
			"ktore",
			"ktor�",
			"co",
			"�o",
			"lebo",
			"ten",
			"tak",
			"ma",
			"pri",
			"od",
			"po",
			"su",
			"s�",
			"ako",
			"dalsi",
			"�al��",
			"ale",
			"si",
			"vo",
			"v",
			"to",
			"ako",
			"za",
			"spat",
			"sp�",
			"ze",
			"�e",
			"do",
			"pre",
			"je",
			"na"
		);
	}
} // END OF StopWords_sk

?>
