#!/usr/bin/php -q

<?php

require( '../../../../prepend.php' );

using( 'io.TailFile' );


$tailfile = "/var/log/maillog";

// How long to delay between checks on the log
// file updates
$check_delay = 5;


$t = new TailFile( $tailfile );
print "Watching mail log... \n";

while ( $t->isOpen() )
{
	$t->checkUpdates();
	$myres = $t->getResults();

	if ( $myres )
		print $myres;
        
	$t->wait( $check_delay );
}

?>
