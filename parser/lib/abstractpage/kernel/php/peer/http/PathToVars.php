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
 * Allows you to use a pseudo directory path in your HTML links instead of a query string
 * to declare variables so that your pages will be properly crawled by search engines.  
 * This is an alternative to using mod_rewrite rules to handle your URLs 
 * (http://httpd.apache.org/docs/mod/mod_rewrite.html). 
 * 
 * I based this on several articles and news posts I read on how search engines crawl dynamic, 
 * php-generated URLs. links to the articles are here:
 * 
 * http://www.phpwizard.net/resources/tutorials/dynamic_and_searchengines.html
 * http://www.phpbuilder.com/forum/read.php3?num=2&id=124088&loc=0&thread=124088
 * http://www.sitepointforums.com/showthread.php?s=&threadid=15798
 * 
 * It was reported that googlebot (google.com's search engines) will 
 * not crawl URLs that end in a directory but no trailing slash.  
 * for example,
 * http://somesite.com/index.php/fake_dir/fake_for_variable 
 * will not be crawled, but 
 * http://somesite.com/index.php/fake_dir/fake_for_variable/
 * will, according to one article.
 * 
 * I couldn't find any definitive info on how URLs which carry session IDs 
 * (or that end in ?something=something) are crawled. If you know, please let me know.  
 * I've noticed recently from stats that googlebot _has_ crawled URIs of mine which include 
 * query strings so it's possible googlebot, at least, recognizes query strings 
 * (but not sure about other crawlers and the crawl I saw from stats wasn't on a URI 
 * that passes a session ID). The pseudo-directory method still ups your odds of 
 * getting crawled, I would imagine.
 * 
 * It seems that pages which end in '.php' are usually crawled by most search engines 
 * so I'm not sure if using the '.html' suffix is necessary.
 * 
 * More on robots:
 * 
 * http://www.google.com/bot.html
 * http://www.robotstxt.org/wc/robots.html
 * 
 * Security:
 * see using register_globals chapter in the manual for security reasons brefore running this class
 * http://www.php.net/manual/en/security.registerglobals.php
 * this script is most secure with register_globals = off
 * 
 * To convert a website's URIs to use this path to variable method, first see the methods 
 * _create_PTV_SELF() and _create_PTV_SELF_Q(), these may be helpful. You will certainly need 
 * to change img src paths from being relative to being absolute. 
 * For example src="images/some.gif" needs to be src="/images/some.gif"
 * 
 * Known issues:
 * You can't declare global array elements to be set with the _var_lookup() method 
 * (a workaround for that is in the example)
 *
 * @package peer_http
 */

class PathToVars extends PEAR
{
	/**
	 * @access public
	 */
	var $useVarLookupMethod = false;
	
	/**
	 * @access public
	 */
	var $useVar_ValueMethod = false;
	
	/**
	 * @access public
	 */
	var $useBothMethods = true;
	
	/**
	 * @access public
	 */
	var $lookup = array( array() );
	
	/**
	 * @access public
	 */
	var $regexLookup = array( array() );
	
	/**
	 * @access public
	 */
	var $extractedPathParts = array();
	
	/**
	 * @access public
	 */
	var $varPath = '';
	
	/**
	 * @access public
	 */
	var $varsCreated = array();

	
	/**
	 * @access public
	 */
	function set( $var, $value )
	{
		$this->$var = $value;
	}
	
	/**
	 * @access public
	 */							
	function createLookup( $lookup, $varsToSet )
	{
		if ( !is_array( $varsToSet ) )
		{
			return false;
		} 
		else 
		{
			foreach ( $varsToSet as $varToSet => $value )
			{
				// __LOOKUP__ is a special value that resolves to the input (the pseudo-dirname):
				if ( empty( $value ) ) 
					$value = "__LOOKUP__"; 
				
				$this->lookup[$lookup][$varToSet] = $value;
			}
			
			return true;
		}
	}
	
	/**
	 * @access public
	 */
	function createRegexLookup( $regexLookup, $varsToSet )
	{
		if ( !is_array( $varsToSet ) )
		{
			return false;
		} 
		else 
		{
			foreach ( $varsToSet as $varToSet => $value )
			{
				if ( empty( $value ) ) 
					$value = "__LOOKUP__"; 
				
				$this->regexLookup[$regexLookup][$varToSet] = $value;
			}
			
			return true;
		}
	}

	/**
	 * @access public
	 */
	function setVars()
	{
		global $HTTP_SERVER_VARS;
		$this->varPath = ( isset( $HTTP_SERVER_VARS['PATH_INFO'] ) )? $HTTP_SERVER_VARS['PATH_INFO']: "";

		if ( !empty( $this->varPath ) )
		{
			// build array with one pseudo-directory per index
			$this->extractedPathParts = explode( "/", $this->varPath );
			
			// make the variable declarations
			if ( $this->useBothMethods )
			{
				$this->_var_value();
				$this->_var_lookup();
			}
			else if ( $this->useVar_ValueMethod )
			{
				$this->_var_value();
			}
			else if ( $this->useVarLookupMethod )
			{
				$this->_var_lookup();
			} 
			else 
			{
				return false;
			}
		}
		
		$this->_create_PTV_SELF();   // see the method
		$this->_create_PTV_SELF_Q(); // see the method
		
		return true;
	}
	
	
	// private methods

	/**
	 * @access private
	 */
	function _var_value()
	{
		foreach ( $this->extractedPathParts as $key => $part )
		{
			if ( $this->useBothMethods )
			{
				// check that this _var_value hasn't been declared in createLookup()
				if ( !array_key_exists( $part, $this->lookup ) )
				{
					if ( ereg( "_", $part ) )
					{
						list( $var, $value ) = explode( "_", $part );
						$GLOBALS[$var] = $value;
						unset( $this->extractedPathParts[$key] );
					}
				}
			} 
			else 
			{
				list( $var, $value ) = explode( "_", $part );
				$GLOBALS[$var] = $value;
			}
		}
	}

	/**
	 * @access private
	 */
	function _var_lookup()
	{
		foreach ( $this->extractedPathParts as $lookup )
		{
			if ( !empty( $lookup ) )
			{
				// for each pseudo-dirname, set the corresponding var=>val pairs
				if ( !empty( $this->lookup ) )
				{
					if ( array_key_exists( $lookup, $this->lookup ) )
					{
						foreach ( $this->lookup[$lookup] as $key => $val )
							$GLOBALS[$key] = ( $val == "__LOOKUP__" )? $lookup: $val;
					}
				}
				
				// for each pseudo-dirname that matches the regex, 
				// set the corresponding var=>val pairs
				if ( !empty( $this->regexLookup ) )
				{
					foreach ( $this->regexLookup as $regex => $lookupsToSet )
					{
						if ( eregi( $regex, $lookup ) )
						{
							foreach ( $lookupsToSet as $key => $val )
								$GLOBALS[$key] = ( $val == "__LOOKUP__" )? $lookup: $val;
						}
					}
				}
			}
		}
	}

	/**
	 * Creates a global variable PTV_SELF, for you to use in your scripts
	 * this is just $PHP_SELF without the path_info you used for your variables.
	 *
	 * @access private
	 */
	function _create_PTV_SELF()
	{
		$GLOBALS['PTV_SELF'] = str_replace( $this->varPath, "", $GLOBALS['PHP_SELF'] );
		return true;
	}

	/**
	 * Creates a global variable PTV_SELF, for you to use in your scripts
	 * this is just $PHP_SELF without the path_info but preserving the query string that was passed.
	 *
	 * @access private
	 */
	function _create_PTV_SELF_Q()
	{
		$GLOBALS['PTV_SELF_Q'] = str_replace( $this->varPath, "", $GLOBALS['PHP_SELF'] );
		
		if ( getenv( 'QUERY_STRING' ) ) 
			$GLOBALS['PTV_SELF_Q'] .= "?" . getenv( 'QUERY_STRING' );
			
		return true;
	}
} // END OF PathToVars

?>
