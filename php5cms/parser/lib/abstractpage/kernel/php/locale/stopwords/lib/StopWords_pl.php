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
			"siê",
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
			"s±",
			"jego",
			"jako",
			"czy",
			"¿e",
			"tak",
			"ma",
			"tej",
			"lub",
			"tak¿e",
			"jednak",
			"ten",
			"jej",
			"u",
			"ju¿",
			"nad",
			"tych",
			"które",
			"te",
			"jeszcze",
			"bardzo",
			"mo¿e",
			"bez",
			"innych",
			"im",
			"przed",
			"wszystkich",
			"we",
			"mo¿na",
			"których",
			"wszystkim",
			"równie¿",
			"który",
			"nawet",
			"te¿",
			"sobie",
			"tu",
			"nich",
			"by³o",
			"by³",
			"bêdzie",
			"wielu",
			"go",
			"gdy",
			"wiele",
			"ta",
			"nas",
			"aby",
			"gdzie",
			"bo",
			"wiêc",
			"tam",
			"którzy",
			"która",
			"kilka",
			"miêdzy",
			"naszego",
			"by³a",
			"bardziej",
			"przede",
			"nam",
			"wszystko",
			"swoje",
			"której",
			"ni¿",
			"czyli",
			"zawsze",
			"którym",
			"takie",
			"bowiem",
			"nim",
			"poza",
			"w³a¶nie",
			"i¿",
			"by³y",
			"kiedy",
			"mog±",
			"mi",
			"którego",
			"mnie",
			"dlatego",
			"naszych",
			"je¶li"
		);
	}
} // END OF StopWords_pl

?>
