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
|Authors: Chris Bizer <chris@bizer.de>                                 |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'xml.rdf.lib.util.Resource' );
using( 'xml.rdf.lib.util.BlankNode' );
using( 'xml.rdf.lib.util.Statement' );
using( 'xml.rdf.lib.util.RdfMemoryModel' );


/**
 * An RDF statement.
 * In this implementation, a statement is not itself a resource. 
 * If you want to use a a statement as subject or object of other statements,
 * you have to reify it first.
 *
 * @package xml_rdf_lib_util
 */

class Statement extends PEAR
{	
	/**
	 * Subject of the statement
	 *
	 * @var		object resource	
	 * @access	private
	 */	
	var $subj;

	/**
	 * Predicate of the statement
	 *
	 * @var		object resource	
	 * @access	private
	 */		
    var $pred;
	
  	/**
	 * Object of the statement
	 *
	 * @var		object node	
	 * @access	private
	 */		
    var $obj;


  	/**
	 * Constructor
     * The parameters to constructor are instances of classes and not just strings.
     *
     * @param	object	node $subj
     * @param	object	node $pred
	 * @param	object	node $obj
	 * @throws	Error
     */
  	function Statement( $subj, $pred, $obj )
	{
    	if ( !is_a( $subj, "Resource" ) )
		{
			$this = new PEAR_Error( "Resource expected as subject." );
			return;
		}
		
		if ( !is_a( $pred, "Resource" ) || is_a( $pred, "BlankNode" ) )
		{
			$this = new PEAR_Error( "Resource expected as predicate, no blank node allowed." );
			return;
		}
	
		if ( !( is_a( $obj, "Resource" ) || is_a( $obj, "Literal" ) ) )
		{
			$this = new PEAR_Error( "Resource or Literal expected as object." );
			return;
		}
		
		$this->pred = $pred;
    	$this->subj = $subj;
    	$this->obj  = $obj;
  	}


  	/**
  	 * Returns the subject of the triple.
	 *
  	 * @access	public 
  	 * @return	object node
  	 */
  	function getSubject()
	{
    	return $this->subj;
  	}

  	/**
   	 * Returns the predicate of the triple.
	 *
   	 * @access	public 
   	 * @return	object node
   	 */
    function getPredicate()
	{
    	return $this->pred;
  	}

  	/**
  	 * Returns the object of the triple.
	 *
  	 * @access	public 
  	 * @return	object node
  	 */
   	function getObject()
	{
     	return $this->obj;
   	}
  
  	/**
  	 * Alias for getSubject()
	 *
  	 * @access	public 
  	 * @return	object node
  	 */
  	function subject()
	{
    	return $this->subj;
  	}

  	/**
  	 * Alias for getPredicate()
	 *
  	 * @access	public 
  	 * @return	object node
  	 */
    function predicate()
	{
    	return $this->pred;
  	}

  	/**
  	 * Alias for getObject()
	 *
  	 * @access	public 
  	 * @return object node
  	 */
   	function object()
	{
     	return $this->obj;
   	}
    
	/**
  	 * Returns the hash code of the triple.	
  	 * @access	public 
  	 * @return string
  	 */
   	function hashCode()
	{
      	return md5( $this->subj->getLabel() . $this->pred->getLabel()  . $this->obj->getLabel() );
   	}

  	/**
  	 * Dumps the triple.
	 *
  	 * @access	public 
  	 * @return string
  	 */  
  	function toString()
	{ 
    	return  "Triple(" . $this->subj->toString() . ", " . $this->pred->toString() . ", " . $this->obj->toString() . ")";	
  	}

  	/**
  	 * Returns a toString() serialization of the statements's subject.
  	 *
  	 * @access	public 
  	 * @return	string 
  	 */  
  	function toStringSubject()
	{
     	return $this->subj->toString();
   	}

  	/**
  	 * Returns a toString() serialization of the statements's predicate.
  	 *
  	 * @access	public 
  	 * @return	string 
  	 */  
   	function toStringPredicate()
	{
       return $this->pred->toString();
   	}

  	/**
  	 * Reurns a toString() serialization of the statements's object.
  	 *
  	 * @access	public 
  	 * @return	string 
  	 */  
  	function toStringObject()
	{
     	return $this->obj->toString();
   	}

  	/**
  	 * Returns the URI or bNode identifier of the statements's subject.
  	 *
  	 * @access	public 
  	 * @return	string 
  	 */  
   	function getLabelSubject()
	{
       return $this->subj->getLabel();
   	}

  	/**
  	 * Returns the URI of the statements's predicate.
  	 *
  	 * @access	public 
  	 * @return	string 
  	 */  
   	function getLabelPredicate()
	{
       return $this->pred->getLabel();
   	}

  	/**
  	 * Reurns the URI, text or bNode identifier of the statements's object.
  	 *
  	 * @access	public 
  	 * @return	string 
  	 */  
   	function getLabelObject()
	{
     	return $this->obj->getLabel();
   	}
  
  	/**
  	 * Checks if two statements are equal.
  	 * Two statements are considered to be equal if they have the
  	 * same subject, predicate and object. A statement can only be equal 
  	 * to another statement object.
	 *
  	 * @access	public 
  	 * @param		object	statement $that
  	 * @return	boolean 
  	 */  
  	function equals( $that )
	{
	    if ( $this == $that )
	      	return true;
	    
	    if ( $that == null || !( is_a( $that, "Statement" ) ) )
	      	return false;
  
	    return
			$this->subj->equals( $that->subject() )   &&
			$this->pred->equals( $that->predicate() ) &&
			$this->obj->equals( $that->object() );
	}

  	/**
  	 * Compares two statements and returns integer less than, equal to, or greater than zero.
  	 * Can be used for writing sorting function for models or with the PHP function usort(). 
  	 *
  	 * @access	public 
  	 * @param		object	statement &$that
  	 * @return	boolean 
  	 */  
  	function compare( &$that )
	{
		// statementsorter function see below
	  	return statementsorter($this, $that);
  	} 
      	  
  	/**
  	 * Reifies a statement.
  	 * Returns a new RdfMemoryModel that is the reification of the statement.
  	 * For naming the statement's bNode a Model or bNodeID must be passed to the method.   
  	 *
  	 * @access	public 
  	 * @param		mixed	&$model_or_bNodeID
  	 * @return	object	model
  	 */  
  	function &reify( &$model_or_bNodeID )
	{	
		if ( is_a( $model_or_bNodeID, "RdfMemoryModel" ) )
		{
			// parameter is model
			$statementModel = new RdfMemoryModel( $model_or_bNodeID->getBaseURI() );
			$thisStatement  = new BlankNode( $model_or_bNodeID );
		} 
		else 
		{
			// parameter is bNodeID
			$statementModel = new RdfMemoryModel();
			$thisStatement  = new BlankNode( $model_or_bNodeID );
		} 
		
		$RDFstatement = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_STATEMENT );
		$RDFtype      = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_TYPE      );
		$RDFsubject   = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_SUBJECT   );
		$RDFpredicate = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_PREDICATE );
		$RDFobject    = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_OBJECT    );
		
		$statementModel->add( new Statement( $thisStatement, $RDFtype,      $RDFstatement )         );
		$statementModel->add( new Statement( $thisStatement, $RDFsubject,   $this->getSubject() )   );
		$statementModel->add( new Statement( $thisStatement, $RDFpredicate, $this->getPredicate() ) );
		$statementModel->add( new Statement( $thisStatement, $RDFobject,    $this->Object() )       );
		
		return $statementModel;
  	}
} // END OF Statement


/**
 * Comparison function for comparing two statements.
 * statementsorter() is used by the PHP function usort ( array array, callback cmp_function)
 *
 * @access	private 
 * @param	object Statement	$a
 * @param	object Statement	$b
 * @return	integer less than, equal to, or greater than zero  
 * @throws  phpErrpr
 */  
function statementsorter( $a, $b )
{
	// compare subjects
	$x = $a->getSubject();
	$y = $b->getSubject();
	$r = strcmp( $x->getLabel(), $y->getLabel() );
	
	if ( $r != 0 ) 
		return $r;
	
	// compare predicates
	$x = $a->getPredicate();
	$y = $b->getPredicate();
	$r = strcmp( $x->getURI(), $y->getURI() );

	if ( $r != 0 )
		return $r;
	
	// final resort, compare objects
	$x = $a->getObject();
	$y = $b->getObject();
	
	return strcmp( $x->toString(), $y->toString() );
}

?>
