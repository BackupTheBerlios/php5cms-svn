<?php

header( "Pragma: no-cache" ); 
header( "Cache-Control: no-cache" );

set_time_limit( 180 );


require( '../../../../../prepend.php' );

using( 'org.apache.ApacheLogVisits' );


$log = new ApacheLogVisits( "C:\Programme\Apache Group\Apache\logs\access.log" );
$log->setLogFormat( APACHELOG_FORMAT_COMBINED );

$from = mktime( 0, 0, 0, 2, 1, 2004 );
$to	  = time();

$visit = $log->getVisits( basename( __FILE__ ), $from, $to );
print( $visit[0] . " viewed page" . ( ( $visit[0] > 1 )? "s" : "" ) . " by " . $visit[1] . " visitor" . ( ( $visit[1] > 1 )? "s" : "" ) . " between " . date( "d/m/Y", $from ) . " and " . date( "d/m/Y", $to ) );

?>
