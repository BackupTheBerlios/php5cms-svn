<html>
<head>

<title>POP3 Example</title>

</head>

<body>

<?php

require( '../../../../../../prepend.php' );

using( 'peer.mail.pop3.POP3' );


$user     = "docuverse_de";
$password = "+++++++++++";
$apop     = 0;

$pop = new POP3;
$pop->hostname = "mail.docuverse.de";

$res = $pop->open();

if ( !PEAR::isError( $res ) )
{
	echo "<pre>Connected to the POP3 server &quot;$pop->hostname&quot;.</pre>\n";
	
	$res = $pop->login( $user, $password, $apop );
	
	if ( !PEAR::isError( $res ) )
	{
		echo "<pre>User &quot;$user&quot; logged in.</pre>\n";
		
		$res = $pop->statistics( &$messages, &$size );
		
		if ( !PEAR::isError( $res ) )
		{
			echo "<pre>There are $messages messages in the mail box with a total of $size bytes.</pre>\n";
			$result = $pop->listMessages( "", 0 );

			if ( gettype( $result ) == "array" )
			{
				for ( reset( $result ), $message = 0; $message < count( $result ); next( $result ), $message++ )
					echo "<PRE>Message " . key($result) . " - " . $result[key($result)] . " bytes.</pre>\n";

				$result = $pop->listMessages( "", 1 );

				if ( gettype( $result ) == "array" )
				{
					for ( reset( $result ), $message = 0; $message < count( $result ); next( $result ), $message++ )
						echo "<pre>Message " . key( $result ) . ", Unique ID - \"",$result[Key($result)],"\"</pre>\n";

					if ( $messages > 0 )
					{
						if ( ( $error = $pop->RetrieveMessage( 1, &$headers, &$body, 2 ) ) == "" )
						{
							echo "<pre>Message 1:\n---Message headers starts below---</pre>\n";

							for ( $line = 0; $line < count( $headers ); $line++ )
								echo "<pre>" . htmlspecialchars( $headers[$line] ) . "</pre>\n";

							echo "<pre>---Message headers ends above---\n---Message body starts below---</pre>\n";

							for ( $line = 0; $line < count( $body ); $line++ )
								echo "<pre>" . htmlspecialchars( $body[$line] ) . "</pre>\n";

							echo "<pre>---Message body ends above---</pre>\n";
							
							$res = $pop->deleteMessage( 1 );
							
							if ( !PEAR::isError( $res ) )
							{
								echo "<pre>Marked message 1 for deletion.</pre>\n";
								
								$res = $pop->resetDeletedMessages();

								if ( !PEAR::isError( $res ) )
									echo "<pre>Resetted the list of messages to be deleted.</pre>\n";
							}
						}
					}
					
					$res = $pop->close();
					
					if ( !PEAR::isError( $res ) )
						echo "<pre>Disconnected from the POP3 server &quot;$pop->hostname&quot;.</pre>\n";
				}
			}
		}
	}
}

?>

</body>
</html>
