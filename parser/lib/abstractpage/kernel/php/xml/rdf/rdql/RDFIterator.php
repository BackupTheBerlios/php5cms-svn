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
 * Abstract class defining methods for an RDFIterator
 * The RDF iterator is used by the RDQLQuery class, the iterator
 * MUST provide a find_tuples($sources,$subject,$predicate,$object)
 * method that returns all the tuples matching subject, predicate and object
 * from the designated sources (The FROM part of a RDQL expresion).
 *
 * @package xml_rdf_rdql
 */
 
class RDFIterator extends PEAR
{
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
	}
  
	/**
	 * @access public
	 */
	function tuple_match( $condition, $tuple )
	{
		if ( $condition{0} == '?' )
		{
			return true; 
		}
		else
		{
			if ( trim( $condition ) == trim( $tuple ) )
				return true;
			else
				return false;
		}
	}
} // END OF RDFIterator

?>
