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
|Authors: Vincent Oostindië <eclipse@sunlight.tmfweb.nl>               |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * Class <code>QueryResult</code> is an abstract base class for query results;
 * it holds all information on the result of a query processed after calling
 * <code>query</code> on a <code>Database</code>-object.
 * <p>
 *   <code>isSuccess</code> can be called to find out if the query actually
 *   worked or not. If not, the accompanying error message can be requested with
 *   <code>getErrorMessage()</code>. Note that the returned error message
 *   needn't necessarily be the same as the one returned after calling
 *   <code>getErrorMessage()</code> on the database connection object.
 * </p>
 * <p>
 *   If the query performed was a <code>SELECT</code>-query, the following
 *   methods are useful:
 * <ul>
 *   <li>
 *     <code>getRowCount()</code>: returns the number of rows in the result.
 *   </li>
 *   <li>
 *     <code>getRow($index, $type)</code>: returns the row at the specified
 *     index. <code>$type</code> is one of <code>DATABASE_NUM</code> for an
 *     indexed result array, <code>DATABASE_ASSOC</code> for an associative
 *     result array, or <code>DATABASE_BOTH</code> for a combination of the
 *     first two. The default is <code>DATABASE_BOTH</code>.
 *   </li>
 * </ul>
 * <p>
 *   Alternatively, a <code>QueryIterator</code> can be used to process the rows
 *   in a selection query in a more generic way.
 * </p>
 *
 * @see Database
 * @see QueryIterator
 * @package db_lib
 */
 
/**
 * Database result types; results can be returned as an indexed array, an
 * associative array, or both. The latter (DATABASE_BOTH) is the default.
 */
define( 'DATABASE_NUM'  , 1 );
define( 'DATABASE_ASSOC', 2 );
define( 'DATABASE_BOTH' , 3 );


class QueryResult extends PEAR
{
    /**
     * The database object
     * @var  Database
     */
    var $database;

    /**
     * The internal result identifier
     * @var  int
     */
    var $resultId;
    

    /**
     * Constructor
	 *
     * @param $database the database the query was executed on
     * @param $resultId an internal result identifier
     */
    function QueryResult( &$database, $resultId )
    {
        $this->database =& $database;
        $this->resultId =  $resultId;
    }
    

    /**
     * Clear the result data from memory; this method need only be called if
     * memory usage is high in a single script. After calling this method, the
     * result can no longer be accessed.
     * @return void
     */
    function clear()
    {
    }

    /**
     * Get the internal result identifier; this is a protected method.
     * @return int
     * @access public
     */
    function getResultId() 
    {
        return $this->resultId;
    }

    /**
     * Get a reference to the database
     * @return Database
     */
    function &getDatabase()
    {
        return $this->database;
    }

    /**
     * Check if the query was executed successfully
     * @return bool
     */
    function isSuccess()
    {
        return ( $this->resultId != 0 );
    }

    /**
     * Get the error message; useful in case <code>isSuccess()</code> returns
     * <code>false</code>. Returns the empty string if there was no error.
     * @return string
     */
    function getErrorMessage() 
    {
    }

    /**
     * Get the number of rows in the result for a <code>SELECT</code>-query
     * @return int
     */
    function getRowCount() 
    {
    }

    /**
     * Get the row at the specified index for a <code>SELECT</code>-query. The
     * optional argument <code>$type</code> defines how the row should be 
     * retrieved: as an indexed array (<code>DATABASE_NUM</code>), an 
     * associative array (<code>DATABASE_ASSOC</code>) or as both an indexed
     * and an associative array (<code>DATABASE_BOTH</code>). The latter is
     * the default.
     * @param $index the index of the row to retrieve
     * @param $type how the row should be retrieved
     * @return array
     */
    function getRow( $index, $type = DATABASE_BOTH ) 
    {
    }
} // END OF QueryResult

?>
