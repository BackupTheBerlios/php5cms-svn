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


using( 'xml.rdf.lib.util.Resource' );


// define DC namespace
define( "DC_NS", "http://purl.org/dc/elements/1.0/" );


/**
 * Dublin Core Vocabulary
 *
 * Wrapper, defining resources for all terms of the Dublin 
 * Core Vocabulary. For details about DC see: http://dublincore.org/
 * Using the wrapper allows you to define all aspects of 
 * the vocabulary in one spot, simplifing implementation and 
 * maintainence. Working with the vocabulary, you should use 
 * these resources as shortcuts in your code.
 *
 * @package xml_rdf_lib_vocabulary
 */

// DC concepts
$DC_contributor = new Resource( DC_NS . "contributor" );
$DC_coverage    = new Resource( DC_NS . "coverage"    );
$DC_creator     = new Resource( DC_NS . "creator"     );
$DC_date        = new Resource( DC_NS . "date"        );
$DC_description = new Resource( DC_NS . "description" );
$DC_format      = new Resource( DC_NS . "format"      );
$DC_identifier  = new Resource( DC_NS . "identifier"  );
$DC_language    = new Resource( DC_NS . "language"    );
$DC_publisher   = new Resource( DC_NS . "publisher"   );
$DC_rights      = new Resource( DC_NS . "rights"      );
$DC_source      = new Resource( DC_NS . "source"      );
$DC_subject     = new Resource( DC_NS . "subject"     );
$DC_title       = new Resource( DC_NS . "title"       );
$DC_type        = new Resource( DC_NS . "type"        );

?>
