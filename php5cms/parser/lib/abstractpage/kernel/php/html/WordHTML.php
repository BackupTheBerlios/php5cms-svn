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
|Authors: Logan Dugenoux <logan.dugenoux@netcourrier.com>              |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * @package html
 */
 
class WordHTML extends PEAR
{
	/**
	 * Clean up Word html.
	 *
	 * @param  string  &$word_html
	 * @param  boolean $remove_styles				remove all styles, so removes CSS also
	 * @param  boolean $remove_ifs 					remove all M$ if
	 * @param  boolean $remove_dblspaces			remove dbl spaces (necessary for the 3 next options)
	 * @param  boolean $remove_unused_css			remove unused CSS (>200 Ko !!)
	 * @param  boolean $search_styles_within_tags	search styles within tags (SLOOOOW)
	 * @param  boolean $shorten_class_names			shorten class names binding them
	 * @param  boolean $remove_border_none			remove "border=none"
	 * @param  boolean $remove_useless_tags			remove various useless tags
	 * @param  boolean $remove_span_tags			remove <span> tags (not within content)
	 * @param  boolean $remove_all_tags				remove all tags (better use TXT output for conversion)
	 * @access public
	 */
	function clean( 
		$word_html, 
		$remove_styles             = true, 
		$remove_ifs                = true, 
		$remove_dblspaces          = true, 
		$remove_unused_css         = true, 
		$search_styles_within_tags = false, 
		$shorten_class_names       = true, 
		$remove_border_none        = true, 
		$remove_useless_tags       = true,
		$remove_span_tags          = true, 
		$remove_all_tags           = false )
	{
		if ( $remove_dblspaces )
		{
			$word_html = str_replace( "\n", " ", $word_html );
			$word_html = str_replace( "\r", " ", $word_html );
			$word_html = str_replace( "\t", " ", $word_html );

			$word_html = $this->_removeWhitespace( $word_html );
		}
			
		// Remove IFs
		if ( $remove_ifs )
			$this->_extractIf( $word_html, 0 );
			
		if ( $remove_all_tags )
		{
			$word_html = ereg_replace( "<style>[^<]*</style>", "", $word_html );
			$word_html = ereg_replace( "<[^>]*>", "", $word_html );	
		}	
			
		if ( $remove_unused_css )
		{
			// Find used tags
			$classes = array();
			
			// -1 within the styles class=...
			$tags = array();
			
			preg_match_all( "(class=[^>]*)", $word_html, $tags, PREG_SET_ORDER );
				
			for ( $i = 0; $i <= sizeof( $tags ); $i++ )
			{
				$good = explode( " ", $tags[$i][0] );
			
				if ( strlen( substr( $good[0], 6 ) ) > 0 )
					$classes[substr( $good[0], 6 )] = 1;
			}
				
			if ( $search_styles_within_tags )
			{
				// SLOOOOOOOOOOOOOW
				// -2 Directly tag names	<tagName>
				$tags = array();
				preg_match_all( "(<[^>]*)", $word_html, $tags, PREG_SET_ORDER );
				
				for ( $i = 0; $i <= sizeof( $tags ); $i++ )
				{
					$good = explode( " ", $tags[$i][0] );
					
					if ( substr( $good[0], 1, 1 ) == "/" )
						continue;
						
					if ( substr( $good[0], 1, 1 ) == "!" )
						continue;
						
					if ( strlen( substr( $good[0], 1 ) ) > 0 )
						$classes[substr( $good[0], 1 )] = 1;
				}
			}
			else
			{
				$classes["h1"] = 2;
				$classes["h2"] = 2;
				$classes["h3"] = 2;
				$classes["h4"] = 2;
				$classes["h5"] = 2;
				$classes["h6"] = 2;
			}
				
			// end of research
			$tagsOk = "";
			
			foreach( $classes as $k => $type )
			{
				if ( $tagsOk )
					$tagsOk .= "|";
	
				$tagsOk .= $k;
			}
			
			$regExpression = "((" . $tagsOk . ") *\\{[^\\}]*\\})";

			// Find used styles
			$stylesDef = array();
			preg_match_all( $regExpression, $word_html, $stylesDef, PREG_SET_ORDER );
			$stylesDefString = "";

			for ( $i = 0; $i <= sizeof( $stylesDef ); $i++ )
				$stylesDefString .= "\n" . $stylesDef[$i][0] . "\n";
				
			if ( $shorten_class_names )
			{
				$i = 0;
				foreach( $classes as $k => $type )
				{
					if ( $type == 1 ) // style
					{
						$word_html = str_replace( $k, "c" . $i, $word_html );
						$stylesDefString = str_replace( $k, "c" . $i, $stylesDefString );

						$i++;
					}
				}
			}
				
			// Remove all <style>	... </style> tags
			$pLastStylePos = 0;
			$pStyleBegin   = $this->_strpos( $word_html, "<style>", $pLastStylePos );
			$pFirstStyleBegin = $pStyleBegin;

			if ( $pStyleBegin != -1 )
				$pStyleEnd = $this->_strpos( $word_html, "</style>", $pStyleBegin );

			while ( $pStyleBegin != -1 )
			{
				$pLastStylePos = $pStyleEnd;
				$word_html = substr( $word_html, 0, $pStyleBegin ) . substr( $word_html, $pStyleEnd + 8 );
				$pStyleBegin = $this->_strpos( $word_html, "<style>", $pLastStylePos );

				if ( $pStyleBegin != -1 )
					$pStyleEnd = $this->_strpos( $word_html, "</style>", $pStyleBegin );
			}
				
			// Write only necesary style
			if ( $stylesDefString )
				$word_html = substr( $word_html, 0, $pFirstStyleBegin ) . "<style>\n<!--" . $stylesDefString . "-->\n</style>" . substr( $word_html, $pFirstStyleBegin );
		}
			
		if ( $remove_styles )
			$word_html = ereg_replace( "style='[^']*'", "", $word_html );
				
		if ( $remove_border_none )
		{
			$word_html = str_replace( "text-decoration:none", "", $word_html );
			$word_html = str_replace( "text-underline:none",  "", $word_html );
			$word_html = str_replace( "border-left:none",     "", $word_html );
			$word_html = str_replace( "border-top:none",      "", $word_html );
			$word_html = str_replace( "border-bottom:none",   "", $word_html );
			$word_html = str_replace( "border-right:none",    "", $word_html );
		}
			
		if ( $remove_useless_tags )
		{
			$word_html = ereg_replace( "v:shapes=\"[^\"]*\"",     "", $word_html );
			$word_html = ereg_replace( "style='tab-stops:[^']*'", "", $word_html );
			$word_html = ereg_replace( "<o[^>]*></o:p>",          "", $word_html );
			$word_html = ereg_replace( "<p[^>]*></p>",            "", $word_html );
		 	$word_html = ereg_replace( "mso-(^[';])*",            "", $word_html );
		 	$word_html = ereg_replace( "field-code-(^[';])*",     "", $word_html );
		}
			
		if ( $remove_span_tags )
		{
			$word_html = ereg_replace( "<span[^>]*>", "", $word_html );	
			$word_html = str_replace(  "</span>",     "", $word_html );
		}
		
		if ( $remove_dblspaces )
			$word_html = $this->_removeWhitespace( $word_html );
			
		return $word_html;
	}
	
		
	// private methods

	/**
	 * @access public
	 */
	function _removeWhitespace( $word_html )
	{
		// much much faster than $word_html = ereg_replace( " +", " ", $word_html );
		// and works if there is less than 256 spaces at the same time
		$word_html = str_replace( "                                ", " ", $word_html );
		$word_html = str_replace( "                ", " ", $word_html );
		$word_html = str_replace( "        ", " ", $word_html );
		$word_html = str_replace( "    ", " ", $word_html );
		$word_html = str_replace( "  ", " ", $word_html );
		
		return $word_html;
	}

	/**
	 * @access public
	 */
	function _extractIf( &$str, $pos )
	{
		$pIf1 = $this->_strpos( $str, "<![if",   $pos );
		$pIf2 = $this->_strpos( $str, "<!--[if", $pos );
		$pIf  = $this->_zmin( $pIf1 ,$pIf2 );

		if ( $pIf >= 0 )
		{
			$pIfEnd   = $this->_strpos( $str, ">", $pIf );
			$pNextIf1 = $this->_strpos( $str, "<![if",   $pIfEnd );
			$pNextIf2 = $this->_strpos( $str, "<!--[if", $pIfEnd );
			$pNextIf  = $this->_zmin( $pNextIf1, $pNextIf2 );

			if ( $pNextIf >= 0 )
				$this->_extractIf( $str, $pNextIf );
			
			$pNextEndIf1 = $this->_strpos( $str, "<![endif]", $pIfEnd );
			$pNextEndIf2 = $this->_strpos( $str, "<![endif]", $pIfEnd );
			$pNextEndIf  = $this->_zmin( $pNextEndIf1, $pNextEndIf2 );
			
			$pNextEndIfEnd1 = $this->_strpos( $str, ">", $pNextEndIf );
			$pNextEndIfEnd2 = $this->_strpos( $str, ">", $pNextEndIf );
			$pNextEndIfEnd  = $this->_zmin( $pNextEndIfEnd1, $pNextEndIfEnd2 );
	
			$pCond = $this->_strpos( $str, "[", $pIf );
			$ifCondition = substr( $str, $pCond + 1 + 2 + 1, $pIfEnd - $pCond - 2 - 2 - 1 );
			
			$oki = false;
			
			if ( $ifCondition == "!vml" )
				$oki = true;
			
			$insideIf = "";
						
			//	$pos		$pIf	$pIfEnd				$pNextEndIf	$pNextEndIfEnd
			//		....	<![if...	>		...		<![end if]>						....
			if ( $oki )
				$insideIf = substr( $str, $pIfEnd + 1, $pNextEndIf - ( $pIfEnd + 1 ) );
	
			$str = substr( $str, 0, $pIf ) . $insideIf . substr( $str, $pNextEndIfEnd + 1 );
		}
		else
		{
			return substr( $str, $pos );
		}
	}

	/**
	 * @access public
	 */		
	function _zmin( $p1, $p2 )
	{
		return ( ( $p1 >= 0 ) && ( ( $p1 < $p2 ) || ( $p2 == -1 ) ) )? $p1 : $p2;
	}

	/**
	 * @access public
	 */	
	function _strpos( $mystring, $findme, $start )
	{
		$res = @strpos( $mystring, $findme, $start );
		
		if ( $res === false )
			return -1;
			
		return $res;
	}
} // END OF WordHTML

?>
