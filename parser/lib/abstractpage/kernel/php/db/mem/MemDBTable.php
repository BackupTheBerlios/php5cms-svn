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


/**
 * @package db_mem
 */
 
class MemDBTable extends PEAR
{
	/**
	 * @access public
	 */
  	var $tablename;
	
	/**
	 * @access public
	 */
  	var $cols;
	
	/**
	 * @access public
	 */
  	var $rowindex;
	
	/**
	 * @access public
	 */
  	var $records;
	
	/**
	 * @access public
	 */
  	var $cryptKey;
    
  
  	/**
	 * Constructor
	 *
	 * @access public
	 */
  	function MemDBTable( $tablename ) 
	{
    	$this->tablename = $tablename;
		$this->cols      = array();
		$this->rowindex  = false;
		$this->records   = array();
    	$this->cryptKey  = "xxxxx";
  	}

  	/**
	 * Destructor
	 */
  	function _MemDBTable() 
	{
    	unset( $this->cols );
		unset( $this->records );
  	}
  

	/**
	 * @access public
	 */  
  	function clear()
	{
		$this->rowindex = false;
		$this->records  = array();
  	}
  
  	/**
	 * @access public
	 */
  	function recordCount()
	{
    	return count( $this->records );
  	}
  
  	/**
	 * @access public
	 */
  	function fieldCount()
	{
    	return count( $this->cols );
  	}
  	
	/**
	 * @access public
	 */
  	function isTablename( $tablename )
	{
    	return $this->tablename == $tablename;
  	}
		
  	/**
	 * @access public
	 */
  	function setCryptKey( $key ) 
	{
    	$this->cryptKey = $key;
  	}
  	
	/**
	 * @access public
	 */
	function addColumn( $colname, $coltype, $defaultvalue ) 
	{
		$colindex = 0;
    
		if ( count( $this->cols ) > 0 ) 
		{
	  		foreach ( $this->cols as $index => $col ) 
			{
	    		if ( $index > $colindex )
		  			$colindex = $index;
	  		}
	  
	  		$colindex++;
		}
	
		$this->cols[$colindex] = array(
			0 => $colname,
			1 => $coltype,
			2 => $defaultvalue
		);
		
		return $colindex;
	}
	
	/**
	 * @access public
	 */
	function delete()
	{
    	unset( $this->records[$this->rowindex] );
		$this->reIndex();
  	}
    
	/**
	 * @access public
	 */
  	function reIndex()
	{
    	$temp = array();
	
		if ( count( $this->records ) == 0 )
	  		return;
	
		foreach ( $this->records as $index => $record )
	  		$temp[] = $record;
	
		unset( $this->records );
		
		$this->records  = $temp;
		$this->rowindex = 0;
  	}
  
  	/**
	 * @access public
	 */
	function first()
	{
		if ( count( $this->records ) > 0 ) 
		{
	  		reset( $this->records );
	  		$this->rowindex = key( $this->records );
	  		return true;
		} 
		else 
		{
	  		$this->rowindex = false;
	  		return false;
		}
  	}
  	
	/**
	 * @access public
	 */
	function last() 
	{
		if ( count( $this->records ) > 0 ) 
		{
	  		end( $this->records );
	  		$this->rowindex = key( $this->records );
	  		return true;
		} 
		else 
		{
	  		$this->rowindex = false;
	  		return false;
		}
  	}
  
  	/**
	 * @access public
	 */
  	function prev()
	{
		if ( count( $this->records ) > 0 ) 
		{
	  		if ( prev( $this->records ) === false ) 
			{
	    		reset( $this->records );
	    		$this->rowindex = key( $this->records );
	    		return false;
	  		} 
			else 
			{
	    		$this->rowindex = key( $this->records );
	    		return true;
	  		}
		} 
		else 
		{
	  		$this->rowindex = false;
	  		return false;
		}
  	}
  	
	/**
	 * @access public
	 */
	function next()
	{
		if ( count( $this->records ) > 0 ) 
		{
	  		if ( next( $this->records ) === false ) 
			{
	    		end( $this->records );
	    		$this->rowindex = key( $this->records );
	    		return false;
	  		} 
			else 
			{
	    		$this->rowindex = key( $this->records );
	    		return true;
	  		}
		} 
		else 
		{
	  		$this->rowindex = false;
	  		return false;
		}
  	}
  
  	/**
	 * @access public
	 */
	function pos( $index ) 
	{
    	if ( isset( $this->records[$index] ) ) 
		{
	  		reset( $this->records );
	  		while ( key( $this->records ) !== $index )
			{
	    		if ( next( $this->records ) === false )
		  			break;
	  		}
	  
	  		return key( $this->records ) === $index;
		} 
		else 
		{
	  		return false;
		}
  	}
  	
	/**
	 * @access public
	 */
	function addRecord()
	{
    	$record = array();
	
		if ( count( $this->cols ) > 0 ) 
		{
	  		foreach ( $this->cols as $index => $col ) 
			{
	    		// Check if column is AutoInc. Then AutoInc :-)
	    		if ( $col[1] == MEMDB_COL_AUTOINC ) 
				{
		  			$col[2] ++;
		  			$this->cols[$index][2]++;
				}
	    
				$record[$index] = $col[2];
	  		}
		}
    
		$this->records[] = $record;
		end( $this->records );
		$this->rowindex = intval( key( $this->records ) );
  	}

	/**
	 * @access public
	 */
  	function addRecordWithValue( $fields ) 
	{
    	$this->addRecord();
	
		if ( count( $fields ) ) 
		{
	  		foreach ( $fields as $key => $value )
	    		$this->setField( $key, $value );
		}
  	}
    
	/**
	 * @access public
	 */
  	function addRecordWithValueByIndex( $fields ) 
	{
    	$this->addRecord();
	
		if ( count( $fields ) ) 
		{
	  		foreach ( $fields as $key => $value )
	    		$this->setFieldByIndex( $key, $value );
		}
  	}
    
	/**
	 * @access public
	 */
  	function getFieldIndex( $fieldname ) 
	{
    	foreach ( $this->cols as $index => $col ) 
		{
	  		if ( $col[0] === $fieldname )
	    		return $index;
		}
	
		return false;
  	}
  
  	/**
	 * @access public
	 */
  	function getField( $fieldname )
	{
    	foreach ( $this->cols as $index => $col ) 
		{
	  		if ( $col[0] === $fieldname ) 
			{
	    		if ( $col[1] == MEMDB_CRYPT )
	      			return MemDBUtil::decrypt( $this->records[$this->rowindex][$index] );
				else
	      			return $this->records[$this->rowindex][$index];
	  		}
		}
	
		return false;
  	}
  	
	/**
	 * @access public
	 */
  	function getFieldByIndex( $fieldindex ) 
	{
		if ( $this->cols[$fieldindex][1] == MEMDB_CRYPT ) 
      		return MemDBUtil::decrypt( $this->records[$this->rowindex][intval( $fieldindex )] );
		else 
     	 	return $this->records[$this->rowindex][intval( $fieldindex )];
  	}

	/**
	 * @access public
	 */
	function setField( $fieldname, $value ) 
	{
    	foreach ( $this->cols as $index => $col ) 
		{
	  		if ( $col[0] === $fieldname ) 
			{
	    		if ( $col[1] == MEMDB_CRYPT )
	      			return ( $this->records[$this->rowindex][intval( $index )] = MemDBUtil::encrypt( $value ) );
				else
	      			return ( $this->records[$this->rowindex][intval( $index )] = $value );
	  		}
		}
	
		return false;
  	}
  
  	/**
	 * @access public
	 */
  	function setFieldByIndex( $fieldindex, $value ) 
	{
		if ( $this->cols[$fieldindex][1] == MEMDB_CRYPT )
      		return ( $this->records[$this->rowindex][intval( $fieldindex )] = MemDBUtil::encrypt( $value ) );
		else
      		return ( $this->records[$this->rowindex][intval( $fieldindex )] = $value);
  	}
	
	/**
	 * @access public
	 */
  	function getRow() 
	{
    	return serialize( $this->records[$this->rowindex] );
  	}
  
  	/**
	 * @access public
	 */
  	function setRow( $rowdata ) 
	{
    	$this->records[$this->rowindex] = unserialize( $rowdata );
  	}
  
  	/**
	 * @access public
	 */
  	function getFieldInfo( $fieldname ) 
	{
    	foreach ( $this->cols as $index => $col ) 
		{
	  		if ( $col[0] === $fieldname )
	    		return $col;
		}
	
		return false;
  	}
  
  	/**
	 * @access public
	 */
  	function getFieldInfoByIndex( $fieldindex ) 
	{
    	return $this->cols[$fieldindex];
  	}

	/**
	 * @access public
	 */
  	function importCSV( $csv, $delimiter, $preserveOld = true ) 
	{
    	if ( $preserveOld === false )
	  		$this->clear();
	
		$lines = explode( "\n", $csv );
	
		foreach ( $lines as $row ) 
		{
	  		$cols = split( preg_quote( $delimiter ), $row );
	  		$this->addRecord();
	  
	  		foreach ( $cols as $index => $col ) 
			{
	    		$value = "";
		
				if ( $this->cols[$index][1] == MEMDB_COL_INT ) 
				{
		  			$value = intval( $col );
				} 
				else if ( $this->cols[$index][1] == MEMDB_COL_FLOAT ) 
				{
		  			$value = doubleval( $col );
				} 
				else if ( $this->cols[$index][1] == MEMDB_CRYPT ) 
				{
		  			// handle info to decrypt
				} 
				else if ( $this->cols[$index][1] == MEMDB_COL_TEXT ) 
				{
		  			$value = trim( $col );
		  
		  			if ( strpos( $value, "\"" ) === 0 ) 
					{
		   	 			if ( preg_match( "/^([\"\'])(.*)(\\1)$/U", $value, $match ) ) //'
			  				$value = $match[1];
		  			}
				}
		
				$this->setFieldByIndex( $index, $value );
	  		}
		}
  	}
  
  	/**
	 * @access public
	 */
  	function exportCSV( $delimiter, $quotechar = "\"" ) 
	{
    	$return = "";
    
		if ( count( $this->records ) > 0 ) 
		{
	  		foreach ( $this->records as $index => $record ) 
			{
	    		$line = "";
	    
				if ( count( $record ) > 0 ) 
				{
		  			foreach ( $record as $index => $col ) 
					{
		    			if ( strlen( $line ) > 0 )
		      				$line .= $delimiter;
		    
						if ( $this->cols[$index][1] == MEMDB_COL_INT ) 
						{
			  				$line .= $col;
						} 
						else if ( $this->cols[$index][1] == MEMDB_COL_FLOAT ) 
						{
			  				$line .= $col;
		    			} 
						else if ( $this->cols[$index][1] == MEMDB_CRYPT ) 
						{
			  				// handle info to encrypt
						} 
						else if ( $this->cols[$index][1] == MEMDB_COL_TEXT ) 
						{
			  				$line .= $quotechar . $col . $quotechar;
						}
		  			}
				}
		
				$return .= $line . "\n";
	  		}
		}
	
		return $return;
  	}

	/**
	 * @access public
	 */
  	function sortByIndex( $sortorder ) 
	{
		$GLOBALS['sortArray'] = $sortorder;
		$GLOBALS['sortCols']  = $this->cols;
    
		usort( &$this->records, "__sortCmp" );
		$this->reIndex();
  	}
  
  	/**
	 * @access public
	 */
  	function sort( $sortorder ) 
	{
		$newsortorder = array();
	
		if ( count( $sortorder ) > 0 ) 
		{
	  		foreach ( $sortorder as $key => $value )
	    		$newsortorder[$this->getFieldIndex( $key )] = $value;
		}

		$GLOBALS['sortArray'] = $newsortorder;
		$GLOBALS['sortCols']  = $this->cols;
    	
		usort( &$this->records, "__sortCmp" );
		$this->reIndex();
 	}
	
	/**
	 * @access public
	 */
  	function select( $select ) 
	{
		$newSelectFilter = array();
	
		if ( count( $select ) > 0 ) 
		{
	  		foreach ( $select as $key => $value )
	    		$newSelectFilter[$this->getFieldIndex( $key )] = $value;
		}
	
		$GLOBALS['selectFilter'] = $newSelect;
		$GLOBALS['selectCols']   = $this->cols;
		$select = array_filter( $this->records, "__selectFilter" );
		$selectQuery = new MemDBTable( $this->tablename );
		$selectQuery->records = $select;
		$selectQuery->cols = $this->cols;
		$selectQuery->reIndex();
	
		return $selectQuery;
  	}
    
	/**
	 * @access public
	 */
	function selectByIndex( $select ) 
	{
		$GLOBALS['selectFilter'] = $select;
		$GLOBALS['selectCols']   = $this->cols;
		$select = array_filter( $this->records, "__selectFilter" );
		$selectQuery = new MemDBTable( $this->tablename );
		$selectQuery->records = $select;
		$selectQuery->cols    = $this->cols;
		$selectQuery->reIndex();
	
		return $selectQuery;
  	}
} // END OF MemDBTable


$GLOBALS['sortArray'] = null;
$GLOBALS['sortCols']  = null;

function __sortCmp( $a, $b ) 
{
  	if ( count( $GLOBALS['sortArray'] ) > 0 ) 
	{
    	foreach ( $GLOBALS['sortArray'] as $key => $order ) 
		{
	  		if ( ( $GLOBALS['sortCols'][$key][1] == MEMDB_COL_INT ) | ( $GLOBALS['sortCols'][$key][1] == MEMDB_COL_AUTOINC ) | ( $GLOBALS['sortCols'][$key][1] == MEMDB_COL_FLOAT ) ) 
			{
	    		if ( $order == MEMDB_SORT_ORDER_ASC ) 
				{
	      			if ( $a[$key] > $b[$key] ) 
		    			return 1;
					else if ( $a[$key] < $b[$key] ) 
		    			return -1;
	    		} 
				else if ( $order == MEMDB_SORT_ORDER_DESC ) 
				{
	      			if ( $a[$key] < $b[$key] ) 
		    			return 1;
					else if ( $a[$key] > $b[$key] ) 
		    			return -1;
	    		} 
				else 
				{
	      			return 0;
	    		}
	  		} 
			else if ( $GLOBALS['sortCols'][$key][1] == MEMDB_COL_TEXT ) 
			{
	    		$strcmp = strcmp( $a[$key], $b[$key] );
		
				if ( $strcmp != 0 ) 
				{
		  			if ( $order == MEMDB_SORT_ORDER_ASC ) 
		    			return $strcmp;
					else if ( $order == MEMDB_SORT_ORDER_DESC ) 
		    			return $strcmp * ( -1 );
					else 
		    			return 0;
		  
		  			return $strcmp;
				}
	  		}
    	}
  	}
  
  	return 0;
}


$GLOBALS['selectFilter'] = null;
$GLOBALS['selectCols']   = null;

function __selectFilter( $var ) 
{
  	if ( count( $GLOBALS['selectFilter'] ) > 0 ) 
	{
    	foreach ( $GLOBALS['selectFilter'] as $index => $filter ) 
		{
	  		$value = $var[$index];
	  
	  		if ( !preg_match( "|" . $filter . "|Ui", $value, $match ) )
	    		return false;
		}
	
		return true;
  	}
  
  	return false;
}

?>
