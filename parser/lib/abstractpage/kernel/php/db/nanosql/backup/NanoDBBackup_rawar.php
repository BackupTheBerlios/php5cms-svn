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


using( 'db.nanodb.backup.NanoDBBackup' );


/** 
 * Provides a special raw PHP archive.
 *
 * @package db_nanosql_backup
 */
 
class NanoDBBackup_rawar extends NanoDBBackup
{
	/**
	 * Constructor
	 */
	function NanoDBBackup_rawar( $options = array() )
	{
		$this->NanoDBBackup( $options );
	}
	
	
	/**
     * @abstract Creates a new RAW ARchive
     * @param asfilename  the filename of the archive
     * @return boolean true on success, false on failure.
     */
   	function create( $asfilename, $tmpdir )
   	{
      	$tmpfp = @fopen( $asfilename, "wb" );
      
	  	if ( !$tmpfp )
         	return false;

      	foreach ( $this->filelist as $file )
      	{
         	// write the filename length to the file
         	$this->write_str( $tmpfp, basename( $file ) );

         	// copy the file contents
         	$fp = @fopen( $file, "rb" );
         
		 	if ( $fp ) 
         	{
            	// copy the file to the temporary file, in ASCII hex
            	$size   = filesize( $file );
            	$buffer = fread( $fp, $size );
            
				fclose( $fp );

            	// write the size of the data
            	$this->write_int( $tmpfp, $size );

            	// write the actual data
            	fwrite( $tmpfp, $buffer, $size );
         	}
      	}

      	fclose( $tmpfp );
   	}
   
   	/**
     * @abstract Extracts a RAW ARchive
     * @param filename  the filename of the archive
     * @param to_dir  the directory to extract to
     * @return boolean true on success, false on failure.
     */
   	function extract( $filename, $to_dir = "." )
   	{
      	if ( $to_dir{strlen( $to_dir ) - 1} != "/" )
         	$to_dir .= "/";

      	// open the file
      	$ar_fp = @fopen( $filename, "rb" );
      
	  	if ( !$ar_fp )
         	return false;

      	while ( !feof( $ar_fp ) )
      	{
         	// read the filename
         	$filename = $this->read_str( $ar_fp );

         	// PHP does dodgy stuff with feof(...)
         	if ( $filename == "" ) 
				break;

         	// read the length of data for the file
         	$size = $this->read_int( $ar_fp );

         	// open the file for writing
         	$fp = @fopen( $to_dir . $filename, "wb" );
         
		 	if ( !$fp )
         	{
            	fclose( $ar_fp );
            	return false;
         	}

         	// read the file data
         	$data = fread( $ar_fp, $size );

         	// write the file data
         	fwrite( $fp, $data, $size );

         	fclose( $fp );
      	}

      	fclose( $ar_fp );
      	return true;
   	}

   	/**
     * @abstract Reads a string from a file
     * @param fp  file pointer
     * @return string the string read
     */
   	function read_str( $fp )
   	{
      	$strlen = $this->_bin2dec( fread( $fp, 4 ), 4 );
      	return fread( $fp, $strlen );
   	}

   	/**
     * @abstract Writes a string to a file
     * @param fp  file pointer
     * @param str  the string to write
     */
   	function write_str( $fp, $str )
   	{
      	$len = strlen( $str );
      	
		fwrite( $fp, $this->_dec2bin( $len, 4 ), 4 );
      	fwrite( $fp, $str, $len );
   	}

   	/**
     * @abstract Reads an int from a file
     * @param fp  file pointer
     * @return int the int read
     */
   	function read_int( $fp )
   	{
      	return $this->_bin2dec( fread( $fp, 4 ), 4 );
   	}

   	/**
     * @abstract Writes an int to a file
     * @param fp  file pointer
     * @param num  the int to write
     */
   	function write_int( $fp, $num )
   	{
      	fwrite( $fp, $this->_dec2bin( $num, 4 ), 4 );
   	}
} // END OF NanoDBBackup_rawar

?>
