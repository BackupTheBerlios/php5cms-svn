<?php

require( '../../../../../../prepend.php' );

using( 'peer.xmlrpc.lib.XMLRPCClient' );
using( 'peer.xmlrpc.lib.XMLRPCCall' );


$client = new XMLRPCClient( "docuverse.de", "example.minimalserver.php" );

$call = new XMLRPCCall( );
$call->setMethodName( "myFunc" );

// send the request
$response = $client->send( $call );

// print out the results
$result = $response->result();

if ( $response->isFault() )
{
    print( "The server returned an error (" .  $response->faultCode() . "): ". 
		$response->faultString() .
		"<br>"
	);
}
else
{
    print( "The server returned: " . $result->value() . "<br>" );
}

?>
