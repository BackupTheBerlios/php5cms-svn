<?php

require( '../../../../../../prepend.php' );

using( 'security.checksum.lib.Checksum' );


echo "<pre>\n";

$csum = Checksum::factory( 'sha1' );

$result = $csum->fromString( "It's a big big world." );
echo "From String: " . $result->getValue() . "\n";

$result = $csum->fromFile( "php-logo.png" );
echo "From File:   " . $result->getValue() . "\n";

echo "</pre>\n";

?>
