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
 * Parser class that parser boolean strings (item1 AND item2 AND NOT (item3 OR item4)) into
 * strings that can be used in MySQL MyIsam fulltext searches (+item1 +item2 -(item3 item4))
 * 
 * Example:
 * <code>	
 * // If you want to do one-time parsing:
 * $query  = "item1 OR item2 AND (item3 AND item4 NOT (item5 OR item6))";
 * $parser = new MySQLParser( $query );
 * echo $parser->result;
 * // This should echo "item1 item2 +(+item3 +item4 -(item5 item6 ))"
 * 
 * // I made some changes to be able to parse multiple strings, I didn't 
 * // actually need it when writing the class, so it is a bit of a late addition:
 * $queries = array(
 *		"item1 OR item2 AND (item3 AND item4 NOT (item5 OR item6))",
 *		"NOT item1 OR item2 AND (item3 OR item4)"
 * );
 *
 * foreach ( $queries as $query )
 *		echo $parser->parse( $parser->atomize( $query ) );
 * </code>
 *
 * @package db_mysql
 */

class MySQLParser extends PEAR
{
	/**
	 * The 'atomized' string used as intermediate 
	 * @var array
	 */
	var $atoms = array();

	/**
	 * Array of processed 'atoms'
	 * @var array
	 */
	var $result = array();

	/**
	 * Boolean keywords to be parsed
	 * @var array
	 */ 
	var $reserved = array(
		'or'  => '', 
		'and' => '+', 
		'not' => '-'
	);

	/**
	 * Any errors / warnings raised during processing
	 * @var array
	 */
	var $log = array();

	/**
	 * True if any errors / warnings were produced
	 * @var boolean
	 */
	var $error = false;

	
	/**
	 * Constructor
	 *
	 * @param string $query the query string to be parsed
	 */
	function MySQLParser( $query = '' )
	{
		$this->query = strtolower( $query );
		$this->atomize();
		$this->result = $this->parse( $this->atoms );
	}
	
         
	/**
	 * Write an error / warning to the array.
	 *
	 * @param string $msg
	 * @return void
	 */
	function error( $msg )
	{
		$this->log[] = $msg;
		$this->error = true;
	}
		   
	/**
	 * Parse the string into an array of 'atoms'.
	 *
	 * @return array
	 */ 		   
	function atomize()
	{
		$this->atoms = array();
		
		if ( func_num_args() )
		{
			$args = func_get_args();
			$this->query = $args[0];
		}
			
		$l = strlen( $this->query );
		$current = '';
		
		for ( $i = 0; $i <= $l; $i++ )
		{
			$char = substr( $this->query, $i, 1 );
			
			if ( $char != ' ' && $char != '(' && $char != ')' )
			{
				$result .= trim( $char );
			}
			else
			{
				if ( !empty( $result ) )
					$this->atoms[] = $result;

				$char = trim( $char );
				
				if ( !empty( $char ) )
					$this->atoms[] = $char;
                               
				$result = '';
			}
		}
		
		if ( !empty( $result ) )
			$this->atoms[] = $result;
		
		return $this->atoms;
	}
		   
	/**
	 * Parse an array of 'atoms' into a mysql compatible string.
	 *
	 * @return string
	 * @param array $atoms
	 */ 
	function parse( $atoms )
	{
		$this->error = false;
		$this->log   = array();
		$atoms = array_reverse( $atoms );
		
		// As we have learned in math, let's get rid of all parens firsts
		$results = array();
			
		do
		{
			$atom = array_pop( $atoms );
			$atom = strtolower( $atom );
			
			if ( $atom == '(' )
			{
				$parens = 0;
				$tmp = array();	
				
				while ( true )
				{
					$atom2 = array_pop( $atoms );
					$atom2 = strtolower( trim( $atom2 ) );
					
					if ( $atom2 == '(' )
					{
						$tmp[] = $atom2;
						$parens++;
						$atom2 = array_pop( $atoms );
					}
					else if ( $atom2 == ')' )
					{
						$parens--;
						$atom2 = array_pop( $atoms );
					}
							
					if ( $atom2 == ')' && $parens <= 0 )
					{
						$results[] = '(' .  $this->parse( $tmp ) . ')';
						break;		
					}
					
					$tmp[] = trim( $atom2 );
					
					if ( count( $atoms ) <= 0 )
					{
						$results[] = '(' .  trim( $this->parse( $tmp ) ) . ')' ;
						break;
					}
				}
			}
			else
			{
				$results[] = $atom;
			}
			
			if ( count( $atoms ) <= 0 )
				break;
		} while ( true );
		
		// If I am not mistaken we now have a nice uniform array where all elements are of equal value
		$tmp = array_reverse( $results );
		$results = array();
			
		while ( count( $tmp ) )
		{
			$a = array_pop( $tmp );
			$b = array_pop( $tmp );
			$c = array_pop( $tmp );
			
			if ( !$a )
				break;
			
			$aistoken = array_key_exists( $a, $this->reserved );
			$bistoken = array_key_exists( $b, $this->reserved ) << 1;
			$cistoken = array_key_exists( $c, $this->reserved ) << 2;
			$sw = $aistoken |  $bistoken | $cistoken;
					
			switch ( $sw )
			{
				case 0: // There were no tokens in any of the elements, all are therefore OR
					$results[] = $a;
					$results[] = $b;
					$results[] = $c;
					
					break;
				
				case 1: // $a is a token, this 'should' not happen but whatever, 
					// $b gets the sign of $a and $c goes back since we know nothing about it 
					$results[] = $this->reserved[$a] . $b;	
					array_push( $tmp, $c );
					
					break ;
				
				case 2: // $b is a sign, both $a and $c get the sign of $b and were done
					$results[] = $this->reserved[$b] . $a;
					$results[] = $this->reserved[$b] . $c;
					
					break;
					
				case 3: // $a and $b are tokens, this is either an error or we're dealing with a AND/OR NOT clause
					if ( $a == $b )	
					{
						// $a and $b are identical, this is an error
						$results[] = $this->reserved[$b] . $c;
						$a = trim( $a );
						$this->error( "Encountered $a twice in a row, I'm assuming once was meant." );
						
						break;
					}
					
					// If we have AND and OR consecutively it is an error and we'll go with OR
					if ( ( $a == 'or' && $b == 'and' ) || ( $a == 'and' && $b == 'or' ) )
					{
						$results[] = $this->reserved['or'] . $c;
						$this->error( "AND and OR are conflicting, I'm going with OR." );
						
						break;
					}
					
					// Every thing else is a AND/OR NOT clause, so we just need the NOT sign 
					// anything else should be apparent
					$results[] = $this->reserved['not'] . $c;
					break;
					
				case 4: // $c is a token, this says nothing on $a or $b so apparently they are OR
					$results[] = $this->reserved['or'] . $a;
					$results[] = $this->reserved['or'] . $b;
					array_push( $tmp, $c );
					
					break;
				
				case 5: // $a and $c are tokens, $b gets the sign for $a and $c goes back because we might need it later
					$results[] = $this->reserved[$a] . $b;
					array_push( $tmp, $c );
					
					break;
					
				case 6:	// $b and $c are tokens, for $a only $b matters but depending on context $b might be needed later 
					// so $a gets sign $b, $b and $c go back
					$results[] = $this->reserved[$b] . $a;
					array_push( $tmp, $b );
					array_push( $tmp, $c );
					
					break;
					
				case 7: // $a, $b and $c are all tokens, this is ALWAYS an error, but we give it a try because we're nice guys
					// if $a == $b == $c we'll asume only one was meant and return that
					// if $a == $b != $c we'll turn $a and $c into one and return that and $c
					// if $a != $b == $c we'll turn $b and $c into one and return that and $a
					// if $a != $b != $c we're hopelessly lost 
					if ( $a == $b && $b == $c )	
					{
						array_push( $tmp, $a );
					}
					else if ( ( $a == $b && $b != $c ) || $b == $c )
					{
						array_push( $tmp, $a );
						array_push( $tmp, $c );
					}
					
					$a = trim( $a );
					$b = trim( $b );
					$c = trim( $c );
					
					$this->error( "I have no way to parse \"$a $b $c\"." );
					
					break;
				
				default:
					break;	
			}
		}
		
		$this->results = $results;
		return implode( $results, ' ' );
	}
} // END OF MySQLParser

?>
