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
 * @package db_file
 */
 
class DBFile extends PEAR
{
	/**
	 * Database that is being used
	 *
	 * @access private
	 */
 	var $_db;
	
	/**
	 * The hands of the fetchArray() function
	 *
	 * @access private
	 */
 	var $_hands = array();
	
	/**
	 * Total Path to the directory where the databases are
	 *
	 * @access private
	 */
 	var $_path;
	
	/**
	 * Array with the results of select() function
	 *
	 * @access private
	 */
 	var $_results = array();
	
	/**
	 * Content of the tables
	 *
	 * @access private
	 */
 	var $_t_data = array();
	
	/**
	 * Informations of the tables
	 *
	 * @access private
	 */
 	var $_t_info = array();

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
 	function DBFile( $path ) 
 	{
 		if ( !is_dir( $path ) ) 
		{
			$this = new PEAR_Error( "Invalid directory " . $path . "." );
			return;
		}

		$this->_path = $path;
 	}

	
	/**
	 * Conversion table
	 *
	 * @access public
	 */
	function convert( $var, $type = "in" ) 
	{
		$array_1 = array(
			"\\",
			"[",
			"]",
			",",
			"#",
			"\"",
			"'",
			"\n",
			"(",
			")",
			"=",
			"%",
			"!",
			"{",
			"}",
			"?",
			".",
			"|",
			"WHERE",
			"ORDER",
			"BY",
			"LIMIT",
			"SET"
		);

		$array_2 = array(
			"\\\\",
			"\\[",
			"\\]",
			"[\\054]",
			"[\\043]",
			"[\\042]",
			"[\\047]",
			"[\\300]",
			"[\\050]",
			"[\\051]",
			"[\\075]",
			"[\\045]",
			"[\\041]",
			"[\\173]",
			"[\\175]",
			"[\\077]",
			"[\\056]",
			"[\\174]",
			"[\\301]",
			"[\\302]",
			"[\\303]",
			"[\\304]",
			"[\\305]"
		);
 
 		if ( $type == "in" ) 
		{
			$var = str_replace( $array_1, $array_2, $var );
		}
		else 
		{
			$array_1 = array_reverse( $array_1 );
			$array_2 = array_reverse( $array_2 );
			$var = str_replace( $array_2, $array_1, $var );
		}
	
		return $var;
 	}
 
 	/**
	 * Creates a database.
	 *
	 * @access public
	 */
 	function createDB( $name ) 
	{
		if ( !preg_match( "/^[A-Z,a-z,0-9]+$/", $name ) )
			return PEAR::raiseError( "The name of the database can only contain letters and numbers." );
 
 		if ( !is_dir( $this->_path . DIRECTORY_SEPARATOR . $name ) )
		{
			if ( mkdir( $this->_path . DIRECTORY_SEPARATOR . $name, 0777 ) )
				return true;
			else
				return PEAR::raiseError( "It was not possible to create the database " . $name );
		}
		else
		{
			return PEAR::raiseError( "Database " . $name . " already exists." );
		}
 	}

	/**
	 * Creates a table.
	 *
	 * @access public
	 */
 	function createTable( $table, $info, $db = 0 ) 
	{
		if ( !preg_match( "/^[A-Z,a-z,0-9]+$/", $table ) )
			return PEAR::raiseError( "The name of the table con only contain letters and numbers." );
 
		$info = trim( $info );
		$info = explode( ",", $info );

		// Prepares the content
		foreach ( $info as $row ) 
		{
			$row = trim( $row );
			$row = substr( $row, 1, strlen( $row ) - 2 );

			$name  = preg_replace( "/^[\040]*([A-Z,a-z,0-9]+).*$/si","\\1", $row );
			$type  = preg_replace( "/^[\040]*[A-Z,a-z,0-9]+[\040]+(int|text)[\040]*.*$/si","\\1", $row );
			$size  = preg_replace( "/^[\040]*[A-Z,a-z,0-9]+[\040]+(int|text)[\040]*(\(\d+\))?[\040]*.*$/si","\\2", $row );
			$other = preg_replace( "/^[\040]*[A-Z,a-z,0-9]+[\040]+(int|text)[\040]*(\(\d+\))?[\040]+(.+)[\040]*$/si","\\3", $row );
		
			if ( $name == $row && eregi( " ", $row ) )
				return PEAR::raiseError( "It was not possible to recognize the names of the fields." );
		
			if ( $type == $row )
				$type = 0;
		
			if ( $size == $row )
				$size = 0;
		
			if ( $other == $row )
				$other = 0;

			$fields[] = $name;
			$types[]  = $type;
			$sizes[]  = $size;
			$others[] = $other;
		}
	
		unset( $row );
		$size_1 = sizeof( $fields );
		
		for ( $i = 0; $i < $size_1; $i++ ) 
		{
			$row[$i]  = trim( $fields[$i] );
			$row[$i] .= " -> ";
			
			if ( $types[$i] ) 
				$row[$i] .= trim( $types[$i] );
				
			if ( $sizes[$i] ) 
				$row[$i] .= trim( $sizes[$i] );
				
			if ( $others[$i] ) 
				$row[$i] .= " " . str_replace( " ", ",", trim( $others[$i] ) );
		}
	
		$save  = implode( "\n", $row );
		$save .= "\n";

		// Saves the information file
		$file  = $this->_path . DIRECTORY_SEPARATOR . $db . DIRECTORY_SEPARATOR . $table . "_info";
		$fopen = fopen( $file, "w+" );
		$fsave = fwrite( $fopen, $save );
		fclose( $fopen );
		@chmod( $file, 0666 );

		//Creates the table
		$file  = $this->_path . DIRECTORY_SEPARATOR . $db . DIRECTORY_SEPARATOR . $table;
		$fopen = fopen( $file, "w+" );
		$fsave = fwrite( $fopen, "" );
		fclose( $fopen );
		@chmod( $file, 0666 );

		return true;
 	}
 	
	/**
	 * Function to use simultaneos databases.
	 *
	 * @access public
	 */
 	function queryDB( $db, $query ) 
 	{
 		$res = $this->_checkDB( $db);
	
 		if ( PEAR::isError( $res ) )
			return $res;
	
		$result = $this->query( $query, $db );
		return $result;
 	}
	
	/**
	 * Deletes values from the table.
	 *
	 * @access public
	 */
 	function delete( $table, $where = 0, $db = 0 ) 
	{
		$db = $this->_returnDB( $db );
	
		if ( PEAR::isError( $db ) )
			return $db;
		
		$res = $this->_checkLoadTable( $table, $db );

		if ( PEAR::isError( $res ) )
			return $res;
		
		// Selects the hands
		$hands = $this->_makeWhere( $where, $table, $db );
	
		if ( PEAR::isError( $hands ) )
			return $hands;
		
		$hands_a = explode( ",", $hands );

		foreach ( $hands_a as $hand ) 
		{
			if ( $hand == null )
				return PEAR::raiseError( "It was not possible to find results to be deleted." );
			else
				unset( $this->_t_data[$db][$table][$hand] );	
		}

		$res = $this->_rebuildArray( $table, $db );
	
		if ( PEAR::isError( $res ) )
			return $res;
		
		$res = $this->_rebuildResults( $hands, $table, $db );

		if ( PEAR::isError( $res ) )
			return $res;
		
		// Saves the file
		$res = $this->_saveTable( $table, $db, $db, $table );
	
		if ( PEAR::isError( $res ) )
			return $res;
		
		return true;
 	}
	
	/**
	 * Deletes one database.
	 *
	 * @access public
	 */
 	function dropDB( $db ) 
	{
 		$res = $this->_checkDB( $db );
 	
		if ( PEAR::isError( $res ) )
			return $res;
		
 		$path = $this->_path . DIRECTORY_SEPARATOR . $db;
	
		if ( $dir = opendir( $path ) ) 
		{
			while ( ( $file = readdir( $dir ) ) !== false ) 
			{ 
       			if ( $file != "." && $file != ".." ) 
					$files[] = $file;
			}
		
			closedir( $dir ); 
		}
	
		if ( $files ) 
		{
			foreach ( $files as $file )
				unlink( $path . DIRECTORY_SEPARATOR . $file );
		}
	
		rmdir( $path );
		return true;
 	}
	
	/**
	 * Deletes one table.
	 *
	 * @access public
	 */
 	function dropTable( $table, $db = 0 ) 
 	{
 		$db = $this->_returnDB( $db );
	
		if ( PEAR::isError( $db ) )
			return $db;
		
		$res = $this->_checkDB( $db );
 	
		if ( PEAR::isError( $res ) )
			return $res;
		
		$res = $this->_checkTable( $table, $db );
	
		if ( PEAR::isError( $res ) )
			return $res;
		
		unlink( $this->_path . DIRECTORY_SEPARATOR . $db . DIRECTORY_SEPARATOR . $table);
		unlink( $this->_path . DIRECTORY_SEPARATOR . $db . DIRECTORY_SEPARATOR . $table . "_info" );
	
		return true;
 	}
 
 	/**
	 * Returns each line of one array.
	 *
	 * @access public
	 */
 	function fetchArray( $key ) 
	{
		// Tests if one key exists
		if ( !$this->_results[$key] )
			return PEAR::raiseError( "It was not possible to find the key for the result." );
 
 		$hand    = $this->_hands[$key];
		$results = explode( ",", $this->_results[$key]['hands'] );
		$fields  = $this->_results[$key]['fields'];
		$table   = $this->_results[$key]['table'];
		$db      = $this->_results[$key]['db'];
	
		// Creates the result to be returned
		$param = $results[$hand];

		if ( $fields == "*" ) 
		{
			$row = $this->_t_data[$db][$table][$param];
		}
		else 
		{
			$fields_array = explode( ",", $fields );
			$size_1 = sizeof( $fields_array );
		
			for ( $j = 0; $j < $size_1; $j++ ) 
			{
				$field = $fields_array[$j];
				$row[$field] = $this->_t_data[$db][$table][$param][$field];
			}
		}

		// Increments the hand
		$size_2 = sizeof( $results );
	
		if ( !$size_2 ) 
		{
			return false;
		}
		else if ( $size_2 - 1 >= $hand ) 
		{
			$this->_hands[$key]++;
			return $row;
		}
		else 
		{
			$this->_hands[$key] = 0;
			return false;
		}
 	}

 	/**
	 * Insert the lines in the memory.
	 *
	 * @access public
	 */
 	function insert( $table, $content, $db = 0 ) 
	{
		$db = $this->_returnDB( $db );
	
		if ( PEAR::isError( $db ) )
			return $db;
		
		$res = $this->_checkLoadTable( $table, $db );

		if ( PEAR::isError( $res ) )
			return $res;
		
		// Splits the results
		$content = substr( $content, 1, strlen( $content ) - 2 );
		$each    = explode( ")(", $content );
		$size_1  = sizeof( $each ) - 1;

		$this->_t_info[$db][$table]['fields'] = array_unique( $this->_t_info[$db][$table]['fields'] );

		$size_3  = sizeof( $this->_t_info[$db][$table]['fields'] );
		$n       = sizeof( $this->_t_data[$db][$table] );

		for ( $i = 0; $i <= $size_1; $i++ ) 
		{
			// Splits the values
			$each[$i] = substr( $each[$i], 1, strlen( $each[$i] ) - 2 );
			$values   = explode( "','", $each[$i] );
			$size_2   = sizeof( $values ) - 1;

			if ( sizeof( $values ) != $size_3 )
				return PEAR::raiseError( "The number of fields sent in the insert " . ( $i + 1 ) . " does not match with the number of the fields of the table!" );

			for ( $j = 0; $j <= $size_2; $j++ ) 
			{
				$field = $this->_t_info[$db][$table]['fields'][$j];
				$type  = $this->_makeType( $field, $table, $db );
			
				if ( PEAR::isError( $type ) )
					return $type;
				
				// Checks if the field is null
				$res = $this->_checkNull( $values[$j], $field,$type['other'] );

				if ( PEAR::isError( $res ) )
					return $res;
				
				// Checks the type
				$res = $this->_checkType( $values[$j], $field, $table, $db );
			
				if ( PEAR::isError( $res ) )
					return $res;
				
				$values[$j] = $res;
			
				// Checks the size
				$res = $this->_checkSize( $field, $values[$j], $type['size'] );

				if ( PEAR::isError( $res ) )
					return $res;
		
				// Checks if it is key
				if ( eregi( "key", $type['other'] ) ) 
				{
					$res = $this->_checkKey( $values[$j], $field, $table, $db );
				
					if ( PEAR::isError( $res ) )
						return $res;
				}

				$this->_t_data[$db][$table][$n][$field] = $this->convert( $values[$j], "out" );
			}
			
			$n++;
		}
	
		// Saves in the file
		$res = $this->_saveTable( $table, $db, $db, $table );
	
		if ( PEAR::isError( $res ) )
			return $res;
		
		return true;
 	}

 	/**
	 * Lists the databases.
	 *
	 * @access public
	 */
 	function listDB()
	{
 		$path = $this->_path;
	
		if ( $dir = opendir( $path ) ) 
		{
			while ( ( $file = readdir( $dir ) ) !== false ) 
			{ 
       			if ( $file != "." && $file != ".." && is_dir( $path . DIRECTORY_SEPARATOR . $file ) ) 
					$dbs[] = $file;
			}
		
			closedir( $dir ); 
		}
		
		return $dbs;
 	}

 	/**
	 * Lists the tables of a database.
	 *
	 * @access public
	 */
 	function listTables( $db = 0 ) 
	{
 		$db = $this->_returnDB( $db );
	
		if ( PEAR::isError( $db ) )
			return $db;
		
		$path = $this->_path . DIRECTORY_SEPARATOR . $db;
	
		if ( $dir = opendir( $path ) ) 
		{
			while ( ( $file = readdir( $dir ) ) !== false ) 
			{ 
       			if ( $file != "." && $file != ".." && is_file( $path . DIRECTORY_SEPARATOR . $file ) && substr( $file, strlen( $file ) - 5, strlen( $file ) ) != "_info" ) 
					$tbs[] = $file;
			}
			
			closedir( $dir ); 
		}
		
		return $tbs;
 	}

 	/**
	 * Build the queries.
	 *
	 * @access public
	 */
 	function query( $query, $db = 0 ) 
	{
		// Instruct for select
		if ( preg_match( "/^[\040]*SELECT.*$/s", $query ) ) 
		{
			$db = $this->_returnDB( $db );
 
 			if ( PEAR::isError( $db ) )
				return $db;
		
 			// Selects the fields
			$a['fields'] = preg_replace( "/[\040]*SELECT[\040]+\(?([\052]|([A-Z,a-z,0-9],?)+)\)?.*/s","\\1", $query );

			// Selects the table
			$a['table'] = preg_replace( "/.*FROM[\040]+'?([A-Z,a-z,0-9]+)'?.*/s","\\1", $query );

			// Selects the where
			$a['where'] = preg_replace( "/.*WHERE[\040]+(\(?[\040]*'?[A-Z,a-z,0-9]+'?[\040]*([\075]|([\041][\075])|[\074]|[\076]|([\074][\075])|([\076][\075])|[\045]|([\041][\045]))[\040]*'.*'[\040]*\)?([\040]+(AND|OR)[\040]+)?)+.*/s","\\1", $query );
		
			// Selects the order
			$a['order'] = preg_replace( "/.*ORDER[\040]+BY[\040]+([A-Z,a-z,0-9]+,?)[\040]*(DESC)?.*/s","\\1 | \\2", $query );

			// Selects the limit
			$a['limit'] = preg_replace( "/.*LIMIT[\040]+([0-9]+(,[0-9]+)?).*/s","\\1", $query );

			// Verify the null values
			while ( list( $key, $value ) = each( $a ) ) 
			{
				if ( $a[$key] == $query ) 
				{
					$a[$key] = 0;
					$key = 0;
				}
			}

			// Verify the essencials values
			if ( !$a['fields'] )
				return PEAR::raiseError( "There is one error in the typed fields." );
			else if ( !$a['table'] )
				return PEAR::raiseError( "There is one error in the typed table." );

			$result = $this->select( 
				$a['table'],
				$a['fields'],
				$a['where'],
				$a['order'],
				$a['limit'],
				$db
			);
		
			if ( PEAR::isError( $result ) )
				return $result;
		}
		// Instruct for insert
		else if ( preg_match( "/^[\040]*INSERT.*$/s", $query ) ) 
		{
			$db = $this->_returnDB( $db );
		
			if ( PEAR::isError( $db ) )
				return $db;
		
			$a['table']  = preg_replace( "/.*INTO[\040]+'?([A-Z,a-z,0-9]+)'?[\040]+.*/s","\\1", $query );
			$a['values'] = preg_replace( "/^.*values(\(.*\))[\040]*$/s","\\1", $query );
		
			// Verify the null fields
			while ( list( $key, $value ) = each( $a ) ) 
			{
				if ( $a[$key] == $query ) 
				{
					$a[$key] = 0;
					$key = 0;
				}
			}
		
			// Verify the essencial values
			if ( !$a['table'] )
				return PEAR::raiseError( "It was not possible to identify the name of the table." );
			else if ( !$a['values'] )
				return PEAR::raiseError( "It was not possible to identify the values." );
		
			$result = $this->insert(
				$a['table'],
				$a['values'],
				$db
			);
		}
		// Instruct for UPDATE
		else if ( preg_match( "/^[\040]*UPDATE.*$/s", $query ) ) 
		{
			$db = $this->_returnDB( $db );
		
			if ( PEAR::isError( $db ) )
				return $db;
		
			$a['table'] = preg_replace( "/[\040]*UPDATE[\040]+'?([A-Z,a-z,0-9]+)'?[\040]+.*/s","\\1", $query );
			$a['where'] = preg_replace( "/.*WHERE[\040]+(\(?[\040]*'?[A-Z,a-z,0-9]+'?[\040]*([\075]|([\041][\075])|[\074]|[\076]|([\074][\075])|([\076][\075])|[\045]|([\041][\045]))[\040]*'.*'[\040]*\)?([\040]+(AND|OR)[\040]+)?)+.*/s","\\1", $query );

			if ( $a['where'] == $query )
				$a['changes'] = preg_replace( "/[\040]*UPDATE[\040]+'?[A-Z,a-z,0-9]+'?[\040]+SET[\040]+((,?[A-Z,a-z,0-9]+='.*',?)+).*/s","\\1", $query );
			else
				$a['changes'] = preg_replace( "/[\040]*UPDATE[\040]+'?[A-Z,a-z,0-9]+'?[\040]+SET[\040]+((,?[A-Z,a-z,0-9]+='.*',?)+)([\040]+WHERE[\040].*)+/s","\\1", $query );

			// Verify the null values
			while ( list( $key, $value ) = each( $a ) ) 
			{
				if ( $a[$key] == $query ) 
				{
					$a[$key] = 0;
					$key = 0;
				}
			}
		
			// Verify the essencial values
			if ( !$a['table'] )
				return PEAR::raiseError( "It was not possible to identify the name of the table." );
			else if ( !$a['changes'] )
				return PEAR::raiseError( "It was not possible to identify the changes." );

			$result = $this->update(
				$a['table'],
				$a['changes'],
				$a['where'],
				$db
			);
		}
		// Instruct for DELETE
		else if ( preg_match( "/^[\040]*DELETE.*$/s", $query ) ) 
		{
			$db = $this->_returnDB( $db );
		
			if ( PEAR::isError( $db ) )
				return $db;
		
			$a['table'] = preg_replace( "/[\040]*DELETE[\040]+FROM[\040]+'?([A-Z,a-z,0-9]+)'?[\040]*.*/s","\\1", $query );
			$a['where'] = preg_replace( "/.*WHERE[\040]+(\(?[\040]*'?[A-Z,a-z,0-9]+'?[\040]*([\075]|([\041][\075])|[\074]|[\076]|([\074][\075])|([\076][\075])|[\045]|([\041][\045]))[\040]*'.*'[\040]*\)?([\040]+(AND|OR)[\040]+)?)+.*/s","\\1", $query );

			// Verify the null values
			while ( list( $key, $value ) = each( $a ) ) 
			{
				if ( $a[$key] == $query ) 
				{
					$a[$key] = 0;
					$key = 0;
				}
			}
		
			// Verify the essencial values
			if ( !$a['table'] )
				return PEAR::raiseError( "It was not possible to identify the table." );

			$result = $this->delete(
				$a['table'],
				$a['where'],
				$db
			);
		}
		// Instruct for CREATE DATABASE
		else if ( preg_match( "/^[\040]*CREATE[\040]+DATABASE.*$/s", $query ) ) 
		{
			$a['name'] = preg_replace( "/[\040]*CREATE[\040]+DATABASE[\040]+'?([A-Z,a-z,0-9]+)'?[\040]*/s","\\1", $query );
		
			if ( $a['name'] == $query )
				return PEAR::raiseError( "It was not possible to identify the name of the database." );
	
			$result = $this->createDB( $a['name'] );
		}
		// Instruct for CREATE TABLE
		else if ( preg_match( "/^[\040]*CREATE[\040]+TABLE.*$/s", $query ) ) 
		{
			$db = $this->_returnDB( $db );
	
			if ( PEAR::isError( $db ) )
				return $db;
		
			$a['name'] = preg_replace( "/[\040]*CREATE[\040]+TABLE[\040]+'?([A-Z,a-z,0-9]+)'?[\040]*.*/s","\\1", $query );
			$a['info'] = preg_replace( "/[\040]*CREATE[\040]+TABLE[\040]+'?[A-Z,a-z,0-9]+'?[\040]+(,?'.+',?)+[\040]*/s","\\1", $query );

			if ( $a['name'] == $query || $a['info'] == $query )
				return PEAR::raiseError( "It was not possible to recognize the values." );
		
			$result = $this->createTable(
				$a['name'],
				$a['info'],
				$db
			);
		}
		// Instruct for DROP DATABASE
		else if ( preg_match( "/^[\040]*DROP[\040]+DATABASE.*$/s", $query ) ) 
		{
			$name = preg_replace( "/[\040]*DROP[\040]+DATABASE[\040]+'?([A-Z,a-z,0-9]+)'?[\040]*/s","\\1", $query );
		
			if ( $name == $query )
				return PEAR::raiseError( "It was not possible to recognize the name of the database." );
		
			$result = $this->dropDB( $name );
		}
		// Instruct for DROP TABLE
		else if ( preg_match( "/^[\040]*DROP[\040]+TABLE.*$/s", $query ) ) 
		{
			$db = $this->_returnDB( $db );
		
			if ( PEAR::isError( $db ) )
				return $db;
		
			$name = preg_replace( "/[\040]*DROP[\040]+TABLE[\040]+'?([A-Z,a-z,0-9]+)'?[\040]*/s","\\1", $query );
		
			if ( $name == $query )
				return PEAR::raiseError( "It was not possible to recognize the name of the table." );
		
			$result = $this->dropTable( $name, $db );
		}
		// Instruct for LIST DATABASES
		else if ( preg_match( "/^[\040]*LIST[\040]+DATABASES.*$/s", $query ) ) 
		{
			$result = $this->listDB();
		}
		// Intruct for LIST TABLES
		else if ( preg_match( "/^[\040]*LIST[\040]+TABLES.*$/s", $query ) ) 
		{
			$db = $this->_returnDB( $db );
		
			if ( PEAR::isError( $db ) )
				return $db;
		
			$result = $this->listTables( $db );
		}
		// Instruction not recognized
		else 
		{
			return PEAR::raiseError( "Could not recognize your instruction." );
		}

		return $result;
 	}

 	/**
	 * Returns the numbers of results of one research.
	 *
	 * @access public
	 */
 	function numRows( $key ) 
	{
 		$results = explode( ",", $this->_results[$key]['hands'] );
	
		if ( $results[0] == null )
			$num = (int)0;
		else
 			$num = sizeof( $results );
	
		return $num;
 	}

	/**
	 * Reloads a table.
	 *
	 * @access public
	 */
 	function reloadTable( $table, $db = 0 ) 
	{
		$db = $this->_returnDB( $db );
	
		if ( PEAR::isError( $db ) )
			return $db;
		
		unset( $this->_t_info[$db][$table] );
		unset( $this->_t_data[$db][$table] );
		
		$res = $this->_loadTable( $table, $db );
	
		if ( PEAR::isError( $res ) )
			return $res;
		
		return true;
 	}

 	/**
	 * Returns one string.
	 *
	 * @access public
	 */
 	function result( $key, $row, $field ) 
	{
		// Tests if the keys exists
		if ( !$this->_results[$key] )
			return PEAR::raiseError( "It was not possible to find the key for the result." );

 		// Select the hands
 		$hands = explode( ",", $this->_results[$key]['hands'] );
		$hand  = $hands[$row];
	
		// Selects the hands
		$db = $this->_results[$key]['db'];
		$table = $this->_results[$key]['table'];

		// Checks if the field exists
		if ( !$this->_t_data[$db][$table][$hand] )
			return PEAR::raiseError( "The result line " . $row . " does not exists." );
	
		$res = $this->_checkFields( $table, $field, $db );
	
		if ( PEAR::isError( $res ) )
			return $res;
		
		// Find and returns the values
		$value = $this->_t_data[$db][$table][$hand][$field];

		return $value;
 	}

 	/**
	 * Random a value.
	 *
	 * @access public
	 */
 	function salt( $num ) 
	{
 		mt_srand( (double)microtime() * 1000000 );
	
		$chars = array_merge( 
			range( 'a', 'z' ), 
			range( 'A', 'Z' ), 
			range( 0, 9 ) 
		);
	
		$salt = null;
	
		for ( $i = 0; $i < $num; $i++ )
			$salt .= $chars[mt_rand( 0, count( $chars ) - 1 )];
	
		return $salt;
 	}

 	/**
	 * Make the select.
	 *
	 * @access public
	 */
 	function select( $table, $fields, $where = 0, $order = 0, $limit = 0, $db = 0 ) 
	{
		$db = $this->_returnDB( $db );
	
		if ( PEAR::isError( $db ) )
			return $db;
	
		$res = $this->_checkLoadTable( $table, $db );

		if ( PEAR::isError( $res ) )
			return $res;
		
		// Creates the string with the results
		$hands = $this->_makeWhere( $where, $table, $db );

		if ( PEAR::isError( $hands ) )
			return $hands;
		
		$hands = $this->_makeOrder( $order, $hands, $table, $db );

		if ( PEAR::isError( $hands ) )
			return $hands;
		
		$hands = $this->_makeLimit( $hands, $limit );

		if ( PEAR::isError( $hands ) )
			return $hands;
		
		// Generates one key
		while ( $key = $this->salt( 5 ) ) 
		{
			if ( !$this->_results[$key] )
				break;
		}

		// Saves the results
		if ( strpos( $hands, "," ) )
			$this->_results[$key]['hands'] = $hands;
		else
			$this->_results[$key]['hands'] = $hands;
	
		$this->_results[$key]['table']  = $table;
		$this->_results[$key]['db']     = $db;
		$this->_results[$key]['fields'] = $fields;
	
		// Creates the hand
		$this->_hands[$key] = (int)0;

		return $key;
 	}

 	/**
	 * Selects the database.
	 *
	 * @access public
	 */
 	function selectDB( $db ) 
	{
 		$res = $this->_checkDB( $db );
 	
		if ( PEAR::isError( $res ) )
			return $res;

			$this->_db = $db;
			return true;
 	}

 	/**
	 * Updates values of the table.
	 *
	 * @access public
	 */
 	function update( $table, $changes, $where, $db = 0 ) 
	{
		$db = $this->_returnDB( $db );
	
		if ( PEAR::isError( $db ) )
			return $db;
		
		$res = $this->_checkLoadTable( $table, $db );

		if ( PEAR::isError( $res ) )
			return $res;
		
		// Split the changes
		$changes = explode( ",", $changes );

		foreach ( $changes as $each ) 
		{
			// Splits field and new value
			list( $field, $value ) = explode( "=", $each );
			
			$size_1 = strlen( $value );
			$value  = substr( $value, 1, $size_1 );
			$value  = substr( $value, 0, $size_1 - 2 );
			
			$fields[] = $field;
			$values[] = $value;
		}

		// Selects the hands
		$hands = $this->_makeWhere( $where, $table, $db );
	
		if ( PEAR::isError( $hands ) )
			return $hands;
		
		$size_2 = sizeof( $fields );
		$hands  = explode( ",", $hands );
		
		if ( sizeof( $hands ) == 1 ) 
		{
			if ( $hands[0] == null )
				return PEAR::raiseError( "Could not find any results to be updated." );
		}

		// Updates the memory
		foreach ( $hands as $hand ) 
		{
			for ( $i = 0; $i < $size_2; $i++ ) 
			{
				$field = $fields[$i];
				$type  = $this->_makeType( $field, $table, $db );
	
				if ( PEAR::isError( $type ) )
					return $type;
				
				// Checks if the field can be null
				$res = $this->_checkNull( $values[$i], $field, $type['other'] );

				if ( PEAR::isError( $res ) )
					return $res;
				
				// Checks the type
				$res = $this->_checkType( $values[$i], $field, $table, $db );
			
				if ( PEAR::isError( $res ) )
					return $res;
				
				$values[$i] = $res;

				// Checks the size
				$res = $this->_checkSize( $field, $values[$i], $type['size'] );

				if ( PEAR::isError( $res ) )
					return $res;
		
				// Checks if it is key
				if ( eregi( "key", $type['other'] ) ) 
				{
					$res = $this->_checkKey( $values[$i], $field, $table, $db );
					
					if ( PEAR::isError( $res ) )
						return $res;
				}
			
				$this->_t_data[$db][$table][$hand][$field] = $this->convert( $values[$i], "out" );
			}
		}

		// Updates the file
		$res = $this->_saveTable( $table, $db, $db, $table );

		if ( PEAR::isError( $res ) )
			return $res;
		
		return true;
 	}
 
 
 	// private methods
 
 	/**
	 * Executes the auto_increment.
	 *
	 * @access private
	 */
 	function _makeIncrement( $value, $field, $table, $db = 0 ) 
	{
		$db = $this->_returnDB( $db );
	
		if ( PEAR::isError( $db ) )
			return $db;
		
		$select = $this->select( $table, $field, 0, 0, 0, 0 );
	
		if ( PEAR::isError( $select ) )
			return $select;
		
		while ( $row = $this->fetchArray( $select ) )
			$num[] = $row[$field];

		natsort( $num );
		$last = array_pop( $num );
	
		if ( $last >= $value ) 
		{
			$last++;
			return $last;
		}
		else 
		{
			return $value;
		}
 	}
 
 	/**
	 * Check if the databases exists.
	 *
	 * @access private
	 */
 	function _checkDB( $db ) 
	{
 		if ( is_dir( $this->_path . DIRECTORY_SEPARATOR . $db ) )
			return true;
		else
			return PEAR::raiseError( "The database " . $db . " does not exists." );
 	}
 
 	/**
	 * Check if the table exists.
	 *
	 * @access private
	 */
 	function _checkTable( $table, $db = 0 ) 
	{
 		$db = $this->_returnDB( $db );
 
 		if ( PEAR::isError( $db ) )
			return $db;
	
		$path = $this->_path . DIRECTORY_SEPARATOR . $db . DIRECTORY_SEPARATOR;

		if ( file_exists( $path . $table ) && file_exists( $path . $table . "_info" ) )
			return true;
		else
			return PEAR::raiseError( "The table " . $table . " does not exists." );
 	}
 
	/**
	 * Check if the table is loaded in memory.
	 *
	 * @access private
	 */
 	function _checkLoadTable( $table, $db = 0 ) 
	{
 		$db = $this->_returnDB( $db );
	
		if ( PEAR::isError( $db ) )
			return $db;
		
 		if ( !$this->_t_data[$db][$table] ) 
		{
			if ( !PEAR::isError( $this->_checkTable( $table, $db ) ) ) 
			{
				$res = $this->_loadTable( $table, $db );
			
				if ( PEAR::isError( $res ) )
					return $res;
			}
			else 
			{
				return PEAR::raiseError( "The table " . $table . " does not exists in the database " . $db );
			}
		}
 	}
 
 	/**
	 * Check if the given fields exists.
	 *
	 * @access private
	 */
 	function _checkFields( $table, $fields, $db = 0 ) 
	{
 		$db = $this->_returnDB( $db );

		if ( PEAR::isError( $db ) )
			return $db;
		
		// Tests if the table exists
		if ( PEAR::isError( $this->_checkTable( $table, $db ) ) )
			return PEAR::raiseError( "Could not find the table." );

		// Tests if the table is loaded
		if ( !$this->_t_info[$db][$table] ) 
		{
			$this->_loadTable( $table, $db );
		
			if ( PEAR::isError( $res ) )
				return $res;
		}

		// Selects all the fields
		foreach ( $this->_t_info[$db][$table]['fields'] as $field )
			$fields_correct[$field] = $field;

		// Testts if the given fields exists
		$fields_array = explode( ",", $fields );
	
		foreach ( $fields_array as $field ) 
		{
			if ( !empty( $field ) && $field != "0" ) 
			{
				if ( !$fields_correct[$field] )
					$errors[] = $field;
			}
		}
	
		// Show error messege or returns true
		if ( !$errors ) 
		{
			return true;
		}
		else 
		{
			$msg  = "The fields ";
			$size = sizeof( $errors ) - 1;
			$i    = 0;

			foreach ( $errors as $error ) 
			{
				$msg .= $error;
			
				if ( $size > $i ) 
				{
					$msg .= ", ";
					$i++;
				}
			}
		
			$msg .= " do not exist.";
			return PEAR::raiseError( $msg );
		}
 	}
 
 	/**
	 * Checks if the table is locked.
	 *
	 * @access private
	 */
 	function _checkLock( $table, $db = 0 ) 
	{
		$db = $this->_returnDB( $db );
	
		if ( PEAR::isError( $db ) )
			return $db;
		
		if ( file_exists( $this->_path . DIRECTORY_SEPARATOR . $db . DIRECTORY_SEPARATOR . $table . "_lock" ) )
			return PEAR::raiseError( "The table " . $table . " is locked. Please, try again." );
		else
			return true;
 	}
 
	/**
	 * Checks if the values already exists in a field configured like key.
	 *
	 * @access private
	 */
 	function _checkKey( $value, $field, $table, $db = 0 ) 
	{
		$db = $this->_returnDB( $db );
	
		if ( PEAR::isError( $db ) )
			return $db;
		
		$select = $this->select( $table, $field, 0, 0, 0, 0 );
	
		if ( PEAR::isError( $select ) )
			return $select;
		
		while ( $row = $this->fetchArray( $select ) ) 
		{
			if ( $row[$field] == $this->convert( $value, "out" ) )
				return PEAR::raiseError( "The value " . $value . " sent to the field " . $field . " already exists. The field " . $field . " is configured as key, so it does not accepts repeated values." );
		}
		
		return true;
 	}
 
 	/**
	 * Checks the size.
	 *
	 * @access private
	 */
 	function _checkSize( $field, $value, $max ) 
	{
 		if ( $max != null ) 
		{
			$length = strlen( $this->convert( $value, "out" ) );
		
			if ( $length > $max )
				return PEAR::raiseError( "The field " . $field . " can have only " . $max . " characters. You tried to insert a value with " . $length . " characters." );
		}
 	}
 
 	/**
	 * Checks the type.
	 *
	 * @access private
	 */
 	function _checkType( $value, $field, $table, $db ) 
	{
 		$db = $this->_returnDB( $db );
	
		if ( PEAR::isError( $db ) )
			return $db;
		
		$type = $this->_makeType( $field, $table, $db );

		if ( PEAR::isError( $type ) )
			return $type;
		
		if ( ( eregi( "null", $type['other'] ) && $value != null ) || !eregi( "null", $type['other'] ) ) 
		{
			if ( $type['type'] == "int" ) 
			{
				if ( is_numeric( $value ) && !eregi( "\.", $value ) )
					$value = (int)$value;
				else
					return PEAR::raiseError( "The sent value is not integer. The field " . $field . " has to have an integer value." );

				if ( eregi( "auto_increment", $type['other'] ) ) 
				{
					$value = $this->_makeIncrement( $value, $field, $table, $db );
			
					if ( PEAR::isError( $value ) )
						return $value;
				}
			}
		}

		return $value;
 	}

 	/**
	 * Checks if the field can be null.
	 *
	 * @access private
	 */
 	function _checkNull( $value, $field, $type ) 
	{
		if ( !eregi( "null", $type ) ) 
		{
			if ( $value == null )
				return PEAR::raiseError( "The sent value is null. The field " . $field . " does not accepts a null value." );
		}
 	}
 
	/**
	 * Returns the name of the database.
	 *
	 * @access private
	 */
 	function _returnDB( $db = 0 ) 
 	{
 		if ( !$db && $this->_checkDB( $this->_db ) )
			return $this->_db;
		else if ( !PEAR::isError( $this->_checkDB( $db ) ) )
			return $db;
		else
			return PEAR::raiseError( "An error ocurred with the database." );
 	}
 
 	/**
	 * Loads the content of a table into memory.
	 *
	 * @access private
	 */
 	function _loadTable( $table, $db = 0 ) 
	{
		$db = $this->_returnDB( $db );
 
 		if ( PEAR::isError( $db ) )
			return $db;
		
 		if ( !PEAR::isError( $this->_checkTable( $table ) ) ) 
		{
			$res = $this->_checkLock( $table, $db );
		
			if ( PEAR::isError( $res ) )
				return $res;
		
			$res = $this->_lockTable( $table, $db );
		
			if ( PEAR::isError( $res ) )
				return $res;
		
			// Reads the data file
			$file  = $this->_path . DIRECTORY_SEPARATOR . $db . DIRECTORY_SEPARATOR . $table;
			$fopen = fopen( $file, "r" );
			$rows  = explode( "\n", fread( $fopen, filesize( $file ) ) );
			fclose( $fopen );

			//Reads the information file
			$file  = $this->_path . DIRECTORY_SEPARATOR . $db . DIRECTORY_SEPARATOR . $table . "_info";
			$fopen = fopen( $file, "r" );
			$info  = explode( "\n", fread( $fopen, filesize( $file ) ) );
			fclose( $fopen );

			$res = $this->_unlockTable( $table, $db );

			if ( PEAR::isError( $res ) )
				return $res;
		
			// Loads the information into $t_info
			$size_1 = sizeof( $info ) - 1;
		
			for ( $z = 0; $z < $size_1; $z++ ) 
			{
				$info[$z] = trim( $info[$z] );
				list( $name, $type ) = explode( " -> ", $info[$z] );
			
				$this->_t_info[$db][$table]['fields'][] = $name;
				$this->_t_info[$db][$table]['types'][$name] = $type;
			}
				
			// Edits line by line
			$size_2 = sizeof( $rows ) - 1;
			$n = 0;
		
			for ( $i = 0; $i < $size_2; $i++ ) 
			{
				$rows[$i] = trim( $rows[$i] );
				$values   = explode( "','", $rows[$i] );

				// Insert the data in the object
				$size_3 = sizeof( $values );
			
				for ( $j = 0; $j < $size_3; $j++ ) 
				{
					$param = $this->_t_info[$db][$table]['fields'][$j];
					$this->_t_data[$db][$table][$n][$param] = $this->convert( $values[$j], "out" );
				}
				
				$n++;
			}
		}
		else 
		{
			return PEAR::raiseError( "The table " . $table . " does not exists." );
		}
 	}
 
 	/**
	 * Saves the table of the memory into a file.
	 *
	 * @access private
	 */
 	function _saveTable( $table, $db = 0, $to_db = 0, $to_tb = 0 ) 
	{
 		$db = $this->_returnDB( $db );
	
		if ( PEAR::isError( $db ) )
			return $db;
		
		if ( !$to_db )
			$to_db = $db;
	
		if ( !$to_tb )
			$to_tb = $table;

		$size_1 = sizeof( $this->_t_data[$db][$table] );

		for ( $i = 0; $i < $size_1; $i++ ) 
		{
			if ( $this->_t_data[$db][$table][$i] ) 
			{
				$size_2 = sizeof( $this->_t_data[$db][$table][$i] );
			
				for ( $j = 0; $j < $size_2; $j++ ) 
				{
					$field  = $this->_t_info[$db][$table]['fields'][$j];
					$save  .= $this->convert( $this->_t_data[$db][$table][$i][$field] );
				
					if ( $j != $size_2 - 1 )
						$save .= "','";
				}
				
				$save .= "\n";
			}
		}
	
		$res = $this->_checkLock( $to_tb, $to_db );
	
		if ( PEAR::isError( $res ) )
			return $res;
		
		$res = $this->_lockTable( $to_tb, $to_db );
	
		if ( PEAR::isError( $res ) )
			return $res;
		
		// Saves in the file
		$file  = $this->_path . DIRECTORY_SEPARATOR . $to_db . DIRECTORY_SEPARATOR . $to_tb;
		$fopen = fopen( $file, "w+" );
		$fsave = fwrite( $fopen, $save );
		fclose( $fopen );
		@chmod( $file, 0666 );

		return $this->_unlockTable( $to_tb, $to_db );
 	}
 
 	/**
	 * Locks one table.
	 *
	 * @access private
	 */
 	function _lockTable( $table, $db = 0 ) 
	{
 		$db = $this->_returnDB( $db );

		if ( PEAR::isError( $db ) )
			return $db;
		
		$file  = $this->_path . DIRECTORY_SEPARATOR . $db . DIRECTORY_SEPARATOR . $table . "_lock";
		$fopen = fopen( $file, "w+" );
		$fsave = fwrite( $fopen, "Locked Table" );
		fclose( $fopen );
		@chmod( $file, 0666 );
 }
 
 	/**
	 * Executes the ORDER.
	 *
	 * @access private
	 */
 	function _makeOrder( $order, $hands, $table, $db = 0 ) 
	{
		$db = $this->_returnDB( $db );

		if ( PEAR::isError( $db ) )
			return $db;
		
		if ( $order ) 
		{
			list( $fields, $type ) = explode( " | ", $order );

			// Checks the fields
			if ( empty( $fields ) && $fields != "0" ) 
			{
				return PEAR::raiseError( "You did not type the fields." );
			}
			else 
			{
				$res = $this->_checkFields( $table, $fields, $db );
			
				if ( PEAR::isError( $res ) )
					return $res;
			}

			$fields_array = explode( ",", $fields );
			$hands = explode( ",", $hands );

			// Creates the order array
			foreach ( $hands as $i ) 
			{
				foreach ( $fields_array as $field ) 
				{
					$type_2 = $this->_makeType( $field, $table, $db );
				
					if ( PEAR::isError( $type2 ) )
						return $type2;
					
					if ( $type_2[type] == "int" )
						$value .= $this->_t_data[$db][$table][$i][$field];
					else
						$value .= substr( $this->_t_data[$db][$table][$i][$field], 0, 1 );
				}
				
				$order_a[$i] = $value;
				unset( $value );
			}

			// Order
			natsort( $order_a );

			// Selects the keys
			while ( list( $key, $value ) = each( $order_a ) )
				$hands_a[] = $key;

			if ( strtoupper( $type ) == "DESC" )
				$hands_a = array_reverse( $hands_a );
		
			$hands = implode( ",", $hands_a );
		}

		return $hands;
 	}
 
 	/**
	 * Returns the result with LIMIT.
	 *
	 * @access private
	 */
 	function _makeLimit( $hands, $limit ) 
	{
 		$hands  = explode( ",",$hands );
		$size_1 = sizeof( $hands ) - 1;
 
		// Make the limits
		if ( $limit ) 
		{
			$limits = explode( ",", $limit );
		
			if ( sizeof( $limits ) == 2 ) 
			{
				$initial = $limits[0];
				$final   = $limits[1];
			}
			else 
			{
				$initial = 0;
				$limits[0]--;
				$final = $limits[0];
			}

			// Checks if the given limits are possible and try to correct
			if ( $initial>$final )
				return PEAR::raiseError( "The typed limits are invalids." );
		
			
			$dif = $final - $initial;
		
			if ( $size_1 < $final )
				$final = $size_1;
		
			if ( $size_1 < $initial )
				$initial = $final - $dif;
		
			if ( $initial < 0 )
				$initial = 0;
		}
		else 
		{
			$initial = 0;
			$final = $size_1;
		}

		// Creates and returns the values
		$values = range( $initial, $final );
	
		foreach ( $values as $value )
			$response[] = $hands[$value];
	
		$hands = implode( ",", $response );
		return $hands;
 	}
 
 	/**
	 * Returns the type of the field.
	 *
	 * @access private
	 */
 	function _makeType( $field, $table, $db = 0 ) 
	{
		$db = $this->_returnDB( $db );
	
		if ( PEAR::isError( $db ) )
			return $db;
		
		$res = $this->_checkFields( $table, $fields, $db );

		if ( PEAR::isError( $res ) )
			return $res;
		
		$content = $this->_t_info[$db][$table]['types'][$field];

		// Creates the array with the type
		$type['type']  = preg_replace( "/^[\040]*([A-Z,a-z,0-9]+)[\040]*.*/si","\\1", $content );
		$type['size']  = preg_replace( "/^[\040]*[A-Z,a-z,0-9]+[\040]*\(?([0-9]+)?\)?.*/si","\\1", $content );
		$type['other'] = preg_replace( "/^[\040]*[A-Z,a-z,0-9]+[\040]*\(?[0-9]*\)?([\040]+)?([A-Z,a-z,\040,\137]+)?[\040]*$/si","\\2", $content );
	
		return $type;
 	}
 
 	/**
	 * Executes the WHERE.
	 *
	 * @access private
	 */
 	function _makeWhere( $clause = 0, $table, $db = 0 ) 
	{
 		$db = $this->_returnDB( $db );
	
		if ( PEAR::isError( $db ) )
			return $db;
		
		$size_1  = sizeof( $this->_t_data[$db][$table] ) - 1;
		$initial = 0;
		$final   = $size_1;

		if ( !$clause ) 
		{
			if ( $final < 0 )
				$hands = "";
			else
				$hands = range( $initial, $final );
		}
		else 
		{		
			$fields = preg_replace( "/.*([A-Z,a-z,0-9]+)[\040]*([\075]|[\041][\075]|[\074]|[\076]|[\074][\075]|[\076][\075]|[\045]|[\041][\045])[\040]*'(.*)'.*/Usi","\\1,", $clause );
			$clause = preg_replace( "/([A-Z,a-z,0-9]+)[\040]*([\075]|[\041][\075]|[\074]|[\076]|[\074][\075]|[\076][\075]|[\045]|[\041][\045])[\040]*'(.*)'/Usi","\$\\1\\2\"\\3\"", $clause );
			$clause = preg_replace( "/[\044]([A-Z,a-z,0-9]+)[\075]\"(.*)\"/Usi","strtolower(\$this->_t_data[\$db][\$table][\$i][\\1])==strtolower(\$this->convert(\"\\2\",\"out\"))", $clause );
			$clause = preg_replace( "/[\044]([A-Z,a-z,0-9]+)[\041][\075]\"(.*)\"/Usi","strtolower(\$this->_t_data[\$db][\$table][\$i][\\1])!=strtolower(\$this->convert(\"\\2\",\"out\"))", $clause );
			$clause = preg_replace( "/[\044]([A-Z,a-z,0-9]+)([\074]|[\076]|[\074][\075]|[\076][\075])\"(.*)\"/Usi","\$this->_t_data[\$db][\$table][\$i][\\1] \\2 \$this->convert(\"\\3\",\"out\")", $clause );
			$clause = preg_replace( "/\[\\\\(\d{3})\]/si","[\\\\\\\\\\1]", $clause );
			$clause = preg_replace( "/[\044]([A-Z,a-z,0-9]+)([\041])?[\045]\"(.*)\"/Usi","\\2@is_integer(strpos(strtolower(\$this->_t_data[\$db][\$table][\$i][\\1]),strtolower(\$this->convert(\"\\3\",\"out\"))))", $clause );

			// Tests if the fields exists
			$fields = substr( $fields, 0, strlen( $fields ) - 1 );
		
			$res = $this->_checkFields( $table, $fields, $db );

			if ( PEAR::isError( $res ) )
				return $res;
		
			// Creates the command
			$command  = "if (" . $clause . ") {\n";
			$command .= "\$hands[] = \$i;\n";
			$command .= "}\n";

			for ( $i = $initial; $i <= $final; $i++ )
				eval( $command );
		}
	
		if ( $hands )
			$response = implode( ",", $hands );
		else
			$response = "";

		return $response;
 	}

 	/**
	 * Re-build the array of one table.
	 *
	 * @access private
	 */
 	function _rebuildArray( $table, $db = 0 ) 
	{
		$db = $this->_returnDB( $db );
	
		if ( PEAR::isError( $db ) )
			return $db;
		
		$temp   = $this->_t_data[$db][$table];
		$size_1 = sizeof( $this->_t_info[$db][$table]['fields'] );
	
		unset( $this->_t_data[$db][$table] );

		$n = (int)0;
		
		while ( list( $key, $array ) = each( $temp ) ) 
		{
			for ( $i = 0; $i < $size_1; $i++ ) 
			{
				$field = $this->_t_info[$db][$table]['fields'][$i];
				$this->_t_data[$db][$table][$n][$field] = $array[$field];
			}
			
			$n++;
		}
		
		return true;
 	}
 
	/**
	 * Re-build the results...executated after _rebuildArray() functions.
	 *
	 * @access private
	 */
 	function _rebuildResults( $mod, $table, $db = 0 ) 
	{
		$db = $this->_returnDB( $db );

		if ( PEAR::isError( $db ) )
			return $db;
		
		do 
		{
			$key = key( $this->_results );
		
			if ( $this->_results[$key]['table'] == $table && $this->_results[$key]['db'] == $db ) 
			{
				$mod_a   = explode( ",", $mod );
				$results = explode( ",", $this->_results[$key]['hands'] );
				$size_1  = sizeof( $results );
			
				foreach ( $mod_a as $mod ) 
				{
					for ( $i = 0; $i < $size_1; $i++ ) 
					{
						if ( $results[$i] >= $mod )
							$results[$i]--;
					}
				}
			
				$string = implode( ",", $results );
				$this->_results[$key]['hands'] = $string;
				unset( $results );
			}
		} while ( next( $this->_results ) );
	
		reset( $this->_results );
 	}
 
 	/**
	 * Open one table.
	 *
	 * @access private
	 */
 	function _unlockTable( $table, $db = 0 ) 
	{
		$db = $this->_returnDB( $db );
	
		if ( PEAR::isError( $db ) )
			return $db;
		
		unlink( $this->_path . DIRECTORY_SEPARATOR . $db . DIRECTORY_SEPARATOR . $table . "_lock" );
 	}
} // END OF DBFile

?>
