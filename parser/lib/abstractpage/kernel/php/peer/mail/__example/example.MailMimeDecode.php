<?php

require( '../../../../../prepend.php' );

using( 'peer.mail.MailMimeDecode' );


$filename = './example.txt';
$message  = fread( fopen( $filename, 'r' ), filesize( $filename ) );

header( 'Content-Type: text/plain' );
header( 'Content-Disposition: inline; filename="stuff.txt"' );

$params = array(
	'input'          => $message,
	'crlf'           => "\r\n",
	'include_bodies' => TRUE,
	'decode_headers' => TRUE,
	'decode_bodies'  => TRUE
);

print_r( MailMimeDecode::decode( $params ) );

?>
