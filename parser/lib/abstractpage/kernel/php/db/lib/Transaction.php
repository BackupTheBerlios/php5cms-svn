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
 * Class <code>Transaction</code> is an abstract base class for ANSI-SQL92
 * database transactions.
 * <p>
 *   Given an object <code>$transaction</code> this class, usage can be as
 *   follows:
 * </p>
 * <pre>
 *   $transaction->begin();
 *   $result_1 = $transaction->query($sql_1);
 *   ...
 *   $result_i = $transaction->query($sql_i);
 *   ...
 *   $result_n = $transaction->query($sql_n);
 *   $transaction->commit();
 * </pre>
 * <p>
 *   Whenever one of the queries in a transaction fails, the transaction is
 *   automatically rolled back, and it is set invalid. If <code>query</code>,
 *   <code>commit</code> or <code>rollback</code> is called on an invalid
 *   transaction, nothing happens. Thus, if <i>n</i>  queries are executed
 *   consecutively on a transaction and the <i>i</i>'th one fails, queries
 *   <i>i</i> + 1 ... <i>n</i> aren't actually executed, nor will the final
 *   <code>commit</code> do anything. This makes it very easy to run many
 *   queries on a transaction without expliclty having to check their results.
 * </p>
 * <p>
 *   An object of class <code>Transaction</code> is generally instantiated by
 *   calling <code>createTransaction</code> on a database object, allowing
 *   DBMS-specific database classes to instantiate some other transaction class
 *   transparantly.
 * </p>
 * <p>
 *   This class has a number of serious limitations:
 * </p>
 * <ul>
 *   <li>
 *     Creating more than one transaction on the same database connection at the
 *     same time will not make those transactions run concurrently, but will run
 *     all queries in a single transaction (the first) instead. This is the
 *     correct behavior, but important to note nonetheless. To create concurrent
 *     transactions, multiple database connections must be used.
 *   </li>
 *   <li>
 *     When using a transaction object, it still remains possible to execute
 *     queries directly through the database object. More importantly, these
 *     queries will take part in the transaction! Thus, when the transaction is
 *     rolled back, so will those queries. The reason for executing queries 
 *     through the transaction object instead of on the database object directly
 *     is to make sure queries are only executed as long as the transaction is 
 *     valid.
 *   </li>
 * </ul>
 * <p>
 *   Given the limitations above, the existence of this class might be 
 *   questionable. A justification:
 * </p>
 * <ul>
 *   <li>
 *     Most of the time, transactions aren't necessary in web applications. 
 *     Merging this class with the <code>Database</code> class therefore
 *     isn't a good idea, as it would result in a big, complicated class
 *     with lots of code that isn't used most of the time.
 *   </li>
 *   <li>
 *     When transactions are used, they are typically run in isolation. That is,
 *     when some transaction begins, all following queries normally belong to
 *     that transaction until it is committed or rolled back, after which a new
 *     transaction is started.
 *   </li>
 *   <li>
 *     <code>SELECT</code>-queries do not interfere with transactions. Any 
 *     correct selection query will always run succesfully, although it may
 *     return zero rows.
 *   </li>
 * </ul>
 * <p>
 *   Note that either <code>commit()</code> or <code>rollback()</code> should
 *   <i>always</i> be called. Also note that <code>rollback()</code> might have
 *   been called automatically (when some query fails), and that calling either
 *   one of the transaction-ending methods multiple times doesn't matter at all.
 * </p>
 *
 * @package db_lib
 */
 
class Transaction extends PEAR
{
    /**
     * The database this transaction runs on
     * @var  Database
     */
    var $database;
    
    /**
     * Whether this transaction is valid
     * @var  bool
     */
    var $valid;
    
    /**
     * The error message produced by the database when this transaction became
     * invalid, or the empty string if all is well
     * @var  string
     */
    var $errorMessage;

	
    /**
     * Create a new transaction.
     * @param $database the database connection to create the transaction for.
     */
    function Transaction( &$database )
    {
        $this->database     =& $database;
        $this->valid        =  false;
        $this->errorMessage =  '';
    }

	
    /**
     * Begin this transaction; returns <code>true</code> if the transaction
     * could be started, and <code>false</code> otherwise.
     * @return bool
     */
    function begin()
    {
        if ( ( $sql = $this->getBeginSql()) !== '' )
        {
            $result             = $this->database->query( $sql );
            $this->valid        = $result->isSuccess();
            $this->errorMessage = $result->getErrorMessage();
        }
        else
        {
            $this->valid = true;
        }
		
        return $this->valid;
    }
    
    /**
     * End this transaction, either by a commit or a rollback. If the 
     * transaction was ended succesfully, <code>true</code> is returned, and
     * <code>false</code> otherwise.
     * @param   $sql the SQL query to end the transaction with
     * @return bool
     * @access  private
     */
    function end( $sql )
    {
        if ( !$this->valid )
            return false;
        
        $this->valid = false;
        
		if ( $sql !== '' )
        {
            $result = $this->database->query( $sql );
            return $result->isSuccess();
        }
		
        return true;
    }

    /**
     * Execute a query in this transaction and return the result. If this
     * transaction is invalid, the query isn't executed and <code>false</code>
     * is returned.
     * @param $sql the query to execute
     * @return QueryResult
     */
    function query( $sql )
    {
        if ( !$this->valid )
            return false;
        
        $result = $this->database->query( $sql );
		
        if ( !$result->isSuccess() )
        {
            $this->errorMessage = $result->getErrorMessage();
            $this->rollback();
        }
		
        return $result;
    }
    
    /**
     * Commit the transaction; returns <code>true</code> on succes, and
     * <code>false</code> otherwise.
     * @return bool
     */
    function commit()
    {
        return $this->end( $this->getCommitSql() );
    }
    
    /**
     * Roll back the transaction; returns <code>true</code> on succes, and
     * <code>false</code> otherwise.
     * @return bool
     */
    function rollback()
    {
        return $this->end( $this->getRollbackSql() );
    }

    /**
     * Return the SQL query to begin the transaction, or the empty string if
     * there is no such thing; this is a protected method
     * @return string
     * @access public
     */
    function getBeginSql()
    {
        return '';
    }
    
    /**
     * Return the SQL query to commit the transaction, or the empty string if
     * there is no such thing; this is a protected method
     * @return string
     * @access public
     */
    function getCommitSql()
    {
        return 'COMMIT WORK';
    }
    
    /**
     * Return the SQL query to rollback the transaction, or the empty string if
     * there is no such thing; this is a protected method
     * @return string
     * @access public
     */
    function getRollbackSql()
    {
        return 'ROLLBACK WORK';
    }
    
    /**
     * Check if the transaction is valid; a transaction is invalid if 
     * <code>begin</code> hasn't been called on it successfully or if any query
     * inside the transaction fails to execute successfully.
     * @return bool
     */
    function isValid()
    {
        return $this->valid;
    }
    
    /**
     * Return a description of the error that occurred when the transaction
     * first became invalid. If the transaction is valid, the empty string is
     * returned.
     * @return string
     */
    function getErrorMessage()
    {
        return $this->errorMessage;
    }
} // END OF Transaction

?>
