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


/**
 * Class <code>DataFileReader</code> is a class for processing lines in a 
 * <code>DataFile</code>, and can also be used as a base class for new readers.
 * <p>
 *   By default, <code>DataFile</code>s are assumed to have a descriptor on the
 *   first line, with the fields separated by a <code>|</code>-character.
 * </p>
 * <p>
 *   The descriptor is a list of fieldnames, separated by the same separator as
 *   is used for the records. For example:
 * </p>
 * <pre>
 *   name     | description         | link
 *   sunlight | My personal website | www.sunlight.tmfweb.nl
 *   dawn     | PHP framework       | www.sunlight.tmfweb.nl/dawn
 *   ...      | ...                 | ...
 * </pre>
 * <p>
 *   The <code>DataFileReader</code> normally returns an integer-indexed array
 *   of fields, and if a descriptor is used, the array is also indexed on
 *   fieldname. Thus in the above example, if <code>$fields</code> is the array
 *   returned by the reader, both <code>$fields[1]</code> and
 *   <code>$fields['description']</code> point to the same value. This is very
 *   useful, as it allows the addition of additional fields in the file at
 *   any index, without enforcing a rewrite of existing code. Also, a possible
 *   transition to databases and queries is extremely simplified.
 * </p>
 * <p>
 *   Class <code>DataFileReader</code> has a method <code>filter</code> that
 *   does nothing by default, but subclasses can override it to select only
 *   those records that are of interest. The method gets an array of fields as
 *   its sole parameter, and returns this same array if the record should show
 *   up in the result. By returning <code>false</code> instead of the array,
 *   the record will be skipped. In short, subclasses need only implement the
 *   method <code>filter</code> to enable some kind of specialized filtering.
 * </p>
 *
 * @see DataFile
 * @package io
 */
 
class DataFileReader extends PEAR
{
    /**
     * The separator between fields
     * @var  string
     */
    var $separator;

    /**
     * The array of descriptors
     * @var  array
     */
    var $fields;

    /**
     * The number of fields in a record
     * @var  int
     */
    var $length;

    /**
     * Whether the first line contains a descriptor or not
     * @var  bool
     */
    var $hasDescription;

    
    /**
     * Constructor
	 *
     * @param $hasDescription whether the first line contains a descriptor
     * @param $separator the separator between fields
     */
    function DataFileReader( $hasDescription = true, $separator = '|' ) 
    {
        $this->separator      = $separator;
        $this->hasDescription = $hasDescription;
        $this->length         = 0;
        $this->fields         = array();
    }

	
    /**
     * Process a single line and turn it into a record (or <code>false</code> 
     * if the record is invalid).
	 *
     * @param $line the line to process
     * @return array
     */
    function parseLine( $line ) 
    {
        $fields = $this->getFields( $line );
        $size   = count( $fields );
		
        if ( !$this->length ) 
        {
            $this->length = $size;
            
			if ( $this->hasDescription )
            {
                $this->fields = $fields;
                return false;
            }
			
            return $this->filter( $fields );
        }
		
        if ( $size != $this->length ) 
            return false;
        
        if ( $this->hasDescription ) 
        {
            for ( $i = 0; $i < $size; $i++ ) 
                $fields[$this->fields[$i]] =& $fields[$i];
        }
		
        return $this->filter( $fields );
    }

    /**
     * Apply a filter on a record; if the record is valid, return the array
     * itself; in all other cases return <code>false</code>
	 *
     * @return array
     */
    function filter( $fields )
    {
        return $fields;
    }

    /**
     * Convert a line to an array of fields
	 *
     * @access  private
     * @param   $line the line to convert
     * @return array
     */
    function getFields( $line )
    {
        return array_map( 'trim', explode( $this->separator, $line ) );
    }
} // END OF DataFileReader

?>
