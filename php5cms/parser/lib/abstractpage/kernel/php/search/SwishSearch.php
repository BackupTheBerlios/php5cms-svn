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
 * SwishSearch Class
 *
 * SWISH-E is a fast, powerful, flexible, free, and easy to
 * use system for indexing collections of Web pages or other files.
 *	
 * @link http://swish-e.org
 * @package search
 */

class SwishSearch extends PEAR
{
	/**
	 * @access public
	 */	
	var $swish;
	
	/**
	 * @access public
	 */	
	var $search_index;
	
	/**
	 * @access public
	 */	
	var $search_query;
	
	/**
	 * @access public
	 */	
	var $errorMessage;
	
	/**
	 * this will be used for highlighting text
	 * @access private
	 */
	var $highlight_element = array();
	
	/**
	 * this will hold command to execute swish-e   
	 * @access private
	 */
	var $cmd;
	
	/**
	 * this will tell from which record swish-e should return results
	 * @access private
	 */
	var $startat;
	
	/**
	 * this tells how many results should be returned
	 * @access private
	 */
	var $no_of_results;

	/**
	 * this will have total no of results returned by swish-e
	 * @access private
	 */
	var $num_results;
	
	/**
	 * this array will have relavance of each result
	 * @access private
	 */
	var $relevance = array();
	
	/**
	 * @access private
	 */
	var $result_url = array();
	
	/**
	 * @access private
	 */
	var $result_title = array();
	
	/**
	 * @access private
	 */
	var $file_size = array();
	
	/**
	 * @access private
	 */
	var $link = array();
	
	/**
	 * @access private
	 */
	var $description = array();
	
	/**
	 * @access private
	 */
	var $search_element = array();
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function SwishSearch( $start = 0 )
	{
		$this->swish             = "";
		$this->search_index      = "";
		$this->search_query      = "";
		$this->cmd               = "";
		$this->num_results       = 0;
		//$this->relevance       = 0;
		$this->result_url        = array();
		$this->result_title      = array();
		$this->file_size         = array();
		$this->link              = array();
		$this->description       = array();
		$this->errorMessage      = "";
		$this->search_element    = array();
		$this->highlight_element = array();
		$this->startat           = $start;
		$this->no_of_results     = 10;
	}


	/**
	 * @access public
	 */	
	function setSearchQuery( $query )
	{
	    $this->search_query = $query;
	}
	
	/**
	 * Tells where swish-e executable is stored.
	 *
	 * @access public
	 */
	function setSwish( $swish )
	{
	    $this->swish = $swish;
	}
	
	/**
	 * Sets the path of the index file.
	 *
	 * @access public
	 */
	function setIndex( $index )
	{
	    $this->search_index = $index;
	}
	
	/**
	 * Pprocesses input query string.
	 * This will filter out any shell command, backslashes, quotes.
	 *
	 * @access public
	 */
	function preProcess()
	{
		// escape potentially malicious shell commands
		$this->search_query = EscapeShellCmd( $this->search_query );
		
		// remove backslashes from search query
		$this->search_query = stripslashes( $this->search_query );
		
		// remove quotes from search query
		$this->search_query = ereg_replace( '("|\')', '', $this->search_query );
	}
	
	/**
	 * This will splitup each word out.
	 *
	 * @access public
	 */
	function getWordsOut()
	{
		// replace wildcard caracter by space
	    $formated_query = str_replace( "*", " ", $this->search_query );
		
		// separate words in search query
		$this->search_element = explode( " ", trim( $formated_query ) );
		
		for ( $i = 0; $i < count( $this->search_element ); $i++ )
		{
			$this->highlight_element[$i] = $this->search_element[$i] . "[^ ]* ";
			$this->search_element[$i]    = ereg_replace( "\*.*", "", $this->search_element[$i] );
		}
	}
	
	/**
	 * This will buildup command to be executed for search.
	 *
	 * @access public
	 */
	function getCommand()
	{
	    $this->cmd =
			$this->swish        . " -w " . 
			$this->search_query . " -f " . 
			$this->search_index . " -b " . 
			$this->startat      . " -m " . 
			$this->no_of_results;
	}

	/**
	 * This will return no of results.
	 *
	 * @access public
	 */
	function getNoofResults( $pp )
	{
	    $line_cnt = 1;

		// loop through each line of the pipe result (i.e. swish-e output) to find hit number
		while ( $nline = @fgets( $pp, 1024 ) )
		{
			// grab the 22nd line, which contains the number of hits returned
			if ( $line_cnt == 22 )
			{
				$num_line = $nline;
				break;
			}
			
			$line_cnt++;
		}
		
		// strip out all but the number of hits
		$this->num_results = ereg_replace( '# Number of hits:.', '', $num_line );
	}

	/**
	 * Sets array of description of each hit.
	 *
	 * @access public
	 */
	function getDescription( $page_requested )
	{
	    $fd = fopen( $page_requested, "r" );
		$contents = fread( $fd, filesize( $page_requested ) );
		fclose( $fd );
		
		$contents = strip_tags( $contents );
		$temp     = strtolower( $contents );
		$needle   = strtolower( $this->search_element[0] );
		$ind      = strpos( $temp, $needle );
		
		if ( $ind < 25 )
			$description = substr( $contents, 0, 200 );    
		else
			$description = substr( $contents, $ind - 25, 200 );    
		
		for ( $i = 0; $i < count( $this->highlight_element ); $i++ )
			$this->description[] = eregi_replace( "(" . $this->highlight_element[$i] . ")", " <b>\\1</b> ", $description );
	}

	/**
	 * Sets array of attributes like file size, relevance, title and link. 
	 *
	 * @access public
	 */
	function getAttributes( $line )
	{
	    list( $rel, $res_url, $res_title, $fl_size ) = explode( "\t", $line );
		$this->relevance[] = $rel / 10;
		$res_title = ereg_replace( '%%', ' ', $res_title );
		$this->result_title[] = ereg_replace( '"', '', $res_title );
		$url  = parse_url( $res_url );
		$link = $url["path"];
		$page_requested = $link;
		$this->getDescription( $page_requested );
				
		if ( $url["query"] )
			$this->link[] = $link . "?" . $url["query"];
		else
		    $this->link[] = $link;
	}

	/**
	 * This will create new process where command in $cmd will be executed.
	 *
	 * @access public
	 */
	function executeCommand()
	{
	    $pp = popen( $this->cmd, "r" );
		
		if ( !$pp )
			return false;

		$this->getNoofResults( $pp );

		// loop through each line of the pipe result (i.e. swish-e output)
		while ( $line = @fgets( $pp, 4096 ) )
		{
			// Skip commented-out lines and the last line.
			if ( preg_match( "/^(\d+)\s+(\S+)\s+\"(.+)\"\s+(\d+)/", $line ) )
			{
				$line    = explode( '"', $line );
				$line[1] = ereg_replace( "[[:blank:]]", "%%", $line[1] );
				$line    = implode( '"', $line );
				$line    = ereg_replace( "[[:blank:]]", "\t", $line );
				
				$this->getAttributes($line);
			}
		}
		
		// close shell pipe
		pclose( $pp );
		return true;
	}
	
	/**
	 * This will sequence all the functions. 
	 *
	 * @access public
	 */
	function execute()
	{
	    $this->preProcess();
		$this->getWordsOut();
		$this->getCommand();
		$this->executeCommand();
	}
} // END OF SwishSearch

?>
