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
|         Radoslaw Oldakowski <radol@gmx.de>                           |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'xml.rdf.lib.util.Resource' );


/**
 * An RDF blank node. 
 * In model theory, blank nodes are considered to be drawn from some set of 
 * 'anonymous' entities which have no label but are unique to the graph.
 * For serialization they are labeled with a URI or a _:X identifier.
 *
 * @package xml_rdf_lib_util
 */ 

class BlankNode extends Resource
{  
   /**
    * Constructor
	* You can supply a label or You supply a model and a unique ID is gernerated
    *
    * @param	mixed	$namespace_or_uri_or_model
 	* @param 	string $localName
	* @access	public
    * @todo     nothing
    */
    function BlankNode( $namespace_or_uri_or_model , $localName = null )
	{	
		if ( is_a( $namespace_or_uri_or_model, "RdfMemoryModel" ) )
		{
			// generate identifier
			$id = $namespace_or_uri_or_model->getUniqueResourceURI( RDFAPI_BNODE_PREFIX );
			$this->uri = $id;
		}
		else
		{
			// set identifier
			if ( $localName == null )
				$this->uri = $namespace_or_uri_or_model;
		  	else
				$this->uri = $namespace_or_uri_or_model . $localName;
		}
    }

  	/**
  	 * Returns the ID of the blank node.
	 *
  	 * @return 	string
  	 * @access	public  
  	 */	
  	function getID()
	{
  		return $this->uri;
   	}

  	/**
  	 * Returns the ID of the blank node.
	 *
  	 * @return 	string
  	 * @access	public  
  	 */	
  	function getLabel()
	{
  		return $this->uri;
   	}

  	/**
   	 * Dumps bNode.
   	 *
   	 * @access	public 
   	 * @return	string 
   	 */  
  	function toString()
	{
		return "bNode(\"" . $this->uri . "\")";
  	}
	
  	/**
  	 * Checks if two blank nodes are equal.
  	 * Two blank nodes are equal, if they have the same temporary ID
  	 *
  	 * @access	public 
  	 * @param		object	resource $that
  	 * @return	boolean 
  	 */  
   	function equals( $that )
	{
	    if ( $this == $that )
	      	return true;
	    
	    if ( ( $that == null ) || !( is_a( $that, "BlankNode" ) ) )
	      	return false;
	    	
		if ( $this->getURI() == $that->getURI() )
	      	return true;
	
	    return false;
	}
} // END OF BlankNode 

?>
