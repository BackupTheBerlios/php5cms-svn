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
using( 'db.lib.MSQueryResult' );
using( 'db.lib.MSTransaction' );


/**
 * Class <code>MSDatabase</code> provides an implementation of the
 * <code>Database</code> interface for <b>Microsoft SQL Server</b> databases.
 * <p>
 *   Note that it's probably not a good idea to connect to <b>Microsoft SQL
 *   Server</b> from a <b>Unix</b> machine. Although this is possible with 
 *   <b>FreeTDS</b> (<code>www.freetds.org</code>), this software package is
 *   still in beta stage. Another method is by using the <b>Sybase</b> classes
 *   in this library in cooperation with the free <b>Sybase</b> drivers for 
 *   <b>Unix</b>.
 * </p>
 *
 * @package db_lib
 */
 
class MSDatabase extends Database 
{
	/**
	 * Constructor
	 */
    function MSDatabase( $name, $host ) 
    {
        $this->Database( $name, $host );
    }
    

    /**
     * @return bool
     */
    function connect( $username, $password, $type = DATABASE_NON_PERSISTENT ) 
    {
        $this->setLink( ( $type == DATABASE_PERSISTENT )? mssql_pconnect($this->getHost(), $username, $password) : mssql_connect( $this->getHost(), $username, $password ) );
        mssql_select_db( $this->getName(), $this->getLink() );
		
        return $this->isConnected();
    }

    /**
     * @return void
     */
    function disconnect() 
    {
        mssql_close( $this->getLink() );
        $this->setLink( 0 );
    }

    /**
     * @return MSQueryResult
     */
    function &query( $sql )
    {
        return new MSQueryResult( $this, mssql_query( $sql, $this->getLink() ) );
    }

    /**
     * @return MSTransaction
     */
    function &createTransaction()
    {
        return new MSTransaction( $this );
    }

    /**
     * @return string
     */
    function getErrorMessage() 
    {
        return mssql_get_last_message( $this->getLink() );
    }
} // END OF MSDatabase

?>
