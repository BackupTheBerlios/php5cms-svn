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
 * @package html
 */
 
class HTMLUtil
{
	/** 
 	 * Removes all html-comments from content.
	 *
	 * @access public
	 * @static
 	 */
	function stripComments( $html ) 
	{
		$content = preg_replace( "/<!--(.*)-->/", "", $html );
		return $content;
	}

	/**
	 * Removes all meta-tags from content.
	 *
	 * @access public
	 * @static
	 */
	function stripMetatags( $html ) 
	{
		$content = preg_replace( "/<(META|meta)(.*)>/", "", $html );
		return $content;
	}

	/**
	 * Rename tag.
	 *
	 * @access public
	 * @static
	 */
	function renameTag( $html, $from, $to )
	{
		$tag  = strtolower( $from );
		$tag2 = strtoupper( $from );

		$content = preg_replace( "/<(" . $tag . "|" . $tag2 . ")/",  "<" . $to, $html );
		$content = preg_replace( "/("  . $tag . "|" . $tag2 . ")>/", $to . ">", $html );
		
		return $content;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function stripLinks( $html )
	{
		return HTMLUtil::stripTag( $html, "a" );
	}

	/**
	 * @access public
	 * @static
	 */	
	function stripTag( $html, $tag, $respect_content = true )
	{
		$which = strtolower( $tag );
		
		if ( $respect_content )
		{
			// strip opening tags (and singleton)
			$content = preg_replace( "/(<" . $which . "[^>]*>)/", "", $html );

			// strip closing tag
			$content = preg_replace( '/(<\/' . $which . '>)/', "", $content );		
		}
		else
		{	
			// doesn't work!
			$content = preg_replace( '/(<' . $which . '[^>]*>)(.*)(<\/' . $which . '>)/', '', $html );
			$content = preg_replace( "/(<" . $which . "[^>]*\/>)/", "", $html );
		}
		
		return $content;
	}

	/**
	 * @access public
	 * @static
	 */	
	function stripSpecialCommentAreas( $html, $string_begin, $string_end, $replace_with = "" )
	{
		$content = preg_replace( "/<!-- " . $string_begin . "(.*)" . $string_end . " -->/s", $replace_with, $html );
		return $content;
	}
	
	/** 
	 * Removes $what as html-attribute from content (not depends which tag)
	 * for example $what = border to remove all border-attributes (img & table)
	 *
	 * @access public
	 * @static
	 */
	function stripAttribute( $html, $what ) 
	{
		$what    = strtolower( $what );
		$what2   = strtoupper( $what );
		$content = preg_replace( "/<(.*)($what|$what2)=('|\")(.*)('|\")(.*)>/", "<\\1 \\6>", $html );

		return $content;
	}

	/**
	 * Sets all html-attributes $what to the value $val (tag-independent).
	 *
	 * @access public
	 * @static
	 */
	function changeAttribute( $html, $what, $val ) 
	{
		$what    = strtolower( $what );
		$what2   = strtoupper( $what );
		$content = preg_replace( "/<(.*)($what|$what2)=('|\")(.*)('|\")(.*)>/", "<\\1 \\2=\"{$val}\" \\6>", $html );
		
		return $content;
	}

	/**
	 * @access public
	 * @static
	 */
	function caseshift( $theValue, $case )	
	{
		$case = strtolower( $case );
		
		switch ( $case )	
		{
			case "upper":
				$theValue = strtoupper( $theValue );
				$theValue = strtr( $theValue, "·È˙Ì‚Í˚ÙÓÊ¯Â‰ˆ¸", "¡…⁄ÕƒÀ‹÷œ∆ÿ≈ƒ÷‹" );
				break;
			
			case "lower":
				$theValue = strtolower( $theValue );
				$theValue = strtr( $theValue, "¡…⁄ÕƒÀ‹÷œ∆ÿ≈", "·È˙Ì‚Í˚ÙÓÊ¯Â" );
				break;
		}
		
		return $theValue;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function htmlCaseshift( $theValue, $case )	
	{
		$inside   = 0;
		$newVal   = "";
		$pointer  = 0;
		$totalLen = strlen( $theValue );
		
		do	
		{
			if ( !$inside )	
			{
				$len     = strcspn( substr( $theValue, $pointer ), "<" );
				$newVal .= HTMLUtil::caseshift( substr( $theValue, $pointer, $len), $case );
				$inside  = 1;	
			} 
			else 
			{
				$len     = strcspn( substr( $theValue, $pointer ), ">" ) + 1;
				$newVal .= substr( $theValue, $pointer, $len );
				$inside  = 0;
			}
			
			$pointer += $len;
		} while ( $pointer < $totalLen );
		
		return $newVal;
	}

	/**
	 * @access public
	 * @static
	 */	
	function hiliteHTMLSafe( $needle, $haystack, $hilite_start = '<b>', $hilite_end = '</b>' )
	{
		if ( !preg_match( '/<.+>/', $haystack ) ) 
		{
			// If there are no tags in the text, we'll just do a simple search and replace.
			$haystack = preg_replace( '/(' . $needle . ')/i', $hilite_start . '$1' . $hilite_end, $haystack );  
		} 
		else 
		{
			// If there are tags, we need to stay outside them.
			$haystack = preg_replace( '/(?<=>)([^<]+)?(' . $needle . ')/i','$1<span class="highlight">$2</span>', $haystack );
		}

		return $haystack;
	}

	/**
	 * Implodes attributes in the array $arr for an attribute list in eg. and HTML tag (with quotes).
	 *
	 * @access public
	 * @static
	 */
	function implodeParams( $arr )	
	{
		if ( is_array( $arr ) )	
		{
			reset( $arr );
			$list = array();
			while( list( $p, $v ) = each( $arr ) )	
			{
				if ( strcmp( $v, "" ) )	
					$list[] = $p . '="' . $v . '"';
			}
			
			return implode( " ", $list );
		}
	}
	
	/** 
	 * Sets all table borders to $width.
	 *
	 * @access public
	 * @static
	 */
	function setBorder( $html, $width = 1 ) 
	{
		$content = preg_replace( "/<(table|TABLE)(.*)border=('|\")(.*)('|\")(.*)>/", "<\\1\\2border=\"$width\"\\6>", $html );		
		return $content;
	}

	/**
	 * Prefixes all href-attributes in the content with $prefix, adds $target-attri if given.
	 *
	 * @access public
	 * @static
	 */
	function fixLinks( $html, $prefix, $target = "" ) 
	{
		if ( strlen( $target ) != 0 ) 
			$target = " target=\"{$target}\" ";
			
		$content = preg_replace( "/(HREF|href)=('|\")\/(.*)('|\")/", "{$target}\\1=\"{$prefix}\\3\"", $html );
		return $content;
	}

	/**
	 * Fixes all src-attributs in the content with $prefix.. (for fixing images).
	 *
	 * @access public
	 * @static
	 */
	function fixSrc( $html, $prefix ) 
	{
		$content = preg_replace( "/(src|SRC)=('|\")\/(.*)('|\")/", "\\1=\"$prefix\\3\"", $html );
		return $content;
	}

	/**
	 * @access public
	 * @static
	 */
	function convertHTMLForPrintout( $html ) 
	{
		$srch = array(
			"&"    => "&amp;",
			"<"    => "&lt;",
			">"    => "&gt;",
			"\""   => "&quot;
			","\n" => "<br>"
		);
		
		$content = $html;
		
		foreach ( $srch as $key => $value )
			$content = str_replace( $key, $value, $content );

		return $content;
	}

	/**
	 * Here you can make $content much tinyer, this function
	 * cuts out the text from $start to $end and defines that as new $content.
	 *
	 * @access public
	 * @static
	 */
	function cutOut( $html, $start, $end ) 
	{
		$content  = $html;
		$startpos = strpos( $content, $start );
		$endpos   = strpos( $content, $end, $startpos );	
		$content  = substr( $content, $startpos, $endpos - $startpos );
		
		return $content;	
	}

	/**
	 * Cuts off $howmany characters from the end of the string.
	 *
	 * @access public
	 * @static
	 */
	function cutOffFromEndInt( $html, $howmany ) 
	{
		return substr( $html, 0, -$howmany );
	}
	
	/**
	 * @access public
	 * @static
	 */
	function charToHtml( $param = '', $reverse = false ) 
	{
		if ( $param == '' ) 
			return $param;
			
		static $lookFor = array( 
			"0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
			"A", "B", "C", "D", "E", "F", "G", "H", "I", "J", 
			"K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", 
			"U", "V", "W", "X", "Y", "Z", 
			"a", "b", "c", "d", "e", "f", "g", "h", "i", 
			"j", "k", "l", "m", "n", "o", "p", "q", "r", 
			"s", "t", "u", "v", "w", "x", "y", "z", 
			"@"
		);
		
		static $replaceWith = null;
		
		if ( is_null( $replaceWith ) ) 
		{
			$replaceWith = array();
			$size = sizeof( $lookFor );
			
			for ( $i = 0; $i < $size; $i++ ) 
				$replaceWith[$lookFor[$i]] = '&#' . ord( $lookFor[$i] ) . ';';
		}
		
		if ( $reverse ) 
		{
			$reverseReplace = array_flip( $replaceWith );
			return strtr( $param, $reverseReplace );
		}

		return strtr( $param, $replaceWith );
	}
	
	/**
	 * @access public
	 * @static
	 */
	function filterForJavaScript( $param ) 
	{
		if ( $param == '' ) 
			return '';
			
		static $replaceWith = array( 
			"\\" => "\\\\",  
			"\"" => "\\\"",  
			"'"  => "\\'",   
			"\n" => "\\n",  
			"\f" => "\\f", 
			"\r" => "\\\r"
		);
		
		return strtr( $param, $replaceWith );
	}

	/**
	 * @access public
	 * @static
	 */
	function filterForHtml( $param, $quoteTrans = ENT_COMPAT ) 
	{
		if ( $param == '' ) 
			return '';
			
		$param = htmlspecialchars( $param, $quoteTrans );
		
		static $replaceWith = array(
			"\n" => '&#10;', 
			"\r" => '&#13;'
		);
		
		return strtr( $param, $replaceWith );
	}

	/**
	 * @access public
	 * @static
	 */
	function jsAlert( $value ) 
	{
		$ret  = "<script language='javascript'>\n";
		$ret .= "alert(' " . HTMLUtil::filterForJavaScript( $value ) . "')\n";
		$ret .= "</script>\n";
		
		return $ret;
	}

	/**
	 * @access public
	 * @static
	 */
	function arrayToJsArray( $array, $nameOfJsArray = 'myArray', $firstCall = true ) 
	{
		if ( $firstCall ) 
			$ret  = "var {$nameOfJsArray} = new Array();\n";
		else 
			$ret  = "{$nameOfJsArray} = new Array();\n";

		foreach ( $array as $key => $val ) 
		{
			if ( is_array( $val ) ) 
			{
				$ret .= HTMLUtil::arrayToJsArray( $array[$key], $nameOfJsArray . "['" . HTMLUtil::filterForJavaScript( $key ) . "']", false );
			} 
			else 
			{
				if ( gettype( $val ) == 'boolean' ) 
					$val = $val? 'true' : 'false';
				else 
					$val = '"' . HTMLUtil::filterForJavaScript( $val ) . '"';

				$ret .= "{$nameOfJsArray}['{$key}'] = " . $val . ";\n";
			}
		}
		
		$ret .= "\n";
		return $ret;
	}

	/**
	 * @access public
	 * @static
	 */
	function arrayToHtmlSelect( $myArray, $selected = '' )  
	{
		$ret = '';
		
		if ( !is_array( $myArray ) ) 
			return $ret;
		
		reset( $myArray );
		$zeroBased = ( key( $myArray ) === 0 )? true : false;
		
		while ( list( $key ) = each( $myArray ) ) 
		{
			$val = &$myArray[$key];
			
			if ( $zeroBased )
				$key = $val; 

			$selString = '';
			
			if ( is_array( $selected ) ) 
			{
				if ( in_array( $key, $selected ) ) 
					$selString = ' selected';
			} 
			else 
			{
				if ( $key == $selected ) 
					$selString = ' selected';
			}
			
			$ret .= "<option value='{$key}'{$selString}>{$val}</option>\n";
		}
		
		return $ret;
	}
	
	/**
	 * Returns an array with the "components" from an attribute list from an HTML tag. The result is normally analyzed by getTagAttributes
	 * Removes tag-name if found.
	 *
	 * @access public
	 * @static
	 */
	function splitTagAttributes( $tag )	
	{
		$tag_tmp = trim( eregi_replace( "^[ ]*<[^ ]*", "", $tag ) );
		
		// Removes any > in the end of the string
		$tag_tmp = trim( eregi_replace( ">$", "", $tag_tmp ) );

		while ( strcmp( $tag_tmp, "" ) )	
		{	
			$firstChar = substr( $tag_tmp, 0, 1 );
			
			if ( !strcmp( $firstChar,'"' ) || !strcmp( $firstChar, "'" ) )	
			{
				$reg = explode( $firstChar, $tag_tmp, 3 );
				$value[] = $reg[1];
				$tag_tmp = trim( $reg[2] );
			} 
			else if ( !strcmp( $firstChar, "=" ) ) 
			{
				$value[] = "=";
				$tag_tmp = trim( substr( $tag_tmp, 1 ) ); // Removes = chars.
			} 
			else 
			{
				// There are "" around the value. We look for the next " " or ">"
				$reg     = split( "[[:space:]=]", $tag_tmp, 2 );
				$value[] = trim( $reg[0] );
				$tag_tmp = trim( substr( $tag_tmp, strlen( $reg[0] ), 1 ) . $reg[1] );
			}
		}
		
		if ( is_array( $value ) )	
			reset( $value );
			
		return $value;
	}
	
	/**
	 * $tag is either a whole tag (eg "<TAG OPTION ATTRIB=VALUE>") or the parameterlist (ex " OPTION ATTRIB=VALUE>")
	 * Returns an array with all attributes as keys. Attributes are only lowercase a-z
	 * If a attribute is empty (I call it "an option"), then the value for the key is empty. You can check if it existed with isset()
	 *
	 * @access public
	 * @static
	 */
	function getTagAttributes( $tag )	
	{
		$components = HTMLUtil::splitTagAttributes( $tag );
		$name       = "";	 // attribute name is stored here
		$valuemode  = "";
		
		if ( is_array( $components ) )	
		{
			while ( list( $key, $val ) = each( $components ) )	
			{
				// Only if $name is set (if there is an attribute, that waits for a value), that valuemode is enabled. 
				// This ensures that the attribute is assigned it's value
				if ( $val != "=" )	
				{						
					if ( $valuemode )	
					{
						if ( $name )	
						{
							$attributes[$name] = $val;
							$name = "";
						}
					} 
					else 
					{
						if ( $key = strtolower( ereg_replace( "[^a-zA-Z0-9]", "", $val ) ) )	
						{
							$attributes[$key] = "";
							$name = $key;
						}
					}
					
					$valuemode = "";
				} 
				else 
				{
					$valuemode = "on";
				}
			}
			
			if ( is_array( $attributes ) )	
				reset( $attributes );
				
			return $attributes;
		}
	}

	/**
	 * @access public
	 * @static
	 */
	function htmlEntitiesUndo( $string ) 
	{
		if ( strlen( $string ) < 5 ) 
			return $string;
				
		static $trans;
		$trans = array_flip( get_html_translation_table( HTML_ENTITIES, ENT_QUOTES ) );
		return strtr( $string, $trans );
	} 
		
	/**
	 * @access public
	 * @static
	 */
	function &parseStyleStr( &$styleStr ) 
	{
		$styleHash = array();
		
		if ( !is_string( $styleStr ) ) 
			return $styleHash;
			
		$tmp = trim( $styleStr );
		
		if ( $tmp == '' ) 
			return $styleHash;
			
		$elements = explode( ';', $styleStr );
		$elemSize = sizeOf( $elements );
		
		for ( $i = 0; $i < $elemSize; $i++ ) 
		{
			$tmp = trim( $elements[$i] );
			
			if ( $tmp == '' ) 
				continue;
				
			$stylePair = explode( ':', $tmp );
			$name = strtolower( trim( $stylePair[0] ) );
			$val  = &$stylePair[1];
			
			if ( isset( $val ) ) 
			{
				if ( $name != '' ) 
					$styleHash[$name] = trim( $val );
			} 
			else 
			{
				if ( $name != '' ) 
					$styleHash[$name] = null;
			}
		}

		return  $styleHash;
	} 

	/**
	 * @access public
	 * @static
	 */	
	function &parseAttrStr( &$attrStr ) 
	{
		$attrHash = array();
		
		if ( !is_string( $attrStr ) ) 
			return $attrHash;
			
		$regEx_ValuePair = '|([^\s]+)\s*=\s*[\'"](.*)[\'"]|U';
		preg_match_all( $regEx_ValuePair, $attrStr, $regs );
		$noApostrophe = preg_replace( $regEx_ValuePair, '', $attrStr );
		$attrSize     = sizeof( $regs[1] );
		
		for ( $i = 0; $i < $attrSize; $i++ ) 
			$attrHash[strtolower( $regs[1][$i] )] = $regs[2][$i];

		$regEx_ValuePair = '|([^\s]+)\s*=\s*([^\s]+)|';
		preg_match_all( $regEx_ValuePair, $noApostrophe, $regs );
		$singleAttr = preg_replace( $regEx_ValuePair, '', $attrStr );
		$attrSize   = sizeof( $regs[1] );
		
		for ( $i = 0; $i < $attrSize; $i++ ) 
			$attrHash[strtolower( $regs[1][$i] )] = $regs[2][$i];

		preg_match_all( '|(\w+)|', $singleAttr, $regs );
		$attrSize = sizeof( $regs[1] );
		
		for ( $i = 0; $i < $attrSize; $i++ ) 
			$attrHash[strtolower( $regs[1][$i] )] = null;

		return $attrHash;
	}

	/**
	 * Checks for HTML entities in submitted text.
	 * If found returns true, otherwise false. HTML specials are:
	 *		"	=>	&quot;
	 *		<	=>	&lt;
	 *		>	=>	&gt;
	 *		&	=>	&amp;
	 * The presence of ",<,>,&  will force this method to return true.
	 *
	 * @access public
	 * @static
	 */
	function hasHTML( $text = "" )
	{
		if ( empty( $text ) )
			return false;
		
		$new = htmlspecialchars( $text );
		
		if ( $new == $text )
			return false;
		
		return true;
	}

	/**
	 * Strips all html entities, attributes, elements and tags from
	 * the submitted string data and returns the results.
	 * Can't use a regex here because there's no way to know
	 * how the data is laid out. We have to examine every character
	 * that's been submitted. Consequently, this is not a very
	 * efficient method. It works, it's very good at removing
	 * all html from the data, but don't send gobs of data
	 * at it or your program will slow to a crawl.
	 * If you're stripping HTML from a file, use PHP's fgetss()
	 * and NOT this method, as fgetss() does the same thing
	 * about 100x faster.
	 *
	 * @access public
	 * @static
	 */
	function stripHTML( $text = "" )
	{
		if ( ( !$text ) || ( empty( $text ) ) )
			return "";
		
		$outside = true;
		$rawText = "";
		$length  = strlen( $text );
		$count   = 0;

		for ( $count = 0; $count < $length; $count++ )
		{
			$digit = substr( $text, $count, 1 );
			
			if ( !empty( $digit ) )
			{
				if ( ( $outside ) && ( $digit != "<" ) && ( $digit != ">" ) )
					$rawText .= $digit;
				
				if ( $digit == "<" )
					$outside = false;
				
				if ( $digit == ">" )
					$outside = true;
			}
		}
		
		return $rawText;
	}

	/**
	 * @access public
	 * @static
	 */
	function makeClickable( $str )
	{
		$str = eregi_replace( '(((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)', '<a href="\\1" target="_blank">\\1</a>', $str ); 
		$str = eregi_replace( '([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&//=]+)', '\\1<a href="http://\\2" target="_blank">\\2</a>', $str ); 
		$str = eregi_replace( '([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})','<a href="mailto:\\1">\\1</a>', $str ); 

		return $str;
	}
	
	/**
	 * Auto linked URL, E-mail address in document.
	 *
	 * @access public
	 * @static
	 */
	function getAutoLink( $str )
	{ 
		// don't use target if tail is follow 
		$regex[file] = "gz|tgz|tar|gzip|zip|rar|mpeg|mpg|exe|com|rpm|dep|rm|ram|asf|ace|viv"; 

		// define URL 
		$regex[http] = "(http|https|ftp|telnet|news):\/\/([a-z0-9_\-]+\.[][a-z0-9:;&#@=_~%\?\/\.\,\+\-]+)"; 

		// define E-mail address 
		$regex[mail] = "([a-z0-9_\-]+)@([a-z0-9_\-]+\.[a-z0-9\-\._\-]+)"; 

		// don't use target 
		$regex[notarget] = "(http|https|ftp|news):\/\/([a-z0-9_\-]+\.[][a-z0-9:;&#@=_~%\?\/\.\,\+\-]+)\/[][a-z0-9:#@_~%\/\.\,\+\-]+\.($regex[file])"; 

		// if use "wrap=hard" option in TEXTAREA tag, connected 2 lines that devided 2 lines in a link 
		$str = eregi_replace( "<([^<>\n]+)\n([^\n<>]+)>", "<\\1 \\2>", $str ); 

		// replaced special char and delete target 
  		$str = eregi_replace( "&(quot|gt|lt)", "!\\1", $str ); 
  		$str = eregi_replace( "([ ]+)target=[\"'_a-z,A-Z]+", "", $str ); 
  		$str = eregi_replace( "([ ]+)on([a-z]+)=[\"'_a-z,A-Z\?\.\-_\/()]+", "", $str ); 

		// protected link when use html link code 

		// protected URL 
  		$str = eregi_replace( "<a([ ]+)href=([\"']*)($regex[http])([\"']*)>", "<a href=\"\\4_orig://\\5\" target=\"_blank\">", $str ); 
  
  		// protected E-mail 
  		$str = eregi_replace( "<a([ ]+)href=([\"']*)mailto:($regex[mail])([\"']*)>", "<a href=\"mailto:\\4#-#\\5\">", $str ); 
  
 	 	// protected Image link 
  		$str = eregi_replace( "<img([ ]*)src=([\"']*)($regex[http])([\"']*)", "<img src=\"\\4_orig://\\5\"", $str ); 
  
  		// protected flash link 
  		$str = eregi_replace( "<embed([ ]*)src=([\"']*)($regex[http])([\"']*)", "<embed src=\"\\4_orig://\\5\"", $str ); 

		// auto linked url and email address that unlinked 
  		$str = eregi_replace( "($regex[http])", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $str ); 
  		$str = eregi_replace( "($regex[mail])", "<a href=\"mailto:\\1\">\\1</a>", $str ); 

		// restored code that replaced for protection 
  		$str = eregi_replace( "!(quot|gt|lt)", "&\\1", $str ); 
  		$str = eregi_replace( "(http|https|ftp|telnet|news)_orig", "\\1", $str ); 
  		$str = eregi_replace( "#-#", "@", $str ); 

		// delete multiple link 
  		$str = eregi_replace( "(<a href=([\"']*)($regex[http])([\"']*)+([^>]*)>)+<a href=([\"']*)($regex[http])([\"']*)+([^>]*)>", "\\1", $str ); 
  		$str = eregi_replace( "(<a href=([\"']*)mailto:($regex[mail])([\"']*)>)+<a href=([\"']*)mailto:($regex[mail])([\"']*)>", "\\1", $str );
  		$str = str_replace( "</a></a>", "</a>", $str); 

	 	// if url is file, delete target 
		$str = eregi_replace( "(<a href=\"$regex[notarget]\")+ target=\"_blank\"", "\\1", $str );

		return $str; 
	}
	
	/**
	 * A function for parsing html.  
	 * This function just extracts the html tags from a string and put
	 * them in a array like ["IMG"][0]["WIDTH"] = 130
	 *
	 * What it does: 
	 * - parses a html string and get the tags 
	 * - exceptions: html tags like <br> <hr> </a>, etc 
	 * - At the end, the array will look like this: 
	 *	 ["IMG"][0]["SRC"] = "xxx" 
	 * 	 ["IMG"][1]["SRC"] = "xxx" 
	 *	 ["IMG"][1]["ALT"] = "xxx" 
	 *	 ["A"][0]["HREF"]  = "xxx"
	 *	
	 * Example:
	 *	 $str = join( "\n", file( "index.html" ) ); 
	 *	 $htmlParsed = array();
	 *	 $parser = new SimpleParser;
	 *	 $htmlParsed = $parser->parseHTML( $str ); 
	 *	 var_dump( $htmlParsed );
	 *
	 * @param  string
	 * @access public
	 * @static
	 */
	function parseHTML( $str )
	{ 
		$indicatorL   = 0; 
		$indicatorR   = 0;
		$arrayCounter = 0;
		$tagOption    = ""; 
		$html         = array(); 

		// search for a tag in string 
 		while ( is_int( ( $indicatorL = strpos( $str, "<", $indicatorR ) ) ) )
		{ 
			// get everything into tag... 
   		    $indicatorL++; 
   	   		$indicatorR = strpos( $str, ">", $indicatorL ); 
			$temp = substr( $str, $indicatorL, ( $indicatorR - $indicatorL ) ); 
			$tag  = explode( ' ', $temp ); 
         
			// here we get the tag's name 
   		   	list( , $tagName,, ) = each( $tag ); 
   	 	    $tagName = strtoupper( $tagName ); 
         
			// Hmm, I am not interesting in <br>, </font> or anything else like that... 
    	    // So, this is false for tags without options. 
			$boolOptions = is_array( ( $tagOption = each( $tag ) ) ) && $tagOption[1]; 
         
			if ( $boolOptions )
			{ 
				// without this, we will mess up the array 
      		  	$arrayCounter = (int)count( $html[$tagName] ); 
            
				// get the tag options, like src="http://". Here, s_tagTokOption is 'src' and s_tagTokValue is '"http://"' 
				do
				{ 
					$tagTokOption = strtoupper( strtok( $tagOption[1], "=" ) ); 
					$tagTokValue  = trim( strtok( "=" ) ); 
					$html[$tagName][$arrayCounter][$tagTokOption] = $tagTokValue; 
					$boolOptions = is_array( ( $tagOption = each( $tag ) ) ) && $tagOption[1]; 
				} while ( $boolOptions ); 
			}
		} 

		return $html; 
	}
	
	/**
	 * This function opens and parses $html_file for $tag
	 * and returns its content and its attributes to the
	 * callback function $element_handler.
	 * $element_handler is a custom funtion which acts upon
	 * the content and the attributes of $tag and gets called
	 * everytime $tag is closed. It must accept the following
	 * parameters:
  	 * - $attributes (attributes of $tag
  	 * - $content (content of the element $tag)
	 *
	 * Example usage:
 	 *
 	 * function my_handler( $attribs, $content )
  	 * {
   	 *		echo $content;
   	 * }
 	 *
 	 * HTMLUtil::parseHTML_handler( "index.htm", "my_handler", "title" );
	 */
	function parseHTML_handler( $html_file, $element_handler, $tag )
	{
		if ( file_exists( $html_file ) )
		{
			$fd = fopen( $html_file, "r" );
			
			if ( !$fd )
				return PEAR::raiseError( "Cannot open file " . $html_file );
				
      		$file_content = fread( $fd, filesize( $html_file ) );
      		fclose( $fd );
      		$file_content = stripslashes( $file_content );
 		}
 		else
		{
			$file_content = $html_file;
		}
     
  		while ( $full_tag = strstr( $file_content, "<$tag" ) )
		{
			$full_tag = substr( $full_tag, 0, strpos( $full_tag, "</$tag>" ) + strlen( $tag ) + 3 );
			$open_tag = substr( $full_tag, 0, strpos( $full_tag, ">" ) + 1 );        
			$open_tag = ereg_replace( "[<>]|$tag",  "", $open_tag );
         
			// Split the string into key/value pairs: first split it into key=value, later into a hash. 
        	$tmp_array = split( "[$\"] +", $open_tag );
        
			for ( $i = 0; $i < count( $tmp_array ); $i++ )
			{
            	$tmp_array[$i] = trim( $tmp_array[$i] );
            	$tmp_array[$i] = ereg_replace( "\"",  "", $tmp_array[$i] );
            	$tmp_attribs   = split( "=", $tmp_array[$i] );
            
				for ( $j = 0; $j < count( $tmp_attribs ); $j++ )
          		{
            		// Don't add empty pairs :) 
                	if ( ( $tmp_attribs[$j] != "" ) && ( $tmp_attribs[$j+1] !=  "" ) )
                		$attribs[trim( $tmp_attribs[$j] )] = trim( $tmp_attribs[$j+1] );
				}
			}
		
        	$content = substr( $full_tag, strpos( $full_tag, ">" ) );
        	$content = substr( $content, 1, strpos( $content, "</$tag>" ) - 1 );
        	$element_handler( $attribs, trim( $content ) );
        	$file_content = substr( $file_content, strpos( $file_content, "</$tag>" ) + strlen( $tag ) + 3 );
		}
	}
} // END OF HTMLUtil

?>
