<?php

// this code shows how a mini php bot could be written

require( '../../../../../../prepend.php' );

using( 'peer.irc.lib.IRC' );


class Mybot
{
	function channel_test( &$irc, &$data )
	{
		$irc->message( IRC_TYPE_CHANNEL, $data->channel, $data->nick.': I dont like tests!' );
	}

	function query_test( &$irc, &$data )
	{
		$irc->message( IRC_TYPE_CHANNEL, '#test', $data->nick . ' said "' . $data->message . '" to me!' );
		$irc->message( IRC_TYPE_QUERY, $data->nick, 'I told everyone on #test what you said!' );
	}
}	

$bot = &new Mybot();
$irc = &new IRC();
$irc->debug( IRC_DEBUG_ALL );
$irc->usesockets( true );
$irc->register_actionhandler( IRC_TYPE_QUERY|IRC_TYPE_NOTICE, "^test", $bot, "query_test" );
$irc->register_actionhandler( IRC_TYPE_CHANNEL, "^test", $bot, "channel_test" );
$irc->connect( 'irc.freenet.de', 6667 );
$irc->login( 'SmartIRC', 'SmartIR Client ' . IRC_PSIC_VERSION, 'SmartIRC' );
$irc->join( '#test' );
$irc->listen();
$irc->disconnect();

?>
