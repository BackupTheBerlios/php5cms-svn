<?php

require( '../../../../../../prepend.php' );

using( 'peer.http.agent.BrowseCap' );


if ( $_GET['apRun'] == 1 )
{
	$bc = new BrowseCap;
	$bc->compute();

	echo "<pre>";
	print_r( $bc->data );
	echo "</pre>";
}
else
{
	$bc = new BrowseCap;
	$bc->runTest();
}

?>
