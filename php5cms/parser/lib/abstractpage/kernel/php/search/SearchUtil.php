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


using( 'util.text.StringUtil' );


/**
 * Static helper
 *
 * @package search
 */
 
class SearchUtil
{
	/**
	 * @access public
	 * @static
	 */		
	function keywordCheck( $filenames, $keywords )
	{ 
		for ( $i = 0; $i < count( $filenames ); $i++ )
  		{ 
    		$filename = $filenames[$i]; 
    		$match    = 0; 
    		$fd       = fopen( $filename, "r" ); 
    		$contents = fread( $fd, filesize( $filename ) );
			
    		fclose( $fd ); 
    
			// remove HTML Tags before searching 
        	$search = array(
				"'<script[^>]*?>.*?</script>'si",	// strip out javascript 
				"'<[\/\!]*?[^<>]*?>'si",  			// strip out html tags 
				"'([\r\n])[\s]+'",  				// strip out white space 
				"'&(quot|#34);'i",  				// replace html entities 
 				"'&(amp|#38);'i", 
				"'&(lt|#60);'i", 
				"'&(gt|#62);'i", 
				"'&(nbsp|#160);'i", 
				"'&(iexcl|#161);'i", 
				"'&(cent|#162);'i", 
				"'&(pound|#163);'i", 
				"'&(copy|#169);'i", 
				"'&#(\d+);'e"						// evaluate as php 
			);
         
        	$replace = array(
				"", 
				" ", 
				"\\1", 
				"\"", 
				"&", 
				"<", 
				">", 
				" ", 
				chr( 161 ), 
				chr( 162 ), 
				chr( 163 ), 
				chr( 169 ), 
				"chr(\\1)"
			); 
         
    		$contents = preg_replace( $search, $replace, $contents ); 
    		$contents = preg_replace( "/\W/",  " ", $contents ); 
    		$contents = preg_replace( "/\s+/", " ", $contents ); 

    		// seperate each word into an array element and compare to keywords 
    		$contents = explode( " ", $contents ); 
    
			$j = 0; 
    		for ( $j = 0; $j < count( $keywords ); $j++ ) 
    		{ 
      			for ( $k = 0; $k < count( $contents ); $k++ ) 
      			{ 
        			// compare contents with each keyword 
       	 			if ( !strcasecmp( $contents[$k], $keywords[$j] ) )  
        			{ 
          				$match++; 
          				break; 
        			} 
      			} 
    		} 
    
   			if ( $match == count( $keywords ) ) 
    			$retVal[count( $retVal )] = $filename; 
  		} 

		return $retVal; 
	}
	
	/**
	 * @access public
	 * @static
	 */
	function booleanSearchQuery( $searchString = "", $searchFieldString = "" ) 
	{
		$searchFieldString = trim( $searchFieldString );
		$searchString      = strtolower( trim( $searchString ) );
		$searchFieldArray  = explode( " ", $searchFieldString );
		$searchArray       = explode( " ", $searchString );
		
		$searchString      = str_replace( "\\\"",    "",   $searchString );
		$searchString      = str_replace( "(",       "",   $searchString );
		$searchString      = str_replace( ")",       "",   $searchString );
		$searchString      = str_replace( "*",       "",   $searchString );
		$searchString      = str_replace( " and ",   " +", $searchString );
		$searchString      = str_replace( " und ",   " +", $searchString );
		$searchString      = str_replace( " not ",   " -", $searchString );
		$searchString      = str_replace( " nicht ", " -", $searchString );
		
		$i = 0;
		while ( list( $dev0, $val ) = each( $searchArray ) ) 
		{
			if ( $val != "" ) 
			{
				if ( $val[0] == "-" ) 
				{
					$queryArray[$i]["operator"] = "AND NOT";
					$queryArray[$i]["string"]   = "LIKE '%" . substr( $val, 1 ) . "%'";
				} 
				else if ( $val[0] == "+" ) 
				{
					$queryArray[$i]["operator"] = "AND";
					$queryArray[$i]["string"]   = "LIKE '%" . substr( $val, 1 ) . "%'";
				} 
				else 
				{
					$queryArray[$i]["operator"] = "AND";
					$queryArray[$i]["string"]   = "LIKE '%" . $val . "%'";
				}
			}
			
			$i++;
		}

		$ret = "";
		
		for ( $i = 0; $i < count( $queryArray ); $i++ ) 
		{
			if ( $i > 0 ) 
				$ret .= $queryArray[$i]["operator"] . " (";
			else 
				$ret .= "(";
			
			for ( $j = 0; $j < count( $searchFieldArray ); $j++ ) 
			{
				if ( $j > 0 ) 
					$ret .= "OR ";
				
				$ret .= "LOWER(" . $searchFieldArray[$j] . ") ";
				$ret .= $queryArray[$i]["string"] . " ";
			}

			$ret .= ") ";
		}

		return $ret;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function parseSearchQuery( $string ) 
	{
		$ret = array();
		
		if ( empty( $string ) ) 
			return $ret;
			
		$string = strtolower( $string );
		$string = StringUtil::normalize( $string );
		$string = ' ' . $string;
		$string = str_replace( " and ",   " &", $string );
		$string = str_replace( " und ",   " &", $string );
		$string = str_replace( " + ",     " &", $string );
		$string = str_replace( " +",      " &", $string );
		$string = str_replace( " or ",    " |", $string );
		$string = str_replace( " oder ",  " |", $string );
		$string = str_replace( " not ",   " !", $string );
		$string = str_replace( " nicht ", " !", $string );
		$string = str_replace( " - ",     " !", $string );
		$string = str_replace( " -",      " !", $string );
		
		$line      = $string;
		$separator = ' ';
		$sepLength = strlen( $separator );
		$offset    = 0;
		$lastPos   = 0;
		$lineArray = array();
		
		do 
		{
			$pos = strpos( $line, $separator, $offset );
			
			if ( $pos === false ) 
			{
				$val = trim( substr( $line, $lastPos ) );
				
				if ( !empty( $val ) ) 
					$lineArray[] = $val;
					
				break;
			}
			
			$currentSnippet = substr( $line, $lastPos, $pos - $lastPos );
			$numQuotes = substr_count( $currentSnippet, '"' );
			
			if ( $numQuotes % 2 == 0 ) 
			{
				$val = trim( substr( $line, $lastPos, $pos - $lastPos ) );
				
				if ( !empty( $val ) ) 
					$lineArray[] = $val;
				
				$lastPos = $pos + $sepLength;
			} 
			else 
			{
			}

			$offset = $pos + $sepLength;
		} while ( true );
		
		while ( list( $k ) = each( $lineArray ) ) 
			$lineArray[$k] = str_replace( '"', '', $lineArray[$k] );

		reset( $lineArray );
		$searchArray = $lineArray;
		
		while ( list(,$word) = each( $searchArray ) ) 
		{
			if ( empty( $word ) ) 
				continue;
			
			$prefix = substr( $word, 0, 1 );
			
			switch ( $prefix ) 
			{
				case '&':
				
				case '!':
				
				case '|':
					$operator = $prefix;
					$word = substr( $word, 1 );
					
					break;
				
				default:
					$operator = '|';
			}

			$phrase = $word;
			$word   = explode( ' ', $word );
			
			$ret[] = array(
				'phrase'   => $phrase, 
				'words'    => $word, 
				'operator' => $operator
			);
		}
	
		return $ret;
	}
} // END OF SearchUtil

?>
