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
|Authors: Ulf Wendel <ulf.wendel@phpdoc.de>                            |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * Parses user query strings like '+nixnutz -netuse +"never last"'.
 * 
 * Example:
 *
 * $s = new SearchStringParser();
 * var_dump($s->parse('nixnutz -netuse +"never last"'));
 *
 * For the given example the class returns:
 *
 * array(3) {
 *   [0]=>
 *   array(2) {
 *     ["operator"]=>
 *     string(3) "and"
 *     ["token"]=>
 *     string(7) "nixnutz"
 *   }
 *   [1]=>
 *   array(2) {
 *     ["operator"]=>
 *     string(3) "not"
 *     ["token"]=>
 *     string(6) "netuse"
 *   }
 *   [2]=>
 *   array(2) {
 *     ["operator"]=>
 *     string(3) "and"
 *     ["token"]=>
 *     string(10) "never last"
 *   }
 * }
 *
 * @package search
 */

class SearchStringParser extends PEAR
{
	/**
	 * Separator (regex) used to split not enclosed tokens
	 * 
	 * @var    string
	 * @access public
	 */
	var $seperator = '\s';

	/**
	 * Hash of operators / modifiers to recognize
	 * 
	 * @var    array 
	 * @see    SearchStringParser(), parse()
	 * @access public
	 */
	var $operators = array(
		'quote' => array(
			'"',
			"'"
		),
		'and' => array(
			'+',
			'and',
			'und'
		),
		'not' => array(
			'-',
			'not',
			'nicht'
		),
		'or' => array(
			'|',
			'or',
			'oder'
		)
	);

	/**
 	 * Search query
 	 *
	 * @var    string
	 * @access public
	 */
	var $query = '';

	/**
	 * Internal hash of tokens
	 * 
	 * @var    array
	 * @access public
	 */
	var $token = array();
  
	/**
	 * Regex used to match operators
	 * 
	 * @var    string
	 * @see    buildRegOperators()
	 * @access public
	 */
	var $reg_operators = '';
  
	/**
	 * Internal Lookup operator => operator class
	 * 
	 * @see    buildOperatorLookup
	 * @access public
	 */
	var $operators_lookup = array();
  
  
	/**
	 * Constructor
	 *
	 * @param  array
	 * @access public
	 */                    
	function SearchStringParser( $operators = array() )
	{
		if ( !empty( $operators ) ) 
			$this->operators = $operators;
    
		$this->buildRegOperators();
		$this->buildOperatorLookup();
	}
  
  
	/**
	 * Parses a given query string.
	 *
	 * @param  string  query string
	 * @param  array   hash of operators to recognize
	 * @return array   list of token: n => array( operator => , token => )    
	 * @access public
	 */
	function parse( $query, $operators = array() )
	{
		$this->query = ltrim( $query );
		
		if ( !empty( $operators ) )
		{
			$this->operators = $operators;
			$this->buildRegOperators();
			$this->buildOperatorLookup();
		}
		
		$this->token = array();
		$this->tokenize();
    
		return $this->interpret();
	}
  
	/**
	 * Tokenize the query string.
	 * 
	 * @see    $token
	 * @access public
	 */
	function tokenize()
	{
		// this one is easy: don't care on quotes strings :)
		if ( !isset( $this->operators['quote'] ) )
		{
			$this->token = split( $this->seperator, $this->query );
			return;
		}
      
		$quotes = $this->operators['quote'];
		
		if ( !is_array( $quotes ) ) 
			$quotes = array($quotes);

		// 0 => ", 1 => ' becomes " => 0, ' => 1 to be used with isset()
		$quotes   = array_flip( $quotes );
		$len      = strlen($this->query);
		$state    = '';
		$token    = '';
		$enc_sign = '';
		$last_pos = 0;
    
		for ( $i = 0; $i < $len; $i++ )
		{
      		$char = $this->query{$i};

			// split on whites 
			if ( 'enclosed' != $state && preg_match( '@' . $this->seperator . '@', $char ) )
			{
				// skip blanks
				if ( '' == ltrim( $token ) )
					continue;
          
				$this->token[] = array(
					'type'  => 'regular',
					'token' => ltrim( $token )
				);
				
				$token = '';
				$last_pos = $i;
			}
        
			$token .= $char;
      
			if ( isset( $quotes[$char] ) )
			{
				if ( '' == $state )
				{
  					$state = 'enclosed'; 
					$enc_sign = $char;
				}
				else if ( 'enclosed' == $state && $enc_sign == $char )
				{
					$this->token[] = array(
						'type'      => 'enclosed',
						'enc_sign'  => $char,
						'token'     => $token
					);
					
					$token = '';
					$state = '';
					$enc_sign = '';
					$i++;
					$last_pos = $i;
				}
			}
		}
    
		if ( $last_pos != $i )
		{
			$this->token[] = array(
				'type'  => 'regular',
				'token' => ltrim( $token )
			);
		}
	}
  
	/**
	 * Interpret the tokens.
	 * 
	 * @return array list of token: n => array( operator => , token => ) 
	 * @access public
	 */
	function interpret()
	{  
		if ( empty( $this->token ) )
			return array();

		$result   = array();
		$operator = 'and';
    
		// walk though every token and interpret      
		reset($this->token);
		while ( list( $k, $token ) = each( $this->token ) )
		{
			// extract token data
			$type     = $token['type'];
			$enc_sign = ( isset( $token['enc_sign'] ) )? $token['enc_sign'] : '';
			$token    = ltrim( $token['token'] );
      
			if ( preg_match( $this->reg_operators, $token, $regs ) )
			{
				// token starts with a modifier or standalone operator
				$operator = $this->operator_lookup[$regs[0]];
        
				// let's see if something remains after we've removed the operator 
				$token = substr($token, strlen($regs[0]));
			}
      
			// if there's something after we removed the operator it 
			// must be the search word / phrase
			if ( $token )
			{
				// remove signs used to enclose phrase
				if ( 'enclosed' == $type )
				{
					$len   = strlen( $enc_sign );
					$token = substr( $token, $len, -$len );
				}

				$result[] = array(
					'operator'  => $operator,
					'token'     => $token
				);
      
				$operator = 'and';                        
			}
		}

		return $result;    
	}
  
	/**
	 * Builds an regex matching all operators but quotes.
	 * 
	 * @see    $reg_operators, $operators
	 * @access public
	 */
	function buildRegOperators()
	{
		$this->reg_operators = '';
      
		reset( $this->operators );
		while ( list( $class, $ops ) = each( $this->operators ) )
		{
			if ( 'quote' == $class )
				continue;
        
			reset( $ops );
			while ( list( $k, $op ) = each( $ops ) )
			{
				switch ( $op )
				{
					case '+':
					
					case '*':
					
					case '|':
					
					case '@':
					
					case '(':
					
					case ')':
						$op = '\\' . $op;
						break;
				}
        
				$this->reg_operators .= sprintf( '%s|', $op );
			}
		}
    
		$this->reg_operators = sprintf('@^%s@ism', substr($this->reg_operators, 0, -1));
	}
  
	/**
	 * Builds a lookup table operator => operator class.
	 * 
	 * @see    $operator_lookup, $operators
	 * @access public
	 */
	function buildOperatorLookup()
	{
		$this->operator_lookup[] = array();
    
		reset( $this->operators );
		while ( list( $class, $ops ) = each( $this->operators ) )
		{
			if ( !is_array( $ops ) )
				$ops = array( $ops );
        
			reset( $ops );
			while ( list( $k, $op ) = each( $ops ) )
				$this->operator_lookup[$op] = $class;
		}
	}
} // END OF SearchStringParser

?>
