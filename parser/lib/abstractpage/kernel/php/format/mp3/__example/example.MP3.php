<?php

require( '../../../../../prepend.php' );

using( 'format.mp3.MP3' );


$mp3 = new MP3( "./futurama.mp3" );
$mp3->get_id3();
$mp3->get_info();

while ( list( $key, $val ) = each( $mp3->info ) )
	echo $key . ": " . $val . "<br>\n";

?>
