<?php

require( '../../../../../../prepend.php' );

using( 'xml.dom.lib.*' );


$doc = new Document;
$doc->createFromFile( "book.xml" );

// getting the xpath context
$xpath = $doc->getXPathContext();
    
// retrieving a NodeList of text elements
$nodes = $xpath->evaluate( "//child::headline/text()" );

for ( $i = 0; $i < $nodes->getLength(); $i++ )
{
	$headline = $nodes->item( $i );
	$headline->appendData(" Changed since it is a Text element"  );
}

// printing the Document
$doc->printDocument();

?>
