<?php

require( '../../../../../../prepend.php' );

using( 'security.crypt.lib.Crypting' );


echo "<pre>\n";

$cCBC = Crypting::factory( 'cbc', array( 'key' => 'MyKey' ) );
$c    = $cCBC->encrypt( 'Ich kam, sah und siegte' );
$m    = $cCBC->decrypt( $c );

echo $c . "\n";
echo $m;

echo "</pre>\n";

?>
