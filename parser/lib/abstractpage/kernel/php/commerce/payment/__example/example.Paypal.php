<?php

require( '../../../../../prepend.php' );

using( 'commerce.payment.Paypal' );


// PayPal will send the information through a POST
$pp = new Paypal( $_POST );

// We send an identical response back to PayPal for verification
$pp->sendResponse();

// PayPal will tell us whether or not this order is valid.
// This will prevent people from simply running your order script
// manually
if ( !$pp->isVerified() )
	die( "Bad order, someone must have tried to run this script manually." );


switch ( $pp->getPaymentStatus() )
{
	case 'Completed':
		// order is good
		break;

	case 'Pending':
		// money isn't in yet, just quit.
		// paypal will contact this script again when it's ready
		echo( "Pending Payment" );
		exit;

	case 'Failed':
		// whoops, not enough money
		echo( "Failed Payment" );
		exit;

	case 'Denied':
		// denied payment by us
		// not sure what causes this one
		echo( "Denied Payment" );
		exit;

	default:
		// order is no good
		echo( "Unknown Payment Status" . $pp->getPaymentStatus() );
		exit;
}


// If we made it down here, the order is verified and payment is complete.
// You could log the order to a MySQL database or do anything else at this point.

// Email the information to us
$date = date(" D M j G:i:s T Y", time() );

$message .= "\n\nThe following info was received from PayPal - $date:\n\n";

@reset( $_POST );
while ( @list( $key, $value ) = @each( $_POST ) )
	$message .= $key . ':' . " \t$value\n";

mail( "root@example.com", "[$date] PayPal Payment Notification", $message );

?>
