<?php

require( '../../../../../../prepend.php' );

using( 'peer.mail.smtp.SMTP' );


header('Content-Type: text/plain');

$params['host'] = 'docuverse.de';				// The smtp server host/ip
$params['port'] = 25;							// The smtp server port
$params['helo'] = 'docuverse.de';				// What to use when sending the helo command. Typically, your domain/hostname; exec( 'hostname' )
$params['auth'] = true;							// Whether to use basic authentication or not
$params['user'] = 'docuverse_de';				// Username for authentication
$params['pass'] = 'playerhater';				// Password for authentication

$send_params['recipients']	= array( 'mnix@docuverse.de' ); // The recipients (can be multiple)
$send_params['headers']		= array(
	'From: "Markus Nix" <mnix@docuverse.de>',	// Headers
	'To: mnix@docuverse.de',
	'Subject: Test email'
);

$send_params['from'] = 'mnix@docuverse.de';		// This is used as in the MAIL FROM: cmd
$send_params['body'] = '.Test email.';			// The body of the email


if ( is_object( $smtp = SMTP::connect( $params ) ) && $smtp->send( $send_params ) )
{
	echo 'Email sent successfully!'."\r\n\r\n";

	// Any recipients that failed (relaying denied for example) will be logged in the errors variable.
	print_r( $smtp->errors );
}
else
{
	echo 'Error sending mail'."\r\n\r\n";
		
	// The reason for failure should be in the errors variable
	print_r( $smtp->errors );
}

?>
