<!doctype html public "-//W3C//DTD HTML 4.0 //EN"> 

<html>
<head>

<title>Cron Example</title>

</head>

<body bgcolor="#ffffff">

<table border="1">
<tr>
	<th>LAST</th>
	<th>NOW</th>
	<th>SPEC</th>
	<th>RESULT</th></tr>
<tr>
	<td colspan="4">The following combinations show a FALSE result</td>
</tr>

<?php

require( '../../../../../prepend.php' );

using( 'sys.cron.Cron' );


function demo( $vLast, $vNow, $sSpec )
{
	$cronVal  = Cron::due( $vLast, $vNow, $sSpec );
	$cronText = ( $cronVal == TRUE )? "TRUE" : "FALSE";
	
	if ( is_integer( $vNow ) )
		$vNow = date( "Y-m-d h:i", $vNow );
		
	if ( is_integer( $vLast ) )
		$vLast = date( "Y-m-d h:i", $vLast );
		
	echo "<tr><td>$vLast</td><td>$vNow</td><td>$sSpec</td><td>$cronText</td></tr>";
}

// last later than reference: always FALSE
demo( "2002-07-16 01:00", "2002-07-16 00:30", "0 * * * *" );
demo( time(), "2002-07-16 00:30", "10-60 * * * *" );
   
// this returns FALSE (must specify last date)
demo( "", "2002-07-16 01:00", "0 * * * *" );
   
// last later than reference:
// FALSE until end of 2029...
demo( "2029-12-31 01:00", time(), "0 * * * *" );
   
// FALSE returned until 01:00
demo( "2002-07-16 00:00", "2002-07-16 00:30", "0 * * * *" );
// FALSE returned until 03:00
demo( "2002-07-15 23:59", "2002-07-16 01:45", "0 3 * * *" );

// this returns FALSE based on days of week
// specified days of week must be in between last and reference
demo( "2002-07-16 00:00", "2002-07-16 01:00", "0 * * * 0,1,3-6" );
demo( "2002-07-16 00:00", "2002-07-19 10:54", "0 * * * 0,6,1" );
demo( "2002-07-26 00:01", "2002-07-27 00:00", "0 * * * 1-5" );
demo( "2002-08-09 00:01", "2002-08-11 00:00", "0 0 * * 1-5" );
   
// this returns FALSE because day and month are not yet passed
// although day of week is ok
demo( "2002-07-16 00:00", "2002-07-19 10:54", "0 * 31 10 4" );
demo( "2002-07-16 00:00", "2002-07-19 10:54", "0 * 31 10 *" );

// Same time is also not allowed
demo( "2002-07-27 00:00", "2002-07-27 00:00", "0 * * * *" );

?>

<tr>
	<td colspan="4">The following combinations show a TRUE result</td>
</tr>

<?php

// this returns TRUE
demo( "2002-07-16 00:00", "2002-07-16 01:00", "0 * * * *" );
demo( "2002-07-16 00:00", "2002-07-16 01:00", "0 * * * 2-4" );
demo( "2002-07-15 23:59", "2002-07-16 00:01", "0 * * * *" );
demo( "2002-07-15 23:59", "2002-07-16 03:01", "0 2 * * *" );

// this returns TRUE
demo( "2002-07-16 00:00", time(), "0 * * * *" );
demo( "2002-07-16 00:00", time(), "0 * 0-31 * 0-6" );

demo( "2002-08-07 05:10", "2002-08-08 05:05", "0 0 * * *" );

// this returns TRUE based on days of week
// specified days of week must be in between last and reference
demo( "2002-07-16 00:00", "2002-07-16 01:00", "0 * * * 2-6" );

exit();

?>

</table>

</body>
</html>
