<?php

require( '../../../../../../prepend.php' );

using( 'security.crypt.lib.Crypting' );


echo "<pre>\n";

$key   = "mein key";
$plain = "Das ist geheim ;)";
$cDES  = Crypting::factory( 'des', array( 'key' => $key ) );

$cipher  = $cDES->encrypt( $plain  );
$replain = $cDES->decrypt( $cipher );

echo $cipher . "\n";
echo htmlentities( $replain ) . "\n";
echo "\n\n";

$plain = "Das ist auch geheim ;)";
$cDES  = new Crypting_des( 'des', array( 'key' => $key ) );

$cipher  = $cDES->encrypt( $plain  );
$replain = $cDES->decrypt( $cipher );

echo $cipher . "\n";
echo htmlentities( $replain );

echo "</pre>\n";

?>
