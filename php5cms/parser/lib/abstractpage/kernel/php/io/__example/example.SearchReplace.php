<?php

require( '../../../../prepend.php' );

using( 'io.SearchReplace' );


// Create the object, set the search function and run it.
// Then change the pattern to find something else, and re-run the search.
$sr = new SearchReplace( 'test', 'Replaced!', array( 'test.txt' ), '', 1, array( '##' ) );

// normal is the default, anyway...
$sr->setSearchFunction( 'normal' );
$sr->performSearch();
$sr->setFind( 'another' );
$sr->performSearch();

// Some ouput purely for the example.
header( 'Content-Type: text/plain' );
echo 'Number of occurences found: ' . $sr->getNumOccurences() . "\r\n";

?>
