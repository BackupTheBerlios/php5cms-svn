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
|Authors: Daniel Westphal <dawe@gmx.de>                                |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'xml.rdf.lib.util.Resource' );


/**
 * Resource Description Framework (RDF) Vocabulary
 *
 * Wrapper, defining resources for all terms of the  
 * Resource Description Framework (RDF). 
 * For details about RDF see: http://www.w3.org/RDF/.
 * Using the wrapper allows you to define all aspects of 
 * the vocabulary in one spot, simplifing implementation and 
 * maintainence. Working with the vocabulary, you should use 
 * these resources as shortcuts in your code.
 *
 * @package xml_rdf_lib_vocabulary
 */

$RDF_Alt             = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_ALT                 );
$RDF_Bag             = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_BAG                 ); 
$RDF_Property        = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_PROPERTY            ); 
$RDF_Seq             = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_SEQ                 ); 
$RDF_Statement       = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_STATEMENT           ); 
$RDF_List            = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_LIST                ); 
$RDF_nil             = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_NIL                 ); 
$RDF_type            = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_TYPE                ); 
$RDF_rest            = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_REST                ); 
$RDF_first           = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_FIRST               ); 
$RDF_subject         = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_SUBJECT             ); 
$RDF_predicate       = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_PREDICATE           ); 
$RDF_object          = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_OBJECT              ); 
$RDF_Description     = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_DESCRIPTION         );
$RDFAPI_ID           = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_ID                  );
$RDF_about           = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_ABOUT               );
$RDF_aboutEach       = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_ABOUT_EACH          );
$RDF_aboutEachPrefix = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_ABOUT_EACH_PREFIX   );
$RDF_bagID           = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_BAG_ID              );
$RDF_resource        = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_RESOURCE            );
$RDF_parseType       = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_PARSE_TYPE          );
$RDF_Literal         = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_PARSE_TYPE_LITERAL  );
$RDF_Resource        = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_PARSE_TYPE_RESOURCE );
$RDF_li              = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_LI                  );
$RDF_nodeID          = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_NODEID              );
$RDF_datatype        = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_DATATYPE            );
$RDF_seeAlso         = new Resource( RDFAPI_NAMESPACE_URI . RDFAPI_SEEALSO             );

?>
