<?php

require( '../../../../../../prepend.php' );

using( 'security.crypt.lib.Crypting' );


echo "<pre>\n";

$plain = "Das ist geheim ;)";
$cACDG = Crypting::factory( 'azdg', array( 'key' => "mein key" ) );

$cipher  = $cACDG->encrypt( $plain  );
$replain = $cACDG->decrypt( $cipher );

echo $cipher . "\n";
echo htmlentities( $replain ) . "\n";

echo "</pre>\n";

?>
