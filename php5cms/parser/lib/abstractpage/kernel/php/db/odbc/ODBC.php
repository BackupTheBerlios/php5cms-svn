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
 * @package db_odbc
 */
 
class ODBC extends PEAR
{
	/**
	 * @access public
	 */
	var $error_code = 0;
	
	/**
	 * @access public
	 */
	var $error_msg = false;
	
	/**
	 * @access public
	 */
	var $error_source = false;
	
	
	/**
	 * @access public
	 */
	function connect( $dsn, $user, $password ) 
	{ 
    	$ret = @odbc_connect( $dsn, $user, $password ); 
    
		if ( !$ret )
		{ 
    		$this->check_errors( $php_errormsg ); 
    		return false; 
    	}
	
    	return $ret; 
	} 

	/**
	 * Arguments: $conn (int)     - connection identifier 
	 *            $query (string) - SQL statement to execute 
	 * Description: executes an SQL statement 
	 * Returns: (int) 0 - query failed 
	 *                1 - query succeeded
	 *
	 * @access public
	 */
	function query( $conn, $query ) 
	{ 
    	$ret = @odbc_exec( $conn, $query ); 
    
		if ( !$ret )
		{ 
    		$this->check_errors( $php_errormsg );
    		return false; 
    	}
	
    	return $ret; 
	} 

	/**
	 * Arguments: $result (int)   - result identifier 
	 * Description: Returns an array containing data from a fetched row. 
	 * Returns:   false - error 
	 *            (array) - returned row 
	 *
	 * @access public
	 */
	function fetch_row( $result ) 
	{ 
    	$row  = array(); 
    	$cols = @odbc_fetch_into( $result, &$row ); 
    
		if ( !$cols )
		{ 
    		$this->check_errors( $php_errormsg ); 
    		return false; 
    	} 
    
		return $row; 
	} 

	/**
	 * Arguments: $result (int)   - result identifier 
	 * Description: Frees all memory associated with a result identifier. 
	 * Returns: (int) 0 - failure 
	 *                1 - success
	 *
	 * @access public
	 */
	function free_result( $result ) 
	{ 
    	$ret = @odbc_free_result( $result ); 
    	$this->check_errors( $php_errormsg ); 
    
		return $ret; 
	}

	/**
	 * Arguments: $connection (int) - connection identifier 
	 * Description: closes a database connection 
	 * Returns: (int) 0 - failure 
	 *                1 - success
	 *
	 * @access public
	 */
	function disconnect( $connection )
	{ 
    	$ret = @odbc_close( $connection ); 
    	$this->check_errors( $php_errormsg );
	 
    	return $ret; 
	} 

	/**
	 * Arguments: $connection (int) - connection identifier 
	 * Description: turn autocommit on or off 
	 * Returns: (int) 0 - failure 
	 *                1 - success 
	 *
	 * @access public
	 */
	function autocommit( $connection, $enabled ) 
	{ 
    	$ret = @odbc_autocommit( $connection, $enabled ); 
    	$this->check_errors( $php_errormsg ); 
    
		return $ret; 
	} 

	/**
	 * @access public
	 */
	function commit( $connection ) 
	{ 
    	$ret = @odbc_commit( $connection ); 
    	$this->check_errors( $php_errormsg );
	 
    	return $ret; 
	} 

	/**
	 * @access public
	 */
	function rollback( $connection ) 
	{ 
    	$ret = @odbc_rollback( $connection ); 
    	$this->check_errors( $php_errormsg ); 
    
		return $ret; 
	} 

	/**
	 * @access public
	 */
	function quote_string( $string )
	{ 
    	$ret = ereg_replace( "'", "''", $string ); 
    	return $ret; 
	} 

	/**
	 * @access public
	 */
	function prepare( $connection, $query ) 
	{ 
    	$ret = @odbc_prepare( $connection, $query );
    	$this->check_errors( $php_errormsg ); 
    
		return $ret; 
	} 

	/**
	 * @access public
	 */
	function execute( $statement, $data )
	{ 
    	$ret = @odbc_execute( $statement, $data ); 
    	$this->check_errors( $php_errormsg ); 
    
		return $ret; 
	} 

	/**
	 * @access public
	 */
	function error_code() 
	{ 
    	return $this->error_code; 
	} 

	/**
	 * @access public
	 */
	function error_msg() 
	{ 
    	return $this->error_msg; 
	} 

	/**
	 * @access public
	 */
	function check_errors( $errormsg ) 
	{     
		if ( ereg( 'SQL error: (\[.*\]\[.*\]\[.*\])(.*), SQL state (.....)', $errormsg, &$data ) )
		{ 
    		list( $foo, $this->error_source, $this->error_msg, $this->error_code ) = $data; 
    	}
		else
		{ 
    		$this->error_msg  = false; 
    		$this->error_code = 0; 
    	} 
	} 

	/**
	 * @access public
	 */
	function post_error( $code, $message ) 
	{     
		$this->error_code = $code; 
    	$this->error_msg  = $message; 
	} 
} // END OF ODBC

?>
