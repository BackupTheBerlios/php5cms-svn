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
 * Italian stopwords.
 *
 * Charset:   latin1
 * Language:  it
 *
 * @package locale_stopwords_lib
 */
 
class StopWords_it extends StopWords
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function StopWords_it()
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
			"far",
			"come",
			"per",
			"se",
			"in",
			"a",
			"pero",
			"quindi",
			"o",
			"ho",
			"questo",
			"quello",
			"chi",
			"che",
			"ma",
			"di",
			"da",
			"con",
			"su",
			"giu",
			"tra",
			"fra",
			"sei",
			"te",
			"un",
			"uno",
			"una",
			"due",
			"tre",
			"quattro",
			"cinque",
			"sette",
			"otto",
			"nove",
			"loro",
			"nostro",
			"vostro",
			"voi",
			"noi",
			"fine",
			"anche",
			"primo",
			"secondo",
			"terzo",
			"quarto",
			"quinto",
			"comprare",
			"vai",
			"buono",
			"avere",
			"qui",
			"lui",
			"io",
			"dentro",
			"qua",
			"lei",
			"solo",
			"ultimo",
			"lungo",
			"molto",
			"molti",
			"molta",
			"me",
			"piu",
			"tanto",
			"poco",
			"devo",
			"deve",
			"nome",
			"nuovo",
			"nuovi",
			"no",
			"ora",
			"adesso",
			"altro",
			"altri",
			"altre",
			"persone",
			"gente",
			"tempo",
			"lavoro",
			"stato",
			"alla",
			"allo",
			"allora",
			"le",
			"il",
			"meglio",
			"peggio",
			"indietro",
			"sul",
			"sulla",
			"fino",
			"va",
			"ai",
			"al",
			"ecco",
			"senza",
			"e",
			"volte",
			"sotto",
			"sopra",
			"lo",
			"la",
			"hai",
			"ha",
			"siamo",
			"siete",
			"sono",
			"quasi",
			"nella",
			"aveva",
			"del",
			"della",
			"dello",
			"stesso",
			"hanno",
			"stati",
			"ancora",
			"ben",
			"nei",
			"oltre",
			"invece",
			"soprattutto",
			"rispetto",
			"cui",
			"avevano",
			"promesso",
			"sia",
			"subito",
			"doppio",
			"triplo",
			"consecutivi",
			"consecutivo",
			"sembra",
			"sembrava",
			"fare",
			"sara",
			"cosa"
		);
	}
} // END OF StopWords_it

?>
