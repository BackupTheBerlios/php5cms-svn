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
 * SQLQuery Class
 *
 * This class has methods that will create sql queriers for you
 * which should make life a little easier. Really easy to use.
 *
 * @package db_sql
 */

class SQLQuery extends PEAR
{
	/**
	 * Create Update SQL string.
	 *
	 * @param  array	$array	Associative array
	 * @param  string	$table	Table Name
	 * @param  array	$ext_array	WHERE array
	 * @param  array	$limit		Limit array, OPTIONAL
	 * @return string
	 */
	function create_update_sql( $array, $table, $ext_array, $limit = "" )
	{	
		$count  = count( $array );
		$string = "UPDATE `" . $table . "` SET ";
			
		while ( list( $i ) = each( $array ) )
			$string .= "`" . $i . "`='" . $array[$i] . "', ";
		
		$string  = substr( $string, 0, -2 );	
		$string .= $this->_create_where_sql( $ext_array );
		$string .= $this->_create_limit_sql( $limit );
			
		return $string;
	}
		
	/**
	 * Create Insert SQL string.
	 *
	 * @param	array	$array	Associative array
	 * @param	string	$table	Table name
	 * @return string
	 */
	function create_insert_sql( $array, $table )
	{	
		$count  = count( $array );
		$string = "INSERT INTO `" . $table . "` ";
			
		while ( list( $i ) = each( $array ) )
		{
			$var_1 .= "`" . $i . "`, ";
			$var_2 .= "'" . $array[$i] . "', ";
		}
			
		$var_1 = substr( $var_1, 0, -2 );			
		$var_2 = substr( $var_2, 0, -2 );
									
		$string .= "(" . $var_1 . ") VALUES (" . $var_2 . ")";
		return $string;
	}
	
	/**
	 * Create Delete SQL string.
	 *
	 * @param	string	$table Table name
	 * @param	array	$where Associative Array for where query string
	 * @param	array	$limit	Limit array, OPTIONAL
	 * @return	string
	 */
	function create_delete_sql( $table, $where, $limit = "" )
	{			
		$string  = "DELETE FROM " . $table;
		$string .= $this->_create_where_sql( $where );
		$string .= $this->_create_limit_sql( $limit );
			
		return $string;
	}
				
	/**
	 * Create Select SQL string.
	 *
	 * @param	array	$select	Array of what to select
	 * @param	string	$table	Table name
	 * @param	array	$where	Assodiative Array of WHERE string
	 * @param	array	$limit	Limit array, OPTIONAL
	 * @return string
	 */
	function create_select_sql( $select, $table, $where, $limit = "" )
	{ 
		while ( list( $i ) = each( $array ) )
			$var_1 .= "`" . $i . "`, ";
			
		$var_1 = substr( $var, 0, -2 );
			
		$string  = "SELECT (" . $var_1 .") FROM " . $table;
		$string .= $this->_create_where_sql( $where );
		$string .= $this->_create_limit_sql( $limit );
			
		return $string;
	}
				
	/**
	 * Create Database SQL string.
	 *
	 * @param	string	$db_name	Database name you want to create	
	 * @return	string
	 */
	function create_database_sql( $db_name )
	{
		$string = "CREATE DATABASE " . $db_name;
		return $string;
	}
		
	/**
	 * Create Drop Database SQL string.
	 *
	 * @param	string	$db_name	Database name you want to drop	
	 * @return	string
	 */
	function drop_database_sql( $db_name )
	{
		$string = "DROP DATABASE " . $db_name;
		return $string;
	}
				
	/**
	 * Create Drop Table(s) SQL string.
	 *
	 * @param	array	$tables	Tables you would like to drop	
	 * @return	string
	 */
	function drop_tables_sql( $tables )
	{	
		while ( list( $i ) = each( $tables ) )
			$tables_str .= $tables[$i] . ", ";
			
		$tables_str = substr( $tables_str, 0, -2 );
		$string = "DROP TABLE " . $tables_str;
			
		return $string;
	}
	
	
	// private methods
	
	/**
	 * Creates the WHERE part of an sql string.
	 *
	 * This functions creates the WHERE part of an sql string
	 * using an associative array
	 *
	 * @param	array	$array	Array to create
	 * @return  string	
	 */
	function _create_where_sql( $array )
	 { 		
		if ( is_array( $array ) )
		{
			while ( list( $i ) = each( $array ) )
				$var .= " AND `" . $i . "`='" . $array[$i] . "'";
			
			$var    = substr( $var, 4 );
			$return =" WHERE " . $var;
				
			return $return;
		}	
	 }

	/**
	 * Creates the LIMIT part of an sql string.
	 *
	 * This functions creates the LIMIT part of an sql string
	 * using an array. Use it something like this...
	 * Ex1: create_limit_sql($limit=array("2")); // Returns " LIMIT 2 "
	 * Ex2: create_limit_sql($limit=array("30","60")); // Returns " LIMIT 30, 60 "
	 * Ex3: create_limit_sql(""); // Returns nothing
	 *
	 * @param	array	$array		
	 * @return  string	
	 */
	function _create_limit_sql( $array )
	{ 		
		if ( is_array( $array ) )
		{
			$count = count( $array );
		
			if ( $count == 1 )
				return " LIMIT " . $array[0];
			else if ( $count == 2 )
				return " LIMIT " . $array[0] . ", " . $array[1];		
		}	
	}
} // END OF SQLQuery

?>
