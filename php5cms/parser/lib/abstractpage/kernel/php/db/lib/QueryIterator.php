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


using( 'util.Iterator' );


/**
 * Class <code>QueryIterator</code> implements an iterator for query results.
 * <p>
 *   This class can be used on an object of class
 *   <code>QueryResult</code> for queries that return rows (a 
 *   <code>SELECT</code>-query). An object is this class is returned after
 *   running a query on a <code>Database</code> or a <code>Query</code>.
 * </p>
 * <p>
 *   Pass an object of class <code>QueryResult</code> to the constructor to
 *   enable iteration of the records in the result, together with the type
 *   each row in the result should have. This is <code>DATABASE_NUM</code>,
 *   <code>DATABASE_ASSOC</code> or <code>DATABASE_BOTH</code>, with the
 *   latter the default. See class <code>QueryResult</code> for additional
 *   information.
 * </p>
 *
 * @see Database
 * @see QueryResult
 * @package db_lib
 */
 
class QueryIterator extends Iterator 
{
    /**
     * The query to iterator over (a <code>QueryResult</code> or a
     * <code>Query</code>)
     * @var  QueryResult
     */
    var $queryResult;

    /**
     * How each row should be retrieved. Should be <code>DATABASE_BOTH</code>,
     * <code>DATABASE_ASSOC</code> or <code>DATABASE_NUM</code>
     * @var  int
     */
    var $rowType;
    
    /**
     * The index of the current row
     * @var  int
     */
    var $index;
    

    /**
     * Construct a new <code>QueryIterator</code>-object
     * @param $queryResult the <code>QueryResult</code> to iterate over
     */
    function QueryIterator( &$queryResult, $rowType = DATABASE_BOTH ) 
    {
        $this->queryResult =& $queryResult;
        $this->rowType     =  $rowType;
        
		$this->reset();
    }


    /**
     * @return void
     */
    function reset() 
    {
        $this->index = 0;
    }

    /**
     * @return void
     */
    function next() 
    {
        $this->index++;
    }

    /**
     * @return bool
     */
    function isValid() 
    {
        return ( $this->index < $this->queryResult->getRowCount() );
    }

    /**
     * Return a reference to the current row of the <code>QueryResult</code>
     * @return array
     */
    function &getCurrent() 
    {
        return $this->queryResult->getRow( $this->index, $this->rowType );
    }
} // END OF QueryIterator

?>
