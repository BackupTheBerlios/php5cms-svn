<?php

/*
+----------------------------------------------------------------------+
|This program is free software; you can redistribute it and/or modify  |
|it under the terms of the GNU General Public License as published by  |
|the Free Software Foundation; either version 2 of the License, or     |
|(at your option) any later version.                                   |
|                                                                      |
|This program is distributed in the hope that it will be useful,       |
|but WITHOUT ANY WARRANTY; without even the implied warranty of        |
|MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
|GNU General Public License for more details.                          |
|                                                                      |
|You should have received a copy of the GNU General Public License     |
|along with this program; if not, write to the Free Software           |
|Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.             |
+----------------------------------------------------------------------+
|Authors: Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'format.ical.ICalendarEvent' );


/**
 * Create a ICalendar file for download.
 *
 * $iCal = new ICalendar($ProgID);
 * $iCal->addEvent(...);
 * ...
 * $iCal->writeEventsFile();
 * header('Location:' . $iCal->getEventsFilePath());
 *
 * Date/Time is stored with an absolute "z" value, which means that the
 * calendar programm should import the time 1:1 not regarding timezones and
 * Daylight Saving Time. MS Outlook imports "z" dates wrong, so you have to
 * "correct" the dates BEFORE you add a new event.
 * Also if you have an event series and not a single event, you have to use
 * "File >> Import" in Outlook to import the whole series and not just the
 * first date.
 *
 * @package format_ical
 */

class ICalendar extends PEAR
{
	/**
  	 * Programm ID for the File.
  	 *
  	 * @var (string) $prodid
  	 * @access private
  	 */ 
  	var $prodid;
  
  	/**
  	 * Array with all the iCalEvent Objects.
  	 *
  	 * @var array 
  	 * @access private  
  	 */ 
  	var $icalevents;

  	/**
  	 * ID number for the array.
  	 *
  	 * @var int
  	 * @access private
  	 */ 
  	var $eventid;
  
  	/**
  	 * Output string to be written in the iCal file.
  	 *
  	 * @var string
  	 * @access private
  	 */ 
  	var $output;
  
  	/**
  	 * Download directory where iCal file will be saved.
  	 *
  	 * @var string
  	 * @access private
  	 */ 
  	var $download_dir;
  
  	/**
  	 * Filename for the iCal file to be saved.
  	 *
  	 * @var string
  	 * @access private
  	 */ 
  	var $events_filename;
  
  	/**
  	 * Method: PUBLISH (1) or REQUEST (0).
  	 *
  	 * @var int
  	 * @access private
  	 */ 
  	var $method;


	/**
  	 * Constructor
  	 *
  	 * @param (string) $prodid
  	 * @param (int) $method
  	 * @param (string) $downloaddir
  	 * @return (void)
  	 * @access private
  	 */
  	function ICalendar( $prodid = '', $method = 1, $downloaddir = '' ) 
    {
    	$this->download_dir    = ( ( strlen( trim( $downloaddir ) ) > 0 )? $downloaddir : 'icaldownload' );
    	$this->events_filename = time() . '.ics';
    	$this->eventid         = 0;
		
		$this->setProdID( $prodid );
    	$this->setMethod( $method );
    	$this->icalevents = array();
    }
 
 
  	/**
  	 * Checks if the download directory exists, else trys to create it.
  	 *
  	 * @return (boolean)
  	 * @access private
  	 */
  	function checkDownloadDir()
    {
    	if ( !is_dir( $this->download_dir ) )
      	{
      		if ( !mkdir( $this->download_dir, 0700 ) )
        		return false;
      		else
        		return true;
      	}
    	else
      	{
      		return true;
      	}
	}
  
  	/**
  	 * Returns string with the status of an attendee.
  	 *
  	 * @param (int) $role
  	 * @return (string) $roles Status
  	 * @access private
  	 */
  	function getAttendeeRole( $role )
    {
    	$roles = array(
			0 => 'CHAIR',
			1 => 'REQ-PARTICIPANT',
			2 => 'OPT-PARTICIPANT',
			3 => 'NON-PARTICIPANT'
		);
    
		if ( array_key_exists( $role, $roles ) )
    		return $roles[$role];
    	else
      		return $roles[1];
    }
  
  	/**
  	 * Set $prodid variable.
  	 *
  	 * @param (string) $prodid
  	 * @return (void)
  	 * @see getProdID(), $prodid
  	 * @access private
  	 */
  	function setProdID( $prodid = '' )
    {
    	if ( strlen( trim( $prodid ) ) > 0 )
      		$this->prodid = $prodid;
    	else
      		$this->prodid = '-//docuverse.de//iCal Class MIMEDIR//EN';
    }
    
  	/**
  	 * Get $prodid variable.
  	 *
  	 * @return (string) $prodid
  	 * @see setProdID(), $prodid
  	 * @access public
  	 */
  	function getProdID()
    {
    	return $this->prodid;
    }
  
  	/**
  	 * Set $method variable.
  	 *
  	 * @param (int) $method
  	 * @return (void)
  	 * @see getMethod(), $method
  	 * @access private
  	 */
  	function setMethod($method = 1)
    {
    	if ( is_int( $method ) )
      		$this->method = $method;
    	else
      		$this->method = 1;
    }
    
  	/**
  	 * Get $method variable.
  	 *
  	 * @return (string) $methods
  	 * @see setMethod(), $methods
  	 * @access public
  	 */
  	function getMethod()
    {
    	$methods = array(
			0 => 'REQUEST',
			1 => 'PUBLISH'
		);
    
		if ( array_key_exists( $this->method, $methods ) )
      		return $methods[$this->method];
    	else
      		return $methods[1];
    }
  
  	/**
  	 * Adds a new Event Object to the Events Array.
  	 *
  	 * @param (string) $organizer
  	 * @param (int) $start
  	 * @param (int) $end
  	 * @param (string) $location
  	 * @param (int) $transp  0|1
  	 * @param (array) $categories
  	 * @param (string) $description
  	 * @param (string) $summary
  	 * @param (int) $class  0|1|2
  	 * @param (string) $language  iso code
  	 * @param (array) $attendees
  	 * @return (void)
  	 * @see getEvent(), Event::iCalEvent()
  	 * @access public
  	 */
  	function addEvent( $organizer, $start, $end, $location, $transp, $categories, $description, $summary, $class, $language, 
					   $attendees, $prio, $frequency, $rec_end, $interval, $days, $weekstart, $exept_dates, $reminder )
    {
    	$event = new ICalendarEvent( $organizer, $start, $end, $location, $transp, $categories, $description, $summary, $class, $language, $attendees, $prio, $frequency, $rec_end, $interval, $days, $weekstart, $exept_dates, $reminder );
    	$this->icalevents[$this->eventid++] = $event;
    	unset( $event );
    }
  
  	/**
  	 * Fetches an event from the array by the ID number.
  	 *
  	 * @param (int) $id
  	 * @return (mixed)
  	 * @see addEvent(), iCalEvent::iCalEvent()
  	 * @access public
  	 */
  	function &getEvent( $id = 0 )
    {
    	if ( count( $this->icalevents) < 1 )
      		return PEAR::raiseError( "No dates found." );
      	else if ( is_int( $id ) && array_key_exists( $id, $this->icalevents ) )
      		return $this->icalevents[$id];
    	else
      		return $this->icalevents[0];
    }
  
  	/**
  	 * Returns the array with the iCal Event Objects.
  	 *
  	 * @return (array) $icalevents
  	 * @see addEvent(), getEvent()
  	 * @access public
  	 */
  	function &getEvents()
    {
    	return $this->icalevents;
    }
  
  	/**
  	 * Generates the string to be written in the file later on.
  	 *
  	 * @return (void)
  	 * @see getEventOutput(), writeEventsFile()
  	 * @access public
  	 */
	function generateEventOutput()
    {
    	$this->output  = "BEGIN:VCALENDAR\r\n";
    	$this->output .= "PRODID:" . $this->prodid . "\r\n";
    	$this->output .= "VERSION:2.0\r\n";                         
    	$this->output .= "METHOD:" . $this->getMethod() . "\r\n";                         
    
		$eventkeys = array_keys( $this->icalevents );
    
		foreach ( $eventkeys as $id )
      	{
      		$this->output .= "BEGIN:VEVENT\r\n"; 
      		$event =& $this->icalevents[$id];
      
	  		if ( $this->method == 0 && count( $event->getAttendees() ) > 0 )
        	{
        		foreach ( $event->getAttendees() as $name => $data )
          		{
          			$values = explode( ',', $data );
          			$email  = (string)$values[0];
          
          			if ( strlen( trim( $email ) ) > 5 )
            			$rsvp = 'RSVP=TRUE:MAILTO:' . $email;
          			else
            			$rsvp = 'RSVP=FALSE';
           
          			$role = (int)$values[1];
          			$this->output .= "ATTENDEE;CN=\"" . $name . "\";ROLE=" . $this->getAttendeeRole($role) . ";" . $rsvp . "\r\n";
          		}
        	}
      
      		$this->output .= "ORGANIZER:" . $event->getOrganizer() . "\r\n";
      		$this->output .= "DTSTART:"   . $event->getStartDate() . "\r\n";
      		$this->output .= "DTEND:"     . $event->getEndDate()   . "\r\n";
      
      		if ( $event->getFrequency() != 'ONCE' )
        	{
    			$this->output .= "RRULE:FREQ=" . $event->getFrequency();
        
        		if ( is_string( $event->getRecEnd() ) )
          			$this->output .= ";UNTIL=" . $event->getRecEnd();
          		else if ( is_int( $event->getRecEnd() ) )
          			$this->output .= ";COUNT=" . $event->getRecEnd();

        		$this->output .= ";INTERVAL=" . $event->getInterval() . ";BYDAY=" . $event->getDays() . ";WKST=" . $event->getWeekStart() . "\r\n";
        	}
      
      		$this->output .= "LOCATION"  . $event->getLanguage()       . ";ENCODING=QUOTED-PRINTABLE:" . $event->getLocation() . "\r\n";
      		$this->output .= "TRANSP:"   . $event->getTransp()         . "\r\n";
      		$this->output .= "SEQUENCE:" . $event->getSequence()       . "\r\n";
      		$this->output .= "UID:"      . $event->getUID()            . "\r\n";
      		$this->output .= "DTSTAMP:"  . $event->getEventTimestamp() . "\r\n";
      
      		if ( strlen( trim( $event->getCategories() ) ) > 0 )
        		$this->output .= "CATEGORIES" . $event->getLanguage() . ";ENCODING=QUOTED-PRINTABLE:" . $event->getCategories() . "\r\n";
      
      		if ( strlen( trim( $event->getDescription() ) ) > 0 )
        		$this->output .= "DESCRIPTION" . $event->getLanguage() . ";ENCODING=QUOTED-PRINTABLE:" . $event->getDescription() . "\r\n";
      
      		$this->output .= "SUMMARY"   . $event->getLanguage() . ";ENCODING=QUOTED-PRINTABLE:" . $event->getSummary() . "\r\n";
      		$this->output .= "PRIORITY:" . $event->getPriority() . "\r\n";
      		$this->output .= "CLASS:"    . $event->getClass()    . "\r\n";
      
      		if ( $event->getReminder() > 0 )
        	{ 
				// the iCal format offers far more options here (like e-mail reminders for example), but programms don't support it yet
        		$this->output .= "BEGIN:VALARM\r\n";
        		$this->output .= "TRIGGER:-PT" . $event->getReminder() . "M\r\n";
        		$this->output .= "ACTION:DISPLAY\r\n";
        		$this->output .= "DESCRIPTION:Reminder\r\n";
        		$this->output .= "END:VALARM\r\n";
        	}

      		$this->output .= "END:VEVENT\r\n";
      	}
    
		$this->output .= "END:VCALENDAR\r\n";
    
		if ( isset( $event ) )
			unset( $event ); 
	}
  
  	/**
  	 * Loads the string into the variable if it hasn't been set before.
  	 *
  	 * @return (string) $output
  	 * @see generateEventOutput(), writeEventsFile()
  	 * @access public
  	 */
  	function getEventOutput()
    {
    	if ( !isset( $this->output ) )
      		$this->generateEventOutput();
      
    	return $this->output;
    }

  	/**
  	 * Writes the string into the file and saves it to the download directory.
  	 *
  	 * @return (void)
  	 * @see generateEventOutput(), getEventOutput()
  	 * @access public
  	 */
  	function writeEventsFile()
    {
    	if ( $this->checkDownloadDir() == false )
      		return PEAR::raiseError( "Error creating download directory." );
      
    	if ( !isset( $this->output ) )
      		$this->generateEventOutput();
     
    	$handle = fopen( $this->download_dir . DIRECTORY_SEPARATOR . $this->events_filename, 'w' );
    	fputs( $handle, $this->output );
    	fclose( $handle );
    	$this->deleteOldFiles( 300 );
    
		if ( isset( $handle ) )
			unset( $handle );
			
		return true;
    }      
    
	/**
	 * Writes the string into the file and saves it to the download directory.
	 *
	 * @param (int) $time  Minimum age of the files (in seconds) before file get deleted
	 * @return (void)
	 * @see writeEventsFile()
	 * @access private
	 */
	function deleteOldFiles( $time = 300 )
    {
    	if ( $this->checkDownloadDir() == false )
      		return PEAR::raiseError( "Error creating download directory." );
    
    	if ( !is_int( $time ) || $time < 1 )
      		$time = 300;
     
    	$handle = opendir( $this->download_dir );
    
		while ( $file = readdir( $handle ) )
      	{
      		if ( !eregi( "^\.{1,2}$", $file ) && !is_dir( $this->download_dir . DIRECTORY_SEPARATOR . $file ) && eregi( "\.ics", $file ) && ( ( time() - filemtime( $this->download_dir . DIRECTORY_SEPARATOR . $file ) ) > $time ) )
        		unlink( $this->download_dir . DIRECTORY_SEPARATOR . $file );
      	}
    
		closedir( $handle );
		
    	if ( isset( $handle ) )
			unset( $handle );
			 
    	if ( isset( $file ) )
			unset( $file );
			
		return true; 
    }
  
  	/**
  	 * Returns the full path to the saved file where it can be downloaded.
  	 *
  	 * Can be used for "header(Location:..."
  	 *
  	 * @return (string)  Full http path
  	 * @access public
  	 */
  	function getEventsFilePath()
    {
    	$path_parts = pathinfo( $_SERVER['SCRIPT_NAME'] );
    	$port = ( ( $_SERVER['SERVER_PORT'] != 80 )? ':' . $_SERVER['SERVER_PORT'] : '' );
    
		return 'http://' . $_SERVER['SERVER_NAME'] . $port . $path_parts["dirname"] . '/' . $this->download_dir . '/' . $this->events_filename;
    }
} // END OF ICalendar

?>
