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


// define OWL namespace
define( "OWL_NS", "http://www.w3.org/2002/07/owl" );


/**
 * OWL Vocabulary
 *
 * Wrapper, defining resources for all concepts of the Web 
 * Ontology Language (OWL). For details about OWL see: 
 * http://www.w3.org/TR/owl-ref/
 * Using the wrapper allows you to define all aspects of 
 * the language in one spot, simplifing implementation and 
 * maintainence. Working with the vocabulary, you should use 
 * these resources as shortcuts in your code.
 *
 * @package xml_rdf_lib_vocabulary
 */

// OWL concepts
$OWL_allValuesFrom             = new Resource( OWL_NS . "allValuesFrom"             );
$OWL_cardinality               = new Resource( OWL_NS . "cardinality"               );
$OWL_Class                     = new Resource( OWL_NS . "Class"                     );
$OWL_complementOf              = new Resource( OWL_NS . "complementOf"              );
$OWL_Datatype                  = new Resource( OWL_NS . "Datatype"                  );
$OWL_DatatypeProperty          = new Resource( OWL_NS . "DatatypeProperty"          );
$OWL_DatatypeRestriction       = new Resource( OWL_NS . "DatatypeRestriction"       );
$OWL_differentFrom             = new Resource( OWL_NS . "differentFrom"             );
$OWL_disjointWith              = new Resource( OWL_NS . "disjointWith"              );
$OWL_sameAs                    = new Resource( OWL_NS . "sameAs"                    );
$OWL_FunctionalProperty        = new Resource( OWL_NS . "FunctionalProperty"        );
$OWL_hasValue                  = new Resource( OWL_NS . "hasValue"                  );
$OWL_imports                   = new Resource( OWL_NS . "imports"                   );
$OWL_intersectionOf            = new Resource( OWL_NS . "intersectionOf"            );
$OWL_InverseFunctionalProperty = new Resource( OWL_NS . "InverseFunctionalProperty" );
$OWL_inverseOf                 = new Resource( OWL_NS . "inverseOf"                 );
$OWL_maxCardinality            = new Resource( OWL_NS . "maxCardinality"            );
$OWL_minCardinality            = new Resource( OWL_NS . "minCardinality"            );
$OWL_ObjectClass               = new Resource( OWL_NS . "ObjectClass"               );
$OWL_ObjectProperty            = new Resource( OWL_NS . "ObjectProperty"            );
$OWL_ObjectRestriction         = new Resource( OWL_NS . "ObjectRestriction"         );
$OWL_oneOf                     = new Resource( OWL_NS . "oneOf"                     );
$OWL_onProperty                = new Resource( OWL_NS . "onProperty"                );
$vOWL_Ontology                 = new Resource( OWL_NS . "Ontology"                  );
$OWL_Property                  = new Resource( OWL_NS . "Property"                  );
$vOWL_Restriction              = new Resource( OWL_NS . "Restriction"               );
$OWL_sameClassAs               = new Resource( OWL_NS . "sameClassAs"               );
$OWL_sameIndividualAs          = new Resource( OWL_NS . "sameIndividualAs"          );
$OWL_samePropertyAs            = new Resource( OWL_NS . "samePropertyAs"            );
$OWL_someValuesFrom            = new Resource( OWL_NS . "someValuesFrom"            );
$OWL_SymmetricProperty         = new Resource( OWL_NS . "SymmetricProperty"         );
$OWL_TransitiveProperty        = new Resource( OWL_NS . "TransitiveProperty"        );
$OWL_unionOf                   = new Resource( OWL_NS . "unionOf"                   );
$OWL_versionInfo               = new Resource( OWL_NS . "versionInfo"               );

?>
