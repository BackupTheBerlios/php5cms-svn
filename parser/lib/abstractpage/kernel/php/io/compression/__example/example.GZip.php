<?php

require( '../../../../../prepend.php' );

using( 'io.compression.GZip' );


$path     = "test.gz";
$filedata = "some file content";

$gz = new GZip;


$gz->add( $filedata, $path );
$gz->write_file( $path );

if ( $g = $gz->extract( $path ) )
{
	// print result structure
  	print_r( $g );
}

?>
