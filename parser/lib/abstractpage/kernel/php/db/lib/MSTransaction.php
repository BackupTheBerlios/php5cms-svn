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
|Authors: Vincent Oostindi� <eclipse@sunlight.tmfweb.nl>               |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'db.lib.Transaction' );


/**
 * Class <code>MSTransaction</code> implements the <code>Transaction</code>
 * interface for <b>Microsoft SQL Server</b> databases.
 *
 * @package db_lib
 */
 
class MSTransaction extends Transaction
{
    /**
	 * Constructor
	 */
    function MSTransaction( &$database )
    {
        $this->Transaction( $database );
    }
    

    /**
     * @return string
     * @access public
     */
    function getBeginSql()
    {
        return 'BEGIN TRANSACTION';
    }
} // END OF MSTransaction

?>
