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


/**
 * @package locale_stopwords_lib
 */
 
class StopWords extends PEAR
{
	/**
	 * list of stop words
	 *
	 * @access public
	 */
	var $words = array();

	
	/**
	 * @access public
	 * @static
	 */
	function &factory( $language = "" )
	{
		$lang_class = "StopWords_" . strtolower( $language );
		
		using( 'locale.stopwords.lib.' . $lang_class );
		
		if ( class_registered( $lang_class ) )
			return new $lang_class;
		else
			return PEAR::raiseError( "Driver not supported." );
	}
	
	/**
	 * Fuzzy method for guessing a language.
	 *
	 * @access public
	 * @static
	 */
	function guess( $text = "" )
	{
		$lang    = StopWords::getAvailLanguages();
		$count   = count( $lang );
		$results = array();
		
		// Loop through available languages.
		for ( $n = 0; $n < $count; ++$n, next( $lang ) )
		{
			$current_lang  =  $lang[$n];
			$stopWordsImpl =& StopWordsFactory::factory( $current_lang );
			
			if ( !PEAR::isError( $stopWordsImpl ) )
				$results[$lang] = $stopWordsImpl->count( $text );
		}
		
		return $results;
	}
	
	/**
	 * @access public
	 */
	function is_in( $word )
	{
		return in_array( strtolower( $word ), $this->words );
	}
	
	/**
	 * @access public
	 */
	function getStopWords()
	{
		return $this->words;
	}

	/**
	 * Remove stop words.
	 *
	 * @param  string
	 * @access public
	 * @return string	 
	 */
	function removeStopWords( $text = "" )
	{
		$arr = explode( " ", $text );
		$results = array();
		
        if ( !is_array( $arr ) )
            $arr = split( "[ ,;\n\r\t]+", trim( $arr ) );

        foreach ( $arr as $word )
		{
            if ( !in_array( strtolower( $word ), $this->words ) )
                $results[] = $word;
        }
		
		return join( " ", $results );
	}
	
	/**
	 * @access public
	 */	
	function getIndexWords( $text = "", $realwords = true )
	{
		$indexable = $this->_stripStopWords( $text );
		
		if ( $realwords )
			$indexable = preg_replace( "/(\s+)[^a-zA-Z0-9](\s+)/", " ", $indexable );
			
		return ( split( "[[:space:]]+", $indexable ) );
	}

	/**
	 * @access public
	 */	
	function strip( $text = "", $caseinsensitive = false, $specialchars = false )
	{
		return PEAR::raiseError( "Abstract method." );
	}
	
	/**
	 * @access private
	 */	
	function count( $text = "" )
	{
		return PEAR::raiseError( "Abstract method." );
	}
	
	
	/**
	 * @access public
	 * @static
	 */
	function getAvailLanguages()
	{
		static $avail_languages;
		$avail_languages = array(
			"ct",
			"cz",
			"de",
			"dk",
			"en",
			"es",
			"fr",
			"hu",
			"it",
			"lt",
			"nl",
			"no",
			"pl",
			"pt",
			"ru",
			"sk",
			"tr",
			"ua"
		);
		
		return $avail_languages;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function isAvailable( $language = "" )
	{
		return in_array( $language, StopWords::getAvailLanguages() );
	}
	
	
	// private methods

	/**
	 * @access private
	 */	
	function _stripStopWords( $content = "" )
	{
		$count = count( $this->words );
		
		// Loop through the stopwords array.
		for ( $n = 0; $n < $count; ++$n, next( $this->words ) )
		{
			// Search for stopwords in content.
			$search  = "$this->words[$n]";
			$content = preg_replace( "'$search'i", "", $content );
		}
		
		return $content;
	}
} // END OF StopWords

?>
