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
|         Gunnar AAstrand Grimnes <ggrimnes@csd.abdn.ac.uk>            |
|         Radoslaw Oldakowski <radol@gmx.de>                           |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'xml.rdf.RdfUtil' );
using( 'xml.rdf.lib.RdfParser' );
using( 'xml.rdf.lib.RdfSerializer' );
using( 'xml.rdf.lib.N3Parser' );
using( 'xml.rdf.lib.N3Serializer' );
using( 'xml.rdf.lib.util.RdfStatementIterator' );
using( 'xml.rdf.lib.util.BlankNode' );
using( 'xml.rdf.lib.rdql.RdqlParser' );
using( 'xml.rdf.lib.rdql.RdqlMemEngine' );


/**
 * A RdfMemoryModel is an RDF Model, which is stored in the main memory.
 * This class provides methods for manipulating RdfMemoryModels.
 *
 * @package xml_rdf_lib_util
 */

class RdfMemoryModel extends PEAR
{
 	/**
	 * Triples of the RdfMemoryModel
	 *
	 * @var		array
	 * @access	private
	 */		
    var $triples = array(); 

 	/**
	 * Search index
	 *
	 * @var		array
	 * @access	private
	 */		
    var $index;

 	/**
	 * This is set to true if the RdfMemoryModel is indexed
	 *
	 * @var		boolean
	 * @access	private
	 */	
    var $indexed;

	/**
 	 * Base URI of the Model.
 	 * Affects creating of new resources and serialization syntax.
 	 *
 	 * @var     string
 	 * @access	private
 	 */
 	var $baseURI;
	
	
   	/**
     * Constructor
     *
     * @param   string $baseURI 
	 * @access	public
     */
    function RdfMemoryModel( $baseURI = null )
	{
		$this->setBaseURI( $baseURI );
		$this->indexed = false;
    }
  
  
  	/**
   	 * Set a base URI for the RdfMemoryModel.
   	 * Affects creating of new resources and serialization syntax.
   	 * If the URI doesn't end with # : or /, then a # is added to the URI. 
	 *
   	 * @param	string	$uri
   	 * @access	public
   	 */
  	function setBaseURI( $uri )
	{
		if ( $uri != null )
		{
			$c = substr( $uri, strlen( $uri ) - 1 ,1 );
		
			if ( !( $c == "#" || $c == ":" || $c == "/" || $c == "\\" ) )
				$uri .= "#";
		}	
		
		$this->baseURI = $uri;
  	}

  	/**
   	 * Number of triples in the RdfMemoryModel.
   	 *
   	 * @return	integer
   	 * @access	public
   	 */
   	function size()
	{
     	return count( $this->triples );
  	}

  	/**
   	 * Checks if RdfMemoryModel is empty
   	 *
   	 * @return	boolean
   	 * @access	public
   	 */
	function isEmpty()
	{
		if ( count( $this->triples ) == 0 )
			return true;
		else
			return false;
	}
	
  	/**
   	 * Adds a new triple to the RdfMemoryModel without checking if the statement is already in the RdfMemoryModel.
   	 * The function doesn't check if the statement is already in the RdfMemoryModel.
   	 * So if you want a duplicate free RdfMemoryModel use the addWithoutDuplicates() function (which is slower then add())
  	 *
   	 * @param   object Statement	$statement
   	 * @access	public
   	 * @throws	Error 
   	 */
  	function add( $statement )
	{
	 	if ( !is_a( $statement, "Statement" ) )
		{
			$errmsg = "RDFAPI error (class: RdfMemoryModel; method: add): Statement expected.";
			trigger_error( $errmsg, E_USER_ERROR ); 
	 	}
	 
     	$this->indexed   = false;
	 	$this->triples[] = $statement;
  	}
  
  	/**
   	 * Checks if a new statement is already in the RdfMemoryModel and adds the statement, if it is not in the RdfMemoryModel.
   	 * addWithoutDuplicates() is significantly slower then add().
  	 *
   	 * @param	object Statement	$statement
   	 * @access	public
   	 * @throws	Error 
   	 */
  	function addWithoutDuplicates( $statement )
	{
	 	if ( !is_a( $statement, "Statement" ) )
		{
			$errmsg = "RDFAPI error (class: RdfMemoryModel; method: addWithoutDuplicates): Statement expected.";
			trigger_error( $errmsg, E_USER_ERROR ); 
	 	}
	 
	 	if ( !$this->contains( $statement ) ) 
		{
	 		$this->indexed   = false;	
  		    $this->triples[] = $statement;
	 	}
  	}

  	/**
   	 * Removes the triple from the RdfMemoryModel.
   	 *
   	 * @param	object Statement	$statement
   	 * @access	public
   	 * @throws	Error
   	 */
   function remove( $statement ) 
   {  
   	 	if ( !is_a( $statement, "Statement" ) ) 
		{
			$errmsg = "RDFAPI error (class: RdfMemoryModel; method: remove): Statement expected.";
			trigger_error( $errmsg, E_USER_ERROR ); 
		}
	  	
		foreach ( $this->triples as $key => $value ) 
		{
			if ( $this->matchStatement( $value, $statement->subject(), $statement->predicate(), $statement->object() ) ) 
			{
				$this->indexed = false;
				unset( $this->triples[$key] );
			}
	  	}
   	}

    /**
     * Short Dump of the RdfMemoryModel.
     *
     * @access	public 
     * @return	string 
     */  
 	function toString()
	{
       	return "RdfMemoryModel[baseURI=" . $this->getBaseURI() . ";  size=" . $this->size() . "]";
   	}

  	/**
   	 * Dumps of the RdfMemoryModel including all triples.
   	 *
   	 * @access	public 
   	 * @return	string 
   	 */  
   	function toStringIncludingTriples()
	{
       	$dump = $this->toString() . chr( 13 );
	   
	   	foreach ( $this->triples as $value )
	   		$dump .= $value->toString() . chr( 13 );
	   
	   	return $dump;
   	}

  	/**
   	 * Saves the RDF serialization of the RdfMemoryModel to a file.
   	 * You can decide to which format the model should be serialized by putting a
   	 * corresponding suffix(.rdf or .n3) at the end of the filename. If no suffix
   	 * is placed this method will serialize the model to XML/RDF format.
   	 * Returns false if the RdfMemoryModel couldn't be saved to the file.
  	 *
   	 * @access	public 
   	 * @param 	string 	$filename
   	 * @throw   Error
   	 * @return	boolean   
   	 */  
   function saveAs( $filename )
   {
     	// get suffix and create a corresponding serializer
     	preg_match( "/\.([a-zA-Z0-9_]+)$/", $filename, $suffix );
     	
		if ( isset( $suffix[1] ) ) 
		{
        	if ( strtolower( $suffix[1]) == 'n3' )
           		$ser = new N3Serializer;
        	else if ( strtolower( $suffix[1] ) == 'rdf' )
           		$ser = new RdfSerializer();
     	}
		else 
		{
       		// default
       		$ser = new RdfSerializer();
       		$filename .= '.rdf';
     	}

     	return $ser->saveAs( $this, $filename );
 	}  

  	/**
   	 * Loades a RdfMemoryModel from a file containing RDF.
   	 * This method recognizes the suffix of the filename (.n3 or .rdf) and
   	 * calls a suitable parser.
   	 * If the model is not empty, the contents of the file is added to the RdfMemoryModel.
  	 *
   	 * @access	public 
   	 * @param 	string 	$filename
   	 * @throws  Error
   	 */  
   	function load( $filename )
	{
     	// create a parser according to the suffix of the filename
     	// if there is no suffix assume the file to be XML/RDF
     	preg_match( "/\.([a-zA-Z0-9_]+)$/", $filename, $suffix );
     
	 	if ( isset( $suffix[1] ) && strtolower( $suffix[1] ) == 'n3' )
        	$parser = new N3Parser();
     	else
	   		$parser = new RdfParser();

     	$temp =& $parser->generateModel( $filename );
		
		if ( PEAR::isError( $temp ) )
			return $temp;
			
     	$this->addModel( $temp );
     	
		if ( $this->getBaseURI() == null )
			$this->setBaseURI( $temp->getBaseURI() );
   	}  
   
  	/**
   	 * Writes the RDF serialization of the RdfMemoryModel as HTML.
   	 *
   	 * @access	public 
   	 */  
   	function writeAsHtml()
	{
		$ser =  new RdfSerializer();
        $rdf =& $ser->serialize( $this );
		$rdf =  htmlspecialchars( $rdf, ENT_QUOTES );
		$rdf =  str_replace( " ", "&nbsp;", $rdf );
		$rdf =  nl2br( $rdf );
		
		echo $rdf; 
   	}  

  	/**
   	 * Writes the RDF serialization of the RdfMemoryModel as HTML table.
   	 *
   	 * @access	public 
   	 */  
   	function writeAsHtmlTable()
	{
		RdfUtil::writeHTMLTable( $this );
   	}  
   
  	/**
   	 * Writes the RDF serialization of the RdfMemoryModel as HTML table.
   	 *
   	 * @access	public 
   	 * @return	string 
   	 */  
   	function writeRdfToString()
	{
		$ser =  new RdfSerializer();
        $rdf =& $ser->serialize( $this );
		
		return $rdf;
   	}
   
  	/**
   	 * Tests if the RdfMemoryModel contains the given triple.
   	 * true if the triple belongs to the RdfMemoryModel;
   	 * false otherwise.
   	 * To improve the search speed with big RdfMemoryModels, call index() before seaching.
  	 *
   	 * @param	object Statement	&$statement
   	 * @return	boolean
   	 * @access	public
   	 */
  	function contains( &$statement )
	{
  		if ( $this->indexed ) 
		{
			// Use index for searching
			$subject = $statement->getSubject();			    
		
			if ( !isset( $this->index[$subject->getLabel()] ) ) 
				return false;			  
			
			for ( $i = 1; $i <= $this->index[$subject->getLabel()][0]; $i++ ) 
			{ 
			  	$t = $this->triples[$this->index[$subject->getLabel()][$i]];
			    
				if ( $t->equals( $statement ) )
			    	return true;
			}
	
			return false;
		} 
		else 
		{ 
		   	// If there is no index, use linear search.
			foreach ( $this->triples as $value ) 
			{
            	if ( $value->equals( $statement ) ) 
					return true;
			}
			
			return false;
		}
	}
  
	/**
	 * Determine if all of the statements in a model are also contained in this RdfMemoryModel.
	 * True if all of the statements in $model are also contained in this RdfMemoryModel and false otherwise.
	 *
	 * @param	object Model	&$model
	 * @return	boolean
	 * @access	public
	 */
	function containsAll( &$model )
	{
    	if ( is_a( $model, "RdfMemoryModel" ) ) 
		{
       		foreach ( $model->triples as $statement )
			{
	     		if ( !$this->contains( $statement ) )
           			return false;
			}
			         	
			return true;
    	}

    	$errmsg = "RDFAPI error (class: RdfMemoryModel; method: containsAll): Model expected.";
    	trigger_error( $errmsg, E_USER_ERROR );
  	}
  
  	/**
	 * Determine if any of the statements in a model are also contained in this RdfMemoryModel.
	 * True if any of the statements in $model are also contained in this RdfMemoryModel and false otherwise.
	 *
	 * @param	object Model	&$model
	 * @return	boolean
	 * @access	public
	 */
	function containsAny( &$model )
	{
    	if ( is_a( $model, "RdfMemoryModel" ) )
		{
       		foreach ( $model->triples as $modelStatement )
			{
	 	 		if ( $this->contains( $modelStatement ) )
		   			return true;
			}
				     	
			return false;
    	}
	
   		$errmsg = "RDFAPI error (class: RdfMemoryModel; method: containsAll): Model expected.";
   		trigger_error( $errmsg, E_USER_ERROR );
  	}
 
  	/**
   	 * Builds a search index for the statements in the RdfMemoryModel.
   	 * The index is used by the find() and contains() functions.
   	 * Performance example using a model with 43000 statements on a Linux machine:  
   	 * Find without index takes 1.7 seconds.
   	 * Indexing takes 1.8 seconds.
   	 * Find with index takes 0.001 seconds.
   	 * So if you want to query a model more then once, build a index first. 
  	 *
   	 * @access	public
   	 */
    function index()
	{
  	  	if ( !$this->indexed )
		{
		  	// Delete old index
	      	$this->index = null;
		 
		  	// Generate lookup table.
		  	foreach ( $this->triples as $k => $t )
			{ 
				$s = $t->getSubject();
				
				if ( isset( $this->index[$s->getLabel()][0] ) )
					$this->index[$s->getLabel()][0]++;
				else
					$this->index[$s->getLabel()][0] = 1;
			
				$this->index[$s->getLabel()][$this->index[$s->getLabel()][0]] = $k;	
			}
		  
			$this->indexed=true;
		}
	} 

  	/**
   	 * Returns true if the RdfMemoryModel is indexed.
	 *
   	 * @return	boolean
   	 * @access	public
   	 */ 
    function isIndexed()
	{ 
      	return $this->indexed;
    }
    
  	/**
   	 * General method to search for triples.
   	 * null input for any parameter will match anything.
   	 * Example:  $result = $m->find( null, null, $node );
   	 * Finds all triples with $node as object.
   	 * Returns an empty RdfMemoryModel if nothing is found.
   	 * To improve the search speed with big RdfMemoryModels, call index() before seaching.
  	 *
   	 * @param	object RdfNode	$subject
   	 * @param	object RdfNode	$predicate
   	 * @param	object RdfNode	$object
   	 * @return	object RdfMemoryModel
   	 * @access	public
   	 * @throws	Error
   	 */
   	function find( $subject, $predicate, $object )
	{	
		if ( ( !is_a( $subject,   "Resource" ) && $subject   != null ) || 
		 	 ( !is_a( $predicate, "Resource" ) && $predicate != null ) || 
			 ( !is_a( $object,    "RdfNode"  ) && $object    != null ) ) 
		{
			$errmsg = "RDFAPI error (class: RdfMemoryModel; method: find): Parameters must be subclasses of RdfNode or null";
			trigger_error( $errmsg, E_USER_ERROR ); 
		}
		
		$res = new RdfMemoryModel( $this->getBaseURI() );

		if ( $this->size() == 0 )
			return $res;

		if ( $subject == null && $predicate == null && $object == null )
			return $this;

		if ( $this->indexed && $subject != null ) 
		{
		  	// Use index for searching
		  	if ( !isset( $this->index[$subject->getLabel()] ) ) 
				return $res;
		  	
			for ( $i = 1; $i <= $this->index[$subject->getLabel()][0]; $i++ ) 
			{ 
		    	$t = $this->triples[$this->index[$subject->getLabel()][$i]];
		    	
				if ( $this->matchStatement( $t, $subject, $predicate, $object ) )
		      		$res->add( $t );
		  	}
		} 
		else 
		{ 
		  	// If there is no index, use linear search.
		  	foreach ( $this->triples as $value ) 
			{
		    	if ( $this->matchStatement( $value, $subject, $predicate, $object ) ) 
		      		$res->add( $value );
		  	}
	  	}
	  	
		return $res;
   	}
   
	/**
	 * Method to search for triples using Perl-style regular expressions.
	 * null input for any parameter will match anything.
	 * Example:  $result = $m->find_regex( null, null, $regex );
	 * Finds all triples where the label of the object node matches the regular expression.
	 * Returns an empty RdfMemoryModel if nothing is found.
	 *
	 * @param	string	$subject_regex
	 * @param	string	$predicate_regex
	 * @param	string	$object_regex
	 * @return	object RdfMemoryModel
   	 * @access	public
	 */
	function findRegex( $subject_regex, $predicate_regex, $object_regex ) 
	{	
		$res = new RdfMemoryModel( $this->getBaseURI() );

		if ( $this->size() == 0 )
			return $res;

		if ( $subject_regex == null && $predicate_regex == null && $object_regex == null )
			return $this;

        foreach ( $this->triples as $value ) 
		{
			if ( ( $subject_regex   == null || preg_match( $subject_regex,   $value->subj->getLabel() ) ) &&
				 ( $predicate_regex == null || preg_match( $predicate_regex, $value->pred->getLabel() ) ) &&
				 ( $object_regex    == null || preg_match( $object_regex,    $value->obj->getLabel()  ) ) ) 
			{
				$res->add( $value );
			}
		}				

		return $res;
	} 
   
  	/**
   	 * Returns all tripels of a certain vocabulary.
   	 * $vocabulary is the namespace of the vocabulary inluding a # : / char at the end.
   	 * e.g. http://www.w3.org/2000/01/rdf-schema#
   	 * Returns an empty RdfMemoryModel if nothing is found.
   	 *
   	 * @param	string	$vocabulary
   	 * @return	object RdfMemoryModel
   	 * @access	public
   	 */
   	function findVocabulary( $vocabulary )
	{
		if ( $this->size() == 0 )
			return $res;
		
		if ( $vocabulary == null || $vocabulary == "" )
			return $this;

		$res = new RdfMemoryModel( $this->getBaseURI() );
			
        foreach ( $this->triples as $value ) 
		{
	        if ( RdfUtil::getNamespace( $value->getPredicate()) == $vocabulary )
				 $res->add( $value );
		}
		
		return $res;
   	}   

  	/**
   	 * Searches for triples and returns the first matching statement.
   	 * null input for any parameter will match anything.
   	 * Example:  $result = $m->findFirstMatchingStatement( null, null, $node );
   	 * Returns the first statement of the RdfMemoryModel where the object equals $node.
   	 * Returns an null if nothing is found.
   	 *
   	 * @param	object RdfNode	$subject
   	 * @param	object RdfNode	$predicate
   	 * @param	object RdfNode	$object
   	 * @return	object Statement      
   	 * @access	public
   	 */
   	function findFirstMatchingStatement( $subject, $predicate, $object ) 
	{	
		$res = $this->find( $subject, $predicate, $object );
		
		if ( $res->size() != 0 )
			return $res->triples[0];
		else
			return null;
	}   
   
  	/**
   	 * Searches for triples and returns the number of matches.
   	 * null input for any parameter will match anything.
   	 * Example:  $result = $m->findCount( null, null, $node );
   	 * Finds all triples with $node as object.
  	 *
   	 * @param	object RdfNode	$subject
   	 * @param	object RdfNode	$predicate
   	 * @param	object RdfNode	$object
   	 * @return	integer      
   	 * @access	public
   	 */
   	function findCount( $subject, $predicate, $object )
	{	
		$res = $this->find( $subject, $predicate, $object );
		return $res->size();
	}

	/**
 	 * Perform an RDQL query on this RdfMemoryModel.
 	 * This method returns an associative array of variable bindings.
 	 * The values of the query variables can either be RAP's objects (instances of Node)
 	 * if $returnNodes set to true, or their string serialization.
 	 *
 	 * @access	public
 	 * @param string $queryString
 	 * @param boolean $returnNodes
 	 * @return  array   [][?VARNAME] = object RdfNode  (if $returnNodes = true)
 	 *      OR  array   [][?VARNAME] = string
 	 *
 	 */
 	function rdqlQuery( $queryString, $returnNodes = true ) 
	{
   		$parser = new RdqlParser;
   		$parsedQuery =& $parser->parseQuery( $queryString );

   		// this method can only query this RdfMemoryModel
   		// if another model was specified in the from clause throw an error
   		if ( isset( $parsedQuery['sources'][1] ) ) 
			return PEAR::raiseError( "This method can only query this RdfMemoryModel." );

   		$engine = new RdqlMemEngine;
   		$res =& $engine->queryModel( $this, $parsedQuery, $returnNodes );
		
   		return $res;
 	}
 
	/**
	 * General method to replace nodes of a RdfMemoryModel.
	 * null input for any parameter will match nothing.
	 * Example:  $m->replace($node, null, $node, $replacement);
	 * Replaces all $node objects beeing subject or object in 
	 * any triple of the RdfMemoryModel with the $needle node.
	 *
	 * @param	object RdfNode	$subject
	 * @param	object RdfNode	$predicate
	 * @param	object RdfNode	$object   
	 * @param	object RdfNode	$replacement
	 * @access	public
	 * @throws	Error
	 */
	function replace( $subject, $predicate, $object, $replacement ) 
	{	
		if ( ( !is_a( $replacement, "RdfNode"  ) ) || 
			 ( !is_a( $subject,     "Resource" ) && $subject   != null ) || 
			 ( !is_a( $predicate,   "Resource" ) && $predicate != null ) || 
			 ( !is_a( $object,      "RdfNode"  ) && $object    != null ) ) 
		{
			$errmsg = "RDFAPI error (class: RdfMemoryModel; method: replace): Parameters must be subclasses of RdfNode or null";
			trigger_error( $errmsg, E_USER_ERROR ); 
		}
		
		if ( $this->size() == 0 )
			break;

        foreach ( $this->triples as $key => $value ) 
		{
			if ( $this->triples[$key]->subj->equals( $subject ) )
			{
				$this->triples[$key]->subj = $replacement;
				$this->indexed = false;
			}
			
			if ( $this->triples[$key]->pred->equals( $predicate ) )
				$this->triples[$key]->pred = $replacement;
			
			if ( $this->triples[$key]->obj->equals( $object ) )
				$this->triples[$key]->obj = $replacement;
		}
	}
  
	/**
	 * Internal method that checks, if a statement matches a S, P, O or null combination.
	 * null input for any parameter will match anything.
	 *
	 * @param	object Statement	$statement   
	 * @param	object RdfNode	$subject
	 * @param	object RdfNode	$predicate
	 * @param	object RdfNode	$object
	 * @return	boolean      
	 * @access	private
	 */
	function matchStatement( $statement, $subject, $predicate, $object )
	{
	  	if ( ( $subject != null ) && !( $statement->subj->equals( $subject ) ) )
	      	return false;
	  
	    if ( $predicate != null && !( $statement->pred->equals( $predicate ) ) )
	      	return false;
	      
	    if ( $object != null && !( $statement->obj->equals( $object ) ) )
	      	return false;
	
	    return true;
	}
  
  	/**
   	 * Internal method, that returns a resource URI that is unique for the RdfMemoryModel.
   	 * URIs are generated using the base_uri of the RdfMemoryModel, the prefix and a unique number.
   	 *
   	 * @param	string	$prefix   
   	 * @return	string      
   	 * @access	private
   	 */
   	function getUniqueResourceURI( $prefix )
	{ 
		$counter = 1;
        
		while ( true )
		{
			$uri       = $this->getBaseURI() . $prefix . $counter;
			$tempbNode = new BlankNode( $uri );
            $res1      = $this->find( $tempbNode, null, null );
			$res2      = $this->find( null, null, $tempbNode );

			if ( $res1->size() == 0 && $res2->size() == 0 )
                return $uri;
				
            $counter++;
        }
	}

	/**
	 * Checks if two models are equal.
	 * Two models are equal if and only if the two RDF graphs they represent are isomorphic.
	 * 
	 * Warning: This method doesn't work correct with models where the same blank node has different 
	 * identifiers in the two models. We will correct this in a future version.
	 *
	 * @access	public 
	 * @param	object	model &$that
	 * @throws  phpErrpr
	 * @return	boolean 
	 */
	function equals( &$that )
	{ 
		if ( !is_a( $that, "RdfMemoryModel" ) )
		{
			$errmsg = "RDFAPI error (class: RdfMemoryModel; method: equals): Model expected.";
			trigger_error( $errmsg, E_USER_ERROR ); 
		}
	
  		if ( $this->size() != $that->size() )
			return false;
	
    	if ( !$this->containsAll( $that ) )
      		return false;
   		
		return true;
  	}	 
  
 	/** 
  	 * Returns a new RdfMemoryModel that is the set-union of the RdfMemoryModel with another model.
  	 * Duplicate statements are removed. If you want to allow duplicates, use addModel() which is much faster.
  	 *
  	 * The result of taking the set-union of two or more RDF graphs (i.e. sets of triples) 
  	 * is another graph, which we will call the merge of the graphs. 
  	 * Each of the original graphs is a subgraph of the merged graph. Notice that when forming 
  	 * a merged graph, two occurrences of a given uriref or literal as nodes in two different 
  	 * graphs become a single node in the union graph (since by definition they are the same 
  	 * uriref or literal) but blank nodes are not 'merged' in this way; and arcs are of course 
  	 * never merged. In particular, this means that every blank node in a merged graph can be 
  	 * identified as coming from one particular graph in the original set of graphs.
  	 * 
  	 * Notice that one does not, in general, obtain the merge of a set of graphs by concatenating 
  	 * their corresponding N-triples documents and constructing the graph described by the merged 
  	 * document, since if some of the documents use the same node identifiers, the merged document 
  	 * will describe a graph in which some of the blank nodes have been 'accidentally' merged. 
  	 * To merge Ntriples documents it is necessary to check if the same nodeID is used in two or 
  	 * more documents, and to replace it with a distinct nodeID in each of them, before merging the 
  	 * documents. (Not implemented yet !!!!!!!!!!!)
  	 *
  	 * @param	object Model	$model
  	 * @return	object RdfMemoryModel
  	 * @access	public
  	 * @throws  phpErrpr
  	 */
  	function &unite( &$model )
	{ 
		if ( !is_a( $model, "RdfMemoryModel" ) ) 
		{
			$errmsg = "RDFAPI error (class: RdfMemoryModel; method: unite): Model expected.";
			trigger_error( $errmsg, E_USER_ERROR ); 
		}
  
  		$res = $this;
    	$res->indexed = false;
	
    	if ( is_a( $model, "RdfMemoryModel" ) ) 
		{
       		foreach ( $model->triples as $value )
		  		$res->addWithoutDuplicates( $value );
    	}

		return $res;
  	}
  
 	/** 
  	 * Returns a new RdfMemoryModel that is the subtraction of another model from this RdfMemoryModel.
  	 *
  	 * @param	object Model	$model
  	 * @return	object RdfMemoryModel
  	 * @access	public
  	 * @throws  phpErrpr
 	 */ 
  	function &subtract( &$model )
	{ 
		if ( !is_a( $model, "RdfMemoryModel" ) ) 
		{
			$errmsg = "RDFAPI error (class: RdfMemoryModel; method: subtract): Model expected.";
			trigger_error( $errmsg, E_USER_ERROR ); 
		}
	
  		$res = $this;
		$res->indexed = false;
	
    	if ( is_a( $model, "RdfMemoryModel" ) ) 
		{
       		foreach ( $model->triples as $value )
		 		$res->remove( $value );
    	}

		return $res;
  	}

 	/** 
  	 * Returns a new RdfMemoryModel containing all the statements which are in both this RdfMemoryModel and another.
  	 *
  	 * @param	object Model	$model
  	 * @return	object RdfMemoryModel
  	 * @access	public
  	 * @throws  phpErrpr
 	 */ 
  	function &intersect( &$model )
  	{
		if ( !is_a( $model, "RdfMemoryModel" ) )
		{
			$errmsg = "RDFAPI error (class: RdfMemoryModel; method: intersect: Model expected.";
			trigger_error( $errmsg, E_USER_ERROR ); 
		}
	
  		$res = new RdfMemoryModel( $this->getBaseURI() );
	
    	if ( is_a( $model, "RdfMemoryModel" ) ) 
		{
       		foreach ( $model->triples as $value ) 
			{
		 		if ( $this->contains( $value ) )
		 			$res->add( $value );
       		}
    	}

		return $res;
  	}
  
 	/** 
  	 * Adds another model to this RdfMemoryModel.
  	 * Duplicate statements are not removed. 
  	 * If you don't want duplicates, use unite().
 	 *
  	 * @param	object Model	$model 
  	 * @access	public
  	 * @throws  phpErrpr
  	 */
  	function addModel( &$model )
	{ 
		if ( !is_a( $model, "RdfMemoryModel" ) ) 
		{
			$errmsg = "RDFAPI error (class: RdfMemoryModel; method: addModel): Model expected.";
			trigger_error( $errmsg, E_USER_ERROR ); 
		}

    	if ( is_a( $model, "RdfMemoryModel" ) ) 
		{
       		foreach ( $model->triples as $value )
  	     		$this->add(	$value	);
    	}
  	}
  
  	/**
   	 * Reifies the RdfMemoryModel.
   	 * Returns a new RdfMemoryModel that contains the reifications of all statements of this RdfMemoryModel.
   	 * 
   	 * @access	public 
   	 * @return	object	RdfMemoryModel
   	 */  	
	function &reify()
	{
	 	$res = new RdfMemoryModel( $this->getBaseURI() );
		
	    foreach ( $this->triples as $statement )
		{
			$pointer =& $statement->reify( $res );
			$res->addModel( $pointer ); 
		}
		
		return $res;
  	}

  	/**
   	 * Returns a RdfStatementIterator for traversing the RdfMemoryModel.
	 *
   	 * @access	public 
   	 * @return	object	RdfStatementIterator
   	 */  
	function & getStatementIterator()
	{
		return new RdfStatementIterator( $this );
    }
	
	/**
  	 * Close the RdfMemoryModel and free up resources held.
   	 * 
   	 * @access	public 
  	 */  
   	function close()
	{
		unset( $baseURI );
		unset( $triples );
	}
	
	/**
	 * Return current baseURI.
	 *
	 * @return  string
	 * @access	public
	 */
 	function getBaseURI() 
	{
		return $this->baseURI;
 	}
} // END OF RdfMemoryModel

?>
