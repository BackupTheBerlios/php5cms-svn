<?php

require( '../../../../../prepend.php' );

using( 'peer.dns.BindNs' );


$dns = new BindNs();
$dns->initialize( "vampire.com" );

$domain	 = $dns->domain;
$ip		 = $dns->getIP();
$ipn	 = $dns->domains["vampire.com"]["A"][0];
$contact = $dns->getContact();
$serial	 = $dns->getSerial();

if ( !$ip )
	echo "getIP() returned nada!<BR>\n";

if ( !$contact )
	echo "getContact() returned nada!<BR>\n";

if ( !$serial )
	echo "getSerial() returned nada!<BR>\n";

echo "Domain: [$domain]\n<BR>IP: $ip|$ipn\n<BR>Contact: $contact\n<BR>Serial: $serial\n<BR>";

if ( $dns->exists )
	echo "Domain Entry EXISTS!\n<BR>Using $dns->DOMAINFILE<BR>\n";

if ( $dns->empty )
	echo "Domain Entry is EMPTY!\n<BR>";

$dns->incSerial();

echo "<pre>\n";
echo "$dns->contents";
echo "</pre>\n";

$dns->named();

exit;

?>
