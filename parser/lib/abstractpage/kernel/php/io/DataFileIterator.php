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


using( 'util.Iterator' );


/**
 * Class <code>DataFileIterator</code> implements the iterator pattern for
 * <code>DataFile</code> objects.
 *
 * @see DataFile
 * @package io
 */
 
class DataFileIterator extends Iterator 
{
    /**
     * The <code>DataFile</code> whose contents must be iterated over
     * @var DataFile
	 * @access public
     */
    var $datafile;

    /**
     * The current index
     * @var int
	 * @access public
     */
    var $current;

    /**
     * The total number of records
     * @var int
	 * @access public
     */
    var $max;


    /**
     * Constructor
	 *
     * @param  $datafile the <code>DataFile</code> to iterate over
	 * @access public
     */
    function DataFileIterator( &$datafile ) 
    {
        $this->datafile =& $datafile;
        $this->max      =  $datafile->getRecordCount();
        
		$this->reset();
    }


    /**
     * @return void
     */
    function reset() 
    {
        $this->current = 0;
    }

    /**
     * @return void
     */
    function next() 
    {
        $this->current++;
    }

    /**
     * @return bool
     */
    function isValid() 
    {
        return $this->current < $this->max;
    }

    /**
     * @return array
     */
    function &getCurrent() 
    {
        return $this->datafile->getRecord( $this->current );
    }
} // END OF DataFileIterator

?>
