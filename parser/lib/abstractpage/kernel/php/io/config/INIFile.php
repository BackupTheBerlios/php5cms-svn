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
 * @package io_config
 */
 
class INIFile extends PEAR
{
	/**
	 * @access public
	 */
	var $ini_file_name = "";
	
	/**
	 * @access public
	 */
    var $current_group = ""; 
	
	/**
	 * @access public
	 */
    var $groups = array(); 
	
	
	/**
	 * Constructor 
	 *
	 * @access public
	 */
    function INIFile( $inifilename = "" ) 
    {
        if ( !empty( $inifilename ) )
		{
			if ( !file_exists( $inifilename ) )
			{
				$this = new PEAR_Error( "File does not exist." );
				return;
			}
		}
		 
        $this->parse( $inifilename ); 
    } 


	/**
	 * @access public
	 */
	function parse( $inifilename ) 
    { 
        $this->ini_file_name = $inifilename; 
        $fp = fopen( $inifilename, "r" ); 
        $contents = fread( $fp, filesize( $inifilename ) ); 
        $ini_data = split( "\n", $contents ); 
         
        while ( list( $key, $data) = each( $ini_data ) ) 
			$this->_parseData( $data );
         
        fclose( $fp ); 
    } 

	/**
	 * @access public
	 */
    function save() 
    { 
        $fp = fopen( $this->ini_file_name, "w" ); 
         
        if ( empty( $fp ) ) 
			return PEAR::raiseError( "Cannot create file " . $this->ini_file_name );
         
        $groups    = $this->readGroups(); 
        $group_cnt = count( $groups ); 
         
        for ( $i = 0; $i < $group_cnt; $i++ ) 
        { 
            $group_name = $groups[$i]; 
            $res = sprintf( "[%s]\n", $group_name ); 
            fwrite( $fp, $res ); 
            $group = $this->readGroup( $group_name );
			 
            for ( reset( $group ); $key = key( $group ); next( $group ) ) 
            { 
                $res = sprintf( "%s=%s\n", $key, $group[$key] ); 
                fwrite( $fp, $res ); 
            } 
        } 
         
        fclose( $fp ); 
    } 

	/**
	 * Returns number of groups.
	 *
	 * @access public
	 */
    function getGroupCount() 
    { 
        return count( $this->groups ); 
    } 
     
    /**
	 * Returns an array with the names of all the groups.
	 *
	 * @access public
	 */
    function readGroups() 
    { 
        $groups = array();
		
        for ( reset( $this->groups ); $key = key( $this->groups ); next( $this->groups ) ) 
            $groups[] = $key;
			 
        return $groups; 
    } 
     
    /**
	 * Checks if a group exists.
	 *
	 * @access public
	 */
    function hasGroup( $group_name ) 
    { 
        $group = $this->groups[$group_name]; 
        
		if ( empty( $group ) )
			return false; 
        else
			return true; 
    } 

    /**
	 * Returns an associative array of the variables in one group.
	 *
	 * @access public
	 */
    function readGroup( $group ) 
    { 
        $group_array = $this->groups[$group]; 
        
		if ( !empty( $group_array ) )
			return $group_array;
		else  
			return PEAR::raiseError( "Group does not exist." );
    } 
     
    /**
	 * Adds a new group.
	 *
	 * @access public
	 */
    function addGroup( $group_name ) 
    { 
        $new_group = $this->groups[$group_name];
		
        if ( empty( $new_group ) ) 
        	$this->groups[$group_name] = array();
        else
			return PEAR::raiseError( "Group already exists." );
    } 

	/**
	 * Reads a single variable from a group.
	 *
	 * @access public
	 */
    function get( $group, $var_name ) 
    { 
        $var_value = $this->groups[$group][$var_name]; 
        
		if ( !empty( $var_value ) )
			return $var_value; 
		else
			return PEAR::raiseError( "Var not found in group " . $group );
    } 
     
    /**
	 * Sets a variable in a group.
	 *
	 * @access public
	 */
    function put( $group, $var_name, $var_value ) 
    { 
        if ( $this->hasGroup( $group ) ) 
            $this->groups[$group][$var_name] = $var_value; 
    }
	
	
	// private methods
	
	/**
	 * @access private
	 */     
    function _parseData( $data ) 
    {
		// comment
		if ( preg_match( '/^#/', $data ) )
			continue;
					
        if ( ereg( "\[([[:alnum:]]+)\]", $data, $out ) ) 
        { 
            $this->current_group = $out[1]; 
        } 
        else 
        { 
            $split_data = split( "=", $data ); 
            $this->groups[$this->current_group][$split_data[0]] = $split_data[1]; 
        } 
    } 
} // END OF INIFile

?>
