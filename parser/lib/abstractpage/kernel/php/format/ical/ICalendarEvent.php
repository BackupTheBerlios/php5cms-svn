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


/**
 * Container for a single event.
 *
 * @package format_ical
 */

class ICalendarEvent extends PEAR
{
	/**
   	 * Organizer of the event; can be something like "mailto:xxx@yyy.com"
   	 *
   	 * @var string
   	 * @access private
   	 */ 
  	var $organizer;
  
  	/**
   	 * Timestamp of the start date
   	 *
   	 * @var int
   	 * @access private
   	 */ 
  	var $startdate_ts;
  
  	/**
   	 * Timestamp of the end date
   	 *
   	 * @var int
   	 * @access private
   	 */ 
  	var $enddate_ts;
  
  	/**
   	 * start date in ICalendar format
   	 *
     * @var string
   	 * @access private
   	 */ 
  	var $startdate;
  
  	/**
   	 * end date in iCal format
   	 *
   	 * @var string
   	 * @access private
   	 */ 
  	var $enddate; 
  
  	/**
   	 * Location of the event
   	 *
   	 * @var string
   	 * @access private
   	 */ 
  	var $location;
  
  	/**
   	 * OPAQUE (1) or TRANSPARENT (1)
   	 *
   	 * @var int
   	 * @access private
   	 */ 
  	var $transp;
  
  	/**
   	 * set to 0
   	 *
   	 * @var int
   	 * @access private
   	 */ 
  	var $sequence;
  
  	/**
   	 * Automaticaly created: md5 value of the start date + end date
   	 *
   	 * @var string
   	 * @access private
   	 */ 
  	var $uid;
  
  	/**
   	 * Time the event entry was created (iCal format) 
   	 *
   	 * @var int
   	 * @access private
   	 */ 
  	var $event_timestamp;
  
  	/**
   	 * Array with the categories asigned to the event 
   	 *
   	 * @var array
   	 * @access private
   	 */   
  	var $categories_array;
  
  	/**
   	 * String with the categories asigned to the event 
   	 *
   	 * @var string
   	 * @access private
   	 */  
  	var $categories;
  
  	/**
   	 * Detailed information for the event 
   	 *
   	 * @var string
   	 * @access private
   	 */  
  	var $description;
  
  	/**
   	 * Headline for the Event (mostly displayed in your cal programm) 
   	 *
   	 * @var string
   	 * @access private
   	 */  
  	var $summary;
  
  	/**
   	 * set to 5 
   	 *
   	 * @var int
   	 * @access private
   	 */ 
  	var $priority;
  
  	/**
   	 * PRIVATE (0) or PUBLIC (1) or CONFIDENTIAL (2)
   	 *
   	 * @var int
   	 * @access private
   	 */ 
  	var $class;
  
  	/**
   	 * iso code language (en, en-us,...)
   	 *
   	 * @var string
   	 * @access private
   	 */ 
  	var $lang;
  
  	/**
   	 * If the method is REQUEST, all attendees are listet in the file
   	 *
   	 * @var array
   	 * @access private
   	 */ 
  	var $attendees;  
  
  	/**
   	 * 0 = once, 1-7 = secoundly - yearly
   	 *
   	 * @var int
   	 * @access private
   	 */ 
  	var $frequency;
  
  	/**
   	 * '' = never, integer < 4 numbers = number of times, integer >= 4 = timestamp
   	 *
   	 * @var mixed
   	 * @access private
   	 */ 
  	var $rec_end;
  
  	/**
   	 * interval of the recurring date (example: every 2,3,4 weeks)
  	 *
   	 * @var int
   	 * @access private
   	 */ 
  	var $interval;
  
  	/**
   	 * List of short strings symbolizing the weekdays
   	 *
   	 * @var array
   	 * @access private
   	 */ 
  	var $shortDaynames;
  
  	/**
   	 * Short string symbolizing the startday of the week
   	 *
   	 * @var string
   	 * @access private
   	 */ 
  	var $week_start;
  
  	/**
   	 * Exeptions dates for the recurring event (Array of timestamps)
   	 *
   	 * @var array
   	 * @access private
   	 */ 
  	var $exept_dates;
  
  	/**
   	 * String of days for the recurring event (example: "SU,MO")
   	 *
   	 * @var string
   	 * @access private
   	 */ 
  	var $rec_days;

  	/**
   	 * If not empty, contains the time in minutes, when an alarm should appear before the event
   	 *
   	 * @var int
   	 * @access private
   	 */ 
  	var $reminder;
  

	/**
   	 * Constructor
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
   	 * @access private
   	 */
	function ICalendarEvent( 
  		$organizer, 
		$start, $end, 
		$location, 
		$transp, 
		$categories, 
		$description, 
		$summary, 
		$class, 
		$language, 
		$attendees, 
		$prio, 
		$frequency, 
		$rec_end, 
		$interval, 
		$days, 
		$weekstart, 
		$exept_dates, 
		$reminder ) 
    {
    	$this->shortDaynames = array(
			0 => 'SU',
			1 => 'MO',
			2 => 'TU',
			3 => 'WE',
			4 => 'TH',
			5 => 'FR',
			6 => 'SA'
		);
    
    	$this->setLanguage( $language );
    	$this->setOrganizer( $organizer );
    	$this->setStartDate( $start );
    	$this->setEndDate( $end );
    	$this->setLocation( $this->quotedPrintableEncode( $location ) );
    	$this->setTransp( $transp );
    	$this->setSequence( 0 );
    	$this->setCategories( $categories );
    	$this->setDescription( $this->quotedPrintableEncode( $description ) );
    	$this->setSummary( $this->quotedPrintableEncode( $summary ) );
    	$this->setPriority( $prio );
    	$this->setClass( $class );
    	$this->setEventTimestamp();
    	$this->setUID();
    	$this->setAttendees( $attendees );
    	$this->setFrequency( $frequency );
    	$this->setRecEnd( $rec_end );
    	$this->setInterval( $interval );
    	$this->setDays( $days );
    	$this->setWeekStart( $weekstart );
    	$this->setExeptDates( $exept_dates );
    	$this->setReminder( $reminder );
    }

	
	/**
  	 * Sets the time a reminder should pop up in the users programm
  	 *
  	 * @param (int) $reminder in minutes 
  	 * @return (void)
  	 * @see getReminder(), $reminder
  	 * @access private
  	 */
  	function setReminder( $reminder = 0 )
    {
    	if ( is_int( $reminder ) )
      		$this->reminder = $reminder;
    	else
      		$this->reminder = 0;
    }
  
  	/**
  	 * Returns the $reminder variable
  	 *
  	 * @return (int) $reminder
  	 * @see setReminder(), $reminder
  	 * @access public 
  	 */ 
   	function getReminder()
    {
    	return $this->reminder;
    }

  	/**
  	 * Sets a string with weekdays of the recurring event
  	 *
  	 * @param (array) $recdays integers 
  	 * @return (void)
  	 * @see getDays(), $rec_days
  	 * @access private
  	 */
  	function setDays( $recdays = '' )
    {
    	$this->rec_days = '';
    
		if ( !is_array( $recdays ) )
      	{
      		$this->rec_days = $this->shortDaynames[1];
      	}
    	else
      	{
      		if ( count( $recdays ) > 0 )
        		$recdays = array_values( array_unique( $recdays ) );
      
      		foreach ($recdays as $day)
        	{
        		if ( array_key_exists( $day, $this->shortDaynames ) )
          			$this->rec_days .= $this->shortDaynames[$day] . ',';
        	}
      
	  		$this->rec_days = substr( $this->rec_days, 0, strlen( $this->rec_days ) - 1 );
      	}
    }
	  
  	/**
  	 * Returns a string with recurring days
  	 *
  	 * @return (string) $rec_days
  	 * @see setDays(), $rec_days
  	 * @access public
  	 */ 
   	function getDays()
    {
    	return $this->rec_days;
    }
  
  	/**
  	 * Sets an array of formated exeptions dates based on an array with timestamps
  	 *
  	 * @param (array) $exeptdates  
  	 * @return (void)
  	 * @see getExeptDates(), $exept_dates
  	 * @access private
  	 */
  	function setExeptDates( $exeptdates = '' )
    {
    	if ( !is_array( $exeptdates ) )
      	{
      		$this->exept_dates = array();
      	}
    	else
      	{
      		foreach ( $exeptdates as $timestamp )
        		$this->exept_dates[] = date( 'Ymd\THi00\Z', $timestamp );
      	}
	}
  
  	/**
  	 * Returns a string with exeptiondates
  	 *
  	 * @return (string) $return
  	 * @see setExeptDates(), $exept_dates
  	 * @access public
  	 */ 
   	function getExeptDates()
    {
    	$return = '';
    
		foreach ( $this->exept_dates as $date )
      		$return .= $date . ',';

    	return substr( $return, 0, strlen( $return ) - 1 );
	}

  	/**
  	 * Sets the starting day for the week (0 = Sunday)
  	 *
  	 * @param (int) $weekstart  0-6
  	 * @return (void)
  	 * @see getWeekStart(), $week_start
  	 * @access private
  	 */
  	function setWeekStart( $weekstart = 1 )
    {
    	if ( is_int( $weekstart ) && preg_match( '(^([0-6]{1})$)', $weekstart ) )
      		$this->week_start = $weekstart;
    	else
      		$this->week_start = 1;
	}
  
  	/**
  	 * Get the string from the $week_start variable
  	 *
  	 * @return (string) $shortDaynames
  	 * @see setWeekStart(), $week_start
  	 * @access public
  	 */ 
   	function getWeekStart()
    {
    	if ( array_key_exists( $this->week_start, $this->shortDaynames ) )
      		return $this->shortDaynames[$this->week_start];
      	else
      		return $this->shortDaynames[1];
    }
  
  	/**
  	 * Sets the interval for a recurring event (2 = every 2 [days|weeks|years|...])
  	 *
  	 * @param (int) $interval
  	 * @return (void)
  	 * @see getInterval(), $interval
  	 * @access private
  	 */
  	function setInterval( $interval = '' )
    {
    	if ( is_int( $interval ) )
      		$this->interval = $interval;
    	else
      		$this->interval = 1;
   	}
  
  	/**
  	 * Get $interval variable
  	 *
  	 * @return (int) $interval
  	 * @see setInterval(), $interval
  	 * @access public 
  	 */ 
   	function getInterval()
    {
    	return $this->interval;
    }
  
  	/**
  	 * Sets the end for a recurring event (0 = never ending, integer < 4 numbers = number of times, integer >= 4 enddate)
  	 *
  	 * @param (int) $freq
  	 * @return (void)
  	 * @see getRecEnd(), $rec_end
  	 * @access private
  	 */
  	function setRecEnd( $freq = '' )
    {
    	if ( strlen( trim( $freq ) ) < 1 )
      		$this->rec_end = 0;
    	else if ( is_int( $freq ) && strlen( trim( $freq ) ) < 4 )
      		$this->rec_end = $freq;
    	else
      		$this->rec_end = (string)date( 'Ymd\THi00\Z', $freq );
    }
  
  	/**
  	 * Get $rec_end variable
  	 *
  	 * @return (mixed) $rec_end
  	 * @see setRecEnd(), $rec_end
  	 * @access public
  	 */ 
   	function getRecEnd()
    {
    	return $this->rec_end;
    }

  	/**
  	 * Sets the frequency of a recurring event
  	 *
  	 * @param (int) $int  Integer 0-7
  	 * @return (void)
  	 * @see getFrequency(), $frequencies
  	 * @access private
  	 */
  	function setFrequency( $int = 0 )
    {
    	if ( is_int( $int ) && preg_match( '(^([0-7]{1})$)', $int ) )
      		$this->frequency = $int;
    	else
      		$this->frequency = 0;
    }
  
  	/**
  	 * Get $frequency variable
  	 *
  	 * @return (string) $frequencies
  	 * @see setFrequency(), $frequencies
  	 * @access public 
  	 */ 
   function getFrequency()
   {
    	$frequencies = array(
			0 => 'ONCE',
			1 => 'SECONDLY',
			2 => 'MINUTELY',
			3 => 'HOURLY',
			4 => 'DAILY',
			5 => 'WEEKLY',
			6 => 'MONTHLY',
			7 => 'YEARLY',
		);

  		if ( array_key_exists( $this->frequency, $frequencies ) )
      		return $frequencies[$this->frequency];
    	else
      		return $frequencies[0];
    }
  
  	/**
  	 * Encodes a string for QUOTE-PRINTABLE
  	 *
  	 * @param (string) $quotprint  String to be encoded
  	 * @return (string)  Encodes string
  	 * @access private
  	 */
  	function quotedPrintableEncode( $quotprint )
    { 
    	$quotprint = str_replace( '\r\n', chr( 13 ) . chr( 10 ), $quotprint );
    	$quotprint = str_replace( '\n',   chr( 13 ) . chr( 10 ), $quotprint );
    	$quotprint = preg_replace( "~([\x01-\x1F\x3D\x7F-\xFF])~e", "sprintf('=%02X', ord('\\1'))", $quotprint );
    	$quotprint = str_replace( '\=0D=0A','=0D=0A', $quotprint );
    
		return $quotprint;
    }
	
  	/**
  	 * Checks if a given string is a valid iso-language-code
  	 *
  	 * @param (string) $code  String that should validated
  	 * @return (boolean) isvalid  If string is valid or not
  	 * @access private
  	 */
  	function isValidLanguageCode( $code )
    {
    	$isvalid = false;
    
		if ( preg_match( '(^([a-z]{2})$|^([a-z]{2}_[a-z]{2})$|^([a-z]{2}-[a-z]{2})$)', trim( $code ) ) > 0 )
      		$isvalid = true;
    
    	return $isvalid;  
    }

  	/**
  	 * Set $lang variable
 	 *
  	 * @param (string) $isocode
  	 * @return (void)
  	 * @see getStartDate(), $startdate
  	 * @access private
  	 */
  	function setLanguage( $isocode = '' )
    {
    	if ( $this->isValidLanguageCode( $isocode ) == true )
      		$this->lang = ';LANGUAGE=' . $isocode; 
    	else
      		$this->lang = '';
    }
    
  	/**
  	 * Get $lang variable
  	 *
  	 * @return (int) $startdate
  	 * @see setStartDate(), $startdate
  	 * @access public
  	 */ 
  	function getLanguage()
    {
    	return $this->lang;
    }
  	
  	/**
  	 * Set $organizer variable
  	 *
  	 * @param (string) $organizer
  	 * @return (void)
  	 * @see getOrganizer(), $organizer
  	 * @access private 
 	 */
  	function setOrganizer( $organizer = '' )
    {
    	if ( strlen( trim( $organizer ) ) > 0 )
      		$this->organizer = $organizer;
    	else
      		$this->organizer = 'Abstractpage CMS';
    }
    
  	/**
  	 * Get $organizer variable
  	 *
  	 * @return (string) $organizer
  	 * @see setOrganizer(), $organizer
  	 * @access public  
  	 */
  	function getOrganizer()
    {
    	return $this->organizer;
    }
  
  	/**
  	 * Set $startdate_ts variable
  	 *
  	 * @param (int) $timestamp
  	 * @return (void)
  	 * @see getStartDateTS(), $startdate_ts
  	 * @access private
  	 */
  	function setStartDateTS( $timestamp = 0 )
    {
    	if (is_int( $timestamp ) && $timestamp > 0)
      	{
      		$this->startdate_ts = $timestamp;
      	}
    	else
      	{
      		if ( isset( $this->enddate_ts ) && is_numeric( $this->enddate_ts ) && $this->enddate_ts > 0 )
        		$this->startdate_ts = $this->enddate_ts - 3600;
      		else
        		$this->startdate_ts = time();
      	}
    }
    
  	/**
  	 * Get $startdate_ts variable
  	 *
  	 * @return (int) $startdate_ts
  	 * @see setStartDateTS(), $startdate_ts
  	 * @access public
  	 */  
  	function getStartDateTS()
    {
    	return $this->startdate_ts;
    }
  
  	/**
  	 * Set $enddate_ts variable
  	 *
  	 * @param (int) $timestamp
  	 * @return (void)
   	 * @see getEndDateTS(), $enddate_ts
  	 * @access private
  	 */
  	function setEndDateTS( $timestamp = 0 )
    {
    	if ( is_int( $timestamp ) && $timestamp > 0 )
      	{
      		$this->enddate_ts = $timestamp;
      	}
    	else
      	{
      		if ( isset( $this->startdate_ts ) && is_numeric( $this->startdate_ts ) && $this->startdate_ts > 0 )
        		$this->enddate_ts = $this->startdate_ts + 3600;
      		else
        		$this->enddate_ts = time() + 3600;
      	}
    }
    
  	/**
  	 * Get $enddate_ts variable
  	 *
  	 * @return (int) $enddate_ts
  	 * @see setEndDateTS(), $enddate_ts
  	 * @access public
  	 */ 
  	function getEndDateTS()
    {
    	return $this->enddate_ts;
    }
  
  	/**
  	 * Set $startdate variable
  	 *
  	 * @param (int) $timestamp
  	 * @return (void)
  	 * @see getStartDate(), $startdate
  	 * @access private
  	 */
  	function setStartDate( $timestamp = 0 )
    {
    	$this->setStartDateTS( $timestamp );
    	$this->startdate = (string)date( 'Ymd\THi00\Z', $this->startdate_ts );
    }
    
  	/**
  	 * Get $startdate variable
  	 *
  	 * @return (int) $startdate
  	 * @see setStartDate(), $startdate
  	 * @access public
  	 */ 
  	function getStartDate()
    {
    	return $this->startdate;
    }
  
  	/**
  	 * Set $enddate variable
  	 *
  	 * @param (int) $timestamp
  	 * @return (void)
  	 * @see getEndDate(), $enddate
  	 * @access private
  	 */
  	function setEndDate( $timestamp = 0 )
    {
    	$this->setEndDateTS( $timestamp );
    	$this->enddate = (string)date( 'Ymd\THi00\Z', $this->enddate_ts );
    }
    
  	/**
  	 * Get $enddate variable
  	 *
  	 * @return (string) $enddate
  	 * @see setEndDate(), $enddate
  	 * @access public
  	 */ 
  	function getEndDate()
    {
    	return $this->enddate;
    }
  
  	/**
  	 * Set $location variable
  	 *
  	 * @param (string) $location
  	 * @return (void)
  	 * @see getLocation(), $location
  	 * @access private
  	 */
  	function setLocation( $location = '' )
    {
    	if ( strlen( trim( $location ) ) > 0 )
      		$this->location = $location;
    	else
      		$this->location = '';
    }
    
  	/**
  	 * Get $location variable
  	 *
  	 * @return (string) $location
  	 * @see setLocation(), $location
  	 * @access public
  	 */ 
  	function getLocation()
    {
    	return $this->location;
    }
  
  	/**
  	 * Set $transp variable
  	 *
  	 * @param (int) $int  0|1
  	 * @return (void)
  	 * @see getTransp(), $transp
  	 * @access private
  	 */
  	function setTransp( $int = 0 )
    {
    	if ( is_int( $int ) )
      		$this->transp = $int;
      	else
      		$this->transp = 0;
    }
    
  	/**
  	 * Get $transp variable
  	 *
  	 * @return (int) $transp
  	 * @see setTransp(), $transp
  	 * @access public
  	 */ 
  	function getTransp()
    {
    	$transps = array(
			0 => 'OPAQUE',
			1 => 'TRANSPARENT'
		);
    
		if ( array_key_exists( $this->transp, $transps ) )
      		return $transps[$this->transp];
    	else
      		return $transps[0];
    }
  
  	/**
  	 * Set $sequence variable
  	 *
  	 * @param (int) $int
  	 * @return (void)
  	 * @see getSequence(), $sequence
  	 * @access private
  	 */
  	function setSequence( $int = 0 )
    {
    	if ( is_int( $int ) )
      		$this->sequence = $int;
    	else
      		$this->sequence = 0;
    }
  
  	/**
  	 * Get $sequence variable
  	 *
  	 * @return (int) $sequence
   	 * @see setSequence(), $sequence
  	 * @access public
  	 */     
  	function getSequence()
    {
    	return $this->sequence;
    }
  
  	/**
  	 * Set $uid variable
  	 *
  	 * @return (void)
  	 * @see getUID(), $uid
  	 * @access private
 	 */
  	function setUID()
    {
    	$rawid = $this->startdate . 'plus' . $this->enddate;
    	$this->uid = (string)md5( $rawid );
    }
    
  	/**
  	 * Get $uid variable
  	 *
  	 * @return (string) $uid
  	 * @see setUID(), $uid
  	 * @access public
  	 */  
  	function getUID()
    {
    	return $this->uid;
    }

  	/**
  	 * Set $event_timestamp variable
  	 *
  	 * @return (void)
  	 * @see getEventTimestamp(), $event_timestamp
  	 * @access private
  	 */
  	function setEventTimestamp()
    {
    	$this->event_timestamp = (string)date( 'Ymd\THi00\Z', time() );
    }
    
  	/**
  	 * Get $event_timestamp variable
  	 *
  	 * @return (string) $event_timestamp
  	 * @see setEventTimestamp(), $event_timestamp
  	 * @access public
  	 */ 
  	function getEventTimestamp()
    {
    	return $this->event_timestamp;
    }
  
  	/**
  	 * Set $categories_array variable
  	 *
  	 * @return (void)
  	 * @see getCategoriesArray(), $categories_array
  	 * @access private
  	 */
  	function setCategoriesArray( $categories )
    {
    	$this->categories_array = (array)$categories;
    }
    
  	/**
  	 * Get $categories_array variable
  	 *
  	 * @return (array) $categories_array
  	 * @see setCategoriesArray(), $categories_array
  	 * @access public
  	 */ 
  	function getCategoriesArray()
    {
    	return $this->categories_array;
    }
  
  	/**
  	 * Set $categories variable
  	 *
  	 * @return (void)
  	 * @see getCategories(), $categories
  	 * @access private
  	 */
  	function setCategories( $categories )
    {
    	$this->setCategoriesArray( $categories );
    	$this->categories = $this->quotedPrintableEncode( implode( ',', $categories ) );
    }
    
  	/**
  	 * Get $categories variable
  	 *
  	 * @return (string) $categories
  	 * @see setCategories(), $categories
  	 * @access public
  	 */ 
  	function getCategories()
    {
    	return $this->categories;
    }
  
  	/**
  	 * Set $description variable
  	 *
  	 * @return (void)
  	 * @see getDescription(), $description
  	 * @access private
  	 */
  	function setDescription( $description )
    {
    	$this->description = str_replace( '\n', '=0D=0A=', str_replace( '\r', '=0D=0A=', $description ) );
    }
    
  	/**
  	 * Get $description variable
  	 *
  	 * @return (string) $description
  	 * @see setDescription(), $description
  	 * @access public
  	 */ 
  	function getDescription()
    {
    	return $this->description;
    }
  
  	/**
  	 * Set $summary variable
  	 *
  	 * @return (void)
  	 * @see getSummary(), $summary
  	 * @access private
  	 */
  	function setSummary( $summary )
    {
    	$this->summary = $summary;
    }
    
  	/**
  	 * Get $summary variable
  	 *
  	 * @return (string) $summary
  	 * @see setSummary(), $summary
  	 * @access public
  	 */ 
  	function getSummary()
    {
    	return $this->summary;
    }
  
  	/**
  	 * Set $priority variable
  	 *
  	 * @return (void)
  	 * @see getPriority(), $priority
  	 * @access private
  	 */
  	function setPriority( $int = 5 )
    {
    	if ( is_int( $int ) && preg_match( '(^([0-9]{1})$)', $int ) )
      		$this->priority = $int;
    	else
      		$this->priority = 5;
    }
    
  	/**
  	 * Get $priority variable
  	 *
  	 * @return (string) $priority
  	 * @see setPriority(), $priority
  	 * @access public
  	 */ 
  	function getPriority()
    {
    	return $this->priority;
    }
  
  	/**
  	 * Set $class variable
  	 *
  	 * @return (void)
  	 * @see getClass(), $class
  	 * @access private
  	 */
  	function setClass( $int = 0 )
    {
    	if ( is_int( $int ) )
      		$this->class = $int;
    	else
      		$this->class = 0;
    }
    
  	/**
  	 * Get $class variable
  	 *
  	 * @return (string) $class
  	 * @see setClass(), $class
  	 * @access public
  	 */ 
  	function getClass()
    {
    	$classes = array(
			0 => 'PRIVATE',
			1 => 'PUBLIC',
			2 => 'CONFIDENTIAL'
		);
    
		if ( array_key_exists( $this->class, $classes ) )
      		return $classes[$this->class];
    	else
      		return $classes[0];
    }

  	/**
  	 * Set $attendees variable
  	 *
  	 * @param (array) $attendees
  	 * @return (void)
  	 * @see getAttendees(), $attendees
  	 * @access private
  	 */
  	function setAttendees( $attendees = '' )
    {
    	if ( is_array( $attendees ) )
      		$this->attendees = $attendees;
    	else
      		$this->attendees = array();
    }
    
  	/**
  	 * Get $attendees variable
  	 *
  	 * @return (string) $attendees
  	 * @see setAttendees(), $attendees
  	 * @access public
  	 */
  	function &getAttendees()
    {
    	return $this->attendees;
    }
} // END OF ICalendarEvent

?>
