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
 * @package Abstractpage
 */
 
class Base
{    
    /**
     * @access private
     */
    var $__id;
    
    
    /**
     * Constructor
     *
     * @access  public
     */
    public function __construct( $params = null ) 
    {
        $this->__id = microtime();
        
        if ( is_array( $params ) ) 
        {
            foreach ( $params as $key => $val ) 
                $this->$key = $val;
        }
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    public function __destruct()
    {
        unset( $this );
    }
    
    /**
     * Returns a hashcode for this object.
     *
     * @access  public
     * @return  string
     */
    public function hashCode()
    {
        return $this->__id;
    }
    
    /**
     * Indicates whether some other object is "equal to" this one.
     *
     * @access  public
     * @param   &Object cmp
     * @return  bool
     */
    public function equals( &$cmp ) 
    {
        return $this === $cmp;
    }
    
    /**
     * Serializes a object into a string.
     *
     * @access    public
     * @return    string        
     */    
    public function toString()
    {
        $objectvars = get_object_vars( $this );
        
        foreach ( $objectvars as $key => $value ) 
            $content = $content . $key ."='". $value. "'; ";
        
        return "Instance of " . get_class( $this ) . " with properties: " . $content;
    }
} // END OF Base

?>
