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
|Authors: Alexander Minkovsky <a_minkovsky@hotmail.com>                |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/



define( "ST_ERR_OPEN_SP_FILE",     100 );
define( "ST_ERR_MYSQL_CONNECT",    101 );


/**
 * Class emulates stored procedure execution.
 *
 * SP is in fact an external file with placeholders for parameters.
 * Each SP statement is execited subsequently. The result of the execute method is an array
 * containing all statements results.
 *
 * An array entry can be an associative array if the statement was SELECT type
 * or INT -> affected rows number. Also accessible through "results" property.
 * For example: $sp->results[count( $sp->results ) - 1] will return the result of the
 * last executed statement (usually the one which returns the results as in the example.)
 *
 * Placeholder Syntax: <param name="..."/>
 *
 * Example:
 * SET @DetailID = <param name="DetailID"/>;
 * SET @myDate = '<param name="myDate"/>';
 * Default value: NULL
 *
 * @package db_mysql
 */
 
Class StoredProcedure extends PEAR
{
	/**
	 * @access public
	 */
	var $db_host = "";
	
	/**
	 * @access public
	 */
	var $db_port = "";
	
	/**
	 * @access public
	 */
	var $db_user = "";
	
	/**
	 * @access public
	 */
	var $db_pass = "";
	
	/**
	 * @access public
	 */
	var $sp_file = "";
	
	/**
	 * @access public
	 */
	var $sp_params = null;
	
	/**
	 * @access public
	 */
	var $results = null;
	
	/**
	 * @access private
	 */
	var $_sql = "";

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function StoredProcedure( $db_host = "localhost", $db_port = "3306", $db_user = "root", $db_pass = "", $sp_file = "", $sp_params = array() )
	{
		$this->db_host   = $db_host;
		$this->db_user   = $db_user;
		$this->db_pass   = $db_pass;
		$this->sp_file   = $sp_file;
		$this->sp_params = $sp_params;
		$this->results   = array();
	}
	

	/**
	 * Execute stored procedure.
	 *
	 * @access public
	 */
	function execute()
	{
    	$this->_sql = @file_get_contents( $this->sp_file );
		
		if ( !$this->_sql )
			return PEAR::raiseError( "Error openning file: " . $this->sp_file, ST_ERR_OPEN_SP_FILE );

		$this->_setParams();
		$statements = $this->_splitSQL();
		$res = $this->_exec( $statements );
		
		if ( PEAR::isError( $res ) )
			return $res;
			
		return $this->results;
	}

  
	// private methods
	
	/**
	 * Replace parameters in the SQL.
	 *
	 * @access private
	 */
	function _setParams()
	{
		$match = preg_match_all( "/\<param.*(name=\"([^\"]*)\")[^\>]\>/i", $this->_sql, $matches );
    	
		if ( $match > 0 )
		{
      		for ( $i = 0; $i < count( $matches[0] ); $i++ )
			{
				$placeholder = $matches[0][$i];
				$name = $matches[2][$i];
				$this->_sql = str_replace( $placeholder, ( ( isset( $this->sp_params[$name] ) )? $this->sp_params[$name] : "NULL" ), $this->_sql );
			}
		}
	}

	/**
	 * Split SQL on separate statements.
	 *
	 * @access private
	 */
	function _splitSQL()
	{
		// FIXME - does not cover number of cases.
		// For example a ';' between string delimiters will cause an error.
		return explode( ";", $this->_sql );
	}

  	/**
	 * Execute statements sequence.
	 *
	 * @access private
	 */
	function _exec( $statements )
	{
		$conn = $this->_connect();
		$res  = true;
		
		if ( PEAR::isError( $conn ) )
			return $conn;
			
		foreach ( $statements as $statement )
		{
			if ( trim( $statement ) != "" )
			{
				$res = $this->_execStatement( $conn, $statement );
				
				if ( PEAR::isError( $res ) )
					break;
			}
    	}
		
		mysql_close( $conn );
		return $res;
  	}

	/**
	 * Connect to mysql.
	 *
	 * @access private
	 */
	function _connect()
	{
    	$conn = @mysql_connect( $this->db_host . ":" . $this->db_port, $this->db_user, $this->db_pass );
		
		if ( !$conn )
			return PEAR::raiseError( mysql_errno() . ": " . mysql_error(), ST_ERR_MYSQL_CONNECT );

    	return $conn;
  	}

	/**
	 * Execute single statement.
	 *
	 * @access private
	 */
	function _execStatement( $conn, $statement )
	{
    	$res = mysql_query( $statement, $conn );
		
		if ( !$res )
			return PEAR::raiseError( mysql_errno() . ": " . mysql_error(), ST_ERR_MYSQL_CONNECT );

    	if ( !( $res === true ) )
		{
      		$this->results[] = mysql_fetch_assoc( $res );
      		mysql_free_result( $res );
    	}
    	else
		{
      		$this->results[] = mysql_affected_rows( $conn );
    	}
  	}
} // END OF StoredProcedure

?>
