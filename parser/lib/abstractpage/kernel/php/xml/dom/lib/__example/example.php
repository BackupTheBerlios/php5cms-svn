<?php

require( '../../../../../../prepend.php' );

using( 'xml.dom.lib.*' );


$doc = new Document;

$doc->create( "root" );
$root = $doc->getDocumentElement();
$child1 = $root->appendChild( new Element( "child" ));
$child2 = $root->appendChild( new Element( "child" ));
	
// setting Attributes and Content
$child1->setAttribute( "attrname", "attrvalue" );
$child1->setNodeValue( "Content of the node Child!" );

// printing the Document
$doc->printDocument();

?>
