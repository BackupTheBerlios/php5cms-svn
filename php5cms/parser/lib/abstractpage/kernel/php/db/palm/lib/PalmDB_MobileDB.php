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


define( 'PALMDB_MOBILEDB_DBVERSION',           3 ); // As of Nov 22, 2001, the docs lie.
define( 'PALMDB_MOBILEDB_FILTER_TEXT_LENGTH', 40 );
define( 'PALMDB_MOBILEDB_FILTER_LENGTH',      44 );
define( 'PALMDB_MOBILEDB_SORT_LENGTH',         4 );


/**
 * Class extender for Handmark MobileDB databases.
 *
 * Database format detailed at
 *   http://www.handmark.com/products/mobiledb/dbstructure.htm
 *
 * Format is for MobileDB database version 3.0
 *
 * @package db_palm_lib
 */

class PalmDB_MobileDB extends PalmDB
{
   	var $mobiledb_version;  			// Version number
   	var $mobiledb_lock;  				// Hash of password
   	var $mobiledb_dontsearch;  			// True = DB is invisible to Find
   	var $mobiledb_editonselect;  		// True = Record should be edited by default
   	var $mobiledb_longdates;  			// True = Display dates in long format
   	
	var $mobiledb_reserved = array();  	// Reserved bytes
   	var $mobiledb_filters  = array();  	// The user's filters
   	var $mobiledb_sort     = array();  	// The user's sort criteria
   	
	var $field_labels  = -1;  			// Labels for the fields
   	var $preferences   = -1;  			// "Dynamic info"
   	var $data_type     = -1;  			// Data types for the fields
   	var $field_lengths = -1;  			// Visible field lengths for fields

	
	/**
     * Constructor
	 */
   	function PalmDB_MobileDB( $params = array() ) 
   	{
   		$this->PalmDB( array( 
			'type'    => 'Mdb1', 
			'creator' => 'Mdb1', 
			'name'    => isset( $params['name' ] )? $params['name' ] : '' 
		) );
      
	  	$this->initializeMobileDB();
   	}


   	/**
	 * Sets all of the variables to a good default value.
	 */
   	function initializeMobileDB()
	{
      	$this->setCategoryList( array(
			'Unfiled', 
			'FieldLabels', 
			'DataRecords',
			'DataRecordsFout', 
			'Preferences',
			'DataType', 
			'FieldLengths'
		) );
      
	  	$this->mobiledb_version      = PALMDB_MOBILEDB_DBVERSION;
      	$this->mobiledb_lock         = 0;
      	$this->mobiledb_dontsearch   = 0;
      	$this->mobiledb_editonselect = 0;
      	$this->mobiledb_longdates    = 0;
      	$this->mobiledb_reserved     = array(0, 0);
      	
		$this->mobiledb_filters = array(
			array( '', 0, 0 ),
			array( '', 0, 0 ),
			array( '', 0, 0 )
		);
      	
		$this->mobiledb_sort = array(
			array( 0, 0, 0 ),
			array( 0, 0, 0 ),
			array( 0, 0, 0 )
		);
	}

	/**
	 * Returns the size of the current record if no arguments.
	 * Returns the size of the specified record if arguments.
	 */
   	function getRecordSize( $num = false ) 
	{
      	if ( $num === false )
         	$num = $this->current_record;
      
	  	if ( !isset( $this->records[$num] ) )
         	return 0;
      
	  	return strlen( $this->getRecord( $num ) ) / 2;
   	}
   
   	/**
	 * Returns the hex-encoded data for the specified record or the current
     * record if not specified.
	 */
   	function getRecord( $Rec = false ) 
	{
      	if ( $Rec === false )
         	$Rec = $this->current_record;
      
	  	if ( !isset( $this->records[$Rec] ) )
         	return '';
      
	  	$RecStr = $this->int32( 4294967041 ) . $this->int16( 65280 ) . $this->int8( 0 );
		ksort( $this->records[$Rec] );
		
      	foreach ( $this->records[$Rec] as $id => $data ) 
		{
	 		$RecStr .= $this->int8( $id );
			
	 		if ( ( is_string( $data ) && $data != '' ) || ( !is_string( $data ) && $data != 0 ) )
	    		$RecStr .= bin2hex( $data );
	 
	 		$RecStr .= $this->int8( 0 );
      	}
      
	  	$RecStr .= $this->int8( 255 );
		return $RecStr;
	}   

	/**
	 * Returns the size of the AppInfo block.
	 */
   	function getAppInfoSize()
	{
      	// The "+ 6" after the category size is for the MobileDB Version & lock
      	// The "+ 2" at the end gets the AppInfo to a 4-byte boundary.
      	return PALMDB_CATEGORY_SIZE + 6 + ( PALMDB_MOBILEDB_FILTER_LENGTH * 3 ) + ( PALMDB_MOBILEDB_SORT_LENGTH * 3 ) + 2;
   	}
   
   	/**
	 * Returns the AppInfo block.
	 */
	function getAppInfo()
	{
      	$AppInfo  = $this->createCategoryData();
      	$AppInfo .= $this->int16( $this->mobiledb_version );
      	$AppInfo .= $this->int32( $this->mobiledb_lock );
      
      	// Filters
      	for ( $i = 0; $i < 3; $i++ ) 
		{
         	if ( isset( $this->mobiledb_filters[$i] ) &&  is_array( $this->mobiledb_filters[$i] ) && count( $this->mobiledb_filters[$i] ) == 3 )
				$filter = $this->mobiledb_filters[$i];
			else
				$filter = array( '', 0, 0 );
				
			// Not sure if they require null termination
			$textStr  = $this->string( $filter[0],  PALMDB_MOBILEDB_FILTER_TEXT_LENGTH );
			$AppInfo .= $this->padString( $textStr, PALMDB_MOBILEDB_FILTER_TEXT_LENGTH );
	 		$AppInfo .= $this->int8( $filter[1] );
			$AppInfo .= $this->int8( $filter[2] );
	 
	 		// Padding to make the 4-byte boundary
	 		$AppInfo .= $this->int16( 0 );
      	}

      	// Sort Info
      	for ( $i = 0; $i < 3; $i++ ) 
		{
         	if ( isset( $this->mobiledb_sort[$i] ) && is_array( $this->mobiledb_sort[$i] ) && count( $this->mobiledb_sort[$i] ) == 3 )
	    		$sort = $this->mobiledb_sort[$i];
	 		else
	    		$sort = array( 0, 0, 0 );
	    
	 		$AppInfo .= $this->int8( $sort[0] );
	 		$AppInfo .= $this->int8( $sort[1] );
	 		$AppInfo .= $this->int8( $sort[2] );
	 
	 		// Padding to make the 4-byte boundary
	 		$AppInfo .= $this->int8( 0 );
      	}
      
		// Pad to a 4-byte boundary
		$AppInfo .= $this->int16( 0 );
      
		return $AppInfo;
	}
   
	/**
	 * Generic function to load the AppInfo block into $this->appInfo.
     * Should only be called within this class.
     * Return false to signal no error.
	 */
   	function loadAppInfo( $fileData ) 
	{
      	$this->loadCategoryData( $fileData );
      	$fileData = substr( $fileData, PALMDB_CATEGORY_SIZE );
      	$this->mobiledb_version = $this->loadInt16( $fileData );
      
	  	// WARNING -- this is short-circuited
      	if ( $this->mobiledb_version != PALMDB_MOBILEDB_DBVERSION )
		{
         	$this->initializeMobileDB();
	 		return false;
      	} 
      
	  	$fileData = substr( $fileData, 2 );
      	$this->mobiledb_lock = $this->loadInt32( $fileData );
      	$fileData = substr( $fileData, 4 );
      	$this->mobiledb_filters = array();
      	$this->mobiledb_filters[] = $this->loadAppInfoFilter( $fileData );
      	$fileData = substr( $fileData, PALMDB_MOBILEDB_FILTER_LENGTH );
      	$this->mobiledb_filters[] = $this->loadAppInfoFilter( $fileData );
      	$fileData = substr( $fileData, PALMDB_MOBILEDB_FILTER_LENGTH );
      	$this->mobiledb_filters[] = $this->loadAppInfoFilter( $fileData );
      	$fileData = substr( $fileData, PALMDB_MOBILEDB_FILTER_LENGTH );
      	$this->mobiledb_sort = array();
      	$this->mobiledb_sort[] = $this->loadAppInfoSort( $fileData );
      	$fileData = substr( $fileData, PALMDB_MOBILEDB_SORT_LENGTH );
      	$this->mobiledb_sort[] = $this->loadAppInfoSort( $fileData );
      	$fileData = substr( $fileData, PALMDB_MOBILEDB_SORT_LENGTH );
      	$this->mobiledb_sort[] = $this->loadAppInfoSort( $fileData );
      
	  	return false;
   	}
   
	/**
	 * Loads a single filter from the string passed in.
	 */
   	function loadAppInfoFilter( $data ) 
	{
      	$text = false;
      
	  	// I'm not sure if they require null termination
      	for ( $i = 0; $i < PALMDB_MOBILEDB_FILTER_TEXT_LENGTH; $i++ ) 
		{
         	if ( bin2hex( $data[$i] ) == '00' ) 
			{
	    		$text = substr( $data, 0, $i );
	    		$i = PALMDB_MOBILEDB_FILTER_TEXT_LENGTH;
	 		}
      	}
      
	  	if ( $text === false )
         	$text = substr( $data, 0, PALMDB_MOBILEDB_FILTER_TEXT_LENGTH );
      
	  	$data    = substr( $data, PALMDB_MOBILEDB_FILTER_TEXT_LENGTH );
      	$fieldNo = $this->loadInt8( $data );
      	$data    = substr( $data, 1 );
      	$flags   = $this->loadInt8( $data );
      
	  	return array( $text, $fieldNo, $flags );
 	}
   
   	/**
	 * Loads a single sort criteria from the string passed in.
	 */
   	function loadAppInfoSort( $data ) 
	{
      	$fieldNo    = $this->loadInt8( $data );
      	$data       = substr( $data, 1 );
      	$descending = $this->loadInt8( $data );
      	$data       = substr( $data, 1 );
      	$type       = $this->loadInt8( $data );
      
	  	return array( $fieldNo, $descending, $type );
   	}

	/**
	 * Generic function to load a record.
	 * Should only be called within this class.
	 * Return false to signal no error.
	 */
   	function loadRecord( $fileData, $recordInfo ) 
	{
      	// There should be no 'Unfiled' records
      	if ( $recordInfo['Attrs'] & PALMDB_CATEGORY_MASK == 0 )
         	return true;
    
      	$d = $this->loadInt32( $fileData );
      
	  	// Check the first 6 bytes.  They must always be correct or else the
      	// record is invalid
      	if ( $this->loadInt32( $fileData ) != 4294967041 )
         	return true;
      
	  	$fileData = substr( $fileData, 4 );
      
	  	if ( $this->loadInt16( $fileData ) != 65280 )
         	return true;
      
	  	// 3 bytes removed -- it appears that an extra NULL is inserted here
      	$fileData = substr( $fileData, 3 );
      
      	// Every record is an array.  Data is separated by NULL characters
      	// Format = [# of field] [field data with no NULLs] [NULL]
      	// End of data = [0xFF]
      	$SaveInfo = array();
      	$Fieldnum = $this->loadInt8( $fileData );
      	$fileData = substr( $fileData, 1 );
     
	  	while ( $Fieldnum != 255 && strlen( $fileData ) > 1 ) 
		{
         	$fileData = explode( "\x00", $fileData, 2 );
	 		$SaveInfo[$Fieldnum] = $fileData[0];
	 		$fileData = $fileData[1];
	 		$Fieldnum = $this->loadInt8( $fileData );
	 		$fileData = substr( $fileData, 1 );
      	}
      
      	// Now that we have the data, just put it in the right spot.
      	if ( ( $recordInfo['Attrs'] & PALMDB_CATEGORY_MASK ) == 1 )
         	$this->field_labels = $recordInfo['UID'];
      	else if ( ( $recordInfo['Attrs'] & PALMDB_CATEGORY_MASK ) == 4 )
         	$this->preferences = $recordInfo['UID'];
      	else if ( ( $recordInfo['Attrs'] & PALMDB_CATEGORY_MASK ) == 5 )
         	$this->data_type = $recordInfo['UID'];
      	else if ( ( $recordInfo['Attrs'] & PALMDB_CATEGORY_MASK ) == 6 )
         	$this->field_lengths = $recordInfo['UID'];

      	$this->records[$recordInfo['UID']] = $SaveInfo;
      	$this->record_attrs[$recordInfo['UID']] = $recordInfo['Attrs'];

      	return false;
   	}
} // END OF PalmDB_MobileDB

?>
