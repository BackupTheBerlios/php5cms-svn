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
 * This class provides access to the common databases.
 * Set the variable $dbtype to any of the following values before including this library:
 * MySQL, mSQL, Postgres, PostgreSQL_local, ODBC, ODBC_Adabas, Interbase, Sybase
 *
 * // $dbtype = "MySQL";
 * // $dbtype = "mSQL";
 * // $dbtype = "PostgreSQL";
 * // $dbtype = "PostgreSQL_local"; // When postmaster start without "-i" option.
 * // $dbtype = "ODBC";
 * // $dbtype = "ODBC_Adabas";
 * // $dbtype = "Interbase";
 * // $dbtype = "Sybase";
 *
 * @package db
 */

class DBSimple extends PEAR
{
	/**
	 * @access public
	 */
	var $auto_increment; // for mySQL
	
	/**
	 * @access public
	 */
	var $dbtype = "";
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function DBSimple( $type = "MySQL", $inc = false )
	{
		$this->dbtype = strtolower( $type );
		$this->auto_increment = $inc;
	}


	/**
	 * @access public
	 */
	function connect( $host, $user, $password, $db )
	{	
		switch ( $this->dbtype )
		{
			case "mysql":
				$conn = @mysql_pconnect( $host, $user, $password );
				mysql_select_db( $db );
				return $conn;
			
				break;

			case "msql":
				$conn = @msql_pconnect( $host );
				msql_select_db( $db );
				return $conn;

				break;
              
			case "psql":
				$conn = @pg_pconnect( "host=$host user=$user password=$password port=5432 dbname=$db" );
				return $conn;
				
				break;
  
			case "psql_local":
				$conn = @pg_pconnect( "user=$user password=$password dbname=$db" );
				return $conn;
				
				break;
  
			case "odbc":
				$conn = @odbc_pconnect( $db, $user, $password );
				return $conn;  
			
				break;

			case "odbc_adabas":
				$conn = @odbc_pconnect( $host . ":" . $db, $user, $password );
				return $conn;  
				
				break;

			case "interbase":
				$conn = @ibase_connect( $host . ":" . $db, $user, $password );
				return $conn;
				
				break;

			case "sybase":
				$conn = @sybase_connect( $host, $user, $password );
				sybase_select_db( $db, $conn );
				return $conn;
			
				break;

			default:
				break;
		}
	}

	/**
	 * @access public
	 */
	function logout( $id )
	{
		switch ( $this->dbtype )
		{
	    	case "mysql":
    	    	$conn = @mysql_close( $id );
				return $conn;
			
				break;

			case "msql":
				$conn = @msql_close( $id );
				return $conn;
				
				break;

			case "psql":
    
			case "PostgreSQL_local":
				$conn = @pg_close( $id );
				return $conn;
				
				break;
  
			case "odbc":
		
			case "odbc_adabas":
				$conn = @odbc_close( $id );
				return $conn;  
				
				break;

			case "interbase":
				$conn = @ibase_close( $id );
				return $conn;
				
				break;

			case "sybase":
				$conn = @sybase_close( $id );
				return $conn;
				
				break;
  
			default:
				break;
		}
	}

	/** 
	 * Executes an SQL statement, returns a result identifier.
	 *
	 * @access public
	 */
	function query( $query, $id )
	{
		switch ($dbtype) 
		{
		    case "mysql":
        		$res = @mysql_query( $query, $id );
        		return $res;
	
				break;
    
			case "msql":
				$res = @msql_query( $query, $id );
				return $res; 
				
				break;

			case "psql":
		

			case "psql_local":
				$res = @pg_exec( $id, $query );
				return $res;
				
				break;
    
			case "odbc":
		
			case "odbc_adabas":
    	    	$res = @odbc_exec( $id, $query );
				return $res;  
				
				break;
  
			case "interbase":
				$res = @ibase_query( $id, $query );
				return $res;
				
				break;

			case "sybase":
				$res = @sybase_query( $query, $id );
				return $res;
				
				break;

			default :
				break;
    	}
	}
       
	/**
	 * Given a result identifier, returns the number of affected rows.
	 *
	 * @access public
	 */
	function numRows( $res )
	{
		switch ( $this->dbtype )
		{
	    	case "mysql":
  	  	    	$rows = mysql_num_rows( $res );
    	    	return $rows;
    		
				break;

		    case "msql":
		        $rows = msql_num_rows( $res );
        		return $rows;
    			
				break;
        
			case "psql":
    
			case "psql_local":
				$rows = pg_numrows( $res );
				return $rows;
				
				break;
        
			case "ODBC":
		
			case "odbc_adabas":
				$rows = odbc_num_rows( $res );
				return $rows;
				
				break;
        
			case "interbase":
				// $rows = ibase_numrows( $res );
				return PEAR::raiseError( "PHP doesn't support ibase_numrows." );

				break;

			case "sybase":
				$rows = sybase_num_rows( $res );
				return $rows; 
				
				break;

			default:
				break;
		}    
	}

	/**
	 * Given a result identifier, returns an array with the resulting row.
	 * Needs also a row number for compatibility with PostgreSQL.
	 *
	 * @access public
	 */
	function fetchRow( $res, $nr )
	{                                    
		switch ( $this->dbtype )
		{
    		case "mysql":                    
    	    	$row = mysql_fetch_row( $res );
        		return $row;
				
    			break;
                                     
			case "msql":                     
    		    $row = msql_fetch_row( $res ); 
    		    return $row;
				
				break;
                                     
			case "psql":

			case "psql_local":
				$row = pg_fetch_row( $res, $nr );
				return $row;                 
				
				break;
                                     
			case "odbc":
	
			case "odbc_adabas":
				$row  = array();
				$cols = odbc_fetch_into( $res, $nr, &$row );
				return $row;
				
				break;
                                     
			case "interbase":
				$row = ibase_fetch_row( $res );
				return $row;
				
				break;

			case "sybase":
				$row = sybase_fetch_row( $res );
				return $row;                 
				
				break;

			default :                         
				break;;                          
    	}                                
	}                                    
                                     
	/**
	 * Given a result identifier, returns an associative array
	 * with the resulting row using field names as keys.          
	 * Needs also a row number for compatibility with PostgreSQL.
	 *
	 * @access public
	 */
	function fetchArray( $res, $nr )
	{                                    
		switch ( $this->dbtype ) 
    	{
    		case "mysql":
        		$row = array();              
        		$row = mysql_fetch_array( $res );
        		return $row;
    		
				break;                     
                   
			case "msql":
				$row = array();
				$row = msql_fetch_array( $res );
				return $row;                 
			
				break;
                                     
			case "psql":
    
			case "psql_local":
				$row = array();
				$row = pg_fetch_array( $res, $nr );
				return $row;                 
			
				break;

			// ODBC doesn't have a native _fetch_array(), so we have to use a trick. Beware: this might cause HUGE loads!
			case "odbc":                     
        		$row    = array();              
        		$result = array();           
        		$result = odbc_fetch_row( $res, $nr );
				$nf     = odbc_num_fields( $res ); // field numbering starts at 1
       
	 	   		for( $count = 1; $count < $nf + 1; $count++ )
				{                        
            		$field_name  = odbc_field_name( $res, $count );
					$field_value = odbc_result( $res, $field_name );
					$row[$field_name] = $field_value;                          
        		}
        
				return $row;
    			break;

			case "odbc_adabas":                     
    	    	$row    = array();              
        		$result = array();           
        		$result = odbc_fetch_row( $res, $nr );
				$nf     = count( $result ) + 2; // field numbering starts at 1
	
				for ( $count = 1; $count < $nf; $count++ )
				{
					$field_name  = odbc_field_name( $res, $count );
					$field_value = odbc_result( $res, $field_name );
	    			$row[$field_name] = $field_value;
				}
        
				return $row;                 
    			break;

			case "interbase":
				$orow = ibase_fetch_object( $res );
				$row  = get_object_vars( $orow );
				return $row;
			
				break;

			case "sybase": 
				$row = sybase_fetch_array( $res );
				return $row;                 
				
				break;
		}                                
	}

	/**
	 * @access public
	 */
	function fetchObject( $res, $nr )
	{                                    
		switch ( $this->dbtype ) 
    	{
    		case "mysql":                    
        		$row = mysql_fetch_object( $res );
			
				if ( $row )
					return $row;
				else
					return false;
    
				break;
                                     
			case "msql":
    	    	$row = msql_fetch_object( $res );
	
				if ( $row )
					return $row;
				else
					return false;
    
				break;
                                     
			case "psql":
		
			case "psql_local":
        		$row = pg_fetch_object( $res, $nr );
	
				if ( $row )
					return $row;
				else
					return false;
    
				break;

			case "odbc":
    	    	$result = odbc_fetch_row( $res, $nr );
				
				if ( !$result )
					return false;
			
				// field numbering starts at 1
				$nf = odbc_num_fields( $res );
        
				for ( $count = 1; $count < $nf + 1; $count++ ) 
				{                        
            		$field_name  = odbc_field_name( $res, $count );
            		$field_value = odbc_result( $res, $field_name );             
            		$row->$field_name = $field_value;
        		}
        
				return $row;
    			break;

			case "odbc_adabas":
				$result = odbc_fetch_row( $res, $nr );
			
				if ( !$result )
					return false;    
			
				// field numbering starts at 1
				$nf = count( $result ) + 2;
	
				for ( $count = 1; $count < $nf; $count++ )
				{
	    			$field_name  = odbc_field_name( $res, $count );
	    			$field_value = odbc_result( $res, $field_name );
	    			$row->$field_name = $field_value;
				}
        
				return $row;                 
				break;

			case "interbase":
				$orow = ibase_fetch_object( $res );
	
				if ( $orow )
				{
					$arow = get_object_vars( $orow );
	    		
					while ( list( $name, $key ) = each( $arow ) )
					{
						$name = strtolower( $name );
						$row->$name = $key;
	    			}
				
					return $row;
				}
				else
				{
					return false;
				}
				
				break;

			case "sybase":
				$row = sybase_fetch_object( $res );
				return $row;                 
				
				break;
    	}
	}

	/**
	 * @access public
	 */
	function result( $res_id, $row, $mix )
	{
		switch ( $this->dbtype )
		{
	    	case "mysql":
    	    	$res = mysql_result( $res_id, $row, $mix );
				return $res;
			
				break;
    
			case "msql":
				$res = msql_result( $res_id, $row, $mix );
				return $res; 
				
				break;

			case "psql":
    
			case "psql_local":
				$res = pg_Result( $res_id, $row, $mix );
				return $res;
				break;
    
			case "odbc":
    	    	$rid = odbc_prepare( $id, $query );
        		$res = odbc_execute( $rid );
				return $res;  
				
				break;
  
			case "odbc_adabas":
        		$temp = @odbc_fetch_into( $res_id, $row + 1, &$arr );
        
				while ( list( $clef, $valeur ) = each( $arr ) )
				{
					$fieldname = odbc_field_name( $res_id, $clef + 1 );
					
					if ( $fieldname == strtoupper( $mix ) )
						$res = $valeur;
				}
			
				return $res;  
				break;
    
			case "interbase":
				return PEAR::raiseError( "PHP dosen't support ibase_result." );			
				break;

			case "sybase":
				$res = sybase_Result( $res_id, $row, $mix );
				return $res;
				
				break;

			default:
				break;
		}   
	}

	/**
	 * @access public
	 */
	function error( $id, $sql_string )
	{
		switch ( $this->dbtype )
		{
			case "mysql":
				return PEAR::raiseError( $sql_string . " - Error message from SQL-server: " . mysql_error() );
				break;

			case "msql":
				return PEAR::raiseError( $sql_string . " - Error message from SQL-server: " . msql_error() );
				break;

			case "psql":
    
			case "psql_local":
				return PEAR::raiseError( $sql_string . " - Error message from SQL-server: " . pg_errormessage( $id ) );			
				break;
  
			case "odbc":
		
			case "odbc_adabas":
				return PEAR::raiseError( $sql_string . " - Error message from SQL-server: " . "ODBC Error." );	
				break;

			case "interbase":
				return PEAR::raiseError( $sql_string . " - Error message from SQL-server: " . ibase_errmsg() );	
				break;;

			case "sybase":
				return PEAR::raiseError( $sql_string . " - Error message from SQL-server: " . "Sybase Error." );	
				break;

			default:
				break;
    	}
	}

	/**
	 * Export as CSV.
	 *
	 * @access public
	 */
	function csv( $csv, $csvfile, $f )
	{
		switch ($dbtype)
		{
			case "mysql":
				$query = stripslashes( $csv );
				$file  = fopen( $csvfile, "w" );
		
				list( $left, $etc )  = explode( "FROM", $query );
				list( $right, $end ) = explode( "LIMIT", $etc );
		
				$left   = ereg_replace( ",", ",\"\t\",", $left );
				$left   = ereg_replace( "SELECT", "SELECT CONCAT(", $left );
				$query  = $left . ") FROM " . $right;
				$result = $this->query( $query, $f );
				$num    = $this->numRows( $result );
		
				for ( $i = 0 ; $i < $num ; $i++ )
					fputs( $file, mysql_result( $result, $i ) . "\n" );
			
				fclose( $file );
				break;
		
			default :
				return PEAR::raiseError( "No CSV functionality for the specified dbtype." );
				break;
		}
	}

	/**
	 * @access public
	 */
	function fetchCols( $res, $id_col, $name_col )
	{
		$tmp_sel = array();
	
    	if ( $dbtype == "Interbase" )
    	{
			$i = 0;
			
			while ( $tmp_obj = $this->fetchObject( $res, $i++ ) )
				$tmp_sel[$tmp_obj->$id_col] = $tmp_obj->$name_col;
    	}
    	else
    	{
			for ( $i = 0; $i < $this->numRows( $res ); $i++ )
			{
	    		$tmp_name = $this->result( $res, $i, $id_col );
	    		$tmp_sel[$tmp_name] = $this->result( $res, $i, $name_col );
			}
    	}

		return $tmp_sel;                 
	}
	
	/**
	 * @access public
	 */	
	function fetchCol( $res, $name_col )
	{
		$tmp_sel = array();
	
    	if ( $dbtype == "Interbase" )
    	{
			$i = 0;
			
			while ( $tmp_obj = $this->fetchObject( $res, $i++ ) )
				$tmp_sel[]=$tmp_obj->$name_col;
	    }
    	else
    	{
			for ( $i = 0; $i < $this->numRows( $res ); $i++ )
				$tmp_sel[] = $this->result( $res, $i, $name_col );
    	}

		return $tmp_sel;
	}	

	/**
	 * @access public
	 */
	function getInsertedID( $id, $res, $table_name, $id_name )
	{
		switch ( $this->dbtype )
		{
	    	case "mysql":
				if ( $auto_increment )
				{
    	    		$iid = mysql_insert_id( $id );
    	    		return $iid;
				}    
    			
				break;
    
			case "msql":
				break;

			case "psql":
    
			case "psql_local":
				$iid        = pg_GetLastOid( $res );
				$sql_string = "SELECT $id_name FROM $table_name WHERE oid=$iid";
				$result     = $this->query( $sql_string, $id );
				
				return $this->result( $result, 0, $id_name );
				break;
    
			case "odbc":
				break;
  
			case "odbc_adabas":
				break;
    
			case "interbase":
				break;    
    	}   

	    $sql_string = "SELECT MAX($id_name) AS maxval FROM $table_name";
    	$result     = $this->query( $sql_string, $id );
    	$ores       = $this->fetchObject( $result, 0 );
	
    	return $ores->maxval;
	}
} // END OF DBSimple

?>
