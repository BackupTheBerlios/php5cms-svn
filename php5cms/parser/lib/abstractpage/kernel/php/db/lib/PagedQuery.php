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


using( 'db.lib.PagedQueryResult' );


/**
 * Class <code>PagedQuery</code> makes it easy to work with queries that return
 * several pages of results.
 * <p>
 *   Given a selection query, this class can be used to select only a specific
 *   number of rows (<code>$pageSize</code>) on a specific page. This is
 *   achieved by wrapping the full query result inside an object of class
 *   <code>PagedQueryResult</code>. Without going into the details, the result
 *   is that this class works directly for <i>any</i> database supported - no 
 *   database-specific SQL commands are used to limit the
 *   resultset (e.g. <code>LIMIT</code> or <code>SELECT TOP</code>).
 * </p>
 * <p>
 *   This class might seem inefficient, exactly because it doesn't use mentioned
 *   database-specific SQL commands. However, for one very good reason, that
 *   isn't really true: it is almost always necessary to know the total number
 *   of pages in a query result - for example to be able to show a page
 *   navigator - and as this value is computed from the total number of rows in
 *   the query, the only way to get this number is by executing the full query.
 *   This completely eliminates the possible gain in efficiency when using
 *   <code>LIMIT</code>- or <code>SELECT TOP</code>-clauses, because in that
 *   case an additional (counting) query must be executed. Also, consider that
 *   most queries specify an ORDER BY clause, and remember that a DBMS can only
 *   compute this ordering by examining all rows in the result, even when just
 *   the first 10 are selected.
 * </p>
 * <p>
 *   When using this class, <i>always</i> use an <code>ORDER BY</code>-clause in
 *   the SQL query; if such a clause is omitted, the order the rows are returned
 *   in is undefined. (This is actually an SQL standard.)
 * </p>
 * <p>
 *   The following example select all book titles from some database and prints
 *   them, 20 at a time:
 * </p>
 * <pre>
 *   $sql   =  'SELECT title FROM book ORDER BY title';
 *   $query =& new PagedQuery($database->query($sql), 20);
 *   $page  =  $query->getPage(isset($_GET['page']) ? (int)$_GET['page'] : 0);
 *   for ($it =& new QueryIterator($page); $it->isValid(); $it->next())
 *   {
 *       $row =& $it->getCurrent();
 *       print "${row['title']}, ${row['year']}&lt;br&gt;\n";
 *   }
 * </pre>
 *
 * @see Database
 * @see QueryResult
 * @see QueryIterator
 * @package db_lib
 */
 
class PagedQuery extends PEAR
{
    /**
     * The number of rows on each page
     * @var  int
     */
    var $pageSize;

    /**
     * The total number of pages
     * @var  int
     */
    var $pageCount;

    /**
     * The result of the full query
     * @var  QueryResult
     */
    var $queryResult;
    

    /**
     * Construct a new <code>PagedQuery</code>
     * @param $queryResult the <code>QueryResult</code> to create a pager for
     * @param $pageSize the number of rows on a single page
     */
    function PagedQuery( &$queryResult, $pageSize = 30 )
    {
        $this->queryResult =& $queryResult;
        $this->pageSize    =  $pageSize;
        $this->pageCount   =  ceil( $this->getRowCount() / $this->pageSize );
    }
    

    /**
     * Get the rows on the specified page. This method returns an instance of
     * class <code>PagedQueryResult</code>, which has the exact same interface
     * as class <code>QueryResult</code>. If <code>$index</code> is invalid, the
     * first is are returned.
     * @return PagedQueryResult
     */
    function &getPage( $index = 0 )
    {
        if ( $index < 0 || $index > $this->pageCount )
            $index = 0;
        
        return new PagedQueryResult(
            $this->queryResult,
            $this->pageSize * $index,
            $this->pageSize
        );
    }
    
    /**
     * Return the size of a single page; note that this isn't necessary the
     * total number of rows on the current page: the last page can have less.
     * @return int
     */
    function getPageSize()
    {
        return $this->pageSize;
    }
    
    /**
     * Get the total number of rows in the query.
     * @return int
     */
    function getRowCount() 
    {
        return $this->queryResult->getRowCount();
    }

    /**
     * Return the total number of pages in the query result.
     * @return int
     */
    function getPageCount() 
    {
        return $this->pageCount;
    }
} // END OF PagedQuery

?>
