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

class SocketUtil extends PEAR 
{
	/**
	 * @access public
	 */
    var $sock; 
	
	/**
	 * @access public
	 */
	var $maxget = 2048; 


	/**
	 * Constructor
	 *
	 * @access public
	 */
    function SocketUtil() 
	{
        $this->sock = socket_create( AF_INET, SOCK_STREAM, 0 );
		
        if ( $this->sock < 0 ) 
            return false; 
		else 
            return true; 
    } 


	/**
	 * @access public
	 */
    function destroy() 
	{ 
        if ( $this->sock ) 
            socket_close( $this->sock ); 
    } 

	/**
	 * @access public
	 */
    function connect( $host, $port ) 
	{ 
		if ( @socket_connect( $this->sock, $host,$port ) ) 
            return true; 
		else 
            return false; 
    } 

	/**
	 * @access public
	 */     
    function send( $cmd ) 
	{ 
        if ( $this->sock ) 
		{ 
            $cmd    = $cmd . "\r\n";
			$result = @socket_write( $this->sock, $cmd, strlen( $cmd ) );
			
			if ( !$result )
				return false;
			else
				return true;
        } 
		else 
		{ 
            return false; 
        } 
    } 

	/**
	 * @access public
	 */     
    function read() 
	{ 
        if ( $this->sock ) 
		{ 
            if ( $out = @socket_read( $this->sock, $this->maxget, PHP_BINARY_READ ) ) 
			{ 
                $out = chop( $out );
                return $out; 
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
	 * @access public
	 */
    function readall() 
	{ 
        if ( $this->sock ) 
		{ 
            for ( $i = 0; $i < 10; $i++ ) 
                $buffer[] = $this->read();
            
            return $buffer; 
        } 
		else 
		{ 
            return false; 
        } 
    } 

	/**
	 * @access public
	 */
    function readtill() 
	{ 
        if ( $this->sock ) 
		{ 
            $data = ""; 
            while ( $buffer = @socket_read( $this->sock, 512 ) ) 
			{ 
                $data .= $buffer; 
                
				if ( preg_match( "/(\r|\r\n|\n){2}$/", $data ) ) 
					break; 
            }
			
            return $data; 
        } 
		else 
		{ 
			return false; 
        } 
    } 
     
	/**
	 * Sends the command and returns the response array.
	 *
	 * @access public
	 */
    function sr( $cmd ) 
	{ 
        $this->send( $cmd ); 
        return ( $this->read() ); 
    } 
} // END OF SocketUtil 

?> 
