<?php

require( '../../../../../../prepend.php' );

using( 'security.crypt.lib.Crypting' );


echo "<pre>\n";

$cAES = Crypting::factory( 'rot13' );
$c    = $cAES->encrypt( 'Ich kam, sah und siegte' );
$m    = $cAES->decrypt( $c );

echo $c . "\n";
echo $m;

echo "</pre>\n";

?>
