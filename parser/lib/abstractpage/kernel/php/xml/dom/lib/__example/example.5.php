<?php

require( '../../../../../../prepend.php' );

using( 'xml.dom.lib.*' );


$doc = new Document;
    
$doc->createFromFile( "addressbook.xml" );

// retrieving one Element with the ID
$person = $doc->getElementById( "2" );
$book = $person->getParentNode();
    
// duplicating the Person with the ID = "2"
$new_person = $book->appendChild( $person->cloneNode( true ) );
    
$children = $new_person->getChildNodes();
$firstname = $children->item( 0 );
$lastname = $children->item( 1 );
$profession = $children->item( 2 );

// setting the values
$new_person->setId( "3" );
$firstname->setNodeValue( "Bruce" );
$lastname->setNodeValue( "Springsteen" );
$profession->setNodeValue( "Rock-Hero" );

// printing the Document
$doc->printDocument();

?>
