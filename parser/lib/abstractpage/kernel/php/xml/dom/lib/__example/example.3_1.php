<?php

require( '../../../../../../prepend.php' );

using( 'xml.dom.lib.*' );


$doc = new Document;
$doc->createFromFile( "book.xml" );

// retrieving a NodeList of elements
$nodes = $doc->getElementsByTagName( "headline" );
for ( $i = 0; $i < $nodes->getLength(); $i++ )
{
	$headline = $nodes->item( $i );
	$headline->setNodeValue( $headline->getNodeValue() . " Changed " . time() );
}

// printing the Document
$doc->printDocument();

?>
