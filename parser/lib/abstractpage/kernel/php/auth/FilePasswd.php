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
 * Manipulate standard UNIX passwd, .htpasswd and CVS pserver passwd files.
 *
 * @package auth
 */

class FilePasswd extends PEAR
{
    /**
     * Passwd file
     * @var string
     */
    var $filename ;

    /**
     * Hash list of users
     * @var array
     */
    var $users ;
    
    /**
     * hash list of csv-users
     * @var array
     */
    var $cvs ;
    
    /**
     * filehandle for lockfile
     * @var int
     */
    var $fplock ;
    
    /**
     * locking state
     * @var boolean
     */
    var $locked ;
    
    /**
     * name of the lockfile
     * @var string    
     */ 
    var $lockfile = './passwd.lock';

	
    /**
     * Constructor
	 *
     * Requires the name of the passwd file. This functions opens the file and read it.
     * Changes to this file will written first in the lock file, so it is still possible
     * to access the passwd file by another programs. The lock parameter controls the locking
     * oft the lockfile, not of the passwd file! (Swapping $lock and $lockfile would
     * breaks bc to v1.3 and smaller). Don't forget to call close() to save changes!
     * 
     * @param $file		name of the passwd file
     * @param $lock		if 'true' $lockfile will be locked
     * @param $lockfile	name of the temp file, where changes are saved
     *
     * @access public
     * @see close() 
     */
    function FilePasswd( $file, $lock = 0, $lockfile = "" )
	{
        $this->filename = $file;
		
        if ( !empty( $lockfile ) )
            $this->lockfile = $lockfile;

        if( $lock )
		{
            $this->fplock = fopen( $this->lockfile, 'w' );
            flock( $this->fplock, LOCK_EX );
            $this->locked = true;
        }
    
        $fp = fopen( $file, 'r' ) ;
        
		if ( !$fp )
			return false;
		
        while ( !feof( $fp ) )
		{
            $line = fgets( $fp, 128 );
            list( $user, $pass, $cvsuser ) = explode( ':', $line );
            
			if ( strlen( $user ) )
			{
                $this->users[$user] = trim( $pass );
                $this->cvs[$user]   = trim( $cvsuser );	
            }
        }
		
        fclose( $fp );
    }
	

    /**
     * Adds a user.
     *
     * @param $user new user id
     * @param $pass password for new user
     * @param $cvs  cvs user id (needed for pserver passwd files)
     *
     * @return false, if the user already exists
     * @access public
     */
    function addUser( $user, $pass, $cvsuser = "" )
	{
        if ( !isset( $this->users[$user] ) && $this->locked )
		{
            $this->users[$user] = crypt( $pass );
            $this->cvs[$user]   = $cvsuser;
           
		    return true;
        }
		else
		{
			return false;
        }
    }

    /**
     * Modifies a user.
     *
     * @param $user user id
     * @param $pass new password for user
     * @param $cvs  cvs user id (needed for pserver passwd files)
     *
     * @return false, if the user doesn't exists
     * @access public
     */
    function modUser( $user, $pass, $cvsuser = "" )
	{
        if ( isset( $this->users[$user] ) && $this->locked )
		{
            $this->users[$user] = crypt( $pass );
            $this->cvs[$user]   = $cvsuser;
			
            return true;
        }
		else
		{
			return false;
        }
    }

    /**
     * Deletes a user.
     *
     * @param $user user id
     *
     * @return false, if the user doesn't exists
     * @access public	
     */
    function delUser( $user )
	{
        if ( isset( $this->users[$user] ) && $this->locked )
		{
            unset( $this->users[$user] );
            unset( $this->cvs[$user] );
			
			return true;
        }
		else
		{
            return false;
        }
    }

    /**
     * Verifies a user's password.
     *
     * @param $user user id
     * @param $pass password for user
     *
     * @return boolean true if password is ok
     * @access public		
     */
    function verifyPassword( $user, $pass )
	{
        if ( isset( $this->users[$user] ) )
		{
            if ( $this->users[$user] == crypt( $pass, substr( $this->users[$user], 0, 2 ) ) )
				return true;
        }
		
        return false;
    }

    /**
     * Return all users from passwd file.
     *
     * @access public
     * @return array
     */
    function listUsers()
	{
        return $this->users;
    }

    /**
     * Writes changes to passwd file and unlocks it.
     *
     * @access public
     */
    function close()
	{
        if ( $this->locked )
		{
            foreach( $this->users as $user => $pass )
			{
                if ( $this->cvs[$user] )
                    fputs( $this->fplock, "$user:$pass:" . $this->cvs[$user] . "\n" );
                else
                    fputs( $this->fplock, "$user:$pass\n" );
            }
			
            rename( $this->lockfile, $this->filename );
            flock( $this->fplock, LOCK_UN );
            $this->locked = false;
            fclose( $this->fplock );
        }
    }
} // END OF FilePasswd

?>
