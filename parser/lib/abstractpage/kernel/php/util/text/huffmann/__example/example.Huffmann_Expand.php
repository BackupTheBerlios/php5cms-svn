<?php

set_time_limit( 60 );


require( '../../../../../../prepend.php' );

using( 'util.text.huffmann.Huffmann' );
using( 'util.text.huffmann.Huffmann_Node' );
using( 'util.text.huffmann.Huffmann_Compress' );
using( 'util.text.huffmann.Huffmann_Expand' );


$expander = new Huffmann_Expand();
$expander->setFiles( "example_compressed.txt", "example_expanded.txt" );
$expander->expand();

?>
