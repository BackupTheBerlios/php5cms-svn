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
using( 'db.lib.SyQueryResult' );
using( 'db.lib.SyTransaction' );


/**
 * Class <code>SyDatabase</code> provides an implementation of the
 * <code>Database</code> interface for <b>Sybase</b> databases.
 * <p>
 *   This class is almost identical to class <code>MSDatabase</code>, with
 *   all occurrences of <code>mssql</code> replaced with <code>sybase</code>.
 * </p>
 * <p>
 *   This code is in beta stage, because it hasn't been tested; I do not have
 *   access to a <b>Sybase</b> database server.
 * </p>
 *
 * @package db_lib
 */
 
class SyDatabase extends Database 
{
    /**
	 * Constructor
	 */
    function SyDatabase( $name, $host ) 
    {
        $this->Database( $name, $host );
    }


    /**
     * @return bool
     */
    function connect( $username, $password, $type = DATABASE_NON_PERSISTENT ) 
    {
        $this->setLink( ( $type == DATABASE_PERSISTENT )? sybase_pconnect( $this->getHost(), $username, $password ) : sybase_connect( $this->getHost(), $username, $password ) );
        sybase_select_db( $this->getName(), $this->getLink() );

        return $this->isConnected();
    }

    /**
     * @return void
     */
    function disconnect() 
    {
        sybase_close( $this->getLink() );
        $this->setLink( 0 );
    }

    /**
     * @return SyQueryResult
     */
    function &query( $sql ) 
    {
        return new SyQueryResult( $this, sybase_query( $sql, $this->getLink() ) );
    }

    /**
     * @return SyTransaction
     */
    function &createTransaction()
    {
        return new SyTransaction( $this );
    }

    /**
     * @return string
     */
    function getErrorMessage() 
    {
        return sybase_get_last_message();
    }
} // END OF SyDatabase

?>
