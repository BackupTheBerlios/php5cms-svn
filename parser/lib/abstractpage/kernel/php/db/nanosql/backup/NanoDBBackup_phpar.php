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
 * Provides a gzip'ed special PHP archive.
 *
 * @package db_nanosql_backup
 */
 
class NanoDBBackup_phpar extends NanoDBBackup
{
	/**
	 * Constructor
	 */
	function NanoDBBackup_phpar( $options = array() )
	{
		$this->NanoDBBackup( $options );
	}
	
	
	/**
     * @abstract Creates a new PHP ARchive
     * @param asfilename  the filename of the archive
     * @return boolean true on success, false on failure.
     */
	function create( $asfilename, $tmpdir )
   	{
      	$tmpfp = @gzopen( $asfilename, "wb" );
      
	  	if ( !$tmpfp )
         	return false;

      	foreach ( $this->filelist as $file )
      	{
         	// write the filename length to the file
         	$this->gzwrite_str( $tmpfp, basename( $file ) );

         	// copy the file contents
         	$fp = @fopen( $file, "rb" );
         
		 	if ( $fp ) 
         	{
            	// copy the file to the temporary file, in ASCII hex
            	$size   = filesize( $file );
            	$buffer = fread( $fp, $size );
            
				fclose( $fp );

            	// write the size of the data
            	$this->gzwrite_int( $tmpfp, $size );

            	// write the actual data
            	gzwrite( $tmpfp, $buffer, $size );
         	}
      	}

      	gzclose( $tmpfp );
   	}

   	/**
     * @abstract Extracts a PHP ARchive
     * @param filename  the filename of the archive
     * @param to_dir  the directory to extract to
     * @return boolean true on success, false on failure.
     */
   	function extract( $filename, $to_dir = "." )
   	{
      	if ( $to_dir{strlen( $to_dir ) - 1} != "/" )
         	$to_dir .= "/";
      
      	// open the file
      	$ar_fp = @gzopen( $filename, "rb" );
      
	  	if ( !$ar_fp )
         	return false;

      	while ( !gzeof( $ar_fp ) )
      	{
         	// read the filename
         	$filename = $this->gzread_str( $ar_fp );

         	// read the length of data for the file
         	$size = $this->gzread_int( $ar_fp );

         	// open the file for writing
         	$fp = @fopen( $to_dir . $filename, "wb" );
         
		 	if ( !$fp )
         	{
            	gzclose( $ar_fp );
            	return false;
         	}

         	// read the file data
         	$data = gzread( $ar_fp, $size );

         	// write the file data
         	fwrite( $fp, $data, $size );
         	
			fclose( $fp );
      	}

      	gzclose( $ar_fp );
      	return true;
   	}

   	/**
     * @abstract Reads a string from a gzip'ed file
     * @param fp  gzip file pointer
     * @return string the string read
     */
   	function gzread_str( $fp )
   	{
      	$strlen = $this->_bin2dec( gzread( $fp, 4 ), 4 );
      	return gzread( $fp, $strlen );
   	}

   	/**
     * @abstract Writes a string to a gzip'ed file
     * @param fp  gzip file pointer
     * @param str  the string to write
     */
   	function gzwrite_str( $fp, $str )
   	{
      	$len = strlen( $str );
      	gzwrite( $fp, $this->_dec2bin( $len, 4 ), 4 );
      	gzwrite( $fp, $str, $len );
   	}

   	/**
     * @abstract Reads an int from a gzip'ed file
     * @param fp  gzip file pointer
     * @return int the int read
     */
   	function gzread_int( $fp )
   	{
      	return $this->_bin2dec( gzread( $fp, 4 ), 4 );
   	}

   	/**
     * @abstract Writes an int to a gzip'ed file
     * @param fp  gzip file pointer
     * @param num  the int to write
     */
   	function gzwrite_int( $fp, $num )
   	{
      	gzwrite( $fp, $this->_dec2bin( $num, 4 ), 4 );
   	}
} // END OF NanoDBBackup_phpar

?>
