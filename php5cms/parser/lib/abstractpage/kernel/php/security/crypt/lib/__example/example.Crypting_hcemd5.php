<?php

require( '../../../../../../prepend.php' );

using( 'security.crypt.lib.Crypting' );


echo "<pre>\n";

$key = 'my secret key';
srand( (double)microtime() * 32767 );
$rand = rand( 1, 32767 );
$rand = pack( 'i*', $rand );
$message = 'text to encrypt';
$hcemd5  = Crypting::factory( 'hcemd5', array( 'key' => $key, 'rand' => $rand ) );

// these functions work with mime decoded data
$ciphertext = $hcemd5->encodeMime( $message );
$cleartext  = $hcemd5->decodeMime( $ciphertext );

echo $ciphertext . "\n";
echo $cleartext  . "\n\n";

// these functions work with binary data
$ciphertext = $hcemd5->encrypt( $message );
$cleartext  = $hcemd5->decrypt( $ciphertext );

echo $ciphertext . "\n";
echo $cleartext  . "\n\n";

echo "</pre>\n";

?>
