<?php

require( '../../../../../../prepend.php' );

using( 'security.crypt.lib.Crypting' );


echo "<pre>\n";

$plain = "Vignere ist die bekannteste unter allen polyalphabetischen Algorithmen.";
$cVIG  = Crypting::factory( 'vigenere', array( 'key' => "Geheimbotschaft" ) );

$cipher  = $cVIG->encrypt( $plain  );
$replain = $cVIG->decrypt( $cipher );

echo $cipher . "\n";
echo htmlentities( $replain );

echo "</pre>\n";

?>
