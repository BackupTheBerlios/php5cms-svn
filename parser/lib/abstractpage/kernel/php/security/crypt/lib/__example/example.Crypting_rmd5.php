<?php

require( '../../../../../../prepend.php' );

using( 'security.crypt.lib.Crypting' );


echo "<pre>\n";

$plain = "Das ist geheim ;)";
$cTEA  = Crypting::factory( 'rmd5', array( 'key' => "mein key" ) );

$cipher  = $cTEA->encrypt( $plain  );
$replain = $cTEA->decrypt( $cipher );

echo $cipher . "\n";
echo bin2hex( $cipher ) . "\n";
echo htmlentities( $replain );

echo "</pre>\n";

?>
