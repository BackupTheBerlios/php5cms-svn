<?php

require( '../../../../../../../prepend.php' );

using( 'util.aviation.icao.stations.Stations' );


$st = &Stations::factory( "de" );

echo( "<pre>\n" );
echo( $st->country . "<br>\n" );
echo( print_r( $st->icaos ) );
echo( "</pre>" );

?>
