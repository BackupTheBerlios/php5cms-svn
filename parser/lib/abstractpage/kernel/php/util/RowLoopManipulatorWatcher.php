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
|Authors: Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'util.array.ArrayIterator' );


/**
 * Class <code>RowLoopManipulatorWatcher</code> implements a watcher to be used
 * with class <code>RowLoopManipulator</code>.
 * <p>
 *   This class is meant to be used only in cooperation with class 
 *   <code>RowLoopManipulator</code>. See that class for more details.
 * </p>
 *
 * @see RowLoopManipulator
 * @package util
 */

class RowLoopManipulatorWatcher extends PEAR
{
    /**
     * The manipulator this watcher is for
     * @var    RowLoopManipulator
	 * @access public
     */
    var $manipulator;
    
    /**
     * The column this watcher is watching
     * @var    string
	 * @access public
     */
    var $column;
    
    /**
     * The list of methods to call on the manipulator when the watched column
     * changes.
     * @var    array
	 * @access public
     */
    var $methods;
    
    /**
     * The last known value of the watched column
     * @var    string
	 * @access public
     */
    var $value;
    
    
    /**
     * Constructor
	 *
     * @param  $manipulator the loop manipulator this watcher is for
     * @param  $column the column this watcher is for
	 * @access public
     */
    function RowLoopManipulatorWatcher( &$manipulator, $column )
    {
        $this->manipulator =& $manipulator;
        $this->column      =  $column;
        $this->methods     =  array();
        
		unset( $this->value );
    }
    

    /**
     * Register a method to call on the manipulator when the watched column
     * changes. If the specified method is already registered, nothing happens.
	 *
     * @param  $method the method to call when the watched column changes
     * @return void
	 * @access public
     */
    function register( $method )
    {
        if ( !in_array( $method, $this->methods ) )
            array_push( $this->methods, $method );
    }
    
    /**
     * Unregister a method from this watcher. If the method wasn't registered in
     * the first place, nothing happens.
	 *
     * @param  $method the method to remove from the list of registered methods
     * @return void
	 * @access public
     */
    function unregister( $method )
    {
        if ( ( $index = array_search( $method, $this->methods ) ) !== false )
            array_splice( $this->methods, $index, 1 );
    }
    
    /**
     * Check a row and call all registered methods if the watched column has
     * changed.
	 *
     * @param  $row the row to check
     * @param  $index the index of the current row
     * @return void
	 * @access public
     */
    function check( &$row, $index )
    {
        if ( !isset( $row[$this->column] ) )
            return;
        
        if ( isset( $this->value ) && $row[$this->column] == $this->value )
            return;
        
        $it =& new ArrayIterator( $this->methods );
		
        for ( ; $it->isValid(); $it->next() )
        {
            $method =& $it->getCurrent();
            $this->manipulator->$method( $row, $index );
        }
		
        $this->value = $row[$this->column];
    }
} // END OF RowLoopManipulatorWatcher

?>
