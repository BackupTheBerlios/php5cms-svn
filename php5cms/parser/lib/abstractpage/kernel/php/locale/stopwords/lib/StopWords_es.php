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
 * Spanish stopwords.
 *
 * Charset:   latin1
 * Language:  es
 *
 * @package locale_stopwords_lib
 */
 
class StopWords_es extends StopWords
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function StopWords_es()
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
			"actual",
			"de",
			"se",
			"y",
			"un",
			"una",
			"unas",
			"unos",
			"uno",
			"sobre",
			"todo",
			"también",
			"tras",
			"otro",
			"algún",
			"alguno",
			"alguna",
			"algunos",
			"algunas",
			"ser",
			"es",
			"soy",
			"eres",
			"somos",
			"sois",
			"estoy",
			"esta",
			"estamos",
			"estais",
			"estan",
			"como",
			"en",
			"para",
			"atras",
			"porque",
			"por qué",
			"estado",
			"estaba",
			"ante",
			"antes",
			"siendo",
			"ambos",
			"pero",
			"por",
			"poder",
			"puede",
			"puedo",
			"podemos",
			"podeis",
			"pueden",
			"fui",
			"fue",
			"fuimos",
			"fueron",
			"hacer",
			"hago",
			"hace",
			"hacemos",
			"haceis",
			"hacen",
			"cada",
			"fin",
			"incluso",
			"primero",
			"desde",
			"conseguir",
			"consigo",
			"consigue",
			"consigues",
			"conseguimos",
			"consiguen",
			"ir",
			"voy",
			"va",
			"vamos",
			"vais",
			"van",
			"vaya",
			"gueno",
			"ha",
			"tener",
			"tengo",
			"tiene",
			"tenemos",
			"teneis",
			"tienen",
			"el",
			"la",
			"lo",
			"las",
			"los",
			"su",
			"aqui",
			"mio",
			"tuyo",
			"ellos",
			"ellas",
			"nos",
			"nosotros",
			"vosotros",
			"vosotras",
			"si",
			"dentro",
			"solo",
			"solamente",
			"saber",
			"sabes",
			"sabe",
			"sabemos",
			"sabeis",
			"saben",
			"ultimo",
			"largo",
			"bastante",
			"haces",
			"muchos",
			"aquellos",
			"aquellas",
			"sus",
			"entonces",
			"tiempo",
			"verdad",
			"verdadero",
			"verdadera",
			"cierto",
			"ciertos",
			"cierta",
			"ciertas",
			"intentar",
			"intento",
			"intenta",
			"intentas",
			"intentamos",
			"intentais",
			"intentan",
			"dos",
			"bajo",
			"arriba",
			"encima",
			"usar",
			"uso",
			"usas",
			"usa",
			"usamos",
			"usais",
			"usan",
			"emplear",
			"empleo",
			"empleas",
			"emplean",
			"ampleamos",
			"empleais",
			"valor",
			"muy",
			"era",
			"eras",
			"eramos",
			"eran",
			"modo",
			"bien",
			"cual",
			"cuando",
			"donde",
			"mientras",
			"quien",
			"con",
			"entre",
			"sin",
			"trabajo",
			"trabajar",
			"trabajas",
			"trabaja",
			"trabajamos",
			"trabajais",
			"trabajan",
			"podria",
			"podrias",
			"podriamos",
			"podrian",
			"podriais",
			"yo",
			"aquel"
		);
	}
} // END OF StopWords_es

?>
