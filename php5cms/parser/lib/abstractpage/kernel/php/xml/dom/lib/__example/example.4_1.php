<?php

require( '../../../../../../prepend.php' );

using( 'xml.dom.lib.*' );


$doc = new Document;
$doc->createFromFile( "sample.xml" );

// retrieving one Element with the ID
$term = $doc->getElementById( "XML" );

if ( $term)
	$term->setNodeValue( strtoupper( $term->getNodeValue()) );

// printing the Document
$doc->printDocument();

?>
