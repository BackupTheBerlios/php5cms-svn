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
 * Portuguese stopwords.
 *
 * Charset:   latin1
 * Language:  pt
 *
 * @package locale_stopwords_lib
 */
 
class StopWords_pt extends StopWords
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function StopWords_pt()
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
			"as",
			"via",
			"se",
			"de",
			"último",
			"é",
			"acerca",
			"agora",
			"algmas",
			"alguns",
			"ali",
			"ambos",
			"antes",
			"apontar",
			"aquela",
			"aquelas",
			"aquele",
			"aqueles",
			"aqui",
			"atrás",
			"bem",
			"bom",
			"cada",
			"caminho",
			"cima",
			"com",
			"como",
			"comprido",
			"conhecido",
			"corrente",
			"das",
			"debaixo",
			"dentro",
			"desde",
			"desligado",
			"deve",
			"devem",
			"deverá",
			"direita",
			"diz",
			"dizer",
			"dois",
			"dos",
			"e",
			"ela",
			"ele",
			"eles",
			"em",
			"enquanto",
			"então",
			"está",
			"estão",
			"estado",
			"estar",
			"estará",
			"este",
			"estes",
			"esteve",
			"estive",
			"estivemos",
			"estiveram",
			"eu",
			"fará",
			"faz",
			"fazer",
			"fazia",
			"fez",
			"fim",
			"foi",
			"fora",
			"horas",
			"iniciar",
			"inicio",
			"ir",
			"irá",
			"ista",
			"iste",
			"isto",
			"ligado",
			"maioria",
			"maiorias",
			"mais",
			"mas",
			"mesmo",
			"meu",
			"muito",
			"muitos",
			"nós",
			"não",
			"nome",
			"nosso",
			"novo",
			"o",
			"onde",
			"os",
			"ou",
			"outro",
			"para",
			"parte",
			"pegar",
			"pelo",
			"pessoas",
			"pode",
			"poderá",
			"podia",
			"por",
			"porque",
			"povo",
			"promeiro",
			"quê",
			"qual",
			"qualquer",
			"quando",
			"quem",
			"quieto",
			"são",
			"saber",
			"sem",
			"ser",
			"seu",
			"somente",
			"têm",
			"tal",
			"também",
			"tem",
			"tempo",
			"tenho",
			"tentar",
			"tentaram",
			"tente",
			"tentei",
			"teu",
			"teve",
			"tipo",
			"tive",
			"todos",
			"trabalhar",
			"trabalho",
			"tu",
			"um",
			"uma",
			"umas",
			"uns",
			"usa",
			"usar",
			"valor",
			"veja",
			"ver",
			"verdade",
			"verdadeiro",
			"você"
		);
	}
} // END OF StopWords_pt

?>
