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


using( 'xml.rdf.rdql.RDFDocumentIterator' );
using( 'xml.rdf.rdql.RDQLQuery' );


/**
 * A wrapper class to Query RDF documents.
 *
 * @package xml_rdf_rdql
 */
 
class RDQLQueryDocument extends PEAR
{
	/**
	 * Queries documents passed as urls or filenames (use urls or filenames in the FROM part of the RDQL query).
	 *
	 * @access public
	 * @static
	 */
	function rdql_query_url( $query )              
	{
		$it  = new RDFDocumentIterator;
		$q   = new RDQLQuery( $it );
		$res = $q->parse_query( $query );

		return $res;
	}
} // END OF RDQLQueryDocument

?>
