#!/usr/bin/php4 -q

<?php

require( '../prepend.php' );

using( 'peer.http.lib.HTTP_Server' );


// create a new server for domain "example.com" on port 9090
$server	= new HTTP_Server( "example.com", 9090 );

// write debug log in text mode
$server->setDebugMode( "text", "logs/http-debug.log" );

// start the server
$server->start();

?>
