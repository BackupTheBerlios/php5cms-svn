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
 * @package db_nanosql_backup
 */
 
class NanoDBBackup extends PEAR
{
	/**
	 * @access public
	 */
   	var $filelist;

	/**
	 * @access private
	 */
	var $_options = array();
	

	/**
	 * Constructor
	 */   
   	function NanoDBBackup( $options = array() )
   	{
		$this->_options = $options;
   	}

	
    /**
     * Attempts to return a concrete NanoDBBackup instance based on $driver.
     *
     * @param mixed $driver  The type of concrete NanoDBBackup subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included.
     * @param array $options (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object NanoDBBackup  The newly created concrete NanoDBBackup instance,
     *                       or false on an error.
     */
    function &factory( $driver, $options = array() )
    {	
        $driver = strtolower( $driver );
		
        if ( empty( $driver ) || ( strcmp( $driver, 'none' ) == 0 ) )
            return new NanoDBBackup( $options );
	
        $backup_class = "NanoDBBackup_" . $driver;

		using( 'db.nanosql.backup.' . $backup_class );
		
		if ( class_registered( $backup_class ) )
	        return new $backup_class( $options );
		else
			return PEAR::raiseError( "Driver not supported." );
    }

    /**
     * Attempts to return a reference to a concrete NanoDBBackup instance
     * based on $driver. It will only create a new instance if no
     * NanoDBBackup instance with the same parameters currently exists.
     *
     * This should be used if multiple types of file backends (and,
     * thus, multiple NanoDBBackup instances) are required.
     *
     * This method must be invoked as: $var = &NanoDBBackup::singleton()
     *
     * @param mixed $driver  The type of concrete NanoDBBackup subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included.
     * @param array $options (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object NanoDBBackup  The concrete NanoDBBackup reference, or false on an
     *                       error.
     */
    function &singleton( $driver, $options = array() )
    {
        static $instances;
        
		if ( !isset( $instances ) )
            $instances = array();

        if ( is_array( $driver ) )
            $drivertag = implode( ':', $driver );
        else
            $drivertag = $driver;
        
        $signature = md5( strtolower( $drivertag ) . '][' . implode('][', $options ) );
        
		if ( !isset( $instances[$signature] ) )
            $instances[$signature] = &NanoDBBackup::factory( $driver, $options );

        return $instances[$signature];
    }
	
   	/**
     * @abstract Archives all added files
     */
   	function create( $asfilename, $tmpdir )
   	{
      	return PEAR::raiseError( "Abstract method." );
   	}

   	/**
     * @abstract Adds a file to the archive
     * @param filename  The filename (full path) to add
     */
   	function addfile( $filename ) 
   	{
      	$this->filelist[] = $filename;
   	}

   	/**
     * @abstract Archives all added files and sends them as a file attachment 
     * to the browser. Note that this must be called before any HTML code is 
     * output by PHP.
     * @param asfilename  the filename of the archive
     * @param tempdir  a directory where to store a temporary file.  This file
     * is automatically deleted once it has been used.  Defaults to the current
     * directory.
     * @return boolean true on success, false on failure.
     */
   	function send( $asfilename, $tmpdir = "." )
   	{
      	if ( $tmpdir{strlen( $tmpdir ) - 1} != "/" )
         	$tmpdir .= "/";

      	$tmpname = tempnam( $tmpdir, "archive" );

      	// create the compressed archive file
      	$this->create( $tmpname, $tmpdir );

      	// now re-read the file and pump out to the browser
      	$tmpfp = fopen( $tmpname, "rb" );
      
	  	if ( !$tmpfp )
         	return false;
      
      	$filesize = filesize( $tmpname );

      	header( "Content-Disposition: filename=$asfilename" ); 
      	header( "Content-Type: application/octet-stream" ); 
      	header( "Content-Length: $filesize" );

      	// pump out to browser and delete temp file
      	fpassthru( $tmpfp );
      	unlink( $tmpname );
      	flush();

      	return true;
   	}

   	/**
     * @abstract Convers an int to a binary string, low byte first
     * @param num  int - number to convert
     * @param bytes  int - minimum number of bytes to covert to
     * @return the binary string form of the number
     */
   	function _dec2bin( $num, $bytes )
   	{
      	$result = "";
      
	  	for ( $i = 0; $i < $bytes; ++$i )
      	{
         	$result .= chr( $num & 0xFF );
         	$num = $num >> 8;
      	}

      	return $result;
   	}

   	/**
     * @abstract Converts a binary string to an int, low byte first
     * @param str  string - binary string to convert
     * @param len  int - length of the binary string to convert
     * @return int version of the binary string
     */
   	function _bin2dec( &$str, $len )
   	{
      	$shift  = 0;
      	$result = 0;
      
	  	for ( $i = 0; $i < $len; ++$i )
      	{
         	$result |= ( @ord( $str[$i] ) << $shift );
         	$shift  += 8;
      	}

      	return $result;
   	}
} // END OF NanoDBBackup

?>
