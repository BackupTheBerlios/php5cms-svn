<html>
<head>

<title>Global Whois Example</title>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

</head>

<body bgcolor="#FFFFFF" text="#000000">

<form name="form1" method="post" action="example.php">
Domain: <input type="text" name="dom">
<input type="submit" name="Submit" value="Submit">
</form>

<?php

if ( $dom )
{
	require( '../../../../prepend.php' );

	using( 'peer.GlobalWhoisServer' );
	
	$gws = new GlobalWhoisServer();
	$whoisresult = $gws->lookup( $dom );

	print "<pre>" . $whoisresult . "</pre>";
}

?>

</body>
</html>
