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
 * Search engine processor script (multiple search engine in one Form)
 *
 * receives engine ($engine) and search criteria ($scrit)
 * from a form and creates the proper search form and
 * submits it to the site specified
 *
 * works with GET or POST method
 * any amount of hidden fields
 *
 * @package search
 */
 
class MultiSearch extends PEAR
{
	/**
	 * DEFINE SEARCH ENGINES
	 * engine name = action, method, criteria field
	 * @access public
	 */
	var $engines = array(
		"excite" => array(
			"http://www.excite.com/search.gw",
			"GET",
			"search"
		),
		"altavista" => array(
			"http://www.altavista.com/cgi-bin/query",
			"GET",
			"q"
		),
		"webster" => array(
			"http://www.m-w.com/cgi-bin/dictionary",
			"POST",
			"va"
		),
		"yahoo" => array(
			"http://search.yahoo.com/bin/search",
			"GET",
			"p"
		),
		"internic" => array(
			"http://www.networksolutions.com/cgi-bin/whois/whois",
			"POST",
			"STRING"
		),
		"google" => array(
			"http://www.google.com/search",
			"GET",
			"q"
		),
		"devsearch" => array(
			"http://www.devsearch.com/cgi-bin/query",
			"GET",
			"q"
		),
		"det" => array(
			"http://www.etrade.com/cgi-bin/gx.cgi/AppLogic+ResearchSymbol",
			"POST",
			"research_quote_symbol"
		),
		"hotbot" => array(
			"http://www.hotbot.com/",
			"GET",
			"MT"
		),
		"lycos" => array(
			"http://www.lycos.com/cgi-bin/pursuit",
			"GET",
			"query"
		),
		"askjeeves" => array(
			"http://www.askjeeves.com/main/askJeeves.asp",
			"GET",
			"ask"
		),
		"slashdot" => array(
			"http://www.slashdot.org/search.pl",
			"GET",
			"query"
		),
		"freshmeat" => array(
			"http://core.freshmeat.net/search.php3",
			"POST",
			"query"
		),
		"php" => array(
			"http://www.php.net/manual-lookup.php3",
			"POST",
			"function"
		)
	);

	/**
	 * DEFINE HIDDEN FIELDS
	 * engine name = "name"=>"value"
	 * @access public
	 */
	var $hiddenfields = array(
		"altavista" => array(
			"pg"			=> "q",
			"what"			=> "web",
			"kl"			=> "en"
		),
		"webster" => array(
			"book"			=> "dictionary"
		),
		"devsearch" => array(
			"mss"			=> "en/simple",
			"pg"			=> "q",
			"what"			=> "web",
			"enc"			=> "iso88591",
			"fmt"			=> ".",
			"op"			=> "a"
		),
		"det" => array(
			"INFOTYPE"		=> "DET_QUOTES"
		),
		"hotbot" => array(
			"OPs"			=> "MDRTP"
		),
		"lycos" => array(
			"cat"			=> "dir"
		),
		"askjeeves" => array(
			"origin"		=> "",
			"qSource"		=> "0",
			"site_name"		=> "Jeeves",
			"metasearch"	=> "yes"
		),
		"infind" => array(
			"time"			=> "10"
		)
	);

	
	/**
	 * @access public
	 */	
	function getHiddenFieldValues( $engine )
	{
		return $this->hiddenfields[$engine];
	}
	
	/**
	 * @access public
	 */	
	function getEngine( $engine )
	{
		return $this->engines[$engine];
	}
} // END OF MultiSearch

?>
