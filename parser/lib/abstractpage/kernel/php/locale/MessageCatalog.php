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
 * Implements Message Catalog like utility to PHP.
 *
 * @package locale
 */

class MessageCatalog extends PEAR
{  
	/**
	 * @access public
	 */
  	var $mclistcount;
	
	/**
	 * Identifies current locale
	 * @access private
	 */
  	var $mclocale;
	
	/**
	 * full path to .msg files
	 * @access private
	 */
  	var $mcloaddir;
	
	/**
	 * Array for the translations for mclocale
	 * @access private
	 */
  	var $mclist;
  

	/**
	 * Constructor
	 *
	 * @access public
	 */ 
  	function MessageCatalog( $localstr, $loaddir = "./" )
    {
    	$this->mclocale    = $localstr;
    	$this->mcloaddir   = $loaddir;
    	$this->mclistcount = 0;
		
    	$this->mclist_create();
    }
    
	
	/**
	 * Returns translated string for STRING $srcstr.
	 *
	 * @access public
	 */
	function mc( $srcstr )
    {
    	$retstr = $this->mclist[$srcstr];
    
		if ( !$retstr ) 
			$retstr = $this->mcunknown( $srcstr );
    
		return $retstr ;
    }
    

	// private methods
  
  	/**
	 * Rebuild this function to suit your needs... called when no translation was found.
	 *
	 * @access private
	 */
  	function mcunknown( $srcstr )
	{
    	return $srcstr ;
	}
    
	/**
	 * sets mc on $localestr locale for $srcstr as $trstr.
	 *
	 * @access private
	 */
	function mcset( $localestr, $srcstr, $trstr )
    {
    	if ( $localestr == $this->mclocale )
		{
      		$this->mclist += Array( $srcstr => $trstr ) ;
      		$this->mclistcount++ ;
    	}
	}
    
	/**
	 * Generates message catalog from file.
	 *
	 * @access private
	 */
	function mclist_create()
    {
		$this->mclist = array();
    	$fp = fopen( $this->mcloaddir . $this->mclocale . ".msg", "r" );
    
		if ( $fp )
		{  
	  		while ( !feof( $fp ) )
        	{
        		$str = fgets( $fp, 4096 );
        
				// note on UNIX/Linux CRLF is length 2 not 1, change -1 to -2
				if ( substr( $str, 0, 2 ) != "//" && trim( $str ) != '' ) 
          		{
          			$data = explode( "#:#", trim( $str ) );
          			$this->mcset( $data[0], $data[1], $data[2] );
          		}
        	} 
      
	  		fclose( $fp );
    	}
	}
} // END OF MessageCatalog

?>
