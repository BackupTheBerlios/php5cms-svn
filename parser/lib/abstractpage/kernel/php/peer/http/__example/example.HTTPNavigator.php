<?php

// choose random site
srand( (float)microtime() * 10000000 );
$site = array(
	"http://www.amazon.co.uk", 
	"http://www.bbc.co.uk", 
	"http://cnn.com", 
	"http://uk.php.net", 
	"http://www.disney.com"
);
$rand_site = $site[array_rand( $site )];

// set form vars
$url          = ( !empty( $_GET["url"]          )? $_GET["url"] : $rand_site );
$use_curl     = ( !empty( $_GET["use_curl"]     )? true : false );
$use_curl_ssl = ( !empty( $_GET["use_curl_ssl"] )? true : false );

?>

<html>
<head>

<title>HTTPNavigator Example</title>

</head>

<body>

<form method="get">
Use cURL <input type="checkbox" name="use_curl" value="yes" <?php echo ( $use_curl? "checked" : "" ); ?>> &nbsp; 
Use cURL for SSL <input type="checkbox" name="use_curl_ssl" value="yes" <?php echo ( $use_curl_ssl? "checked" : "" ); ?>>
<br>
URL: <input type="text" name="url" value="<?php echo $url; ?>" size="35">&nbsp;
<input type="submit" name="submit" value="Go!">
</form>

<?php

require( '../../../../../prepend.php' );

using( 'peer.http.HTTPNavigator' );


// create instance of class
$http = new HTTPNavigator();

// set cURL settings (based on form checkboxes)
$http->set_var( "use_curl",     $use_curl     );
$http->set_var( "use_curl_ssl", $use_curl_ssl );

// grab page
$result = $http->get_url( "$url" );

echo "<pre>";

// if error or warning
if ( $http->isError( $result ) )
{
	echo "<b>Error: ".$result->getMessage()."</b>";
	echo "</pre></body></html>";
	
	exit;
}
elseif ( $http->isError( $result ) )
{
	echo "<b>Warning: ".$result->getMessage()."</b>";
}

if ( $result )
{
	// get status info
	$status = $http->status_info( $http->get_status() );
	echo '
-----------------------------------------------------
<b>Last Request:</b>
Time taken: '     . $http->get_info( 'time_taken' ) . '
Status Code: '    . $http->get_status() . '
Status Txt: '     . $status['meaning'] . '
Status Meaning: ' . $status['range_meaning'] . '
Body Size: '      . $http->get_body_size() . '
-----------------------------------------------------
	';

	// list headers
	echo "\n<b>Last Header Returned:</b>\n" . $http->get_headers();
}

$cookie = &$http->get_var( "cookie" );

echo "\n\n<b>Cookies Found:</b>\n";

if ( count( $cookie ) == 0 )
{
	echo "<i>None</i>\n\n";
}
else
{
	// list cookies

	// loop through domains
	foreach ( $cookie as $domain => $domain_val )
	{
		echo "Domain: $domain\n";

		// loop through path
		foreach ( $domain_val as $path => $path_val )
		{
			echo "	Path: $path\n";

			// loop through name
			foreach ( $path_val as $name => $name_val )
			{
				echo "		Cookie: $name\n";
				echo "		Value: $name_val[value]\n";
				
				if ( isset( $name_val["expires"] ) )
					echo "		Expires: " . date( "D jS M Y H:i:s", $name_val["expires"] ) . "\n";
				else
					echo "		Expires: <i>session</i>\n";
				
				if ( $name_val["secure"] )
					echo "		Secure: Yes\n\n";
				else
					echo "		Secure: No\n\n";
			}
		}
	}
}

print "<hr />";
print_r($http);
?>

</pre>

</body>
</html>
