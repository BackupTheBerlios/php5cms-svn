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
 * @package services_google
 */
 
class GoogleQuery extends PEAR
{
	/**
     * Lang type
	 *
     * @var    string
	 * @access public
     */
	var $lang;
	
	/**
     * Number of results in one page
	 *
     * @var    int
	 * @access public
     */
	var $resultsInPage;
	
	/**
     * If specified, google results are search only in this site
	 *
     * @var    string
	 * @access public
     */
	var $site;
	
	/**
     * FileTypes parameters
	 *
     * @var    string
	 * @access public
     */
	var $fileType;
	
	/**
     * Search words in title, body, links, etc...
	 *
     * @var    string
	 * @access public
     */
	var $position;
	
	/**
     * Search specified periods (in months)
	 *
     * @var    int
	 * @access public
     */
	var $period;
	
	/**
     * Search without theses words
	 *
     * @var    array
	 * @access public
     */
	var $noWords;
	
	/**
     * Search with exactly theses sentence
	 *
     * @var    string
	 * @access public
     */
	var $sentence;
	
	/**
     * Search with at least one of these words
	 *
     * @var    array
	 * @access public
     */
	var $oneWords;
	
	/**
     * The link to query build with the parameters
	 *
     * @var    array
	 * @access public
     */
	var $link;
	
	/**
     * Search with all the words in this array
	 *
     * @var    array
	 * @access public
     */
	var $keywords;


	/**
     * Constructor
	 *
     * @public
     */
	function GoogleQuery()
	{
		$this->lang          = '';
		$this->resultsInPage = 10;
		$this->site          = '';
		$this->fileType      = '';
		$this->position      = 'any';
		$this->period        = 'all';
		$this->noWords       = array();
		$this->sentence      = '';
		$this->oneWords      = array();
		$this->link          = '';
		$this->keywords      = array();
	}


	/**
	 * Set the lang search; for exemple, asking in french pages, you must put fr in parameters.
	 *
	 * @param string lang: the lang type (ex, fr, ru, etc...)
	 * @var  void
     * @public
     */
	function setLang( $lang = 'all' )
	{
		$this->lang = ( ( $lang != 'all' )? 'lang_' . strtolower( $lang ) : '' );
	}

	/**
	 * Set the lang search; for exemple, asking in french pages, you must put fr in parameters.
	 *
	 * @param int resultsInPage: number of result per page
	 * @var  void
     * @public
     */
	function setNumInPage( $resultsInPage )
	{
		$this->resultsInPage = intval( $resultsInPage );
	}

	/**
	 * Set the "search only in this site" value.
	 *
	 * @param string site: the url of the site where where search the results
	 * @var  void
     * @public
     */
	function setSite( $site )
	{
		$this->site = $site;
	}

	/**
	 * Set the "file parameters" value.
	 *
	 * @param string fileType: fileType where search the results (valids values are pdf, ps, xls, doc, ppt, rtf)
	 * @var  void
     * @public
     */
	function setFiletype( $fileType )
	{
		$this->fileType = $fileType;
	}

	/**
	 * Set the "file parameters" value.
	 *
	 * @param string position: position where search the values (valids values are title, body, url, links)
	 * @var  void
     * @public
     */
	function setPosition( $position )
	{
		$this->position = $position;
	}

	/**
	 * Set the Period where url where updated.
	 *
	 * @param int period: duration in month, where were search the modified urls; max 12
	 * @var  void
     * @public
     */
	function setPeriod( $period )
	{
		if ( $period >= 12 ) 
			$this->period = 'y';
		else 
			$this->period = 'm' . $period;
	}

	/**
	 * Set the list of words which must be exclude from the results.
	 *
	 * @param array noWords: array of Words which must be exclude from search
	 * @var  void
     * @public
     */
	function setExclude( $noWords = array() )
 	{
		$this->noWords = $noWords;
 	}

	/**
	 * Set a sentence which must be search.
	 *
	 * @param string sentence: exact sentence which must be search
	 * @var  void
     * @public
     */
 	function setSentence( $sentence )
 	{
		$this->sentence = $sentence;
 	}

	/**
	 * Set a list of words which can be use.
	 *
	 * @param array words: array of Words which must at least existing in search results
	 * @var  void
     * @public
     */
 	function setWords( $words = array() )
  	{
		$this->oneWords = $words;
  	}

	/**
	 * Set a list of words which must be search.
	 *
	 * @param array words: array of Words which must existing in results (not in order)
	 * @var  void
     * @public
     */
  	function setKeywords( $words = array() )
  	{
		$this->keywords = $words;
  	}

	/**
	 * Make the url and return the url.
	 *
	 * @var  string
     * @public
     */
	function getLink()
	{
		$this->_setlink();
		return $this->link;
	}

	/**
	 * Get the google search content.
	 *
	 * @var  string
     * @public
     */
	function getContents()
	{
		$this->_setLink();
		
		$contents = '';
		temp      = explode( "/", $this->link );
		$domain   = $temp[2];
		$port     = 80;
		$query    = '/';
		
		for ( $i = 3; $i < ( count( $temp ) - 1 ); $i++ ) 
			$query .= $temp[$i] . '/';
			
		$query .= $temp[( count( $temp ) - 1 )];
		$fp = @fsockopen( $domain, $port, $errno, $errstr );
		
		if ( $fp )
		{
			fputs( $fp, 'GET ' . $query . ' HTTP/1.1' . "\r\n" );
		    fputs( $fp, 'Host: ' . $host . "\r\n" );
		    fputs( $fp, 'Accept: text/html' . "\r\n" );
		    fputs( $fp, 'Connection: Close' . "\r\n\r\n" );

		    while ( !feof( $fp ) )
				$contents .= fgets( $fp, 4096 );

			fclose( $fp );
		}
		
		return $contents;
	}

	/**
	 * Get the number of results found.
	 *
	 * @var  int
     * @public
     */
	function getResults()
 	{
		$results  = 0;
		$contents = $this->getContents();
		
		if ( $contents != '' )
		{
			if ( preg_match( "/<b>[0-9]+<\/b> \- <b>[0-9]+<\/b>.+<b>([0-9]+)<\/b>/", $contents, $matches ) ) 
				$results = $matches[1];
		}
		
		return $results;
	}
	
	
	// private methods
	
	/**
	 * Make the url sentence.
	 *
     * @access private
     */
	function _setLink()
	{
		$this->link = 'http://www.google.com/search?as_q=' . urlencode( implode( " ", $this->keywords ) ) .
			'&num=' . $this->resultsInPage .
			( ( $lang != 'all' )? '&hl=' . substr( $this->lang, 5 ) : '' ) .
			'&ie=UTF-8&oe=UTF-8&btnG=Search&as_epq=' . urlencode( $this->sentence ) .
			'&as_oq=' . urlencode( implode( " ", $this->oneWords ) ) .
			'&as_eq=' . urlencode( implode( " ", $this->noWords  ) ) .
			'&lr=' . $this->lang .
			'&as_ft=i&as_filetype=' . $this->fileType .
			'&as_qdr='  . $this->period .
			'&as_occt=' . $this->position .
			'&as_dt=i&as_sitesearch=' . $this->site;
	}
} // END OF GoogleQuery

?>
