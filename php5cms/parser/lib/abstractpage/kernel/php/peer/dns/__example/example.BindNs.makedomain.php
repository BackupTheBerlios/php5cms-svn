<?php

$domain = $_GET['domain'];
$IP     = $_GET['IP'];

if( !$domain )
{

?>

<form action="make-domain.php" method="GET">
Domain name: <input type="text" size="20" name="domain"><Br>
IP Address: <input type="text" size="20" name="IP"><br>
<input type=submit value="Make Domain">
</form>

<?php

exit;

}

require( '../../../../../prepend.php' );

using( 'peer.dns.BindNs' );


echo "<P>We are creating $domain with IP: $IP\n<BR>";
$dns = new BindNs();
$dns->initialize( $domain );
$dns->addA( $IP );
$dns->addA( "mail:10.1.1.51" );
$dns->addMX( 10, "mail.samples.com" );

$dns->addCNAME( "www" );
$dns->addCNAME( "mail" );
$dns->addCNAME( "junkie" );

$dns->delA( "205.1.1.25" );

// We set some bull defaults here for stuff we always use.
echo "Setting defaults...\n<br>";

$dns->setNameserver( "crap.nameserver.com" );
$dns->setContact( "dillweed@mydomain.com"  );

if ( $dns->exists )
	$dns->incSerial();
else
	$dns->autoSerial();	// Note: Automatically generate a serial #.

$dns->setRefresh();		// Note: We default to 10800 (3 hours)
$dns->setRetry();		// Note: We default to 3600 (1 hour)
$dns->setExpire();		// Note: We default to 604800 (1 week)
$dns->setTtl();			// Note: We default to 86400 (1 day)
$dns->addNS( "my.crappy.nameserver.com" );
$dns->activate();

?>

Domain created!<BR>

<?php

exit;

?>
