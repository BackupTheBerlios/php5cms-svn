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
 * APScript is a quick hack to let users have access to some scripting to e.g.
 * control layout of a page without them having any control over the PHP scripts used
 * to get data in and out of the database. 
 *
 * What it does is to map all variable accesses to a given object. All function call's will
 * be mapped to a given code object (can be the same). This way you have total control over
 * what the user can or cannot access.
 *
 * All functions you want your user to have access to must be mapped as
 * "_functionname" under the code object. Many 'harmless' functions are left
 * alone by default. These can be changed in the constructor routine.
 *
 * After compiling your script you can run it with 'eval("?".">$script");' or
 * you can write it to disk as a new php script, which you can then include.
 *
 * @package util
 */

class APScript extends PEAR
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function APScript( $allowed_functions, $var_prefix, $function_prefix = "" )
	{
    	if ( !$function_prefix )
			$function_prefix = $var_prefix;
    
    	$this->starttag = "<apscript>";
    	$this->endtag   = "</apscript>";
		
    	$this->var_prefix      = $var_prefix;
    	$this->function_prefix = $function_prefix;

    	if ( is_array( $allowed_functions ) )
			$allowed_functions = implode( "|", $allowed_functions );

		$control_funcs	= "if|while|for|switch";    
		$array_funcs	= "array|array_count_values|array_flip|array_keys|array_merge|array_pad|array_pop|array_push|array_reverse|array_shift|array_slice|array_splice|array_unshift|array_values|array_walk|arsort|asort|count|current|each|end|in_array|key|krsort|ksort|list|next|pos|prev|range|reset|rsort|shuffle|sizeof|sort";
		$string_funcs	= "AddSlashes|bin2hex|Chop|Chr|chunk_split|convert_cyr_string|crypt|echo|explode|flush|get_html_translation_table|htmlentities|htmlspecialchars|implode|join|ltrim|md5|Metaphone|nl2br|Ord|print|printf|quoted_printable_decode|QuoteMeta|rawurldecode|rawurlencode|setlocale|similar_text|soundex|sprintf|strcasecmp|strchar|strcmp|strcspn|strip_tags|StripCSlashes|StripSlashes|stristr|strlen|strpos|strrchr|str_repeat|strrev|strrpos|strspn|strstr|strtok|strtolower|strtoupper|str_replace|strtr|substr|substr_replace|trim|ucfirst|ucwords";
		$math_funcs		= "abs|acos|asin|atan|atan2|base_convert|bindec|ceil|cos|decbin|dechex|decoct|exp|floor|getrandmax|hexdec|log|log10|max|min|mt_rand|mt_srand|mt_getrandmax|number_format|octdec|pi|pow|rand|round|sin|sqrt|srand|tan";
		$var_funcs		= "doubleval|empty|gettype|intval|is_array|is_double|is_float|is_int|is_integer|is_long|is_object|is_real|is_string|isset|settype|strval|unset";
		$time_funcs		= "checkdate|date|getdate|gettimeofday|gmdate|gmmktime|gmstrftime|microtime|mktime|strftime|time|easter_days|easter_date";
		$regex_funcs	= "ereg|ereg_replace|eregi|eregi_replace|split";
		
		$this->re_allowed = "|$control_funcs|$array_funcs|$string_funcs|$math_funcs|$var_funcs|$time_funcs|$allowed_functions|$regex_funcs|";

    	// don�t touch this unless you know what you are doing
    	$slash              = "\\\\";
    	$delimiter          = "'"; 
    	$this->re_string1   = "([^'\"]*)($delimiter([^$slash]|$slash.)*?$delimiter)";  
    	$delimiter          = '"';
    	$this->re_string2   = "([^'\"]*)($delimiter([^$slash]|$slash.)*?$delimiter)";  
    	$this->re_apscript  = $this->starttag . "([^'\"]*?|($this->re_string1|$this->re_string2)*?[^'\"]*?)" . $this->endtag; 
    	$this->pre_comments = '|/\*.*\*/|U';
	}

	
	/**
	 * @access public
	 */
	function compile( $template )
	{
    	// first remove any php code
    	$template = preg_replace( "/<\?.*\?>/isU", "", $template );
    	$template = preg_replace( "/<%.*%>/isU",   "", $template );
    	$template = preg_replace( "|<script[^>]*php[^>]*>.*</script>|isU", "", $template );

    	// then remove /* .. */ comments, even in html code... for security
    	// since php supports comment tags including ? > and < ?php the comments check must be made first. 
    	$template = preg_replace( $this->pre_comments, "", $template ); // this does :)
    	$template = str_replace( "\r\n", "\n", $template );

    	// now find next <apscript> </apscript> block
    	if ( preg_match("!^(.*?)" . $this->re_apscript . "!m", $template, $matches ) )
		{
			// apscript code block found, move 'head' to result
			$result = substr( $template, 0, strpos( $template, $matches[0] ) );

      		// cut matched part from the template
      		$template = substr( $template, strpos( $template, $matches[0] ) + strlen( $matches[0] ) );

      		// fix code, call compile on rest recursively
      		$result .= $matches[1] . $this->fixcode( "<" . "?php " . $matches[2] . " ?" . ">" ) . $this->compile( $template );
		}
		else
		{
			// no apscript code found
      		$result = $template;
    	} 

		return $result;
	}

	/**
	 * @access public
	 */
	function fixcode( $code )
	{
    	// find first occurance of a string
    	if ( preg_match( "!^(" . $this->re_string1 . "|" . $this->re_string2 . ")!m", $code, $matches ) )
		{
      		if ( ( $matchpos = strpos( $code, $mathes[0] ) ) !== 0 )
			{ 
        		// a single ' or " found, but isn't matched in any of the $matches
        		$pre = substr( $code, 0, $matchpos );
      		}
      
	  		// string found, cut matched part from $code
      		$code = substr( $code, strpos( $code, $matches[0] ) + strlen( $matches[0] ) );
      
	  		// check which kind of string was found '' or "".
      		if ( $matches[2] )
			{
				// single quoted string found
        		// $matches[2] is the part before the string, 
        		// $matches[3] is the string ('')
        		$pre    .= $matches[2];
        		$string  = $matches[3];
      		}
			else
			{
				// double quoted string found
        		// $matches[5] is the part before the string
        		// $matches[6] is the string ("")
        		$pre    .= $matches[5];
        		$string  = $matches[6];
			}
			
			if ( preg_match( "|//[^\n]*\$|", $pre ) )
			{
        		if ( preg_match( "|([^\n]*)\n|", $string . $code, $matches ) )
				{
          			$pre   .= $matches[1] . "\n";
          			$code   = substr( $string . $code, strlen( $matches[1] ) + 1 );
          			$string = "";          
        		}
      		} 
      
	  		$result = $this->fixother( $pre ) . $this->fixstring( $string ) . $this->fixcode( $code );
    	}
		else
		{
      		$result = $this->fixother( $code );
    	}

		return $result;
	}

	/**
	 * @access public
	 */
	function fixstring( $string )
	{
    	// in a string only the variables need to be fixed
    	$slash = "\\\\";
    	return ereg_replace( "([^$slash]($slash$slash)*\\\$)", "\\1" . $this->var_prefix, $string );
  	}

	/**
	 * @access public
	 */
	function fixother( $other )
	{
    	// fix variables and functions  

    	// remove backticks
    	$other  = ereg_replace( "`","", $other );
		$result = ereg_replace( "\\\$", "\$" . $this->var_prefix, $other ); // fixes variables

		return $this->fixfunctions( $result );
	}

	/**
	 * @access public
	 */
	function fixfunctions( $varfixed )
	{
    	// functions that are not in the allowed list must be prefixed

    	// prevent any attempt of creating a new object.
    	$varfixed = eregi_replace( "([^a-z0-9_-])new([^a-z0-9_-])", "\\1nonew\\2", $varfixed );
    
		// find function names
    	while ( eregi( "([^a-z0-9_-])([a-z][a-z0-9_-]*)[[:space:]]*\(", $varfixed, $matches ) )
		{
      		// function name found
      		$function = $matches[2];

      		// add 'head' to the result
      		$result  = substr( $varfixed, 0, strpos( $varfixed, $matches[0] ) );
      		$result .= $matches[1];

      		// cut matched part from $varfixed
      		$varfixed = substr( $varfixed, strpos( $varfixed, $matches[0] ) + strlen( $matches[0] ) - 1 );

      		// check if function is allowed
      		if ( !eregi( "\|" . $function . "\|", $this->re_allowed ) )
			{
				// function not allowed, add prefix
        		$result .= $this->function_prefix . $function;
			}
			else
			{
				// function allowed, copy unchanged 
				$result .= $function;
			}
		}

		// add reminder to $result
		$result .= $varfixed;

		return $result;
	}
} // END OF APScript

?>
