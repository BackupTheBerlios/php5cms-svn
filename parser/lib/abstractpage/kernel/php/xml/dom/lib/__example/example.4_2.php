<?php

require( '../../../../../../prepend.php' );

using( 'xml.dom.lib.*' );


$doc = new Document;
$doc->createFromFile( "sample.xml" );

// getting the xpath context
$xpath = $doc->getXPathContext();

// retrieving a NodeList of elements with the given id
$nodes = $xpath->evaluate( "//child::*[@id=\"HTML\"]" );

if ( $nodes->getLength( ) > 0 )
{
	$term = $nodes->item( 0 );
	$term->setNodeValue( strtoupper( $term->getNodeValue() ) );
}

// printing the Document
$doc->printDocument();

?>
