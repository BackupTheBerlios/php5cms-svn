<?php

set_time_limit( 60 );


require( '../../../../../../prepend.php' );

using( 'util.text.huffmann.Huffmann' );
using( 'util.text.huffmann.Huffmann_Node' );
using( 'util.text.huffmann.Huffmann_Compress' );
using( 'util.text.huffmann.Huffmann_Expand' );


$compressor = new Huffmann_Compress();
$compressor->setFiles( "example_orig.txt", "example_compressed.txt" );
$compressor->compress();

?>
