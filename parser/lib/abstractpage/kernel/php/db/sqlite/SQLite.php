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
 * SQLite Class (tested on PHP 4.3.3 (Win XP))
 *
 * @since PHP 4.3.3 (SQLite PECL extension)
 *
 * Usage:
 *
 * // set a path for a dabase file
 * $path = 'C:/AppServ/www/';
 * 
 * // create the object and connect to the database
 * $sqlite =& new SQLite($path . 'test.db');
 * 
 * // create table
 * $query =<<<QRY
 * CREATE TABLE test(
 *     id INTEGER PRIMARY KEY,
 *     name VARCHAR(25),
 *     quantity INTEGER,
 *     price NUMERIC(5,2)
 * );
 * QRY;
 * 
 * $sqlite->query( $query );
 * 
 * // insert data
 * $queries = array(
 * 		"INSERT INTO test ( name, quantity, price ) VALUES( 'toro', 10, 500.00 );",
 * 		"INSERT INTO test ( name, quantity, price ) VALUES( 'gallo', 5, 200.00 );",
 * 		"INSERT INTO test ( name, quantity, price ) VALUES( 'rana', 20, 100.00 );",
 * 		"INSERT INTO test ( name, quantity, price ) VALUES( 'cane',  3, 500.00 );"
 * );
 * 
 * foreach ( $queries as $query )
 *     $sqlite->query( $query );
 * 
 * // create a query
 * $query = 'SELECT * FROM test';
 * $sqlite->query( $query );
 * 
 * // return a rowset with column names index
 * $rows = $sqlite->returnRows( 'assoc' );
 * echo '<pre>';
 * print_r( $rows );
 * echo '</pre>';
 * 
 * $sqlite->query( $query );
 * // return a rowset with numeric index
 * $rows = $sqlite->returnRows( 'num' );
 * echo '<pre>';
 * print_r( $rows );
 * echo '</pre>';
 * 
 * // create a transaction 
 * $sqlite->beginTransaction();
 * $sqlite->addQuery( "UPDATE test SET name='castoro' WHERE id = 1;" );
 * $sqlite->addQuery( "UPDATE test SET price=300.00 WHERE id = 2;" );
 * $sqlite->addQuery( "INSERT INTO test VALUES(NULL, 'asino', 1, 1000.00);" );
 * $sqlite->commitTransaction();
 * 
 * // verify the transaction modification
 * $query = 'SELECT name, price FROM test;';
 * // unbuffered query
 * $sqlite->query( $query, false );
 * $rows = $sqlite->returnRows( 'assoc' );
 * echo '<pre>';
 * print_r( $rows );
 * echo '</pre>';
 * 
 * // create a transaction
 * $sqlite->beginTransaction();
 * // escape string
 * $sqlite->addQuery( "UPDATE test SET name='" . $sqlite->escapeString("alan l'è bel") . "' WHERE id = 1;" );
 * $sqlite->addQuery( "UPDATE test SET price=300.00 WHERE id = 2;" );
 * // bad query: id 1 alredy exists - rollback start
 * $sqlite->addQuery( "INSERT INTO test VALUES(1, 'asino', 1, 1000.00);" );
 * $sqlite->commitTransaction();
 * 
 * // verify the transaction modification
 * $query = 'SELECT name, price FROM test;';
 * $sqlite->query( $query, false );
 * $rows = $sqlite->returnRows( 'assoc' );
 * echo '<pre>';
 * print_r( $rows );
 * echo '</pre>';
 * 
 * // delete all data
 * $query = 'DELETE FROM test;';
 * $sqlite->query( $query );
 * 
 * // close SQLite connection
 * $sqlite->close();
 * 
 * // unset the object
 * unset( $sqlite );
 * 
 * // delete the datadase file
 * unlink( $path . 'test.db' );
 *
 * @package db_sqlite
 */

class SQLite extends PEAR
{
    /**
     * The name of database
     *
     * @var     string
     * @access  private
     * @see		SQLite()
     */
	var $_file = '';
	
    /**
     * Resouce of SQLite connection
     *
     * @var     resource
     * @access  private
     * @see		SQLite()
     */
	var $_conn = null;
	
    /**
     * the SQL query 
     *
     * @var     string
     * @access  private
     */
	var $_command = '';
	
    /**
     * The result resource
     *
     * @var     resource
     * @access  private
     */
	var $_result = null;
	
    /**
     * Obtain or not obtain buffer/unbeffered result?
     *
     * @var     bool
     * @access  private
     */
	var $_buffer = true;
	
    /**
     * Type of array index
     *
     * @var     string
     * @access  private
     */
	var $_type = 'num';
	
    /**
     * Array of query
     *
     * @var     array of string (query)
     * @access  private
     */	
	var $_transaction = array();

    /**
     * The state of transaction
     *
     * @var     bool
     * @access  private
     */	
	var $_openTransaction = false;

    /**
     * Last error
     *
     * @var     string
     * @access  private
     */	
	var $_lastError = "";
	
	
    /**
	 * Constructor
	 *
     * Set the properties $file, $persistent and $showMessage.
     * Connect to database.
     *
     * @param    string  $file			filename (the SQLite database)
     * @param    bool	$persistent		true or false
     * @param    bool	$showError		true or false
     * @access	public
     * @return	void
     */	
	function SQLite( $file, $persistent = false )
	{
		$this->_file = $file;

		if ( !$persistent ) 
			$this->_conn = sqlite_open( $this->_file, 0666, $error );
		else 
			$this->_conn = @sqlite_popen( $this->_file, 0666, $error );
		
		if ( !is_resource( $this->_conn ) )
		{
			$this = new PEAR_Error( "Enable to open or create database " . $this->_file );
			return;
		}
	}

	
    /**
     * Submit a SQL query to database.
     *
     * @param	string	$query		query SQL SQLite compatible		
     * @param	bool	$buffer		true or false (If you only need sequential access to
     *								the data, it is recommended false)
     *								If you use false, some function do not work.
     * @access	public
     * @return	bool
     */
	function query( $query, $buffer = true )
	{	
		$this->_command = $query;
		$this->_buffer  = $buffer;
		
		if ( $buffer )
			$this->_result = @sqlite_query( $query, $this->_conn );
		else
			$this->_result = @sqlite_unbuffered_query( $query, $this->_conn );
		
		if ( !$this->_result ) 
		{
			$this->_updateError();
			return false;
		} 
		else 
		{
			// echo $query . '<p>';
			return true;
		}
	}

    /**
     * Get rows.
     *
     * @param  string $type		'assoc' or 'num'
     * @access public
     * @return mixed
     */
	function returnRows( $type = 'assoc' )
	{
		if ( $type == 'assoc' ) 
			$this->_type = SQLITE_ASSOC;
		
		if ( $type == 'num' ) 
			$this->_type = SQLITE_NUM;

		while ( $row = sqlite_fetch_array( $this->_result, $this->_type, true ) )
			$rows[] = $row;
		
		if ( $rows ) 
		{
			return $rows;
		} 
		else 
		{
			$this->_updateError();
			return false;
		}
	}
	
    /**
     * Return the last insert id (column declared INTEGER PRIMARY KEY).
     *
     * @access public
     * @return int
     */
	function lastInsertId()
	{
		return sqlite_last_insert_rowid( $this->_conn );
	}
	
    /**
     * Return how many lines are changed.
     *
     * @access public
     * @return int
     */
	function affectedRows()
	{
		return sqlite_changes( $this->_conn );
	}

    /**
     * Return the number of rows.
     *
     * @access public
     * @return int
     */	
	function numRows()
	{
		if ( $this->_buffer ) 
		{
			return sqlite_num_rows( $this->_result );
		} 
		else 
		{
			$this->_updateError( 'Query unbuffered: numRows() is unavailable' );
			return false;
		}
	}

    /**
     * Start transaction process.
     *
     * @access public
     * @return void
     */
	function beginTransaction()
	{
		$this->_openTransaction = true;
		$this->_transaction     = array();
		$this->_transaction[]   = "BEGIN TRANSACTION;";
	}
   
    /**
     * Finish the transaction process.
     *
     * @param  bool $stop		true or false, if $stop is true 
     * @access public
     * @return void
     */
	function commitTransaction( $stop = false )
	{
		$this->_transaction[] = "COMMIT TRANSACTION;";
		
		foreach ( $this->_transaction as $query ) 
		{
			if ( !$this->query( $query ) ) 
			{
				$this->_rollbackTransaction();
				
				if ( !$stop ) 
				{
					return false;
				} 
				else 
				{
					$this->close();
					return PEAR::raiseError( 'A query as failed - Rollback go!' );
				}
			}
		}
		
		$this->_openTransaction = false;
	}

    /**
     * Add a query to transaction.
     *
     * @param  string $query
     * @access public
     * @return void
     */
	function addQuery( $query )
	{
		if ( $this->_openTransaction )
			$this->_transaction[] = $query;
		else
			$this->_updateError( 'No one transaction is open' );
	}
	
    /**
     * Prepare a string with special characters
     *
     * @param  string $string
     * @access public
     * @return string
     */
	function escapeString( $string )
	{
		return sqlite_escape_string( $string );
	}
	
    /**
     * The encoding of library.
     *
     * @access public
     * @return string
     */
	function libEncoding()
	{
		return sqlite_libencoding();
	}
	
    /**
     * The version of library.
     *
     * @access public
     * @return string
     */
	function libVersion()
	{
		return sqlite_libversion();
	}

    /**
     * Get last error message.
     *
     * @access public
     * @return string
     */
	function getLastError()
	{
		return $this->_lastError;
	}
	
    /**
     * Close a connection to database.
     *
     * @access public
     * @return void
     */
	function close()
	{
		unset( $this->_conn );
	}
	
	
	// private methods
	
    /**
     * If a query fails in a transaction, this method it takes part.
     *
     * @access private
     * @return void
     */
	function _rollbackTransaction()
	{
		$this->query( 'ROLLBACK TRANSACTION' );
	}
	
    /**
     * Print the last error.
     *
     * @param  string $message		the error message
     * @access private
     * @return void
     */
	function _updateError( $message = '' )
	{
		$cause = empty( $message )? $this->_command : $message;
	
		$error_msg = "Error no: " . sqlite_last_error( $this->_conn ) . 
			" - Description: "    . sqlite_error_string( sqlite_last_error( $this->_conn ) ) . 
			" - Possible cause: " . htmlentities( $cause );
		
		$this->_lastError = $error_msg;
	}
} // END OF SQLite

?>
