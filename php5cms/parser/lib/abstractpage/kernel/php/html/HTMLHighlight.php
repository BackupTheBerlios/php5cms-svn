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
|Authors: Bojidar Naydenov <bojo2000@mail.bg>                          |
|         Antony Raijekov <dev@strategma.bg>                           |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/** 
 * This class highlight text matches a search string (keyword) in html 
 * based documents, without destroying html-tags.
 *
 * @package html
 */

class HTMLHighlight extends PEAR
{
	/**
	 * @access public
	 */
    var $keyword;
	
	/**
	 * @access public
	 */
	var $replacement;
	
	/**
	 * @access public
	 */
	var $bad_tags = array(
		"A",
		"IMG",
		"OPTION"
	);
	
	/**
	 * @access public
	 */
	var $respect_attributes = false;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function HTMLHighlight( $keyword, $replacement = "<strong>{keyword}</strong>" )
	{
		$this->keyword     = $keyword;
		$this->replacement = $replacement;
	}
	

	/**
	 * @access public
	 */
	function setBadTags( $tags = null )
	{
		if ( is_array( $tags ) )
			$this->bad_tags = $tags;
	}

	/**
	 * @access public
	 */	
	function respectAttributes( $b = true )
	{
		if ( is_bool( $b ) )
			$this->respect_attributes = $b;
	}
	
	/**
	 * @access public
	 */
	function highlight( $text, $keyword = false, $replacement = false )
	{
		// if there are specific keyword/replacement given
		if ( $keyword != false ) 
			$this->keyword = $keyword;
		
		if ( $replacement != false ) 
			$this->replacement = $replacement;

		if ( isset( $this->keyword ) )
		{
			if ( $this->respect_attributes )
			{
				// TODO
				
				return preg_replace_callback( "#(<([A-Za-z]+)[^>]*[\>]*)*(" . $this->keyword . ")\b(.*?)(<\/\\2>)*#si", array( &$this, '_highlightCallback' ), $text );
				// return preg_replace_callback( "/((?<=>)([^<]+)?(" . $this->keyword . "))/", array( &$this, '_highlightCallback' ), $text );
			}
			else
			{
				return preg_replace_callback( "#(<([A-Za-z]+)[^>]*[\>]*)*(" . $this->keyword . ")\b(.*?)(<\/\\2>)*#si", array( &$this, '_highlightCallback' ), $text );
			}
		}
		else
		{
			return $text;
		}
	}

	
	// private methods

	/**
	 * @access private
	 */	
	function _highlightCallback( $matches )
	{		
		//check for bad tags and keyword					
		if ( !in_array( strtoupper( $matches[2] ), $this->bad_tags ) )  
			$proceed =  preg_replace( "#\b(" . $this->keyword . ")\b#si", str_replace( "{keyword}", $matches[3], $this->replacement ), $matches[0] );
		else
			$proceed = $matches[0]; // return as-is
		
		return stripslashes( $proceed );
	}
} // END OF HTMLHighlight

?>
