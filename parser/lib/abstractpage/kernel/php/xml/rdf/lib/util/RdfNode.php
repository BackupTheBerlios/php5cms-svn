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


/**
 * An abstract RDF node. 
 * Can either be resource, literal or blank node. 
 * RdfNode is used in some comparisons like is_a( $obj, "RdfNode" ), 
 * meaning is $obj a resource, blank node or literal.
 * 
 * @package xml_rdf_lib_util
 */

class RdfNode
{
  	/**
   	 * Serializes a object into a string.
   	 *
   	 * @access	public
   	 * @return	string		
   	 */    
	function toString()
	{
		$objectvars = get_object_vars( $this );
		
		foreach ( $objectvars as $key => $value ) 
			$content = $content . $key ."='". $value. "'; ";
		
		return "Instance of " . get_class( $this ) . "; Properties: " . $content;
	}
} // END OF RdfNode

?>
