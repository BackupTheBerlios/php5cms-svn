<?php

require( '../../../../../../prepend.php' );

using( 'util.datetime.calendar.CalendarData' );


$tstamp    = $_GET['stamp'];
$thisMonth = ( !empty( $tstamp ) )? date( 'm', $tstamp ) : date( "m" );
$thisYear  = ( !empty( $tstamp ) )? date( 'Y', $tstamp ) : date( "Y" );

$cal = new CalendarData( $thisMonth, $thisYear );

$wday_index = array( 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun' );

?>

<style>

.calTitle
{
	font:				12px Tahoma,Verdana,Arial; 
	font-weight:		bold; 
	text-align:			center
}
.calDayname 
{
	font:				11px Tahoma,Verdana,Arial;
	font-weight:		bold; 
	width:				30px; 
	text-align:			center;
}
.calToday   
{
	font:				11px Tahoma,Verdana,Arial; 
	background-color:	FFFF00; 
	color:				#FF0000; 
	text-align:			left; 
	vertical-align:		top;
}		
.calDay     
{
	font:				11px Tahoma,Verdana,Arial; 
	width:				30px; 
	height:				30px; 
	text-align:			left; 
	vertical-align:		top;
}	
a.calNav    
{
	font:				12px Tahoma,Verdana,Arial; 
	text-decoration:	none;
}
a.calNav:hover 
{
	text-decoration:	underline;
}

</style>

<table border="0" bgcolor="#000000">

<tr bgcolor="#FFFFFF"><td colspan="8">
	<table width="100%" border="0">
	<tr>
		<td align="left"><a class="calNav" href="<?= $_SERVER['PHP_SELF'].'?stamp='.$cal->getLastMonth() ?>">&laquo; Prev</a></td>
		<td class="calTitle"><?= date( "M Y", mktime( 0, 0, 0, $cal->month, 1, $cal->year ) ) ?></td>
		<td align="right"><a class="calNav" href="<?= $_SERVER['PHP_SELF'].'?stamp='.$cal->getNextMonth() ?>">Next &raquo;</a></td>
	</tr>
	</table>
</td></tr>

<tr bgcolor="#FFFFFF"><td class="calDayname">
	<? echo implode( "</b></td><td class=\"calDayname\">", $wday_index ) ?>
</td></tr>

<?php 

foreach ( $cal->getCalendarMonth( 'd' ) as $stamp ) 
{
	echo '<tr bgcolor="#FFFFFF">';
	
	for ( $i = 0; $i < count( $wday_index ); $i++ ) 
	{
		$theDay = $stamp[$wday_index[$i]];
		
		if ( $theDay == date( "d" ) )
			echo '<td class="calToday">';
		else
			echo '<td class="calDay">';
		
		echo $theDay;
		echo "</td>";
	}
	
	echo "</tr>";
} 

?>
