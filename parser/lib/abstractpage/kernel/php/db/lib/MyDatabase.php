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


using( 'db.lib.Database' );
using( 'db.lib.MyQueryResult' );
using( 'db.lib.MyTransaction' );


/**
 * Class <code>MyDatabase</code> provides an implementation of the
 * <code>Database</code> interface for <b>MySQL</b> databases.
 * <p>
 *   To use sockets (often the preferred method), use the path to the socket
 *   file prepended with a colon as the hostname, e.q. <code>$db =& new
 *   MyDatabase('database', ':/var/mysql/mysql.sock');</code>.
 * </p>
 * <p>
 *   To be able to use transactions in <b>MySQL</b>, transaction-safe table
 *   types must be used (<i>BDB</i> or <i>InnoDB</i>).
 * </p>
 *
 * @package db_lib
 */
 
class MyDatabase extends Database 
{
    /**
     * Prepare a new database connection on the specified host. If the
     * host is left empty, the localhost is used. To use sockets, specify
     * the path to the socket as the hostname (prepended with ':')
     * @param $name the name of the database
     * @param $host the host the database is running on
     */
    function MyDatabase( $name, $host = '' ) 
    {
        $this->Database( $name, $host );
    }


     /**
     * @return bool
     */
    function connect( $username, $password, $type = DATABASE_NON_PERSISTENT ) 
    {
        $this->setLink( ( $type == DATABASE_PERSISTENT )? mysql_pconnect( $this->getHost(), $username, $password ) : mysql_connect( $this->getHost(), $username, $password ) );
        mysql_select_db( $this->getName(), $this->getLink() );
		
        return $this->isConnected();
    }

    /**
     * @return void
     */
    function disconnect() 
    {
        mysql_close( $this->getLink() );
        $this->setLink( 0 );
    }

    /**
     * @return MyQueryResult
     */
    function &query( $sql ) 
    {
        return new MyQueryResult( $this, mysql_query( $sql, $this->getLink() ) );
    }

    /**
     * @return MyTransaction
     */
    function &createTransaction()
    {
        return new MyTransaction( $this );
    }

    /**
     * @return string
     */
    function getErrorMessage() 
    {
        return mysql_error( $this->getLink() );
    }
} // END OF MyDatabase

?>
