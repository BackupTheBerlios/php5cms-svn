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
 * Class <code>PagedQueryResult</code> wraps around a <code>QueryResult</code>
 * to allow access to only part of a query result.
 * <p>
 *   This class is an implementation detail of class <code>PagedQuery</code>,
 *   and is not meant to be used on its own.
 * </p>
 * <p>
 *   This class behaves just like class <code>QueryResult</code>. For more
 *   information on processing rows in query results, please examine the
 *   documentation for that class.
 * </p>
 *
 * @see PagedQuery
 * @see QueryResult
 * @package db_lib
 */
 
class PagedQueryResult extends PEAR
{
    /**
     * The wrapped QueryResult
     * @var  QueryResult
     */
    var $queryResult;

    /**
     * The first row that can be accessed
     * @var  int
     */
    var $offset;
    
    /**
     * The maximum number of rows that may be accessed
     * @var  int
     */
    var $total;
    
    
    /**
     * Construct a new PagedQueryResult
     * @param $queryResult the <code>QueryResult</code> wrapped by this object
     * @param $offset the index of the first row
     * @param $total the number of rows on a single page
     */
    function PagedQueryResult( &$queryResult, $offset, $total )
    {
        $this->queryResult =& $queryResult;
        $this->total       =  $total;
        $this->offset      =  $offset;
    }
    
    
    /**
     * Clear the result data from memory
     * @return void
     */
    function clear()
    {
        $this->queryResult->clear();
    }

    /**
     * Get the internal result identifier; this is a protected method
     * @return int
     * @access public
     */
    function getResultId()
    {
        return $this->queryResult->getResultId();
    }
    
    /**
     * Return a reference to the database
     * @return Database
     */
    function &getDatabase()
    {
        return $this->queryResult->getDatabase();
    }
    
    /**
     * Check if the query was executed successfully
     * @return bool
     */
    function isSuccess()
    {
        return $this->queryResult->isSuccess();
    }
    
    /**
     * Get the error message
     * @return string
     */
    function getErrorMessage()
    {
        return $this->queryResult->getErrorMessage();
    }

    /**
     * Return the number of rows on the active page
     * @return int
     */
    function getRowCount()
    {
        return min(
            $this->queryResult->getRowCount() - $this->offset,
            $this->total
        );
    }

    /**
     * Return the row at the specified index of the active page
     * @return array
     */
    function getRow( $index, $type = DATABASE_BOTH )
    {
        return $this->queryResult->getRow( $index + $this->offset, $type );
    }
} // END OF PagedQueryResult

?>
