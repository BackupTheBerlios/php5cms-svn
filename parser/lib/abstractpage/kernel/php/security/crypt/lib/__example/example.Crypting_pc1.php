<?php

require( '../../../../../../prepend.php' );

using( 'security.crypt.lib.Crypting' );


echo "<pre>\n";

$plain = "Das ist geheim ;)";
$cTEA  = Crypting::factory( 'pc1', array( 'key' => "the key the secret" ) );

$cipher  = $cTEA->encrypt( $plain  );
$replain = $cTEA->decrypt( $cipher );

echo $cipher . "\n";
echo htmlentities( $replain );

echo "</pre>\n";

?>
