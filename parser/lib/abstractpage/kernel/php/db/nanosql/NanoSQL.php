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


using( 'db.nanosql.NanoDB' );
using( 'db.nanosql.NanoSQLResult' );
using( 'db.nanosql.NanoSQLResultSet' );


define( "NANOSQL_LIKE_CASE_SENSITIVE",  0 );
define( "NANOSQL_ORDER_CASE_SENSITIVE", 0 );

/*
// Timeouts
define( "NANOSQL_OPEN_TIMEOUT",     10 ); // Timeout in seconds to try opening a still locked Table
define( "NANOSQL_LOCK_TIMEOUT",     10 ); // Timeout in seconds to try locking a still locked Table
define( "NANOSQL_LOCKFILE_TIMEOUT", 30 ); // Timeout for the maximum time a lockfile can exist
*/

/*
// Predefined Databases
define( "NANOSQL_ROOT_DATABASE", "" );
*/

/*
// Order Types
define( "NANOSQL_ORDER_ASC",  1 );
define( "NANOSQL_ORDER_DESC", 2 );
*/

/*
// Error Constants
define( "NANOSQL_ERROR_", 1000 );
define( "NANOSQL_ERROR_", 1000 );
define( "NANOSQL_ERROR_", 1000 );
define( "NANOSQL_ERROR_", 1000 );
define( "NANOSQL_ERROR_", 1000 );
define( "NANOSQL_ERROR_", 1000 );
define( "NANOSQL_ERROR_", 1000 );
define( "NANOSQL_ERROR_", 1000 );
define( "NANOSQL_ERROR_", 1000 );
define( "NANOSQL_ERROR_", 1000 );
define( "NANOSQL_ERROR_", 1000 );
define( "NANOSQL_ERROR_", 1000 );
*/


$GLOBAL['NANOSQL_CONNECTION']        = array();
$GLOBAL['NANOSQL_LAST_ERROR_STRING'] = "";
$GLOBAL['NANOSQL_LAST_ERROR_CODE']   = -1;


/**
 * NanoSQL Class - basically a SQL wrapper for NanoDB.
 *
 * @package db_nanosql
 */
 
class NanoSQL extends NanoDB
{	
	/**
	 * Constructor
	 */
	function NanoSQL()
	{
		$this->NanoDB();
	}
	
	
	// helper
	
	/**
	 * Compares 2 values by $operator, and returns true or false.
	 *
	 * @static
	 */
	function compare( $value1, $value2, $operator )
	{
		if ( $operator == "=" )
			return ( $value1 == $value2 );

		if ( $operator == ">" )
			return ( $value1 > $value2 );
		
		if ( $operator == "<" )
			return ( $value1 < $value2 );
		
		if ( $operator == "<>" || $operator == "!=" )
			return ( $value1 != $value2 );
		
		if ( $operator == ">=" )
			return ( $value1 >= $value2 );
		
		if ( $operator == "<=" )
			return ( $value1 <= $value2 );
		
		if ( trim( strtoupper( $operator ) ) == "LIKE" )
			return NanoSQL::compare_like( $value1, $value2 );
		
		return false;
	}

	function compare_like( $value1, $value2 ) 
	{ 
		static $patterns = array(); 

		// Lookup precomputed pattern 
		if ( isset( $patterns[$value2] ) ) 
		{ 
			$pat = $patterns[$value2]; 
		} 
		else 
		{ 
			// Calculate pattern 
			$rc     = 0; 
			$mod    = ""; 
			$prefix = "/^"; 
			$suffix = "$/"; 
       
			// quote regular expression characters 
			$str = preg_quote( $value2, "/" ); 
       
			// unquote \ 
			$str = str_replace( "\\\\", "\\", $str ); 
       
			// Optimize leading/trailing wildcards 
			if ( substr( $str, 0, 1 ) == '%' ) 
			{ 
				$str    = substr( $str, 1 ); 
				$prefix = "/"; 
			} 
		
			if ( substr( $str, -1 ) == '%' && substr( $str, -2, 1 ) != '\\' ) 
			{ 
				$str    = substr( $str, 0, -1 ); 
				$suffix = "/"; 
			} 
       
			// case sensitive ? 
			if ( !NANOSQL_LIKE_CASE_SENSITIVE ) 
				$mod = "i"; 
          
			// setup a StringParser and replace unescaped '%' with '.*' 
			$sp = new StringParser(); 
			$sp->setConfig( array() ,"\\", array() ); 
			$sp->setString( $str ); 
			$str = $sp->replaceCharWithStr( "%", ".*" ); 
		
			// replace unescaped '_' with '.' 
			$sp->setString( $str ); 
			$str = $sp->replaceCharWithStr( "_", "." ); 
			$pat = $prefix . $str . $suffix . $mod; 

			// Stash precomputed value 
			$patterns[$value2] = $pat; 
		} 
       
		return preg_match( $pat, $value1 ); 
	}
	
	/**
	 * Splits a full column name into its subparts (name, table, function).
	 * return true or false on error
	 */
	function split_full_colname( $fullColName, &$colName, &$colTable, &$colFunc )
	{
		$colName  = "";
		$colTable = "";
		$colFunc  = "";
	
		// direct value ?
		if ( is_numeric( $fullColName ) || NanoSQL::has_quotes( $fullColName ) ) 
		{
			$colName = trim( $fullColName );
			return true;
		}
	
		if ( !NanoSQL::is_false( $pos = strpos( $fullColName, "(" ) ) ) 
		{
			$colFunc = substr( $fullColName, 0, $pos );
			$fullColName = substr( $fullColName, $pos + 1 );
		}
	
		if ( !NanoSQL::is_false( $pos = strpos( $fullColName, "." ) ) ) 
		{
			$colTable = substr( $fullColName, 0, $pos  );
			$colName  = substr( $fullColName, $pos + 1 );
		}  
		else 
		{
			$colName = $fullColName;
		}

		$colName = trim( $colName );
	
		if ( $colFunc ) 
		{
			if ( NanoSQL::last_char( $colName ) == ")" ) 
				NanoSQL::remove_last_char( $colName );
			else 
				return false;
		}
		
		$colName  = trim( $colName  );
		$colTable = trim( $colTable );
		$colFunc  = strtoupper( trim( $colFunc ) );
	
		return true;
	}

	function execFunc( $func, $param ) 
	{
		switch ( $func ) 
		{
			case "MD5":
				return NanoSQL::doFuncMD5( $param );
		
			case "NOW":
				return NanoSQL::doFuncNOW();
		
			case "UNIX_TIMESTAMP":
				return NanoSQL::doFuncUNIX_TIMESTAMP();
		
			case "ABS":
				return NanoSQL::doFuncABS( $param );
			
			case "LCASE":
		
			case "LOWER":
				return NanoSQL::doFuncLCASE( $param );
		
			case "UCASE":
		
			case "UPPER":
				return NanoSQL::doFuncUCASE( $param );
		
			default:
				return $param;
		}
	}

	function doFuncMD5( $param ) 
	{
		return md5( $param );
	}

	function doFuncNOW()
	{
		return date( "Y-m-d H:i:s", NanoSQL::get_static_timestamp() );
	}

	function doFuncUNIX_TIMESTAMP()
	{
		return NanoSQL::get_static_timestamp();
	}

	function doFuncABS( $param ) 
	{
		return abs( $param );
	}

	function doFuncLCASE( $param ) 
	{
		return strtolower( $param );
	}

	function doFuncUCASE( $param ) 
	{
		return strtoupper( $param );
	}

	function execGroupFunc( $func, $params ) 
	{
		switch ( $func ) 
		{
			case "MAX":
				return NanoSQL::doFuncMAX( $params );
		
			case "MIN":
				return NanoSQL::doFuncMIN( $params );
		
			case "COUNT":
				return NanoSQL::doFuncCOUNT( $params );
		
			case "SUM":
				return NanoSQL::doFuncSUM( $params );
		
			case "AVG":
				return NanoSQL::doFuncAVG( $params );
				
			default:
				return "";
		}
	}

	function doFuncMAX( $params ) 
	{
		$maxVal = $params[0];
	
		for ( $i = 1; $i < count( $params ); ++$i )
			$maxVal = max( $maxVal, $params[$i] );
	
		return $maxVal;
	}

	function doFuncMIN( $params ) 
	{
		$minVal = $params[0];
	
		for ( $i = 1; $i < count( $params ); ++$i )
			$minVal = min( $minVal, $params[$i] );
	
		return $minVal;
	}

	function doFuncCOUNT( $params ) 
	{
		return count( $params );
	}

	function doFuncSUM( $params ) 
	{
		$sum = 0;
	
		for ( $i = 0; $i < count( $params ); ++$i )
			$sum += $params[$i];
	
		return $sum;
	}

	function doFuncAVG( $params ) 
	{
		$sum = NanoSQL::doFuncSUM( $params );
		return $sum / count( $params );
	}
	
	function is_false( $var ) 
	{
		return ( is_bool( $var ) && !$var );
	}
	
	function array_walk_remove_quotes( &$value, &$key ) 
	{
		if ( NanoSQL::has_quotes( $value ) )
			NanoSQL::remove_quotes( $value );
	}

	/**
	 * Ensures that all timestamp requests of one execution have the same time.
	 */
	function get_static_timestamp()
	{
		static $t = 0;
	
		if ( $t == 0 )
			$t = time();
	
		return $t;
	}
	
	function last_char( $string ) 
	{
		return $string{strlen( $string ) - 1};
	}

	function remove_last_char( &$string ) 
	{
		$string = substr( $string, 0, strlen($string) - 1 );
	}

	function is_empty_str( $var ) 
	{
		return ( is_string( $var ) && $var == "" );
	}
	
	function remove_quotes( &$str ) 
	{
		$str = substr( $str, 1 );
		NanoSQL::remove_last_char( $str );
	}

	/**
	 * Returns $length chars from the right side of $string.
	 */
	function substr_right( $string, $length ) 
	{
		return substr( $string, strlen( $string ) - $length );
	}
	
	function has_quotes( $str ) 
	{
		if ( NanoSQL::is_empty_str( $str ) )
			return false;
	
		return ( $str[0] == "'" || $str[0] == "\"" ) && ( NanoSQL::last_char( $str ) == "'" || NanoSQL::last_char( $str ) == "\"" );
	}
} // END OF NanoSQL


/**
 * Get number of affected rows in previous NanoSQL operation.
 */
function nanosql_affected_rows()
{
}

/**
 * Close NanoSQL connection.
 */
function nanosql_close()
{
}

/**
 * Open a connection to a NanoSQL Server.
 */
function nanosql_connect()
{
}

/**
 * Create a NanoSQL database.
 */
function nanosql_create_db()
{
}

/**
 * Drop (delete) a NanoSQL database.
 */
function nanosql_drop_db()
{
}

/**
 * Returns the numerical value of the error message from previous NanoSQL operation.
 */
function nanosql_errno()
{
}

/**
 * Returns the text of the error message from previous NanoSQL operation.
 */
function nanosql_error()
{
}

/**
 * Fetch a result row as an associative array.
 */
function nanosql_fetch_array()
{
}

/**
 * Get column information from a result and return as an object.
 */
function nanosql_fetch_field()
{
}

/**
 * Fetch a result row as an object.
 */
function nanosql_fetch_object()
{
}

/**
 * Get a result row as an enumerated array.
 */
function nanosql_fetch_row()
{
}

/**
 * List tables in a NanoSQL database.
 */
function nanosql_list_tables()
{
 	$handle = opendir( NANODB_DB_DIRECTORY ); 
   	$count  = 0;
	$tables = array();
	
   	while ( false != ( $file = readdir( $handle ) ) ) 
   	{
      	if ( preg_match( "/\.meta$/i", $file ) && ( $file != NANODB_DB_DIRECTORY . ".meta" ) )
      	{
			++$count;

			// We have a database file. Extract the database name.
			$tables[] = substr( $file, 0, strlen( $file ) - 5 );
		}
	}

	closedir( $handle );
		
   	if ( $count > 0)
   		return $tables;
	else
   		return false; 
}

/**
 * Get number of fields in result.
 */
function nanosql_num_fields()
{
}

/**
 * Get number of rows in result.
 */
function nanosql_num_rows()
{
}

/**
 * Open a persistent connection to a NanoSQL server.
 */
function nanosql_pconnect()
{
} 

/**
 * Send a NanoSQL query.
 */
function nanosql_query()
{
}

/**
 * Get result data.
 */
function nanosql_result()
{
}

/**
 * Select a NanoSQL database.
 */
function nanosql_select_db()
{
}

/**
 * Get version of NanoSQL.
 */
function nanosql_version()
{
	return NANODB_VERSION;
}

?>
