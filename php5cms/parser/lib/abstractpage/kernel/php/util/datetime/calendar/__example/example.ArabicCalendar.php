<?php

require( '../../../../../../prepend.php' );

using( 'util.datetime.calendar.ArabicCalendar' );


echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1256\">";

$acalend = new ArabicCalendar();
$GDay   = 13;
$GMonth = 8;
$GYear  = 2002;
$acdate = $acalend->GregorianToIslamic( $GYear, $GMonth, $GDay );

$jour   = $acdate[dayname];
$ADay   = $acdate[day];
$AMonth = $acdate[monthname];
$AYear  = $acdate[year];

echo "Georgian date : $GDay - $GMonth - $GYear<br>";
echo "Islamic date :<font size=+1> $jour : $ADay - $AMonth - $AYear</font><br>";
	
$IDay     = 18;
$IMonth   = 5;
$IYear    = 1423;
$acdate_g = $acalend->IslamicToGregorian( $IYear, $IMonth, $IDay );
	
$jour   = $acdate_g[dayname];
$ADay   = $acdate_g[day];
$AMonth = $acdate_g[monthname];
$AYear  = $acdate_g[year];
	
echo "<br>Islamic date : $IDay - $IMonth - $IYear<br>";
echo "Georgian date<font size=+1> $jour : $ADay - $AMonth - $AYear</font><br>";

?>
