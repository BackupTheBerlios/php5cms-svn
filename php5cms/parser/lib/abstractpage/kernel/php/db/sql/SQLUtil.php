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
|Authors: Chuck Hagenbuch <chuck@horde.org>                            |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * Static helper functions.
 *
 * @package db_sql
 */
 
class SQLUtil
{
	/**
	 * Takes a search string and a field to be matched to and returns the sql conditions.
	 * 
	 * @static
	 */
	function complexSearchSQLConditions( $match_field, $q ) 
	{
		list( $ors, $ands, $nots ) = SQLUtil::complexSearchSQLSplitSearch( $q );
	
		for ( $i = 0; $i < count( $ors ); $i++ ) 
		{
			if ( $orcon ) 
				$orcon .= " OR ";
		
			$orcon .= "$match_field LIKE '%" . $ors[$i] . "%'";
		}
	
		for ( $i = 0; $i < count( $ands ); $i++ ) 
		{
			if ( $andcon ) 
				$andcon .= " AND ";
		
			$andcon .= "$match_field LIKE '%" . $ands[$i] . "%'";
		}
	
		for ( $i = 0; $i < count( $nots ); $i++ ) 
		{
			if ( $notcon ) 
				$notcon .= " AND ";
		
			$notcon .= "$match_field NOT LIKE '%" . $nots[$i] . "%'";
		}
	
		$conditions  = ( ( $orcon  )? "(" . $orcon . ")" : "" );
		$conditions .= ( ( $andcon )? ( ( $conditions )? " AND " : "" ) . "(" . $andcon . ")" : "" );
		$conditions .= ( ( $notcon )? ( ( $conditions )? " AND " : "" ) . "(" . $notcon . ")" : "" );
		$conditions  = ( ( $conditions )? "(" . $conditions . ")" : "1=0" );
	
		return $conditions;
	}

	/**
	 * Takes a search string and splits it into ORs ANDs and NOT strings.
	 *
	 * @static
	 */
	function complexSearchSQLSplitSearch( $q ) 
	{
		$q          = ap_gpc_stripslashes( $q );
		$q          = trim( $q ) . " ";
		$q          = ereg_replace( "'", "\"", $q ); 			// allow for both quote types
		$q          = preg_replace( "[^\-\+\w\" ]", "", $q ); 	// remove invalid characters. 
		
		$ands       = array(); 
		$ors        = array(); 
		$nots       = array();
		
		$expr_start = 0;
		$logic      = "or";
		$end        = 0;
	
		for ( $i = 0; $i < strlen( $q ); $i++ ) 
		{
			if ( $i == $expr_start ) 
			{
				if ( $q[$i] == '+' ) 
				{
					$logic = "and";
					$expr_start++;
				
					continue;
				}
			
				if ( $q[$i] == '-' ) 
				{
					$logic = "not";
					$expr_start++;
				
					continue;
				}
			
				// Quoted string match.. Skips to the end.
				if ( substr( $q, $i, 1 ) == "\"" ) 
				{
					$i++;
				
					while ( substr( $q, $i, 1 ) != "\"" && $i < strlen( $q ) ) 
						$i++;
				
					$expr_start++;
					$end = 1;
				}
			}
		
			if ( $q[$i] == "," || $q[$i] == " " )
				$end = 1;
		
			if ( $end ) 
			{
				$expr = substr( $q, $expr_start, $i - $expr_start );

				if ( $logic == "and" && strlen( $expr ) >= 2 ) 
					$ands[] = trim( $expr );
					
				if ( $logic == "or"  && strlen( $expr ) >= 2 ) 
					$ors[]  = trim( $expr );
					
				if ( $logic == "not" && strlen( $expr ) >= 2 ) 
					$nots[] = trim( $expr );
			
				$expr_start = $i + 1;
				$logic      = "or";
				$end        = 0;
			}
		}
	
		return array( $ors, $ands, $nots );
	}

	/**
	 * Takes a field and a search string and returns an SQL expression that will
	 * evaluate to a "score", taking into account the amount of words matched and 
	 * their position in the text.
	 *
	 * @static
	 */
	function complexSearchSQLScore( $match_field, $q ) 
	{
		list( $ors,$ands,$nots ) = SQLUtil::complexSearchSQLSplitSearch( strtolower( $q ) );

		$length = "(1+LENGTH($match_field))";
		$order  = "(0"; // decending order

		for ( $i = 0; $i < count( $ors ); $i++ ) 
		{
			$or = $ors[$i];
		
			// phrases closer to the beginning have higher priority
			$importance = 0.9 + 0.1 * ( count( $ors ) - $i ) / count( $ors );
			$order .= " + $importance * ((1 + (1.0 - (POSITION('$or' IN LOWER($match_field))/$length))) * ($match_field LIKE '%$or%'))";
		}
		
		for ( $i = 0; $i < count( $ands ); $i++ ) 
		{
			// Ands are twice as important. Aren't they?
			$and = $ands[$i];
		
			// phrases closer to the beginning have higher priority
			$importance = 0.9 + 0.1 * ( count( $ands ) - $i ) / count( $ands );
			$order .= " + 2 * $importance * ((1 + (1.0 - (POSITION('$and' IN LOWER($match_field))/$length))) * ($match_field LIKE '%$and%'))";
		}

		$order .= ")";
		return $order;
	}
	
	/**
	 * Returns a nicely formatted date string for a SQL date format.
	 *
	 * @access public
	 */
	function sqlDateToString( $t = "", $short = 0 )
	{
		if ( $t == "" )
			return "";
		
		if ( !$short )
		{
			$months = array(
				"January",
				"Februrary",
				"March",
				"April",
				"May",
				"June",
				"July",
				"August",
				"September",
				"October",
				"November",
				"December"
			);
		}
		else
		{
			$months = array(
				"Jan",
				"Feb",
				"Mar",
				"Apr",
				"May",
				"Jun",
				"Jul",
				"Aug",
				"Sep",
				"Oct",
				"Nov",
				"Dec"
			);
		}
		
		if ( ereg( "^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})", $t, $args ) )
			return sprintf( "%s %d, %s", $months[$args[2]-1], $args[3], $args[1] );
		else if ( ereg( "^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $t, $args ) )
			return sprintf( "%s %d, %s", $months[$args[2]-1], $args[3], $args[1] );
		else
			return $t;
	}
	
	/**
	 * @static
	 */
    function readBlob( $dbh, $table, $field, $criteria )
    {
        if ( !count( $criteria ) )
            return PEAR::raiseError( 'You must specify the fetch criteria.' );

        $where = '';

        switch ( $dbh->dbsyntax ) 
		{
        	case 'oci8':
            	foreach ( $criteria as $key => $value ) 
				{
                	if ( !empty( $where ) )
                    	$where .= ' AND ';
                
                	if ( empty( $value ) )
                    	$where .= $key . ' IS NULL';
                	else
                    	$where .= $key . ' = ' . $dbh->quote( $value );
            	}

            	$statement = OCIParse( $dbh->connection, sprintf( 'SELECT %s FROM %s WHERE %s', $field, $table, $where ) );
            	OCIExecute( $statement );
				
            	if ( OCIFetchInto( $statement, $lob ) )
                	$result = $lob[0]->load();
            	else
                	$result = PEAR::raiseError( 'Unable to load SQL Data.' );
            	
            	OCIFreeStatement( $statement );
            	break;

        	default:
            	foreach ( $criteria as $key => $value ) 
				{
                	if ( !empty( $where ) )
                    	$where .= ' AND ';
                
                	$where .= $key . ' = ' . $dbh->quote( $value );
            	}
            
				$result = $dbh->getOne( sprintf( 'SELECT %s FROM %s WHERE %s', $field, $table, $where ) );

            	switch ( $dbh->dbsyntax ) 
				{
            		case 'pgsql':
                		$data   = substr( $result, 2 );
                		$result = pack( 'H' . strlen( $data ), $data );
                		
						break;
            	}
        }

        return $result;
    }

	/**
	 * @static
	 */
    function insertBlob( $dbh, $table, $field, $data, $attributes )
    {
        $fields = array();
        $values = array();

        switch ( $dbh->dbsyntax ) 
		{
        	case 'oci8':
            	foreach ( $attributes as $key => $value ) 
				{
                	$fields[] = $key;
                	$values[] = $dbh->quote( $value );
            	}

            	$statement = OCIParse(
					$dbh->connection,
					sprintf( 'INSERT INTO %s (%s, %s) VALUES (%s, EMPTY_BLOB()) RETURNING %s INTO :blob',
						$table,
						implode( ', ', $fields ),
						$field,
						implode( ', ', $values ),
						$field
					)
				);

            	$lob = OCINewDescriptor( $dbh->connection );
            	OCIBindByName( $statement, ':blob', $lob, -1, SQLT_BLOB );
            	OCIExecute( $statement, OCI_DEFAULT );
            	$lob->save( $data );
            	$result = OCICommit( $dbh->connection );
            	$lob->free();
            	OCIFreeStatement($statement);
            
				return $result? true : PEAR::raiseError( 'Unknown Error.' );

        	default:
            	foreach ( $attributes as $key => $value ) 
				{
                	$fields[] = $key;
                	$values[] = $value;
            	}

            	$query = sprintf(
					'INSERT INTO %s (%s, %s) VALUES (%s)',
					$table,
					implode( ', ', $fields ),
					$field,
					'?' . str_repeat( ', ?', count( $values ) )
				);
            
				break;
        }

        switch ( $dbh->dbsyntax ) 
		{
        	case 'mssql':
        
			case 'pgsql':
        	    $values[] = bin2hex( $data );
            	break;

        	default:
            	$values[] = $data;
        }

        /* Execute the query. */
        $stmt = $this->_db->prepare( $query );
        return $this->_db->execute( $stmt, $values );
    }

	/**
	 * @static
	 */
    function updateBlob( $dbh, $table, $field, $data, $where, $alsoupdate )
    {
        $fields = array();
        $values = array();

        switch ( $dbh->dbsyntax ) 
		{
        	case 'oci8':
            	$wherestring = '';
            
				foreach ( $where as $key => $value ) 
				{
                	if ( !empty( $wherestring ) )
                    	$wherestring .= ' AND ';
                
                	$wherestring .= $key . ' = ' . $dbh->quote( $value );
            	}

            	$statement = OCIParse(
					$dbh->connection,
					sprintf( 'SELECT %s FROM %s FOR UPDATE WHERE %s',
						$field,
						$table,
						$wherestring
					)
				);

            	OCIExecute( $statement, OCI_DEFAULT );
            	OCIFetchInto( $statement, $lob );
            	$lob[0]->save( $data );
            	$result = OCICommit( $dbh->connection );
            	$lob[0]->free();
            	OCIFreeStatement( $statement );
            
				return $result? true : PEAR::raiseError( 'Unknown Error.' );

        	default:
            	$updatestring = '';
            	$values = array();
            
				foreach ( $alsoupdate as $key => $value ) 
				{
                	$updatestring .= $key . ' = ?, ';
                	$values[] = $value;
            	}
            
				$updatestring .= $field . ' = ?';
            
				switch ( $dbh->dbsyntax ) 
				{
            		case 'mssql':
            
					case 'pgsql':
                		$values[] = bin2hex( $data );
                		break;

            		default:
                		$values[] = $data;
            	}

            	$wherestring = '';
            
				foreach ( $where as $key => $value ) 
				{
                	if ( !empty( $wherestring ) )
                    	$wherestring .= ' AND ';
                
                	$wherestring .= $key . ' = ?';
                	$values[] = $value;
            	}

            	$query = sprintf( 'UPDATE %s SET %s WHERE %s',
					$table,
					$updatestring,
					$wherestring
				);
            
				break;
        }

        /* Execute the query. */
        $stmt = $dbh->prepare( $query );
        return $dbh->execute( $stmt, $values );
    }
} // END OF SQLUtil

?>
