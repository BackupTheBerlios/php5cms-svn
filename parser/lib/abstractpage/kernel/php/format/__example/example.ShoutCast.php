<?php

require( '../../../../prepend.php' );

using( 'format.ShoutCast' );


$shoutcast = new ShoutCast;
$shoutcast->host   = "localhost";
$shoutcast->port   = 8000;
$shoutcast->passwd = "YOURPASSWORDGOESHERE";

$result = $shoutcast->openstats();

if ( !PEAR::isError( $result ) ) 
{
	if ( $shoutcast->getStreamStatus() ) 
	{
		echo "<b>" . $shoutcast->getServerTitle() . "</b> (" . $shoutcast->getCurrentListenersCount() . " of " . $shoutcast->getMaxListenersCount() . " listeners, peak: " . $shoutcast->getPeakListenersCount() . ")<p>\n\n";		
		echo "<table border=0 cellpadding=0 cellspacing=0>\n";
		echo "<tr><td width=\"180\"><b>Server Genre: </b></td><td>" . $shoutcast->getServerGenre() . "</td></tr>\n";
		echo "<tr><td><b>Server URL: </b></td><td><a href=\"" . $shoutcast->getServerURL() . "\">" . $shoutcast->getServerURL() . "</a></td></tr>\n";
		echo "<tr><td><b>Server Title: </b></td><td>" . $shoutcast->getServerTitle() . "</td></tr><tr><td colspan=2>&nbsp;</td></tr>\n";
		echo "<tr><td><b>Current Song: </b></td><td>" . $shoutcast->getCurrentSongTitle() . "</td></tr>\n";
		echo "<tr><td><b>BitRate: </b></td><td>" . $shoutcast->getBitRate() . "</td></tr><tr><td colspan=2>&nbsp;</td></tr>\n";		
		echo "<tr><td><b>Average listen time: </b></td><td>" . $shoutcast->convertSeconds( $shoutcast->getAverageListenTime() ) . "</td></tr><tr><td colspan=2>&nbsp;</td></tr>\n";
		echo "<tr><td><b>IRC: </b></td><td>" . $shoutcast->getIRC() . "</td></tr>\n";
		echo "<tr><td><b>AIM: </b></td><td>" . $shoutcast->getAIM() . "</td></tr>\n";
		echo "<tr><td><b>ICQ: </b></td><td>" . $shoutcast->getICQ() . "</td></tr><tr><td colspan=2>&nbsp;</td></tr>\n";
		echo "<tr><td><b>WebHits Count: </b></td><td>" . $shoutcast->getWebHitsCount() . "</td></tr>\n";
		echo "<tr><td><b>StreamHits Count: </b></td><td>" . $shoutcast->getStreamHitsCount() . "</td></tr>\n";
		echo "</table><p>";
		
		echo "<b>Song history;</b><br>\n";
		$history = $shoutcast->getSongHistory();
		
		if ( is_array( $history ) ) 
		{
			for ( $i = 0; $i < sizeof( $history ); $i++ )
				echo "[" . $history[$i]["playedat"] . "] - " . $history[$i]["title"] . "<br>\n";
		} 
		else 
		{
			echo "No song history available.";
		}
		
		echo "<p>";
		echo "<b>Listeners;</b><br>\n";
		
		$listeners = $shoutcast->getListeners();
		
		if ( is_array( $listeners ) ) 
		{
			for ( $i = 0; $i < sizeof( $listeners ); $i++ )
				echo "[" . $listeners[$i]["uid"] . "] - " . $listeners[$i]["hostname"] . " using " . $listeners[$i]["useragent"] . ", connected for " . $shoutcast->convertSeconds( $listeners[$i]["connecttime"] ) . "<br>\n";
		} 
		else 
		{
			echo "No one listens right now.";
		}
	} 
	else 
	{
		echo "Server is up, but no stream available.";
	}
}

?>
