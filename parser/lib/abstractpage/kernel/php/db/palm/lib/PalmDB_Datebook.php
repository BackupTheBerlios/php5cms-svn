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
|Authors: Eduardo Pascual Martinez                                     |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'db.palm.lib.PalmDB' );


// Repeat types
define( 'PALMDB_DATEBOOK_REPEAT_NONE',          0 );
define( 'PALMDB_DATEBOOK_REPEAT_DAILY',         1 );
define( 'PALMDB_DATEBOOK_REPEAT_WEEKLY',        2 );
define( 'PALMDB_DATEBOOK_REPEAT_MONTH_BY_DAY',  3 );
define( 'PALMDB_DATEBOOK_REPEAT_MONTH_BY_DATE', 4 );
define( 'PALMDB_DATEBOOK_REPEAT_YEARLY',        5 );


// Record flags
define( 'PALMDB_DATEBOOK_FLAG_DESCRIPTION',  1024 ); // Record has description
                                            		 // (mandatory, as far as I know)
define( 'PALMDB_DATEBOOK_FLAG_EXCEPTIONS',   2048 ); // Are there any exceptions?
define( 'PALMDB_DATEBOOK_FLAG_NOTE',         4096 ); // Is there an associated note?
define( 'PALMDB_DATEBOOK_FLAG_REPEAT',       8192 ); // Does the event repeat?
define( 'PALMDB_DATEBOOK_FLAG_ALARM',       16384 ); // Is there an alarm set?
define( 'PALMDB_DATEBOOK_FLAG_WHEN',        32768 ); // Was the 'when' updated? 
                                         			 // (Internal use only?)

													 
/**
 * Class extender for PalmOS Datebook files.
 *
 * The data for setRecordRaw and from getRecordRaw should be/return a
 * special array, detailed below. Optional values can be set to '' or not
 * defined.  If they are anything else (including zero), they are considered
 * to be 'set'. Optional values are marked with a ^.
 *
 * Key           Example          Description
 * ------------------------------------------
 * StartTime     2:00             Starting time of event, 24 hour format
 * EndTime       13:00            Ending time of event, 24 hour format
 * Date          2001-01-23       Year-Month-Day of event
 * Description   Title            This is the title of the event
 * Alarm         5d               ^ Number of units (m=min, h=hours, d=days)
 * Repeat        ???              ^ Repeating event data (array)
 * Note          NoteNote         ^ A note for the event
 * Exceptions    ???              ^ Exceptions to the event
 * WhenChanged   ???              ^ True if "when info" for event has changed
 * Flags         3                ^ User flags (highest bit allowed is 512)
 *
 * EndTime must happen at or after StartTime. The time the event occurs
 * may not pass midnight (0:00). If the event doesn't have a time, use '' 
 * or do not define StartTime and EndTime.
 *
 * Repeating events:
 *
 *    No repeat (or leave the array undefined):
 *       $repeat['Type'] = PALMDB_DATEBOOK_REPEAT_NONE;
 *
 *    Daily repeating events:
 *       $repeat['Type'] = PALMDB_DATEBOOK_REPEAT_DAILY;
 *       $repeat['Frequency'] = FREQ;	// Explained below
 *       $repeat['End'] = END_DATE;		// Explained below
 *
 *    Weekly repeating events:
 *       $repeat['Type'] = PALMDB_DATEBOOK_REPEAT_WEEKLY;
 *       $repeat['Frequency'] = FREQ;	// Explained below
 *       $repeat['Days'] = DAYS;		// Explained below
 *       $repeat['End'] = END_DATE;		// Explained below
 *       $repeat['StartOfWeek'] = SOW;	// Explained below
 *
 *    "Monthly by day" repeating events:
 *       $repeat['Type'] = PALMDB_DATEBOOK_REPEAT_MONTH_BY_DAY;
 *       $repeat['WeekNum'] = WEEKNUM;	// Explained below
 *       $repeat['DayNum'] = DAYNUM;	// Explained below
 *       $repeat['Frequency'] = FREQ;	// Explained below
 *       $repeat['End'] = END_DATE;		// Explained below
 *
 *    "Monthly by date" repeating events:
 *       $repeat['Type'] = PALMDB_DATEBOOK_REPEAT_MONTH_BY_DATE;
 *       $repeat['Frequency'] = FREQ;	// Explained below
 *       $repeat['End'] = END_DATE;		// Explained below
 *
 *    Yearly repeating events:
 *       $repeat['Type'] = PALMDB_DATEBOOK_REPEAT_YEARLY;
 *       $repeat['Frequency'] = FREQ;	// Explained below
 *       $repeat['End'] = END_DATE;		// Explained below
 *
 *    There is also two mysterious 'unknown' fields for the repeat event that
 *    will be populated if the database is loaded from a file. They will
 *    otherwise default to 0. They are 'unknown1' and 'unknown2'.
 *
 *    FREQ = Frequency of the event. If it is a daily event and you want it
 *           to happen every other day, set Frequency to 2. This will default
 *           to 1 if not specified.
 *    END_DATE = The last day, month, and year on which the event occurs.
 *               Format is YYYY-MM-DD. If not specified, no end date will
 *               be set.
 *    DAYS = What days during the week the event occurs. This is a string of
 *           numbers from 0 - 6.  I'm not sure if 0 = Sunday or if 0 = 
 *           start of week from the preferences.
 *    SOW = As quoted from P5-Palm: "I'm not sure what this is, but the
 *          Datebook app appears to perform some hairy calculations
 *          involving this."
 *    WEEKNUM = The number of the week on which the event occurs. 5 is the
 *              last week of the month.
 *    DAYNUM = The day of the week on which the event occurs. Again, I don't
 *             know if 0 = Sunday or if 0 = start of week from the prefs.
 *
 * Exceptions are specified in an array consisting of dates the event occured
 * and did not happen or should not be shown.  Dates are in the format
 * YYYY-MM-DD
 *
 * @package db_palm_lib
 */
 
class PalmDB_Datebook extends PalmDB
{
   	var $first_day;
   
	/**
     * Constructor
	 */
   	function PalmDB_Datebook( $params = array() )
	{
   		$this->PalmDB( array( 
			'type'    => 'DATA', 
			'creator' => 'date', 
			'name'    => 'DatebookDB' 
		) );
      
	  	$this->first_day = 0;
   	}
   

   	/**
     * Returns an array with default data for a new record.
     * This doesn't actually add the record.
	 */
 	function newRecord()
   	{
      	// Default event is untimed
      	// Event's date is today
      	$Event['Date'] = date( "Y-m-d" );
      
      	// Set an alarm 10 min before the event
      	$Event['Alarm'] = '10m';
      
      	return $Event;
   	}
   
   	/**
	 * Prepares the record flags for the specified record;
     * Should only be used when saving.
	 */
   	function getRecordFlags( &$data ) 
	{
      	if ( !isset( $data['Flags'] ) )
         	$data['Flags'] = 0;
      
	  	$Flags = $data['Flags'] % 1024;
      
	  	if ( isset( $data['Description'] ) && $data['Description'] != '' )
         	$Flags += PALMDB_DATEBOOK_FLAG_DESCRIPTION;
      
	  	if ( isset( $data['Exceptions'] ) && is_array( $data['Exceptions'] ) && count( $data['Exceptions'] ) > 0 )
	 		$Flags += PALMDB_DATEBOOK_FLAG_EXCEPTIONS;
      
	  	if ( isset( $data['Note'] ) && $data['Note'] != '' )
         	$Flags += PALMDB_DATEBOOK_FLAG_NOTE;
      
	  	if ( isset( $data['Alarm'] ) && $data['Alarm'] != '' && preg_match( '/^([0-9]+)([mMhHdD])$/', $data['Alarm'], $AlarmMatch ) )
         	$Flags += PALMDB_DATEBOOK_FLAG_ALARM;
      
	  	if ( isset( $data['WhenChanged'] ) && $data['WhenChanged'] != '' && $data['WhenChanged'] )
	 		$Flags += PALMDB_DATEBOOK_FLAG_WHEN;
	 
      	// Slightly more complex when dealing with the repeat array
      	if ( isset( $data['Repeat'] ) && is_array( $data['Repeat'] ) && count( $data['Repeat'] ) > 0 && isset( $data['Repeat']['Type'] ) && $data['Repeat']['Type'] > PALMDB_DATEBOOK_REPEAT_NONE && $data['Repeat']['Type'] <= PALMDB_DATEBOOK_REPEAT_YEARLY )
	 		$Flags += PALMDB_DATEBOOK_FLAG_REPEAT;

      	$data['Flags'] = $Flags;
   	}
   
   	/**
	 * Overrides the GetRecordSize method.
     * Probably should only be used when saving.
	 */
   	function getRecordSize( $num = false ) 
	{
      	if ( $num === false )
         	$num = $this->current_record;
	 
      	if ( !isset( $this->records[$num] ) || !is_array( $this->records[$num] ) )
         	return PalmDB::getRecordSize( $num );
         
      	$data = $this->records[$num];
	 
      	// Start Time and End Time (4)
      	// The day of the event (2)
      	// Flags (2)
      	$Bytes = 8;
      	$this->getRecordFlags( $data );
      
		if ( $data['Flags'] & PALMDB_DATEBOOK_FLAG_ALARM )
			$Bytes += 2;
      
      	if ( $data['Flags'] & PALMDB_DATEBOOK_FLAG_REPEAT )
			$Bytes += 8;
      
		if ( $data['Flags'] & PALMDB_DATEBOOK_FLAG_EXCEPTIONS )
         	$Bytes += 2 + count( $data['Exceptions'] ) * 2;
      
      	if ( $data['Flags'] & PALMDB_DATEBOOK_FLAG_DESCRIPTION )
         	$Bytes += strlen( $data['Description'] ) + 1;
      
      	if ( $data['Flags'] & PALMDB_DATEBOOK_FLAG_NOTE )
         	$Bytes += strlen( $data['Note'] ) + 1;
      
      	return $Bytes;
   	}
   
   	/**
	 * Overrides the GetRecord method. We store data in associative arrays.
     * Just convert the data into the proper format and then return the
     * generated string.
	 */
   	function getRecord( $num = false ) 
	{
      	if ( $num === false )
         	$num = $this->current_record;
	 
      	if ( !isset( $this->records[$num] ) || !is_array( $this->records[$num] ) )
         	return PalmDB::getRecord( $num );
      
      	$data = $this->records[$num];
      	$RecordString = '';
      
      	// Start Time and End Time
      	// 4 bytes
      	// 0xFFFFFFFF if the event has no time
      	if ( !isset( $data['StartTime'] ) || ! isset( $data['EndTime'] ) || strpos( $data['StartTime'], ':' ) === false || strpos( $data['EndTime'], ':' ) === false ) 
		{
			$RecordString .= $this->int16( 65535 );
			$RecordString .= $this->int16( 65535 );
      	} 
		else 
		{
         	list( $StartH, $StartM ) = explode( ':', $data['StartTime'] );         
         	list( $EndH,   $EndM   ) = explode( ':', $data['EndTime']   );
	 
	 		if ( $StartH < 0 || $StartH > 23 || $StartM < 0 || $StartM > 59 || $EndH < 0 || $EndH > 23 || $EndM < 0 || $EndM > 59 ) 
			{
	    		$RecordString .= $this->int16( 65535 );
	    		$RecordString .= $this->int16( 65535 );
	 		} 
			else 
			{
	    		if ( $EndH < $StartH || ($EndH == $StartH && $EndM < $StartM ) ) 
				{
	       			$EndM = $StartM;
	       
		   			if ( $StartH < 23 )
  	          			$EndH = $StartH + 1;
	       			else
	          			$EndH = $StartH;
	    		}
	    
				$RecordString .= $this->int8( $StartH );
	    		$RecordString .= $this->int8( $StartM );
	    		$RecordString .= $this->int8( $EndH );
	    		$RecordString .= $this->int8( $EndM );
	 		}
      	}
      
		// The day of the event
		// For repeating events, this is the first day the event occurs
		$RecordString .= $this->int16( $this->dateToInt16( $data['Date'] ) );
      
      	// Flags
      	$this->getRecordFlags( $data );
      	$Flags = $data['Flags'];
      	$RecordString .= $this->int16( $Flags );
      
      	if ( $Flags & PALMDB_DATEBOOK_FLAG_ALARM && preg_match( '/^([0-9]+)([mMhHdD])$/', $data['Alarm'], $AlarmMatch ) ) 
		{
         	$RecordString  .= $this->int8( $AlarmMatch[1] );
	 		$AlarmMatch[2]  = strtolower( $AlarmMatch[2]  );
	 
	 		if ( $AlarmMatch[2] == 'm' )
	    		$RecordString .= $this->int8( 0 );
	 		else if ( $AlarmMatch[2] == 'h' )
	    		$RecordString .= $this->int8( 1 );
	 		else
	    		$RecordString .= $this->int8( 2 );
      	}
      
      	if ( $Flags & PALMDB_DATEBOOK_FLAG_REPEAT ) 
		{
         	$d = $data['Repeat'];
         	$RecordString .= $this->int8( $d['Type'] );
	 
	 		if ( !isset( $d['unknown1'] ) )
	    		$d['unknown1'] = 0;
	 
	 		$RecordString .= $this->int8( $d['unknown1'] );
      
	 		if ( isset( $d['End'] ) )
	    		$RecordString .= $this->int16( $this->dateToInt16($d['End'] ) );
	 		else
	    		$RecordString .= $this->int16( 65535 );
	 
	 		if ( !isset( $d['Frequency'] ) )
	    		$d['Frequency'] = 1;
	 
	 		$RecordString .= $this->int8( $d['Frequency'] );
	    
	 		if ( $d['Type'] == PALMDB_DATEBOOK_REPEAT_WEEKLY ) 
			{
	    		$days  = $d['Days'];
	    		$flags = 0;
	    		$QuickLookup = array( 1, 2, 4, 8, 16, 32, 64 );
	    		
				$i = 0;
	    		while ( $i < strlen( $days ) ) 
				{
	       			$num = $days[$i];
	       			settype( $num, 'integer' );
					
	       			if ( isset( $QuickLookup[$num] ) )
	          			$flags = $flags | $QuickLookup[$num];
	       
		   			$i++;
	    		}
	    
				$RecordString .= $this->int8( $flags );
				
	    		if ( isset( $d['StartOfWeek'] ) && $d['StartOfWeek'] != '' )
 	       			$RecordString .= $this->int8( $d['StartOfWeek'] );
	    		else
	       			$RecordString .= $this->int8( 0 );
	 		} 
			else if ( $d['Type'] == PALMDB_DATEBOOK_REPEAT_MONTH_BY_DAY ) 
			{
	    		if ( $d['WeekNum'] > 5 )
	       			$d['WeekNum'] = 5;
	    
				$RecordString .= $this->int8( $d['WeekNum'] * 7 + $d['DayNum'] );
	    		$RecordString .= $this->int8( 0 );
	 		} 
			else 
			{
	    		$RecordString .= $this->int16( 0 );
		 	}
	 
	 		if ( !isset( $d['unknown2'] ) )
	    		$d['unknown2'] = 0;
	 		
			$RecordString .= $this->int8( $d['unknown2'] );
      	}
      
      	if ( $Flags & PALMDB_DATEBOOK_FLAG_EXCEPTIONS ) 
		{
         	$d = $data['Exceptions'];
         	$RecordString .= $this->int16( count( $d ) );
	 
	 		foreach ( $d as $exception )
	    		$RecordString .= $this->int16( $this->dateToInt16( $exception ) );
      	}
      
      	if ( $Flags & PALMDB_DATEBOOK_FLAG_DESCRIPTION ) 
		{
         	$RecordString .= $this->string( $data['Description'] );
         	$RecordString .= $this->int8( 0 );
      	}
      
      	if ( $Flags & PALMDB_DATEBOOK_FLAG_NOTE ) 
		{
         	$RecordString .= $this->string( $data['Note'] );
	 		$RecordString .= $this->int8( 0 );
      	}
      
      	return $RecordString;
 	}
   
   	/**
	 * Returns the size of the AppInfo block. It is the size of the
     * category list plus four bytes.
	 */
   	function getAppInfoSize()
	{
      	return PALMDB_CATEGORY_SIZE + 4;
   	}
   
   	/**
	 * Returns the AppInfo block. It is composed of the category list (which
     * doesn't seem to be used and is just filled with NULL bytes) and four
     * bytes that specify the first day of the week. Not sure what that 
     * value is supposed to be, so I just use zero.
	 */
   	function getAppInfo()
	{
      	// Category list (Nulls)
      	$this->app_info = $this->padString( '', PALMDB_CATEGORY_SIZE );
      
      	// Unknown thing (first_day_in_week)
      	// 00 00 FD 00 == where FD is the first day in week.
      	// I'm using 0 as the default value since I don't know what it should be
      	$this->app_info .= $this->int16( 0 );
      	$this->app_info .= $this->int8( $this->first_day );
      	$this->app_info .= $this->int8( 0 );
      
      	return $this->app_info;
   	}
   
   	/**
	 * Parses $fileData for the information we need when loading a datebook
     * file.
	 */
   	function loadAppInfo( $fileData ) 
	{
      	$fileData = substr( $fileData, PALMDB_CATEGORY_SIZE + 2 );
      
	  	if ( strlen( $fileData < 1 ) )
         	return;
      
	  	$this->first_day = $this->loadInt8( $fileData );
   	}
   
   	/**
	 * Converts the datebook record data loaded from a file into the internal
   	 * storage method that is used for the rest of the class and for ease of
  	 * use.
  	 * Return false to signal no error.
	 */
   	function loadRecord( $fileData, $RecordInfo ) 
	{
      	$this->record_attrs[$RecordInfo['UID']] = $RecordInfo['Attrs'];
      
      	$NewRec = $this->newRecord();
      	$StartH = $this->loadInt8( substr( $fileData, 0, 1 ) );
      	$StartM = $this->loadInt8( substr( $fileData, 1, 1 ) );
      	$EndH   = $this->loadInt8( substr( $fileData, 2, 1 ) );
      	$EndM   = $this->loadInt8( substr( $fileData, 3, 1 ) );
      
	  	if ( $StartH != 255 && $StartM != 255 ) 
		{
         	$NewRec['StartTime'] = $StartH . ':';
	 
	 		if ( $StartM < 10 )
	    		$NewRec['StartTime'] .= '0';
	 
	 		$NewRec['StartTime'] .= $StartM;
      	}
      
	  	if ( $EndH != 255 && $EndM != 255 ) 
		{
         	$NewRec['EndTime'] = $EndH . ':';
	 
	 		if ( $EndM < 10 )
	    		$NewRec['EndTime'] .= '0';
	 
	 		$NewRec['EndTime'] .= $EndM;
      	}
      
	  	$NewRec['Date'] = $this->loadInt16( substr( $fileData, 4, 2 ) );
      	$NewRec['Date'] = $this->int16ToDate( $NewRec['Date'] );
      	$Flags = $this->loadInt16( substr( $fileData, 6, 2 ) );
      	$NewRec['Flags'] = $Flags;
      	$fileData = substr( $fileData, 8 );
      
      	if ( $Flags & PALMDB_DATEBOOK_FLAG_WHEN )
         	$NewRec['WhenChanged'] = true;
      
      	if ( $Flags & PALMDB_DATEBOOK_FLAG_ALARM ) 
		{
         	$amount = $this->loadInt8( substr( $fileData, 0, 1 ) );
	 		$unit   = $this->loadInt8( substr( $fileData, 1, 1 ) );
	 
	 		if ( $unit == 0 )
	    		$unit = 'm';
	 		else if ( $unit == 1 )
	    		$unit = 'h';
	 		else
	    		$unit = 'd';
	 
	 		$NewRec['Alarm'] = $amount . $unit;
	 		$fileData = substr( $fileData, 2 );
      	} 
		else
		{
         	unset( $NewRec['Alarm'] );
      	}
		
      	if ( $Flags & PALMDB_DATEBOOK_FLAG_REPEAT ) 
		{
         	$Repeat = array();
	 		$Repeat['Type'] = $this->loadInt8( substr( $fileData, 0, 1 ) );
	 		$Repeat['unknown1'] = $this->loadInt8( substr( $fileData, 1, 1 ) );
	 		$End = $this->loadInt16( substr( $fileData, 2, 2 ) );
	 		$Repeat['Frequency'] = $this->loadInt8( substr( $fileData, 4, 1 ) );
	 		$RepeatOn = $this->loadInt8( substr( $fileData, 5, 1 ) );
	 		$RepeatSoW = $this->loadInt8( substr( $fileData, 6, 1 ) );
	 		$Repeat['unknown2'] = $this->loadInt8( substr( $fileData, 7, 1 ) );
	 		$fileData = substr( $fileData, 8 );
	 
	 		if ( $End != 65535 && $End >= 0 )
  	    		$Repeat['End'] = $this->int16ToDate( $End );
      
         	if ( $Repeat['Type'] == PALMDB_DATEBOOK_REPEAT_WEEKLY ) 
			{
	    		$days = '';
	    
				if ( $RepeatOn & 64 )
	       			$days .= '0';

	    		if ( $RepeatOn & 32 )
	       			$days .= '1';
					
	    		if ( $RepeatOn & 16 )
	       			$days .= '2';
					
	    		if ( $RepeatOn & 8 )
	       			$days .= '3';
	    
				if ( $RepeatOn & 4 )
	       			$days .= '4';
					
	    		if ( $RepeatOn & 2 )
	       			$days .= '5';
					
	    		if ( $RepeatOn & 1 )
	       			$days .= '6';
					
	    		$Repeat['Days'] = $days;
	    		$Repeat['StartOfWeek'] = $RepeatSoW;
	 		} 
			else if ( $Repeat['Type'] == PALMDB_DATEBOOK_REPEAT_MONTH_BY_DAY ) 
			{
	    		$Repeat['DayNum'] = $RepeatOn % 7;
	    		$RepeatOn /= 7;
	    		settype( $RepeatOn, 'integer' );
	    		$Repeat['WeekNum'] = $RepeatOn;
	 		}
	 
	 		$NewRec['Repeat'] = $Repeat;
      	}

      	if ( $Flags & PALMDB_DATEBOOK_FLAG_EXCEPTIONS ) 
		{
         	$Exceptions = array();
	 		$number     = $this->loadInt16( substr( $fileData, 0, 2 ) );
	 		$fileData   = substr( $fileData, 2 );
	 
	 		while ( $number-- ) 
			{
	    		$Exc = $this->loadInt16( substr( $fileData, 0, 2 ) );
	    		$Exceptions[] = $this->int16ToDate( $Exc );
	    		$fileData = substr( $fileData, 2 );
	 		}
	 
	 		$NewRec['Exceptions'] = $Exceptions;
      	}
      
      	if ( $Flags & PALMDB_DATEBOOK_FLAG_DESCRIPTION ) 
		{
         	$i = 0;
	 		$NewRec['Description'] = '';
	 
	 		while ( $i < strlen( $fileData ) && $fileData[$i] != "\0" ) 
			{
	    		$NewRec['Description'] .= $fileData[$i];
	    		$i++;
	 		}
	 
	 		$fileData = substr( $fileData, $i + 1 );
      	}
      
      	if ( $Flags & PALMDB_DATEBOOK_FLAG_NOTE ) 
		{
         	$i = 0;
	 		$NewRec['Note'] = '';
	 
	 		while ( $i < strlen( $fileData ) && $fileData[$i] != "\0" ) 
			{
	    		$NewRec['Note'] .= $fileData[$i];
	    		$i++;
	 		}
	 
	 		$fileData = substr( $fileData, 0, $i + 1 );
      	}
      
      	$this->records[$RecordInfo['UID']] = $NewRec;
      	return false;
   	}
} // END OF PalmDB_Datebook

?>
