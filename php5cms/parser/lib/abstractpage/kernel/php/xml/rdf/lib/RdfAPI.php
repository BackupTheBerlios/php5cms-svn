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


// include vocabularies
// for a better performance, you should comment vocabularies out, that you are not using.
using( 'xml.rdf.lib.vocabulary.Vocabulary_RDF' );
using( 'xml.rdf.lib.vocabulary.Vocabulary_RDFS' );
using( 'xml.rdf.lib.vocabulary.Vocabulary_OWL' );
using( 'xml.rdf.lib.vocabulary.Vocabulary_DC' );
using( 'xml.rdf.lib.vocabulary.Vocabulary_VCARD' );


define( "RDFAPI_NAMESPACE_URI", "http://www.w3.org/1999/02/22-rdf-syntax-ns#" );
define( "RDFAPI_RDF", "RDF" );
define( "RDFAPI_DESCRIPTION", "Description" );
define( "RDFAPI_ID", "ID" );
define( "RDFAPI_ABOUT", "about" );
define( "RDFAPI_ABOUT_EACH", "aboutEach" );
define( "RDFAPI_ABOUT_EACH_PREFIX", "aboutEachPrefix" );
define( "RDFAPI_BAG_ID", "bagID" );
define( "RDFAPI_RESOURCE", "resource" );
define( "RDFAPI_PARSE_TYPE", "parseType" );
define( "RDFAPI_PARSE_TYPE_LITERAL", "Literal" );
define( "RDFAPI_PARSE_TYPE_RESOURCE", "Resource" );
define( "RDFAPI_TYPE", "type" );
define( "RDFAPI_BAG", "Bag" );
define( "RDFAPI_SEQ", "Seq" );
define( "RDFAPI_ALT", "Alt" );
define( "RDFAPI_LI", "li" );
define( "RDFAPI_STATEMENT", "Statement" );
define( "RDFAPI_SUBJECT", "subject" );
define( "RDFAPI_PREDICATE", "predicate");
define( "RDFAPI_OBJECT", "object" );
define( "RDFAPI_NODEID", "nodeID" );
define( "RDFAPI_DATATYPE", "datatype" );
define( "RDFAPI_SEEALSO", "seeAlso" );
define( "RDFAPI_PROPERTY", "Property" );
define( "RDFAPI_LIST", "List" );
define( "RDFAPI_NIL", "nil" );
define( "RDFAPI_REST", "rest" );
define( "RDFAPI_FIRST", "first" );
define( "RDFAPI_INDENTATION", "   " );
define( "RDFAPI_LINEFEED", chr( 10 ) );
define( "RDFAPI_BNODE_PREFIX", "bNode" );
define( "RDFAPI_SCHEMA_URI", "http://www.w3.org/2000/01/rdf-schema#" );
define( "RDFAPI_XML_LANG", "lang" );


/**
 * @package xml_rdf_lib
 */
 
class RdfAPI extends PEAR
{
} // END OF RdfAPI

?>
