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
 * Simple csv file database implementation to allow PHP based applications
 * to have database facilities where none are otherwise available.
 * All file operations are transient, with the file being held open for the 
 * barest minimim of time. The data is held in arrays and written back to 
 * the file as appropriate.
 *
 * Functions intended as the public interfaces for the class return true or
 * false/Error on successful operation.
 *
 * @package db_file
 */

class CSVDB extends PEAR
{
	/**
	 * @access public
	 */
    var $db;
	
	/**
	 * @access public
	 */
    var $newrows;
	
	/**
	 * @access public
	 */
    var $dataFile = "data.dat";
	
	/**
	 * @access public
	 */
    var $dir = "data/";
	
	/**
	 * @access public
	 */
    var $assoc = false;
	
	/**
	 * @access public
	 */
    var $cryptKey = false;
	
	/**
	 * @access public
	 */
    var $lastUpd = "";
	
	/**
	 * @access public
	 */
    var $written = 0;
	
	/**
	 * @access public
	 */
	var $unpacked = '';


	/**
	 * Constructor
	 *
	 * Takes optional parameters to set filename, 
	 * directory, associative array mode and encryption key.
	 * These parameters may also be set directly as class properties.
	 *
	 * @access public
	 */
    function CSVDB( $df = '', $dd = '', $as = false, $ck = false ) 
    {
        $this->newrows = array();
        $this->db = array();

        if ( $df != '' )
            $this->dataFile = $df;
        
        if ( $dd != '' )
            $this->dir = $df;
        
        if ( $as !== false )
            $this->assoc = $as;
        
        if ( $ck !== false )
            $this->cryptKey = $ck;
    } 
	
	
	/**
	 * Opens specified file, returning true if successful, else false/Error, 
	 * an appropriate error text is loaded into the error property
	 * array db is loaded with data from the csv file.
	 * if associated mode is asserted, the first row of the csv file is used as
	 * array keys else numeric keys are used. Setting a text string in cryptKey 
	 * causes the csv file to be read via the decrypter.
	 *
	 * @access public
	 */
    function readDB() 
    {
		$cf  = $this->dir . $this->dataFile;
        $row = 0;
		$lu  = @filemtime( $cf );
		
		if ( $lu )
			$this->lastUpd = $lu;

        $fp = @fopen( $cf, 'rb' );
        
		if ( is_resource( $fp ) ) 
		{
            while ( $data = fgetcsv( $fp, 1024, "," ) ) 
			{
                if ( $this->cryptKey ) 
				{
                    for ( $n = 0; $n < count( $data ); $n++ )
                        $this->decrypt( $data[$n], $this->cryptKey );
                } 
				
                if ( ( $this->assoc === true ) && ( $row == 0 ) ) 
				{
                    $num = count( $data );
                    $key = $data;
                    $row = 1;
                } 
				else 
				{
                    $num = count( $data );
                    $tmp = array();
					
                    for ( $n = 0; $n < $num; $n++ ) 
					{
                        if ( $this->assoc === true )
                            $k = strtolower( $key[$n] );
                        else
                            $k = $n;
                        
                        $d = $data[$n];
                        $tmp[$k] = $d;
                    } 
					
                    $this->db[] = $tmp;
                } 
            }
			
			if ( function_exists( 'array_change_key_case' ) )
	            $this->db = array_change_key_case( $this->db, CASE_LOWER );

            return @fclose( $fp );
        } 
		else 
		{
			return PEAR::raiseError( "Cannot open $cf." );
        } 
    } 

	/** 
	 * Appends data from newrows array to file.
     *
	 * @return boolean
	 * @access public
	 */
    function appendDB() 
    {
        if ( $this->rowstowrite() == 0 )
 			return false;
        
        $tmp = '';
        $this->written = 0;
        
		for ( $n = 0; $n < count( $this->newrows ); $n++ )
		{
            $tmp .= $this->package( $this->newrows[$n] );
            $this->written++;
        } 
		
        $cf = $this->dir . $this->dataFile;
        $fp = @fopen( $cf, 'ab' );
		
        if ( is_resource( $fp ) ) 
		{
            if ( !@fwrite( $fp, $tmp ) ) 
			{
                @fclose( $fp );
				return PEAR::raiseError( "Cannot write to $cf." );
            } 
			
            $this->newrows = array();
            return @fclose( $fp );
        } 
		else 
		{
			return PEAR::raiseError( "Cannot open $cf." );
        } 
    }

	/**
	 * This function writes the contents of array db to a csv file.
	 * Using encryption if cryptKey is set.
	 * Behavior modifier: append causes the array to be appended to the 
	 * existing csv file, else file is overwritten.
	 * Behavior modifier: force, overrides a change detection mechanism. 
	 * readDB() records the file's timestamp. If force is not asserted and the file's 
	 * timestamp is found to have been changed between reading and writing,
	 * an attempt to overwrite the csv file by this method will fail with an error.
	 * force should be set to enable a new file to be created.
	 *
	 * @access public
	 */
    function writeDB( $append = false, $force = false )
    { 
        $cf = $this->dir . $this->dataFile;
        
		if ( !$force ) 
		{
            if ( $this->lastUpd != filemtime( $cf ) ) 
				return PEAR::raiseError( "$cf updated by others since last read." );
        } 
		
        $mode = ( $append? 'ab+' : 'wb+' );
		
        if ( $this->assoc )
            $loop = 0;
        else
            $loop = 1;
        
        $fp  = @fopen( $cf, $mode );
        $tmp = '';
		
        if ( is_resource( $fp ) ) 
		{
            foreach ( $this->db as $row ) 
			{
                if ( ( $loop == 0 ) && ( !$append ) ) 
				{
                    $loop  = 1;
                    $tmp  .= $this->package( array_keys( $row ) );
                    $tmp  .= $this->package( $row );
                    
					$this->written++;
                } 
				else 
				{
                    $test = implode( ' ', $row );
					
					// row is not null
                    if ( trim( $test ) > '' ) 
					{
                        $tmp .= $this->package( $row );
                        $this->written++;
                    } 
                } 
            } 
			
			// stop it squaking
            if ( $this->written == 0 ) 
                $tmp .= " ";
            
            if ( !@fwrite( $fp, $tmp ) ) 
			{
                @fclose( $fp );
				return PEAR::raiseError( "Cannot write to $cf (" . $this->written . " rows)." );
            } 
        } 
		else 
		{
			return PEAR::raiseError( "Cannot open $cf as file resource." );
        }
		 
        return @fclose( $fp );
    } 
	
	/**
	 * Returns entire database as a comma delimited bytestream.
	 *
	 * @access public
	 */
    function unpackDB()
    {
        if ( ( $this->readDB() === true ) && ( count( $this->db ) >= 1 ) ) 
		{
            foreach ( $this->db as $row )
                $this->unpacked .= $this->xpackage( $row );
        }
		
        return $this->unpacked;
    } 
	
	/**
	 * Deletes row indicated in $row plus additional rows as identified in $count
 	 * ex. if row = 5 and count = 3, rows 5,6,7 will be deleted.
	 * Count defaults to 1.
	 * BEWARE - multiple calls to delete() in ASCENDING ORDER
	 * the main db array renumbers itself automatically, thus if row 5 is deleted, 
	 * the old row 6 becomes 5, 7 becomes 6 etc.
	 * Calling delete(5), then delete(6) then delete(7) will actually 
	 * delete rows 5,7,9 from the db array as seen before the row 5 deletion.
	 * Deleting in DECENDING numerical order does NOT suffer this affect 
	 * and is the recommended method.
	 *
	 * @return boolean
	 * @access public
	 */
    function delete( $rownumber, $n = 1 ) 
    {
        $key_index = array_keys( array_keys( $this->db ), $rownumber );
        array_splice( $this->db, $key_index[0], 1 );
        
		if ( !$this->db ) 
			return PEAR::raiseError( "Delete $n row from $rownumber failed" . $this->rowCount() );
		else 
            return true;
    } 
	
	/**
	 * First parameter $row. value is generally obtained via a call to find().
 	 * Second parameter $aValues is an array in appropriate format for the 
	 * current mode of the db array, either having numeric keys for assoc = false
	 * or associative values for keys if assoc is true.
	 * All keys must exist in the db array or an error is returned.
	 *
	 * @return boolean
	 * @access public
	 */
    function update( $rownumber, $aValues ) 
    {
        $aValues = array_change_key_case( $aValues, CASE_LOWER );
		
        while ( list( $key, $val ) = each( $aValues ) ) 
		{
            if ( @array_key_exists( $key, $this->db[$rownumber] ) ) 
                $this->db[$rownumber][$key] = $val;
			else 
				return PEAR::raiseError( "Array key [$key] not found in row [$rownumber]." );
        }
		
        return true;
    } 
	
	/** 
	 * Returns number of lines appended to $this->db.
	 *
	 * @return int
	 * @access public
	 */
    function append() 
    {
        $n = $this->rowstowrite();
        
		foreach ( $this->newrows as $new )
            $this->db[] = $new;
        
        $this->newrows = array();
        return $n;
    } 
	
	/**
	 * Returns an array of rownumbers that match the key/value pairs supplied as argument
     * false on failure to find any matches case and space insensitive.
	 *
	 * @return array
	 * @access public
	 */
    function find( $aValues )
    {
        $afound = false;
        $n      = 0;
        $target = count( $aValues ); 

        foreach ( $this->db as $record ) 
		{
            $found = 0;

            reset( $aValues );
            while ( list( $key, $val ) = each( $aValues ) ) 
			{
                if ( ( array_key_exists( $key, $record ) ) && ( strcasecmp( trim( CSVDB::singleSpace( $record[$key] ) ), trim( CSVDB::singleSpace( $val ) ) ) == 0 ) ) 
				{
                    $found += 1;
                } 
				else 
				{
                    $found = 0;
                    continue 1;
                } 
            }
			
            if ( $found == $target )
                $afound[] = $n;

            $n++;
        } 
		
        return $afound;
    }
	
	/**
	 * @return boolean
	 * @access public
	 */
    function writeable()
    {
        $cf = $this->dir . $this->dataFile;
        
		if ( !is_writeable( $cf ) )
            return false;
        else
            return true;
    } 
	
	/**
	 * Returns number of rows in database.
	 *
	 * @return int
	 * @access public
	 */
    function rowCount()
    { 
        return count( $this->db );
    } 
	
	/**
	 * Returns number of rows in newrows (append) database.
	 *
	 * @return int
	 * @access public
	 */
    function rowstowrite()
    { 
        return count( $this->newrows );
    } 
	
	/**
	 * Packages an array of data into a formatted string.
	 *
	 * @access public
	 */
    function package( $aData )
    { 
        if ( $this->cryptKey ) 
		{
            while ( list( $key, $val ) = each( $aData ) )
                $this->encrypt( $aData[$key], $this->cryptKey );
        } 
		
        return '"' . implode( '","', $aData ) . '"' . "\n";
    } 
	
	/**
	 * Packages an array of data into a formatted string.
	 *
	 * @access public
	 */
    function xpackage( $aData )
    { 
        return '"' . implode( '","', $aData ) . '"' . "\n";
    } 
	
	/**
	 * Encrypt/obfuscate data.
	 *
	 * @access public
	 */
    function encrypt( &$txt, $key ) 
    {
        srand( (double)microtime() * 1000000 );
        $encrypt_key = md5( rand( 0, 32000 ) );
        $ctr = 0;
        $tmp = "";
        $tx  = $txt;
        
		for ( $i = 0; $i < strlen( $tx ); $i++ ) 
		{
            if ( $ctr == strlen( $encrypt_key ) )
                $ctr = 0;
				
            $tmp .= substr( $encrypt_key, $ctr, 1 ) . ( substr( $tx, $i, 1 ) ^ substr( $encrypt_key, $ctr, 1 ) );
            $ctr++;
        } 
		
        $txt = base64_encode( $this->crypt( $tmp, $key ) );
    } 

	/**
	 * Reverse the obfuscation.
	 *
	 * @access public
	 */
    function decrypt( &$txt, $key )
    {
        $tx  = $this->crypt( base64_decode( $txt ), $key );
        $tmp = "";
		
        for ( $i = 0; $i < strlen( $tx ); $i++ ) 
		{
            $md5 = substr( $tx, $i, 1 );
            $i++;
            $tmp .= ( substr( $tx, $i, 1 ) ^ $md5 );
        } 
		
        $txt = $tmp;
    } 

	/**
	 * @access public
	 */
    function crypt( $txt, $crypt_key ) 
    {
        $md5 = md5( $crypt_key );
        $ctr = 0;
        $tmp = "";
		
        for ( $i = 0; $i < strlen( $txt ); $i++ ) 
		{
            if ( $ctr == strlen( $md5 ) ) 
				$ctr = 0;
            
			$tmp .= substr( $txt, $i, 1 ) ^ substr( $md5, $ctr, 1 );
            $ctr++;
        } 
		
        return $tmp;
    } 
		
	/**
	 * Reduces multiple spaces to a single space character.
	 *
	 * @access public
	 * @static
	 */
	function singleSpace( $a ) 
	{
    	$b = str_replace( '  ', ' ', $a );
    	return $b;
	} 
} // END OF CSVDB

?>
