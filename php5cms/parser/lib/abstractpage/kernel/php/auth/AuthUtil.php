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
 * Static helper functions.
 *
 * @package auth
 */
 
class AuthUtil
{
	/**
	 * imapAuth eliminates the need to maintain/syncronize password lists
	 * you can just plug right into your organization's existing directory
	 * pass a username, password and server address to the function and
	 * imapAuth will return true if the user is valid or false otherwise.
     *
	 * Usage: 
	 * ( AuthUtil::imapAuth( "username", "password", "mail.yourdom.com" ) )?
	 *		print "valid" : print "failed";
	 *
	 * imapAuth has been tested with Microsoft Exchange Server 5.5 and 
	 * Redhat 6 and 7 IMAP servers but should work with all others.
	 *
	 * @access public
	 * @static
	 */
	function imapAuth( $username, $password, $auth_host, $tcp_port = 143 )
	{
		$server_reply = "";

		// connect to IMAP port
		$fp = fsockopen( "$auth_host", $tcp_port );
  
		// make sure that you get a response...
  		if ( $fp > 0 )
   		{
			$server_reply = fgets( $fp, 128 );
	
			if ( ord( $server_reply ) == ord( "*" ) )
			{
				// send username and password
				$user_info = fputs( $fp, "a999 LOGIN " . $username ." $password\r\n" );
				
				$server_reply = fgets( $fp, 128 );
			
				// see if your login was valid
				$foo = strncmp( $server_reply, "a999 OK LOGIN", 13 );
		
				if ( $foo == "0" )
				{
					// password accepted
					return true;
				}
				else
				{
					// password accepted
					return false;
				}
				
				// say goodbye...
				fputs( $fp, "a999 LOGOUT" . "\r\n" );
				
				$server_reply = fgets( $fp, 128 );
				fclose( $fp );
			}
			else
			{
				return PEAR::raiseError( "Problem conversing with host." );
			}
  	 	}
   		else
   		{
			return PEAR::raiseError( "No response from specified host." );
  		}
	}
	
	/**
	 * popAuth eliminates the need to maintain/syncronize password lists
	 * you can just plug right into your organization's existing directory
	 * pass a username, password and server address to the function and
	 * popAuth will return true if the user is valid or false otherwise.
 	 * 
	 * Usage: 
	 * ( AuthUtil::popAuth( "username", "password", "mail.yourdom.com" ) )?
	 *		print "valid" : print "failed";
	 *
	 * popAuth has been tested with Microsoft Exchange Server 5.5 and 
	 * Redhat 6 and 7 IMAP servers but should work with all others.
	 *
	 * @access public
	 * @static
	 */
	function popAuth( $username, $password, $auth_host, $tcp_port = 110 )
	{
		// connect to pop port
   		$fp = fsockopen( "$auth_host", $tcp_port );
   
  	 	// make sure that you get a response...
   		if ( $fp > 0 )
   		{
			// send username
   			$user_info = fputs( $fp, "USER " . $username. "\r\n" );
      
		  	if ( !$user_info )
    	  	{
				return PEAR::raiseError( "Problem conversing with host." );
 	     	}
    	  	else
      		{
	        	$server_reply = fgets( $fp, 128 );
	
				if ( ord( $server_reply ) == ord( "+" ) )
        		{
        	        fputs( $fp, "PASS " . $password . "\r\n" );
            	    $passwd_attempt = fgets( $fp, 128 );
                
					if ( ord( $passwd_attempt ) == ord( "+" ) )
            	    {
						// password accepted
						return true;
         	       }
            	    else
                	{
						// password failed
						return false;
        	        }
        		}
        
				fputs( $fp, "QUIT" . "\r\n" );
    	    	fclose( $fp );
      		}
   		}
   		else
   		{
			return PEAR::raiseError( "No response from specified host." );
	   	}
	}
} // END OF AuthUtil

?>
