<?php

require( '../../../../../../prepend.php' );

using( 'xml.dom.lib.*' );


$doc = new Document;
$doc->createFromFile( "book.xml" );
    
// getting the xpath context
$xpath = $doc->getXPathContext();

// retrieving a NodeList of elements
$nodes = $xpath->evaluate( "//child::headline" );
for ( $i = 0; $i < $nodes->getLength(); $i++ )
{
	$headline = $nodes->item( $i );
	$headline->setNodeValue( $headline->getNodeValue() . " Changed " . time() );
}
	
// printing the Document
$doc->printDocument();

?>