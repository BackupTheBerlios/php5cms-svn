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
using( 'db.lib.PgQueryResult' );
using( 'db.lib.PgTransaction' );


/**
 * Class <code>PgDatabase</code> provides an implementation of the
 * <code>Database</code> interface for <b>PostgreSQL</b> databases.
 * <p>
 *   To use sockets (often the preferred method), leave the hostname blank.
 *   Using sockets leads to much better performance than making a connection
 *   to <code>localhost</code>.
 * </p>
 *
 * @package db_lib
 */
 
class PgDatabase extends Database 
{
    /**
     * Prepare a new database connection on the specified host. If the
     * host is left empty, sockets are used
     * @param $name the name of the database
     * @param $host the host the database is running on
     */
    function PgDatabase( $name, $host = '' ) 
    {
        $this->Database( $name, $host );
    }


    /**
     * @return bool
     */
    function connect( $username, $password, $type = DATABASE_NON_PERSISTENT )
    {
        $host = $this->getHost();
        $name = $this->getName();
        $conn = ( $host == '' ? '' : "host=$host " ) . "dbname=$name user=$username" . ( $password == '' ? '' : " password=$password" );

        $this->setLink( ( $type == DATABASE_PERSISTENT )? pg_pconnect( $conn ) : pg_connect( $conn ) );
        return $this->isConnected();
    }

    /**
     * @return void
     */
    function disconnect() 
    {
        pg_close( $this->getLink() );
        $this->setLink( 0 );
    }

    /**
     * @return PgQueryResult
     */
    function &query( $sql ) 
    {
        return new PgQueryResult( $this, pg_query( $this->getLink(), $sql ) );
    }

    /**
     * @return PgTransaction
     */
    function &createTransaction()
    {
        return new PgTransaction( $this );
    }

    /**
     * @return string
     */
    function getErrorMessage() 
    {
        return pg_last_error( $this->getLink() );
    }
} // END OF PgDatabase

?>
