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


using( 'xml.rdf.rdql.RDFIterator' );
using( 'xml.rdf.RepatRDFParser' );


/**
 * This class implements an iterator for RDF documents
 * using URLs or filenames (paths) to locate the documents.
 *
 * @package xml_rdf_rdql
 */
 
class RDFDocumentIterator extends RDFIterator
{
	/**
	 * @access public
	 */
	var $rdf_parser;
	
	/**
	 * @access public
	 */
	var $subject;
	
	/**
	 * @access public
	 */
	var $object;
	
	/**
	 * @access public
	 */
	var $predicate;
	
	/**
	 * @access public
	 */
	var $tuples = array();
	
	
	/**
	 * @access public
	 */
	function init( $sources )
	{
	}
  
  	/**
	 * @access public
	 */
	function get_tuple()
	{
    }
  
  	/**
	 * @access public
	 */
	function find_tuples( $sources, $subject, $predicate, $object )
	{
		$this->subject   = $subject;
		$this->predicate = $predicate;
		$this->object    = $object;
		$this->tuples    = array(); 
		
		foreach ( $sources as $source )
		{
			preg_match("/\<([^>]*)\>/",$source,$reqs);
			$source=$reqs[1];
			
			$this->rdf_parser = new RepatRDFParser;
			$this->rdf_parser->rdf_parser_create( null );
			$this->rdf_parser->rdf_set_statement_handler( "_statement_handler" );
			$this->rdf_parser->rdf_set_user_data( $this );
			
			$input = fopen( $source, "r" );
			$done  = false;
			
			if ( !$input )
				$done = true; 
       
			$done = false;
			
			while ( !$done )
			{
				$buf  = fread( $input, 512 );
				$done = feof( $input );
				
				if ( !$this->rdf_parser->rdf_parse( $buf, strlen( $buf ), feof( $input ) ) )
					$done = true;
			} 
			
			fclose( $input );
			$this->rdf_parser->rdf_parser_free();
     	}

		return $this->tuples;     
	}
} // END OF RDFDocumentIterator


// This is the statement handler used by the RDF parser in the Document Iterator.
function _statement_handler( &$user_data, $subject_type, $subject, $predicate, $ordinal, $object_type, $object, $xml_lang )
{
	if ( $user_data->tuple_match( $user_data->subject,   $subject   ) &&
		 $user_data->tuple_match( $user_data->predicate, $predicate ) &&
		 $user_data->tuple_match( $user_data->object,    $object)   )
	{
		$result = array();
		
		if ( $user_data->subject{0} == '?' )
			$result[$user_data->subject] = $subject; 
    
		if ( $user_data->predicate{0} == '?' )
			$result[$user_data->predicate] = $predicate; 
    
		if ( $user_data->object{0} == '?' )
			$result[$user_data->object] = $object; 
    
		if ( count( $result ) > 0 )
			$user_data->tuples[]=$result;
	}
}

?>
