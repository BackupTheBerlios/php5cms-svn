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


using( 'xml.rdf.lib.rdql.RdqlEngine' );
using( 'xml.rdf.lib.util.RdfMemoryModel' );


define( "RDQLMEMENGINE_AND_ERR", "RDQL error in the AND clause: " );


/**
 * This class performes as RDQL query on a RdfMemoryModel.
 *
 * Provided an rdql query parsed into an array of php variables and constraints
 * at first the engine searches for tuples matching all patterns from the WHERE clause
 * of the given RDQL query. Then the query result set is filtered with evaluated
 * boolean expressions from the AND clause of the given RDQL query.
 *
 * @package xml_rdf_lib_rdql
 */

Class RdqlMemEngine extends RdqlEngine
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
	 *                         ( [] stands for an integer index - 0..N )
	 * @access	private
	 */
 	var $parsedQuery;
	
	
 	/**
	 * Perform an RDQL Query on the given RdfMemoryModel.
	 *
	 * @param   object  RdfMemoryModel &$memModel
	 * @param   array   &$parsedQuery  (the same format as $this->parsedQuery)
	 * @param   boolean $returnNodes
	 * @return  array   [][?VARNAME] = object RdfNode  (if $returnNodes = true)
	 *      OR  array   [][?VARNAME] = string
	 *
	 * @access  public
	 */
	function &queryModel( &$memModel, &$parsedQuery, $returnNodes = true ) 
	{
		$this->parsedQuery = $parsedQuery;

		// find tuples matching all patterns
		$res = $this->findTuplesMatchingAllPatterns( $memModel );

		// filter tuples
		if ( isset( $parsedQuery['filters'] ) )
		{
			$res = $this->filterTuples( $res );
		
			if ( PEAR::isError( $res ) )
				return $res;
		}
		
		// select variables to be returned
		$res = $this->selectVariables( $res );

		if ( !$returnNodes )
			return $this->toString( $res );

		return $res;
	}

	/**
	 * Find triples matching all patterns of an RDQL query and return an array
	 * with variables from all patterns and their corresponding values.
	 * The variable values returned are instances of object RdfNode.
	 *
	 * @param   object  RdfMemoryModel  &$memModel
	 * @return  array   [][?VARNAME] = object RdfNode
	 *
	 * @access  private
	 */
	function findTuplesMatchingAllPatterns( &$memModel ) 
	{
		$resultSet = $this->findTuplesMatchingOnePattern( $memModel, $this->parsedQuery['patterns'][0] );
		
		for ( $i = 1; $i < count( $this->parsedQuery['patterns'] ); $i++ ) 
		{
			$rs = $this->findTuplesMatchingOnePattern( $memModel, $this->parsedQuery['patterns'][$i] );
			$resultSet = $this->joinTuples( $resultSet, $rs );
		}
		
		return $resultSet;
	}

	/**
	 * Find tuples matching one pattern and return an array with pattern
	 * variables and their corresponding values (instances of object RdfNode).
	 *
	 * @param   object  RdfMemoryModel &$memModel
	 * @param   array  &$pattern ['subject']['value'] = VARorURI
	 *                           ['predicate']['value'] = VARorURI
	 *                           ['object']['value'] = VARorURIorLiterl
	 *                                     ['is_literal'] = boolean
	 *                                     ['l_lang'] = string
	 *                                     ['l_dtype'] = string
	 * @return  array   [][?VARNAME] = object RdfNode
	 *
	 * @access  private
	 */
	function findTuplesMatchingOnePattern( &$memModel, &$pattern ) 
	{
		$resultSet = array();
		$i = 0;
		
		// parameters to be passed to the method findTriplesMatchingPattern
		foreach ( $pattern as $key => $v ) 
		{
			$param['object']['is_a'] = null;
			
			if ( $v['value']{0} == '?' ) 
			{
				$param[$key] = null;
				$var[$i]['key']   = $key;
				$var[$i++]['val'] = $v['value'];
			}
			else if ( isset( $v['is_literal'] ) ) 
			{
				$param[$key]['is_a']   = 'Literal';
				$param[$key]['string'] = $v['value'];
				$param[$key]['lang']   = $v['l_lang'];
				$param[$key]['dtype']  = $v['l_dtype'];
			}
			else
			{
				if ( $key == 'object' ) 
				{
					$param[$key]['is_a'] = 'Resource';
					$param[$key]['string'] = $v['value'];
					$param[$key]['lang'] = null;
					$param[$key]['dtype'] = null;
				}
				else
				{
					$param[$key] = $v['value'];
				}
			}
		}
   
		// find pattern internal bindings e.g. (?x, ?z, ?x)
		$intBindings = null;
		
		for ( $i = 0; $i < count( $var ); $i++ )
		{
			foreach ( $var as $n => $v )
			{
				if ( $i != $n && $var[$i]['val'] == $v['val'] )
					$intBindings[] = $var[$i]['key'];
			}
		}
			
		// find triples of the $memModel matching $pattern
		$resModel = $this->findTriplesMatchingPattern(
			$memModel, $param['subject'],
			$param['predicate'],
			$param['object']['is_a'],
			$param['object']['string'],
			$param['object']['lang'],
			$param['object']['dtype'],
			&$intBindings
		);

		// set values of the pattern variables to be returned
		if ( $pattern['subject']['value']{0} == '?' ) 
		{
			$n = 0;
			
			foreach ( $resModel->triples as $triple )
				$resultSet[$n++][$pattern['subject']['value']] = $triple->subj;
		}
		
		if ( $pattern['predicate']['value']{0} == '?' ) 
		{
			$n = 0;
			
			foreach ( $resModel->triples as $triple )
				$resultSet[$n++][$pattern['predicate']['value']] = $triple->pred;
		}
		
		if ( $pattern['object']['value']{0} == '?' ) 
		{
			$n = 0;
			
			foreach ( $resModel->triples as $triple )
				$resultSet[$n++][$pattern['object']['value']] = $triple->obj;
		}
		
		return $resultSet;
	}

	/**
	 * Search in $memModel for triples matching one pattern from the WHERE clause.
	 * null input for $subjLabel..$objLabel, $obj_is will match anything.
	 * null input for $objDtype will only match opbj->dtype = null
	 * null input for $objLanguage will match obj->lang = null or anything if a
	 * literal is datatyped (except for XMLLiterals and plain literals)
	 * This method also checks internal bindings if provided.
	 *
	 * @param   object RdfMemoryModel $memModel
	 * @param   string $subjLabel
	 * @param   string $predLabel
	 * @param   string $objLabel
	 * @param   string $obj_is
	 * @param   string $objLanguage
	 * @param   string $objDtype
	 * @param   array  $intBindings [] = string
	 * @return  object RdfMemoryModel
	 * @access	private
	 */
	function findTriplesMatchingPattern( &$memModel, $subjLabel, $predLabel, $obj_is, $objLabel, $objLang, $objDtype, &$intBindings )
	{
		$res = new RdfMemoryModel();

		if ( $memModel->size() == 0 )
			return $res;

		if ( $subjLabel == null && $predLabel == null && $objLabel == null ) 
		{
			$res = $memModel;
			
			if ( $intBindings )
			{
				foreach ( $res->triples as $triple )
				{
					if ( !$this->_checkIntBindings( $triple, $intBindings ) )
						$res->remove( $triple );
				}
			}
										
			return $res;
		}

		if ( $memModel->indexed && $subjLabel != null ) 
		{
			// Use index for searching
			if ( !isset( $memModel->index[$subjLabel] ) )
				return $res;
			
			for ( $i = 1; $i <= $memModel->index[$subjLabel][0]; $i++ ) 
			{
				$t = $memModel->triples[$memModel->index[$subjLabel][$i]];
				
				if ( ( $subjLabel == null || $t->subj->getLabel() == $subjLabel ) &&
					 ($predLabel  == null || $t->pred->getLabel() == $predLabel ) &&
					 ( $objLabel  == null || $t->obj->getLabel()  == $objLabel  ) )
				{					
					if ( $obj_is == 'Literal' && is_a( $t->obj, "Literal" ) ) 
					{
						if ( $this->_equalsLangDtype( $t->getObject(), $objLang, $objDtype ) )
							$res->add( $t );
					}
					else if ( $obj_is == null || ( $obj_is == 'Resource' && !is_a( $t->obj, "Literal" ) ) )
					{
						$res->add( $t );
					}
				}
      		}
		}
		else
		{
			// If there is no index, use linear search.
			foreach ( $memModel->triples as $t ) 
			{
				if ( ( $subjLabel == null || $t->subj->getLabel() == $subjLabel ) &&
					 ( $predLabel == null || $t->pred->getLabel() == $predLabel ) &&
					 ( $objLabel  == null || $t->obj->getLabel()  == $objLabel  ) )
				{
					if ( $obj_is == 'Literal' && is_a( $t->obj, "Literal" ) ) 
					{
						if ( $this->_equalsLangDtype( $t->getObject(), $objLang, $objDtype ) )
							$res->add( $t );
					}
					else if ( $obj_is == null || ( $obj_is == 'Resource' && !is_a( $t->obj, "Literal" ) ) )
					{
						$res->add( $t );
					}
				}
			}
		}

		if ( $intBindings )
		{
			foreach ( $res->triples as $triple )
			{
				if ( !$this->_checkIntBindings( $triple, $intBindings ) )
					$res->remove( $triple );
			}
		}

		return $res;
	}
 
	/**
	 * Perform an SQL-like inner join on two resultSets.
	 *
	 * @param   array   &$finalRes [][?VARNAME] = object RdfNode
	 * @param   array   &$res      [][?VARNAME] = object RdfNode
	 * @return  array              [][?VARNAME] = object RdfNode
	 *
	 * @access  private
	 */
	function joinTuples( &$finalRes, &$res )
	{
		if ( count( $finalRes ) == 0 || count( $res ) == 0 )
			return array();

		// find joint variables and new variables to be added to $finalRes
		$jointVars = array();
		$newVars   = array();
		
		foreach ( $res[0] as $varname => $node ) 
		{
			if ( array_key_exists( $varname, $finalRes[0] ) )
				$jointVars[] = $varname;
			else
				$newVars[] = $varname;
		}

		// eliminate rows of $finalRes in which the values of $jointVars do not have
		// a corresponding row in $res.
		foreach ( $finalRes as $n => $fRes ) 
		{
			foreach ( $res as $i => $r ) 
			{
				$ok = true;
				
				foreach ( $jointVars as $j_varname )
				{
					if ( $r[$j_varname] != $fRes[$j_varname] ) 
					{
						$ok = false;
						break;
					}
				}
				
				if ( $ok )
					break;
			}
			
			if ( !$ok )
				unset( $finalRes[$n] );
		}

		// join $res and $finalRes
		$joinedRes = array();
		
		foreach ( $res as $i => $r )
		{
			foreach ( $finalRes as $n => $fRes ) 
			{
				$ok = true;
				
				foreach ( $jointVars as $j_varname )
				{
					if ( $r[$j_varname] != $fRes[$j_varname] )
					{
						$ok = false;
						break;
					}
				}
					
				if ( $ok ) 
				{
					$joinedRow = $finalRes[$n];
					
					foreach ( $newVars as $n_varname )
						$joinedRow[$n_varname] = $r[$n_varname];
					
					$joinedRes[] = $joinedRow;
				}
			}
		}
   
		return $joinedRes;
	}
 
	/**
	 * Filter the result-set of query variables by evaluating each filter from the
	 * AND clause of the RDQL query.
	 *
	 * @param   array  &$finalRes  [][?VARNAME] = object RdfNode
	 * @return  array  [][?VARNAME] = object RdfNode
	 * @access	private
	 */
	function filterTuples( &$finalRes )
	{
		foreach ( $this->parsedQuery['filters'] as $filter )
		{
			// evaluate regex equality expressions of each filter
			foreach ( $filter['regexEqExprs'] as $i => $expr )
			{
				foreach ( $finalRes as $n => $fRes ) 
				{
					preg_match( $expr['regex'], $fRes[$expr['var']]->getLabel(), $match );
					$op = substr( $expr['operator'], 0, 1 );
					
					if ( ( $op != '!' && !isset( $match[0] ) ) || ( $op == '!' && isset( $match[0] ) ) )
						unset( $finalRes[$n] );
					else
						$filter['evalFilterStr'] = str_replace( "##RegEx_$i##", 'true', $filter['evalFilterStr'] );
				}
			}

			if ( count( $filter['strEqExprs'] ) != 0 || count( $filter['numExprVars'] ) != 0 )
			{
				foreach ( $finalRes as $n => $fRes )
				{
					$evalFilterStr = $filter['evalFilterStr'];
        
					// evaluate string equality expressions
					foreach ( $filter['strEqExprs'] as $i => $expr ) 
					{
						$exprBoolVal = 'false';
          
						switch ( $expr['value_type'] )
						{
							case 'variable':
								if ( ( $fRes[$expr['var']] == $fRes[$expr['value']] && $expr['operator'] == 'eq' ) || ( $fRes[$expr['var']] != $fRes[$expr['value']] && $expr['operator'] == 'ne' ) )
									$exprBoolVal = 'true';
								
								break;
                 
							case 'URI':
								if ( is_a( $fRes[$expr['var']], "Literal" ) )
									break;
								
								if ( ( $fRes[$expr['var']]->getLabel() == $expr['value'] && $expr['operator'] == 'eq' ) || ( $fRes[$expr['var']]->getLabel() != $expr['value'] && $expr['operator'] == 'ne' ) )
									$exprBoolVal = 'true';
								
								break;
                 
							case 'Literal':
								if ( !is_a( $fRes[$expr['var']], "Literal" ) )
									break;
								
								if ( ( ( $fRes[$expr['var']]->getLabel() == $expr['value'] && $expr['operator'] == 'eq' ) ||
									   ( $fRes[$expr['var']]->getLabel() != $expr['value'] && $expr['operator'] == 'ne' ) ) &&
									 ( ( $fRes[$expr['var']]->getDatatype() == $expr['value_dtype'] && $expr['operator'] == 'eq' ) ||
									   ( $fRes[$expr['var']]->getDatatype() != $expr['value_dtype'] && $expr['operator'] == 'ne' ) ) )
								{
									$exprBoolVal = 'true';
								
									// Lang tags only differentiate literals in rdf:XMLLiterals and plain literals.
									// Therefore if a literal is datatyped ignore the language tag.
									if ( ( ( $expr['value_dtype'] == null ) ||
										   ( $expr['value_dtype'] == 'http://www.w3.org/1999/02/22-rdf-syntax-ns#XMLLiteral' ) ||
										   ( $expr['value_dtype'] == 'http://www.w3.org/2001/XMLSchema#string' ) ) &&
										 ( ( $fRes[$expr['var']]->getLanguage() != $expr['value_lang'] && $expr['operator'] == 'eq' ) ||
										   ( $fRes[$expr['var']]->getLanguage() == $expr['value_lang'] && $expr['operator'] == 'ne' ) ) )
									{
										$exprBoolVal = 'false';
									}
								}
						}
          				
						$evalFilterStr = str_replace( "##strEqExpr_$i##", $exprBoolVal, $evalFilterStr );
					}

					// evaluate numerical expressions
					foreach ( $filter['numExprVars'] as $varName ) 
					{
						$varValue = "'"  . $fRes[$varName]->getLabel() . "'";
						$evalFilterStr = str_replace( $varName, $varValue, $evalFilterStr );
					}

					eval( "\$filterBoolVal = $evalFilterStr; \$eval_filter_ok = true;" );
					
					if ( !isset( $eval_filter_ok ) )
						return PEAR::raiseError( RDQLMEMENGINE_AND_ERR . "'" . htmlspecialchars( $filter['string'] ) . "'." );

					if ( !$filterBoolVal )
						unset( $finalRes[$n] );
				}
			}
		}

		return $finalRes;
	}

	/**
	 * Remove all conditional variables from the result-set and leave only variables
	 * specified in the SELECT clause of the RDQL query.
	 *
	 * @param   array  &$finalRes  [][?VARNAME] = object RdfNode
	 * @return  array  [][?VARNAME] = object RdfNode
	 * @access	private
	 */
	function selectVariables( &$finalRes ) 
	{
		// if nothing has been found return only one row of $finalRes
		// with select variables having empty values
		if ( count( $finalRes ) == 0 ) 
		{
			foreach ( $this->parsedQuery['selectVars'] as $selectVar )
				$finalRes[0][$selectVar] = null;
			
			return $finalRes;
		}
   
		// return only selectVars in the same order as given in the RDQL query
		// and reindex $finalRes.
		$n = 0;
		
		foreach( $finalRes as $key => $val ) 
		{
			foreach ( $this->parsedQuery['selectVars'] as $selectVar )
				$resultSet[$n][$selectVar] = $val[$selectVar];
			
			unset( $finalRes[$key] );
			++$n;
		}

		return $resultSet;
	}

	/**
	 * Convert the variable values of $finalRes from objects to their string serialization.
	 *
	 * @param   array  &$finalRes  [][?VARNAME] = object RdfNode
	 * @return  array  [][?VARNAME] = string
	 * @access	private
	 */
	function toString( &$finalRes ) 
	{
		foreach ( $finalRes as $n => $tuple )
		{
			foreach ( $tuple as $varname => $node ) 
			{
				if ( is_a( $node, "Resource" ) )
				{
					$res[$n][$varname] = '<' . $node->getLabel() . '>';
				}
				else if ( is_a( $node, "Literal" ) ) 
				{
					$res[$n][$varname] = '"' . $node->getLabel() . '"';
					
					if ( $node->getLanguage() )
						$res[$n][$varname] .= ' (xml:lang="' . $node->getLanguage() . '")';
					
					if ( $node->getDatatype() )
						$res[$n][$varname] .= ' (rdf:datatype="' . $node->getDatatype() . '")';
				}
				else
				{
					$res[$n][$varname] = $node;
				}
			}
		}
				
		return $res;
	}

	/**
	 * Check if the given triple meets pattern internal bindings
	 * e.g. (?x, ?z, ?x) ==> statement subject must be identical with the statement object
	 *
	 * @param   object statement &$triple
	 * @param   array  &$intBindings [] = string
	 * @return  boolean
	 * @access	private
	 */
	function _checkIntBindings( &$triple, &$intBindings )
	{
		if ( in_array( "subject", $intBindings ) ) 
		{
			if ( in_array( "predicate", $intBindings ) )
			{
				if ( $triple->subj != $triple->pred )
					return false;
			}
			
			if ( in_array( "object", $intBindings ) ) 
			{
				if ( is_a( $triple->obj, "Literal" ) )
					return false;
				else if ( $triple->subj != $triple->obj )
					return false;
			}
			
			return true;
		}
		
		if ( in_array( "predicate", $intBindings ) ) 
		{
			if ( is_a( $triple->obj, "Literal" ) )
				return false;
			else if ( $triple->pred != $triple->obj )
				return false;
			
			return true;
		}
	}

	/**
	 * Check if the lang and dtype of the passed object Literal are equal $lang and $dtype
	 * !!! Language only differentiates literals in rdf:XMLLiterals and plain literals (xsd:string).
	 * !!! Therefore if a literal is datatyped ignore the language.
	 *
	 * @param  object  Literal &$literal
	 * @param  string  $dtype1
	 * @param  string  $dtype2
	 * @return boolean
	 * @access private
	 */
	function _equalsLangDtype( &$literal, $lang, $dtype )
	{
		if ( $dtype == $literal->getDatatype() )
		{
			if ( ( $dtype == null || $dtype == 'http://www.w3.org/2001/XMLSchema#string' || $dtype == 'http://www.w3.org/1999/02/22-rdf-syntax-ns#XMLLiteral' ) && ( $lang != $literal->getLanguage() ) )
				return false;
			
			return true;
		}
		
		return false;
	}
} // END OF RdqlMemEngine

?>
