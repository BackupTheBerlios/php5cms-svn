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
 * @package auth
 */
 
class BasicAuth extends PEAR
{
	/**
	 * @access public
	 */
    var $users;
	
	/**
	 * @access public
	 */
	var $realm = "";
	
	/**
	 * @access public
	 */
    var $authenticated = -1;
    
	
	/**
	 * Constructor
	 * 
	 * @access public
	 */ 
    function BasicAuth( $message = "Access Denied", $realm = "<private>" )
    {
		$this->realm   = $realm;
        $this->message = $message
    }
    

	/**
	 * @access public
	 */    
    function authenticate()
    {
        if ( $this->isAuthenticated() == 0 )
        {
            header( "HTTP/1.0 401 Unauthorized" );
            header( "WWW-Authenticate: Basic realm=\"$this->realm\"" );
			
            echo( $this->message );
			exit();
        }
        else
        {
            header( "HTTP/1.0 200 OK" );
        }
    }
    
	/**
	 * @access public
	 */
    function addUser( $user, $passwd )
    {
        $this->users[$user] = $passwd;
    }
    
	/**
	 * @access public
	 */
    function isAuthenticated()
    {
    	if ( $this->authenticated < 0 )
    	{
        	if ( isset( $_SERVER["PHP_AUTH_USER"] ) ) 
 				$this->authenticated = $this->validate( $_SERVER["PHP_AUTH_USER"], $_SERVER["PHP_AUTH_PW"] );
			else
				$this->authenticated = 0;
        }
        
        return $this->authenticated;
    }
    
	/**
	 * @access public
	 */
    function validate( $user, $passwd )
    {
        if ( strlen( trim( $user ) ) > 0 && strlen( trim( $passwd ) ) > 0 )
        {
            // both $user and $password are non-zero length
            if ( isset( $this->users[$user] ) && $this->users[$user] == $passwd )
            	return true;
        }
		
        return false;
    }
} // END OF BasicAuth

?>
