<?php

require( '../../../../../prepend.php' );

using( 'xml.xbel.XBEL' );

	 
$xbel = new XBEL( 'bookmarks.xml' );

?>

<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">

<html>
<head>

<title>XBEL Example</title>

<style type="text/css">

#desc
{
	padding: 0px 0px 10px 0px;
	font-family : Arial, Helvetica, sans-serif;
}
#cont0
{
}
#cont1
{
}
#cont2
{
}
#cont3
{
}
#cont4
{
}
#cont5
{
}
#cont6
{
}
#cont7
{
}
#0
{
	background-color : #B5B6B5;
}
#1
{
	background-color : #C6BA9C;
}
#2
{
	background-color : #B5B6B5;
}
#3
{
	background-color : #C6BA9C;
}
#4
{
	background-color : #B5B6B5;
}
#5
{
	background-color : #B5B6B5;
}
#6
{
	background-color : #B5B6B5;
}
#7
{
	background-color : #B5B6B5;
}

</style>

</head>

<body>

<?php
		 
// top anchor holder, you can place this also at
// bottom when the content is placed under the list
echo "
	$xbel->top
	<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" width=\"800\">
	<tr>
		<td align=\"center\">
		<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
		<tr>
";

// $xbel->content is your content list
// it is a single string with | concatenated
// you can place it either above or under your
// content list
$acounter = 1;
$tok = strtok( $xbel->content, "|" );
	
while ( $tok )
{
   	if ( preg_match ( "/\bcont2\b/i", $tok ) )
	{
		echo '<td width=\"33%\">' . $tok . '</td>';
		  	
		if ( $acounter == 3 )
		{
		  	echo '</tr><tr>';
		  	$acounter = 1;
		}
		else
		{
		  	$acounter++;
		}
	}
	
	$tok = strtok( "|");
}

// $xbel->output is the main xbel list
// just echo it to the stdout.
echo "
	</tr>
	</table>
	</td>
	</tr>
	<tr>
	<td align=\"center\">$xbel->output</td>
	</tr>
	</table>
";

?>

</body>
</html>
