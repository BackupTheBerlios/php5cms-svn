<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>

<title>XSelect Example</title>

</head>

<body>

Try a query:<br>
example: select link(cdata,@category) from links where link(@category="xml")<br><br>

<form method='post'>
	<textarea name='query' rows='4' width='40'></textarea>
	<input type='submit'>
</form><br><br>

<?php

require( '../../../../../prepend.php' );

using( 'xml.xselect.XSelect' );

$query = (isset($_REQUEST['query']) ? $_REQUEST['query'] : false);


if ( $query )
{
	print "your query: " . stripslashes( $query ) . "<br><br>";
	$xselect = new XSelect;
	
	// toggle debug
	// $xselect->debug = true;
	
	$xselect->loadXML( "example.xml" );
	$result = $xselect->executeQuery( $query );
		
	print "<b>Results:</b><br><br>";
	
	foreach ( $result as $path => $data )
	{
		print "path: $path<br>";
		
		foreach ( $data as $arg => $val)
			print "&nbsp;&nbsp;$arg = $val<br>";
	}
	
	// null the object
	$xselect = "";
}

// xml dump
print "<p><b>test xml:</b><br>";
$fd = fopen( "example.xml", "r" );
print "<pre>" . htmlentities( fread( $fd, filesize( "example.xml" ) ) ) . "</pre>";
fclose( $fd );

?>

</body>
</html>
