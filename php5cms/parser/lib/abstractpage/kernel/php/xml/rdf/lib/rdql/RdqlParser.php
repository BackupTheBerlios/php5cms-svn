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
|Authors: Radoslaw Oldakowski <radol@gmx.de>                           |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


define( "RDQLPARSER_SEL_ERR", "RDQL error in the SELECT clause: " );
define( "RDQLPARSER_SRC_ERR", "RDQL error in the SOURCE clause: " );
define( "RDQLPARSER_WHR_ERR", "RDQL error in the WHERE clause: "  );
define( "RDQLPARSER_USG_ERR", "RDQL error in the USING clause: "  );
define( "RDQLPARSER_AND_ERR", "RDQL error in the AND clause: "    );


/**
 * This class contains methods for parsing an Rdql query string into PHP variables.
 * The output of the RdqlParser is an array with variables and constraints
 * of each query clause (Select, From, Where, And, Using).
 * To perform an RDQL query this array has to be passed to the RdqlEngine.
 *
 * @package xml_rdf_lib_rdql
 */

class RdqlParser extends PEAR
{
	/**
	 * Parsed query variables and constraints.
	 *
	 * @var     array   ['selectVars'][] = ?VARNAME
	 *                  ['sources'][] = URI
	 *                  ['patterns'][]['subject']['value'] = VARorURI
	 *                                ['predicate']['value'] = VARorURI
	 *                                ['object']['value'] = VARorURIorLiterl
	 *                                          ['is_literal'] = boolean
	 *                                          ['l_lang'] = string
	 *                                          ['l_dtype'] = string
	 *                  ['filters'][]['string'] = string
	 *                               ['evalFilterStr'] = string
	 *                               ['reqexEqExprs'][]['var'] = ?VARNAME
	 *                                                 ['operator'] = (eq | ne)
	 *                                                 ['regex'] = string
	 *                               ['strEqExprs'][]['var'] = ?VARNAME
	 *                                               ['operator'] = (eq | ne)
	 *                                               ['value'] = string
	 *                                               ['value_type'] = ('variable' | 'URI' | 'Literal')
	 *                                               ['value_lang'] = string
	 *                                               ['value_dtype'] = string
	 *                               ['numExpr']['vars'][] = ?VARNAME
	 *                  ['ns'][PREFIX] = NAMESPACE
	 *                         ( [] stands for an integer index - 0..N )
	 * @access	private
	 */
 	var $parsedQuery;

	/**
	 * Query string divided into a sequence of tokens.
	 * A token is either: ' ' or "\n" or "\r" or "\t" or ',' or '(' or ')'
	 * or a string containing any characters except from the above.
	 *
	 * @var     array
	 * @access	private
	 */
 	var $tokens;
	
	
	/**
	 * Parse the given RDQL query string and return an array with query variables and constraints.
	 *
	 * @param   string  $queryString
	 * @return  array   $this->parsedQuery
	 * @access	public
	 */
 	function &parseQuery( $queryString )
	{
		$cleanQueryString = $this->removeComments( $queryString );
		$this->tokenize( $cleanQueryString );
		$this->startParsing();
		
		if ( $this->parsedQuery['selectVars'][0] == '*' )
			$this->parsedQuery['selectVars'] = $this->findAllQueryVariables();
		else
			$this->_checkSelectVars();

		$this->replaceNamespacePrefixes();
		return $this->parsedQuery;
	}

	/**
	 *  Remove comments from the passed query string.
	 *
	 *  @param  string  $query
	 *  @return string
	 *  @throws PHPError
	 *  @access private
	 */
	function removeComments( $query )
	{
		$last   = strlen( $query ) - 1;
		$query .= ' ';
		$clean  = '';
		
		for ( $i = 0; $i <= $last; $i++ )
		{
			// don't search for comments inside a 'literal'@lang^^dtype or "literal"@lang^^dtype
			if ( $query{$i} == "'" || $query{$i} == '"' )
			{
				$quotMark = $query{$i};
				
				do
				{
					$clean .= $query{$i++};
				} while ( $i < $last && $query{$i} != $quotMark );
				
				$clean .= $query{$i};
				
				// language
				if ( $query{$i + 1} == '@' )
				{
					do
					{
						if ( $query{$i + 1} == '^' && $query{$i + 2} == '^' )
							break;
						
						$clean .= $query{++$i};
					} while ( $i < $last && $query{$i} != ' '  && $query{$i} != "\t" && $query{$i} != "\n" && $query{$i} != "\r" );
				}
				
				// datatype
				if ( $query{$i + 1} == '^' && $query{$i + 2} == '^' ) 
				{
					do
					{
						$clean .= $query{++$i};
					} while ( $i < $last && $query{$i} != ' '  && $query{$i} != "\t" && $query{$i} != "\n" && $query{$i} != "\r" );
				}
			}
			else if ( $query{$i} == '<' ) 
			{
				do
				{
					$clean .= $query{$i++};
				} while ( $i < $last && $query{$i} != '>' );
				
				$clean .= $query{$i};
			}
			else if ( $query{$i} == '/' ) 
			{
				// clear: // comment
				if ( $i < $last && $query{$i + 1} == '/' ) 
				{
					while ( $i < $last && $query{$i} != "\n" && $query{$i} != "\r" )
						++$i;
					
					$clean .= ' ';
					// clear: /*comment*/
				}
				else if ( $i < $last-2 && $query{$i + 1} == '*' )
				{
					$i += 2;
					
					while ( $i < $last  && ($query{$i} != '*' || $query{$i + 1} != '/' ) )
						++$i;
					
					if ( $i >= $last && ( $query{$last - 1} != '*' || $query{$last} != '/' ) )
						trigger_error( "RDQL error: unterminated comment - '*/' missing", E_USER_ERROR );
					
					++$i;
				}
				else
				{
					$clean .= $query{$i};
				}
			}
			else
			{
				$clean .= $query{$i};
			}
		}
		
		return $clean;
	}

	/**
	 * Divide the query string into tokens.
	 * A token is either: ' ' or "\n" or "\r" or '\t' or ',' or '(' or ')'
	 * or a string containing any character except from the above.
	 *
	 * @param   string  $queryString
	 * @access	private
	 */
	function tokenize( $queryString )
	{
		$queryString  = trim( $queryString, " \r\n\t" );
		$specialChars = array( " ", "\t", "\r", "\n", ",", "(", ")" );
		$len = strlen( $queryString );
		$this->tokens[0] = '';
		$n = 0;

		for ( $i = 0; $i < $len; ++$i )
		{
			if ( !in_array( $queryString{$i}, $specialChars ) )
			{
				$this->tokens[$n] .= $queryString{$i};
			}
			else
			{
				if ( $this->tokens[$n] != '' )
					++$n;
				
				$this->tokens[$n] = $queryString{$i};
				$this->tokens[++$n] = '';
			}
		}
	}

	/**
	 * Start parsing of the tokenized query string.
	 *
	 * @access private
	 */
	function startParsing()
	{
		$this->parseSelect();
	}

	/**
	 * Parse the SELECT clause of an Rdql query.
	 * When the parsing of the SELECT clause is finished, this method will call
	 * a suitable method to parse the subsequent clause.
	 *
	 * @throws	Error
	 * @access	private
	 */
	function parseSelect()
	{
		$this->_clearWhiteSpaces();

		// Check if the queryString contains a "SELECT" token
		if ( strcasecmp( 'SELECT', current( $this->tokens ) ) )
			trigger_error( RDQLPARSER_SEL_ERR . "'" . current( $this->tokens ) . "' - SELECT keyword expected", E_USER_ERROR );
		
		unset( $this->tokens[key( $this->tokens )] );
		$this->_clearWhiteSpaces();

		// Parse SELECT *
		if ( current( $this->tokens ) == '*' )
		{
			unset( $this->tokens[key( $this->tokens )] );
			$this->parsedQuery['selectVars'][0] = '*';
			$this->_clearWhiteSpaces();
			
			if ( strcasecmp( 'FROM', current( $this->tokens ) ) && strcasecmp( 'SOURCE', current( $this->tokens ) ) && strcasecmp( 'WHERE', current( $this->tokens ) ) )
				trigger_error( "RDQL error: '" . htmlspecialchars( current( $this->tokens ) ) . "' - SOURCE or WHERE clause expected", E_USER_ERROR );
		}

		// Parse SELECT ?Var (, ?Var)*
		$commaExpected = false;
		$comma = false;
		
		while ( current( $this->tokens ) != null )
		{
			$k = key($this->tokens);
			$token = $this->tokens[$k];

			switch ( $token )
			{
				case ',':
					if ( !$commaExpected )
						trigger_error( RDQLPARSER_SEL_ERR . " ',' - unexpected comma", E_USER_ERROR );
						
					$comma = true;
					$commaExpected = false;
					
					break;
				
				case '(':
				
				case ')': 
					trigger_error( RDQLPARSER_SEL_ERR . " '$token' - illegal input", E_USER_ERROR );
					break;
				
				default :
					if ( !strcasecmp( 'FROM', $token ) || !strcasecmp( 'SOURCE', $token ) )
					{
						if ( $comma )
							trigger_error( RDQLPARSER_SEL_ERR . " ',' - unexpected comma", E_USER_ERROR );
						
						unset( $this->tokens[$k] );
						return $this->parseFrom();
					}
					else if ( !strcasecmp( 'WHERE', $token ) && !$comma )
					{
						if ( $comma )
							trigger_error( RDQLPARSER_SEL_ERR . " ',' - unexpected comma", E_USER_ERROR );
						
						unset( $this->tokens[$k] );
						return $this->parseWhere();
					}
					
					if ( $token{0} == '?' )
					{
						$this->parsedQuery['selectVars'][] = $this->_validateVar( $token, 'SELECT' );
						$commaExpected = true;
						$comma = false;
					}
					else
					{
						trigger_error( RDQLPARSER_SEL_ERR ." '$token' - '?' missing", E_USER_ERROR );
					}
			}
				
			unset( $this->tokens[$k] );
			$this->_clearWhiteSpaces();
		}
			
		trigger_error( RDQL_PARSE_ERROR . ": WHERE clause missing", E_USER_ERROR );
 	}

	/**
	 * Parse the FROM/SOURCES clause of an Rdql query
	 * When the parsing of this clause is finished, parseWhere() will be called.
	 *
	 * @throws	Error
	 * @access	private
	 */
	function parseFrom()
	{
		$comma = false;
		$commaExpected = false;
		
		while ( current( $this->tokens ) != null )
		{
			$this->_clearWhiteSpaces();
			
			if ( !strcasecmp( 'WHERE', current( $this->tokens ) ) && count( $this->parsedQuery['sources'] ) != 0 )
			{
				if ( $comma )
					trigger_error( RDQLPARSER_SEL_ERR ." ',' - unexpected comma", E_USER_ERROR );
					
				unset( $this->tokens[key( $this->tokens )] );
				return $this->parseWhere();
			}
			
			if ( current( $this->tokens ) == ',' )
			{
				if ( $commaExpected )
				{
					$comma = true;
					$commaExpected = false;
					
					unset( $this->tokens[key( $this->tokens )] );
				}
				else
				{
					trigger_error( RDQLPARSER_SRC_ERR . "',' - unecpected comma", E_USER_ERROR );
				}
			}
			else
			{
				$token = current( $this->tokens );
				$this->parsedQuery['sources'][] = $this->_validateURI( $token, 'FROM' );
				$commaExpected = true;
				$comma = false;
			}
		}
		
		trigger_error( "RDQL error: WHERE clause missing", E_USER_ERROR );
	}

	/**
	 * Parse the WHERE clause of an Rdql query.
	 * When the parsing of the WHERE clause is finished, this method will call
	 * a suitable method to parse the subsequent clause if provided.
	 *
	 * @throws	Error
	 * @access	private
	 */
	function parseWhere()
	{
		$comma = false;
		$commaExpected = false;
		$i = 0;

		do
		{
			$this->_clearWhiteSpaces();
			
			if ( !strcasecmp( 'AND', current( $this->tokens ) ) && count( $this->parsedQuery['patterns'] ) != 0 )
			{
				if ( $comma )
					trigger_error( RDQLPARSER_WHR_ERR ." ',' - unexpected comma", E_USER_ERROR );
				
				unset( $this->tokens[key( $this->tokens )] );
				return $this->parseAnd();
			}
			else if ( !strcasecmp( 'USING', current( $this->tokens ) ) && count( $this->parsedQuery['patterns'] ) != 0 )
			{
				if ( $comma )
					trigger_error( RDQLPARSER_WHR_ERR ." ',' - unexpected comma", E_USER_ERROR );
				
				unset( $this->tokens[key( $this->tokens )] );
				return $this->parseUsing();
			}

			if ( current( $this->tokens) == ',' )
			{
				$comma = true;
				$this->_checkComma( $commaExpected, 'WHERE' );
			}
			else
			{
				if ( current( $this->tokens ) != '(' )
					trigger_error( RDQLPARSER_WHR_ERR . "'"  . current( $this->tokens ) . "' - '(' expected", E_USER_ERROR );
				
				unset( $this->tokens[key( $this->tokens )] );
				$this->_clearWhiteSpaces();

				$this->parsedQuery['patterns'][$i]['subject'] = $this->_validateVarUri( current( $this->tokens ) );
				$this->_checkComma( true, 'WHERE' );
				$this->parsedQuery['patterns'][$i]['predicate'] = $this->_validateVarUri( current( $this->tokens ) );
				$this->_checkComma( true, 'WHERE' );
				$this->parsedQuery['patterns'][$i++]['object'] = $this->_validateVarUriLiteral( current( $this->tokens ) );
				$this->_clearWhiteSpaces();

				if ( current( $this->tokens ) != ')' )
					trigger_error( RDQLPARSER_WHR_ERR . "'"  . current( $this->tokens ) . "' - ')' expected", E_USER_ERROR );
				
				unset( $this->tokens[key( $this->tokens )] );
				$this->_clearWhiteSpaces();
				$commaExpected = true;
				$comma = false;
			}
		} while ( current( $this->tokens ) != null );

		if ( $comma )
			trigger_error( RDQLPARSER_WHR_ERR . " ',' - unexpected comma", E_USER_ERROR );
	}

	/**
	 * Parse the AND clause of an Rdql query.
	 *
	 * @throws	Error
	 * @access	private
	 * @toDo clear comments
	 */
	function parseAnd()
	{
		$this->_clearWhiteSpaces();
		$n = 0;
		$filterStr = '';

		while ( current( $this->tokens ) != null )
		{
			$k = key( $this->tokens );
			$token = $this->tokens[$k];

			if ( !strcasecmp( 'USING', $token ) )
			{
				$this->parseFilter( $n, $filterStr );
				unset( $this->tokens[$k] );
				
				return $this->parseUsing();
			}
			else if ( $token == ',' )
			{
				$this->parseFilter( $n, $filterStr );
				$filterStr = '';
				$token = '';
				++$n;
			}
			
			$filterStr .= $token;
			unset( $this->tokens[$k] );
		}
		
		$this->parseFilter( $n, $filterStr );
	}
 
	/**
	 * Parse the USING clause of an Rdql query
	 *
	 * @throws	Error
	 * @access	private
	 */
	function parseUsing()
	{
		$commaExpected = false;
		$comma = false;

		do
		{
			$this->_clearWhiteSpaces();
			
			if ( current( $this->tokens ) == ',' )
			{
				$comma = true;
				$this->_checkComma( $commaExpected, 'USING' );
			}
			else
			{
				$prefix = $this->_validatePrefix( current( $this->tokens ) );
				$this->_clearWhiteSpaces();

				if ( strcasecmp( 'FOR', current( $this->tokens ) ) )
					trigger_error( RDQLPARSER_USG_ERROR . " keyword: 'FOR' missing in the namespace declaration: '", E_USER_ERROR );
				
				unset( $this->tokens[key( $this->tokens )] );
				$this->_clearWhiteSpaces();
				$this->parsedQuery['ns'][$prefix] = $this->_validateUri( current( $this->tokens ), 'USING' );
				$this->_clearWhiteSpaces();
		
				$commaExpected = true;
				$comma = false;
			}
		} while ( current( $this->tokens ) != null );

		if ( $comma )
			trigger_error( RDQLPARSER_WHR_ERR . " ',' - unexpected comma", E_USER_ERROR );
	}

	/**
	 * Check if a filter from the AND clause contains an equal number of '(' and ')'
	 * and parse filter expressions.
	 *
	 * @param   integer $n
	 * @param   string  $filter
	 * @throws  PHPError
	 * @access	private
	 */
	function parseFilter( $n, $filter )
	{
		if ( $filter == null )
			trigger_error( RDQLPARSER_AND_ERR . " ',' - unexpected comma", E_USER_ERROR );
			
		$paren = substr_count( $filter, '(') - substr_count( $filter, ')' );
		
		if ( $paren != 0 )
		{
			if ( $paren > 0 )
				$errorMsg = "'" . htmlspecialchars( $filter ) . "' - ')' missing ";
			else if ( $paren < 0 )
				$errorMsg = "'" . htmlspecialchars( $filter ) . "' - too many ')' ";
			
			trigger_error( RDQLPARSER_AND_ERR . $errorMsg, E_USER_ERROR );
		}

		$this->parsedQuery['filters'][$n] = $this->parseExpressions( $filter );
	}

	/**
	 * Parse expressions inside the passed filter:
	 * 1)  regex equality expressions:    ?var [~~ | =~ | !~ ] REG_EX
	 * 2a) string equality expressions:   ?var  [eq | ne] "literal"@lang^^dtype.
	 * 2b) string equality expressions:   ?var [eq | ne] <URI>
	 * 3)  numerical expressions: e.q.    (?var1 - ?var2)*4 >= 20
	 *
	 * In cases 1-2 parse each expression of the given filter into an array of variables.
	 * For each parsed expression put a place holder (e.g. ##RegEx_1##) into the filterStr.
	 * The RDQLengine will then replace each place holder with the outcomming boolean value
	 * of the corresponding expression.
	 * The remaining filterStr contains only numerical expressions and place holders.
	 *
	 * @param   string  $filteStr
	 * @return  array   ['string'] = string
	 *                  ['evalFilterStr'] = string
	 *                  ['reqexEqExprs'][]['var'] = ?VARNAME
	 *                                    ['operator'] = (eq | ne)
	 *                                    ['regex'] = string
	 *                  ['strEqExprs'][]['var'] = ?VARNAME
	 *                                 ['operator'] = (eq | ne)
	 *                                 ['value'] = string
	 *                                 ['value_type'] = ('variable' | 'URI' | 'Literal')
	 *                                 ['value_lang'] = string
	 *                                 ['value_dtype'] = string
	 *                  ['numExpr']['vars'][] = ?VARNAME
	 * @access	private
	 */
	function parseExpressions( $filterStr )
	{
		$parsedFilter['string']       = $filterStr;
		$parsedFilter['regexEqExprs'] = array();
		$parsedFilter['strEqExprs']   = array();
		$parsedFilter['numExprVars']  = array();

		// parse regex string equality expressions, e.g. ?x ~~ !//foo.com/r!i
		$reg_ex  = "/(\?[a-zA-Z0-9_]+)\s+([~!=]~)\s+(['|\"])?([^\s'\"]+)(['|\"])?/";
		preg_match_all( $reg_ex, $filterStr, $eqExprs );
		
		foreach ( $eqExprs[0] as $i => $eqExpr )
		{
			$this->_checkRegExQuotation( $filterStr, $eqExprs[3][$i], $eqExprs[5][$i] );
			$parsedFilter['regexEqExprs'][$i]['var']      = $this->_isDefined( $eqExprs[1][$i] );
			$parsedFilter['regexEqExprs'][$i]['operator'] = $eqExprs[2][$i];
			$parsedFilter['regexEqExprs'][$i]['regex']    = $eqExprs[4][$i];
			$filterStr = str_replace( $eqExpr, " ##RegEx_$i## ", $filterStr );
		}

		// parse ?var  [eq | ne] "literal"@lang^^dtype
		$reg_ex  = "/(\?[a-zA-Z0-9_]+)\s+(eq|ne)\s+(\'[^\']*\'|\"[^\"]*\")";
		$reg_ex .= "(@[a-zA-Z]+)?(\^{2}\S+:?\S+)?/i";
		preg_match_all( $reg_ex, $filterStr, $eqExprs );
		
		foreach ( $eqExprs[0] as $i => $eqExpr )
		{
			$parsedFilter['strEqExprs'][$i]['var']         = $this->_isDefined( $eqExprs[1][$i] );
			$parsedFilter['strEqExprs'][$i]['operator']    = strtolower( $eqExprs[2][$i] );
			$parsedFilter['strEqExprs'][$i]['value']       = trim( $eqExprs[3][$i], "'\"" );
			$parsedFilter['strEqExprs'][$i]['value_type']  = 'Literal';
			$parsedFilter['strEqExprs'][$i]['value_lang']  = substr( $eqExprs[4][$i], 1 );
			$parsedFilter['strEqExprs'][$i]['value_dtype'] = substr( $eqExprs[5][$i], 2 );

			$filterStr = str_replace( $eqExprs[0][$i], " ##strEqExpr_$i## ", $filterStr );
		}

		// parse ?var [eq | ne] <URI>
		$ii = count( $parsedFilter['strEqExprs'] );
		$reg_ex = "/(\?[a-zA-Z0-9_]+)\s+(eq|ne)\s+(<\S+>)/i";
		preg_match_all( $reg_ex, $filterStr, $eqExprs );
		
		foreach ( $eqExprs[0] as $i => $eqExpr )
		{
			$parsedFilter['strEqExprs'][$ii]['var']        = $this->_isDefined( $eqExprs[1][$i] );
			$parsedFilter['strEqExprs'][$ii]['operator']   = strtolower( $eqExprs[2][$i] );
			$parsedFilter['strEqExprs'][$ii]['value']      = trim( $eqExprs[3][$i], "<>" );
			$parsedFilter['strEqExprs'][$ii]['value_type'] = 'URI';

			$filterStr = str_replace( $eqExprs[0][$i], " ##strEqExpr_$ii## ", $filterStr );
			$ii++;
		}

		// parse ?var [eq | ne] ?var
		$reg_ex = "/(\?[a-zA-Z0-9_]+)\s+(eq|ne)\s+(\?[a-zA-Z0-9_]+)/i";
		preg_match_all( $reg_ex, $filterStr, $eqExprs );
		
		foreach ( $eqExprs[0] as $i => $eqExpr )
		{
			$parsedFilter['strEqExprs'][$ii]['var']        = $this->_isDefined( $eqExprs[1][$i] );
			$parsedFilter['strEqExprs'][$ii]['operator']   = strtolower( $eqExprs[2][$i] );
			$parsedFilter['strEqExprs'][$ii]['value']      = $this->_isDefined( $eqExprs[3][$i] );
			$parsedFilter['strEqExprs'][$ii]['value_type'] = 'variable';

			$filterStr = str_replace( $eqExprs[0][$i], " ##strEqExpr_$ii## ", $filterStr );
			$ii++;
		}

		$parsedFilter['evalFilterStr'] = $filterStr;

		// all that is left are numerical expressions and place holders for the above expressions
		preg_match_all( "/\?[a-zA-Z0-9_]+/", $filterStr, $vars );
		
		foreach ( $vars[0] as $var )
			$parsedFilter['numExprVars'][] = $this->_isDefined( $var );

		return $parsedFilter;
	}

	/**
	 * Find all query variables used in the WHERE clause.
	 *
	 * @return  array [] = ?VARNAME
	 * @access	private
	 */
	function findAllQueryVariables()
	{
		$vars = array();
		
		foreach ( $this->parsedQuery['patterns'] as $pattern )
		{
			$count = 0;
			
			foreach ( $pattern as $v )
			{
				if ( $v['value']{0} == '?' ) 
				{
					++$count;
					
					if ( !in_array( $v['value'], $vars ) )
						$vars[] = $v['value'];
				}
			}
			
			if ( !$count )
				trigger_error( RDQLPARSER_WHR_ERR . "pattern contains no variables", E_USER_ERROR );
		}

		return $vars;
	}

	/**
	 * Replace all namespace prefixes in the pattern and constraint clause of an rdql query
	 * with the namespaces declared in the USING clause and default namespaces.
	 *
	 * @access	private
	 */
	function replaceNamespacePrefixes()
	{
		$default_prefixes = array(
			'rdf'  => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
			'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#',
			'xsd'  => 'http://www.w3.org/2001/XMLSchema#'
		);

		if ( !isset($this->parsedQuery['ns'] ) )
			$this->parsedQuery['ns'] = array();

		// add default namespaces
		// if in an rdql query a reserved prefix (e.g. rdf: rdfs:) is used
		// it will be overridden by the default namespace defined in constants.php
		$this->parsedQuery['ns'] = array_merge( $this->parsedQuery['ns'], $default_prefixes );

		// replace namespace prefixes in the where clause
		foreach ( $this->parsedQuery['patterns'] as $n => $pattern )
		{
			foreach ( $pattern as $key => $v )
			{
				if ( $v['value']{0} != '?' )
				{
					foreach ( $this->parsedQuery['ns'] as $prefix => $uri ) 
					{
						if ( isset( $this->parsedQuery['patterns'][$n][$key]['is_literal'] ) )
							$this->parsedQuery['patterns'][$n][$key]['l_dtype'] = eregi_replace( "$prefix:", $uri, $this->parsedQuery['patterns'][$n][$key]['l_dtype'] );
						else
							$this->parsedQuery['patterns'][$n][$key]['value'] = eregi_replace( "$prefix:", $uri, $this->parsedQuery['patterns'][$n][$key]['value'] );
					}
				}
			}
		}

		// replace prefixes in the constraint clause
		if ( isset( $this->parsedQuery['filters'] ) )
		{
			foreach ( $this->parsedQuery['filters'] as $n => $filter )
			{
				foreach ( $filter['strEqExprs'] as $i => $expr )
				{
					foreach ( $this->parsedQuery['ns'] as $prefix => $uri )
					{
						if ( $expr['value_type'] == 'URI' )
							$this->parsedQuery['filters'][$n]['strEqExprs'][$i]['value'] = eregi_replace( "$prefix:", $uri, $this->parsedQuery['filters'][$n]['strEqExprs'][$i]['value'] );
						else if ( $expr['value_type'] == 'Literal' )
							$this->parsedQuery['filters'][$n]['strEqExprs'][$i]['value_dtype'] = eregi_replace( "$prefix:", $uri, $this->parsedQuery['filters'][$n]['strEqExprs'][$i]['value_dtype'] );
					}
				}
			}
		}

		unset($this->parsedQuery['ns']);
	}


	// helper functions

	/**
	 * Remove whitespace-tokens from the array $this->tokens
	 *
	 * @access	private
	 */
	function _clearWhiteSpaces()
	{
		while ( current( $this->tokens ) == ' '  ||
				current( $this->tokens ) == "\n" ||
				current( $this->tokens ) == "\t" ||
				current( $this->tokens ) == "\r" )
		{
			unset( $this->tokens[key( $this->tokens )] );
		}
	}

	/**
	 * Check if the query string of the given clause contains an undesired ','.
	 * If a comma was correctly placed then remove it and clear all whitespaces.
	 *
	 * @param   string  $commaExpected
	 * @param   string  $clause
	 * @throws  PHPError
	 * @access  private
	 */
	function _checkComma( $commaExpected, $clause )
	{
		$this->_clearWhiteSpaces();
		
		if ( current( $this->tokens ) == ',' )
		{
			if ( !$commaExpected )
			{
				trigger_error( "RDQL error: in the $clause clause : ',' - unexpected comma", E_USER_ERROR );
			}
			else
			{
				unset( $this->tokens[key( $this->tokens )] );
				$this->_checkComma(false, $clause);
			}
		}
	}

	/**
	 * Check if the given token is either a variable (?var) or the first token of an URI (<URI>).
	 * In case of an URI this function returns the whole URI string.
	 *
	 * @param   string  $token
	 * @return  array ['value'] = string
	 * @throws  PHPError
	 * @access	private
	 */
	function _validateVarUri( $token )
	{
		if ( $token{0} == '?' )
			$token_res['value'] = $this->_validateVar( $token, 'WHERE' );
		else
			$token_res['value'] = $this->_validateUri( $token, 'WHERE' );
		
		return $token_res;
	}

	/**
	 * Check if the given token is either a variable (?var) or the first token
	 * of either an URI (<URI>) or a literal ("Literal").
	 * In case of a literal return an array with literal properties (value, language, datatype).
	 * In case of a variable or an URI return only ['value'] = string.
	 *
	 * @param   string  $token
	 * @return  array ['value'] = string
	 *                ['is_literal'] = boolean
	 *                ['l_lang'] = string
	 *                ['l_dtype'] = string
	 * @throws  PHPError
	 * @access	private
	 */
	function _validateVarUriLiteral( $token )
	{
		if ( $token{0} == '?' )
			$statement_object['value'] = $this->_validateVar( $token, 'WHERE' );
		else if ($token{0} == '<')
			$statement_object['value'] = $this->_validateUri( $token, 'WHERE' );
		else
			$statement_object = $this->_validateLiteral( $token );
		
		return $statement_object;
	}
 
	/**
	 * Check if the given token is a valid variable name (?var).
	 *
	 * @param   string  $token
	 * @param   string  $clause
	 * @return  string
	 * @throws  PHPError
	 * @access	private
	 */
	function _validateVar( $token, $clause )
	{
		preg_match( "/\?[a-zA-Z0-9_]+/", $token, $match );
		
		if ( !isset( $match[0] ) || $match[0] != $token )
			trigger_error( "RDQL error: in the $clause clause: '" . htmlspecialchars( $token ) . "' - variable name contains illegal characters", E_USER_ERROR );
			
		unset( $this->tokens[key( $this->tokens )] );
		return $token;
	}

	/**
	 * Check if $token is the first token of a valid URI (<URI>) and return the whole URI string
	 *
	 * @param   string  $token
	 * @param   string  $clause
	 * @return  string
	 * @throws  PHPError
	 * @access	private
	 */
	function _validateUri( $token, $clause )
	{
		if ( $token{0} != '<' )
		{
			if ( $clause == 'WHERE' )
				$errmsg = "RDQL error: in the $clause clause: '" . htmlspecialchars( $token ) . "' - ?Variable or &lt;URI&gt; expected";
			else
				$errmsg = "RDQL error: in the $clause clause: '" . htmlspecialchars( $token ) . "' - &lt;URI&gt; expected";
			
			trigger_error( $errmsg, E_USER_ERROR );
		}
		else
		{
			$token_res = $token;
			
			while ( $token{strlen( $token ) - 1} != '>' && $token != null )
			{
				if ( $token == '(' || $token == ')' || $token == ',' || $token == ' ' || $token == "\n" || $token == "\r" )
					trigger_error( "RDQL error: in the $clause clause: '" . htmlspecialchars( $token_res ) . "' - illegal input: '$token' - '>' missing", E_USER_ERROR );
        
				unset( $this->tokens[key( $this->tokens )] );
				$token = current( $this->tokens );
				$token_res .= $token;
			}
			
			if ( $token == null )
				trigger_error( "RDQL error: in the $clause clause: '" . htmlspecialchars( $token_res ) . "' - '>' missing", E_USER_ERROR );
				
			unset( $this->tokens[key( $this->tokens )] );
			return trim( $token_res, "<>" );
		}
	}

	/**
	 * Check if $token is the first token of a valid literal ("LITERAL") and
	 * return an array with literal properties (value, language, datatype).
	 *
	 * @param   string  $token
	 * @return  array   ['value'] = string
	 *                  ['is_literal'] = boolean
	 *                  ['l_lang'] = string
	 *                  ['l_dtype'] = string
	 * @throws  PHPError
	 * @access	private
	 */
	function _validateLiteral( $token )
	{
		if ( $token{0} != "'" && $token{0} != '"' )
			trigger_error( RDQLPARSER_WHR_ERR . " '$token' - ?Variable, &lt;URI&gt; or \"LITERAL\" expected", E_USER_ERROR );
		
		$quotation_mark = $token{0};
		
		$statement_object = array(
			'value'      => '',
			'is_literal' => true,
			'l_lang'     => '',
			'l_dtype'    => ''
		);
		
		$this->tokens[key( $this->tokens )] = substr( $token, 1 );

		$return = false;
		foreach ( $this->tokens as $k => $token )
		{
			if ( $token != null && $token{strlen( $token ) - 1} == $quotation_mark )
			{
				$token = rtrim( $token, $quotation_mark );
				$return = true;
			}
			else if ( strpos( $token, $quotation_mark . '@' ) || substr( $token, 0, 2 ) == $quotation_mark . '@' )
			{
				$lang = substr( $token, strpos( $token, $quotation_mark . '@' ) + 2 );
				
				if ( strpos( $lang, '^^' ) || substr( $lang, 0, 2 ) == '^^' )
				{
					$dtype = substr( $lang, strpos( $lang, '^^' ) + 2 );
					
					if ( !$dtype )
						trigger_error( RDQLPARSER_WHR_ERR . $quotation_mark . $statement_object['value'] . $token  . " - datatype expected", E_USER_ERROR );
						
					$statement_object['l_dtype'] = $dtype;
					$lang = substr( $lang, 0, strpos( $lang, '^^' ) );
				}
				
				if ( !$lang )
					trigger_error( RDQLPARSER_WHR_ERR . $quotation_mark . $statement_object['value'] . $token . " - language expected", E_USER_ERROR );
        
				$statement_object['l_lang'] = $lang;
				$token  = substr( $token, 0, strpos( $token, $quotation_mark . '@' ) );
				$return = true;
			}
			else if ( strpos( $token, $quotation_mark . '^^' ) || substr( $token, 0, 3 ) == $quotation_mark . '^^' )
			{
				$dtype = substr( $token, strpos( $token, $quotation_mark . '^^' ) + 3 );
				
				if ( !$dtype )
					trigger_error( RDQLPARSER_WHR_ERR . $quotation_mark . $statement_object['value'] . $token . " - datatype expected", E_USER_ERROR );
					
				$statement_object['l_dtype'] = $dtype;
				$token  = substr( $token, 0, strpos( $token, $quotation_mark . '^^' ) );
				$return = true;
			}
			else if ( strpos( $token, $quotation_mark ) )
			{
				trigger_error( RDQLPARSER_WHR_ERR . "'$token' - illegal input", E_USER_ERROR );
			}
			
			$statement_object['value'] .= $token;
			unset( $this->tokens[$k] );
			
			if ( $return )
				return $statement_object;
		}
		
		trigger_error( RDQLPARSER_WHR_ERR . "quotation end mark: $quotation_mark missing", E_USER_ERROR );
	}

	/**
	 * Check if the given token is a valid namespace prefix.
	 *
	 * @param   string  $token
	 * @return  string
	 * @throws  PHPError
	 * @access	private
	 */
	function _validatePrefix( $token )
	{
		preg_match( "/[a-zA-Z0-9_]+/", $token, $match );
		
		if ( !isset( $match[0] ) || $match[0] != $token )
			trigger_error( RDQLPARSER_USG_ERR . "'" . htmlspecialchars( $token ) . "' - illegal input, namespace prefix expected", E_USER_ERROR );
		
		unset( $this->tokens[key( $this->tokens )] );
		return $token;
	}

	/**
	 * Check if all variables from the SELECT clause are defined in the WHERE clause
	 *
	 * @access private
	 */
	function _checkSelectVars()
	{
		foreach ( $this->parsedQuery['selectVars'] as $var )
			$this->_isDefined( $var );
	}

	/**
	 * Check if the given variable is defined in the WHERE clause.
	 *
	 * @param  $var string
	 * @return string
	 * @throws PHPError
	 * @access private
	 */
	function _isDefined( $var )
	{
		$allQueryVars = $this->findAllQueryVariables();

		if ( !in_array( $var, $allQueryVars ) )
			trigger_error( "RDQL error: '$var' - variable must be defined in the WHERE clause", E_USER_ERROR );
			
		return $var;
	}

	/**
	 * Throw an error if the regular expression from the AND clause is not quoted.
	 *
	 * @param  string $filterString
	 * @param  string $lQuotMark
	 * @param  string $rQuotMark
	 * @throws PHPError
	 * @access private
	 */
	function _checkRegExQuotation( $filterString, $lQuotMark, $rQuotMark )
	{
		if ( !$lQuotMark )
			trigger_error( RDQLPARSER_AND_ERR . "'$filterString' - regular expressions must be quoted", E_USER_ERROR );

		if ( $lQuotMark != $rQuotMark )
			trigger_error( RDQLPARSER_AND_ERR . "'$filterString' - quotation end mark in the regular expression missing", E_USER_ERROR );
	}
} // END OF RdqlParser

?>
