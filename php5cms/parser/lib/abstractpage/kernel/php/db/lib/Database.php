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


using( 'db.lib.Transaction' );

 
/**
 * Database connection methods; either persistent or non-persistent. The latter
 * (DATABASE_NON_PERSISTENT) is the default.
 */
define( 'DATABASE_PERSISTENT'    , true  );
define( 'DATABASE_NON_PERSISTENT', false );


/**
 * Class <code>Database</code> provides a simple, efficient abstract base class
 * for setting up up a database connection and executing SQL-queries.
 * <p>
 *   Setting up a connection can be as simple as:
 * </p>
 * <pre>
 *   $db =& new Database('dbname' ,'host');
 *   if (!$db->connect('user', 'pass'))
 *   {
 *       header('Location: dberror.php');
 *   }
 *   $result =& $db->query('SELECT title FROM books');
 * </pre>
 * <p>
 *   To check if a connection is valid, the method <code>isConnected</code> is
 *   available, which returns either <code>true</code> or <code>false</code> and
 *   can be called at any time.
 * </p>
 * <p>
 *   Once a connection has been established, SQL queries can be executed by
 *   calling the <code>query</code>-method with the appropriate SQL query.
 *   This method returns an object of class <code>QueryResult</code>.
 * </p>
 * <p>
 *  If a transaction must be performed, call the method
 *  <code>createTransaction</code> and run all queries through the returned
 *  <code>Transaction</code> object.
 * </p>
 * <p>
 *   Although not strictly necessary, closing the connection at the end of the
 *   PHP-script is a proper thing to do, and for this the method
 *   <code>disconnect</code> is provided, but note that this method is of no use
 *   (i.e.: does nothing) if persistent connections are used.
 * </p>
 * <p>
 *   A note about persistent connections: although they are by far the best way
 *   to connect to a database if the database server is on the same machine as
 *   the web server, but:
 * </p>
 * <ol>
 *   <li>
 *     PHP must be run as a module (non-CGI) for persistent connections to work.
 *   </li>
 *   <li>
 *     Persistent connections are still buggy, as of PHP 4.2.2. I personally
 *     found it impossible to keep the web server running for more than a few
 *     weeks with persistent connections enabled. Your mileage may vary.
 *   </li>
 * </ol>
 *
 * @see QueryResult
 * @see Transaction
 * @package db_lib
 */
 
class Database extends PEAR
{
     /**
     * The name of the database
     * @var  string
     */
    var $name;

    /**
     * The host the database is on; if left empty, sockets are used
     * @var  string
     */
    var $host;

    /**
     * The internal link to the database
     * @var  int
     */
    var $link;


    /**
	 * Constructor
	 *
     * Prepare a new database connection on the specified host.
	 *
     * @param $name the name of the database
     * @param $host the host the database is running on
     */
    function Database( $name, $host )
    {
        $this->name = $name;
        $this->host = $host;
        $this->link = 0;
    }


    /**
     * Set the internal link identifier; this is a protected method.
	 *
     * @param $link the resource index
     * @return void
     * @access public
     */
    function setLink( $link )
    {
        $this->link = $link;
    }

    /**
     * Make a connection with a database; return <code>true</code> on success,
     * false otherwise.
	 *
     * @param $username the name of the database user
     * @param $password the password of the database user
     * @param $type the connection type; either
     * <code>DATABASE_NON_PERSISTENT</code> or
     * <code>DATABASE_PERSISTENT</code>
     * @return bool
     */
    function connect( $username, $password, $type = DATABASE_NON_PERSISTENT )
    {
    }

    /**
     * Close the connection with the database; this method needn't be called
     * when using a persistent connection.
	 *
     * @return void
     */
    function disconnect()
    {
    }

    /**
     * Execute a query and return the result.
	 *
     * @param $sql the query to perform
     * @return QueryResult
     */
    function &query( $sql )
    {
    }

    /**
     * Create a transaction and return it. Subclasses can override this method
     * to return an instance of a database-specific transaction class.
	 *
     * @return Transaction
     */
    function &createTransaction()
    {
        return new Transaction( $this );
    }
	
    /**
     * Get the internal link identifier; this is a protected method.
	 *
     * @return int
     * @access public
     */
    function getLink()
    {
        return $this->link;
    }

    /**
     * Get the host the database is running on.
	 *
     * @return string
     */
    function getHost()
    {
        return $this->host;
    }

    /**
     * Get the name of the database.
	 *
     * @return string
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * Check if a connection with the database has been established.
	 *
     * @return bool
     */
    function isConnected()
    {
        return ( $this->link != 0 );
    }

    /**
     * Get the last error message produced by the database.
	 *
     * @return string
     */
    function getErrorMessage()
    {
    }
} // END OF Database

?>
