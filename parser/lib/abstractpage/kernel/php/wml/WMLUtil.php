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
 * Static helper functions.
 *
 * @package wml
 */
 
class WMLUtil
{
	/**
	 * Convert html to wml.
	 *
	 * @access public
	 * @static
	 */	
	function toWML( $string ) 
	{
		preg_match( '#^(.*)<body[^>]*>(.*)</body#i', $string, $uu );
	
		if ( $uu[2] ) 
			$string = $uu[2]; 
					
		if ( preg_match( '#<title[^>]*>(.+)<#i', $uu[1], $uu ) ) 
			$title = $uu[1];
		else 
			$title = "untitled document"; 

		$from_orig = array( 'br', '/tr', 't[dfh]', '[h]', '/[h]', 'li', '/li', '[fcdgjklmnoqrvwxyz]', '/[fcdgjklmnoqrvwxyz]', '/*t\w+', '/*img', '/*table', '/*[uo]l', '/*input' );
		$to_wml    = array( "<br/>", " |<br/>", " | ", "<b>", "</b><br/>", '<b>*</b> ', '<br/>', "<em>", "</em>" );
				
		foreach ( $from_orig as $value )  
			$from_html[] = '#<' . $value . '[^>]*>#ims';

		if ( preg_match( '#<title[^>]*>(.+)<#i', $string, $uu ) ) 
			$title = $uu[1];
		else 
			$title = "untitled document"; 
				
		$string = preg_replace( '#^.{0,4096}<body[^>]*?>#is', '', $string );
		$string = preg_replace( '#</body.+$#is', '', $string );

		$from_orig = array( 'br', '/tr', 't[dfh]', '[h]', '/[h]', 'li', '/li', '[fcdgjklmnoqrvwxyz]', '/[fcdgjklmnoqrvwxyz]', '/*t\w+', '/*img', '/*table', '/*[uo]l', '/*input' );
		$to_wml    = array( "<br/>", " |<br/>", " | ", "<b>", "</b><br/>", '<b>*</b> ', '<br/>', "<em>", "</em>" );
				
		foreach ( $from_orig as $value )  
			$from_html[] = '#<' . $value . '[^>]*>#ims';
			
		$string = preg_replace( $from_html, $to_wml, $string );
		$string = preg_replace( array( '/&nbsp;/', '/&auml;/', '/&ouml;/', '/&uuml;/', '/&Auml;/', '/&Ouml;/', '/&Uuml;/', '/&szlig;/', '/&shy;/', '/&[#a-z0-9]+;/' ), array( ' ', 'ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß', ' ', '?' ), $string );
		$string = preg_replace( '/(<a[^>]+>)(.+?)(<\/a>)/imse', '"\\1" . strip_tags("\\2") . "\\3"', $string );

		$string =
			'<?xml version="1.0" encoding="ISO-8859-1"?>' .
			'<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">' .
			'<wml>'.
			'<template><do type="prev" label="back"><prev/></do>' .
			'<do type="refresh" label="refresh"><refresh/></do></template>' .
			'<card id="page"><p>' .
			"$string";
			'</p></card></wml>';
			
		return $string;
	}
} // END OF WMLUtil

?>
