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
 * Class <code>MSQueryResult</code> provides an implementation of the
 * <code>QueryResult</code> interface for use with the <code>MSDatabase</code>
 * class.
 *
 * @see MSDatabase
 * @package db_lib
 */
 
class MSQueryResult extends QueryResult 
{
    /**
     * The optional error message
     * @var  string
     */
    var $errorMessage;

    /**
     * The previous row index
     * @var  int
     */
    var $currentRow;

    
	/**
	 * Constructor
	 */
    function MSQueryResult( &$database, $resultId ) 
    {
		$this->QueryResult( $database, $resultId );
		
        $this->currentRow   = 0;
        $this->errorMessage = ( $resultId !== false )? '' : $database->getErrorMessage();
    }


    /**
     * @return void
     */
    function clear() 
    {
        mssql_free_result( $this->getResultId() );
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
        return mssql_num_rows( $this->getResultId() );
    }

    /**
     * @return array
     */
    function getRow( $index, $type = DATABASE_BOTH )
    {
        // Jump to the correct row if necessary
        if ( $index != $this->currentRow ) 
            mssql_data_seek( $this->getResultId(), $index );
        
        $this->currentRow = $index + 1;
        
		switch ( $type )
        {
            case DATABASE_ASSOC:
                return mssql_fetch_assoc( $this->getResultId() );

            case DATABASE_NUM:
                return mssql_fetch_row( $this->getResultId() );

            case DATABASE_BOTH:

            default:
                return mssql_fetch_array( $this->getResultId() );
        }
    }
} // END OF MSQueryResult

?>
