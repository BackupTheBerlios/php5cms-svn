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
 * @package io
 */
 
class AutorunFile extends PEAR
{
	/**
	 * @access private
	 */
	$_file_name = "autorun.inf";
	

	/**
	 * @access public
	 */
	function get( $open_file = "index.htm", $icon_file = "icon.ico" )
	{
		$out  = ";"; // CMS-Signature + date
		$out .= "[autorun]\n";
		$out .= "OPEN=" . $open_file . "\n";
		$out .= "ICON=" . $icon_file . "\n";
		
		return $out;
	}
	
	/**
	 * @access public
	 */
	function write( $dir = "", $open_file = "index.htm", $icon_file = "icon.ico" )
	{
		if ( is_dir( $dir ) && is_writeable( $dir ) )
		{
			$content = $this->get( $open_file, $icon_file );
			
			// TODO: do some error checking
			
			$fh = fopen( $this->_file_name, "wb" );
			
			if ( !$fh )
				return PEAR::raiseError( "Cannot write 'autorun.inf' file." );
				
			fwrite( $fh, $str );
			fclose( $fh );
			
			return true;
		}
		else
		{
			return PEAR::raiseError( "Cannot write 'autorun.inf' file." );
		}
	}
} // END OF AutorunFile

?>
