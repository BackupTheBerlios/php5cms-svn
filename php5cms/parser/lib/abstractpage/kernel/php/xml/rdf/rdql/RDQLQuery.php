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
 * This class implements the RDQL engine.
 *
 * @package xml_rdf_rdql
 */
 
class RDQLQuery extends PEAR
{
	/**
	 * @access public
	 */
	var $iterator;
  
	
	/**
	 * Constructor
	 *
	 * Receives a RDFIterator object that must implement a 
	 * find_tuples($sources,$subject,$predicate,$object) method
	 * returning all the tuples in the RDF sources matching the provided arguments.
	 * The first one is used to query a set of RDF documents passed as filepaths or URLs
	 * The second one can be used to query a document stored in MySQL using the RDF_store class 
	 *
	 * @access public
	 */
	function RDQLQuery( $iterator )
	{
		$this->iterator = $iterator;
	}

	
	/**
	 * This parses the RDQL query returning an array of asociative arrays with the Query Results.
	 *
	 * @access public
	 */
	function parse_query( $query )
	{
		$exps        = $this->tokenize( $query );
		$select_vars = array();
		$sources     = array();
		$conditions  = array();
		$filters     = array();
		$ns          = array();

		foreach ( $exps as $exp )
		{
			$exp = trim( $exp );
			
			if ( strtoupper( substr( $exp, 0, 6 ) ) == "SELECT" )
				$select_vars = $this->parse_select( $exp );
      
			if ( strtoupper( substr( $exp, 0, 4 ) ) == "FROM" )
				$sources = $this->parse_from( $exp );
      
			if ( strtoupper( substr( $exp, 0, 5 ) ) == "WHERE" )
				$conditions = $this->parse_where( $exp );
      
			if ( strtoupper( substr( $exp, 0, 3 ) ) == "AND" )
				$filters = $this->parse_and( $exp );
      
			if ( strtoupper( substr( $exp, 0, 5 ) ) == "USING" )
				$ns = $this->parse_using( $exp );
		}
		
		// Now everything is parsed and the query can be processed.
		// The next step will parse all the conditions against the
		// supplied source's tuples returning an array of asociative
		// arrays with all the variables involved in the conditions
		$tuples = $this->find_matching_tuples( $sources, $conditions, $ns );

		foreach ( $filters as $filter )
		{
			// $tuples is passed by reference
			$this->filter_tuples( $tuples, $filter );
    	}
    
		$query_results = array();
    
		foreach ( $tuples as $a_tuple )
		{
      		$a_result = array();
      
	  		foreach ( $a_tuple as $key=>$val )
			{
        		if ( in_array( $key, $select_vars ) )
					$a_result[$key] = $val;
      		}
      
	  		if ( count( $a_result ) > 0 )
			{
				ksort( $a_result );
				$query_results[] = $a_result; 
      		}
    	} 
    
		if ( count( $query_results ) > 0 )
			return $query_results;
		else
			return false; 
	}

	/**
	 * @access public
	 */
	function tokenize( $exp )
	{
		$exprs   = array();
		$current ='';
		$tok     = strtok( $exp, " \n\t" );
		
		while ( $tok )
		{
			if ( in_array( trim( strtoupper( $tok ) ), array( "SELECT", "FROM", "WHERE", "AND", "USING" ) ) )
			{
				if ( strlen( $current ) > 0 )
				{
					$exprs[] = $current; 
					$current ='';
				}
			}
			
			$current .= $tok.' ';
			$tok = strtok( " \n\t" );
		}
		
		if ( strlen( $current ) > 0 )
		{
			$exprs[] = $current; 
			$current = $tok;
		}
		
		return $exprs;
	}

	/**
	 * @access public
	 */
	function array_sql_join( $v1, $v2 )
	{
		$result_set = array(); 
		
		foreach ( $v1 as $elemv1 )
		{
			foreach ( $v2 as $elemv2 )
			{
				$res = $this->array_join_elems( $elemv1, $elemv2 );
				
				if ( $res )
					$result_set[] = $res; 
			} 
		}
		
		return $result_set;
	}

	/**
	 * @access public
	 */
	function array_join_elems( $v1, $v2 )
	{
		$ret = array();
		
		foreach ( array_keys($v1) as $k1 )
		{
			if ( isset( $v2[$k1] ) )
			{
				if ( $v2[$k1] == $v1[$k1] )
					$ret[$k1] = $v1[$k1];
				else
					return false;
			}
			else
			{
				$ret[$k1] = $v1[$k1];
			} 
		} 
		
		foreach ( array_keys( $v2 ) as $k2 )
		{
			if ( !isset( $ret[$k2] ) )
				$ret[$k2]=$v2[$k2];
		}
		
		return $ret;
	}
  
	/**
	 * This parses a 'SELECT ?x,?y,?z' expression returning an array with variable names.
	 *
	 * @access public
	 */
	function parse_select( $exp )
	{
		$vars = array();
		$exp  = trim( $exp );
		$exp_parts = explode( " ", $exp );
		
		if ( $exp_parts[0] != "SELECT" )
			PEAR::raiseError( "Expected a SELECT token in the query.", null, PEAR_ERROR_TRIGGER );
    
		array_shift( $exp_parts );
		$vars = explode( ',', implode( '', $exp_parts ) );
		
		return $vars;
	}

	/**
	 * This parses a 'FROM doc1,doc2' expression returning an array with document URIs/filenames.
	 *
	 * @access public
	 */
	function parse_from( $exp )
	{
		$vars = array();
		$exp  = trim( $exp );
		$exp_parts = explode( " ", $exp );
		
		if ( $exp_parts[0] != "FROM" )
			PEAR::raiseError( "Expected a FROM token in the query.", null, PEAR_ERROR_TRIGGER );
    
		array_shift( $exp_parts );
		$vars = explode( ',', implode( '', $exp_parts ) );
		
		return $vars;
	}

	/**
	 * This parses a where construction in the form 'WHERE (x1,x2,x3),(z1,z2,z3)' returning and array of conditions.
	 *
	 * @access public
	 */
	function parse_where( $exp )
	{
		$vars = array();
		$exp  = trim( $exp );
		$exp_parts = explode( " ", $exp );
		
		if ( $exp_parts[0] != "WHERE" )
			PEAR::raiseError( "Expected a WHERE token in the query.", null, PEAR_ERROR_TRIGGER );
    
		array_shift( $exp_parts );
		$expr  = implode( '', $exp_parts );
		$avar  = '';
		$level = 0;
		
		for ( $i = 0; $i < strlen( $expr ); $i++ )
		{
			$chr = substr( $expr, $i, 1 );
			
			if ( $chr == "(" )
				$level++; 
      
			if ( $chr == ")" )
				$level--; 
      
			if ( ( $chr == ',' ) && ( $level == 0 ) )
			{
				if ( strlen( $avar ) > 0 )
				{
					$vars[] = $avar;
					$avar   = ''; 
				} 
			}
			else
			{
				$avar .= $chr; 
			}
		} 
		
		if ( strlen( $avar ) > 0 )
		{
			$vars[] = $avar;
			$avar   = ''; 
		} 
		
		return $vars;
	}

	/**
	 * This parses and AND condition.
	 *
	 * @access public
	 */
	function parse_and( $exp )
	{
		$vars = array();
		$exp  = trim( $exp );
		$exp_parts = explode( " ", $exp );
	
		if ( $exp_parts[0] != "AND" )
			PEAR::raiseError( "Expected a AND token in the query.", null, PEAR_ERROR_TRIGGER );
    
		array_shift( $exp_parts );
		$vars = explode( ',', implode( '', $exp_parts ) );
		
		return $vars; 
	}

	/**
	 * This parses a "USING" expr in the form USING prefix for URI, prefix for URI.
	 *
	 * @access public
	 */
	function parse_using( $exp )
	{
		$vars = array();
		$ns   = array();
		$exp  = trim( $exp );
		$exp_parts = explode( " ", $exp );
		
		if ( $exp_parts[0] != "USING" )
			PEAR::raiseError( "Expected a USING token in the query.", null, PEAR_ERROR_TRIGGER );
   
		array_shift( $exp_parts );
		$vars = explode( ',', implode( ' ', $exp_parts ) );
		
		foreach( $vars as $var )
		{
			$var_parts = explode( ' ', trim( $var ) );
			
			if ( strtoupper( $var_parts[1] ) != "FOR" )
				PEAR::raiseError( "Expected a for token in the USING part: $exp.", null, PEAR_ERROR_TRIGGER );
      
			preg_match( "/\<([^>]*)\>/", $var_parts[2], $reqs );
			$var_parts[2] = $reqs[1];
			$ns[$var_parts[0]] = $var_parts[2];
		}
		
		return $ns;
	}

	/**
	 * This function filters the tuples passed as arguments according to the filter.
	 *
	 * @access public
	 */
	function filter_tuples( &$tuples, $filter )
	{
		$toelim = array();
		
		for ( $i = 0; $i < count( $tuples ); $i++ )
		{
			$a_tuple  = $tuples[$i];
			$a_filter = $filter;
			
			foreach ( $a_tuple as $varname => $value )
				$a_filter = str_replace( $varname, "\"$value\"", $a_filter );
      
			$php_code = 'return(' . $a_filter . ');';
			$result = eval( $php_code );
			
			if ( !$result )
				$toelim[] = $i;
    	}
		
		foreach ( $toelim as $i )
			unset( $tuples[$i] );
	}

	/**
	 * @param  array $sources array with the names of RDF documents stored (keys)
	 * @param  array $conditions array with the coditions to be evaluated
	 * @param  array $ns array with the namespaces
	 * @access public
	 */
	function find_matching_tuples( $sources, $conditions, $ns )
	{
		$vec = '';
		
		foreach ( $conditions as $condition )
		{
			$condition = trim( $condition );
			preg_match( "/\(([^)]*)\)/", $condition, $reqs );
			$elems = explode( ',', $reqs[1] );
			
			// Check each element, if it is <something:foo> then replace it by the namespace.
			if ( $elems[0]{0} == '<' )
			{
				preg_match( "/\<([^>]*)\>/", $elems[0], $reqs );
				$elems[0] = $reqs[1];
				$predicate_parts = explode( ':', $elems[0] );
				$elems[0] = $ns[$predicate_parts[0]] . $predicate_parts[1]; 
			}
			
			if ( $elems[1]{0} == '<' )
			{
				preg_match( "/\<([^>]*)\>/", $elems[1], $reqs );
				$elems[1] = $reqs[1];
				$predicate_parts = explode( ':', $elems[1] );
				$elems[1] = $ns[$predicate_parts[0]] . $predicate_parts[1]; 
			}
			
			if ( $elems[2]{0} == '<' )
			{
				preg_match( "/\<([^>]*)\>/", $elems[2], $reqs );
				$elems[2] = $reqs[1];
				$predicate_parts = explode( ':', $elems[2] );
				$elems[2] = $ns[$predicate_parts[0]] . $predicate_parts[1]; 
			}
			
			$a_vec = $this->iterator->find_tuples( $sources, $elems[0], $elems[1], $elems[2] );
			
			if ( $vec )
				$vec = $this->array_sql_join( $a_vec, $vec );
			else
				$vec = $a_vec; 
		}
		
		return $vec;
	}
} // END OF RDQLQuery

?>
