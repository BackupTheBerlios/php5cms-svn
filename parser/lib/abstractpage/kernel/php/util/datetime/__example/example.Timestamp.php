<?php

require( '../../../../../prepend.php' );

using( 'util.datetime.Timestamp' );


$unix_ts = "1084577792";
$norm_ts = "20040515003944";
	
// A Timestamp object can be initialised empty, in which case it will be set with the 
// current date & time,  and you can also either pass it a standard Timestamp or UNIX
// epochs formatted.
$ts = new Timestamp();
 
// Output the current time ...
echo "Formatted Time - " . $ts->format( "m/d/Y H:i:s" ) ."<br>"; 

// Do a tinezone conversion ...
echo "Default Timezone is: " . $ts->getDefaultTZ() . " and the date/time is " . $ts->format( "m/d/Y H:i:s" ) . "<br>";
	
// An array / list of potential timezones is included in Timezones.php, you could use that list to generate
// a drop down box that your users can select from...
echo "LA - " .	  $ts->format( "M d Y H:i:s", "America/Los_Angeles" ) . "<br>";
echo "London -" . $ts->format( "M d Y H:i:s", "Europe/London" )		  . "<br>";	
echo "Paris - " . $ts->format( "M d Y H:i:s", "Europe/Paris" )		  . "<br>";
		
// manipulate the date and time by simply setting the appropriate variables
// note that no checking is performed on these values...
$ts->day    = 12;
$ts->month  = 05;
$ts->year   = 2004;
	
$ts->hour   = 12;
$ts->minute = 34;
$ts->second = 59;

echo "Modified Values - " . $ts->format( "m/d/Y H:i:s" ) . "<br>"; 

// You can also directly set from a timestamp with 
$ts->setTS( $norm_ts );  // a normal TS? (favored by mySQL etc)
$ts->setUTS( $unix_ts ); // Set from a UNIX TIMESTAMP
	
// you can retrieve the timestamp
echo "Standard Timestamp 	- " . $ts->getTS()  . "<br>";
echo "UNIX Epochs Timestamp - " . $ts->getUTS() . "<br>";		

?>
