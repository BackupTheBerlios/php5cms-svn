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
 * Search Criteria Parser
 *
 * Description:
 * 	Parses search criteria according to the following rules
 *	E -> E and T
 *	E -> E or T
 *	E -> T
 *	T -> not F
 *	T -> F
 *	F -> Id
 *	F -> (E)
 *
 * Processes search criteria as lowercase.
 * 'sandwich' is ok, 'oracle' is ok, 'lotus notes' is ok, but not 'or', 'and' or 'not' by itself.
 * No keyword or keyword phrase can contain '(' or ')'
 *
 * Example:
 *	$scp = new SearchCriteriaParser;
 *	$searchcriteria = "chef and (2nd or Second) and not (wellington or south island)";
 *	$tokens = $scp->gettokens( $searchcriteria );
 *
 *	for ( $i = 0; $i < sizeof( $tokens ); $i++ )
 *		echo "<br>" . $tokens[$i];
 *
 * $complieswithrules = $scp->checkwithrules( $tokens );
 *
 *	if ( $complieswithrules == true )
 *		echo "<br>SUCCESS: complies with the rules";
 * else
 *		echo "<br>ERROR: does not comply with the rules";
 *
 * @package search
 */
 
class SearchCriteriaParser extends PEAR
{
	/**
	 * @access public
	 */		
	function gettokens( $str ) 
	{
		// put the string into lowercase
		$str = strtolower( $str );

		// make sure ( or ) get picked up as separate tokens
		$str = str_replace( "(", " ( ", $str );
		$str = str_replace( ")", " ) ", $str );

		// get the actual tokens
		$actualtokens = explode( " ", $str );

		// trim spaces around tokens and discard those which have only spaces in them
		$h = 0;
	
		for ( $i = 0; $i < sizeof( $actualtokens ); $i++ ) 
		{
			$actualtokens[$i] = trim( $actualtokens[$i] );
		
			if ( $actualtokens[$i] != "" )
				$nospacetokens[$h++] = $actualtokens[$i];
		}

		// now put together tokens which are actually one token e.g. upper hutt
		$onetoken = "";
		$h = 0;
	
		for ( $i = 0; $i < sizeof( $nospacetokens ); $i++ ) 
		{
			$token = $nospacetokens[$i];
		
			switch ( $token ) 
			{
				case ")":

				case "(":
	
				case "and":

				case "or":

				case "not":
					if ( $onetoken != "" ) 
					{
						$tokens[$h++] = $onetoken;
						$onetoken = "";
					}
				
					$tokens[$h++] = $token;
					break;

				default:
					if ( $onetoken == "" )
						$onetoken = $token;
					else
						$onetoken = $onetoken . " " . $token;
				
					break;
			}
		}
	
		if ( $onetoken != "" ) 
		{
			$tokens[$h++] = $onetoken;
			$onetoken = "";
		}
	
		return $tokens;
	}

	/**
	 * @access public
	 */	
	function checkwithrules( $tokens ) 
	{
		$rhs   = array( "E and T", "E or T", "T", "not F", "F", "Id", "( E )" );
		$lhs   = array( "E", "E", "E", "T", "T", "F", "F" );
		$i     = 0;
		$stack = "";
	
		while ( $i < sizeof( $tokens ) ) 
		{
			$token = $tokens[$i];
		
			switch ( $token ) 
			{
				case "and":
			
				case "or":
			
				case "not":
			
				case "(":
			
				case ")":
					if ( $stack == "" )
						$stack = $token;
					else
						$stack = $stack . " " . $token;
				
					// go through the rules
					$j = 0;
					while ( $j < sizeof( $rhs ) ) 
					{
						$len = strlen( $rhs[$j] );
						$lenstack = strlen( $stack );
					
						if ( $lenstack < $len ) 
						{
							$j++;
							continue;
						}
					
						$str = substr( $stack, $lenstack - $len, $len );
						// echo "<br>stack=" . $stack . ",str=" . $str . ",rhs[j]=" . $rhs[$j];
					
						if ( $str == $rhs[$j] ) 
						{
						    $stack = substr( $stack, 0, $lenstack - $len );
							$stack = $stack . $lhs[$j];
						
							$j = 0;
						}
						else 
						{
							$j++;
						}
					}
				
					break;

				default:
					if ( $stack == "" )
						$stack = "Id";
					else
						$stack = $stack . " " . "Id";
				
					// go through the rules
					$j = 0;
					while ( $j < sizeof( $rhs ) ) 
					{
						$len = strlen( $rhs[$j] );
						$lenstack = strlen( $stack );
						
						if ( $lenstack < $len ) 
						{
							$j++;
							continue;
						}
					
						$str = substr( $stack, $lenstack - $len, $len );
						// echo "<br>stack=" . $stack . ",str=" . $str . ",rhs[j]=" . $rhs[$j];
						
						if ( $str == $rhs[$j] ) 
						{
							$stack = substr( $stack, 0, $lenstack - $len );
							$stack = $stack . $lhs[$j];
						
							$j = 0;
						}
						else 
						{
							$j++;
						}
					}
				
					break;
			}

			$i++;
		}
	
		// echo "<br>Stack = '" . $stack . "'";
	
		if ( $stack != "E" )
			return false;

		return true;
	}
} // END OF SearchCriteriaParser

?>
