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
|Authors: John Papandriopoulos                                         |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * Path to NanoDB databases
 * @defined NANODB_DB_DIRECTORY
 */
if ( !defined( "NANODB_DB_DIRECTORY" ) ) 
	define( "NANODB_DB_DIRECTORY", AP_ROOT_PATH . ap_ini_get( "path_nano_db", "path" ) );

/**
 * Path to temp directory
 * @defined NANODB_TMP_DIRECTORY
 */
if ( !defined( "NANODB_TMP_DIRECTORY" ) ) 
	define( "NANODB_TMP_DIRECTORY", AP_ROOT_PATH . ap_ini_get( "path_tmp_ap", "path" ) );

/**
 * Major version of the NanoDB package
 * @defined NANODB_VERSION_MAJOR
 */
define( "NANODB_VERSION_MAJOR", 1 );

/**
 * Minor version of the NanoDB package
 * @defined NANODB_VERSION_MINOR
 */
define( "NANODB_VERSION_MINOR", 0 );

/**
 * Version of the NanoDB package
 * @defined NANODB_VERSION
 */
define( "NANODB_VERSION", NANODB_VERSION_MAJOR . "." . NANODB_VERSION_MINOR );

/**
 * NanoDB int type
 * @defined NANODB_INT
 */
define( "NANODB_INT", 0 );

/**
 * NanoDB automatically incremented int type
 * @defined NANODB_INT_AUTOINC
 */
define( "NANODB_INT_AUTOINC", 5 );

/**
 * NanoDB string type
 * @defined NANODB_STRING
 */
define( "NANODB_STRING", 1 );

/**
 * NanoDB array type
 * @defined NANODB_ARRAY
 */
define( "NANODB_ARRAY", 2 );

/**
 * NanoDB float type
 * @defined NANODB_FLOAT
 */
define( "NANODB_FLOAT", 3 );

/**
 * NanoDB boolean type
 * @defined NANODB_BOOL
 */
define( "NANODB_BOOL", 4 );

/**
 * Index field used when returning records. See the 
 * getbyfield(...), getbyfunction(...) and getall(...) methods.
 * @defined NANODB_IFIELD
 */
define( "NANODB_IFIELD", "NANODB_IFIELD" );

/**
 * Database signature placed at the start of each index file.
 * Internal use only.
 * @defined NANODB_SIGNATURE
 */
define( "NANODB_SIGNATURE", 90601344 ); /* actually my phone number*/

/**
 * Location of the 'version' offset in the NanoDB index.
 * Internal use only.
 * @defined NANODB_INDEX_VERSION_OFFSET
 */
define( "NANODB_INDEX_VERSION_OFFSET", 4 );

/**
 * Location of the 'records count' offset in the NanoDB index.
 * Internal use only.
 * @defined NANODB_INDEX_RECORDS_OFFSET
 */
define( "NANODB_INDEX_RECORDS_OFFSET", 6 );

/**
 * Location of the 'deleted count' offset in the NanoDB index.
 * Always the next 'int size' offset after the 'records count' offset.
 * Internal use only.
 * @defined NANODB_INDEX_DELETED_OFFSET
 */
define( "NANODB_INDEX_DELETED_OFFSET", NANODB_INDEX_RECORDS_OFFSET + 4 );

/**
 * Size of the field specifing the size of a record in the index.
 * Internal use only.
 * @defined NANODB_INDEX_RBLOCK_SIZE
 */
define( "NANODB_INDEX_RBLOCK_SIZE", 4 /* int */ );


/**
 * Advanced PHP database for those without mySQL or similar 
 * dedicated databases. Supports many data types and advanced features through 
 * a simple PHP object API. Low-level implementation very efficient with 
 * indexes.
 *
 * Overview
 * 
 * This package provides an advanced database package for PHP for those without
 * access to mySQL. It is not a complete drop-in replacement for mySQL.  In 
 * fact, it doesn't support SQL at all.
 * 
 * However, most commonly used database functionality is provided in addition
 * to some advanced features. It is possible to write another PHP class to 
 * wrap around NanoDB to provide SQL functionality. I am happy with using the 
 * methods on the current NanoDB class, so I will probably not write such a 
 * wrapper class myself.
 * 
 * Originally, I had wanted a small database for some PHP scripting, but did
 * not have access to mySQL.  A package called TextDB was found, however I kept
 * finding bugs - in the end I decided it would be better to create my own
 * database library that worked the way I wanted, and that I could customise to 
 * my own use with great ease.
 * 
 * Although the performance of NanoDB is nowhere near that of mySQL, it should be 
 * OK for light use.  It uses a binary search and a sorted index to find items
 * in the database, and tries to minimise the disk space taken up by each file
 * and the amount of disk IO that is required to do a read or write.  If you 
 * really want the result of your queries sorted, you can do this too.  You
 * can also specify a user defined function or a regular expression to select 
 * the records you want before sorting.
 * 
 * Note that although the package is called "NanoDB", it 
 * really isn't a flat file due to the index and optimisations written in 
 * the code.
 * 
 * 
 * Requirements
 * 
 * The NanoDB package requires:
 * 
 *    o PHP4.0.0 or better
 *    o Ability to read/write (binary) files [1]
 * 
 * ** WARNING **
 * 
 *   The flock(...) function may not work correctly under MS-Windows. Without
 *   the correct operation of flock(...), your databases may become corrupted.
 *   See http://www.php.net/manual/en/function.flock.php for more information.
 * 
 * [1] Make sure your permissions are correct on your web host so that the 
 *     webserver/PHP can read/write your database files.
 * 
 *
 * How to Install
 * 
 * Just copy the file 'src/NanoDB.php' to your webspace, and in your PHP
 * script insert the line:
 * 
 *    include( "NanoDB.php" );
 * 
 * before you start using NanoDB.  Note that this assumes the 'NanoDB.php' file
 * is in the same directory as your own PHP code.
 * 
 * 
 * How to protect your databases
 * 
 * All of the databases created with NanoDB are just files in your publically
 * accessible webspace.  This means that if someone knows the filename of your
 * database, they can just download it.  If you are storing sensitive information
 * in these database files, then you need to protect them.
 * 
 * To do this, place all of your databases into a single directory, having it
 * separate to all of your public content.  e.g. if your content is in 
 * the directory /home/public_html/php/ then place all of your databases in the 
 * directory /home/public_html/php/db/
 * 
 * Now set up an access control file: .htaccess in your database directory
 * (/home/public_html/php/db/ in the case above).
 * 
 * To do this, create a file called .htaccess in your database directory, and 
 * place in it the following 5 lines:
 * 
 * <Limit GET POST>
 *    order deny,allow
 *    deny from all
 *    satisfy all
 * </Limit>
 * 
 * This tells the webserver that no HTTP client (web browser) can get access
 *  * to any of the files in the directory.  PHP is free to read/write those
 * files however, meaning your databases will still work with the NanoDB package.
 * 
 * Note that some webservers have not enabled user .htaccess files.  In this
 * case, your only choice is to try and hide your databases with filenames
 * that you think no-one would guess -- or you could just place all of your
 * databases in a directory that you think no-one will guess.
 * 
 * Alternatively, you could use the mcrypt PHP functions to encrypt your data
 * before storing it in the database.  Note that before trying to store raw
 * binary data in a NanoDB database, try encoding them with the base64_encode
 * and base64_decode functions.
 *
 *
 * WISHLIST
 *
 * - SQL-Wrapper
 * - Socket-Server
 * - sort, limit
 * - fulltext search
 * - query cache
 * - support for sqlite file format
 * - crypting stuff
 * 
 *
 * SUPPORTED TYPES
 * 
 * Integer 			done
 * Float (Double) 	done
 * Boolean 			done
 * String			done
 * 
 * Array 			done
 * Object			todo
 * Date				todo
 *
 * @package db_nanosql
 */
 
class NanoDB extends PEAR
{
	var $isopen;
	var $dbname;
	var $data_fp;
	var $meta_fp;
	var $records;
	var $deleted;
	var $locked;
	var $auto_clean;
	var $fields;
	var $autoinc;
	var $primary_key;
	var $index_start;
	
	var $db_path = "./";
	
	
	/**
	 * Constructor
	 */
	function NanoDB()
	{
		// Disable auto-clean by default
		$this->auto_clean = -1;

		// Database hasn't been opened yet...
		$this->isopen = false;

		// Ignore user aborts that might corrupt the database
		ignore_user_abort( true );
	}

	/**
	 * Set database path.
	 * Note: useless at the moment
	 *
	 * @param dbpath  string - The path of the database to open
	 * @return true if successful, Error if failed
	 */
	function setdbpath( $path = "./" )
	{
		if ( !is_dir( $path ) )
			return PEAR::raiseError( "Path is not a valid directory." );
		
		$this->db_path = $path;
		return true;
	}
	
	/**
	 * Get database path.
	 * Note: useless at the moment
	 *
	 * @return string - database path
	 */
	function getdbpath()
	{
		return $this->db_path;
	}
	
	/**
	 * Opens the given database.
	 *
	 * @param dbname  string - The name of the database to open
	 * @return bool - true if successful, Error/false if failed
	 */
	function open( $dbname )
	{
		// Close existing databases first.
		if ( $this->isopen )
			$this->close();
		
		// Open the database files.
		$this->data_fp = @fopen( $dbname . ".data", "rb+" );
		
		if ( $this->data_fp === false )
			return false;

		$this->meta_fp = @fopen( $dbname . ".meta", "rb+" );

		if ( !$this->meta_fp )
		{
			fclose( $this->data_fp );
			return false;
		}

		$this->forcelock = 0;
		$this->locked    = 0;
		$this->isopen    = true;
		$this->dbname    = $dbname;
		
		if ( PEAR::isError( $res = $this->_lock_read() ) )
			return $res;

		// Read and verify the signature.
		$sig = $this->_read_int( $this->meta_fp );
		
		if ( $sig != NANODB_SIGNATURE )
			return PEAR::raiseError( "Invalid database: $dbname." );

		// Read the version.
		$ver_major = $this->_read_byte( $this->meta_fp );
		$ver_minor = $this->_read_byte( $this->meta_fp );

		// Make sure we only read databases of the same major version,
		// with minor version less or equal to the current.
		if ( $ver_major != NANODB_VERSION_MAJOR )
		{
			$this->_unlock();
			return PEAR::raiseError( "Cannot open database (of version $ver_major.$ver_minor), wrong version." );
		}
		
		if ( $ver_minor > NANODB_VERSION_MINOR )
		{
			$this->_unlock();
			return PEAR::raiseError( "Cannot open database (of version $ver_major.$ver_minor), wrong version." );
		}

		// Read the schema and database statistics from the meta file.
		$this->_readschema();

		return $this->_unlock();
	}
	
	/**
	 * Closes the currently opened database.
	 */
	function close()
	{
		if ( $this->isopen )
		{
			@fclose( $this->data_fp );
			@fclose( $this->meta_fp );

			$this->isopen = false;
		}
	}

	/**
	 * Closes the current database then deletes it.
	 *
	 * @return bool - true on success
	 */
	function drop()
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );

		$this->close();

		@unlink( $this->dbname . ".data" );
		@unlink( $this->dbname . ".meta" );

		return true;
	}

	/**
	 * Creates a new database.
	 *
	 * @param dbname  string - name of the database
	 * @param schema  array - name<->type array of fields for table.
	 * Note that the key cannot be an array or boolean type field.  
	 * The key is given by a third attribute - a string "key".
	 * @return bool - true if successful, Error/false on failure
	 */
	function create( $dbname, $schema )
	{
		// Close any existing DB first.
		if ( $this->isopen )
			$this->close();

		// Find the primary key and do error checking on the schema.
		$this->fields      = array();
		$this->autoinc     = array();
		$this->primary_key = "";

		for ( $i = 0; $i < count( $schema ); ++$i )
		{
			$field = $schema[$i];

			if ( !is_array( $field ) )
				return false;

			$name = &$field[0];
			$type = &$field[1];

			// Make sure the name of the field is a string.
			if ( !is_string( $name ) )
				return false;

			// Make sure the field type is one of our constants.
			if ( !is_int( $type ) )
				return false;

			switch ( $type )
			{
				case NANODB_INT:
				
				case NANODB_STRING:
				
				case NANODB_ARRAY:
				
				case NANODB_FLOAT:
				
				case NANODB_BOOL:
					break;

				case NANODB_INT_AUTOINC:
					// Set up the default starting value for an auto-inc'ed field.
					$this->autoinc[$name] = 0;
					break;

				default:
					// Unknown type...!
					return PEAR::raiseError( "Invalid type in schema (found $type)." );
			}

			if ( count( $field ) == 3 )
			{
				$keyword = &$field[2];

				if ( ( $type == NANODB_INT_AUTOINC ) && is_int( $keyword ) )
				{
					// Auto-increment starting value.
					$this->autoinc[$name] = $keyword;
				}
				else if ( ( $keyword == "key" ) && ( $this->primary_key == "" ) )
				{
					// Primary key!

					// Is the key an array or boolean?  
					// If so, don't allow them to be primary keys...
					switch ( $type )
					{
						case NANODB_ARRAY:
						
						case NANODB_BOOL:
							return false;
					}

					$this->primary_key = $name;
				}
				else
				{
					return false;
				}
			}
			else if ( count( $field ) == 4 )
			{
				$start   = &$field[2];
				$keyword = &$field[3];

				// This MUST be a starting-value & "key" keyword 
				// combination (in that order).
				if ( ( $type == NANODB_INT_AUTOINC ) && is_int( $start ) && ( $keyword == "key" ) && ( $this->primary_key == "" ) )
				{
					// Found an auto-increment starting value.
					$this->autoinc[$name] = $start;

					// Found a primary key.
					$this->primary_key = $name;
				}
				else
				{
					return false;
				}
			}

			$this->fields[$field[0]] = $field[1];
		}

		// Create the database files.
		$this->meta_fp = @fopen( $dbname . ".meta", "wb+" );
		
		if ( !$this->meta_fp ) 
			return PEAR::raiseError( "Cannot create meta file: $dbname.meta" );

		$this->data_fp = @fopen( $dbname . ".data", "wb+" );
		
		if ( !$this->data_fp )
		{
			fclose( $this->meta_fp );
			return PEAR::raiseError( "Cannot create data file: $dbname.data" );
		}

		$this->forcelock = 0;
		$this->locked    = 0;
		$this->isopen    = true;
		$this->dbname    = $dbname;

		if ( PEAR::isError( $res = $this->_lock_write() ) )
			return $res;

		$this->records = 0;
		$this->deleted = 0;

		// Write the signature.
		$this->_write_int( $this->meta_fp, NANODB_SIGNATURE );

		// Write the version.
		$this->_write_byte( $this->meta_fp, NANODB_VERSION_MAJOR );
		$this->_write_byte( $this->meta_fp, NANODB_VERSION_MINOR );
		
		// Write the schema to the meta file.
		$this->_writeschema();

		return $this->_unlock();
	}
	
	/**
	 * Configures autoclean. When an edit or delete is made, the
	 * record is normally not removed from the data file - only the index.
	 * After repeated edits/deletions, the data file may become very big with
	 * dirty (non-removed) records. A cleanup is normally done with the
	 * cleanup() method. Autoclean will do this automatically, keeping the
	 * number of dirty records to under the $threshold value.
	 * To turn off autoclean, set $threshold to a negative value.
	 *
	 * @param threshold  - number of dirty records to have at any one time.
	 */
	function autoclean( $threshold = -1 )
	{
		$this->auto_clean = $threshold;

		// Do an auto-cleanup if required.
		if ( ( $this->isopen ) && ( $this->auto_clean >= 0 ) && ( $this->deleted > $this->auto_clean ) )
			$this->cleanup();
	}

	/**
	 * Adds an entry to the database.
	 *
	 * @param record  array - record to add to the database
	 * @return bool - true on success, Error/false on failure
	 */
	function add( &$record )
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );

		// Verify record as compared to the schema.
		foreach ( $this->fields as $key => $type )
		{
			// We don't mind if they include a NANODB_INT_AUTOINC field,
			// as we determine its value in any case.
			if ( $type == NANODB_INT_AUTOINC )
				continue;

			// Ensure they have included an entry for each record field.
			if ( !array_key_exists( $key, $record ) )
				return PEAR::raiseError( "Missing field during add: $key." );

			// Verify the type.
			switch ( $type )
			{
				case NANODB_INT:
					if ( ! is_int( $record[$key] ) )
						return PEAR::raiseError( "Invalid int value field during add: $key." );
					
					break;
				
				case NANODB_STRING:
					if ( !is_string( $record[$key] ) )
						return PEAR::raiseError( "Invalid string value field during add: $key." );
	
					break;
				
				case NANODB_ARRAY:
					if ( !is_array( $record[$key] ) )
						return PEAR::raiseError( "Invalid array value for field during add: $key." );
	
					break;

				case NANODB_FLOAT:
					if ( !is_float( $record[$key] ) )
						return PEAR::raiseError( "Invalid float value for field during add: $key." );

					break;
				
				case NANODB_BOOL:
					if ( !is_bool( $record[$key] ) )
						return PEAR::raiseError( "Invalid bool value for field during add: $key." );
					
					break;

				default:
					// Unknown type...!
					return PEAR::raiseError( "Invalid type in record during add (found $type)." );
			}
		}

		// Add the item to the data file.

		if ( PEAR::isError( $res = $this->_lock_write() ) )
			return $res;

		// Add in the auto-incremented field if required.
		if ( count( $this->autoinc ) > 0 )
		{
			// Get the latest copy of the auto-inc values.
			$this->_readschema();

			// Now update those values in our record.
			foreach ( $this->fields as $key => $type )
			{
				if ( $type == NANODB_INT_AUTOINC )
					$record[$key] = $this->autoinc[$key]++;
			}
			
			// Write out the newly updated auto-inc values.
			$this->_writeschema();
		}

		fseek( $this->data_fp, 0, SEEK_END );
		$new_offset = ftell( $this->data_fp );

		// Write the index. To enable a binary search, we must read in the 
		// entire index, add in our item, sort it, then write it back out.
		// Where there is no primary key, we can't do a binary search so skip
		// this sorting business.

		if ( $this->primary_key != "" )
		{
			if ( $this->records > 0 )
			{
				$index = $this->_readindex();

				// Do a binary search to find the insertion position.
				$pos = $this->_bsearch(
					$index, 
					0, 
					$this->records - 1, 
					$record[$this->primary_key]
				);

				// Ensure we don't have a duplicate key in the database.
				if ( $pos > 0 )
				{
					// Oops... duplicate key
					return false;
				}

				// Revert the result from bsearch to the proper insertion position.
				$pos = ( -$pos ) - 1;

				// Shuffle all of the items up by one to make room for the new item.
				for ( $i = $this->records; $i > $pos; --$i )
					$index[$i] = $index[$i-1];

				// Insert the new item to the correct position.
				$index[$pos] = $new_offset;
			}
			else
			{
				$index[0] = $new_offset;
			}

			// We have a new entry.
			++$this->records;

			// Write the index back out to the file.
			$this->_writeindex( $index );
		}
		else
		{
			// We have a new entry.
			++$this->records;

			// Add an entry into the index.
			fseek( $this->meta_fp, 0, SEEK_END );
			$this->_write_int( $this->meta_fp, $new_offset );
		}

		// Write the number of records to the meta data file.
		fseek( $this->meta_fp, NANODB_INDEX_RECORDS_OFFSET, SEEK_SET );
		$this->_write_int( $this->meta_fp, $this->records );

		// Write the record to the end of the database file.
		fseek( $this->data_fp, $new_offset, SEEK_SET );

		if ( !$this->_writerecord( $this->data_fp, $record ) )
		{
			// Error writing item to the database.
			$this->_unlock();
			return false;
		}

		return $this->_unlock();
	}
	
	/**
	 * Removes an entry from the database INDEX only - it appears
	 * deleted, but the actual data is only removed from the file when a 
	 * cleanup() is called.
	 *
	 * @param key  primary key used to identify record to remove. For
	 * databases without primary keys, it is the record number (zero based) in
	 * the table.
	 * @return bool - true on success, Error/false on failure
	 */
	function removebykey( $key )
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );

		if ( $this->records == 0 )
			return false;

		// All we do here is remove the item from the index.
		// Read in the index, check to see if it exists, delete the item,
		// then rebuild the index on disk.
		
		if ( PEAR::isError( $res = $this->_lock_write() ) )
			return $res;

		$index = $this->_readindex();

		if ( $this->primary_key != "" )
		{
			// Do a binary search to find the item.
			$pos = $this->_bsearch( $index, 0, $this->records - 1, $key );

			if ( $pos < 0 )
			{
				// Not found!
				$this->_unlock();
				return false;
			}

			// Revert the result from bsearch to the proper insertion position.
			--$pos;

			// Shuffle all of the items down by one to remove the item.
			for ( $i = $pos; $i < $this->records - 1; ++$i )
				$index[$i] = $index[$i+1];

			// Kill the last array item.
			array_pop($index);
		}
		else
		{
			// Ensure the "key" is the item number within range.
			if ( !is_int( $key ) || ( $key < 0 ) || ( $key > $this->records - 1 ) )
			{
				$this->_unlock();
				return PEAR::raiseError( "Invalid record number ($key)." );
			}

			// Shuffle all of the items down by one to remove the item.
			for ( $i = $key; $i < $this->records - 1; ++$i )
				$index[$i] = $index[$i + 1];

			// Kill the last array item.
			array_pop( $index );
		}

		fseek( $this->meta_fp, NANODB_INDEX_RECORDS_OFFSET, SEEK_SET );

		// Write the number of records to the meta data file.
		$this->_write_int( $this->meta_fp, --$this->records );

		// Write the number of (unclean) deleted records to the meta data file.
		$this->_write_int( $this->meta_fp, ++$this->deleted );

		// Write the index back out to the file.
		$this->_writeindex( $index );

		$this->_unlock();

		// Do an auto-cleanup if required.
		if ( ( $this->auto_clean >= 0 ) && ( $this->deleted > $this->auto_clean ) )
			$this->cleanup();

		return true;
	}

	/**
	 * Removes an entry from the database INDEX only - it appears
	 * deleted, but the actual data is only removed from the file when a 
	 * cleanup() is called.
	 *
	 * @param record_num  The record number (zero based) in the table to remove.
	 * @return bool - true on success, Error/false on failure
	 */
	function removebyindex( $record_num )
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );

		if ( $this->records == 0 )
			return false;

		// All we do here is remove the item from the index.
		// Read in the index, check to see if it exists, delete the item,
		// then rebuild the index on disk.

		if ( PEAR::isError( $res = $this->_lock_write() ) )
			return $res;

		$index = $this->_readindex();

		// Make sure the record number is an int.
		if ( !is_int( $record_num ) )
		{
			$this->_unlock();
			return PEAR::raiseError( "Invalid record number ($record_num)." );
		}

		// Ensure it is within range.
		if ( ( $record_num < 0 ) || ( $record_num > $this->records - 1 ) )
		{
			$this->_unlock();
			return PEAR::raiseError( "Invalid record number ($record_num)." );
		}

		// Shuffle all of the items down by one to remove the item.
		for ( $i = $record_num; $i < $this->records - 1; ++$i )
			$index[$i] = $index[$i + 1];

		// Kill the last array item.
		array_pop( $index );

		fseek( $this->meta_fp, NANODB_INDEX_RECORDS_OFFSET, SEEK_SET );

		// Write the number of records to the meta data file.
		$this->_write_int( $this->meta_fp, --$this->records );

		// Write the number of (unclean) deleted records to the meta data file.
		$this->_write_int( $this->meta_fp, ++$this->deleted );

		// Write the index back out to the file.
		$this->_writeindex( $index );

		$this->_unlock();

		// Do an auto-cleanup if required.
		if ( ( $this->auto_clean >= 0 ) && ( $this->deleted > $this->auto_clean ) )
			$this->cleanup();

		return true;
	}

	/**
	 * Removes entries from the database INDEX only, based on the
	 * result of a regular expression match on a given field - records appear 
	 * deleted, but the actual data is only removed from the file when a 
	 * cleanup() is called.
	 *
	 * @param fieldname  the field which to do matching on
	 * @param regex  the regular expression to match a field on.
	 * Note: you should include the delimiters ("/php/i" for example).
	 *
	 * @return int - number of records removed , or Error/false on failure
	 */
	function removebyfield( $fieldname, $regex )
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );

		if ( $this->records == 0 )
			return 0;

		// Read in each record once at a time, and remove it from
		// the index if the select function determines it to be deleted.
		// Rebuild the index on disc if there items were deleted.
		$delete_count = 0;

		if ( PEAR::isError( $res = $this->_lock_write() ) )
			return $res;

		$index = $this->_readindex();

		// Read and delete selected records.
		for ( $record_num = 0; $record_num < $this->records; ++$record_num )
		{
			// Read the record.
			list( $record, $rsize ) = $this->_readrecord( $this->data_fp, $index[$record_num] );

			// Remove the record if the field matches the regular expression.
			if ( preg_match( $regex, $record[$fieldname] ) )
			{
				// Delete this item from the index.
				// Shuffle all of the items down by one to remove the item.
				for ( $i = $record_num; $i < $this->records - 1; ++$i )
					$index[$i] = $index[$i + 1];
				
				// Kill the last index item that was duplicated.
				array_pop( $index );

				--$this->records;
				++$this->deleted;

				// Make sure we don't skip over the next item in the for() loop.
				--$record_num;

				++$delete_count;
			}
		}

		if ( $delete_count > 0 )
		{
			fseek( $this->meta_fp, NANODB_INDEX_RECORDS_OFFSET, SEEK_SET );

			// Write the number of records to the meta data file.
			$this->_write_int( $this->meta_fp, $this->records );

			// Write the number of (unclean) deleted records to the meta data file.
			$this->_write_int( $this->meta_fp, $this->deleted );

			// Write the index back out to the file.
			$this->_writeindex( $index );
		}

		$this->_unlock();

		// Do an auto-cleanup if required.
		if ( ( $delete_count > 0 ) && ( $this->auto_clean >= 0 ) && ( $this->deleted > $this->auto_clean ) )
			$this->cleanup();

		return $delete_count;
	}

	/**
	 * Removes entries from the database INDEX only, based on the
	 * result of a user-specified function - records appear deleted, but the 
	 * actual data is only removed from the file when a  cleanup() is called.
	 *
	 * @param selectfn  the function that accepts one argument (an array record),
	 * and returns true or false.
	 * @return int - number of records removed, or Error/false on failure
	 */
	function removebyfunction( $selectfn )
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );
			
		if ( $this->records == 0 )
			return 0;

		// Read in each record once at a time, and remove it from
		// the index if the select function determines it to be deleted.
		// Rebuild the index on disc if there items were deleted.
		$delete_count = 0;

		if ( PEAR::isError( $res = $this->_lock_write() ) )
			return $res;

		$index = $this->_readindex();

		// Read and delete selected records.
		for ( $record_num = 0; $record_num < $this->records; ++$record_num )
		{
			// Read the record.
			list( $record, $rsize ) = $this->_readrecord( $this->data_fp, $index[$record_num] );

			// Remove the record if the $selectfn OK's it.
			if ( $selectfn($record) == true )
			{
				// Delete this item from the index.
				// Shuffle all of the items down by one to remove the item.
				for ( $i = $record_num; $i < $this->records-1; ++$i )
					$index[$i] = $index[$i + 1];
				
				// Kill the last index item that was duplicated.
				array_pop( $index );

				--$this->records;
				++$this->deleted;

				// Make sure we don't skip over the next item in the for() loop.
				--$record_num;

				++$delete_count;
			}
		}

		if ( $delete_count > 0 )
		{
			fseek( $this->meta_fp, NANODB_INDEX_RECORDS_OFFSET, SEEK_SET );

			// Write the number of records to the meta data file.
			$this->_write_int( $this->meta_fp, $this->records );

			// Write the number of (unclean) deleted records to the meta data file.
			$this->_write_int( $this->meta_fp, $this->deleted );

			// Write the index back out to the file.
			$this->_writeindex( $index );
		}

		$this->_unlock();

		// Do an auto-cleanup if required.
		if ( ( $delete_count > 0 ) && ( $this->auto_clean >= 0 ) && ( $this->deleted > $this->auto_clean ) )
			$this->cleanup();

		return $delete_count;
	}

	/**
	 * Replaces an existing record with the same primary 
	 * key as the new record.
	 *
	 * @param record  record that will replace an existing one
	 * @param record_num  record number to replace: ONLY for databases without
	 * a primary key. Ignored for databases WITH a primary key.
	 * @return bool - true on success, Error/false on failure
	 */
	function edit( &$record, $record_num = -1 )
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );

		if ( $this->records == 0 )
			return false;

		// Verify record as compared to the schema.
		foreach ( $this->fields as $key => $type )
		{
			// Ensure they have included an entry for each record field.
			if ( !array_key_exists( $key, $record ) )
				return PEAR::raiseError( "Missing field during edit: $key." );

			// Verify the type.
			switch ( $type )
			{
				case NANODB_INT_AUTOINC:
				
				case NANODB_INT:
					if ( !is_int( $record[$key] ) )
						return PEAR::raiseError( "Invalid int value for field during edit: $key." );
					
					break;
				
				case NANODB_STRING:
					if ( !is_string( $record[$key] ) )
						return PEAR::raiseError( "Invalid string value for field during edit: $key." );
						
					break;
				
				case NANODB_ARRAY:
					if ( !is_array( $record[$key] ) )
						return PEAR::raiseError( "Invalid array value for field during edit: $key." );
						
					break;

				case NANODB_FLOAT:
					if ( !is_float( $record[$key] ) )
						return PEAR::raiseError( "Invalid float value for field during edit: $key." );

					break;
				
				case NANODB_BOOL:
					if ( !is_bool( $record[$key] ) )
						return PEAR::raiseError( "Invalid bool value for field during edit: $key." );
					
					break;

				default:
					// Unknown type...!
					return PEAR::raiseError( "Invalid type in record during edit (found $type)." );
			}
		}

		if ( PEAR::isError( $res = $this->_lock_write() ) )
			return $res;

		$index = $this->_readindex();

		// Get the position of a new record in the database store.
		fseek( $this->data_fp, 0, SEEK_END );
		$new_offset = ftell( $this->data_fp );

		// Re-jiggle the index.
		if ( $this->primary_key != "" )
		{
			// Do a binary search to find the index position.
			$pos = $this->_bsearch(
				$index, 
				0, 
				$this->records - 1, 
				$record[$this->primary_key]
			);

			// Ensure the item to edit IS in the database, 
			// as the new one takes its place.
			if ( $pos < 0 )
			{
				// Oops... record wasn't found
				return PEAR::raiseError( "Existing record not found in database for edit." );
			}

			// Revert the result from bsearch to the proper position.
			$record_num = $pos-1;
		}
		else
		{
			// Ensure the record number is a number within range.
			if ( !is_int( $record_num ) || ( $record_num < 0 ) || ( $record_num > $this->records - 1 ) )
			{
				$this->_unlock();
				return PEAR::raiseError( "Invalid record number ($record_num)." );
			}
		}

		// Read the size of the record. If it is the same or bigger than 
		// the new one, then we can just place it in its original position
		// and not worry about a dirty record.
		fseek( $this->data_fp, $index[$record_num], SEEK_SET );
		$hole_size = $this->_read_int( $this->data_fp );

		// Get the size of the new record for calculations below.
		$new_size = $this->_recordsize( $record );

		if ( $new_size > $hole_size )
		{
			// Record is too big for the "hole".
			// Set the index to the newly edited record.  
			// The old one will be removed on the next cleanup.
			$index[$record_num] = $new_offset;

			// Write the index back out to the file.
			$this->_writeindex( $index );

			// We have a new dirty entry (the old record).
			++$this->deleted;

			// Write the number of deleted records to the meta data file.
			fseek( $this->meta_fp, NANODB_INDEX_DELETED_OFFSET, SEEK_SET );
			$this->_write_int( $this->meta_fp, $this->deleted );
		}

		// Write the record to the database file.
		fseek( $this->data_fp, $index[$record_num], SEEK_SET );
		
		if ( !$this->_writerecord( $this->data_fp, $record, $hole_size ) )
		{
			// Error writing item to the database.
			$this->_unlock();
			return false;
		}

		$this->_unlock();

		// Do an auto-cleanup if required.
		if ( ( $this->auto_clean >= 0 ) && ( $this->deleted > $this->auto_clean ) )
			$this->cleanup();

		return true;
	}

	/**
	 * Cleans up the database by removing deleted entries
	 * from the flat file.
	 *
	 * @return true if successful, Error/false otherwise
	 */
	function cleanup()
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );

		// Don't bother if the database is clean.
		if ( $this->deleted == 0 )
			return true;

		if ( PEAR::isError( $res = $this->_lock_write() ) )
			return $res;

		// Read in the index, and rebuild it along with the database data
		// into a separate file. Then move that new file back over the old
		// database.

		// Note that we attempt the file creation under the DB lock, so
		// that another process doesn't try to create the same file at the
		// same time.
		$tmpdb = @fopen( $this->dbname . ".tmp", "wb+" );
		
		if ( !$tmpdb )
		{
			$this->_unlock();
			return PEAR::raiseError( "Cannot create temporary file." );
		}
		
		// Read in the index.
		$index = $this->_readindex();

		// For each item in the index, move it from the current database
		// file to the new one. Also update the new file offset in the index
		// so we can write it back out to the index file.
		for ( $i = 0; $i < $this->records; ++$i )
		{
			$offset = $index[$i];

			// Read in the entire record.
			unset( $record );
			list( $record, $rsize ) = $this->_readrecord( $this->data_fp, $offset );

			// Save the new file offset.
			$index[$i] = ftell( $tmpdb );

			// Write out the record to the temporary file.
			if ( !$this->_writerecord( $tmpdb, $record ) ) 
			{
				// Error writing item to the database.
				fclose( $tmpdb );
				@unlink( $this->dbname . ".tmp" );
				$this->_unlock();
				
				return false;
			}
		}

		// Move the temporary file over the original database file.
		fclose( $tmpdb );
		fclose( $this->data_fp );
		
		@unlink( $this->dbname . ".data" );
		@rename( $this->dbname . ".tmp", $this->dbname.".data" );

		// Set the number of (unclean) deleted items to zero.
		$this->deleted = 0;
		fseek( $this->meta_fp, NANODB_INDEX_DELETED_OFFSET, SEEK_SET );
		$this->_write_int( $this->meta_fp, $this->deleted );

		// Rewrite the database index.
		$this->_writeindex( $index );

		// Re-open the database data file.
		$this->data_fp = @fopen( $this->dbname.".data", "rb+" );

		if ( !$this->data_fp ) 
		{
			$this->_unlock();
			return false;
		}

		return $this->_unlock();
	}

	/**
	 * Reset the internal pointer used for iterating over records.
	 */
	function reset()
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );

		if ( PEAR::isError( $res = $this->_lock_read() ) )
			return $res;
		
		$this->iterator = $this->index_start;
		fseek( $this->meta_fp, $this->iterator, SEEK_SET );

		return $this->_unlock();
	}

	/**
	 * Return the current record in the database. Note that the 
	 * current iterator pointer is not moved in any way.
	 *
	 * @return the current database record, or Error/false if there are no 
	 * more items left.
	 */
	function current()
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );

		// No items?
		if ( $this->records == 0 )
			return false;

		if ( PEAR::isError( $res = $this->_lock_read() ) )
			return $res;

		// Offset of record to read.
		$offset = $this->_read_int( $this->meta_fp );

		// No more items left?
		if ( feof( $this->meta_fp ) )
		{
			$this->_unlock();
			return false;
		}

		// Restore the index position.
		fseek( $this->meta_fp, -4, SEEK_CUR );

		// Read in the entire record.
		list( $record, $rsize ) = $this->_readrecord( $this->data_fp, $offset );

		$this->_unlock();

		// Return the record.
		return $record;
	}

	/**
	 * Move the current iterator pointer to the next database item.
	 *
	 * @return bool - true if advanced to a new item, Error/false if there are 
	 * none left.
	 */
	function next()
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );

		// No items?
		if ( $this->records == 0 )
			return false;

		if ( PEAR::isError( $res = $this->_lock_read() ) )
			return $res;

		// Advance the pointer...
		$this->_read_int( $this->meta_fp );

		// Read another byte, to push uss over the EOF if it's there.
		// Seems to be a stupid problem with feof(...)
		$this->_read_byte( $this->meta_fp );

		$result = !feof( $this->meta_fp );

		// Back up that extra byte we read.
		fseek( $this->meta_fp, -1, SEEK_CUR );

		$this->_unlock();		
		return $result;
	}

	/**
	 * Retrieves a record based on the specified key.
	 *
	 * @param key  primary key used to identify record to retrieve. For
	 * databases without primary keys, it is the record number (zero based) in 
	 * the table.
	 * @param includeindex  if true, an extra field called 'NANODB_IFIELD' will
	 * be added to each record returned. It will contain an int that specifies
	 * the original position in the database (zero based) that the record is 
	 * positioned. It might be useful when an orderby is used, and an future 
	 * operation on a record is required, given it's index in the table.
	 * @return record if found, or Error/false otherwise
	 */
	function getbykey( $key, $includeindex = false )
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );

		if ( PEAR::isError( $res = $this->_lock_read() ) )
			return $res;

		// Read the index.
		$index = $this->_readindex();
		
		if ( $this->primary_key != "" )
		{
			// Do a binary search to find the item.
			$pos = $this->_bsearch( $index, 0, $this->records - 1, $key );

			if ( $pos < 0 )
			{
				// Not found!
				$this->_unlock();
				return false;
			}

			// bsearch always returns the real position + 1
			--$pos;

			// Get the offset of the record in the database.
			$offset = $index[$pos];

			// Save the record number.
			$rcount = $pos;
		}
		else
		{
			// Ensure the record number is an int and within range.
			if ( !is_int( $key ) || ( $key < 0 ) || ( $key > $this->records - 1 ) )
			{
				$this->_unlock();
				return PEAR::raiseError( "Invalid record number ($key)." );
			}

			$offset = $index[$key];

			// The record number is the key...
			$rcount = $key;
		}

		// Read the record.
		list( $record, $rsize ) = $this->_readrecord( $this->data_fp, $offset );

		// Add the index field if required.
		if ( $includeindex )
			$record[NANODB_IFIELD] = $rcount;

		$this->_unlock();
		return $record;
	}

	/**
	 * Retrieves a record based on the record number in the table
	 * (zero based).
	 *
	 * @param record_num  zero based record number to retrieve
	 * @return record if found, or Error/false otherwise
	 */
	function getbyindex( $record_num )
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );

		// Ensure the record number is an int.
		if ( !is_int( $record_num ) )
		{
			$this->_unlock();
			return PEAR::raiseError( "Invalid record number ($record_num)." );
		}

		// Ensure the record number is within range.
		if ( ( $record_num < 0 ) || ( $record_num > $this->records - 1 ) )
		{
			$this->_unlock();
			return PEAR::raiseError( "Invalid record number ($record_num)." );
		}

		if ( PEAR::isError( $res = $this->_lock_read() ) )
			return $res;

		// Read the index.
		$index  = $this->_readindex();
		$offset = $index[$record_num];

		// Read the record.
		list( $record, $rsize ) = $this->_readrecord( $this->data_fp, $offset );

		$this->_unlock();
		return $record;
	}

	/**
	 * Retrieves records in the database whose field matches the
	 * given regular expression.
	 *
	 * @param fieldname  the field which to do matching on
	 * @param regex  the regular expression to match a field on.
	 * Note: you should include the delimiters ("/php/i" for example).
	 * @param orderby  order the results. Set to the field name to order by
	 * (as a string). If left unset, sorting is not done and it is a lot faster.
	 * If prefixed by "!", results will be ordered in reverse order.  
	 * If orderby is an array, the 1st element refers to the field to order by,
	 * and the 2nd, a function that will take two take two parameters A and B 
	 * - two fields from two records - used to do the ordering. It is expected 
	 * that the function would return -ve if A < B and +ve if A > B, or zero 
	 * if A == B (to order in ascending order).
	 * @param includeindex  if true, an extra field called 'NANODB_IFIELD' will
	 * be added to each record returned. It will contain an int that specifies
	 * the original position in the database (zero based) that the record is 
	 * positioned. It might be useful when an orderby is used, and an future 
	 * operation on a record is required, given it's index in the table.
	 *
	 * @return matching records in an array, or Error/false on failure
	 */
	function getbyfield( $fieldname, $regex, $orderby = null, $includeindex = false )
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );

		// Check the field name.
		if ( !array_key_exists( $fieldname, $this->fields ) )
			return PEAR::raiseError( "Invalid field name for getbyfield: $fieldname." );

		// If there are no records, return.
		if ( $this->records == 0 )
			return array();

		if ( PEAR::isError( $res = $this->_lock_read() ) )
			return $res;

		// Read the index.
		$index = $this->_readindex();

		// Read each record and add it to an array.
		$rcount = 0;
		foreach ( $index as $offset )
		{
			// Read the record.
			list( $record, $rsize ) = $this->_readrecord( $this->data_fp, $offset );

			// See if the record matches the regular expression.
			if ( preg_match( $regex, $record[$fieldname] ) )
			{
				// Add the index field if required.
				if ( $includeindex )
					$record[NANODB_IFIELD] = $rcount;

				$result[] = $record;
			}
			
			++$rcount;
		}

		$this->_unlock();

		// Re-order as required.
		if ( $orderby !== null )
			return $this->_orderby( $result, $orderby );
		else
			return $result;
	}

	/**
	 * Retrieves all records in the database, passing each record 
	 * into a given function. If that function returns true, then it is added
	 * to the result (array) list.
	 *
	 * @param selectfn  the function that accepts one argument (an array record),
	 * and returns true or false.
	 * @param orderby  order the results. Set to the field name to order by
	 * (as a string). If left unset, sorting is not done and it is a lot faster.
	 * If prefixed by "!", results will be ordered in reverse order.  
	 * If orderby is an array, the 1st element refers to the field to order by,
	 * and the 2nd, a function that will take two take two parameters A and B 
	 * - two fields from two records - used to do the ordering.  It is expected 
	 * that the function would return -ve if A < B and +ve if A > B, or zero 
	 * if A == B (to order in ascending order).
	 * @param includeindex  if true, an extra field called 'NANODB_IFIELD' will
	 * be added to each record returned. It will contain an int that specifies
	 * the original position in the database (zero based) that the record is 
	 * positioned. It might be useful when an orderby is used, and an future 
	 * operation on a record is required, given it's index in the table.
	 
	 * @return all database records as an array, or Error on failure
	 */
	function getbyfunction( $selectfn, $orderby = null, $includeindex = false )
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );

		// If there are no records, return.
		if ( $this->records == 0 )
			return array();

		if ( PEAR::isError( $res = $this->_lock_read() ) )
			return $res;

		// Read the index.
		$index = $this->_readindex();

		// Read each record and add it to an array.
		$rcount = 0;
		foreach ( $index as $offset )
		{
			// Read the record.
			list( $record, $rsize ) = $this->_readrecord( $this->data_fp, $offset );

			// Add it to the result if the $selectfn OK's it.
			if ( $selectfn( $record ) == true )
			{
				// Add the index field if required.
				if ( $includeindex )
					$record[NANODB_IFIELD] = $rcount;

				$result[] = $record;
			}

			++$rcount;
		}

		$this->_unlock();

		// Re-order as required.
		if ( $orderby !== null )
			return $this->_orderby( $result, $orderby );
		else
			return $result;
	}

	/**
	 * Retrieves all records in the database, each record in an array
	 * element.
	 *
	 * @param orderby  order the results. Set to the field name to order by
	 * (as a string). If left unset, sorting is not done and it is a lot faster.
	 * If prefixed by "!", results will be ordered in reverse order.  
	 * If orderby is an array, the 1st element refers to the field to order by,
	 * and the 2nd, a function that will take two take two parameters A and B 
	 * - two fields from two records - used to do the ordering. It is expected 
	 * that the function would return -ve if A < B and +ve if A > B, or zero 
	 * if A == B (to order in ascending order).
	 * @param includeindex  if true, an extra field called 'NANODB_IFIELD' will
	 * be added to each record returned. It will contain an int that specifies
	 * the original position in the database (zero based) that the record is 
	 * positioned. It might be useful when an orderby is used, and an future 
	 * operation on a record is required, given it's index in the table.
	 *
	 * @return all database records as an array, Error on failure
	 */
	function getall( $orderby = null, $includeindex = false )
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );

		// If there are no records, return.
		if ( $this->records == 0 )
			return array();

		if ( PEAR::isError( $res = $this->_lock_read() ) )
			return $res;

		// Read the index.
		$index = $this->_readindex();

		// Read each record and add it to an array.
		$rcount = 0;
		foreach ( $index as $offset )
		{
			// Read the record.
			list( $record, $rsize ) = $this->_readrecord( $this->data_fp, $offset );

			// Add the index field if required.
			if ( $includeindex )
				$record[NANODB_IFIELD] = $rcount++;

			// Add it to the result.
			$result[] = $record;
		}

		$this->_unlock();

		// Re-order as required.
		if ( $orderby !== null )
			return $this->_orderby( $result, $orderby );
		else
			return $result;
	}

	/**
	 * Retrieves all keys in the database, each in an array.
	 *
	 * @return all database record keys as an array, in order, or Error/false
	 * if the database does not use keys.
	 */
	function getkeys()
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );

		// If there is no key, return false.
		if ( $this->primary_key == "" )
			return false;

		// If there are no records, return.
		if ( $this->records == 0 )
			return array();

		if ( PEAR::isError( $res = $this->_lock_read() ) )
			return $res;

		// Read the index.
		$index = $this->_readindex();

		// Read each record key and add it to an array.
		foreach ( $index as $offset )
		{
			// Read the record key and add it to the result.
			$records[] = $this->_readrecordkey( $this->data_fp, $offset );
		}

		$this->_unlock();
		return $records;
	}

	/**
	 * Searches the database for an item, and returns true 
	 * if found, Error/false otherwise.
	 *
	 * @param key  primary key of record to search for, or the record
	 * number (zero based) for databases without a primary key.
	 * @return true if found, Error/false otherwise
	 */
	function exists($key)
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );

		// Assume we won't find it until proven otherwise.
		$result = false;

		if ( PEAR::isError( $res = $this->_lock_read() ) )
			return $res;

		// Read the index.
		$index = $this->_readindex();
		
		if ( $this->primary_key != "" )
		{
			// Do a binary search to find the item.
			$pos = $this->_bsearch( $index, 0, $this->records-1, $key );

			if ( $pos > 0 )
			{
				// Found!
				$result = true;
			}
		}
		else
		{
			// Ensure the record number is an int.
			if ( !is_int( $key ) )
			{
				$this->_unlock();
				return PEAR::raiseError( "Invalid record number ($key)." );
			}

			// Ensure the record number is within range.
			if ( ( $key < 0 ) || ( $key > $this->records - 1 ) )
			{
				$this->_unlock();
				return PEAR::raiseError( "Invalid record number ($key)." );
			}

			// ... must be found!
			$result = true;
		}
		
		$this->_unlock();
		return $result;
	}

	/**
	 * Returns the number of records in the database.
	 *
	 * @return int - the number of records in the database
	 */
	function size()
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );
		
		return $this->records;
	}

	/**
	 * Returns the number of dirty records in the database,
	 * that would be removed if cleanup() is called.
	 *
	 * @return int - the number of dirty records in the database
	 */
	function sizedirty()
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );
		
		return $this->deleted;
	}

	/**
	 * Returns the current database schema in the same form
	 * as that used in the parameter for the create(...) method.
	 *
	 * @return array - the database schema in the format used for the 
	 * create(...) method, or false if failure.
	 */
	function schema()
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );

		// Reconstruct the schema array.
		$result = array();
		foreach ( $this->fields as $key => $type )
		{
			$item = array( $key, $type );

			if ( $type == NANODB_INT_AUTOINC )
				array_push( $item, $this->autoinc[$key] );

			if ( $key == $this->primary_key )
				array_push( $item, "key" );

			array_push( $result, $item );
		}

		return $result;
	}

	/**
	 * Adds a field to the database. Note that this is a fairly
	 * expensive operation as the database has to be rebuilt.
	 * WARNING: Do not call this method unless you are sure that no other
	 * people are using the database at the same time. This will cause their
	 * PHP scripts to fail. NanoDB does not check to see if the database schema
	 * has been changed while in use (too much overhead).
	 * @param name  name of the new field -- must not already exist
	 * @param type  type of the new field (NANODB_INT, NANODB_INT_AUTOINC, 
	 * NANODB_STRING, NANODB_ARRAY, NANODB_FLOAT, NANODB_BOOL)
	 *
	 * @param default  default value for new field in all entries
	 * @param iskey  true if the new field is to become the new primary key,
	 * false otherwise. Not that this can only be TRUE if the database
	 * is empty, otherwise we will have multiple records with the same (default)
	 * key, which would make the database invalid. The primary key cannot be
	 * an array or boolean type.
	 * @return bool - true on success, false on failure
	 */
	function addfield( $name, $type, $default, $iskey = false )
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );

		// Only allow keys if the database has no records.
		if ( $iskey && ( $this->records > 0 ) )
			return false;

		// Make sure the name of the field is a string and it is unique.
		if ( !is_string( $name ) )
			return false;

		foreach ( $this->fields as $key => $value )
		{
			if ( $name == $key )
				return false;
		}

		// Make sure that the array or boolean value is NOT the key.
		if ( $iskey && ( ( $type == NANODB_ARRAY ) || ( $type == NANODB_BOOL ) ) )
			return false;

		// Make sure the field type is one of our constants.
		if ( !is_int( $type ) )
			return false;

		switch ( $type )
		{
			case NANODB_INT_AUTOINC:
				// We use the default value as the starting value of the auto-inc.
				if ( !is_int( $default ) && ( $this->records > 0 ) )
					return PEAR::raiseError( "Invalid starting value for autoinc int." );
				
				break;

			case NANODB_INT:
				if ( !is_int( $default ) && ( $this->records > 0 ) )
					return PEAR::raiseError( "Invalid default value for int." );
				
				break;

			case NANODB_STRING:
				if ( !is_string( $default ) && ( $this->records > 0 ) )
					return PEAR::raiseError( "Invalid default value for string." );
				
				break;

			case NANODB_ARRAY:
				if ( !is_array( $default ) && ( $this->records > 0 ) )
					return PEAR::raiseError( "Invalid default value for array." );
				
				break;

			case NANODB_FLOAT:
				if ( !is_float( $default ) && ( $this->records > 0 ) )
					return PEAR::raiseError( "Invalid default value for float." );
					
				break;

			case NANODB_BOOL:
				if ( !is_bool( $default ) && ( $this->records > 0 ) )
					return PEAR::raiseError( "Invalid default value for bool." );

				break;
			
			default:
				// Unknown type...!
				return PEAR::raiseError( "Invalid type in field (found $type)." );
		}

		if ( PEAR::isError( $res = $this->_lock_write() ) )
			return $res;

		// Note that we attempt the file creation under the DB lock, so
		// that another process doesn't try to create the same file at the
		// same time.
		$tmpdb = @fopen( $this->dbname . ".tmp", "wb+" );
		
		if ( !$tmpdb )
		{
			$this->_unlock();
			return false;
		}

		// Add the field to the schema.
		$this->fields[$name] = $type;

		if ( $type == NANODB_INT_AUTOINC )
			$this->autoinc[$name] = $default;

		// Do we have a new primary key?
		if ( $iskey )
			$this->primary_key = $name;

		// Read in the current index.
		$index = $this->_readindex();

		// Now translate the data file. For each index entry, read in
		// the record, add in the new default value, then write it back
		// out to a new temporary file. Then move that temporary file
		// back over the old data file.

		// For each item in the index, move it from the current database
		// file to the new one. Also update the new file offset in the index
		// so we can write it back out to the index file.
		for ( $i = 0; $i < $this->records; ++$i )
		{
			$offset = $index[$i];

			// Read in the entire record.
			unset( $record );
			list( $record, $rsize ) = $this->_readrecord( $this->data_fp, $offset );

			// Save the new file offset.
			$index[$i] = ftell( $tmpdb );

			// Add in the new field to the record.
			if ( $type == NANODB_INT_AUTOINC )
				$record[$name] = $this->autoinc[$name]++;
			else
				$record[$name] = $default;

			// Write out the record to the temporary file.
			if ( !$this->_writerecord( $tmpdb, $record ) ) 
			{
				// Error writing item to the database.
				fclose( $tmpdb );
				@unlink( $this->dbname . ".tmp" );
				$this->_unlock();
				
				return false;
			}
		}

		// Move the temporary file over the original database file.
		fclose( $tmpdb );
		fclose( $this->data_fp );
		@unlink( $this->dbname . ".data" );
		@rename( $this->dbname . ".tmp", $this->dbname.".data" );

		// Since we've effectively done a cleanup(), set the number 
		// of (unclean) deleted items to zero.
		$this->deleted = 0;

		// Write the new schema to the meta data file.
		$this->_writeschema();

		// Write out the index (which may have been overwritten).
		$this->_writeindex( $index );

		// Re-open the database data file.
		$this->data_fp = @fopen( $this->dbname . ".data", "rb+" );
		
		if ( !$this->data_fp ) 
		{
			$this->_unlock();
			return false;
		}

		return $this->_unlock();
	}

	/**
	 * Removes a field from the database. Note that this is a fairly
	 * expensive operation as the database has to be rebuilt.
	 * WARNING: Do not call this method unless you are sure that no other
	 * people are using the database at the same time. This will cause their
	 * PHP scripts to fail. NanoDB does not check to see if the database schema
	 * has been changed while in use (too much overhead).
	 *
	 * @param fieldname  name of the field to delete -- must currently exist
	 * @return bool - true on success, Error/false on failure
	 */
	function removefield( $fieldname )
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );

		// Make sure the name of the field is a string and it exists.
		if ( !is_string( $fieldname ) )
			return false;

		if ( !array_key_exists( $fieldname, $this->fields ) )
			return false;

		if ( PEAR::isError( $res = $this->_lock_write() ) )
			return $res;

		// Note that we attempt the file creation under the DB lock, so
		// that another process doesn't try to create the same file at the
		// same time.
		$tmpdb = @fopen( $this->dbname . ".tmp", "wb+" );

		if ( !$tmpdb )
		{
			$this->_unlock();
			return false;
		}

		// Save a copy of the field list. It is needed in the main
		// loop to play with the fields[] array and {read, write}_record(...)
		$oldfields = $this->fields;

		// Read in the current index.
		$index = $this->_readindex();

		// Now translate the data file. For each index entry, read in
		// the record, remove the deleted field, then write it back
		// out to a new temporary file. Then move that temporary file
		// back over the old data file.

		// For each item in the index, move it from the current database
		// file to the new one. Also update the new file offset in the index
		// so we can write it back out to the index file.
		if ( count( $this->fields ) > 1 )
		{
			for ( $i = 0; $i < $this->records; ++$i )
			{
				// Use the original fields[] array so _readrecord(...) will 
				// operate correctly for the old-format database .data file.
				$this->fields = $oldfields;

				// Read in the entire record.
				unset( $record );
				list( $record, $rsize ) = $this->_readrecord( $this->data_fp, $index[$i] );
				
				if ( $record === false )
				{
					// Error reading item from the database.
					fclose( $tmpdb );
					@unlink( $this->dbname . ".tmp" ); // $this->$dbname ??;
					$this->_unlock();
					
					return false;
				}

				// Save the new file offset.
				$index[$i] = ftell( $tmpdb );

				// Remove the field from the record.
				unset( $record[$fieldname] );

				// Make sure the field[] array DOES NOT include the original field,
				// so _writerecord(...) will operate correctly and write out a 
				// new-stlye database .data file (without the deleted field).
				unset( $this->fields[$fieldname] );

				// Write out the record to the temporary file.
				if ( !$this->_writerecord( $tmpdb, $record ) ) 
				{
					// Error writing item to the database.
					fclose( $tmpdb );
					@unlink( $this->dbname . ".tmp" );
					$this->_unlock();
					
					return false;
				}
			}
		}
		else
		{
			// We have deleted the last field, so 
			// there are essentially no records left.
			$this->records = 0;
		}
		
		// Remove the field to the schema.
		unset( $this->fields[$fieldname] );
		
		if ( isset( $this->autoinc[$fieldname] ) )
			unset( $this->autoinc[$fieldname] );

		// Is the field to be removed the primary key?
		if ( $this->primary_key == $fieldname )
			$this->primary_key = "";

		// Move the temporary file over the original database file.
		fclose( $tmpdb );
		fclose( $this->data_fp );
		
		@unlink( $this->dbname . ".data" );
		@rename( $this->dbname . ".tmp", $this->dbname.".data" );

		// Since we've effectively done a cleanup(), set the number 
		// of (unclean) deleted items to zero.
		$this->deleted = 0;

		// Write the new schema to the meta data file.
		$this->_writeschema();

		// Write out the index.
		$this->_writeindex( $index );

		// Re-open the database data file.
		$this->data_fp = @fopen( $this->dbname . ".data", "rb+" );
		
		if ( !$this->data_fp ) 
		{
			$this->_unlock();
			return false;
		}

		return $this->_unlock();
	}
	
	
	// static methods
	
	/**
	 * @static
	 */
	function str2int( $str )
	{
   		if ( function_exists( "sscanf" ) )
   		{
      		list( $result ) = sscanf( $str, "%d" );
   		}
   		else
   		{
			$str = trim( $str );
			$len = strlen( $str );
			$exp = $len - 1;
			$result = 0;
			
			for ( $i = 0; $i < $len; ++$i )
      		{
         		$val = ord( $str{$i} ) - ord( "0" );
         		$result += $val * (int)pow( 10, $exp-- );
      		}
   		}

   		return $result;
	}

	/**
	 * @static
	 */	
	function str2float( $str )
	{
   		if ( function_exists( "sscanf" ) )
   		{
      		list( $result ) = sscanf( $str, "%f" );
   		}
   		else
   		{
      		// Do the real part first
      		$pos  = strchr( $str, "." );
      		$real = substr( $str, 0, $pos );
      		echo "real = $real\n";
      		$fraction = substr( $str, $pos );
      		echo "fraction = $fraction\n";

      		$real = NanoDB::str2int( $real );
      		$fraction = NanoDB::str2int( $fraction ) / pow( 10, strlen( $fraction ) ); 

      		$result = $real + $fraction;
   		}

   		return $result;
	}
		
	
	// private methods

	/**
	 * Lock the database for a write.
	 *
	 * @param force  force the lock to stick until unlocked by force
	 * @access private
	 */
	function _lock_write( $force = false )
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );

		// If the DB is locked (for writing), don't bother locking again.
		if ( ( $this->locked == 2 ) || ( $this->forcelock == 2 ) )
			return true;

		if ( $force )
			$this->forcelock = 2;

		// Lock the index file.
		$this->locked = 2;

		if ( !flock( $this->meta_fp, $this->locked ) )
			return PEAR::raiseError( "Could not (write) lock database " . "'" . $this->dbname . "'." );

		return true;
	}

	/**
	 * Lock the database for a read.
	 *
	 * @param force force the lock to stick until unlocked by force
	 * @access private
	 */
	function _lock_read( $force = false )
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );
		
		// If the DB is locked (for reading), don't bother locking again.
		if ( ( $this->locked == 1 ) || ( $this->forcelock == 1 ) )
			return true;

		if ( $force )
			$this->forcelock = 1;

		// Lock the index file.
		$this->locked = 1;

		if ( !flock( $this->meta_fp, $this->locked ) )
			return PEAR::raiseError( "Could not (read) lock database " . "'" . $this->dbname . "'." );

		return true;
	}

	/**
	 * Unlock the database.
	 *
	 * @param force unlock a previously forced (sticky) lock
	 * @access private
	 */
	function _unlock( $force = false )
	{
		if ( !$this->isopen )
			return PEAR::raiseError( "Database not open." );
			
		if ( $force )
			$this->forcelock = 0;
		
		// If the DB is unlocked, don't bother unlocking again.
		if ( ( $this->locked == 0 ) || ( $this->forcelock != 0 ) )
			return true;

		// Flush all data to the file before unlocking.
		fflush( $this->meta_fp );
		fflush( $this->data_fp );

		// Unlock the index file.
		$this->locked = 0;

		if ( !flock( $this->meta_fp, 3 ) )
			return false;

		return true;
	}

	/**
	 * Reads an int (4 bytes) from a file.
	 *
	 * @param fp  file pointer - pointer to an open file
	 * @return the read int
	 * @access private
	 */
	function _read_int( $fp )
	{
		return $this->_bin2dec( fread( $fp, 4 ), 4 );
	}

	/**
	 * Reads a byte from a file.
	 *
	 * @param fp  file pointer - pointer to an open file
	 * @return the read byte as an int
	 * @access private
	 */
	function _read_byte( $fp )
	{
		return $this->_bin2dec( fread( $fp, 1 ), 1 );
	}

	/**
	 * Reads a float (6 bytes) from a file.
	 *
	 * @param fp  file pointer - pointer to an open file
	 * @return the read float
	 * @access private
	 */
	function _read_float( $fp )
	{
		return $this->_bin2float( fread( $fp, 6 ) );
	}

	/**
	 * Reads a string from a file.
	 *
	 * @param fp  file pointer - pointer to an open file
	 * @return the read string
	 * @access private
	 */
	function _read_str( $fp )
	{
		$strlen = $this->_bin2dec( fread( $fp, 4 ), 4 );
		return fread( $fp, $strlen );
	}

	/**
	 * @abstract Writes an int (4 bytes) to a file
	 * @param fp  file pointer - pointer to an open file
	 * @param num  int - the int to write
	 * @access private
	 */
	function _write_int( $fp, $num )
	{
		fwrite( $fp, $this->_dec2bin( $num, 4 ), 4 );
	}

	/**
	 * Writes a byte to a file.
	 *
	 * @param fp  file pointer - pointer to an open file
	 * @param num  int - the byte to write
	 * @access private
	 */
	function _write_byte( $fp, $num )
	{
		fwrite( $fp, $this->_dec2bin( $num & 0xFF, 1 ), 1 );
	}

	/**
	 * Writes a float (6 bytes) to a file.
	 *
	 * @param fp  file pointer - pointer to an open file
	 * @param num  float - the float to write
	 * @access private
	 */
	function _write_float( $fp, $num )
	{
		fwrite( $fp, $this->_float2bin( $num ), 6 );
	}

	/**
	 * Writes a string to a file.
	 *
	 * @param fp  file pointer - pointer to an open file
	 * @param str  string - the string to write
	 * @access private
	 */
	function _write_str( $fp, &$str )
	{
		$len = strlen( $str );
		fwrite( $fp, $this->_dec2bin( $len, 4 ), 4 );
		fwrite( $fp, $str, $len );
	}

	/**
	 * Converts an int to a binary string, low byte first.
	 *
	 * @param num  int - number to convert
	 * @param bytes  int - minimum number of bytes to covert to
	 * @return the binary string form of the number
	 * @access private
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
	 * Converts a binary string to an int, low byte first.
	 *
	 * @param str  string - binary string to convert
	 * @param len  int - length of the binary string to convert
	 * @return the int version of the binary string
	 * @access private
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

	/**
	 * Converts a single-precision floating point number
	 * to a 6 byte binary string.
	 *
	 * @param num  float - the float to convert
	 * @return the binary string representing the float
	 * @access private
	 */
	function _float2bin( $num )
	{
		// Save the sign bit.
		$sign = ( $num < 0 )? 0x8000 : 0x0000;

		// Now treat the number as positive...
		if ( $num < 0 ) 
			$num = -$num;

		// Get the exponent and limit to 15 bits.
		$exponent = ( 1 + (int)floor( log10( $num ) ) ) & 0x7FFF;

		// Convert the number into a fraction.
		$num /= pow( 10, $exponent );

		// Now convert the fraction to a 31bit int.
		// We don't use the full 32bits, because the -ve numbers
		// stuff us up -- this results in better than single
		// precision floats, but not as good as double precision.
		$fraction = (int)floor( $num * 0x7FFFFFFF );

		// Pack the number into a 6 byte binary string
		return $this->_dec2bin( $sign | $exponent, 2 ) . $this->_dec2bin( $fraction, 4 );
	}

	/**
	 * Converts a 6 byte binary string to a single-precision
	 * floating point number.
	 *
	 * @param data  string - the binary string to convert
	 * @return the floating point number
	 * @access private
	 */
	function _bin2float( &$data )
	{
		// Extract the sign bit and exponent.
		$exponent  = $this->_bin2dec( substr( $data, 0, 2 ), 2 );
		$sign      = ( ( $exponent & 0x8000 ) == 0 )? 1 : -1;
		$exponent &= 0x7FFF;

		// Extract the fractional part.
		$fraction = $this->_bin2dec( substr( $data, 2, 4 ), 4 );

		// Return the reconstructed float.
		return $sign * pow( 10, $exponent ) * $fraction / 0x7FFFFFFF;
	}

	/**
	 * Writes out a data type to a file. Note that arrays can only 
	 * consist of other arrays, ints, and strings.
	 *
	 * @param fp  file pointer - pointer to data file
	 * @param type  data type to write
	 * @param data  actual data to write
	 * @return bool - true on success, false on failure
	 * @access private
	 */
	function _write_item( $fp, $type, &$data )
	{
		switch ( $type )
		{
			case NANODB_INT:
			
			case NANODB_INT_AUTOINC:
				$this->_write_int( $fp, $data );
				break;

			case NANODB_STRING:
				$this->_write_str( $fp, $data );
				break;

			case NANODB_FLOAT:
				$this->_write_float( $fp, $data );
				break;

			case NANODB_BOOL:
				$this->_write_byte( $fp, ( $data == true )? 1 : 0 );
				break;

			case NANODB_ARRAY:
				$this->_write_int( $fp, count( $data ) );
				
				foreach ( $data as $k => $d )
				{
					// Write the array key.
					if ( is_int( $k ) )
					{
						$this->_write_byte( $fp, NANODB_INT );
						$this->_write_int( $fp, $k );
					}
					else if ( is_string( $k ) )
					{
						$this->_write_byte( $fp, NANODB_STRING );
						$this->_write_str( $fp, $k );
					}
					else if ( is_float( $k ) )
					{
						$this->_write_byte( $fp, NANODB_FLOAT );
						$this->_write_float( $fp, $k );
					}
					else if ( is_bool( $k ) )
					{
						$this->_write_byte( $fp, NANODB_BOOL );
						$this->_write_byte( $fp, ( $k == true )? 1 : 0 );
					}
					else
					{
						// Error in type.
						return PEAR::raiseError( "Invalid array key data type ($k)." );
					}

					// Write the array data.
					if ( is_int( $d ) )
					{
						$this->_write_byte( $fp, NANODB_INT );
						$this->_write_int( $fp, $d );
					}
					else if ( is_string( $d ) )
					{
						$this->_write_byte( $fp, NANODB_STRING );
						$this->_write_str( $fp, $d );
					}
					else if ( is_float( $d ) )
					{
						$this->_write_byte( $fp, NANODB_FLOAT );
						$this->_write_float( $fp, $d );
					}
					else if ( is_bool( $d ) )
					{
						$this->_write_byte( $fp, NANODB_BOOL );
						$this->_write_byte( $fp, ( $d == true )? 1 : 0 );
					}
					else if ( is_array( $d ) )
					{
						$this->_write_byte( $fp, NANODB_ARRAY );
						
						if ( PEAR::isError( $res = $this->_write_item( $fp, NANODB_ARRAY, $d ) ) );
							return $res;
					}
					else
					{
						// Error in type.
						return PEAR::raiseError( "Invalid array data type ($d)." );
					}
				}
				
				break;
			
			default:
				// Error in type.
				return PEAR::raiseError( "Invalid data type ($type)." );
		}

		return true;
	}

	/**
	 * Reads a data type from a file. Note that arrays can only 
	 * consist of other arrays, ints, and strings.
	 *
	 * @param fp  file pointer - pointer to data file
	 * @param type  data type to read
	 * @return bool - data on success, false on failure
	 * @access private
	 */
	function _read_item( $fp, $type )
	{
		switch ( $type )
		{
			case NANODB_INT:
			
			case NANODB_INT_AUTOINC:
				return $this->_read_int( $fp );

			case NANODB_STRING:
				return $this->_read_str( $fp );

			case NANODB_FLOAT:
				return $this->_read_float( $fp );

			case NANODB_BOOL:
				return ($this->_read_byte( $fp ) == 1 );

			case NANODB_ARRAY:
				$elements = $this->_read_int( $fp );

				for ( $i = 0; $i < $elements; ++$i )
				{
					// Get the array key data type.
					$keytype = $this->_read_byte( $fp );

					switch ( $keytype )
					{
						case NANODB_INT:
							$key = $this->_read_int( $fp );
							break;
							
						case NANODB_STRING:
							$key = $this->_read_str( $fp );
							break;
							
						case NANODB_FLOAT:
							$key = $this->_read_float( $fp );
							break;
							
						case NANODB_BOOL:
							$key = ( $this->_read_byte( $fp ) == 1 );
							break;
					}

					// Get the array data type.
					$datatype = $this->_read_byte( $fp );

					switch ( $datatype )
					{
						case NANODB_INT:
							$data = $this->_read_int( $fp );
							break;
							
						case NANODB_STRING:
							$data = $this->_read_str( $fp );
							break;
							
						case NANODB_FLOAT:
							$data = $this->_read_float( $fp );
							break;
							
						case NANODB_BOOL:
							$data = ( $this->_read_byte( $fp ) == 1 );
							break;
							
						case NANODB_ARRAY:
							$data = $this->_read_item( $fp, NANODB_ARRAY );
							break;
					}

					$result[$key] = $data;
				}
				
				// Preserve null arrays...
				if ( !isset( $result ) )
					$result = array();

				return $result;

			default:
				// Error in type.
				return false;
		}

		return false;
	}

	/**
	 * Returns the size of an item.
	 *
	 * @param type  data type
	 * @param data  actual data to size
	 * @return int - size in bytes
	 * @access private
	 */
	function _item_size( $type, &$data )
	{
		switch ( $type )
		{
			case NANODB_INT:
			
			case NANODB_INT_AUTOINC:
				return 4;
			
			case NANODB_STRING:
				return 4 + strlen( $data );
			
			case NANODB_FLOAT:
				return 6;
			
			case NANODB_BOOL:
				return 1;
			
			case NANODB_ARRAY:
				$size = 0;
				
				foreach ( $data as $k => $d )
				{
					$size += 1;
					
					if ( is_int( $k ) )
					{
						$size += 4;
					}
					else if ( is_string( $k ) )
					{
						$size += 4 + strlen( $k );
					}
					else if ( is_float( $k ) )
					{
						$size += 6;
					}
					else if ( is_bool( $k ) )
					{
						$size += 1;
					}
					else
					{
						// Error in type.
						return false;
					}

					$size += 1;
					
					if ( is_int( $d ) )
					{
						$size += 4;
					}
					else if ( is_string( $d ) )
					{
						$size += 4 + strlen( $d );
					}
					else if ( is_float( $d ) )
					{
						$size += 6;
					}
					else if ( is_bool( $d ) )
					{
						$size += 1;
					}
					else if ( is_array( $d ) )
					{
						$size += $this->_item_size( NANODB_ARRAY, $d );
					}
					else
					{
						// Error in type
						return false;
					}
				}
				
				return $size;
		}			

		// Error in type
		return false;
	}
	
	/**
	 * Sort a result set by a particular field.
	 *
	 * @param result  the result list to order
	 * @param orderby  order the results. Set to the field name to order by
	 * (as a string). If left unset, sorting is not done and it is a lot faster.
	 * If prefixed by "!", results will be ordered in reverse order.  
	 * If orderby is an array, the 1st element refers to the field to order by,
	 * and the 2nd, a function that will take two take two parameters A and B 
	 * - two fields from two records - used to do the ordering. It is expected 
	 * that the function would return -ve if A < B and +ve if A > B, or zero 
	 * if A == B (to order in ascending order).
	 * @return array - input results ordered as required, or false on error.
	 * @access private
	 */
	function _orderby( &$result, $orderby )
	{
		$record_count = count( $result );

		// Do we want reverse sort?
		$rev_sort = is_string( $orderby ) && ( strlen( $orderby ) > 0 ) && ( $orderby[0] == "!" );

		// Do we want to use a function?
		$use_funct = ( is_array( $orderby ) );

		// Remove the control code(s) from the order by field.
		if ( $rev_sort )
			$orderby = substr( $orderby, 1 );

		if ( $use_funct )
		{
			$funct   = $orderby[1];
			$orderby = $orderby[0];
		}

		// Check the order by field name.
		if ( !array_key_exists( $orderby, $this->fields ) )
			return PEAR::raiseError( "Invalid orderby field name ($orderby)." );
		
		if ( $this->fields[$orderby] == NANODB_ARRAY )		
			return PEAR::raiseError( "Cannot orderby on an array field ($orderby)." );		
		
		if ( $use_funct && !function_exists( $funct ) )
			return PEAR::raiseError( "Invalid orderby user function ($funct)." );	

		// Construct an array that points into our list
		// We use an array of indices into $result, because there might
		// be more than one record with a given sortby-field.
		$sorted = array();
		
		for ( $i = 0; $i < $record_count; ++$i )
		{
			$key = &$result[$i][$orderby];
			$sorted[$key][] = $i;
		}

		// Sort the array.
		if ( $rev_sort )
			krsort( $sorted );			// Reverse (decending) sort
		else if ( $use_funct )
			uksort( $sorted, $funct );	// User function sort
		else
			ksort( $sorted );			// Regular (ascending) sort

		// Rearrange the items to form the result. Unfortunately
		// because we will return the array, we can't use references,
		// and we end up having to copy all records across to a new array.
		foreach( $sorted as $ilist )
		{
			foreach ( $ilist as $index )
				$sresult[] = $result[$index];
		}
		
		return $sresult;
	}

	/**
	 * Read the database schema and other meta information. 
	 * We assume the database has been locked before calling this function.
	 *
	 * @access private
	 */
	function _readschema()
	{
		fseek( $this->meta_fp, NANODB_INDEX_RECORDS_OFFSET, SEEK_SET );

		// Read the database statistics from the meta file.
		//
		// Statistics format:
		//	 [number of valid records: int]
		//	 [number of (unclean) deleted records: int]

		$this->records = $this->_read_int( $this->meta_fp );
		$this->deleted = $this->_read_int( $this->meta_fp );

		// Read the schema from the meta file.
		//
		// Schema format:
		//	[primary key field name]
		//	[number of fields]
		//	  [field 1: name]
		//	  [field 1: type]
		//	  ...
		//	  [field n: name]
		//	  [field n: type]
		//
		// For auto-incrementing fields, there is an extra int specifying
		// the last value used in the last record added.
		
		$this->primary_key = $this->_read_str( $this->meta_fp );
		$field_count = $this->_read_int( $this->meta_fp );

		$this->fields  = array();
		$this->autoinc = array();
		
		for ( $i = 0; $i < $field_count; ++$i )
		{
			// Read the fields in.
			$name = $this->_read_str( $this->meta_fp );
			$type = $this->_read_byte( $this->meta_fp );
			$this->fields[$name] = $type;

			if ( $type == NANODB_INT_AUTOINC )
				$this->autoinc[$name] = $this->_read_int( $this->meta_fp );
		}
		
		if ( $field_count == 0 )
		{
			$this->fields  = array();
			$this->autoinc = array();
		}

		// Save where the index starts in the meta file.
		$this->index_start = ftell( $this->meta_fp );		
	}

	/**
	 * Write the database schema and other meta information.  
	 * We assume the database has been locked before calling this function.
	 *
	 * @access private
	 */
	function _writeschema()
	{
		fseek( $this->meta_fp, NANODB_INDEX_RECORDS_OFFSET, SEEK_SET );

		// Write the database statistics information
		//
		// Statistics format:
		//	 [number of valid records: int]
		//	 [number of (unclean) deleted records: int]

		$this->_write_int( $this->meta_fp, $this->records );
		$this->_write_int( $this->meta_fp, $this->deleted );

		// Write the schema from the meta file.
		//
		// Schema format:
		//	[primary key field name]
		//	[number of fields]
		//	  [field 1: name]
		//	  [field 1: type]
		//	  ...
		//	  [field n: name]
		//	  [field n: type]
		//
		// For auto-incrementing fields, there is an extra int specifying
		// the last value used in the last record added.
		
		$this->_write_str( $this->meta_fp, $this->primary_key );
		$this->_write_int( $this->meta_fp, count( $this->fields ) );

		// Write the key entry first, always.
		if ( $this->primary_key != "" )
		{
			$this->_write_str( $this->meta_fp, $this->primary_key ); 
			$this->_write_byte( $this->meta_fp, $this->fields[$this->primary_key] );

			if ( $this->fields[$this->primary_key] == NANODB_INT_AUTOINC )
				$this->_write_int( $this->meta_fp, $this->autoinc[$this->primary_key] );
		}

		// Write out all of the other entries.
		foreach ( $this->fields as $name => $type )
		{
			if ( $name != $this->primary_key )
			{
				$this->_write_str( $this->meta_fp, $name );
				$this->_write_byte( $this->meta_fp, $type );

				if ( $type == NANODB_INT_AUTOINC )
					$this->_write_int( $this->meta_fp, $this->autoinc[$name] );	
			}
		}

		$this->index_start = ftell( $this->meta_fp );
	}

	/**
	 * Return the index values. We assume the
	 * database has been locked before calling this function.
	 *
	 * @return array - list of file offsets into the .data file
	 * @access private
	 */
	function _readindex()
	{
		fseek( $this->meta_fp, $this->index_start, SEEK_SET );

		// Read in the index.
		$index = array();
		
		for ( $i = 0; $i < $this->records; ++$i )
			$index[] = $this->_read_int( $this->meta_fp );

		return $index;
	}

	/**
	 * Write the index values. We assume the
	 * database has been locked before calling this function.
	 *
	 * @param index  the index *data* to write out
	 * @access private
	 */
	function _writeindex( &$index )
	{
		fseek( $this->meta_fp, $this->index_start, SEEK_SET );
		ftruncate( $this->meta_fp, $this->index_start );

		for ( $i = 0; $i < $this->records; ++$i )
			$this->_write_int( $this->meta_fp, $index[$i] );
	}

	/**
	 * Read a record from the database.
	 *
	 * @param fp  the file pointer used to read a record from
	 * @param offset  file offset into the .data file
	 * @return Returns false on error, or the record otherwise
	 * @access private
	 */
	function _readrecord( $fp, $offset )
	{
		// Read in the record at the given offset.
		fseek( $fp, $offset, SEEK_SET );

		// Read in the size of the block allocated for the record.
		$size = $this->_read_int( $fp );

		// Read in the entire record.
		foreach ( $this->fields as $item => $datatype )
			$record[$item] = $this->_read_item( $fp, $datatype );
		
		return array( $record, $size );
	}

	/**
	 * Read a record KEY from the database. Note
	 * that this function relies on the fact that they key is ALWAYS the first
	 * item in the database record as stored on disk.
	 *
	 * @param fp  the file pointer used to read a record from
	 * @param offset  file offset into the .data file
	 * @return Returns false on error, or the key otherwise
	 * @access private
	 */
	function _readrecordkey( $fp, $offset )
	{
		// Read in the record at the given offset.
		fseek( $fp, $offset + NANODB_INDEX_RBLOCK_SIZE, SEEK_SET );

		// Read in the record KEY only.
		return $this->_read_item( $fp, $this->fields[$this->primary_key] );
	}

	/**
	 * Write a record to the END of the .data file.
	 *
	 * @param fp  the file pointer used to write a record to.
	 * @param record  the record to write
	 * @param size  the size of the record.  
	 * @param atoffset  the offset to write to, or -1 for the current position
	 * @return Returns false on error, true otherwise
	 * @access private
	 */
	function _writerecord( $fp, &$record, $size = -1 )
	{
		// Auto-calculate the record size.
		if ( $size < 0 )
			$size = $this->_recordsize( $record );

		// Write out the size of the record.
		$this->_write_int( $fp, $size );

		// Write out the entire record.
		foreach ( $this->fields as $item => $datatype )
		{
			if ( PEAR::isError( $this->_write_item( $fp, $datatype, $record[$item] ) ) )
				return false;
		}
		
		return true;
	}

	/**
	 * Determine the size (bytes) of a record.
	 *
	 * @param record  the record to investigate
	 * @return int - the size of the record
	 * @access private
	 */
	function _recordsize( &$record )
	{
		$size = 0;

		// Size up each field.
		foreach ( $this->fields as $item => $datatype )
			$size += $this->_item_size( $datatype, $record[$item] );
		
		return $size;
	}

	/**
	 * Perform a binary search.
	 *
	 * @param index  file offsets into the .data file, it must be ordered 
	 * by primary key.
	 * @param left  the left most index to start searching from
	 * @param right  the right most index to start searching from
	 * @param target  the search target we're looking for
	 * @return Returns -[insert pos+1] when not found, or the array index+1 
	 * when found. Note that we don't return the normal position, because we 
	 * can't differentiate between -0 and +0.
	 * @access private
	 */
	function _bsearch( &$index, $left, $right, &$target )
	{
		while ( $left <= $right )
		{
			$middle = (int)( ( $left + $right ) / 2 );
		  
			// Read in the record key at the given offset.
			$key = $this->_readrecordkey( $this->data_fp, $index[$middle] );

			if ( ( $left == $right ) && ( $key != $target ) )
			{
				if ( $target < $key )
					return -( $left + 1 );
				else
					return -( $left + 1 + 1 );
			}
			else if ( $key == $target )
			{
				// Found!
				return $middle + 1;
			}
			else if ( $target < $key )
			{
				// Try the left side.
				$right = $middle - 1;
			}
			else /* $target > $key */
			{
				// Try the right side.
				$left = $middle + 1;
			}
		}

		// Not found: return the insert position (as negative).
		return -( $middle + 1 );
	}
} // END OF NanoDB

?>
