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

 
define( "RDFUTIL_HTML_TABLE_HEADER_COLOR",   "#FFFFFF" );
define( "RDFUTIL_HTML_TABLE_RESOURCE_COLOR", "#FFFFCC" );
define( "RDFUTIL_HTML_TABLE_LITERAL_COLOR",  "#E7E7EF" );
define( "RDFUTIL_HTML_TABLE_BNODE_COLOR",    "#FFCCFF" );
define( "RDFUTIL_HTML_TABLE_RDF_NS_COLOR",   "#CCFFCC" );


/**
 * @package xml_rdf
 */
 
class RdfUtil
{    
  	/**
	 * Extracts the namespace prefix out of a URI.
   	 *
   	 * @param	string	$uri
   	 * @return	string
   	 * @access	public
   	 */
   	function guessNamespace( $uri ) 
	{
      	$l = RdfUtil::getNamespaceEnd( $uri );
        return ( $l > 1 )? substr( $uri ,0, $l ) : "";
   }

  	/**
   	 * Delivers the name out of the URI (without the namespace prefix).
   	 *
   	 * @param	string	$uri
   	 * @return	string
   	 * @access	public
   	 */
   	function guessName( $uri ) 
	{
    	return substr( $uri, RdfUtil::getNamespaceEnd( $uri ) );
    }

  	/**
   	 * Extracts the namespace prefix out of the URI of a Resource.
   	 *
   	 * @param	Object Resource	$resource
   	 * @return	string
   	 * @access	public
   	 */
   	function getNamespace( $resource ) 
	{
      	return RdfUtil::guessNamespace( $resource->getURI() );
    }

  	/**
   	 * Delivers the Localname (without the namespace prefix) out of the URI of a Resource.
   	 *
   	 * @param	Object Resource	$resource
   	 * @return	string
   	 * @access	public
   	 */
   	function getLocalName( $resource ) 
	{
      	return RdfUtil::guessName( $resource->getURI() );
    }

    /**
     * Position of the namespace end 
	 * Method looks for # : and /
	 *
     * @param	String	$uri
     * @access	private
     */
    function getNamespaceEnd( $uri ) 
	{
      	$l = strlen( $uri ) - 1;
		
        do 
		{
           	$c = substr( $uri, $l, 1 );
			
            if ( $c == '#' || $c == ':' || $c == '/' )
                break;
				
            $l--;
        } while ( $l >= 0 );
		
        $l++;
        return $l;
    }	
	
  	/**
   	 * Tests if the URI of a resource belongs to the RDF syntax/model namespace.
   	 *
   	 * @param	Object Resource	$resource
   	 * @return	boolean
   	 * @access	public
   	 */	
   	function isRDF( $resource ) 
	{
      	return ( $resource != null && RdfUtil::getNamespace( $resource ) == RDFAPI_NAMESPACE_URI );
   	}

  	/**
   	 * Escapes < > and & 
   	 *
   	 * @param	String	$textValue
   	 * @return	String
   	 * @access	public
   	 */	
   	function escapeValue( $textValue ) 
	{
      	$textValue = str_replace( "<", "&lt;",  $textValue );
		$textValue = str_replace( ">", "&gt;",  $textValue );
		$textValue = str_replace( "&", "&amp;", $textValue );
		
		return $textValue;
    }

	/**
     * Converts an ordinal RDF resource to an integer.
     * e.g. Resource(RDF:_1) => 1
     *
     * @param	object Resource	$resource
     * @return	Integer
     * @access	public
	 */
     function getOrd( $resource )
	 {
        if ( $resource == null || !is_a( $resource, "Resource" ) || !RdfUtil::isRDF( $resource ) )
            return -1;
        
		$name = RdfUtil::getLocalName( $resource );
        echo substr( $name, 1 ) . " " . RdfUtil::getLocalName( $resource );
		$n = substr( $name, 1 );

		return $n;  
     }

	/**
     * Creates ordinal RDF resource out of an integer.
     *
     * @param	Integer	$num
     * @return	object Resource
     * @access	public
	 */
    function createOrd( $num ) 
	{
		return new Resource( RDFAPI_NAMESPACE_URI . "_" . $num );
    }

	/**
     * Prints a MemModel as HTML table.
	 * You can change the colors in the configuration file.
     *
     * @param	object MemModel 	&$model
     * @access	public
	 */
    function writeHTMLTable( &$model )
	{    
		echo "<table border=\"1\" cellpadding=\"3\" cellspacing=\"0\" width=\"100%\">" . RDFAPI_LINEFEED;
	    echo RDFAPI_INDENTATION . "<tr bgcolor=\"" . RDFUTIL_HTML_TABLE_HEADER_COLOR . "\">" . RDFAPI_LINEFEED . RDFAPI_INDENTATION . RDFAPI_INDENTATION . "<td td width=\"68%\" colspan=\"3\">" ; 
	    echo "<p><b>Base URI:</b> " .$model->getBaseURI() . "</p></td>" . RDFAPI_LINEFEED;
	    echo RDFAPI_INDENTATION . RDFAPI_INDENTATION . "<td width=\"32%\"><p><b>Size:</b> " . $model->size() . "</p></td>" .RDFAPI_LINEFEED .RDFAPI_INDENTATION . "</tr>";
	    echo RDFAPI_INDENTATION . "<tr bgcolor=\"" . RDFUTIL_HTML_TABLE_HEADER_COLOR . "\">" . RDFAPI_LINEFEED . RDFAPI_INDENTATION . RDFAPI_INDENTATION . "<td width=\"4%\"><p align=center><b>No.</b></p></td>" . RDFAPI_LINEFEED . RDFAPI_INDENTATION . RDFAPI_INDENTATION . "<td width=\"32%\"><p><b>Subject</b></p></td>" . RDFAPI_LINEFEED . RDFAPI_INDENTATION . RDFAPI_INDENTATION . "<td width=\"32%\"><p><b>Predicate</b></p></td>" . RDFAPI_LINEFEED . RDFAPI_INDENTATION . RDFAPI_INDENTATION . "<td width=\"32%\"><p><b>Object</b></p></td>" . RDFAPI_LINEFEED . RDFAPI_INDENTATION . "</tr>" . RDFAPI_LINEFEED;
	    
		$i = 1;
		foreach ( $model->triples as $key => $statement )
		{
			echo RDFAPI_INDENTATION . "<tr valign=\"top\">" . RDFAPI_LINEFEED . RDFAPI_INDENTATION . RDFAPI_INDENTATION . "<td><p align=center>" . $i .".</p></td>" . RDFAPI_LINEFEED;
	    	
			// subject
			echo RDFAPI_INDENTATION . RDFAPI_INDENTATION . "<td bgcolor=\""; 
			echo RdfUtil::chooseColor( $statement->getSubject() );
			echo "\">"; 
	        echo "<p>" .  RdfUtil::getNodeTypeName( $statement->getSubject() ) . $statement->subj->getLabel() . "</p></td>" .  RDFAPI_LINEFEED;

	    	// predicate
			echo RDFAPI_INDENTATION . RDFAPI_INDENTATION . "<td bgcolor=\""; 
			echo RdfUtil::chooseColor( $statement->getPredicate() );
			echo "\">"; 
	        echo "<p>" . RdfUtil::getNodeTypeName( $statement->getPredicate() ) . $statement->pred->getLabel() . "</p></td>" .  RDFAPI_LINEFEED;	    

	    	// object
			echo RDFAPI_INDENTATION . RDFAPI_INDENTATION . "<td bgcolor=\""; 
			echo RdfUtil::chooseColor( $statement->getObject() );
			echo "\">"; 
	        echo "<p>";
			
			if ( is_a( $statement->getObject(), "Literal" ) ) 
			{ 
			   	if ( $statement->obj->getLanguage() != null )	
					$lang =  " <b>(xml:lang=\"" . $statement->obj->getLanguage() . "\") </b> "; 
				else 
					$lang = "";
					
			   	if ( $statement->obj->getDatatype() != null ) 	
					$dtype =  " <b>(rdf:datatype=\"" . $statement->obj->getDatatype() . "\") </b> "; 
				else 
					$dtype ="";
			}
			else 
			{
				$lang  = "";		
				$dtype = "";
			}
			
			echo RdfUtil::getNodeTypeName( $statement->getObject() ) . $statement->obj->getLabel() . $lang . $dtype;
			echo  "</p></td>" . RDFAPI_LINEFEED;
	  		echo RDFAPI_INDENTATION . "</tr>" . RDFAPI_LINEFEED;
	  		
			$i++;
	  	}
	  	
		echo "</table>" . RDFAPI_LINEFEED;
	}

	/**
     * Chooses a node color.
     * Used by RdfUtil::writeHTMLTable()
	 *
     * @param	object Node	$node
     * @return	object Resource
     * @access	private
	 */
    function chooseColor( $node )
	{
		if ( is_a( $node, "BlankNode" ) )
		{
			return RDFUTIL_HTML_TABLE_BNODE_COLOR;
		}
		else if ( is_a( $node, "Literal" ) )
		{
			return RDFUTIL_HTML_TABLE_LITERAL_COLOR;
		}
		else 
		{
			if ( RdfUtil::getNamespace( $node ) == RDFAPI_NAMESPACE_URI || RdfUtil::getNamespace( $node ) == RDFAPI_SCHEMA_URI )
				return RDFUTIL_HTML_TABLE_RDF_NS_COLOR;
		}
		
		return RDFUTIL_HTML_TABLE_RESOURCE_COLOR;
    }

	/**
     * Get Node Type.
     * Used by RdfUtil::writeHTMLTable()
	 *
     * @param	object Node	$node
     * @return	object Resource
     * @access	private
	 */
    function getNodeTypeName( $node )
	{
		if ( is_a( $node, "BlankNode" ) )
		{
			return "Blank Node: ";
		}
		else if ( is_a( $node, "Literal" ) )
		{
			return "Literal: ";
		}
		else
		{
			if ( RdfUtil::getNamespace( $node ) == RDFAPI_NAMESPACE_URI || RdfUtil::getNamespace( $node ) == RDFAPI_SCHEMA_URI )
				return "RDF Node: ";
		}
		
		return "Resource: ";		
    }
} // end: RDfUtil

?>
