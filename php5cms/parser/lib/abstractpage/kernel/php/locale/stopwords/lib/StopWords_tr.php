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
 * Turkish stopwords.
 *
 * Charset:   iso-8859-9
 * Language:  tr
 *
 * @package locale_stopwords_lib
 */
 
class StopWords_tr extends StopWords
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function StopWords_tr()
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
			"b",
			"c",
			"ç",
			"d",
			"e",
			"f",
			"g",
			"ð",
			"h",
			"ý",
			"i",
			"j",
			"k",
			"l",
			"m",
			"n",
			"o",
			"ö",
			"p",
			"q",
			"r",
			"s",
			"þ",
			"t",
			"u",
			"ü",
			"v",
			"w",
			"x",
			"y",
			"z",
			"ben",
			"beni",
			"benim",
			"benden",
			"bana",
			"sen",
			"seni",
			"senin",
			"senden",
			"onu",
			"ona",
			"ondan",
			"biz",
			"bizi",
			"bizden",
			"bizim",
			"siz",
			"sizi",
			"sizden",
			"sizin",
			"onlar",
			"onlari",
			"onlarýn",
			"onlardan",
			"ve",
			"veya",
			"da",
			"de",
			"ki",
			"çünkü",
			"niye",
			"neden",
			"nasýl",
			"nerde",
			"nerede",
			"nereye",
			"niçin",
			"mi",
			"mý",
			"mu",
			"mü",
			"kim",
			"kimi",
			"kimden",
			"kime",
			"bir",
			"iki",
			"üç",
			"dört",
			"beþ",
			"altý",
			"yedi",
			"sekiz",
			"dokuz",
			"on",
			"yirmi",
			"otuz",
			"kýrk",
			"elli",
			"altmýþ",
			"yetmiþ",
			"seksen",
			"doksan",
			"yüz",
			"bin",
			"milyon",
			"milyar",
			"trilyon",
			"katrilyon",
			"en",
			"hiç",
			"ile",
			"bu",
			"bunu",
			"bunun",
			"buna",
			"bunda",
			"bundan",
			"þu",
			"þunu",
			"þuna",
			"þunda",
			"þundan",
			"birþey",
			"þey",
			"þeyi",
			"þeyden",
			"þeyler",
			"birþeyi",
			"gibi",
			"ne",
			"hem",
			"daha",
			"dahi",
			"kez",
			"defa",
			"ya",
			"yani",
			"her",
			"hep",
			"hepsi",
			"belki",
			"sanki",
			"birkez",
			"tüm",
			"çok",
			"için",
			"ama",
			"acaba",
			"diye",
			"bazý",
			"ise",
			"birkaç",
			"biri"
		);
	}
} // END OF StopWords_tr

?>
