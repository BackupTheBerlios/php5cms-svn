<?php

ob_start();

require( '../../../../../../prepend.php' );

using( 'peer.xmlrpc.lib.XMLRPCServer' );


$server = new XMLRPCServer();
$server->registerFunction( "myFunc" );

// process the server requests
$server->processRequest();

function myFunc( )
{
    return new XMLRPCString( "This command was run by xml rpc" );
}

ob_end_flush();

?>
