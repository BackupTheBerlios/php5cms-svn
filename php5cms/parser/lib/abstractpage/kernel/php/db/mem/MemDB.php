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


using( 'db.mem.MemDBUtil' );
using( 'db.mem.MemDBTable' );


define( "MEMDB_COL_UNDEFINDED",   0 );
define( "MEMDB_COL_TEXT",         1 );
define( "MEMDB_COL_INT",          2 );
define( "MEMDB_COL_FLOAT",        3 );
define( "MEMDB_COL_AUTOINC",      4 );
define( "MEMDB_CRYPT",           16 );
define( "MEMDB_SORT_ORDER_ASC",   0 );
define( "MEMDB_SORT_ORDER_DESC",  1 );


/**
 * MemDB is a really simple databaselike class.
 * You can create tables, columns and records.
 * There is no fancy errorchecking so you might run into 
 * some unexpected results if you're not careful.
 * MemDB can import and export commaseparated text.
 * MemDB can also load and save information from textstrings and files.
 * The textstring might be useful if the data is stored in another database.
 *
 * @todo encryption of entire database
 * @todo encryption of single table
 * @todo locate, find a record based on a fields value
 * @todo better query language
 *
 * Usage:
 *
 * $m = new MemDB();
 * $m->createTable( "nisse" );
 * $m->createTable( "pelle" );
 * $m->addColumn( "nisse", "first",  MEMDB_COL_INT,  0 );
 * $m->addColumn( "nisse", "second", MEMDB_COL_TEXT, "Pelle" );
 * $m->addRecord( "nisse" );
 * $m->setField( "nisse", "first", 5 );
 * $m->setFieldByIndex( "nisse", 1, 10 );
 * $m->addRecord( "nisse" );
 * $m->setField( "nisse", "first", 10 );
 * $m->setFieldByIndex( "nisse", 1, "Hej" );
 * 
 * echo "<PRE>";
 * $q = $m->query( "nisse", array( "second" => "==\"Hej\"" ) );
 * print_r( $q );
 * $t =& $m->getTable( "pelle" );
 * $t->addColumn( "x", MEMDB_COL_INT, 0 );
 * print_r( $m );
 * $m->delete( "nisse" );
 * print_r( $m );
 * echo "</PRE>";
 *
 * @package db_mem
 */
 
class MemDB extends PEAR
{
  	var $tables;
  
  
  	/**
	 * Constructor
	 */
  	function MemDB() 
	{
    	$this->tables = array();
  	}
  
  	/**
	 * Destructor
	 */
  	function _MemDB()
	{
		unset( $this->tables );
  	}
  
  
	function loadDatabase( $s, $filename = "", $key = "" ) 
	{
    	if ( strlen( $filename ) > 0 ) 
		{
	  		if ( file_exists( $filename ) )
	    		$s = file_get_contents( $filename );
		}
    
		if ( strpos( $s, "BTN" ) === 0 ) 
		{
	  		if ( function_exists( "md5" ) ) 
			{
	    		$s = MemDBUtil::_simpleDecrypt( substr( $s, 3, strlen( $s ) ), $key );
	  		} 
			else 
			{
	    		// String is encrypted with BTN and there is no decryption function available
	  		}
		}
    
		if ( strpos( $s, "_CR" ) === 0 ) 
	  		$s = MemDBUtil::decrypt( $key, substr( $s, 3, strlen( $s ) ) );
		
    	if ( strpos( $s, "BZ" ) === 0 ) 
		{
	  		if ( function_exists( "bzdecompress" ) ) 
			{
	    		$s = bzdecompress( $s );
	  		} 
			else 
			{
	    		// String is packed with BZ2 and there is no decompression function available
	  		}
		}
	
		$this = unserialize( $s );
  	}
  
  	function saveDatabase( $filename = "" ) 
	{
		$object = preg_replace( "|\"cryptKey\";s\:.*\:\".*\";|Ui","\"cryptKey\";s:5:\"?????\";", serialize( $this ) );
    
		if ( strlen( $filename ) > 0 ) 
		{
	  		$fp = fopen( $filename, "w+" );
	  		fwrite( $fp, $object );
	  		fclose( $fp );
		} 
		else 
		{
     	 	return $object;
		}
  	}
  
  	function saveDatabaseBZ2( $filename = "" ) 
	{
		if ( function_exists( "bzcompress" ) ) 
	  		$object = bzcompress( preg_replace( "|\"cryptKey\";s\:.*\:\".*\";|Ui","\"cryptKey\";s:5:\"?????\";", serialize( $this ) ) );
		else 
	  		$object = preg_replace( "|\"cryptKey\";s\:.*\:\".*\";|Ui","\"cryptKey\";s:5:\"?????\";", serialize( $this ) );
    
		if ( strlen( $filename ) > 0 ) 
		{
	  		$fp = fopen( $filename, "w+" );
	  		fwrite( $fp, $object );
	  		fclose( $fp );
		} 
		else 
		{
      		return $object;
		}
  	}
  
  	function saveDatabaseBTN( $key, $filename = "" ) 
	{
		if ( function_exists( "bzcompress" ) ) 
	  		$object = bzcompress( preg_replace( "|\"cryptKey\";s\:.*\:\".*\";|Ui","\"cryptKey\";s:5:\"?????\";", serialize( $this ) ) );
		else 
	  		$object = preg_replace( "|\"cryptKey\";s\:.*\:\".*\";|Ui","\"cryptKey\";s:5:\"?????\";", serialize( $this ) );
    
		if ( strlen( $filename ) > 0 ) 
		{
	  		$fp = fopen( $filename, "w+" );
	  		fwrite( $fp, $object );
	  		fclose( $fp );
		} 
		else 
		{
      		return $object;
		}
  	}
  
  	function saveDatabaseBZ2BTN( $key, $filename = "" ) 
	{
		if ( function_exists( "bzcompress" ) ) 
	  		$object = bzcompress( preg_replace( "|\"cryptKey\";s\:.*\:\".*\";|Ui","\"cryptKey\";s:5:\"?????\";", serialize( $this ) ) );
		else 
	  		$object = preg_replace( "|\"cryptKey\";s\:.*\:\".*\";|Ui","\"cryptKey\";s:5:\"?????\";", serialize( $this ) );
			
		if ( function_exists( "md5" ) ) 
	  		$object = "BTN" . MemDBUtil::_simpleCrypt( $object, $key );
		
    	if ( strlen( $filename ) > 0 ) 
		{
	  		$fp = fopen( $filename, "w+" );
	  		fwrite( $fp, $object );
	  		fclose( $fp );
		} 
		else 
		{
      		return $object;
		}
  	}
  
  	function saveDatabaseCR( $key, $filename = "" ) 
	{
		$object = "_CR" . MemDBUtil::encrypt( $key, preg_replace( "|\"cryptKey\";s\:.*\:\".*\";|Ui","\"cryptKey\";s:5:\"?????\";", serialize( $this ) ) );
    
		if ( strlen( $filename ) > 0 ) 
		{
	  		$fp = fopen( $filename, "w+" );
	  		fwrite( $fp, $object );
	  		fclose( $fp );
		} 
		else 
		{
      		return $object;
		}
  	}
  
  	function saveDatabaseBZ2CR( $key, $filename = "" ) 
	{
		if ( function_exists( "bzcompress" ) ) 
	  		$object = bzcompress( preg_replace( "|\"cryptKey\";s\:.*\:\".*\";|Ui","\"cryptKey\";s:5:\"?????\";", serialize( $this ) ) );
		else 
	  		$object = preg_replace( "|\"cryptKey\";s\:.*\:\".*\";|Ui","\"cryptKey\";s:5:\"?????\";", serialize( $this ) );

		$object = "_CR" . MemDBUtil::encrypt( $key, $object );
    
		if ( strlen( $filename ) > 0 ) 
		{
	  		$fp = fopen( $filename, "w+" );
	  		fwrite( $fp, $object );
	  		fclose( $fp );
		} 
		else 
		{
      		return $object;
		}
  	}
  
  	function createTable( $tablename ) 
	{
    	$this->tables[] = new MemDBTable( $tablename );
  	}
  
  	function clear( $tablename ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
	
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex]->clear();
	
		return false;
  	}
  
  	function recordCount( $tablename ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
		
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex]->recordCount();
	
		return false;
  	}
  
  	function fieldCount( $tablename ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
	
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex]->fieldCount();
	
		return false;
  	}
  
  	function setCryptKey( $tablename, $key ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
		
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex]->setCryptKey( $key );
	
		return false;
  	}
  
  	function addColumn( $tablename, $colname, $coltype, $defaultvalue ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
	
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex]->addColumn( $colname, $coltype, $defaultvalue );
	
		return false;
  	}
  
  	function delete( $tablename ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
		
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex]->delete();
	
		return false;
  	}
  
  	function getTableIndex( $tablename ) 
	{
    	foreach ( $this->tables as $index => $table ) 
		{
	  		if ( $table->isTablename( $tablename ) )
	    		return $index;
		}
	
		return null;
  	}

  	function first( $tablename ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
	
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex]->first();
	
		return false;
  	}
    
  	function last( $tablename ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
	
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex]->last();
	
		return false;
  	}
    
  	function prev( $tablename ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
	
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex]->prev();
	
		return false;
  	}
    
  	function next( $tablename ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
	
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex]->next();
	
		return false;
  	}
    
  	function pos( $tablename, $index ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
	
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex]->pos( $index );
	
		return false;
  	}

  	function addRecord( $tablename ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
	
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex]->addRecord();
	
		return false;
  	}

  	function addRecordWithValue( $tablename, $fields ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
	
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex]->addRecordWithValue( $fields );
	
		return false;
  	}
  
  	function addRecordWithValueByIndex( $tablename, $fields ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
	
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex]->addRecordWithValueByIndex( $fields );
	
		return false;
  	}
  
  	function getField( $tablename, $fieldname ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
	
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex]->getField( $fieldname );
	
		return false;
  	}
  
  	function getFieldByIndex( $tablename, $fieldindex ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
		
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex]->getFieldByIndex( $fieldindex );
	
		return false;
  	}
  
  	function setField( $tablename, $fieldname, $value ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
	
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex]->setField( $fieldname, $value );
	
		return false;
  	}
  
  	function setFieldByIndex( $tablename, $fieldindex, $value ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
	
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex]->setFieldByIndex( $fieldindex, $value );
	
		return false;
  	}
  
  	function getFieldInfo( $tablename, $fieldname ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
	
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex]->getFieldInfo( $fieldname );
	
		return false;
  	}
  
  	function getFieldInfoByIndex( $tablename, $fieldindex ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
	
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex]->getFieldInfoByIndex( $fieldindex );
	
		return false;
  	}
  
  	function importCSV( $tablename, $csv, $delimiter, $preserveOld = true ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
	
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex]->importCSV( $csv, $delimiter, $preserveOld );
	
		return false;
  	}
  
  	function exportCSV( $tablename, $delimiter, $quotechar = "\"" ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
		
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex]->exportCSV( $delimiter, $quotechar );
	
		return false;
  	}
  
  	function &query( $tablename, $filter = null ) 
	{
    	$qtable = new MemDBTable( $tablename );
    	$tableindex = &$this->getTableIndex( $tablename );
		
		if ( $tableindex !== false ) 
		{
	  		if ( count( $filter ) == 0 ) 
			{
	    		$table->cols = $this->tables[$tableindex]->cols;
	  		} 
			else 
			{
	    		foreach ( $filter as $fieldname => $s )
		  			$qtable->cols[] = $this->tables[$tableindex]->cols[$this->tables[$tableindex]->getFieldIndex( $fieldname )];
	  		}
	  
	  		if ( $this->tables[$tableindex]->first() ) 
			{
	    		do 
				{
		  			if ( is_array( $filter ) ) 
					{
		    			if ( count( $filter ) == 0 ) 
						{
		      				$qtable->addRecord();
		      				$qtable->setRow( $this->tables[$tableindex]->getRow() );
						} 
						else 
						{
			  				// check if record matches filter
			  				$valid = true;
			  
			  				foreach ( $filter as $field => $s ) 
							{
			    				$fieldindex = $this->tables[$tableindex]->getFieldIndex( $field );
								$value = $this->tables[$tableindex]->getFieldByIndex( $fieldindex );
								
		        				if ( $this->tables[$tableindex]->cols[$fieldindex][1] == MEMDB_COL_INT ) 
								{
		          					$value = intval( $value );
		        				} 
								else if ( $this->tables[$tableindex]->cols[$fieldindex][1] == MEMDB_COL_FLOAT ) 
								{
		          					$value = doubleval( $value );
		        				} 
								else if ( $this->tables[$tableindex]->cols[$fieldindex][1] == MEMDB_COL_TEXT ) 
								{
		          					$value = trim( $value );
	              
				  					if ( preg_match( "/^([\"\'])(.*)(\\1)$/U", $value, $match ) ) 
			       	 					$value = "\"" . $match[1] . "\"";
									else 
										$value = "\"" . $value . "\"";
		        				}
				
								eval( "\$valid &= (" . $value . $s . ");" );
				
								if ( $valid === false )
				  					break;
			  				}
			  
			  				if ( $valid == true ) 
							{
		        				$qtable->addRecord();
		        				$qtable->setRow( $this->tables[$tableindex]->getRow() );
			  				}
						}
		  			} 
					else 
					{
		    			$qtable->addRecord();
		    			$qtable->setRow( $this->tables[$tableindex]->getRow() );
		  			}
				} while ( $this->tables[$tableindex]->next() );
	  		}
		}
	
		$qtable->reIndex();
		$qtable->first();
		
		return $qtable;
  	}
  
  	function getTable( $tablename ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
	
		if ( $tableindex !== false )
	  		return $this->tables[$tableindex];
	
		return false;
  	}
  
  	function sortByIndex( $tablename, $sortorder ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
	
		if ( $tableindex !== false )
	  		$this->tables[$tableindex]->sortByIndex( $sortorder );
	
		return false;
  	}
  
  	function sort( $tablename, $sortorder ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
	
		if ( $tableindex !== false ) 
		{
	  		$table = &$this->tables[$tableindex];
	  		$table->sort( $sortorder );
		}
	
		return false;
  	}
  
  	function selectByIndex( $tablename, $select )
	{
    	$tableindex = &$this->getTableIndex( $tablename );
	
		if ( $tableindex !== false )
	  		$this->tables[$tableindex]->selectByIndex( $select );
	
		return false;
  	}
  
  	function select( $tablename, $select ) 
	{
    	$tableindex = &$this->getTableIndex( $tablename );
	
		if ( $tableindex !== false )
	  		$this->tables[$tableindex]->select( $select );
	
		return false;
  	}
} // END OF MemDB

?>
