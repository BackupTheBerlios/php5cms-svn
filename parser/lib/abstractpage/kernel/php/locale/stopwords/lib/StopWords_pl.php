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
 * Polish stopwords.
 *
 * Charset:   latin2
 * Language:  pl
 *
 * @package locale_stopwords_lib
 */
 
class StopWords_pl extends StopWords
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function StopWords_pl()
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
			"i",
			"w",
			"z",
			"o",
			"na",
			"do",
			"a",
			"jest",
			"od",
			"przez",
			"to",
			"po",
			"si�",
			"dla",
			"nie",
			"oraz",
			"jak",
			"za",
			"ze",
			"tym",
			"co",
			"przy",
			"tego",
			"ich",
			"ale",
			"tylko",
			"pod",
			"s�",
			"jego",
			"jako",
			"czy",
			"�e",
			"tak",
			"ma",
			"tej",
			"lub",
			"tak�e",
			"jednak",
			"ten",
			"jej",
			"u",
			"ju�",
			"nad",
			"tych",
			"kt�re",
			"te",
			"jeszcze",
			"bardzo",
			"mo�e",
			"bez",
			"innych",
			"im",
			"przed",
			"wszystkich",
			"we",
			"mo�na",
			"kt�rych",
			"wszystkim",
			"r�wnie�",
			"kt�ry",
			"nawet",
			"te�",
			"sobie",
			"tu",
			"nich",
			"by�o",
			"by�",
			"b�dzie",
			"wielu",
			"go",
			"gdy",
			"wiele",
			"ta",
			"nas",
			"aby",
			"gdzie",
			"bo",
			"wi�c",
			"tam",
			"kt�rzy",
			"kt�ra",
			"kilka",
			"mi�dzy",
			"naszego",
			"by�a",
			"bardziej",
			"przede",
			"nam",
			"wszystko",
			"swoje",
			"kt�rej",
			"ni�",
			"czyli",
			"zawsze",
			"kt�rym",
			"takie",
			"bowiem",
			"nim",
			"poza",
			"w�a�nie",
			"i�",
			"by�y",
			"kiedy",
			"mog�",
			"mi",
			"kt�rego",
			"mnie",
			"dlatego",
			"naszych",
			"je�li"
		);
	}
} // END OF StopWords_pl

?>
