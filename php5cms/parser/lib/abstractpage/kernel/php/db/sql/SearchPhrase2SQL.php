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
 * Creates a sql query with match statements
 * from a given search string.
 *
 * possible operators: +(AND) -(NOT) und |(OR)
 *
 * Usage (mysql example):
 *
 * mysql_pconnect( 'db_host', 'db_user', 'db_pw' ) or die( "Unable to connect to SQL server." );
 * mysql_select_db( 'table_name' ) or die( "Unable to select database." ); 
 *
 * $resultArray = array();
 * $search = new SearchPhrase2SQL( '+Haus +Garten -Garage', 'tips', array( 'thead', 'tbody' ), 'tid' );
 * $search->parseSearchArray();
 * $result = mysql_query( $search->getQuery() );
 * 		 
 * while ( $row = mysql_fetch_array( $result ) )
 * 		$resultArray[] = $row['tid'];
 *		 		 
 * mysql_free_result( $result );
 *
 * foreach ( $resultArray as $result )
 * 		echo "$result found<br>";
 *
 * @package db_sql
 */

class SearchPhrase2SQL extends PEAR 
{
	/**
	 * @access public
	 */
	var $searchString;
	
	/**
	 * @access public
	 */
	var $searchIndexString;
	
	/**
	 * @access public
	 */
	var $searchTable;
	
	/**
	 * @access public
	 */
	var $searchReturnKey;
	
	/**
	 * @access public
	 */
	var $SQLSearchString;
		
	/**
	 * @access public
	 */	 		 		 
	var $SQLStringNOT;
	
	/**
	 * @access public
	 */
	var $SQLStringAND;
	
	/**
	 * @access public
	 */
	var $SQLStringOR;

	/**
	 * @access public
	 */
	var $ignoredKeys = array();
	
	/**
	 * @access public
	 */
	var $searchKeys = array();		 
	
	/**
	 * @access public
	 */
	var $SQLsearcharray = array();
		 

	/**
	 * Constructor
	 *
	 * @access public
	 */
	function SearchPhrase2SQL( $searchString, $searchTable, $searchIndex, $searchReturnKey )
	{
		$this->searchTable       = $searchTable;
		$this->searchIndexString = implode( ",", $searchIndex );
		$this->searchReturnKey   = $searchReturnKey;
		 		 
		$this->searchString      = trim( $searchString );
		$this->searchString      = eregi_replace( "([\ ;,][|+-])\ +", "\\1", $this->searchString );
		$this->searchKeys        = preg_split( "/[\s,;]+/", $this->searchString );
	}
		 

	/**
	 * @access public
	 */
	function parseSearchArray()
	{
		foreach ( $this->searchKeys as $key )
		{
			switch ( $key{0} )
			{
				case "-":
					if ( $this->keylenOK( $key, 4 ) )
						$this->SQLStringNOT .= " AND MATCH ($this->searchIndexString) AGAINST ('" . substr( $key, 1 ) . "') = 0";
					
					break;
				
				case "|":
					if ( $this->keylenOK( $key, 4 ) )
						$this->SQLStringOR .= " " . substr( $key, 1 );
					
					break;
				
				case "+":
					if ( $this->keylenOK( $key, 4 ) )
						$this->SQLStringAND .= " AND MATCH ($this->searchIndexString) AGAINST ('" . substr( $key, 1 ) . "')";
					
					break;
				
				default:
					if ( $this->keylenOK( $key, 3 ) )
						$this->SQLStringAND .= " AND MATCH ($this->searchIndexString) AGAINST ('" . $key . "')";
					
					break;
			}
		}
		
		if ( strlen( $this->SQLStringOR ) > 1 )
			$this->SQLStringOR = " OR MATCH ($this->searchIndexString) AGAINST ('" . trim( $this->SQLStringOR ) . "')";
		
		$this->SQLSearchString = $this->SQLStringAND . $this->SQLStringNOT . $this->SQLStringOR;
		$this->SQLSearchString = substr( $this->SQLSearchString, 4 );
	}
	
	/**
	 * @access public
	 */
	function getQuery()
	{
		return "SELECT $this->searchReturnKey from $this->searchTable WHERE $this->SQLSearchString";
	}
		
	/**
	 * @access public
	 */ 
	function showIgnoreList()
	{
		return $this->ignoredKeys;
	}
		
	/**
	 * @access public
	 */ 
	function keylenOK( $key, $len )
	{
		if ( strlen( $key ) > $len ) 
		{
			return true;
		}
		else 
		{
			$this->ignoredKeys[] = $key;
			return false;
		}
	}
} // END OF SearchPhrase2SQL

?>
