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
 * Looks for any URLs in a string of text and matches each one with a customizable <a> tag.
 * Convenient for parsing user input where users are not expected to write HTML.
 * The class finds URLs starting with 'http|https|ftp|www', ignores HTML tags, supports
 * the addition of <a> properties (such as defining a CSS class), and supports a character
 * limit for the displayed link text.  I have tested it with many possibilities but please
 * don't hesitate to report any bugs to my above email address. 
 *
 * @package html
 */

class URLMatch extends PEAR
{
	/**
	 * num of chars to limit for the screen-displayed URL
	 * @access public
	 */
	var $charLimit = 50;

	/**
	 * text to show at the end of a break, if the char limit is reached
	 * @access public
	 */
	var $breakTxt = "...";
	
	/**
	 * in the event your screen URL is longer than the character limit
	 * TRUE: shows the domain side of the URL [left side]  
	 * FALSE: shows the directory side [right side] of the URL
	 *
	 * @access public
	 */
	var $startFromLeft = true;
	
	/**
	 * @access private
	 */
	var $tags = array();
	
	/**
	 * @access private
	 */
	var $match = "";
	
	/**
	 * @access private
	 */
	var $matchHTML = "<{1}[[:alpha:]]+.*>{1}";
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function URLMatch()
	{
		$this->match =
			"(www|ftp://|http://|https://)"	.	// match opening of a URL 
			"([[:alnum:]*\.?]{0,4})" .			// match 4 possible subdomains
			"([[:alnum:]]+\.)" .				// match the domain
			"([[:alpha:]]{2,3}\.?)" .			// match possible top level sub domain
			"([[:alpha:]]{0,3})" .				// match top level domain
			"([^[:space:]\(\)\"]*)";			// match possible directories or query string
	}

	
	/**
	 * Call this to add any tag properties for the <a> tag.
	 *
	 * @param  string $strProperty (i.e. "target=_blank")
	 * @access public
	 */
	function addProperty( $strProperty )
	{
		$this->tags[] = $strProperty;
	}
	
	/**
	 * Call this to return the final string with all urls matched.
	 *
	 * @param  string $string (entire string in which to match URLs)
	 * @param  string $screenTxt (text, if any, which should be shown for the matched URL)
	 * @access public
	 */
	function match( $string, $screenTxt = "" )
	{
		// if not an HTML tag:
		if ( !eregi( $this->matchHTML, $string ) )
		{
			// if we have matched a URL:
			if ( eregi( $this->match, $string, $theURL ) )
			{
				$pre = "<a ";
				
				foreach ( $this->tags as $tag )
					$pre .= $tag." ";
				
				$pre .= "href=\"";
				
				if ( eregi( "^www", $theURL[0] ) )
					$pre .= "http://";
				
				$string = str_replace( $theURL[0], $pre . $theURL[0] . "\">" . $this->screenURL( $theURL[0], $screenTxt ) . "</a>", $string );
			} 
		}
		
		return $string;
	}
	
	/**
	 * Logic for the displayed link text.
	 *
	 * @param  string $theScreenURL (the matched URL passed from match() )
	 * @param  string $screenTxt ($screenTxt if passed from match() )
	 * @access public
	 */
	function screenURL( $theScreenURL, $screenTxt = "" )
	{
		$output = ( !empty( $screenTxt ) )? $screenTxt: $theScreenURL;
		
		if ( strlen( $output ) > $this->charLimit )
		{
			if( $this->startFromLeft )
				$output = substr( $output, 0, $this->charLimit ) . $this->breakTxt;
			else
				$output = $this->breakTxt . substr( $output, -$this->charLimit );
		}
		
		return $output;
	}
} // END OF URLMatch

?>
