<?php

require( '../../../../../prepend.php' );

using( 'services.google.GoogleClient' );


$licenceKey   = '00000000000000000000000000000000';
$searchString = 'PHP Classes';

$google = new GoogleClient( $licenceKey );

if ( $google->search( $searchString ) ) 
	print_r( $google->results );
else
	echo $google->error();

?>
