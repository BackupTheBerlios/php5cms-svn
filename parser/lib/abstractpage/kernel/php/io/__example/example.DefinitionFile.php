<?php

require( '../../../../prepend.php' );

using( 'io.DefinitionFile' );


$def  = new DefinitionFile();
$data = $def->load( "test.def" );

header( "Content-Type: text/plain" );
print_r( $data );

?>
