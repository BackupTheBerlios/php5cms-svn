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
 * @package format_csv
 */
 
class CSVWalk extends PEAR
{
	/**
	 * @access public
	 */
	var $fp;
	
	/**
	 * @access public
	 */
	var $length;

	/**
	 * @access public
	 */	
	var $delimiter = "";
	
	/**
	 * @access public
	 */
	var $csvfile = "";
	
	/**
	 * @access public
	 */
	var $fields = array();
	
	/**
	 * @access public
	 */
	var $filter = array();
	
	/**
	 * @access public
	 */
	var $arow = array();
	
	/**
	 * @access public
	 */
	var $irow = 0;
	
	/**
	 * @access public
	 */
	var $end = false;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function CSVWalk( $deli = ";", $len = 65536 )
 	{
  		$this->length    = $len;
  		$this->delimiter = $deli;
	}


	/**
	 * @access public
	 */	
	function open( $path )
	{
  		if ( is_dir( $path ) )
			$this->newest_file( $path, "\.csv$" );
  		else
			$this->csvfile = $path;
		
		if ( $this->fp = fopen( $this->csvfile, "r" ) )
		{
   			$this->fields = array_flip( fgetcsv( $this->fp, $this->length, $this->delimiter ) );
   			$this->arow   = $this->fields;
   
   			return true;
		}
  		else 
		{
   			return false;
		}
	}

	/**
	 * Looking for newest file of $type in $path
	 *
	 * @access public
	 */ 
 	function newest_file( $path, $type = ".*" )
	{
  		clearstatcache();
  		$path = ereg_replace( "/$", "", $path );
  
  		if ( $handle = opendir( $path ) )
		{
   			$told = 0;
   
   			while ( ( $file = readdir( $handle ) ) !== false )
			{ 
    			if ( $file != "." && $file != ".." && ereg( $type, $file ) && !is_dir( $path . DIRECTORY_SEPARATOR . $file ) )
				{
					$tnew = filemtime( $path . DIRECTORY_SEPARATOR . $file );
     
	 				if ( $tnew > $told )
					{
      					$told = $tnew;
      					$this->csvfile = $path . DIRECTORY_SEPARATOR . $file;
					}
				}
			}
			
			closedir( $handle );
		}
	}

	/**
	 * Set regex filter to field for reading
	 *
	 * @access public
	 */
 	function set_filter( $field, $regex )
	{
  		$this->filter[$field] = $regex;
	}

	/**
	 * Check one row for filter conditions.
	 *
	 * @access public
	 */
 	function check_filter( &$row )
	{
  		foreach ( $this->filter as $fld => $rex )
		{
   			$v = $this->fields[$fld];
   
   			if ( !ereg( $rex, $row[$v] ) )
				return false;
		}
  
  		return true;
	}

	/**
	 * Read next valid row.
	 *
	 * @access public
	 */
 	function next_row()
	{
  		$this->end = true;
  
  		while ( $adat = fgetcsv( $this->fp, $this->length, $this->delimiter ) )
		{
   			if ( count( $adat ) && $this->check_filter( $adat ) )
			{
    			$this->irow++;
				
    			$this->arow = $adat;
   	 			$this->end  = false;
    
				break;
			}
		}
  
  		return !$this->end;
	}

	/**
	 * Read field value of row (see example).
	 *
	 * @access public
	 */
 	function showfield( $field )
	{
  		$v = $this->fields[$field];
  		return $this->arow[$v];
	}

	/**
	 * File-pointer to start of file; reset filter manually.
	 *
	 * @access public
	 */
 	function reset()
	{
  		$this->irow = 0;
  		$this->arow = array();
  
  		rewind( $this->fp );
	}

	/**
	 * @access public
	 */
	function close()
	{
  		fclose( $this->fp );
	}
} // END OF CSVWalk

?>
