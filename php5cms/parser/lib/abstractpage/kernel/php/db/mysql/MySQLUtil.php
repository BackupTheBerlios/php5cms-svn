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
 * Static helper functions.
 *
 * @package db_mysql
 */
 
class MySQLUtil
{
	/**
	 * Converts a MySQL datetime to a timestamp.
	 *
	 * @access public
	 * @static
	 */
	function mysqlToTimestamp( $datetime ) 
	{
		if ( !list( $d, $t ) = explode( " ", $datetime ) ) 
			$d = $datetime;
	
		list( $y, $m, $d ) = explode( "-", $d );
		list( $h, $i, $s ) = explode( ":", $t );
	
		return mktime( $h, $i, $s, $m, $d, $y );
	}

	/**
	 * Converts a MySQL datetime to a timestamp.
	 *
	 * @access public
	 * @static
	 */
	function timestampToMysql( $time ) 
	{
		return date( 'Y-m-d h:i:s', $time );
	}
	
	/**
	 * @access public
	 * @static
	 */
	function getXML( $dbname, $host, $user, $password, $sqlstring = "" )
	{
		if ( $sqlstring == "" )
			return PEAR::raiseError( "No SQL statement provided." );
	
		$out = "";
		
		$link = mysql_connect( $host, $user, $password );
		
		if ( !$link )
			return PEAR::raiseError( "Connection error." );
		
		$db = mysql_select_db( $dbname );
		
		if ( !$db )
			return PEAR::raiseError( "Error selecting database." );
			
		$result = mysql_query( $sqlstring, $link );
	
		if ( $result == false )
			return PEAR::raiseError( "Error in SQL statement: " . $sqlstring );

		$out .= "<?xml version=\"1.0\"?>\n\n";
		$out .= "<dataxml>\n";
		$out .= "\t<fields>\n";
		
		$i = 0;
		$FieldsVector = array();
		
		while ( $i < mysql_num_fields( $result ) )
		{
			$meta = mysql_fetch_field( $result );
			
			if ( $meta )
			{
				$out .= "\t\t<field>" .$meta->name . "</field>\n";
			
				$FieldsVector[] = $meta->name;
				$i = $i + 1;
			}
		}
	
		$out .= "\t</fields>\n\n";
	
		// And NOW the Data ...
		$out .= "\t<rows>\n";
	
		while ( $row = mysql_fetch_array( $result ) )
		{
			$out .= "\t\t<row>\n";
		
			for ( $j = 0; $j < $i; $j++ )
			{
				$FieldName  = "data";
				$Attributes = " fieldname=\"".$FieldsVector[$j]."\"";
			
				$out .= "\t\t\t<" . $FieldName . $Attributes . ">" . $row[$j] . "</" . $FieldName . ">\n";
			}
		
			$out .= "\t\t</row>\n";
		}
	
		$out .= "\t</rows>\n";
		$out .= "</dataxml>";

		mysql_free_result( $result );	
		mysql_close( $link );
		
		return $out;
	}
	
	/**
	 * Turn a MySQL database table into a hash, and optionally
	 * a state-maintaining popup menu (<SELECT> box).
	 *
	 * Parameters
	 * host:       database host
	 * user:       database user
	 * password:   password
	 * dbname:     database name
	 * table:      the database table name
	 * id:         the unique key for the table
	 * values:     the value (or values) that correspond to each key
	 *	           you can supply either a scalar (column name) or an
	 *             array of column name
	 * where:      an optional WHERE clause, restricting which items to select
	 * order:      an optional ORDER BV clauses, sorting the results from the db
	 * concatChar: the character which joins multiple columns in the values
	 *             of the hash
	 *
	 * Example
	 * $members = MySQLUtil::getTable( 
	 *		"localhost",
	 *		"somename",
	 *		"mypass",
	 *		"mydb",
	 *		"people", 
	 *		"people_id",
	 *		array( "fname", "lname"),
	 *		"", // leave where blank
	 *		"lname" 
	 * );
	 *
	 * @access public
	 * @static
	 */
	function getTable( $host, $user, $password, $dbname, $table, $id, $values, $where = "", $order = "", $concatChar = " " )
	{
		$link = mysql_connect( $host, $user, $password );
		
		if ( !$link )
			return PEAR::raiseError( "Connection error." );
		
		$db = mysql_select_db( $dbname );
		
		if ( !$db )
			return PEAR::raiseError( "Error selecting database." );
			
		// check the required parameters
		if ( !( $table && $id && $values ) )
			return PEAR::raiseError( "Parameter Error." );
		
		// if we are selecting multiple columns (i.e. have been passed an array)
		// then join the array items together in a list
		if ( is_array( $values ) )
			$valueList = join( ", ", $values );
		else
			$valueList = $values;

		// the minimal select statement
		$sql = "SELECT $id, $valueList FROM $table";

		// add a where statement, if the user supplied one
		if ( $where )
			$sql .= " WHERE $where";

		// add a order by statement, if the user supplied one
		if ( $order )
			$sql .= " ORDER BY $order";

		// execute the query
		// quiet mode, keep the output nice
		$result = mysql_query( $sql, $link );
		
		if ( $result )
		{
			$rows = mysql_num_rows( $result );

			// the database returned at least one row,
			// so loop through the result
			if ( $rows )
			{	
				// we are selecting multiple columns, so we will concatenate
				// them together for the values of the hash
				if ( is_array( $values ) )
				{
					while ( $myrow = mysql_fetch_array( $result ) )
					{
						$temp = "";
					
						while ( list( $key, $val) = each( $values ) )
							$temp .= $myrow[$val] . $concatChar;
						
						// get rid of the extra space at the end
						$temp = chop( $temp );
						$hash[$myrow[$id]] = $temp;
						reset( $values );
					}
				}
				// we are selecting only one column
				else
				{
					while ( $myrow = mysql_fetch_array( $result ) )
						$hash[$myrow[$id]] = $myrow[$values];
				}

				// success!
				return $hash;
			}
			else
			{
				return PEAR::raiseError( "Table is not available." );
			}
		}
		else
		{
			return PEAR::raiseError( "Database Error." );
		}
	}
} // END OF MySQLUtil

?>
