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
 * Class <code>MyQueryResult</code> provides an implementation of the
 * <code>QueryResult</code> interface for use with the <code>MyDatabase</code>
 * class.
 *
 * @see MyDatabase
 * @package db_lib
 */

class MyQueryResult extends QueryResult 
{
    /**
     * The optional error message
     * @var  string
     */
    var $errorMessage;

    /**
     * The current row index
     * @var  int
     */
    var $currentRow;

    
	/**
	 * Constructor
	 */
    function MyQueryResult( &$database, $resultId ) 
    {
        $this->QueryResult( $database, $resultId );
		
        $this->currentRow   = 0;
        $this->errorMessage = ( $resultId !== false ) ? '' : $database->getErrorMessage();
    }


    /**
     * @return void
     */
    function clear() 
    {
        mysql_free_result( $this->getResultId() );
    }

    /**
     * @return string
     */
    function getErrorMessage() 
    {
        return $this->errorMessage;
    }

    /**
     * @return int
     */
    function getRowCount() 
    {
        return mysql_num_rows( $this->getResultId() );
    }

    /**
     * @return array
     */
    function getRow( $index, $type = DATABASE_BOTH ) 
    {
        if ( $index != $this->currentRow ) 
            mysql_data_seek( $this->getResultId(), $index );
        
        $this->currentRow = $index + 1;
        
		switch ( $type )
        {
            case DATABASE_ASSOC:
                return mysql_fetch_assoc( $this->getResultId() );
         
		    case DATABASE_NUM:
                return mysql_fetch_row( $this->getResultId() );
         
		    case DATABASE_BOTH:
         
		    default:
                return mysql_fetch_array( $this->getResultId() );
        }
    }
} // END OF MyQueryResult

?>
