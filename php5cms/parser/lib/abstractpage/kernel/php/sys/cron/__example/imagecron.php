<?php

/**
 * You can use this file to run pseudo-cron even from non-PHP pages
 * to do this, place this code somewhere into your page:
 * <img src="imagecron.php" width="1" height="1" border="0" alt="">
 * when you use the script that way, it's even more important that 
 * none of your cron jobs does not output anything
 */

require( '../../../../../prepend.php' );

using( 'sys.cron.PseudoCron' );


$pcron = new PseudoCron();
$jobs  = $pcron->parseCronFile( "crontab.txt" );

for ( $i = 0; $i < count( $jobs ); $i++ )
	$pcron->runJob( $jobs[$i] );

header( "Content-Type: image/gif" );
header( "Content-Disposition: inline;filename=empty.gif" );

// equivalent of a transparent 1x1px 1bpp GIF file
echo base64_decode( "R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAEALAAAAAABAAEAAAIBTAA7" );

?>
