<?php

require( '../../../../prepend.php' );

using( 'html.WordHTML' );


if ( !function_exists( "file_get_contents" ) )
{
	function file_get_contents( $name )
	{
		return implode( '', file( $name ) );
	}
}

$wd = new WordHTML;
$orig  = file_get_contents( "example.htm" );
$clean = $wd->clean( $orig );

echo $clean;

?>
