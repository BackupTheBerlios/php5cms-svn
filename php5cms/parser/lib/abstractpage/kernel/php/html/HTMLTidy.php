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
 * Class for HTMLTidy processing
 *
 * @todo tidy from file, string
 * @todo methods to set specific flags
 * @package html
 */

class HTMLTidy extends PEAR
{
	/**
	 * @access public
	 */
	var $tidy_path;
	
	/**
	 * @access public
	 */
	var $tidy_arg = array();
	
	/**
	 * @access public
	 */
	var $tidy_arg_str = "";
 	
	
	/**
 	 * Constructor
	 *
	 * @access public
  	 */
 	function HTMLTidy()
	{
		$this->tidy_path = "/usr/bin/tidy";
 	}
	

	/**
	 * @access public
	 */	
	function tidy()
	{
		ob_start();
		
		$str = addslashes( ob_get_contents() );
		$fp  = popen( "echo \"" . $str . "\" | $this->tidy_path $this->_getArgString()", "r" );

   		$newstr = "";
 
 		/*
		 * To parse your output through HTMLtidy where PHP=>4.3.2, it is important to
		 * know about the fread() bug (http://bugs.php.net/bug.php?id=24033). Previous
		 * versions had a bug which made the code "work as expected.".
		 */
 		do 
		{
       		$data = fread( $fp, 999999 );
       
	   		if ( strlen( $data ) == 0 )
           		break;
       
       		$newstr .= $data;
   		}
   		
		pclose( $fp );
		ob_end_clean();
		
   		header( "Content-length: " . strlen( stripslashes( $newstr ) ) );
  		echo stripslashes($newstr);
   		
		exit;
	}

	/**
	 * @access public
	 */	
	function tidyFromString( $html )
	{
		// TODO
		
		return $html;
	}
	
	/**
	 * @access public
	 */
	function tidyFromFile( $file )
	{
		$html = "";
		
		// TODO
		
		return $html;
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _getArgString()
	{
		return "-i -u -q -latin1 --indent-spaces 1 -wrap 0";
	}
} // END OF HTMLTidy

?>
