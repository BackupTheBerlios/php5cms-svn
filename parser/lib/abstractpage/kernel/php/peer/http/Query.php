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
 * @package peer_http
 */
 
class Query extends PEAR
{
	/**
	 * Add values to query string.
	 * This function allows to form query string with a values added to it from current query string  
	 *
	 * Example:
	 * we call query.php?var=val, then in query.php: 
	 * <a href="<?php echo addToQuery( array( "var2" => "val2, "var3" => "val3" ) ) ?>">add val2 and val3</a>
	 *
	 * @access public
	 */
	function addToQuery( $q_arr )
	{ 
		$str = $_SERVER["PHP_SELF"] . "?"; 
    	reset( $q_arr ); 
    
		while ( list( $k, $v ) = each( $q_arr ) )
			$str .= "$k=$v&"; 
    
    	reset( $_GET );
    	while ( list( $k, $v ) = each( $_GET ) )
		{ 
    		if ( isset( $q_arr[$k] ) )
				continue; 
    	
			$str .= "$k=$v&"; 
    	}
	
    	return $str; 
	}
	
	/**
	 * Function to get all variables sended on any query and output this an array.
	 *
	 * @access public
	 */
	function getQueryVars( $query, &$vars, $division )
	{
		parse_str( $query, $urlvars );
		reset( $urlvars ); 
 		$vars = array(); 

		while ( list( $a, $b ) = each( $urlvars ) )
		{ 
			if ( $division == true )
			{ 
				for ( $b .= " ", $i = 0, $tmp = ""; $i <= strlen( $b ); $i++ )
				{ 
            	    $segment = $b[$i];
				 
            	    if ( $segment == " " )
					{ 
                    	if ( strlen( trim( $tmp ) ) )
							$vars[$a][] = $tmp; 
                    
						$tmp = ""; 
					}
					else
					{ 
                    	$tmp .= $segment; 
                	}
            	}
        	}
			else if ( strlen( trim( $b ) ) )
			{
				$vars[$a] = $b;
			}
	    }
	
    	return $vars; 
	}
	
	/**
	 * @access public
	 */
	function getTranslatedPath( $which = 1000, $relative = "" )
	{
		$sep     = "/";
		$pt      = $_SERVER['PATH_TRANSLATED'];
		$arrpath = explode( "/", $pt );
		$pathct  = count( $arrpath );
		
		// $which
		if ( $which >= $pathct )
			$which = $pathct - 2;
		else if ( $which < 0 )
			$which = $pathct - 1 + $which ;
		else if ( $which == 1000 )
			$which = $pathct - 2;

		// $relative
		if ( $relative == 1 )
		{
			$path = $arrpath[$which] . $sep;
		}
		else if ( $relative == 0 )
		{
			for ( $r = 0 ;$r <= $which ; ++$r )
				$path .= $arrpath[$r] . $sep;
		}

		return $path;
	}
	
	/**
	 * @access public
	 */
	function getQueryVar( $item )
	{ 
		$pairs = explode( "&", $_SERVER["QUERY_STRING"] ); 

		for ( $i = 0; $i < count( $pairs ); $i++ )
		{
			$query[$i] = explode( "=", $pairs[$i] ); 
			
			if ( $query[$i][0] == $item )
			{
				return $query[$i][1]; 
				break;
			}
		}
	}
} // END OF Query

?>
