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
|Authors: Setec Astronomy <setec@freemail.it>                          |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * @package org_samba
 */
 
class SambaConfig extends PEAR
{
	/**
	 * @access public
	 */
	var $filename;
	
	/**
	 * @access public
	 */
	var $smbconf;
	

	/**
	 * @access private
	 */
	function parse( $filename = ""  )
	{
		if ( empty( $filename ) )
			return PEAR::raiseError( "No filename specified." );
			
		$this->filename = $filename;		
		
		if ( !is_readable( $this->filename ) )
			return PEAR::raiseError( "File not readable." );
		
		$return = false;
		
		$this->smbconf = array();
		
		$section_name = "UNKNOWN";
		$value_name   = "UNKNOWN";
		$join_line    = false;
		$lines        = file( $this->filename );

		foreach ( $lines as $line )
		{
			$trim_line  = trim( $line );
			$begin_char = substr( $trim_line, 0, 1 );
			$end_char   = substr( $trim_line, -1 );
			
			// comment
			if ( ( $begin_char == "#" ) || ( $begin_char == ";" ) ) 
			{
				$raw = $trim_line; 
			}
			// section 
			else if ( ( $begin_char == "[" ) && ( $end_char == "]" ) ) 
			{
				$raw = $trim_line;
				$section_name = substr( $trim_line, 1, -1 );
			}
			// values 
			else if ( $trim_line != "" ) 
			{
				$raw = $trim_line;
				$pieces = explode( "=", $trim_line, 2 );
				
				if ( $join_line )
				{
					$this->smbconf[$section_name][$value_name][] = $trim_line;	
				} 
				else if ( count( $pieces ) == 2 )
				{
					$value_name = trim( $pieces[0] );
					$this->smbconf[$section_name][$value_name][] = trim( $pieces[1] );	
				}
			}
			
			$join_line = $end_char == "\\";
		}
		
		return $this->smbconf;
	}
	
	/**
	 * @access public
	 */
	function recreate( $smbconf = array() )
	{
		if ( !empty( $smbconf ) )
			$this->smbconf = $smbconf;
		
		$return = false;
		
		foreach ( $this->smbconf as $section => $content )
		{
			$return .= "[" . $section . "]\n";
			
			foreach ( $content as $key => $values )
			{
				$rows = implode( "\n\t\t", $values );
				$return .= "\t" . $key . " = " . $rows . "\n";
			}
			
			$return .= "\n";
		}
		
		return $return;
	}
} // END OF SambaConfig

?>
