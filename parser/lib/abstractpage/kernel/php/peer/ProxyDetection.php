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
 * @package peer
 */
 
class ProxyDetection extends PEAR
{
	/**
	 * Pretty simple to add a port, just do like the next variable and repeat.
	 * @access public
	 */
    var $ports = array( 1 => '1080', '8080', '8000', '3128', '8888', '23', '80', '8081' ); 

	/**
	 * Keep adding hosts/secure proxys that you wish to allow to the website.
	 * @access public
	 */
    var $allowed = array( 
		'127.0.0.1', 
		'localhost' 
	); 
	
	
	/**
	 * @access public
	 */	
	function allow()
	{
		// Go ahead and check to see if host is allowed. 
    	if ( in_array( $_SERVER["REMOTE_ADDR"], $this->allowed ) ) 
			return true;
			
		return false;
	}
	
	/**
	 * @access public
	 */
	function detect()
	{
	    $i = 1; 
    	$found = false; 

	    // The while statement, keeps increasing by 1 until it checks 
		// all the ports in the ports array.
    	while ( $this->ports[$i] ) 
		{ 
        	// Opening the socket. 
        	$fp = fsockopen( $_SERVER["REMOTE_ADDR"], $this->ports[$i], $errno, $errstr, 1 ); 
        
			if ( $fp ) 
			{ 
            	flush(); 
            	fclose( $fp ); 
            
				$found = true; 
        	}

        	$i++; 
    	}	 

		return $found;
	}
} // END OF ProxyDetection

?>
