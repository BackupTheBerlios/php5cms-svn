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
 * @package locale
 */
 
class LanguageSuggestion extends PEAR 
{
	/**
	 * @access public
	 */
	var $wordDict;

	/**
	 * @access public
	 */
	var $wordDictEn = array(
		'the' 			=> 'en', 
		'of'			=> 'en', 
		'to'			=> 'en', 
		'and'			=> 'en', 
		'a'				=> 'en', 
		'in'			=> 'en', 
		'for'			=> 'en', 
		'is'			=> 'en', 
		'The'			=> 'en', 
		'that'			=> 'en', 
		'on'			=> 'en', 
		'said'			=> 'en', 
		'with'			=> 'en', 
		'be'			=> 'en', 
		'was'			=> 'en', 
		'by'			=> 'en', 
		'as'			=> 'en', 
		'are'			=> 'en', 
		'at'			=> 'en', 
		'from'			=> 'en', 
		'it'			=> 'en', 
		'has'			=> 'en', 
		'an'			=> 'en', 
		'have'			=> 'en', 
		'will'			=> 'en', 
		'or'			=> 'en', 
		'its'			=> 'en', 
		'he'			=> 'en', 
		'not'			=> 'en', 
		'were'			=> 'en', 
		'which'			=> 'en', 
		'this'			=> 'en', 
		'but'			=> 'en', 
		'can'			=> 'en', 
		'more'			=> 'en', 
		'his'			=> 'en', 
		'been'			=> 'en', 
		'would'			=> 'en', 
		'about'			=> 'en', 
		'their'			=> 'en', 
		'also'			=> 'en', 
		'they'			=> 'en', 
		'million'		=> 'en', 
		'had'			=> 'en', 
		'than'			=> 'en', 
		'up'			=> 'en', 
		'who'			=> 'en', 
		'In'			=> 'en', 
		'one'			=> 'en', 
		'you'			=> 'en', 
		'new'			=> 'en', 
		'A'				=> 'en', 
		'I'				=> 'en', 
		'other'			=> 'en', 
		'year'			=> 'en', 
		'all'			=> 'en', 
		'two'			=> 'en', 
		'S'				=> 'en', 
		'But'			=> 'en', 
		'It'			=> 'en', 
		'company'		=> 'en', 
		'into'			=> 'en', 
		'U'				=> 'en', 
		'Mr.'			=> 'en', 
		'system'		=> 'en', 
		'some'			=> 'en', 
		'when'			=> 'en', 
		'out'			=> 'en', 
		'last'			=> 'en', 
		'only'			=> 'en', 
		'after'			=> 'en', 
		'first'			=> 'en', 
		'time'			=> 'en', 
		'says'			=> 'en', 
		'He'			=> 'en', 
		'years'			=> 'en', 
		'market'		=> 'en', 
		'no'			=> 'en', 
		'over'			=> 'en', 
		'we'			=> 'en', 
		'could'			=> 'en', 
		'if'			=> 'en', 
		'people'		=> 'en', 
		'percent'		=> 'en', 
		'such'			=> 'en', 
		'This'			=> 'en', 
		'most'			=> 'en', 
		'use'			=> 'en', 
		'because'		=> 'en', 
		'any'			=> 'en', 
		'data'			=> 'en', 
		'there'			=> 'en', 
		'them'			=> 'en', 
		'government'	=> 'en', 
		'may'			=> 'en', 
		'software'		=> 'en', 
		'so'			=> 'en', 
		'New'			=> 'en', 
		'now'			=> 'en', 
		'many'			=> 'en'
	);
	
	/**
	 * @access public
	 */
	var $wordDictFr = array(
		'de'			=> 'fr', 
		'la'			=> 'fr', 
		'le'			=> 'fr', 
		'et'			=> 'fr', 
		'les'			=> 'fr', 
		'des'			=> 'fr', 
		'en'			=> 'fr', 
		'un'			=> 'fr', 
		'du'			=> 'fr', 
		'une'			=> 'fr', 
		'que'			=> 'fr', 
		'est'			=> 'fr', 
		'pour'			=> 'fr', 
		'qui'			=> 'fr', 
		'dans'			=> 'fr', 
		'a'				=> 'fr', 
		'par'			=> 'fr', 
		'plus'			=> 'fr', 
		'pas'			=> 'fr', 
		'au'			=> 'fr', 
		'sur'			=> 'fr', 
		'ne'			=> 'fr', 
		'se'			=> 'fr', 
		'Le'			=> 'fr', 
		'ce'			=> 'fr', 
		'il'			=> 'fr', 
		'sont'			=> 'fr', 
		'La'			=> 'fr', 
		'Les'			=> 'fr', 
		'ou'			=> 'fr', 
		'avec'			=> 'fr', 
		'son'			=> 'fr', 
		'Il'			=> 'fr', 
		'aux'			=> 'fr', 
		'd\'un'			=> 'fr', 
		'En'			=> 'fr', 
		'cette'			=> 'fr', 
		'd\'une'		=> 'fr', 
		'ont'			=> 'fr', 
		'ses'			=> 'fr', 
		'mais'			=> 'fr', 
		'comme'			=> 'fr', 
		'on'			=> 'fr', 
		'tout'			=> 'fr', 
		'nous'			=> 'fr', 
		'sa'			=> 'fr', 
		'Mais'			=> 'fr', 
		'fait'			=> 'fr', 
		'été'			=> 'fr', 
		'aussi'			=> 'fr', 
		'leur'			=> 'fr', 
		'bien'			=> 'fr', 
		'peut'			=> 'fr', 
		'ces'			=> 'fr', 
		'y'				=> 'fr', 
		'deux'			=> 'fr', 
		'A'				=> 'fr', 
		'ans'			=> 'fr', 
		'l'				=> 'fr', 
		'encore'		=> 'fr', 
		'n\'est'		=> 'fr', 
		'marché'		=> 'fr', 
		'd'				=> 'fr', 
		'Pour'			=> 'fr', 
		'donc'			=> 'fr', 
		'cours'			=> 'fr', 
		'qu\'il'		=> 'fr', 
		'moins'			=> 'fr', 
		'sans'			=> 'fr', 
		'C\'est'		=> 'fr', 
		'Et'			=> 'fr', 
		'si'			=> 'fr', 
		'entre'			=> 'fr', 
		'Un'			=> 'fr', 
		'Ce'			=> 'fr', 
		'faire'			=> 'fr', 
		'elle'			=> 'fr', 
		'c\'est'		=> 'fr', 
		'peu'			=> 'fr', 
		'vous'			=> 'fr', 
		'Une'			=> 'fr', 
		'prix'			=> 'fr', 
		'On'			=> 'fr', 
		'dont'			=> 'fr', 
		'lui'			=> 'fr', 
		'également'		=> 'fr', 
		'Dans'			=> 'fr', 
		'effet'			=> 'fr', 
		'pays'			=> 'fr', 
		'cas'			=> 'fr', 
		'De'			=> 'fr', 
		'millions'		=> 'fr', 
		'Belgique'		=> 'fr', 
		'BEF'			=> 'fr', 
		'mois'			=> 'fr', 
		'leurs'			=> 'fr', 
		'taux'			=> 'fr', 
		'années'		=> 'fr', 
		'temps'			=> 'fr', 
		'groupe'		=> 'fr'
	);

	/**
	 * @access public
	 */
	var $wordDictDe = array(
		'der'			=> 'de', 
		'die'			=> 'de', 
		'und'			=> 'de', 
		'in'			=> 'de', 
		'den'			=> 'de', 
		'von'			=> 'de', 
		'zu'			=> 'de', 
		'das'			=> 'de', 
		'mit'			=> 'de', 
		'sich'			=> 'de', 
		'des'			=> 'de', 
		'auf'			=> 'de', 
		'für'			=> 'de', 
		'ist'			=> 'de', 
		'im'			=> 'de', 
		'dem'			=> 'de', 
		'nicht'			=> 'de', 
		'ein'			=> 'de', 
		'Die'			=> 'de', 
		'eine'			=> 'de', 
		'als'			=> 'de', 
		'auch'			=> 'de', 
		'es'			=> 'de', 
		'an'			=> 'de', 
		'werden'		=> 'de', 
		'aus'			=> 'de', 
		'er'			=> 'de', 
		'hat'			=> 'de', 
		'daß'			=> 'de', 
		'sie'			=> 'de', 
		'nach'			=> 'de', 
		'wird'			=> 'de', 
		'bei'			=> 'de', 
		'einer'			=> 'de', 
		'Der'			=> 'de', 
		'um'			=> 'de', 
		'am'			=> 'de', 
		'sind'			=> 'de', 
		'noch'			=> 'de', 
		'wie'			=> 'de', 
		'einem'			=> 'de', 
		'über'			=> 'de', 
		'einen'			=> 'de', 
		'Das'			=> 'de', 
		'so'			=> 'de', 
		'Sie'			=> 'de', 
		'zum'			=> 'de', 
		'war'			=> 'de', 
		'haben'			=> 'de', 
		'nur'			=> 'de', 
		'oder'			=> 'de', 
		'aber'			=> 'de', 
		'vor'			=> 'de', 
		'zur'			=> 'de', 
		'bis'			=> 'de', 
		'mehr'			=> 'de', 
		'durch'			=> 'de', 
		'man'			=> 'de', 
		'sein'			=> 'de', 
		'wurde'			=> 'de', 
		'sei'			=> 'de', 
		'In'			=> 'de', 
		'Prozent'		=> 'de', 
		'hatte'			=> 'de', 
		'kann'			=> 'de', 
		'gegen'			=> 'de', 
		'vom'			=> 'de', 
		'können'		=> 'de', 
		'schon'			=> 'de', 
		'wenn'			=> 'de', 
		'habe'			=> 'de', 
		'seine'			=> 'de', 
		'Mark'			=> 'de', 
		'ihre'			=> 'de', 
		'dann'			=> 'de', 
		'unter'			=> 'de', 
		'wir'			=> 'de', 
		'soll'			=> 'de', 
		'ich'			=> 'de', 
		'eines'			=> 'de', 
		'Es'			=> 'de', 
		'Jahr'			=> 'de', 
		'zwei'			=> 'de', 
		'Jahren'		=> 'de', 
		'diese'			=> 'de', 
		'dieser'		=> 'de', 
		'wieder'		=> 'de', 
		'keine'			=> 'de', 
		'Uhr'			=> 'de', 
		'seiner'		=> 'de', 
		'worden'		=> 'de', 
		'Und'			=> 'de', 
		'will'			=> 'de', 
		'zwischen'		=> 'de', 
		'Im'			=> 'de', 
		'immer'			=> 'de', 
		'Millionen'		=> 'de', 
		'Ein'			=> 'de', 
		'was'			=> 'de', 
		'sagte'			=> 'de'
	);

	/**
	 * @access public
	 */
	var $wordDictNl = array(
		'de'			=> 'nl', 
		'van'			=> 'nl', 
		'een'			=> 'nl', 
		'het'			=> 'nl', 
		'en'			=> 'nl', 
		'in'			=> 'nl', 
		'is'			=> 'nl', 
		'dat'			=> 'nl', 
		'op'			=> 'nl', 
		'te'			=> 'nl', 
		'De'			=> 'nl', 
		'zijn'			=> 'nl', 
		'voor'			=> 'nl', 
		'met'			=> 'nl', 
		'die'			=> 'nl', 
		'niet'			=> 'nl', 
		'aan'			=> 'nl', 
		'er'			=> 'nl', 
		'om'			=> 'nl', 
		'Het'			=> 'nl', 
		'ook'			=> 'nl', 
		'als'			=> 'nl', 
		'dan'			=> 'nl', 
		'maar'			=> 'nl', 
		'bij'			=> 'nl', 
		'of'			=> 'nl', 
		'uit'			=> 'nl', 
		'nog'			=> 'nl', 
		'worden'		=> 'nl', 
		'door'			=> 'nl', 
		'naar'			=> 'nl', 
		'heeft'			=> 'nl', 
		'tot'			=> 'nl', 
		'ze'			=> 'nl', 
		'wordt'			=> 'nl', 
		'over'			=> 'nl', 
		'hij'			=> 'nl', 
		'In'			=> 'nl', 
		'meer'			=> 'nl', 
		'jaar'			=> 'nl', 
		'was'			=> 'nl', 
		'ik'			=> 'nl', 
		'kan'			=> 'nl', 
		'je'			=> 'nl', 
		'zich'			=> 'nl', 
		'al'			=> 'nl', 
		'hebben'		=> 'nl', 
		'geen'			=> 'nl', 
		'hun'			=> 'nl', 
		'we'			=> 'nl', 
		'wat'			=> 'nl', 
		'Een'			=> 'nl', 
		'Maar'			=> 'nl', 
		'werd'			=> 'nl', 
		'moet'			=> 'nl', 
		'wel'			=> 'nl', 
		'kunnen'		=> 'nl', 
		'Dat'			=> 'nl', 
		'nu'			=> 'nl', 
		'dit'			=> 'nl', 
		'deze'			=> 'nl', 
		'zal'			=> 'nl', 
		'Ik'			=> 'nl', 
		'veel'			=> 'nl', 
		'zo'			=> 'nl', 
		'En'			=> 'nl', 
		'andere'		=> 'nl', 
		'nieuwe'		=> 'nl', 
		'zou'			=> 'nl', 
		'twee'			=> 'nl', 
		'moeten'		=> 'nl', 
		'onder'			=> 'nl', 
		'eerste'		=> 'nl', 
		'haar'			=> 'nl', 
		'Van'			=> 'nl', 
		'wil'			=> 'nl', 
		'tegen'			=> 'nl', 
		'men'			=> 'nl', 
		'mensen'		=> 'nl', 
		'gaat'			=> 'nl', 
		'tussen'		=> 'nl', 
		'grote'			=> 'nl', 
		'waar'			=> 'nl', 
		'goed'			=> 'nl', 
		'maken'			=> 'nl', 
		'dus'			=> 'nl', 
		'alleen'		=> 'nl', 
		'Hij'			=> 'nl', 
		'Op'			=> 'nl', 
		'frank'			=> 'nl', 
		'ons'			=> 'nl', 
		'u'				=> 'nl', 
		'daar'			=> 'nl', 
		'na'			=> 'nl', 
		'had'			=> 'nl', 
		'gaan'			=> 'nl', 
		'alle'			=> 'nl', 
		'Als'			=> 'nl', 
		'Er'			=> 'nl', 
		'één'			=> 'nl'
	);
	
	/**
	 * @access public
	 */
	var $prefixDict = array(
		'off'   => 'en', 
		'to'    => 'en', 
		'under' => 'en', 
		'thou'  => 'en', 
		'mont'  => 'fr', 
		'contr' => 'fr', 
		'mal'   => 'fr', 
		'ver'   => 'de', 
		'zu'    => 'de', 
		'los'   => 'de', 
		'gut'   => 'de'
	);
	
	/**
	 * @access public
	 */
	var $suffixDict = array(
		'son'   => 'en', 
		'day'   => 'en', 
		'ing'   => 'en', 
		'ly'    => 'en', 
		'ght'   => 'en', 
		'ique'  => 'fr', 
		'tude'  => 'fr', 
		'ont'   => 'fr', 
		'nal'   => 'fr', 
		'tung'  => 'de', 
		'heim'  => 'de', 
		'zeug'  => 'de'
	);

	/**
	 * @access public
	 */
	var $specialChars = '.,!?"()[]{}!§$%&/*+#';

	
	/**
	 * @access public
	 */
	function detectText( $string ) 
	{
		$this->_loadDictionary();
		
		$ret = array(
			'lang'   => '', 
			'hits'   => array( 'en' => 0,       'fr' => 0,       'de' => 0 ), 
			'reason' => array( 'en' => array(), 'fr' => array(), 'de' => array() )
		);
		
		$stringLower = strtolower( $string );
		$stringClean = $stringLower;
		$stringArray = explode( ' ', $stringClean );
		
		while ( list( $k ) = each( $stringArray ) ) 
		{
			$word = $stringArray[$k];
			
			if ( isset( $this->wordDict[$word] ) ) 
			{
				if ( is_array( $this->wordDict[$word] ) ) 
				{
					while ( list( $k2 ) = each( $this->wordDict[$word] ) ) 
					{
						$ret['hits'][$this->wordDict[$word][$k2]]++;
						$ret['reason'][$this->wordDict[$word][$k2]][] = $word;
					}
				} 
				else 
				{
					$ret['hits'][$this->wordDict[$word]]++;
					$ret['reason'][$this->wordDict[$word]][] = $word;
				}
				
				continue;	
			} 
			else 
			{
				$success = false;
				reset( $this->prefixDict );
				
				while ( list( $k2 ) = each( $this->prefixDict ) ) 
				{
					if ( substr( $word, 0, strlen( $k2 ) ) == $k2 ) 
					{
						$ret['hits'][$this->prefixDict[$k2]]++;
						$ret['hits'][$this->prefixDict[$k2]][] = $word;
						
						$success = true;
						break;
					}
				}
				
				if ( $success ) 
					continue;
				
				reset( $this->suffixDict );
				
				while ( list( $k2 ) = each( $this->suffixDict ) ) 
				{
					if ( substr( $word, 0, strlen( $k2 ) ) == $k2 ) 
					{
						$ret['hits'][$this->suffixDict[$k2]]++;
						$ret['hits'][$this->suffixDict[$k2]][] = $word;
						
						break;	
					}
				}
			}
		}
		
		$ret['lang'] = $this->_max( $ret['hits'], 'key' );
		return $ret;
	}


	// private methods

	/**
	 * @access private
	 */	
	function _loadDictionary() 
	{
		if ( is_array( $this->wordDict ) ) 
			return;
			
		$t = array();
		
		for ( $i = 1; $i <= 4; $i++ ) 
		{
			switch ( $i ) 
			{
				case 1:
					$tempWordDict =& $this->wordDictEn;
					break;
				
				case 2:
					$tempWordDict =& $this->wordDictFr;
					break;
					
				case 3:
					$tempWordDict =& $this->wordDictDe;
					break;
				
				case 4:
					$tempWordDict =& $this->wordDictNl;
					break;
			}

			while ( list( $k ) = each( $tempWordDict ) ) 
			{
				$key = strtolower( $k );
				
				if ( isset( $t[$key] ) ) 
				{
					if ( is_string( $t[$key] ) ) 
					{
						if ( $t[$key] != $tempWordDict[$k] ) 
							$t[$key] = array( $t[$key], $tempWordDict[$k] );
					}
					else 
					{ 
						if ( !in_array( $tempWordDict[$k], $t[$key] ) ) 
							$t[$key][] = $tempWordDict[$k];
					}
				} 
				else 
				{
					$t[$key] = $tempWordDict[$k];
				}
			}
		}

		$this->wordDict = &$t;
	}

	/**
	 * @access private
	 */	
	function _max( $array, $what = 'value' ) 
	{
		if ( !is_array( $array ) ) 
			return false;
		
		$ret = false;
		reset( $array );
		
		while ( list( $k ) = each( $array ) ) 
		{
			if ( is_numeric( $array[$k] ) ) 
			{
				if ( $what == 'value' ) 
				{
					if ( ( $ret === false ) || ( $array[$k] > $ret ) )
						$ret = $array[$k];
				} 
				else 
				{
					if ( ( $ret === false ) || ( $array[$k] > $array[$ret] ) )
						$ret = $k;
				}
			}
		}

		return ( $what == 'value' )? (double)$ret : (string)$ret;
	}
} // END OF LanguageSuggestion

?>
