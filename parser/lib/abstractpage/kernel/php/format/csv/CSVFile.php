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
 
class CSVFile extends PEAR
{
	/**
	 * Filename
	 * @access public
	 */
	var $filename = "";
	
	/**
	 * Filepointer - false if no file open
	 * @access public
	 */
	var $fp = false;
	
	/**
	 * Open mode - default is read only
	 * @access public
	 */
	var $mode = "r";
	
	/**
	 * Default length of line (must be greater than the longest line)
	 * @access public
	 */
	var $length = 65536;
	
	/**
	 * Default delimiter
	 * @access public
	 */
	var $delimiter = "|";


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function CSVFile( $filename = "", $length = "" )
	{
		if ( $filename != "" )
			$this->setFilename( $filename );
		
		if ( ( $length != "" ) )
			$this->setLength( $length );
	}

	
	/**
	 * Opens $filename with $mode. Returns true on success, false otherwise.
	 *
	 * @access public
	 */
	function open( $mode )
	{
		if ( ( $this->filename != "" ) && $this->_is_mode( $mode ) )
		{
			if ( $this->fp )
				$this->close();
				
			$fp = fopen( $this->filename, $mode );
			
			if ( $fp )
			{
				$this->mode	= $mode;
				$this->fp 	= $fp;
				
				return true;
			}
			else
			{
				$this->fp	= false;
				$this->mode	= false;
				
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Close an open file. Returns true on success, false otherwise.
	 *
	 * @access public
	 */
	function close()
	{
		if ( $this->fp )
		{		
			fclose( $this->fp );
			
			$this->fp   = false;
			$this->mode = false;
			
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Add array to CSV file delimited by delimiter. Returns true if succeeded, false otherwise.
	 * [string] is used by $this->removeRow(), and should not be used out side the class.
	 *
	 * @access public
	 */
	function addRow( $data, $type = "add" )
	{
		if ( $this->fp && ( is_array( $data ) ) && ( ( ( $this->mode == "a" ) && ( $type == "add" ) ) || ( ( $this->mode == "w" ) && ( $type == "delete" ) ) ) )
		{
			// Count how many fields we need (field1|field2|...|fieldn).
			$elements = count( $data );
			
			for ( $i = 0; $i < $elements; $i++ )
			{
				// Create the entry to add to CSV file.
				$CSVentry = ( $i == 0 )? $data[$i] : $CSVentry . $this->delimiter . $data[$i];
			}
			
			if ( (fputs( $this->fp, $CSVentry . "\n" ) ) )
				return true;
			else
				return false;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Read a line in CSV file, except the last those with 1 element which is like "".
	 * Returns an array with the read data if succeeded, false otherwise. 
	 *
	 * @access public
	 */
	function readRow()
	{
		// If we have an open file in the right mode then...
		if ( $this->fp && ( ( $this->mode == "r" ) || ( $this->mode == "r+" ) ) )
		{
			// If read row from CSV file.
			if ( $row = fgetcsv( $this->fp, $this->length, $this->delimiter ) )
			{
				// Don't return a blank line (for instance the last).
				if ( ( count( $row ) != 1 ) && ( $row[0] != "" ) )
					return $row;
				else
					return false;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Read entire CSV file except the row "WHERE element[$field] like $test", 
	 * and writes all read data. Returns true on succeess, false otherwise.
	 *
	 * @access public
	 */
	function removeRow( $test, $field = 1 )
	{	
		// Store mode so we can open the file in the same state as it was before.
		if ( $this->mode )
		{
			$tmp_mode = $this->mode; 
			$tmp = true; 
		}
		else
		{
			$tmp = false;
		}
		
		// Open file for reading, if field is of type int and test isn't empty.
		if ( $this->open( "r" ) && ( gettype( $field ) == "integer" ) && ( $test != "" ) )
		{	
			// Set some test variables.
			$i = 0;
			$succes = false;
			
			while ( $row = $this->readRow() )
			{
				$elements = count( $row );		
				
				if ( $row[$field-1] != $test )
				{	
					$listRows[$i] = $row; 			
					$i++;
				}
				else
				{
					$success = true;			
				}
			}
			
			$this->close();
	
			if ( $success )
			{		
				if ( is_array( $listRows ) )
				{											
					reset( $listRows );						
					$this->open( "w" );
							
					for ( $i = 0; $i < count( $listRows ); $i++ )
						$this->addRow( $listRows[$i], "delete" );										
					
					$this->close();			
				}
				else
				{
					$this->open( "w" );					
					$this->close();
				}
				
				if ( $tmp )
					$this->open( $tmp_mode ); 
							
				return true;
			}
			else
			{
				if ( $tmp )
					$this->open( $tmp_mode );
					 
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Sets filename to open. Returns true on succeess, false otherwise.
	 *
	 * @access public
	 */
	function setFilename( $filename )
	{
		if ( $this->_file_check( $filename ) )
		{
			if ( $this->fp )
				$this->close();
				
			$this->filename = $filename;
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Sets delimiter. If not argument are provided, default value is used. Returns true.
	 *
	 * @access public
	 */
	function setDelimiter( $delimiter = "" )
	{
		if ( $delimiter != "" )
			$this->delimiter = $delimiter;
		else
			$this->delimiter = "|";
		
		return true;
	}

	/**
	 * Sets length of longest line. If not argument are provided, default value is used.
	 * Returns true on positives integers, false otherwise.
	 *
	 * @access public
	 */
	function setLength( $length = 65536 )
	{
		if ( ( gettype( $length ) == "integer" ) && ( $length > 0 ) )
		{
			$this->length = $length;
			return true;
		}
		else
		{
			return false;
		}
	}


	// private methods
	
	/**
	 * Checks string to see if it is a valid mode. Returns true on succeess, false otherwise.
	 *
	 * @access private
	 */
	function _is_mode( $mode )
	{
		if ( ( $mode == "r" ) || ( $mode == "r+" ) || ( $mode == "w" ) || ( $mode == "w+" ) || ( $mode == "a" ) || ( $mode == "a+" ) )
			return true;
		else
			return false;
	}

	/**
	 * Checks string to see if $filename exists. Returns true on succeess, false otherwise.
	 *
	 * @access private
	 */
	function _file_check( $filename )
	{
		if ( file_exists( $filename ) )
			return true;
		else
			return false;
	}
} // END OF CSVFile

?>
