<?php

require( '../../../../../../prepend.php' );

using( 'security.crypt.lib.Crypting' );


echo "<pre>\n";

$plain = "Das ist geheim ;)";
$cXTEA  = Crypting::factory( 'xtea', array( 'key' => "mein key" ) );

$cipher  = $cXTEA->encrypt( $plain  );
$replain = $cXTEA->decrypt( $cipher );

echo $cipher . "\n";
echo bin2hex( $cipher ) . "\n";
echo htmlentities( $replain );

echo "</pre>\n";

?>
