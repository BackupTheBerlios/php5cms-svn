<?php

require( '../../../../prepend.php' );

using( 'io.FolderArray' );


$fa = new FolderArray( AP_ROOT_PATH );
$fa->setRecursive = false;
$fa->setIgnore = array( ".", ".." );
$fa->parseDir();

print "<pre>";
print_r( $fa->getFolderArray() );  
print "</pre>";

?>
