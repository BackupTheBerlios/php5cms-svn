<?php

// To see the action of this script, 
// http://localhost/<virtual directory>/example.GMTTime.php?tzone=5.30&cdate=12/31/2003

require( '../../../../../prepend.php' );

using( 'util.datetime.GMTTime' );


$gt = new GMTTime();
echo "Current System Time is <b>" . date( "d - F - Y H:i:s" ) . "</b> and its Time Zone is <b>" . date( "T" ) . "</b>";
echo "<BR>" . str_repeat( "-", 100 ) . "<BR>";
echo "Current GMT Time is <b>" . $gt->getTime( $HTTP_GET_VARS['tzone'] ) . "</b>";
echo "<BR>" . str_repeat( "-", 100 ) . "<BR>";
echo "The following table lists time at various time zones:<br><br>";
echo "<table width=50% align=left border=1>";
echo "<tr><td valign=top align=center>Sl.No.</td><td valign=top align=center>Time Zone</td><td valign=top align=center>Current Date & Time</td></tr>";

if ( $HTTP_GET_VARS['cdate'] != "" )
	$sCurDate = strtotime( $HTTP_GET_VARS['cdate'] );
else
	$sCurDate = time();

for ( $i = 0; $i < sizeof( $gt->nTZones ); $i++ )
	echo "<tr><td valign=top align=center>" . ( $i + 1 ) . "</td><td valign=top align=center>" . $gt->nTZones[$i] . "</td><td valign=top align=center>" . $gt->convertTime( $sCurDate, $HTTP_GET_VARS['tzone'], $gt->nTZones[$i] ) . "</td></tr>";

echo "</table>";

?>
