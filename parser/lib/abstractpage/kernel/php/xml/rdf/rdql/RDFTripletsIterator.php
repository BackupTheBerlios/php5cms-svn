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


/**
 * This is an iterator for RDF triplets the sources in the FROM part
 * of the RDQL expression must be PHP vars in the form $var
 *
 * @package xml_rdf_rdql
 */
 
class RDFTripletsIterator extends RDFIterator
{
	/**
	 * @access public
	 */
	var $cosa;
	
	/**
	 * @access public
	 */
	var $index;
	
	/**
	 * @access public
	 */
	var $tuples;
	
	
	/**
	 * @access public
	 */
	function init()
	{
		$this->index = 0;
	}

	/**
	 * @access public
	 */
	function find_tuples( $sources, $subject, $predicate, $object )
	{
		$ret = array();
		$this->init();
		
		$elems[0] = $subject;
		$elems[1] = $predicate;
		$elems[2] = $object;
		
		foreach ( $sources as $source )
		{
			// remove '$' from source
			preg_match( "/\<([^>]*)\>/", $source, $reqs );
			$source = $reqs[1];
      
	  		if ( $source{0} == '$' )
				$source = substr( $source, 1 ); 
      
			global $$source;
			
			$this->tuples = $$source;
			$this->init();
			
			while ( $tuple = $this->get_tuple() )
			{
				if ( $this->tuple_match( $elems[0], $tuple[0] ) && $this->tuple_match( $elems[1], $tuple[1] ) && $this->tuple_match( $elems[2], $tuple[2] ) )
				{
          			$result = array();
					
					for ( $i = 0; $i < 3; $i++ )
					{
						if ( $elems[$i]{0} == '?' )
							$result[$elems[$i]]= $tuple[$i];
					}
					
					if ( count( result ) > 0 )
						$ret[] = $result;
				}
			}
		}

		return $ret; 
	}

	/**
	 * @access public
	 */
	function get_tuple()
	{
    	if ( $this->index >= count( $this->tuples ) )
			return false; 
   
		$elem = $this->tuples[$this->index];
		$this->index++;
		
		return $elem;
	}
} // END OF RDFTripletsIterator

?>
