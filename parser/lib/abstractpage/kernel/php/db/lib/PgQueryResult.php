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


using( 'db.lib.QueryResult' );


/**
 * Class <code>PgQueryResult</code> provides an implementation of the
 * <code>QueryResult</code> interface for use with the <code>PgDatabase</code>
 * class.
 *
 * @see PgDatabase
 * @package db_lib
 */
 
class PgQueryResult extends QueryResult 
{
    /**
     * The optional error message from running the query
     * @var  string
     */
    var $errorMessage;

    
	/**
	 * Constructor
	 */
    function PgQueryResult( &$database, $resultId ) 
    {
        $this->QueryResult( $database, $resultId );
        $this->errorMessage = ( $resultId !== false )? '' : pg_last_error( $database->getLink() );
    }
    

    /**
     * @return string
     */
    function getErrorMessage() 
    {
        return $this->errorMessage;
    }

    /**
     * @return void
     */
    function clear() 
    {
        pg_free_result( $this->getResultId() );
    }

    /**
     * @return int
     */
    function getRowCount() 
    {
        return pg_num_rows( $this->getResultId() );
    }

    /**
     * @return array
     */
    function getRow( $index, $type = DATABASE_BOTH ) 
    {
        switch ( $type )
        {
            case DATABASE_ASSOC:
                return pg_fetch_array(
                    $this->getResultId(), 
                    $index, 
                    PGSQL_ASSOC
                );
         
		    case DATABASE_NUM:
                return pg_fetch_row( 
					$this->getResultId(), 
					$index 
				);
         
		    case DATABASE_BOTH:
         
		    default:
                return pg_fetch_array(
                    $this->getResultId(), 
                    $index, 
                    PGSQL_BOTH
                );
        }
    }
} // END OF PgQueryResult

?>
