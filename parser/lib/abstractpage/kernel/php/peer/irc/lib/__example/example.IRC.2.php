<?php

// this code shows how you could show on your homepage how many users are in a specific channel

require( '../../../../../../prepend.php' );

using( 'peer.irc.lib.IRC' );


$irc = &new IRC();
$irc->benchmarkstart();
$irc->debug( IRC_DEBUG_ALL );
$irc->usesockets( true );
$irc->benchmark( true );
$irc->connect( 'irc.freenet.de', 6667 );
$irc->login( 'SmartIRC', 'SmartIRC Client ' . IRC_PSIC_VERSION, 'SmartIRC' );
$irc->getlist( '#debian.de' );
$resultar = array();
$irc->listen_for( IRC_TYPE_LIST, $resultar );
$irc->disconnect();
$irc->benchmarkend();

$resultex = explode( ' ', $resultar[0] );
$count = $resultex[1];

?>

<B>On our IRC Channel #debian.de are <? echo $count; ?> Users</B>
