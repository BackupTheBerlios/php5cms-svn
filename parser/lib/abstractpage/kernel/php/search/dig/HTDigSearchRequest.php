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
 * Model an htdig search request as a PHP object.
 *
 * This class expects the following:
 *
 * - that the following files exist:
 *      - results-header.html
 *      - results-footer.html
 *      - results-template.html
 *
 * - that the location of these files is specified below in this->templatesDir
 *
 * - that results-header.html contains at a minimum $(MATCHES) and $(MATCHES_PER_PAGE)
 *
 * - that each template file consists of an htdig variable followed by 
 *   a new line and/or carriage return
 *
 * - that none of the templates contain variabls that produce form elements (sorry!)
 *
 * - that the search is executed outside this class and the class is passed
 *   the results of the search when an new object is created
 *
 * @package search_dig
 */

class HTDigSearchRequest extends PEAR
{	
	/**
	 * @access public
	 */
	var $anchor;
	
	/**
	 * @access public
	 */
	var $cgi;
	
	/**
	 * @access public
	 */
	var $current;
	
	/**
	 * @access public
	 */
	var $description;
	
	/**
	 * @access public
	 */
	var $descriptions;
	
	/**
	 * @access public
	 */
	var $docid;
	
	/**
	 * @access public
	 */
	var $excerpt;
	
	/**
	 * @access public
	 */
	var $firstdisplayed;
	
	/**
	 * @access public
	 */
	var $format;
	
	/**
	 * @access public
	 */
	var $hopcount;
	
	/**
	 * @access public
	 */
	var $keywords;
	
	/**
	 * @access public
	 */
	var $lastdisplayed;
	
	/**
	 * @access public
	 */
	var $logical_words;
	
	/**
	 * @access public
	 */
	var $match_message;
	
	/**
	 * @access public
	 */
	var $matches;
	
	/**
	 * @access public
	 */
	var $matches_per_page;
	
	/**
	 * @access public
	 */
	var $max_stars;
	
	/**
	 * @access public
	 */
	var $metadescription;
	
	/**
	 * @access public
	 */
	var $method;
	
	/**
	 * @access public
	 */
	var $modified;
	
	/**
	 * @access public
	 */
	var $nextpage;
	
	/**
	 * @access public
	 */
	var $nstars;
	
	/**
	 * @access public
	 */
	var $page;
	
	/**
	 * @access public
	 */
	var $pageheader;
	
	/**
	 * @access public
	 */
	var $pagelist;
	
	/**
	 * @access public
	 */
	var $pages;
	
	/**
	 * @access public
	 */
	var $percent;
	
	/**
	 * @access public
	 */
	var $plural_matches;
	

	/**
	 * @access public
	 */
	var $prevpage;
	
	/**
	 * @access public
	 */
	var $score;
	
	/**
	 * @access public
	 */
	var $selected_format;
	
	/**
	 * @access public
	 */
	var $selected_method;
	
	/**
	 * @access public
	 */
	var $selected_sort;
	
	/**
	 * @access public
	 */
	var $size;
	
	/**
	 * @access public
	 */
	var $sizek;
	
	/**
	 * @access public
	 */
	var $sort;
	
	/**
	 * @access public
	 */
	var $starsleft;
	
	/**
	 * @access public
	 */
	var $starsright;
	
	/**
	 * @access public
	 */
	var $startyear;
	
	/**
	 * @access public
	 */
	var $startmonth;
	
	/**
	 * @access public
	 */
	var $startday;
	
	/**
	 * @access public
	 */
	var $endyear;
	
	/**
	 * @access public
	 */
	var $endmonth;
	
	/**
	 * @access public
	 */
	var $endday;
	
	/**
	 * @access public
	 */
	var $syntaxerror;
	
	/**
	 * @access public
	 */
	var $title;
	
	/**
	 * @access public
	 */
	var $url;
	
	/**
	 * @access public
	 */
	var $version;
	
	/**
	 * @access public
	 */
	var $words;
	
	/**
	 * @access public
	 */
	var $htError;
	
	/**
	 * @access public
	 */
	var $template;
	
	/**
	 * @access public
	 */
	var $header;

	/**
	 * @access public
	 */
	var $footer;
	
	/**
	 * @access public
	 */
	var $templatesDir;
	
	/**
	 * @access public
	 */
	var $results;
	
	/**
	 * @access public
	 */
	var $nomatch;
	
	/**
	 * @access public
	 */
	var $syntaxerror;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function HTDigSearchRequest( $result, $template_dir = "templates/" )
	{
		$this->results = $result;

		// make sure there are some results to work with
		if ( sizeof( $this->results ) < 3 )
		{
			$this->error = "Unknown error. No results were returned from the search request.";
			$continue = false;
		}
		// something was returned, even if it was no matches
		else
		{
			if ( eregi( "^nomatch.*", $this->results ) )
				$this->nomatch = "Sorry. No matches were found. Please try again but with a modified query.";
			
			if ( eregi( "^SYNTAXERROR.*", $this->results ) )
				$this->syntaxerror = "Sorry. There was a syntax error. It's probably not your fault, but there's nothing more that can be done to fix it at this time. Try contacting the webmaster of this site.";
			
			// NOTE: even if no results were returned, we want to try and
			//		 stuff the variables because some of them may be useful
			
			// tell us where to find the template, header, footer files
			// this is relative to the document that this class is included in
			if ( eregi( ".*\.com$", $GLOBALS["HTTP_HOST"] ) )
				$this->templatesDir = "/htdocs/dev/search/";
			else
				$this->templatesDir = $template_dir;

			// fill the template, header, footer vars with data
			$header = file( $this->templatesDir . "results-header.html" );
			array_walk( $header, "__setVars" );
			$this->header = $header;
			
			for ( $i = 0; $i < sizeof( $this->header ); $i++ )
			{
				$var = strtolower( $this->header[$i] );
				$this->$var = $this->results[ $i + 2 ];
			}
			
			$template = file( $this->templatesDir . "results-template.html" );
			array_walk( $template, "__setVars" );
			$this->template = $template;
			
			// process sets of template elements in batches of results
			for ( $i = 0; $i < $this->matches_per_page; $i++ )
			{
				// update pointer for template results
				$pointer = $i * sizeof( $this->template ) + sizeof( $this->header ) + 2;
				
				for ( $j = 0; $j < sizeof( $this->template ); $j++ )
				{
					$var = strtolower( $this->template[$j] );
					
					if ( !is_array( $this->$var ) )
						$this->$var = array();
				
					array_push( $this->$var, $this->results[$j + $pointer] );
				}
			}
			
			$footer = file( $this->templatesDir . "results-footer.html" );
			array_walk( $footer, "__setVars" );
			$this->footer = $footer;
			$offset = sizeof( $this->results ) - sizeof( $this->footer );
			
			for ( $i = 0; $i < sizeof( $this->footer ); $i++ )
			{
				$var = strtolower( $this->footer[$i] );
				$this->$var = $this->results[ $i + $offset ];
			}
		}
	}
	
	
	/**
	 * Method to append arguments to htdig's prev and next links.
	 *
	 * @access public
	 */
	function appendArgToNextPrevLinks( $source, $newPart )
	{
		$href = explode( " ", $source );
		eregi( "\?(.*)\">", $href[1], $matches );
		$qstring = $matches[1] . $newPart;
		$href[1] = "href=\"?$qstring\"><img";
		$href = implode( " ", $href );
		
		return $href;
	}

	/**
	 * Method to append arguments to htdig's pages links.
	 *
	 * @access public
	 */
	function appendArgToPagesLinks( $source, $newPart, $current )
	{
		$href = explode( "> <", $source );
		
		for ( $i = 0; $i < sizeof( $href ); $i++ )
		{
			if ( $current - 1 != $i )
			{
				// version 3.1.6 produces different page links than 3.2
				if ( substr( $this->version, 0, 3 ) < 3.2 )
				{
					if ( eregi( "^<?a href=\"\?(.*)\">(.*)</a", $href[$i] ) )
						eregi( "^<?a href=\"\?(.*)\">(.*)</a", $href[$i], $matches );
					else
						echo "<br>no match on $i";
				}
				else
				{
					eregi( "^<?a href=\"".$GLOBALS["PHP_SELF"]."\?(.*)\">(.*)</a", $href[$i], $matches );
				}
				
				$qstring  = $matches[1] . $newPart;
				$display  = $matches[2];
				$href[$i] = "a href=\"".$GLOBALS["PHP_SELF"]."?$qstring\">$display</a";
			}
		}
		
		$href = ( $current != 1? "<" : "" ) . implode( "> <", $href );
		return $href;
	}
	
	/**
	 * Method to display the description of the image instead of the excerpt, 
	 * but maintaining the bold believe it or not, it took me three hourse to get this right.
	 * main sumbling blocks were: str_replace is not case-sensitive,
	 * and referencing a callback function from within a class.
	 *
	 * @access public
	 */
	function swapExcerpt( $desc )
	{
		// put logical words into an array
		$words = substr( $this->logical_words, 1 , strlen( $this->logical_words ) - 2 );
		$words = explode( " or ", $words );
		
		// put words in order by length, longest first
		// forces highlighting of entire word (not omitting plural 's')
		usort( $words, "__reorderWords" );
		
		// embolden matched words
		if ( $desc != "" )
		{
			for ( $i = 0; $i < sizeof( $words ); $i++ )
				$desc = HTDigSearchRequest::highlight( $words[$i], $desc );
		}
		
		return $desc;
	}
	
	/**
	 * Method for doing case-insensitive search and replace
	 * found on php.net under str_replace().
	 *
	 * @access public
	 * @static
	 */
	function highlight( $needle, $haystack )
	{
		$parts = explode( strtolower( $needle ), strtolower( $haystack ) );
		$pos   = 0;

		foreach( $parts as $key => $part )
		{
			$parts[ $key ] = substr( $haystack, $pos, strlen( $part ) );
			$pos += strlen( $part );

			$parts[ $key ] .= '<strong>' . substr( $haystack, $pos, strlen( $needle ) ) . '</strong>';
			$pos += strlen( $needle );
		}

		return( join( '', $parts ) );
	}
} // END OF HTDigSearchRequest


function __reorderWords( $a, $b )
{
	if ( strlen( $a ) == strlen( $b ) ) 
		return 0;
	
	return ( strlen( $a ) > strlen( $b ) )? -1 : 1;
}

function __setVars( &$val, $key )
{
	trim( $val );
	eregi( "[^a-zA-Z]*([a-zA-Z_]*).*", "$val", $matches );
	$val = $matches[1];
}

?>
