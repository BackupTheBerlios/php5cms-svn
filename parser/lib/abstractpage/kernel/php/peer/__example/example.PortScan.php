<?php

require( '../../../../prepend.php' );

using( 'peer.Portscan' );


// Test for checkPort() and getService()
if ( Portscan::checkPort( "localhost", 80 ) == true ) 
{
    echo "There is a service on your machine on port 80 (" . Portscan::getService( 80 ) . ").\n";
}

// Test for checkPortRange()
echo "Scanning localhost ports 70-90\n";
$result = Portscan::checkPortRange( "localhost", 70, 90 );

foreach ( $result as $port => $element ) 
{
    if ( $element == true )
        echo "On port " . $port . " there is running a service.\n";
    else
        echo "On port " . $port . " there is no service running.\n";
}

// Test for getService()
echo "On port 22, there service " . Portscan::getService( 22 ) . " is running.\n";

// Test for getPort()
echo "The finger service usually runs on port " . Portscan::getPort( "finger" ) . ".\n";

?>
