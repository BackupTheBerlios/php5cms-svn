<?php

require( '../../../../../../prepend.php' );

using( 'xml.dom.lib.*' );


header( "Content-type: text/xml" );

$doc = new Document;
$doc->createFromFile( "book.xml" );

// retrieving the root element
$book = $doc->getDocumentElement();
$number = 0;
	
// retrieving all chapters as a nodelist
if ( $nodelist	= $book->getElementsByTagName( "chapter" ) )
{
	// take the last chapter from the nodelist
	$chapter = $nodelist->item( $nodelist->getLength() - 1 );
    $number	= (int) $chapter->getAttribute( "number" );
}

$chapter = $book->appendChild( new Element( "chapter" ) );
$chapter->setAttribute( "number", ++$number );
$headline = $chapter->appendChild( new Element( "headline", "Constraining XML" ) );
$content  = $chapter->appendChild( new Element( "content", "This is the content of the chapter ". $number . "!" ) );
	
// printing the Document
$doc->printDocument();

?>
