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


define( 'PALMDB_SMALLBASIC_MAIN_SECTION', 'Main' );
define( 'PALMDB_SMALLBASIC_MAX_SECTION',  32766  );
define( 'PALMDB_SMALLBASIC_FILE_VERSION', 4 );


/**
 * Class extender for SmallBASIC source files.
 *
 * @package db_palm_lib
 */

class PalmDB_SmallBasic extends PalmDB
{
	/**
     * Constructor
	 */
   	function PalmDB_SmallBasic( $params = array() )
   	{
   		$this->PalmDB( array( 
			'type'    => 'TEXT', 
			'creator' => 'SmBa', 
			'name'    => isset( $params['name' ] )? $params['name' ] : '' 
		) );
      
	  	$this->records = array();
 	}


   	/**
	 * Returns a giant string that can be saved to a file and loaded by
     * other SmallBASIC interpreters.
	 */
   	function convertToText()
	{
      	$RecordNames = $this->getOrderedSectionNames;
      	$String = '';
     
      	foreach ( $RecordNames as $Name ) 
		{
         	if ( $String != '' )
	    		$String .= "\n";
         
		 	if ( $Name == PALMDB_SMALLBASIC_MAIN_SECTION ) 
			{
	    		$Data = explode( "\n", $this->records[$Name], 2 );
	    
				if ( $Data[0][0] == '#' )
	       			$String .= $Data[0] . '#sec:' . $Name . "\n";
	    		else
	       			$String .= '#sec:' . $Name . "\n" . $Data[0];
	    
	    		if ( isset( $Data[1] ) )
	       			$String .= "\n" . $Data[1];
	 		} 
			else 
			{
	    		$String .= '#sec:' . $Name . "\n" . $this->records[$Name];
	 		}
      	}
      
      	return $String; 
   	}
   
   	/** 
	 * Sets all of the records properly to the SmallBASIC code passed in.
     * Returns 0 on success
     *  Returns array(section num, section name, section bytes)
     * if the text of a section is > 32k
	 */
   	function convertFromText( $String ) 
	{
      	$this->records = array();
      
      	// Convert newlines to \n
      	$String = str_replace( "\r\n", "\n", $String );
      	$String = str_replace( "\r",   "\n", $String );
      
      	$Lines = explode( "\n", $String );
      	$ThisName = PALMDB_SMALLBASIC_MAIN_SECTION;
      
	  	while ( count( $Lines ) ) 
		{
         	if ( strncmp( $Lines[0], '#sec:', 5 ) == 0 ) 
			{
	    		$ThisName = array_shift( $Lines );
	    		$ThisName = substr( $ThisName, 5 );
	 		} 
			else 
			{
	    		if ( isset( $this->records[$ThisName] ) )
	       			$this->records[$ThisName] .= "\n";
	    		else
	       			$this->records[$ThisName] = '';
	    
				$this->records[$ThisName] .= array_shift( $Lines );
	 		}
      	}

      	$SectionNames = $this->getOrderedSectionNames();
      
	  	foreach ( $SectionNames as $index => $Name ) 
		{
	 		if ( strlen( $this->records[$Name] ) > PALMDB_SMALLBASIC_MAX_SECTION )
	    		return array( $index, $this->records[$Name],  strlen( $this->records[$Name] ) );
      	}
      
      	return false;
   	}
   
   	/**
	 * Returns the sorted list of section names.
     * Forces 'Main' to be first.
	 */
   	function getOrderedSectionNames()
	{
      	$keys = array_keys( $this->records );
      	sort( $keys );
      
	  	if ( !isset( $this->records[PALMDB_SMALLBASIC_MAIN_SECTION] ) || $keys[0] == PALMDB_SMALLBASIC_MAIN_SECTION )
         	return $keys;
      
	  	$SkipName = $keys[0];
      	$index = 1;
      
	  	while ( $keys[$index] != PALMDB_SMALLBASIC_MAIN_SECTION )
         	$index++;
      
	  	while ( $index )
         	$keys[$index] = $keys[$index - 1];
      
      	$keys[0] = PALMDB_SMALLBASIC_MAIN_SECTION;
      	return $keys;
   	}

   	/**
	 * Returns the size of the specified record.
	 */
   	function getRecordSize( $num = false ) 
	{
      	if ( $num === false )
         	$num = $this->current_record;
	 
      	if ( $num == 0 )
         	return 68;
	 
      	$keys = $this->getOrderedSectionNames();
      	$num--;
      
	  	if ( !isset( $keys[$num] ) )
         	return 0;
	 
      	return strlen( $this->records[$keys[$num]] ) + 70;
   	}
   
   	/**
	 * Returns the data of the specified record, or the current record if no
     * record is specified.  If the record doesn't exist, returns ''.
	 */
   	function getRecord( $num = false ) 
	{
      	if ( $num === false )
         	$num = $this->current_record;
	 
      	if ( $num == 0 )
         	return $this->createRecordZero();
	 
      	$num--;
      	$keys = $this->getOrderedSectionNames();
      
      	if ( !isset( $keys[$num] ) )
         	return '';

      	$Str  = $this->string( 'S' );  // Sign
      	$Str .= $this->int8( 0 );     // Unused
      	$Str .= $this->int16( PALMDB_SMALLBASIC_FILE_VERSION );  // version
       
	   	// Not sure if this is always the same as the one in the
	 	// header. Since it was the same when I was given the specs, 
	 	// I just used the define()d value.
      	if ( $keys[$num] == PALMDB_SMALLBASIC_MAIN_SECTION )
         	$Str .= $this->int16( 1 ); // Flags (1 for main section)
      	else
         	$Str .= $this->int16( 0 ); // Flags (0 for every other section)
      
	  	$name  = $this->string( $keys[$num] );
      	$name  = substr( $name, 0, 63 ); // Trim if it is too long of a name
      	$Str  .= $this->padString( $name, 64 );

      	$code  = $this->records[$keys[$num]];
      	$code  = substr( $code, 0, PALMDB_SMALLBASIC_MAX_SECTION );
      	$Str  .= $this->string( $code );

      	return $Str . '00';
   	}
   
   	/**
	 * Returns what should be record zero.
	 */
   	function createRecordZero()
	{
      	$Str  = $this->string( 'H' ); // Sign
      	$Str .= $this->int8( PALMDB_SMALLBASIC_FILE_VERSION );
      	
		$Str .= '0000'; // First byte = unused.
                        // Second = category (unfiled)
      
	  	$Str .= $this->padString( '', 64 );
      					// First four bytes = flags
      					//   0x00000001 = compressed (not supported yet)
      					//   0x00000002 = PalmOS script
      
      	return $Str;
   	}
   
   	/**
	 * Returns a list of records to write to a file in the order specified.
	 */
   	function getRecordIDs( )
	{
      	$keys   = array_keys( $this->records );
      	$keys[] = 'blah';
		
      	return array_keys( $keys );
   	}
   
   	/**
	 * Returns the number of records to write.
	 */
   	function getRecordCount()
	{
      	return count( $this->records ) + 1;
   	}
   
   	/**
	 * Converts the PalmOS record to our internal format.
	 * Returns false to signal no error.
	 */
   	function loadRecord( $fileData, $RecordInfo ) 
	{
      	// Throw away record 0 since we can just regenerate it easily enough
      	// (Note:  We're losing the category info.  If that is important
      	//         enough, it is easy enough to grab.)
      	if ( $RecordInfo['UID'] == 0 )
         	return false;
	 
      	// Grab the name
      	$i = 6;
      	$Name = '';
      
	  	while ( $fileData[$i] != "\0" )
		{
         	$Name .= $fileData[$i];
	 		$i++;
      	}
      
      	$this->records[$Name] = substr( $fileData, 70, strlen( $fileData ) - 71 );
      	return false;
   	}
} // END OF PalmDB_SmallBasic

?>
