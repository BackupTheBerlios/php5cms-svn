<?php

require( '../../../../../../prepend.php' );

using( 'peer.mail.pgp.PGPMail' );


$mail = new PGPMail( "X-Mailer: Abstractpage PGP Mailer\n" );

$text = "This is a test";
$mail->addKey( "Your ID" );
$mail->body = $text;
$mail->sign( "Your ID", "Password" );
$mail->encryptBody();
$mail->buildMessage();
$mail->send( 'Your Name', 'You@yourdomain.com', 'PGP Encryption Test', 'webmaster@yourdomain.com', 'PGP Encryption Class Test' );

?>
