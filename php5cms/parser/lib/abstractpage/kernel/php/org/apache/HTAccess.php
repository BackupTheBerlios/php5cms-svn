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
 * Class for handling htaccess of Apache.
 *
 * Usage:
 *
 * $ht = new HTAccess;
 * $ht->setFPasswd("/var/www/htpasswd");		// Setting up path of password file.
 * $ht->setFHtaccess("/var/www/.htaccess");		// Setting up path of password file.
 * $ht->addUser("username","0815");				// Adding user.
 * $ht->setPasswd("username","newPassword");	// Changing password for User.
 * $ht->delUser("username");					// Deleting user.
 * $ht->setAuthName("My private Area");			// Setting authenification area name.
 * $ht->addLogin();								// Finally you have to process addLogin() to write out the .htaccess file.
 * $ht->delLogin();								// To delete a Login use the delLogin function.
 *
 * @package org_apache
 */

class HTAccess extends PEAR
{
	/**
	 * path and filename for htaccess file
	 * @access public
	 */
	var $fHtaccess = "";
	
	/**
	 * path and filename for htgroup file
	 * @access public
	 */
    var $fHtgroup = "";
	
	/**
	 * path and filename for passwd file
	 * @access public
	 */
    var $fPasswd = "";
    
	/**
	 * default authentification type
	 * @access public
	 */
    var $authType = "Basic";
	
	/**
	 * default authentification name
	 * @access public
	 */
    var $authName = "Internal area";
	
	
	/**
	 * Sets the filename and path of .htaccess to work with.
	 *
	 * @param  string  $filename  the name of htaccess file
	 * @access public
	 */
    function setFHtaccess( $filename )
	{
        $this->fHtaccess = $filename;
    }
    
	/**
	 * Sets the filename and path of the htgroup file for the htaccess file.
	 *
	 * @param  string  $filename	 the name of htgroup file
	 * @access public
	 */
    function setFHtgroup( $filename )
	{
        $this->fHtgroup = $filename;
    }
    
 	/**
	 * Sets the filename and path of the password file for the htaccess file.
	 *
	 * @param  string  $filename  the name of htgroup file
	 * @access public
	 */
    function setFPasswd( $filename )
	{
        $this->fPasswd = $filename;
    }

	/**
	 * Adds a user to the password file.
	 *
	 * @param  string  $username  Username
     * @param  string  $password  Password for Username
     * @param  string  $group     Groupname for User (optional)
     * @return boolean $created   Returns true if user have been created otherwise false
	 * @access public
  	 */
    function addUser( $username, $password, $group )
	{
        // Checking if user already exists.
        $file = @fopen( $this->fPasswd, "r" );
        $isAlready = false;
		
        while ( $line = @fgets( $file, 200 ) )
		{
            $lineArr = explode( ":", $line );
			
            if ( $username == $lineArr[0] )
                $isAlready = true;
        }
		
        fclose( $file );
        
		if ( $isAlready == false )
		{
            $file     = fopen( $this->fPasswd, "a" );
            $password = crypt( $password );
            $newLine  = $username . ":" . $password;
			
            fputs( $file, $newLine . "\n" );
            fclose( $file );
			
            return true;
        }
		else
		{
            return false;
        }
    }

	/**
	 * Adds a group to the htgroup file.
	 *
	 * @param  string  $groupname  Groupname
	 * @access public
  	 */
    function addGroup( $groupname )
	{
        $file = fopen( $this->fHtgroup, "a" );
        fclose( $file );
    }

	/**
	 * Deletes a user in the password file.
	 *
	 * @param  string  $username  Username to delete
     * @return boolean $deleted   Returns true if user have been deleted otherwise false
	 * @access public
  	 */
    function delUser( $username )
	{
        // Reading names from file.
        $file = fopen( $path . $this->fPasswd, "r" );

        $i = 0;
        while ( $line = fgets( $file, 200 ) )
		{
            $lineArr = explode( ":", $line );
            
			if ( $username != $lineArr[0] )
			{
                $newUserlist[$i][0] = $lineArr[0];
                $newUserlist[$i][1] = $lineArr[1];

                $i++;
            }
			else
			{
                $deleted = true;
            }
        }
		
        fclose( $file );

        // Writing names back to file (without the user to delete).
        $file = fopen( $path . $this->fPasswd, "w" );
        
		for ( $i = 0; $i < count( $newUserlist ); $i++ )
            fputs( $file, $newUserlist[$i][0] . ":" . $newUserlist[$i][1]);
     
        fclose( $file );
        
        if ( $deleted == true )
            return true;
        else
            return false;
    }
    
   	/**
	 * Returns an array of all users in a password file.
	 *
 	 * @return array $users  All usernames of a password file in an array
     * @see    setFPasswd()
	 * @access public
  	 */
    function getUsers()
	{
		// TODO
    }
    
	/**
	 * Sets a password to the given username.
	 *
	 * @param  string  $username  The name of the User for changing password
     * @param  string  $password  New Password for the User
     * @return boolean $isSet     Returns true if password have been set
	 * @access public
  	 */
    function setPasswd( $username, $new_password )
	{
        // Reading names from file.
        $newUserlist = "";
        
        $file = fopen( $this->fPasswd, "r" );
        $x    = 0;
		
        for ( $i = 0; $line = fgets( $file, 200 ); $i++ )
		{
            $lineArr = explode( ":", $line );
			
            if ( $username != $lineArr[0] && $lineArr[0] != "" && $lineArr[1] != "" )
			{
                $newUserlist[$i][0] = $lineArr[0];
                $newUserlist[$i][1] = $lineArr[1];
				$x++;
            }
			else if ( $lineArr[0] != "" && $lineArr[1] != "" )
			{
                $newUserlist[$i][0] = $lineArr[0];
                $newUserlist[$i][1] = crypt( $new_password ) . "\n";
                $isSet = true;
                $x++;
            }
        }

        fclose( $file );
        unlink( $this->fPasswd );

        // Writing names back to file (with new password).
        $file = fopen( $this->fPasswd, "w" );
        
		for ( $i = 0; $i < count( $newUserlist ); $i++ )
		{
            $content = $newUserlist[$i][0] . ":" . $newUserlist[$i][1];
            fputs( $file, $content );
        }
		
        fclose( $file );

        if ( $isSet == true )
            return true;
        else
            return false;
    }

	/**
	 * Sets the Authentification type for Login.
	 *
     * @param  string  $authtype  Authentification type as string
	 * @access public
  	 */
    function setAuthType( $authtype )
	{
        $this->authType = $authtype;
    }

	/**
	 * Sets the Authentification Name (Name of the login area).
	 *
     * @param  string  $authname  Name of the login area
	 * @access public
  	 */
    function setAuthName( $authname )
	{
        $this->authName = $authname;
    }

	/**
	 * Writes the htaccess file to the given Directory and protects it.
	 *
     * @param  string  $path  Path name to protect
     * @see    setFhtaccess()
	 * @access public
  	 */
    function addLogin()
	{
       $file = fopen( $this->fHtaccess, "w+" );
       fputs( $file, "Order allow,deny\n" );
       fputs( $file, "Allow from all\n"   );
       fputs( $file, "AuthType        "   . $this->authType . "\n"   );
       fputs( $file, "AuthUserFile    "   . $this->fPasswd  . "\n\n" );
       fputs( $file, "AuthName        \"" . $this->authName . "\"\n" );
       fputs( $file, "require valid-user\n" );
       
	   fclose( $file );
    }

    /**
	 * Deletes the protection of the given directory.
	 *
     * @param  string  $path  Path name to delete protection
     * @see    setFhtaccess()
	 * @access public
  	 */
    function delLogin()
	{
        unlink( $this->fHtaccess );
    }
} // END OF HTAccess

?>
