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
 * Norwegian stopwords.
 *
 * Charset:   latin1
 * Language:  no
 *
 * @package locale_stopwords_lib
 */
 
class StopWords_no extends StopWords
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function StopWords_no()
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
			"de",
			"alle",
			"andre",
			"arbeid",
			"av",
			"begge",
			"bort",
			"bra",
			"bruke",
			"da",
			"denne",
			"der",
			"deres",
			"det",
			"din",
			"disse",
			"du",
			"eller",
			"en",
			"ene",
			"eneste",
			"enhver",
			"enn",
			"er",
			"et",
			"folk",
			"for",
			"fordi",
			"forsÛke",
			"fra",
			"fÅ",
			"fÛr",
			"fÛrst",
			"gjorde",
			"gjÛre",
			"god",
			"gÅ",
			"ha",
			"hadde",
			"han",
			"hans",
			"hennes",
			"her",
			"hva",
			"hvem",
			"hver",
			"hvilken",
			"hvis",
			"hvor",
			"hvordan",
			"hvorfor",
			"i",
			"ikke",
			"inn",
			"innen",
			"kan",
			"kunne",
			"lage",
			"lang",
			"lik",
			"like",
			"makt",
			"mange",
			"med",
			"meg",
			"meget",
			"men",
			"mens",
			"mer",
			"mest",
			"min",
			"mye",
			"mÅ",
			"mÅte",
			"navn",
			"nei",
			"ny",
			"nÅ",
			"nÅr",
			"og",
			"ogsÅ",
			"om",
			"opp",
			"oss",
			"over",
			"part",
			"punkt",
			"pÅ",
			"rett",
			"riktig",
			"samme",
			"sant",
			"si",
			"siden",
			"sist",
			"skulle",
			"slik",
			"slutt",
			"som",
			"start",
			"stille",
			"sÅ",
			"tid",
			"til",
			"tilbake",
			"tilstand",
			"under",
			"ut",
			"uten",
			"var",
			"ved",
			"verdi",
			"vi",
			"vil",
			"ville",
			"vite",
			"vÅr",
			"vÖre",
			"vÖrt",
			"Å"
		);
	}
} // END OF StopWords_no

?>
