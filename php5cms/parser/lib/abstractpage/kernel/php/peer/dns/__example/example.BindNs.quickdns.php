<html>
<head>

<title>Adding to DNS</title>

</head>

<body>

<?php

require( '../../../../../prepend.php' );

using( 'peer.dns.BindNs' );


$domain		= $_GET['domain'];
$ip			= $_GET['ip'];
$type		= $_GET['type'];
$mx			= $_GET['mx'];
$maindomain	= $_GET['maindom'];

if ( !$domain || !$ip || !$type )
{
	echo "Usage Error!\n";
	return;
}

$dns = new BindNs( $domain );

if ( $mx )
	$dns->addMX( 10, $mx );

if ( $dns->exists )
{
	$dns->incSerial();
}
else
{
	$dns->autoSerial(); // Note: Automatically generate a serial #.
	$dns->addCNAME( "www" );
	$dns->addA( $ip );
	$dns->addNS( "ns1.somesite.com" );
}

$dns->setRefresh();	// Note: We default to 10800 (3 hours)
$dns->setRetry();	// Note: We default to 3600 (1 hour)
$dns->setExpire();	// Note: We default to 604800 (1 week)
$dns->setTtl();		// Note: We default to 86400 (1 day)
$dns->setNameserver( "default.nameserver.net" );
$dns->setContact( "joe@blow.com" );
$dns->activate();

?>

</body>
</html>
