<?php

require( '../../../../../prepend.php' );

using( 'format.ical.ICalendar' );


$categories1 = array( 'Freitzeit', 'Feste' );
$categories2 = array( 'Feste', 'Demonstrationen' );
$categories3 = array( 'Sport' );
$attendees   = array(
	'Markus' => 'mnix@docuverse.de',
	'Felix'  => ' ,2',
	'Karl'   => 'mnix@docuverse.de,3'
);

$days = array( 2 );

// (ProgrammID, Method (1 = Publish | 0 = Request), Download Directory)              
$iCal = new ICalendar( '', 1, '' );

$iCal->addEvent(
	'mailto:mnix@docuverse.de',		// Organizer
	time()+3600,					// Start Time
	time()+7200,					// End Time
	'Wien',							// Location
	0,								// Transparancy (0 = OPAQUE | 1 = TRANSPARENT)
	$categories1,					// Array with Strings
	'Hier steht die Beschreibung',	// Description
	'Wiener Stadtfest',				// Title
	0,								// Class (0 = PRIVATE | 1 = PUBLIC | 2 = CONFIDENTIAL)
	'de',							// Language of the Strings
	$attendees,						// Array (key = attendee name, value = e-mail, second value = role of the attendee [0 = CHAIR | 1 = REQ | 2 = OPT | 3 =NON])
	5,								// Priority = 0-9
	5,								// frequency: 0 = once, secoundly - yearly = 1-7
	10,								// recurrency end: ('' = forever | integer = number of times | timestring = explicit date)
	1,								// Interval for frequency (every 2,3,4 weeks...)
	$days,							// Array with the number of the days the event accures (example: array(0,1,5) = Sunday, Monday, Friday
	0,								// Startday of the Week ( 0 = Sunday - 6 = Saturday)
	'',								// exeption dates: Array with timestamps of dates that should not be includes in the recurring event
	60								// Sets the time in minutes an alarm appears before the event in the programm. no alarm if empty string or 0
);
    
header( 'Content-Type: text/Calendar' );
header( 'Content-Disposition: inline; filename=iCalender_dates_' . date( 'Y-m-d_H-m-s' ) . '.ics' );

echo $iCal->getEventOutput();

/*
$iCal->writeEventsFile();
header( 'Location:' . $iCal->getEventsFilePath() );
exit;
*/

?>
