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


// Sizes
define( 'PALMDB_HEADER_SIZE',                72 ); // Size of the database header
define( 'PALMDB_INDEX_HEADER_SIZE',           6 ); // Size of the record index header
define( 'PALMDB_RECORD_HEADER_SIZE',          8 ); // Size of the record index entry
define( 'PALMDB_RESOURCE_SIZE',              10 ); // Size of the resource index entry
define( 'PALMDB_EPOCH_1904',         2082844800 ); // Difference between Palm's time and Unix

// Attribute Flags
define( 'PALMDB_ATTRIB_RESOURCE',      0x01 );
define( 'PALMDB_ATTRIB_READ_ONLY',     0x02 );
define( 'PALMDB_ATTRIB_APPINFO_DIRTY', 0x04 );
define( 'PALMDB_ATTRIB_BACKUP',        0x08 );
define( 'PALMDB_ATTRIB_OK_NEWER',      0x10 );
define( 'PALMDB_ATTRIB_RESET',         0x20 );
define( 'PALMDB_ATTRIB_OPEN',          0x40 );

// Where are 0x80 and 0x100?
define( 'PALMDB_ATTRIB_LAUNCHABLE',    0x200 );

// Record Flags
// The first nibble is reserved for the category number
// See PALMDB_CATEGORY_MASK
define( 'PALMDB_RECORD_ATTRIB_ARCHIVE',       0x08 ); // Special -- see below
define( 'PALMDB_RECORD_ATTRIB_PRIVATE',       0x10 );
define( 'PALMDB_RECORD_ATTRIB_DELETED',       0x20 );
define( 'PALMDB_RECORD_ATTRIB_DIRTY',         0x40 );
define( 'PALMDB_RECORD_ATTRIB_EXPUNGED',      0x80 );
define( 'PALMDB_RECORD_ATTRIB_DEL_EXP',       0xA0 ); // Mask for easier use
define( 'PALMDB_RECORD_ATTRIB_MASK',          0xF0 ); // The 4 bytes for the attributes
define( 'PALMDB_RECORD_ATTRIB_CATEGORY_MASK', 0xFF ); // 1 byte

/*
 * The archive bit should only be used when the record is deleted or
 * expunged.
 *
 * if ( $attr & PALMDB_RECORD_ATTRIB_DEL_EXP ) 
 * {
 *    // Lower 3 bits (0x07) should be 0
 *    if ( $attr & PALMDB_RECORD_ATTRIB_ARCHIVE )
 *       echo "Record is deleted/expunged and should be archived.\n";
 *    else
 *       echo "Record is deleted/expunged and should not be archived.\n";
 * } 
 * else 
 * {
 *    // Lower 4 bits are the category
 *    echo "Record is not deleted/expunged.\n";
 *    echo "Record's category # is " . ( $attr & PALMDB_CATEGORY_MASK ) . "\n";
 * }
 */

// Category support
define( 'PALMDB_CATEGORY_NUM',           16 ); // Number of categories
define( 'PALMDB_CATEGORY_NAME_LENGTH',   16 ); // Bytes allocated for name
define( 'PALMDB_CATEGORY_SIZE',         276 ); // 2 + (num * length) + num + 1 + 1
define( 'PALMDB_CATEGORY_MASK',        0x0f ); // Bitmask -- use with attribute of record
                                               // to get the category ID

// Double conversion
define( 'PALMDB_DOUBLEMETHOD_UNTESTED', 0 );
define( 'PALMDB_DOUBLEMETHOD_NORMAL',   1 );
define( 'PALMDB_DOUBLEMETHOD_REVERSE',  2 );
define( 'PALMDB_DOUBLEMETHOD_BROKEN',   3 );


/**
 * PHP class to write PalmOS databases.
 *
 * Contains all of the required methods and variables to write a pdb file.
 * Extend this class to provide functionality for memos, addresses, etc.
 *
 * See http://php-pdb.sourceforge.net/ for more information about the library.
 *
 * As a note, storing all of the information as hexadecimal kinda sucks,
 * but it is tough to store and properly manipulate a binary string in
 * PHP.  We double the size of the data but decrease the difficulty level
 * immensely.
 *
 * @package db_palm_lib
 */

class PalmDB extends PEAR
{
   	var $records           = array();	// All of the data from the records is here
										// Key = record ID
   	var $record_attrs      = array(); 	// And their attributes are here
   	var $current_record    = 1;			// Which record we are currently editing
   	var $name              = '';		// Name of the PDB file
   	var $type_id           = '';		// The 'Type' of the file (4 chars)
   	var $creator_id        = '';		// The 'Creator' of the file (4 chars)
   	var $attributes        = 0;			// Attributes (bitmask)
   	var $version           = 0;			// Version of the file
   	var $mod_number        = 0;			// Modification number
   	var $creation_time     = 0;			// Stored in unix time (Jan 1, 1970)
   	var $modification_time = 0;			// Stored in unix time (Jan 1, 1970)
   	var $backup_time       = 0;			// Stored in unix time (Jan 1, 1970)
   	var $app_info          = '';		// Basic AppInfo block
   	var $sort_info         = '';		// Basic SortInfo block

	// What method to use for converting doubles
   	var $double_method     = PALMDB_DOUBLEMETHOD_UNTESTED;
   
   	var $category_list = array();		// Category data (not used by default --
										// See "Category Support" comment below)
	

	/**
	 * Constructor
	 */
   	function PalmDB( $params = array() )
   	{
      	$this->type_id    = isset( $params['type' ]    )? $params['type' ]    : '';
      	$this->creator_id = isset( $params['creator' ] )? $params['creator' ] : '';
      	$this->name       = isset( $params['name' ]    )? $params['name' ]    : '';

      	$this->creation_time     = time();
      	$this->modification_time = time();
   	}


    /**
     * Attempts to return a concrete PalmDB instance based on $driver.
     *
     * @param mixed $driver  The type of concrete PalmDB subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included.
     * @param array $params  (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object PalmDB The newly created concrete PalmDB instance, or
     *                      false an error.
     */
    function &factory( $driver, $params = array() )
    {
        if ( empty( $driver ) || ( strcmp( $driver, 'none' ) == 0 ) )
            return new PalmDB( $params );
		
		$palm_class = "";
			
		switch ( strtolower( $driver ) )
		{
			case 'address':
				$palm_class = "PalmDB_Address";
				break;
				
			case 'date':

			case 'datebook':
				$palm_class = "PalmDB_Datebook";
				break;
				
			case 'doc':
				$palm_class = "PalmDB_Doc";
				break;
				
			case 'mobile':

			case 'mobiledb':
				$palm_class = "PalmDB_MobileDB";
				break;
				
			case 'basic':

			case 'smallbasic':
				$palm_class = "PalmDB_SmallBasic";
				break;
				
			case 'todo':

			case 'todolist':
				$palm_class = "PalmDB_TodoList";
				break;
				
			case 'toolbox':

			case 'toolboxdb':
				$palm_class = "PalmDB_ToolboxDB";
				break;
		}
		
		if ( empty( $palm_class ) )
			return PEAR::raiseError( "Invalid driver." );		
		
		using( 'db.palm.lib.' . $palm_class );
		
		if ( class_registered( $palm_class ) )
	        return new $palm_class( $params );
		else
			return PEAR::raiseError( "Driver not supported." );
    }

    /**
     * Attempts to return a reference to a concrete PalmDB instance
     * based on $driver. It will only create a new instance if no
     * PalmDB instance with the same parameters currently exists.
     *
     * This method must be invoked as: $var = &PalmDB::singleton()
     *
     * @param mixed $driver  The type of concrete PalmDB subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included. If $driver is an array,
     *                       then we will look in $driver[0]/lib/PalmDB/ for
     *                       the subclass implementation named $driver[1].php.
     * @param array $params  (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object PalmDB The concrete PalmDB reference, or false on an
     *                       error.
     */
    function &singleton( $driver, $params = array() )
    {
        static $instances;
        
		if ( !isset( $instances ) )
            $instances = array();

        if ( is_array( $driver ) )
            $drivertag = implode( ':', $driver );
        else
            $drivertag = $driver;
        
        $signature = md5( strtolower( $drivertag ) . '][' . implode( '][', $params ) );

        if ( !isset( $instances[$signature] ) )
            $instances[$signature] = &PalmDB::factory( $driver, $params );

        return $instances[$signature];
    }
	
	
   	/*
     * Data manipulation functions
     *
     * These convert various numbers and strings into the hexadecimal
     * format that is used internally to construct the file.  We use hex
     * encoded strings since that is a lot easier to work with than binary
     * data in strings, and we can easily tell how big the true value is.
     * B64 encoding does some odd stuff, so we just make the memory
     * consumption grow tremendously and the complexity level drops
     * considerably.
     */
       
   	/**
	 * Converts a byte and returns the value.
	 */
   	function int8( $value ) 
	{
      	$value &= 0xFF;
      	return sprintf( "%02x", $value );
   	}
   
   	/**
	 * Loads a single byte as a number from the file.
	 * Use if you want to make your own ReadFile function.
	 */
   	function loadInt8( $file ) 
	{
      	if ( is_resource( $file ) )
         	$string = fread( $file, 1 );
      	else
         	$string = $file;
      
	  	return ord( $string[0] );
   	}
   
   	/** 
	 * Converts an integer (two bytes) and returns the value.
	 */
   	function int16( $value ) 
	{
      	$value &= 0xFFFF;
      	return sprintf( "%02x%02x", $value / 256, $value % 256 );
   	}
   
   	/**
	 * Loads two bytes as a number from the file.
     * Use if you want to make your own ReadFile function.
	 */
   	function loadInt16( $file ) 
	{
      	if ( is_resource( $file ) )
         	$string = fread( $file, 2 );
      	else
         	$string = $file;
      
	  	return ord( $string[0] ) * 256 + ord( $string[1] );
   	}
   
   	/**
	 * Converts an integer (three bytes) and returns the value.
	 */
   	function int24( $value ) 
	{
      	$value &= 0xFFFFFF;
      	return sprintf( "%02x%02x%02x", $value / 65536, ( $value / 256 ) % 256, $value % 256 );
   	}

   	/**
	 * Loads three bytes as a number from the file.
     * Use if you want to make your own ReadFile function.
	 */
   	function loadInt24( $file ) 
	{
      	if ( is_resource( $file ) )
         	$string = fread( $file, 3 );
      	else
	 		$string = $file;
      
	  	return ord( $string[0] ) * 65536 + ord( $string[1] ) * 256 + ord( $string[2] );
   	}
        
   	/**
	 * Converts an integer (four bytes) and returns the value.
     * 32-bit integers have problems with PHP when they are bigger than
     * 0x80000000 (about 2 billion) and that's why I don't use pack() here.
	 */
   	function int32( $value ) 
	{
      	$negative = false;
      
	  	if ( $value < 0 ) 
		{
         	$negative = true;
	 		$value = -$value;
      	}
      
	  	$big = $value / 65536;
      	settype( $big, 'integer' );
      	$little = $value - ( $big * 65536 );
      
	  	if ( $negative ) 
		{
         	// Little must contain a value
         	$little = - $little;
	 
	 		// Big might be zero, and should be 0xFFFF if that is the case.
	 		$big = 0xFFFF - $big;
      	}
      
	  	$value = PalmDB::int16( $big ) . PalmDB::int16( $little );
      	return $value;
   	}
   
   	/**
	 * Loads a four-byte string from a file into a number.
     * Use if you want to make your own ReadFile function.
	 */
   	function loadInt32( $file ) 
	{
     	if ( is_resource( $file ) )
         	$string = fread( $file, 4 );
      	else
         	$string = $file;
      
	  	$value = 0;
      	$i = 0;
      
	  	while ( $i < 4 ) 
		{
         	$value *= 256;
	 		$value += ord( $string[$i] );
	 
	 		$i++;
      	}
      
	  	return $value;
   	}
   
   	/**
	 * Returns the method used for generating doubles.
	 */
   	function getDoubleMethod()
	{
      	if ( $this->double_method != PALMDB_DOUBLEMETHOD_UNTESTED )
         	return $this->double_method;
	 
      	$val = bin2hex( pack( 'd', 10.53 ) );
      	$val = strtolower( $val );
      
	  	if ( substr( $val, 0, 4 ) == '8fc2' )
	 		$this->double_method = PALMDB_DOUBLEMETHOD_REVERSE;
      
	  	if ( substr( $val, 0, 4 ) == '4025' )
	 		$this->double_method = PALMDB_DOUBLEMETHOD_NORMAL;
      
	  	if ( $this->double_method == PALMDB_DOUBLEMETHOD_UNTESTED )
	 		$this->double_method = PALMDB_DOUBLEMETHOD_BROKEN;
      
      	return $this->double_method;
   	}

   	/**
	 * Converts the number into a double and returns the encoded value.
     * Not sure if this will work on all platforms.
     * double(10.53) should return "40250f5c28f5c28f"
	 */
   	function double( $value ) 
	{
      	$method = $this->getDoubleMethod();
      
      	if ( $method == PALMDB_DOUBLEMETHOD_BROKEN )
         	return '0000000000000000';
	 
      	$value = bin2hex( pack( 'd', $value ) );
      
      	if ( $method == PALMDB_DOUBLEMETHOD_REVERSE )
		{
         	$value = substr( $value, 14, 2 ) . 
					 substr( $value, 12, 2 ) . 
					 substr( $value, 10, 2 ) . 
					 substr( $value,  8, 2 ) . 
					 substr( $value,  6, 2 ) . 
					 substr( $value,  4, 2 ) . 
					 substr( $value,  2, 2 ) . 
					 substr( $value,  0, 2 );
		}
			    
      	return $value;
   	}
   
   	/**
	 * The reverse? May not work on your platform.
	 * Use if you want to make your own ReadFile function.
	 */
   	function loadDouble( $file ) 
	{
      	$method = $this->getDoubleMethod();
      
      	if ( $method == PALMDB_DOUBLEMETHOD_BROKEN )
         	return 0.0;
    
      	if ( is_resource( $file ) )
         	$string = fread( $file, 8 );
      	else
	 		$string = $file;
	 
      	// Reverse the bytes... this might not be nessesary
      	// if PHP is running on a big-endian server
      	if ( $method == PALMDB_DOUBLEMETHOD_REVERSE )
		{
         	$string = substr( $string, 7, 1 ) . 
					  substr( $string, 6, 1 ) . 
					  substr( $string, 5, 1 ) . 
					  substr( $string, 4, 1 ) . 
					  substr( $string, 3, 1 ) . 
					  substr( $string, 2, 1 ) .
					  substr( $string, 1, 1 ) . 
					  substr( $string, 0, 1 );
		}
				    
      	// Back to binary 
      	$dnum = unpack( 'd', $string );
      
      	return $dnum[''];
   	}
   
   	/**
	 * Converts a date string ( YYYY-MM-DD )( "2001-10-31" )
     * into bitwise ( YYYY YYYM MMMD DDDD ).
	 * Should only be used when saving.
	 */
   	function dateToInt16( $date ) 
	{
      	$YMD = explode( '-', $date );
      	settype( $YMD[0], 'integer' );
      	settype( $YMD[1], 'integer' );
      	settype( $YMD[2], 'integer' );
      
	  	return ( ( ( $YMD[0] - 1904 ) & 0x7F ) << 9 ) |  ( ( $YMD[1] & 0x0f ) << 5 ) | ( $YMD[2] & 0x1f );
   	}
   
   	/**
	 * Converts a bitwise date ( YYYY YYYM MMMD DDDD ).
     * Into the human readable date string ( YYYY-MM-DD )( "2001-2-28" ).
     * Should only be used when loading.
	 */
   	function int16ToDate( $number ) 
	{
      	settype($number, 'integer');
      	$year   = ( $number >> 9 ) & 0x7F;
      	$year  += 1904;
      	$month  = ( $number >> 5 ) & 0xF;
      	$day    = $number & 0x1F;
      
	  	return $year . '-' . $month . '-' . $day;
   	}  

   	/**
	 * Converts a string into hexadecimal.
     * If $maxLen is specified and is greater than zero, the string is 
     * trimmed and will contain up to $maxLen characters.
     * string("abcd", 2) will return "ab" hex encoded (a total of 4
     * resulting bytes, but 2 encoded characters).
     * Returned string is *not* NULL-terminated.
	 */
   	function string( $value, $maxLen = false ) 
	{
      	$value = bin2hex( $value );
      
	  	if ( $maxLen !== false && $maxLen > 0 )
         	$value = substr( $value, 0, $maxLen * 2 );
      	
		return $value;
   	}
   
   	/**
	 * Pads a hex-encoded value (typically a string) to a fixed size.
     * May grow too long if $value starts too long.
     * $value  = hex encoded value
     * $minLen = Append nulls to $value until it reaches $minLen
     * $minLen is the desired size of the string, unencoded.
     * padString('6162', 3) results in '616200' (remember the hex encoding)
	 */
   	function padString( $value, $minLen ) 
   	{
      	$PadBytes = '00000000000000000000000000000000';
      	$PadMe = $minLen - ( strlen( $value ) / 2 );
      
	  	while ( $PadMe > 0 ) 
		{
         	if ( $PadMe > 16 )
	    		$value .= $PadBytes;
	 		else
	    		return $value . substr( $PadBytes, 0, $PadMe * 2 );
	       
	 		$PadMe = $minLen - ( strlen( $value ) / 2 );
      	}
      
      	return $value;
   	}
   
   
    /*
     * Record manipulation functions
     */
    
   	/**
	 * Sets the current record pointer to the new record number if an
     * argument is passed in.
     * Returns the old record number (just in case you want to jump back)
     * Does not do basic record initialization if we are going to a new 
     * record.
	 */
   	function goToRecord( $num = false ) 
	{
      	if ( $num === false )
         	return $this->current_record;
      
	  	if ( gettype( $num ) == 'string' && ( $num[0] == '+' || $num[0] == '-' ) )
         	$num = $this->current_record + $num;
      
	  	$oldRecord = $this->current_record;
      	$this->current_record = $num;
      
	  	return $oldRecord;
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
      
	  	return strlen( $this->records[$num] ) / 2;
   	}
   
	/**
	 * Adds to the current record. The input data must be already
	 * hex encoded. Initializes the record if it doesn't exist.
	 */
   	function appendCurrent( $value ) 
	{
      	if ( !isset( $this->records[$this->current_record] ) )
         	$this->records[$this->current_record] = '';
      
	  	$this->records[$this->current_record] .= $value;
   	}
   
   	/**
	 * Adds a byte to the current record.
	 */
   	function appendInt8( $value ) 
	{
      	$this->appendCurrent( $this->int8( $value ) );
   	}
   
   	/**
	 * Adds an integer (2 bytes) to the current record.
	 */
   	function appendInt16( $value ) 
	{
      	$this->appendCurrent( $this->int16( $value ) );
   	}
   
   	/**
	 * Adds an integer (4 bytes) to the current record.
	 */
   	function appendInt32( $value ) 
	{
   		$this->appendCurrent( $this->int32( $value ) );
   	}
   
   	/**
	 * Adds a double to the current record.
	 */
   	function appendDouble( $value ) 
	{
      	$this->appendCurrent( $this->double( $value ) );
   	}
   
	/** 
	 * Adds a string (not NULL-terminated).
	 */
   	function appendString( $value, $maxLen = false ) 
	{
      	$this->appendCurrent( $this->string( $value, $maxLen ) );
   	}
   
   	/**
	 * Returns true if the specified/current record exists and is set.
	 */
   	function recordExists( $Rec = false ) 
	{
      	if ( $Rec === false )
         	$Rec = $this->current_record;
      
	  	if ( isset( $this->records[$Rec] ) )
         	return true;
      
	  	return false;
   	}
   
   	/**
	 * Returns the hex-encoded data for the specified record or the current
     * record if not specified.
	 *
     * This is nearly identical to GetRecordRaw except that this function
     * may be overridden by classes and that there
     * should always be a function that will return the raw data of the
     * Records array.
	 */
	function getRecord( $Rec = false ) 
	{
      	if ( $Rec === false )
         	$Rec = $this->current_record;
      
	  	if ( isset( $this->records[$Rec] ) )
         	return $this->records[$Rec];
      
	  	return '';
   	}
   
	/**
	 * Returns the attributes for the specified record or the current
     * record if not specified.
	 */
   	function getRecordAttrib( $Rec = false ) 
	{
      	if ( $Rec === false )
         	$Rec = $this->current_record;
      
	  	if ( isset( $this->record_attrs[$Rec] ) )
         	return $this->record_attrs[$Rec] & PALMDB_RECORD_ATTRIB_CATEGORY_MASK;
      
	  	return 0;
   	}
   
   	/**
	 * Returns the raw data inside the current/specified record. Use this
     * for odd record types (like a Datebook record). Also, use this
     * instead of just using $PDB->records[] directly.
     * Please do not override this function.
	 */
   	function getRecordRaw( $Rec = false ) 
	{
      	if ( $Rec === false )
         	$Rec = $this->current_record;
      
	  	if ( isset( $this->records[$Rec] ) )
         	return $this->records[$Rec];
      
	  	return false;
   	}
   
   	/**
	 * Sets the hex-encoded data (or whatever) for the current record
     * Use this instead of the Append* functions if you have an odd
     * type of record (like a Datebook record).
     * Also, use this instead of just setting $PDB->records[]
     * directly.
     * setRecordRaw( 'data' );
     * setRecordRaw( 24, 'data' );   (specifying the record num)
	 */
   	function setRecordRaw( $A, $B = false ) 
	{
      	if ( $B === false ) 
		{
         	$B = $A;
	 		$A = $this->current_record;
      	}
      
	  	$this->records[$A] = $B;
   	}
   
   	/**
	 * Sets the attributes for the specified record or the current
     * record if not specified.
     * Note:  The 'attributes' byte also sets the category.
     * setRecordAttrib( $attr );
     * setRecordAttrib( $RecNo, $attr );
	 */
   	function setRecordAttrib( $A, $B = false ) 
	{
      	if ( $B === false ) 
		{
         	$B = $A;
	 		$A = $this->current_record;
      	}
      
	  	$this->record_attrs[$A] = $B & PALMDB_RECORD_ATTRIB_CATEGORY_MASK;
   	}
   
   	/**
	 * Deletes the specified record or the current record if not specified.
     * If you delete the current record and then use an append function, the
     * record will be recreated.
	 */
   	function deleteRecord( $RecNo = false ) 
	{
      	if ( $RecNo === false )
         	$RecNo = $this->current_record;
      
      	if ( isset( $this->records[$RecNo] ) )
         	unset( $this->records[$RecNo] );
      
	  	if ( isset( $this->record_attrs[$RecNo] ) )
         	unset( $this->record_attrs[$RecNo] );
   	}
   
   	/**
	 * Returns an array of available record IDs in the order they should
     * be written. Probably should only be called within the class.
	 */
   	function getRecordIDs()
	{
      	$keys = array_keys( $this->records );
      
	  	if ( !is_array( $keys ) || count( $keys ) < 1 )
         	return array();
      
	  	sort( $keys, SORT_NUMERIC );
      	return $keys;
   	}
   
	/**
	 * Returns the number of records. This should match the number of
     * keys returned by getRecordIDs().
	 */
   	function getRecordCount() 
	{
      	return count( $this->records );
   	}
   
   	/**
	 * Returns the size of the AppInfo block.
     * Used only for writing.
	 */
   	function getAppInfoSize()
	{
      	if ( !isset( $this->app_info ) )
         	return 0;
      
	  	return strlen( $this->app_info ) / 2;
   	}
   
   	/**
	 * Returns the AppInfo block (hex encoded).
     * Used only for writing.
	 */
   	function getAppInfo()
	{
      	if ( !isset( $this->app_info ) )
         	return 0;
      	return $this->app_info;
   	}
   
   	/**
	 * Returns the size of the SortInfo block.
     * Used only for writing.
	 */
   	function getSortInfoSize()
	{
      	if ( !isset( $this->sort_info ) )
         	return 0;
      
	  	return strlen( $this->sort_info ) / 2;
   	}
   
   	/**
	 * Returns the SortInfo block (hex encoded).
     * Used only for writing.
	 */
   	function getSortInfo()
	{
      	if ( !isset( $this->sort_info ) )
         	return 0;
      
	  	return $this->sort_info;
   	}
   
   
    /*
     * Category Support
     *
     * If you plan on using categories in your module, you will have to use
     * these next four functions.  
     *
     * In your loadAppInfo(), you should have something like this ...
     *    function loadAppInfo( $fileData ) 
	 *    {
     *       $this->loadCategoryData( $fileData );
     *       $fileData = substr( $fileData, PALMDB_CATEGORY_SIZE );
     *       // .....
     *    }
     *
     * In your getAppInfo() function, you need to output the categories ...
     *    function getAppInfo() 
	 *    {
     *       $AppInfo = $this->createCategoryData();
     *       // .....
     *       return $AppInfo;
     *    }
     *
     * To change the category data, use setCategoryList() and getCategoryList()
     * helper functions.
     */
   
   	/**
	 * Returns the category data. See setCategoryList() for a description
	 * of the format of the array returned.
	 */
   	function getCategoryList()
	{
      	return $this->category_list;
   	}
   
   	/** Sets the categories to what you specified.
     *
     * Data format:  (easy way)
     *    $categoryArray[###] = name
     * Or:  (how it is stored in the class)
     *    $categoryArray[###]['Name'] = name
     *    $categoryArray[###]['Renamed'] = true / false
     *    $categoryArray[###]['ID'] = number from 0 to 255
     *
     * Tips:
     *  - The number for the key of $categoryArray is from 0-15, specifying
     *    the order that the category is written in the AppInfo block.
     *  - I'd suggest numbering your categories sequentially
     *  - ID numbers must be unique.  If they are not, a new arbitrary number
     *    will be assigned.
     *  - $categoryArray[0] is reserved for the 'Unfiled' category.  It's
     *    ID is 0.  Do not use 0 as an index for the array.  Do not use 0 as
     *    an ID number.  This function will enforce this rule.
     *  - There is a maximum of 16 categories, including 'Unfiled'.  This
     *    means that you only have 15 left to play with.
     *
     * Category 0 is reserved for 'Unfiled'
     * Categories 1-127 are used for handheld ID numbers
     * Categories 128-255 are used for desktop ID numbers
     * Do not let category numbers be created larger than 255 -- this function
     * will erase categories with an ID larger than 255
   	 */
   	function setCategoryList( $list ) 
	{
      	$usedCheck = 0;
      	$usedList  = array();
      
      	// Clear out old category list
      	$this->category_list = array();
      
      	// Force category ID 0 to be "Unfiled"
      	$list[0] = array(
			'Name'    => 'Unfiled', 
			'Renamed' => false, 
			'ID'      => 0
		);
      
      	$keys = array_keys( $list );
      
      	// Loop through the array
      	$CatsWritten = 0;
      	foreach ( $keys as $key ) 
		{
         	// If there is room for more categories ...
         	if ( $CatsWritten < PALMDB_CATEGORY_NUM && $key <= 15 && $key >= 0 ) 
			{
	    		if ( is_array( $list[$key] ) && isset( $list[$key]['ID'] ) )
	       			$id = $list[$key]['ID'];
	    		else
	       			$id = $key;
	    
				if ( $id > 255 || isset( $usedList[$id] ) ) 
				{
	       			// Find a new arbitrary number for this category
	       			$usedCheck++;
	       
		   			while ( isset( $usedList[$usedCheck] ) )
	          			$usedCheck++;
	       
		   			$id = $usedCheck;
	    		}
	    
	    		$CatsWritten++;
	    
	    		// Set the "Renamed" flag if available
	    		// By default, the Renamed flag is false
	    		$RenamedFlag = false;
	    
				if ( is_array( $list[$key] ) && isset( $list[$key]['Renamed'] ) && $list[$key]['Renamed'] )
	       			$RenamedFlag = true;
	       
	    		// Set the name of the category
	    		$name = '';
	    
				if ( is_array( $list[$key] ) ) 
				{
	       			if ( isset( $list[$key]['Name'] ) )
	          			$name = $list[$key]['Name'];
	    		} 
				else 
				{
	       			$name = $list[$key];
	    		}
	    
	    		$this->category_list[$key] = array(
					'Name'    => $name,
					'Renamed' => $RenamedFlag,
					'ID'      => $id
				);
	 		}
      	}
   	}   
   
   	/**
	 * Creates the hex-encoded data to be stuck in the AppInfo
     * block if the database supports categories.
     *
     * See setCategoryList() for the format of $CategoryArray
	 */
   	function createCategoryData()
	{
      	$UsedIds = array();
      	$UsedIdCheck = 0;
      
      	// Force category data to be valid and in a specific format
      	$this->setCategoryList( $this->category_list );
      
      	$RenamedFlags = 0;
      	$CategoryStr  = '';
      	$IdStr  = '';
      	$LastID = 0;
      
      	foreach ( $this->category_list as $key => $data ) 
		{
         	$UsedIds[$data['ID']] = true;
	 
	 		if ( $data['ID'] > $LastID )
	    		$LastID = $data['ID'];
      	}
      
      	// Loop through the array
      	for ( $key = 0; $key < 16; $key++ ) 
		{
         	if ( isset( $this->category_list[$key] ) ) 
			{
	    		$RenamedFlags *= 2;
	    
	    		// Set the "Renamed" flag if available
	    		// By default, the Renamed flag is false
	    		if ( $this->category_list[$key]['Renamed'] )
	       			$RenamedFlags += 1;
	       
	    		// Set the name of the category
	    		$name = $this->category_list[$key]['Name'];
	    		$name = $this->string( $name, PALMDB_CATEGORY_NAME_LENGTH );
	    		$CategoryStr .= $this->padString( $name, PALMDB_CATEGORY_NAME_LENGTH );
				$IdStr .= $this->int8( $this->category_list[$key]['ID'] );
	 		} 
			else 
			{
	    		// Add blank categories where they are missing
	    		$UsedIdCheck++;
	    
				while ( isset( $UsedIds[$UsedIdCheck] ) )
	       			$UsedIdCheck++;
	    
				$RenamedFlags *= 2;
	    		$CategoryStr  .= $this->padString( '', PALMDB_CATEGORY_NAME_LENGTH );
	    		$IdStr .= $this->int8( $UsedIdCheck );
	 		}
      	}
      
      	// According to the docs, this is just the last ID written.  It doesn't
      	// say whether this is the last one written by the palm, last one
      	// written by the desktop, highest one written, or the ID for number
      	// 15.
      	$TrailingBytes  = $this->int8( $LastID );
      	$TrailingBytes .= $this->int8( 0 );
	 
      	return $this->int16( $RenamedFlags ) . $CategoryStr . $IdStr . $TrailingBytes;
   	}
   
   	/**
	 * This should be called by other subclasses that use category support
     * It returns a category array. Each element in the array is another
     * array with the key 'Name' set to the name of the category and 
     * the key 'Renamed' set to the renamed flag for that category.
	 */
   	function loadCategoryData( $fileData ) 
	{
      	$key = 0;
      	$RenamedFlags = $this->loadInt16( substr( $fileData, 0, 2 ) );
      	$Offset = 2;
      	$StartingFlag = 65536;
      	$Categories   = array();
      
	  	while ( $StartingFlag > 1 ) 
		{
         	$StartingFlag /= 2;
	 		$Name = substr( $fileData, $Offset, PALMDB_CATEGORY_NAME_LENGTH );
	 
	 		$i = 0;
		 	while ( $i < PALMDB_CATEGORY_NAME_LENGTH && $Name[$i] != "\0" )
	    		$i++;
	 
	 		if ( $i == 0 )
	    		$Name = '';
	 		else if ( $i < PALMDB_CATEGORY_NAME_LENGTH )
	    		$Name = substr( $Name, 0, $i );
	 
	 		if ( $RenamedFlags & $StartingFlag )
	    		$RenamedFlag = true;
	 		else
	    		$RenamedFlag = false;
	 
	 		$Categories[$key] = array(
				'Name'    => $Name, 
				'Renamed' => $RenamedFlag
			);
	 		
			$Offset += PALMDB_CATEGORY_NAME_LENGTH;
	 		$key++;
      	}
      
      	$CategoriesParsed = array();
      
      	for ( $key = 0; $key < 16; $key++ ) 
		{
         	$UID = $this->loadInt8( substr( $fileData, $Offset, 1 ) );
			$Offset++;
	 
	 		$CategoriesParsed[$key] = array(
				'Name'    => $Categories[$key]['Name'],
				'Renamed' => $Categories[$key]['Renamed'],
				'ID'      => $UID
			);
      	}
      
      	// Ignore the last ID.  Maybe it should be preserved?
      	$this->category_list = $CategoriesParsed;
   	}
   
   
    /*
     * Database Writing Functions
     */
   
   	/**
	 * Takes a hex-encoded string and makes sure that when decoded, the data
     * lies on a four-byte boundary. If it doesn't, it pads the string with
     * NULLs
     *
     * We don't use this function currently.
     * It is part of a test to see what is needed to get files to sync
     * properly with Desktop 4.0
     *
	 */
   	function padTo4ByteBoundary( $string ) 
	{
      	while ( ( strlen( $string ) / 2 ) % 4 )
         	$string .= '00';
      
      	return $string;
   	}
    
   	/**
	 * Returns the hex encoded header of the pdb file.
     * Header = name, attributes, version, creation/modification/backup 
     *          dates, modification number, some offsets, record offsets,
     *          record attributes, appinfo block, sortinfo block
     * Shouldn't be called from outside the class.
	 */
   	function makeHeader()
	{
      	// 32 bytes = name, but only 31 available (one for null)
      	$header = $this->string( $this->name, 31 );
      	$header = $this->padString( $header, 32 );
      
      	// Attributes & version fields
      	$header .= $this->int16( $this->attributes );
      	$header .= $this->int16( $this->version );
      
		// Creation, modification, and backup date
      	if ( $this->creation_time != 0 )
         	$header .= $this->int32( $this->creation_time + PALMDB_EPOCH_1904 );
      	else
         	$header .= $this->int32( time() + PALMDB_EPOCH_1904 );
			
      	if ( $this->modification_time != 0 )
         	$header .= $this->int32( $this->modification_time + PALMDB_EPOCH_1904 );
      	else
         	$header .= $this->int32( time() + PALMDB_EPOCH_1904 );
      
	  	if ( $this->backup_time != 0 )
         	$header .= $this->int32( $this->backup_time + PALMDB_EPOCH_1904 );
      	else
         	$header .= $this->int32( 0 );
      
      	// Calculate the initial offset
      	$Offset  = PALMDB_HEADER_SIZE + PALMDB_INDEX_HEADER_SIZE;
      	$Offset += PALMDB_RECORD_HEADER_SIZE * count( $this->getRecordIDs() );
      
      	// Modification number, app information id, sort information id
      	$header .= $this->int32( $this->mod_number );
      
      	$AppInfo_Size = $this->getAppInfoSize();
      
	  	if ( $AppInfo_Size > 0 ) 
		{
         	$header .= $this->int32( $Offset );
	 		$Offset += $AppInfo_Size;
      	} 
		else
		{
         	$header .= $this->int32( 0 );
      	}
		
      	$SortInfo_Size = $this->getSortInfoSize();
      
	  	if ( $SortInfo_Size > 0 ) 
		{
         	$header .= $this->int32( $Offset );
         	$Offset += $SortInfo_Size;
      	} 
		else
		{
         	$header .= $this->int32( 0 );
	 	}
		
      	// Type, creator
      	$header .= $this->string( $this->type_id, 4 );
      	$header .= $this->string( $this->creator_id, 4 );
      
      	// Unique ID seed
      	$header .= $this->int32( 0 );
      
      	// next record list
      	$header .= $this->int32( 0 );
      
      	// Number of records
      	$header .= $this->int16( $this->getRecordCount() );
      
      	// Compensate for the extra 2 NULL characters in the $Offset
      	$Offset += 2;
      
      	// Dump each record
      	if ( $this->getRecordCount() != 0 ) 
		{
         	$keys = $this->getRecordIDs( );
	 		sort( $keys, SORT_NUMERIC );
	 
	 		foreach ( $keys as $index ) 
			{
	    		$header .= $this->int32( $Offset );
	    		$header .= $this->int8( $this->getRecordAttrib( $index ) );
	    
	    		// The unique id is just going to be the record number
	    		$header .= $this->int24( $index );
	    
	    		$Offset += $this->getRecordSize( $index );
	    
				// *new* method 3
	    		// $Mod4 = $Offset % 4;
	    		// if ( $Mod4 )
	    		//   $Offset += 4 - $Mod4;
	 		}
      	}
      
      	// AppInfo and SortInfo blocks go here
      	if ( $AppInfo_Size > 0 )
         	$header .= $this->getAppInfo();
      
	  	// $header .= $this->padTo4ByteBoundary( $this->getAppInfo() );
      
      	if ( $SortInfo_Size > 0 )
         	$header .= $this->getSortInfo();
      
	  	// $header .= $this->padTo4ByteBoundary( $this->getSortInfo() );

      	// These are the mysterious two NULL characters that we need
      	$header .= $this->int16( 0 );
      
      	return $header;
   	}
   
   	/**
	 * Writes the database to the file handle specified.
     * Use this function like this:
	 *
     *   $file = fopen( "output.pdb", "wb" ); 
     *   // "wb" = write binary for non-Unix systems
     *   if ( !$file ) 
	 *   {
     *      echo "big problem -- can't open file";
     *      exit;
     *   }
	 *  
     *   $pdb->writeToFile( $file );
     *   fclose( $file );
	 */
   	function writeToFile( $file ) 
	{
      	$header = $this->makeHeader();
      	fwrite( $file, pack( 'H*', $header ), strlen( $header ) / 2 );
      	$keys = $this->getRecordIDs();
      	sort( $keys, SORT_NUMERIC );
      
	  	foreach ( $keys as $index ) 
		{
         	// *new* method 3
         	// $data = $this->padTo4ByteBoundary( $this->getRecord( $index ) );
         
		 	$data = $this->getRecord( $index );
	 		fwrite( $file, pack( 'H*', $data ), strlen( $data ) / 2 );
      	}
      
	  	fflush( $file );
   	}
   
   	/**
     * Writes the database to the standard output (like echo).
     * Can be trapped with output buffering.
	 */
   	function writeToStdout()
	{
      	// You'd think these three lines would work.
      	// If someone can figure out why they don't, please tell me.
      	//
      	// $fp = fopen( 'php://stdout', 'wb' );
      	// $this->writeToFile( $fp );
      	// fclose( $fp );
      
      	$header = $this->makeHeader();
      	echo pack( "H*", $header );
      	$keys = $this->getRecordIDs();
      	sort( $keys, SORT_NUMERIC );
      
	  	foreach ( $keys as $index ) 
		{
         	// *new* method 3
	 		$data = $this->getRecord( $index );
         	// $data = $this->padTo4ByteBoundary( $this->getRecord( $index ) );
	 		echo pack( "H*", $data );
      	}
   	}
   
    /**
	 * Writes the database to the standard output (like echo) but also
     * writes some headers so that the browser should prompt to save the
     * file properly.
     *
     * Use this only if you didn't send any content and you only want the
     * PHP script to output the PDB file and nothing else.  An example
     * would be if you wanted to have 'download' link so the user can
     * stick the information they are currently viewing and transfer
     * it easily into their handheld.
     *
     * $filename is the desired filename to download the database as.
     * For example, downloadPDB( 'memos.pdb' );
	 */
   	function downloadPDB( $filename ) 
	{
      	global $HTTP_USER_AGENT;
      
      	// Alter the filename to only allow certain characters.
      	// Some platforms and some browsers don't respond well if
      	// there are illegal characters (such as spaces) in the name of
      	// the file being downloaded.
      	$filename = preg_replace( '/[^-a-zA-Z0-9\\.]/', '_', $filename );
      
      	if ( strstr( $HTTP_USER_AGENT, 'compatible; MSIE ' ) !== false && strstr( $HTTP_USER_AGENT, 'Opera' ) === false ) 
		{
	 		// IE doesn't properly download attachments.  This should work
	 		// pretty well for IE 5.5 SP 1
	 		header( "Content-Disposition: inline; filename=$filename" );
	 		header( "Content-Type: application/download; name=\"$filename\"" );
      	} 
		else 
		{
         	// Use standard headers for Netscape, Opera, etc.
	 		header( "Content-Disposition: attachment; filename=\"$filename\"" );
	 		header( "Content-Type: application/x-pilot; name=\"$filename\"" );
      	}
      
      	$this->writeToStdout();
   	}
   
   
    /*
     * Loading in a database
     */
       
   	/**
	 * Reads data from the file and tries to load it properly
     * $file is the already-opened file handle.
     * Returns false if no error.
	 */
   	function readFile( $file ) 
	{
      	// 32 bytes = name, but only 31 available
      	$this->name = fread( $file, 32 );
      
      	$i = 0;
      	while ( $i < 32 && $this->name[$i] != "\0" )
         	$i++;
      
	  	$this->name          = substr( $this->name, 0, $i );
      	$this->attributes    = $this->loadInt16( $file );
      	$this->version       = $this->loadInt16( $file );
      	$this->creation_time = $this->loadInt32( $file );
      
	  	if ( $this->creation_time != 0 )
       		$this->creation_time -= PALMDB_EPOCH_1904;
      
	  	if ( $this->creation_time < 0 )
         	$this->creation_time = 0;
	    
      	$this->modification_time = $this->loadInt32( $file );
      
	  	if ( $this->modification_time != 0 )
         	$this->modification_time -= PALMDB_EPOCH_1904;
      
	  	if ( $this->modification_time < 0 )
         	$this->modification_time = 0;
	    
      	$this->backup_time = $this->loadInt32( $file );
      
	  	if ( $this->backup_time != 0 )
         	$this->backup_time -= PALMDB_EPOCH_1904;
      
	  	if ( $this->backup_time < 0 )
         	$this->backup_time = 0;
	 
      	// Modification number
      	$this->mod_number = $this->loadInt32( $file );
      
      	// AppInfo and SortInfo size
      	$AppInfoOffset  = $this->loadInt32( $file );
      	$SortInfoOffset = $this->loadInt32( $file );
      
      	// Type, creator
      	$this->type_id    = fread( $file, 4 );
      	$this->creator_id = fread( $file, 4 );
      
      	// Skip unique ID seed
      	fread( $file, 4 );
      
      	// skip next record list (hope that's ok)
      	fread( $file, 4 );
      
      	$RecCount   = $this->loadInt16( $file );
      	$RecordData = array();
      
      	while ( $RecCount > 0 ) 
		{
         	$RecCount--;
	 
	 		$Offset = $this->loadInt32( $file );
	 		$Attrs  = $this->loadInt8(  $file );
	 		$UID    = $this->loadInt24( $file );
	 
	 		$RecordData[] = array(
				'Offset' => $Offset, 
				'Attrs'  => $Attrs,
				'UID'    => $UID
			);
      	}
      
      	// Create the offset list
      	if ( $AppInfoOffset != 0 )
         	$OffsetList[$AppInfoOffset] = 'AppInfo';
      
	  	if ( $SortInfoOffset != 0 )
         	$OffsetList[$SortInfoOffset] = 'SortInfo';
      
	  	foreach ( $RecordData as $data )
         	$OffsetList[$data['Offset']] = array( 'Record', $data );
      
	  	fseek( $file, 0, SEEK_END );
      	$OffsetList[ftell( $file )] = 'EOF';
      
      	// Parse each chunk
      	ksort( $OffsetList );
      	$Offsets = array_keys( $OffsetList );
      
	  	while ( count( $Offsets ) > 1 ) 
		{
         	// Don't use the EOF (which should be the last offset)
	 		$ThisOffset = $Offsets[0];
	 		$NextOffset = $Offsets[1];
	 
	 		// Messed up file. Stop here.
	 		if ( $OffsetList[$ThisOffset] == 'EOF' )
	    		return true;
	 
	 		$FuncName = 'Load';
	 
	 		if ( is_array( $OffsetList[$ThisOffset] ) ) 
			{
	    		$FuncName  .= $OffsetList[$ThisOffset][0];
	    		$extraData  = $OffsetList[$ThisOffset][1];
	 		} 
			else 
			{
	    		$FuncName  .= $OffsetList[$ThisOffset];
	    		$extraData  = false;
	 		}
	 
	 		fseek( $file, $ThisOffset );
	 		$fileData = fread( $file, $NextOffset - $ThisOffset );
	 
	 		if ( $this->$FuncName( $fileData, $extraData ) )
	    		return -2;
	 
	 		array_shift( $Offsets );
      	}
      
      	return false;
 	}
  
   	/**
	 * Generic function to load the AppInfo block into $this->AppInfo.
     * Should only be called within this class.
     * Return false to signal no error.
	 */
   	function loadAppInfo( $fileData ) 
	{
      	$this->app_info = bin2hex( $fileData );
      	return false;
   	}
   
   	/**
	 * Generic function to load the SortInfo block into $this->SortInfo.
     * Should only be called within this class.
     * Return false to signal no error.
	 */
   	function loadSortInfo( $fileData ) 
	{
      	$this->sort_info = bin2hex( $fileData );
      	return false;
   	}
   
   	/**
	 * Generic function to load a record.
     * Should only be called within this class.
     * Return false to signal no error.
	 */
   	function loadRecord( $fileData, $recordInfo ) 
	{
      	$this->records[$recordInfo['UID']] = bin2hex( $fileData );
      	$this->record_attrs[$recordInfo['UID']] = $recordInfo['Attrs'];
      
	  	return false;
   	}
} // END OF PalmDB

?>
