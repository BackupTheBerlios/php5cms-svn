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
 * RDF Vocabulary Description Language 1.0: RDF Schema (RDFS) Vocabulary
 *
 * Wrapper, defining resources for all terms of the  
 * RDF Schema (RDFS). 
 * For details about RDF see: http://www.w3.org/TR/rdf-schema/
 * Using the wrapper allows you to define all aspects of 
 * the vocabulary in one spot, simplifing implementation and 
 * maintainence. Working with the vocabulary, you should use 
 * these resources as shortcuts in your code.
 *
 * @package xml_rdf_lib_vocabulary
 */

// RDFS concepts
$RDFS_Resource                    = new Resource( RDFAPI_SCHEMA_URI . "Resource"                    );
$RDFS_Literal                     = new Resource( RDFAPI_SCHEMA_URI . "Literal"                     );
$RDFS_Class                       = new Resource( RDFAPI_SCHEMA_URI . "Class"                       );
$RDFS_Datatype                    = new Resource( RDFAPI_SCHEMA_URI . "Datatype"                    );
$RDFS_Container                   = new Resource( RDFAPI_SCHEMA_URI . "Container"                   );
$RDFS_ContainerMembershipProperty = new Resource( RDFAPI_SCHEMA_URI . "ContainerMembershipProperty" );
$RDFS_subClassOf                  = new Resource( RDFAPI_SCHEMA_URI . "subClassOf"                  );
$RDFS_subPropertyOf               = new Resource( RDFAPI_SCHEMA_URI . "subPropertyOf"               );
$RDFS_domain                      = new Resource( RDFAPI_SCHEMA_URI . "domain"                      );
$RDFS_range                       = new Resource( RDFAPI_SCHEMA_URI . "range"                       );
$RDFS_label                       = new Resource( RDFAPI_SCHEMA_URI . "label"                       );
$RDFS_comment                     = new Resource( RDFAPI_SCHEMA_URI . "comment"                     );
$RDFS_member                      = new Resource( RDFAPI_SCHEMA_URI . "member"                      );
$RDFS_seeAlso                     = new Resource( RDFAPI_SCHEMA_URI . "seeAlso"                     );
$RDFS_isDefinedBy                 = new Resource( RDFAPI_SCHEMA_URI . "isDefinedBy"                 );

?>
