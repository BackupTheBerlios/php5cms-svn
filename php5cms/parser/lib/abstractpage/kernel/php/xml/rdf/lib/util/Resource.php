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


using( 'xml.rdf.lib.util.RdfNode' );
using( 'xml.rdf.RdfUtil' );


/**
 * An RDF resource. 
 * Every RDF resource must have a URIref.  
 * URIrefs are treated as logical constants, i.e. as names which denote something 
 * (the things are called 'resources', but no assumptions are made about the nature of resources.) 
 * Many RDF resources are pieces of vocabulary. They typically have a namespace
 * and a local name. In this case, a URI is composed as a
 * concatenation of the namespace and the local name.
 * 
 * @package xml_rdf_lib_util
 */ 
 
class Resource extends RdfNode
{
 	/**
	 * URIref to the resource
	 *
	 * @var		string
	 * @access	private
	 */	
    var $uri;
   
  
   	/**
     * Constructor
	 * Takes an URI or a namespace/localname combination
     *
     * @param	string	$namespace_or_uri
 	 * @param   string $localName
	 * @access	public
     */
    function Resource( $namespace_or_uri , $localName = null )
	{
		if ( $localName == null )
			$this->uri = $namespace_or_uri;
	  	else
			$this->uri = $namespace_or_uri . $localName;
	}

 
  	/**
   	 * Returns the URI of the resource.
	 *
   	 * @return  string
   	 * @access	public  
   	 */
  	function getURI()
	{
  		return $this->uri;
   	}

	/**
	 * Returns the label of the resource, which is the URI of the resource.
	 *
     * @access	public  
	 * @return  string 
	 */
    function getLabel()
	{
    	return $this->getURI();
    }
	   	
  	/**
   	 * Returns the namespace of the resource. May return null.
	 *
   	 * @access	public  
   	 * @return  string
   	 */
  	function getNamespace()
	{
    	return RdfUtil::guessNamespace( $this->uri );
  	}

  	/**
   	 * Returns the local name of the resource.
	 *
   	 * @access	public  
   	 * @return  string
   	 */
    function getLocalName()
	{
	 	return RdfUtil::guessName( $this->uri );
  	}
    
  	/**
   	 * Dumps resource.
	 *
   	 * @access	public  
   	 * @return  string
   	 */
  	function toString()
	{
		return "Resource(\"" . $this->uri . "\")";
  	}

  	/**
   	 * Checks if the resource equals another resource.
   	 * Two resources are equal, if they have the same URI.
   	 *
   	 * @access	public 
   	 * @param		object	resource $that
   	 * @return	boolean 
   	 */  
   	function equals( $that )
	{
	    if ( $this == $that )
	      	return true;
	    
	    if ( ( $that == null ) || !( is_a( $that, "Resource" ) ) )
	      	return false;
	    
		if ( $this->getURI() == $that->getURI() )
	      	return true;
	
	    return false;
	}
} // END OF Resource

?>
