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
 * Czech stopwords.
 *
 * Charset:  us-ascii
 * Language: cz
 *
 * @package locale_stopwords_lib
 */
 
class StopWords_cz extends StopWords
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function StopWords_cz()
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
			"se",
			"dnes",
			"cz",
			"timto",
			"budes",
			"budem",
			"byli",
			"jses",
			"muj",
			"svym",
			"ta",
			"tomto",
			"tohle",
			"tuto",
			"tyto",
			"jej",
			"zda",
			"proc",
			"mate",
			"tato",
			"kam",
			"tohoto",
			"kdo",
			"kteri",
			"mi",
			"nam",
			"tom",
			"tomuto",
			"mit",
			"nic",
			"proto",
			"kterou",
			"byla",
			"toho",
			"protoze",
			"asi",
			"ho",
			"nasi",
			"napiste",
			"re",
			"coz",
			"tim",
			"takze",
			"svych",
			"jeji",
			"svymi",
			"jste",
			"aj",
			"tu",
			"tedy",
			"teto",
			"bylo",
			"kde",
			"ke",
			"prave",
			"ji",
			"nad",
			"nejsou",
			"ci",
			"pod",
			"tema",
			"mezi",
			"pres",
			"ty",
			"pak",
			"vam",
			"ani",
			"kdyz",
			"vsak",
			"ne",
			"jsem",
			"tento",
			"clanku",
			"clanky",
			"aby",
			"jsme",
			"pred",
			"pta",
			"jejich",
			"byl",
			"jeste",
			"az",
			"bez",
			"take",
			"pouze",
			"prvni",
			"vase",
			"ktera",
			"nas",
			"novy",
			"tipy",
			"pokud",
			"muze",
			"strana",
			"jeho",
			"sve",
			"jine",
			"zpravy",
			"nove",
			"neni",
			"vas",
			"jen",
			"podle",
			"zde",
			"clanek",
			"uz",
			"email",
			"byt",
			"vice",
			"bude",
			"jiz",
			"nez",
			"ktery",
			"by",
			"ktere",
			"co",
			"nebo",
			"ten",
			"tak",
			"ma",
			"pri",
			"od",
			"po",
			"jsou",
			"jak",
			"dalsi",
			"ale",
			"si",
			"ve",
			"to",
			"jako",
			"za",
			"zpet",
			"ze",
			"do",
			"pro",
			"je",
			"na"
		);
	}
} // END OF StopWords_cz

?>
