<html>
<head>

<title>DoubleMetaphone Example</title>

</head>

<body>

<h1>DoubleMetaphone Example</h1>

<?php

require( '../../../../../prepend.php' );

using( 'util.text.DoubleMetaPhone' );


if ( isset( $in ) && $in != "" )
{
	$mp = new DoubleMetaPhone( $in );

	?>

	<h2><? print $in; ?> yields '<? print $mp->primary; ?>' and '<? print $mp->secondary; ?>'</h2>

	<?php

}

?>

<form action="<? print $_SERVER['PHP_SELF']; ?>" method="GET">
<input type=text name=in>
<input type=submit>
</form>

</body>
</html>
