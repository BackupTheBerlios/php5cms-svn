<?php

require( '../../../../../prepend.php' );

using( 'xml.xupdate.XUpdateParser' );


// create an instance of XUpdateParser
$xup =& new XUpdateParser();

// parse test.xml (containing valid XUpdate code), die if any error occurs
if ( ( $error = $xup->parseFile( 'test.xml' ) ) !== true )
   die( $error );

// get the resulting array containing the query data (the internal array is empty afterwards
// unless you don't provide a parameter. $xup->get(false) would prevent the internal array
// from being emptied.
$query = $xup->get();

// create a simple output of the resulting array
print( '<pre>' );
print_r( $query );
print( '</pre>' );

?>
