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
 * PseudoRewrite Class
 * A simple way to hide dynamic sites to web spiders so they can index it.
 *
 * Almost all web-spiders (like Google) can't index dynamic sites, 
 * because the parameters present on almost all links in the site.
 * This class, along a simple modification onto your Apache configuration file, 
 * allows yo to hide all the parameters and dynamic-like links and URLs of 
 * your site, so all web spiders will catch it. Best of all, this will not 
 * take you too work to get this working into your dynamic site.
 *
 * Usage:
 *
 * $prw = new PseudoRewrite();
 * $prw>doRequest();
 *
 * or
 *
 * $params = array(
 *     "session_id" => "18563967891267945672186905637893",
 *     "language"   => "de",
 *     "article_id" => 457
 * );
 * 
 * $pr  = new PseudoRewrite();
 * $str = $pr->buildQuery( "broker.php", $params );
 * 
 * $pr->doRequest( $str );
 *
 * @package peer_http
 */

class PseudoRewrite extends PEAR
{	
	var $redirector_query_fragment;
	var $dot_replacer;
	var $section_parameters_separator;
	var $parameters_separator;
	var $parameters_equal;
	
	
	/**
	 * Constructor
	 */	
	function PseudoRewrite( $redirector_query_fragment = "./", $dot_replacer = ".", $section_parameters_separator = "__params__", $parameters_separator = ",", $parameters_equal = "__is__" )
	{
		$this->redirector_query_fragment    = $redirector_query_fragment;
		$this->dot_replacer                 = $dot_replacer;
		$this->section_parameters_separator = $section_parameters_separator;
		$this->parameters_separator         = $parameters_separator;
		$this->parameters_equal             = $parameters_equal;
	}
	
	
	function doRequest( $query = "" )
	{
	    if ( $query == "" )
	       $query = $_SERVER["REQUEST_URI"];
	       
		// strip redirector_query_fragment
		$query = substr( $query, strlen( $this->redirector_query_fragment ) );
			
		// replace dots
		$query = str_replace( $this->dot_replacer, ".", $query );
			
		// divide file from parameters
		list( $file, $parameters ) = explode( $this->section_parameters_separator, $query );
			
		// if there are parameters
		if ( $parameters != "" )
		{
			// parse them into an array
			// get tokens
			$atokens = explode( $this->parameters_separator, $parameters );
			
			while ( list(, $token) = each( $atokens ) )
				$aparameters[] = explode( $this->parameters_equal, $token );
		
			if ( !is_array( $aparameters ) )
				$aparameters = null;
		}
		else
		{
			$aparameters = null;
		}
		
		echo( $this->buildRealQuery( $file, $aparameters ) );
		$this->redirect( $this->buildRealQuery( $file, $aparameters ) );
	}
		
	function buildRealQuery( $file, $aparameters )
	{
		$retr = $file;
		
		if ( is_array( $aparameters ) )
		{
			$retr .= "?";
			
			while( list(, $parameter) = each( $aparameters ) )
				$retr .= $parameter[0] . "=" . $parameter[1] . "&";
			
			$retr = substr( $retr, 0, strlen( $retr ) - 1 );
		}
		
		return $retr;
	}
		
	function redirect( $url )
	{
		header( "location: $url" );
	}
		
	function buildQuery( $file, $aparameters )
	{
		list( $domain, $file ) = $this->divideQuery( $file );
		$file = str_replace( ".", $this->dot_replacer, $file );
		
		if ( is_array( $aparameters ) )
		{
			$parameters = $this->section_parameters_separator;
			
			while ( list( $name, $value ) = each( $aparameters ) )
				$parameters .= $name . $this->parameters_equal . $value . $this->parameters_separator;
		
			$parameters = substr( $parameters, 0, strlen( $parameters ) - strlen( $this->parameters_separator ) );
		}
		
		return $domain . $this->redirector_query_fragment . $file . $parameters;
	}
		
	function buildQueryFromHTTP( $query )
	{
		list( $domain, $request ) = $this->divideQuery( $query );
		$retr = str_replace( ".", $this->dot_replacer, $request );
		$retr = str_replace( "?", $this->section_parameters_separator, $retr );
		$retr = str_replace( "&", $this->parameters_separator, $retr );
		$retr = str_replace( "=", $this->parameters_equal, $retr );
	
		return $domain . $this->redirector_query_fragment . $retr;
	}
		
	function divideQuery( $query )
	{
		if ( stristr( $query, "http://" ) )
		{
			$query      = substr( $query, 7   );
			$firstslash = strpos( $query, "/" );
			$domain     = substr( $query, 0, $firstslash  );
			$file       = substr( $query, $firstslash + 1 );
			
			return array( "http://" . $domain, $file );
		}
		else
		{
			return array( "", $query );
		}
	}
} // END OF PseudoRewrite

?>
