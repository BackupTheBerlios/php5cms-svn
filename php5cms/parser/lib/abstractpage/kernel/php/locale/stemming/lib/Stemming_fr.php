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


using( 'locale.stemming.lib.Stemming' );


/**
 * @package locale_stemming_lib
 */
 
class Stemming_fr extends Stemming
{
	/**
	 * Constructor
	 *
	 * @param  array  $options  Array of options
	 * @access public
	 */
	function Stemming_fr( $options = array() )
	{
		$this->Stemming( $options );
	}

	
	/**
     *  Takes a word and returns it reduced to its stem.
     *
     *  Non-alphanumerics and hyphens are removed, and if the word is less than
     *  three characters in length, it will be stemmed according to the five-step
     *  Porter stemming algorithm.
     *
     *  Note special cases here: hyphenated words (such as half-life) will only
     *  have the base after the last hyphen stemmed (so half-life would only have
     *  "life" subject to stemming).
     *
     *  @param  string $word Word to reduce
     *  @access public
     *  @return string Stemmed word
     */
  	function stem( $word = "" )
  	{
		return $word;
	}
} // END OF Stemming_fr

?>
