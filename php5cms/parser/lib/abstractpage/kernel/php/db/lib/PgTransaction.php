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
 * Class <code>PgTransaction</code> implements the <code>Transaction</code>
 * interface for <b>PostgreSQL</b> databases.
 * <p>
 *   To create concurrent transactions on a <b>PostgreSQL</b> database, it is
 *   not enough to set up multiple connections: care must be taken that the
 *   various connection parameters are different for each connection, or else
 *   an already existent connection will be used. (This is an implementation
 *   decision of the PHP PostgreSQL module writers, not mine!)
 * </p>
 *
 * @package db_lib
 */
 
class PgTransaction extends Transaction
{
    /**
	 * Constructor
	 */
    function PgTransaction( &$database )
    {
        $this->Transaction( $database );
    }


    /**
     * @return string
     * @access public
     */
    function getBeginSql()
    {
        return 'BEGIN WORK';
    }
} // END OF PgTransaction

?>
