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
 * Danish stopwords.
 *
 * Charset:   latin1
 * Language:  da
 *
 * @package locale_stopwords_lib
 */
 
class StopWords_dk extends StopWords
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function StopWords_dk()
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
			"af",
			"alle",
			"andet",
			"andre",
			"at",
			"begge",
			"da",
			"de",
			"den",
			"denne",
			"der",
			"deres",
			"det",
			"dette",
			"dig",
			"din",
			"dog",
			"du",
			"ej",
			"eller",
			"en",
			"end",
			"ene",
			"eneste",
			"enhver",
			"et",
			"fem",
			"fire",
			"flere",
			"fleste",
			"for",
			"fordi",
			"forrige",
			"fra",
			"få",
			"før",
			"god",
			"han",
			"hans",
			"har",
			"hendes",
			"her",
			"hun",
			"hvad",
			"hvem",
			"hver",
			"hvilken",
			"hvis",
			"hvor",
			"hvordan",
			"hvorfor",
			"hvornår",
			"i",
			"ikke",
			"ind",
			"ingen",
			"intet",
			"jeg",
			"jeres",
			"kan",
			"kom",
			"kommer",
			"lav",
			"lidt",
			"lille",
			"man",
			"mand",
			"mange",
			"med",
			"meget",
			"men",
			"mens",
			"mere",
			"mig",
			"ned",
			"ni",
			"nogen",
			"noget",
			"ny",
			"nyt",
			"nær",
			"næste",
			"næsten",
			"og",
			"op",
			"otte",
			"over",
			"på",
			"se",
			"seks",
			"ses",
			"som",
			"stor",
			"store",
			"syv",
			"ti",
			"til",
			"to",
			"tre",
			"ud",
			"var"
		);
	}
} // END OF StopWords_dk

?>
