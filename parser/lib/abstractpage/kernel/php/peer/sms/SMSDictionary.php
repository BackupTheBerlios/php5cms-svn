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


define( 'SMS_MODE_LCASE', 1 );


/**
 * @package peer_sms
 */
 
class SMSDictionary extends PEAR
{
	/**
	 * @access public
	 */
	var $mode = null;

	/**
	 * @access public
	 */
	var $words = array(
		'aardvark'		=> 'RdvRk',
		'able'			=> 'abL',
		'access'		=> 'ax$',
		'accurate'		=> 'aQr8',
		'across'		=> 'aX',
		'activate'		=> 'activ8',
		'address'		=> 'adr$',
		'allergy'		=> 'LRG',
		'altogether'	=> 'al2gethr',
		'amazing'		=> 'amazN',
		'annoying'		=> 'anoyN',
		'any'			=> 'NE',
		'anyone'		=> 'NE1',
		'archive'		=> 'Rkive',
		'are'			=> 'R',
		'article'		=> 'RTcL',
		'artificial'	=> 'RTficL',
		'at'			=> '@',
		'atmosphere'	=> '@mosfER',
		'attack'		=> '@ak',
		'await'			=> 'aw8',

		'baby'			=> 'baB',
		'backward'		=> 'BWD',
		'balance'		=> 'balNs',
		'band'			=> 'B&',
		'batter'		=> 'b@R',
		'battery'		=> 'b@RE',
		'be'			=> 'B',
		'become'		=> 'Bcum',
		'before'		=> 'B4',
		'beside'		=> 'Bside',
		'better'		=> 'betR',
		'bland'			=> 'bl&',
		'bless'			=> 'bl$',
		'book'			=> 'b%k',
		'bossy'			=> 'boC',
		'brand'			=> 'br&',
		'brat'			=> 'br@',
		'breezy'		=> 'brEZ',
		'bubble'		=> 'bubL',

		'capable'		=> 'KpabL',
		'caress'		=> 'cRS',
		'cassette'		=> 'ca$et',
		'celebrate'		=> 'cLebr8',
		'central'		=> 'cNtrL',
		'centre'		=> 'centR',
		'certain'		=> 'crtN',
		'champagne'		=> 'bubLE',
		'chef'			=> 'chF',
		'chess'			=> 'ch$',
		'chew'			=> 'chU',
		'chocolate'		=> 'chocl@',
		'city'			=> 'CT',
		'clubbing'		=> 'clubN',
		'combat'		=> 'comb@',
		'coming'		=> 'comN',
		'command'		=> 'com&',
		'communicate'	=> 'comunic8',
		'company'		=> 'compNE',
		'complex'		=> 'complX',
		'complicate'	=> 'complic8',
		'congratulate'	=> 'congr@ul8',
		'cool'			=> 'c%l',
		'cosy'			=> 'koZ',
		'could'			=> 'c%d',
		'couldn\'t'		=> 'c%dNt',
		'couple'		=> 'cupL',
		'crate'			=> 'cr8',
		'crazy'			=> 'craZ',
		'create'		=> 'cre8',

		'date'			=> 'd8',
		'debate'		=> 'Db8',
		'decay'			=> 'DK',
		'decks'			=> 'dX',
		'deflate'		=> 'Dfl8',
		'degree'		=> 'DgrE',
		'delayed'		=> 'DlAd',
		'demand'		=> 'Dm&',
		'demonstration'	=> 'Dmonstr8N',
		'den'			=> 'dN',
		'deviate'		=> 'DV8',
		'deviant'		=> 'DVNt',
		'diamond'		=> 'dimNd',
		'dictate'		=> 'dict8',
		'dictionary'	=> 'DXNRE',
		'dinner'		=> 'dinR',
		'disappear'		=> 'DsapER',
		'does'			=> 'duz',
		'doesn\'t'		=> 'duzNt',
		'doing'			=> 'doN',
		'donate'		=> 'don8',
		'double'		=> 'dubL',
		'dress'			=> 'dr$',

		'easy'			=> 'EZ',
		'ecstasy'		=> 'XTC',
		'effects'		=> 'FX',
		'else'			=> 'Ls',
		'embarrass'		=> 'MbR$',
		'emergency'		=> 'MergNC',
		'empty'			=> 'MT',
		'end'			=> 'Nd',
		'energy'		=> 'NRG',
		'enjoy'			=> 'Njoy',
		'enough'		=> 'Nuf',
		'enter'			=> 'NtR',
		'entertain'		=> 'NtRtain',
		'escape'		=> 'Scape',
		'essay'			=> 'SA',
		'estate'		=> 'St8',
		'ever'			=> 'evR',
		'example'		=> 'XampL',
		'excellent'		=> 'XLNT',
		'expect'		=> 'Xpect',
		'expensive'		=> 'XpNsiv',
		'experience'	=> 'XperENs',
		'express'		=> 'Xpr$',
		'extra'			=> 'Xtra',

		'fantasy'		=> 'fantaC',
		'favour'		=> 'favR',
		'final'			=> 'finL',
		'firsthand'		=> '1sth&',
		'flat'			=> 'fl@',
		'food'			=> 'F%D',
		'foot'			=> 'f%t',
		'footie'		=> 'f%T',
		'four'			=> '4',
		'foreign'		=> '4N',
		'foreplay'		=> '4plA',
		'forever'		=> '4evR',
		'forget'		=> '4get',
		'forgive'		=> '4giv',
		'fortnight'		=> '4tnite',
		'fortunate'		=> '4tun8',
		'forward'		=> '4wardt',
		'frenzy'		=> 'frNZ',
		'friend'		=> 'frNd',

		'generally'		=> 'gNRLE',
		'generate'		=> 'gNR8',
		'generation'	=> 'gNR8n',
		'gentle'		=> 'gNtL',
		'glamorous'		=> 'glamRS',
		'good'			=> 'g%d',
		'graduate'		=> 'gradu8',
		'grand'			=> 'gr&',
		'great'			=> 'gr8',
		'greatly'		=> 'gr8ly',
		'groovy'		=> 'gr%V',
		'gruesome'		=> 'grUsum',
		'guarantee'		=> 'garNT',
		'guard'			=> 'gRd',

		'hammered'		=> 'hamRd',
		'hand'			=> 'h&',
		'handy'			=> 'h&D',
		'happen'		=> 'hapN',
		'hate'			=> 'h8',
		'heart'			=> 'hRt',
		'heavy'			=> 'hevE',
		'hello'			=> 'LO',
		'higher'		=> 'hiR',
		'humour'		=> 'humR',

		'illegal'		=> 'LEgL',
		'immature'		=> 'im@UR',
		'impersonate'	=> 'impRsN8',
		'impounded'		=> 'im£ed',
		'impress'		=> 'impr$',
		'incredible'	=> 'NcreDbL',
		'inflate'		=> 'infl8',
		'informal'		=> 'in4ML',
		'into'			=> 'in2',
		'investigate'	=> 'invSTg8',
		'irate'			=> 'ir8',

		'jacuzzi'		=> 'jacuZ',
		'jealousy'		=> 'jLSE',
		'jellybaby'		=> 'jLEbAB',
		'Jesus'			=> 'Gsus',
		'joking'		=> 'jokN',
		'journalism'	=> 'jurnLSM',
		'journey'		=> 'jRNE',
		'juicy'			=> 'juC',
		'juvenile'		=> 'juvNiL',

		'kettle'		=> 'ketL',
		'kissogram'		=> 'Xogram',
		'kumquat'		=> 'QmQ@',

		'land'			=> 'l&',
		'landing'		=> 'l&N',
		'late'			=> 'l8',
		'later'			=> 'l8r',
		'lightweight'	=> 'lytw8',
		'look'			=> 'l%k',
		'loopy'			=> 'l%P',
		'lottery'		=> 'lotRE',
		'loverat'		=> 'luvr@',
		'lying'			=> 'lyN',

		'many'			=> 'mNE',
		'marketing'		=> 'mRketN',
		'marvellous'	=> 'mRvLS',
		'mate'			=> 'm8',
		'matter'		=> 'm@R',
		'meeting'		=> 'mEtN',
		'mellow'		=> 'mLO',
		'melody'		=> 'mLOD',
		'memory'		=> 'memRE',
		'message'		=> 'm$ge',
		'meticulous'	=> 'meTQLS',
		'might'			=> 'myt',
		'mighty'		=> 'miT',
		'millennium'	=> 'milNEM',
		'movie'			=> 'moV',

		'necessary'		=> 'nSSRE',
		'need'			=> 'nEd',
		'negative'		=> 'neg@iv',
		'neighbour'		=> 'nAbR',
		'never'			=> 'nevR',
		'night'			=> 'nyt',
		'nothing'		=> 'nufN',
		'no one'		=> 'no1',

		'occupation'	=> 'ocup8n',
		'often'			=> 'ofN',
		'open'			=> 'opN',
		'operation'		=> 'oper8n',
		'opportunity'	=> 'opRtunET',
		'outdoors'		=> 'outd%rs',
		'overdue'		=> 'ovRdU',
		'overrated'		=> 'ovR8d',

		'particular'	=> 'pRticulR',
		'partner'		=> 'pRtnR',
		'party'			=> 'pRT',
		'people'		=> 'PpL',
		'person'		=> 'pRsN',
		'personal'		=> 'pRsNL',
		'planned'		=> 'pl&',
		'possible'		=> 'po$EbL',
		'prat'			=> 'pr@',
		'press'			=> 'pr$',
		'private'		=> 'prv8',
		'pussy'			=> 'puC',
		'pussycat'		=> 'puCk@',

		'quantity'		=> 'quanTT',
		'quarrel'		=> 'qRL',
		'queasy'		=> 'qEZ',
		'question'		=> 'qSTN',
		'queue'			=> 'Q',

		'rate'			=> 'r8',
		'ready'			=> 'reD',
		'receive'		=> 'reCv',
		'remember'		=> 'remMbR',
		'reservation'	=> 'resRv8n',
		'respects'		=> 'respX',
		'restaurant'	=> 'rstRNt',
		'room'			=> 'r%m',
		'rubber'		=> 'rubR',

		'safety'		=> 'sAfT',
		'said'			=> 'Z',
		'saturday'		=> 's@RdA',
		'sex'			=> 'sX',
		'see'			=> 'C',
		'shopping'		=> 'shopN',
		'should'		=> 'sh%d',
		'single'		=> 'sngL',
		'skate'			=> 'sk8',
		'smell'			=> 'smL',
		'soon'			=> 's%n',
		'stand'			=> 'st&',
		'standard'		=> 'st&rd',
		'start'			=> 'stRt',
		'state'			=> 'st8',
		'station'		=> 'st8n',
		'steady'		=> 'steD',
		'strand'		=> 'str&',
		'stress'		=> 'str$',
		'subjects'		=> 'subjX',
		'suspects'		=> 'suspX',

		'tanned'		=> 't&',
		'technology'	=> 'teknoloG',
		'text'			=> 'tXt',
		'thank you'		=> 'thnQ',
		'thanks'		=> 'thnX',
		'that'			=> 'th@',
		'them'			=> 'thM',
		'then'			=> 'thN',
		'threesome'		=> '3sum',
		'to'			=> '2',
		'today'			=> '2dA',
		'tomorrow'		=> '2moro',
		'tonight'		=> '2nite,2nyt',
		'took'			=> 't%k',
		'toward'		=> '2wrd',
		'twat'			=> 'tw@',

		'uncertain'		=> 'uncRtN',
		'understand'	=> 'undRst&',
		'unforgettable'	=> 'un4gtebL',
		'unfortunate'	=> 'un4tUn8',
		'unless'		=> 'unl$',
		'unplanned'		=> 'unpl&',
		'update'		=> 'upd8',

		'valentine'		=> 'vLNtine',
		'value'			=> 'valU',
		'video'			=> 'VDO',
		'view'			=> 'vU',

		'waiting'		=> 'w8N',
		'want to'		=> 'wan2',
		'went'			=> 'wNt',
		'when'			=> 'wN',
		'why'			=> 'Y',
		'without'		=> 'W/O',
		'wonderful'		=> '1dRfL',
		'would'			=> 'w%d',
		'wouldn\'t'		=> 'w%dNt',

		'yes'			=> 'yS',
		'you'			=> 'U',
		'your'			=> 'yr',

		'zoo'			=> 'z%'
	);
	
	
	/**
	 * Get list.
	 *
	 * @access public
	 */
	function getList()
	{
		return $this->words;
	}

	/**
	 * Translate.
	 *
	 * @access public
	 */
	function translate( $text )
	{
		if ( $this->mode === SMS_MODE_LCASE )
			$text = strtolower( $text );
		
		return str_replace( array_keys( $this->words ), array_values( $this->words ), $text );
	}
} // END OF SMSDictionary

?>
