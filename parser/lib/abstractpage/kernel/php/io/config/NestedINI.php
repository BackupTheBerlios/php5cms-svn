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
 
class NestedINI extends PEAR
{	
	/**
	 * @access private         
	 * @var object FileHandle
	 */			
	var $_file;
		
	/**
	 * @access private         
	 * @var Array holds the settings-data 
	 */
	var $_settings;
		
		
	/**
	 * Constructor
	 *
	 * @access public
	 * @param	string	filename of script to parse
	 */		
	function NestedINI( $filename = "" )
	{
		$this->_file = $filename;
	}
		
	
	/**
	 * Set script-file that should be parsed.
	 *
	 * @access public
	 * @param  string	filename of script to parse
	 */		
	function setScriptFile( $filename ) 
	{
		$this->_file = $filename;
	}
		
	/**
	 * @access public
	 * @return Object settings as array
	 */		
	function parse()
	{
		if ( $this->_checkFile() )
		{
			$brace  = (int)0;
			$script = file( $this->_file );
			
			if ( sizeof( $script ) >= 1 )
			{	
				// run through lines of script-code
				foreach ( $script as $line )
				{
					if ( eregi( "\{", $line ) ) 
					{
						if ( $brace != 0 ) 
						{
							$recLines[] = $line;
						} 
						else 
						{
							$s		= $this->_getValues( $line );
							$subset = $s[0];
						}
						
						$brace++;
					} 
					else if ( eregi( "\}", $line ) ) 
					{
						$brace--;
						
						if ( $brace == 0 )
							$this->_getSettings( $subset, $recLines );
						else
							$recLines[] = $line;	
					}
					else
					{
						if ( $brace == 0 ) 
						{
							$s = $this->_getValues( $line );
							$this->_settings[$s[0]] = $s[1];
						}
						else
						{
							$recLines[] = $line;
						}
					}
				}
				
				// check if all braces are closed
				if ( $brace != (int)0 )
					return PEAR::raiseError( "Missing brace." );
				else
					return $this->_settings;	
			} 
			else 
			{
				return PEAR::raiseError( "File is empty." );
			}
		} 
		else 
		{
			return PEAR::raiseError( "File does not exist." );
		}
	}
		
		
	// private methods
	
	/**
	 * This function will be called every time a nested setting has been found.
	 *
	 * @access private
	 */				
	function _getSettings( $name, $lines ) 
	{
		$brace = (int)0;
		
		foreach ( $lines as $line ) 
		{
			if ( eregi( "\{", $line ) ) 
			{
				if ( $brace != 0 ) 
				{
					$recLines[] = $line;
				}
				else
				{				
					$s		= $this->_getValues( $line );
					$subset	= $s[0];
				}
				
				$brace++;
			} 
			else if ( eregi( "\}", $line ) ) 
			{
				$brace--;
				
				if ( $brace == 0 )
					$this->_getSettings( $name . "." . $subset, $recLines );
				else
					$recLines[] = $line;
			} 
			else 
			{
				if ( $brace == 0 )
				{
					$s = $this->_getValues( $line );
					$this->_settings[$name . "." . $s[0]] = $s[1];
				} 
				else 
				{
					$recLines[] = $line;
				}			
			}
		}
	}	
	
	/**
	 * This function returns the values of a script.setting
	 * e.g.	myvar = myval	returns array(myvar, myval)
	 *
	 * @return array Array with ValueName and ValueValue
	 * @access private
	 */			
	function _getValues($line) {
		$name  = trim( trim( substr( $line, 0, strpos( $line, "=" ) ),  "\t " ) );
		$value = trim( trim( substr( $line, strpos( $line, "=" ) + 1 ), "\t " ) );

		return array( $name, $value );
	}
	
	/**
	 * @access private
	 * @return boolean
	 */		
	function _checkFile()
	{
		if ( is_file( $this->_file ) )
			return true;
		else
			return false;
	}
} // END OF NestedINI

?>
