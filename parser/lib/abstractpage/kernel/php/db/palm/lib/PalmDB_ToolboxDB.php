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
 * Class extender for PDA Toolbox databases.
 *
 * The data for the getRecord and setRecord functions should return/be fed
 * an array. The data in the array should match the format specified by the
 * data format string.
 *
 * For instance, if your data format string is 'sdkljasfgljhg'
 * Then your array should have X elements and look like this:
 *   $array[0] = 
 *   $array[1] = 
 *
 * @package db_palm_lib
 */
 
class PalmDB_ToolboxDB extends PalmDB
{
	/**
     * Constructor
	 */
   	function PalmDB_ToolboxDB(  $params = array() ) 
	{
      	// Are all called TOUR ?
      	$this->PalmDB( array( 
			'type'    => 'TOUR', 
			'creator' => isset( $params['creator' ] )? $params['creator' ] : '', 
			'name'    => isset( $params['name' ]    )? $params['name' ]    : '' 
		) );
   	}
   

   	/**
	 * Overrides the getRecordSize method.
	 */
   	function getRecordSize( $num = false ) 
   	{
      	if ( $num === false )
         	$num = $this->current_record;
	 
    	if ( !isset( $this->records[$num] ) || !is_array( $this->records[$num] ) )
         	return PalmDB::getRecordSize( $num );
         
      	$data = $this->records[$num];
		return $Bytes;
	}
   
	/**
	 * Overrides the GetRecord method. We store data in associative arrays.
	 * Just convert the data into the proper format and then return the
	 * generated string.
	 */
   	function getRecord( $num ) 
	{
      	if ( !isset( $this->records[$num] ) || !is_array( $this->records[$num] ) )
         	return PalmDB::getRecord( $num );
      
      	$data = $this->records[$num];
      	$RecordString = '';

      	return $RecordString;
   	}
   
   	/**
	 * Converts the datebook record data loaded from a file into the internal
     * storage method that is used for the rest of the class and for ease of
     * use.
	 */
   	function loadRecord( $fileData, $RecordInfo ) 
	{
      	$this->record_attrs[$RecordInfo['UID']] = $RecordInfo['Attrs'];
      	$NewRec = array();
		$this->records[$RecordInfo['UID']] = $NewRec;
   	}
} // END OF PalmDB_ToolboxDB

?>
