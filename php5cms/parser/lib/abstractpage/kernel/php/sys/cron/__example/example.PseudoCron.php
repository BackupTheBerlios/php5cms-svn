<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>

<title>PseudoCron Example</title>

</head>

<body>

<!--<img src="imagecron.php" width="1" height="1" border="0">-->

<?php

require( '../../../../../prepend.php' );

using( 'sys.cron.PseudoCron' );


$pcron = new PseudoCron();
$jobs  = $pcron->parseCronFile( "crontab.txt" );

for ( $i = 0; $i < count( $jobs ); $i++ )
	$pcron->runJob( $jobs[$i] );
	
?>

</body>
</html>
