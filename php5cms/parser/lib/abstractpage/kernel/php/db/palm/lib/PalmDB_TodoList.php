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


/**
 * Class extender for Todo databases.
 *
 ** The data for setRecordRaw and from getRecordRaw should be/return a
 * special array, detailed below. Optional values can be set to '' or not
 * defined. If they are anything else (including zero), they are considered
 * to be 'set'. Optional values are marked with a ^.
 *
 * Key            Example             Description
 * ----------------------------------------------
 * Description    ToDo                This is the ToDo text
 * Note           Note                ^ A note for the todo
 * DueDate        2002-06-03          ^ Year-Month-Day of the todo
 * Completed      1                   ^ 1/0 (default 0 not completed)
 * Priority       3                   ^ 1-5 (default 1)
 *
 * If description is not specified, then the string 'No description' will be
 * used.
 *
 * @package db_palm_lib
 */
 
class PalmDB_TodoList extends PalmDB
{
   	var $dirty = 0;      // Unknown
   	var $sort_order = 0; // Unknown


 	/**
     * Constructor
	 */
   	function PalmDB_TodoList( $params = array() ) 
	{
      	$this->PalmDB( array( 
			'type'    => 'DATA', 
			'creator' => 'todo', 
			'name'    => 'ToDoDB' 
		) );
    
      	// Set a default CategoryList array.
      	$this->setCategoryList( array( 1 => 'Business', 'Personal' ) );
   	}

	
   	/**
	 * Returns a new array with default data for a new record.
     * This doesn't actually add the record.
	 */
   	function newRecord()
	{
      	// No due date by default.
      	$Item['DueDate'] = '';

      	$Item['Completed'] = 0;
      	$Item['Priority']  = 1;

      	$Item['Description'] = '';
      	$Item['Note'] = '';

      	return $Item;
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

      	// Due_date (1).
      	// Completed and Priority (2).
      	// NULL for description
      	// NULL for note
      	$Bytes = 5;

      	if ( isset( $data['Description'] ) )
         	$Bytes += strlen( $data['Description'] );

      	if ( isset( $data['Note'] ) )
         	$Bytes += strlen( $data['Note'] );

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

      	if ( isset( $data['DueDate'] ) )
         	$rawDate = $this->dateToInt16( $data['DueDate'] );
      	else
         	$rawDate = 0xffff;
      
      	$RecordString .= $this->int16( $rawDate );

      	if ( isset( $data['Priority'] ) )
      	 	$priority = $data['Priority'];
      	else
         	$priority = 1;

      	if ( $priority < 1 )
         	$priority = 1;
      
	  	if ( $priority > 5 )
         	$priority = 5;
      
	  	if ( isset( $data['Completed'] ) && $data['Completed'] )
         	$priority |= 0x80;
      
      	$RecordString .= $this->int8( $priority );

      	if ( isset( $data['Description'] ) && $data['Description'] != '' )
         	$RecordString .= $this->string( $data['Description'] );
      	else
         	$RecordString .= $this->string( 'No description' );
      
	  	$RecordString .= $this->int8( 0 );
      
      	if ( isset( $data['Note'] ) )
         	$RecordString .= $this->string( $data['Note'] );
      
	  	$RecordString .= $this->int8( 0 );
      	return $RecordString;
   	}

   	/**
	 * Sets the data for the current record.
	 */
   	function setRecordRaw( $A, $B = false ) 
	{
      	if ( $B === false ) 
		{
         	$B = $A;
         	$A = $this->current_record;
      	}
      
	  	if ( !isset( $B['Priority'] ) )
         	$B['Priority'] = 1;
      
      	$this->records[$A] = $B;
   	}

   	/**
	 * Returns the size of the AppInfo block. It is the size of the
     * category list plus six bytes.
	 */
   	function getAppInfoSize()
	{
      	return PALMDB_CATEGORY_SIZE + 6;
   	}

   	/**
	 * Returns the AppInfo block. It is composed of the category list.
	 */
   	function getAppInfo()
	{
      	// Category List.
      	$AppInfo = $this->createCategoryData();

      	// Two unknown (reserved?) bytes
      	// I'm using 0 as the default value since I don't know what it should be
      	$AppInfo .= $this->int16( 0 );
      
	  	// Two bytes for "dirty" information?
      	$AppInfo .= $this->int16( $this->dirty );
      
	  	// One byte for sort order?
      	$AppInfo .= $this->int8( $this->sort_order );
      
	  	// One null byte to land on an even boundary
      	$AppInfo .= $this->int8( 0 );
      
	  	return $AppInfo;
   	}

   	/**
	 * Parse $fileData for the information we need when loading a Todo
     * file.
	 */
   	function loadAppInfo( $fileData ) 
	{
      	$this->loadCategoryData( $fileData );
      
	  	// Skip past the category information and some unknown data
      	$fileData = substr( $fileData, PALMDB_CATEGORY_SIZE + 2 );
      	$this->dirty = $this->loadInt16( $fileData );
      	$fileData = substr( $fileData, 2 );
      	$this->sort_order = $this->loadInt8( $fileData );
   	}

   	/**
	 * Converts the todo record data loaded from a file into the internal
     * storage method that is used for the rest of the class and for ease of
     * use. Return false to signal no error.
	 */
   	function loadRecord( $fileData, $RecordInfo ) 
	{
      	$this->record_attrs[$RecordInfo['UID']] = $RecordInfo['Attrs'];
		$NewRec = $this->newRecord();

      	// Load date
      	$date = $this->loadInt16( substr( $fileData, 0, 2 ) );
      
	  	if ( $date != 0xffff )
         	$NewRec['DueDate'] = $this->int16ToDate( $date );

      	$priority   = $this->loadInt8( substr( $fileData, 2, 1 ) );
      	$completed  = $priority & 0x80;
      	$priority  &= 0x7f;
      	
		$NewRec['Completed'] = $completed? 1 : 0;
      	$NewRec['Priority']  = $priority;

      	$i = 3;
      	$max = strlen( $fileData );
      	$description = '';
		
      	while ( $fileData[$i] != "\0" ) 
		{
         	// echo "Looping\n";
         	$description .= $fileData[$i];
         	$i++;
	 
	 		if ( $i > $max )
	    		return true;
      	}
      
	  	$i++;
      	$note = '';
      
	  	while ( $fileData[$i] != "\0" ) 
		{
         	$note .= $fileData[$i];
         	$i++;
	 
	 		if ( $i > $max )
	    		return true;
      	}
      
	  	$NewRec['Description'] = $description;
      	$NewRec['Note'] = $note;
      
      	$this->records[$RecordInfo['UID']] = $NewRec;
   	}
} // END OF PalmDB_TodoList

?>
