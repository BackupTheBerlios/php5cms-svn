<?php

require( '../../../../../../prepend.php' );

using( 'security.crypt.lib.Crypting' );


echo "<pre>\n";

$cAES = Crypting::factory( 'aes', array( 'key' => "mein key" ) );
$c    = $cAES->encrypt( 'Hello' );
$m    = $cAES->decrypt( $c );

echo bin2hex( $c ) . "\n";	// 050000005e441dd4b7e14df77e3c473739b3419f
echo bin2hex( $m ) . "\n"; 	// 48656c6c6f
echo $m; 	         		// Hello

echo "</pre>\n";

?>
