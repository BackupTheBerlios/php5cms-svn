<?php

require( '../../../../../../prepend.php' );

using( 'commerce.payment.lib.Payment' );


$options = array(
	"loginid"		=> "yourLoginId",
	"customeremail"	=> "carl@thewinslows.com",
	"address"		=> "5309 Cyclamen Way",
	"city"			=> "West Jordan",
	"state"			=> "UT",
	"zip"			=> "84084",
	"country"		=> "USA",
	"phone"			=> "967-2539",
	"firstname"		=> "Carl",
	"lastname"		=> "Winslow",
	"amount"		=> "12.00",
	"cardnumber"	=> "4222222222222222",
	"expiredate"	=> "0303"
);

$pm = Payment::factory( 'authorizenet', $options );

if ( !$pm->process() )
	print "Error: " . $pm->getError(); 

exit;

?>
