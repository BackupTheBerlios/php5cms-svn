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


using( 'util.Util' );
using( 'peer.IPUtil' );

 
// SQL Tables
if ( !defined( "UM_USERS_TABLE" ) ) 
	define( "UM_USERS_TABLE", "usermanagement_users" );

if ( !defined( "UM_SESSION_TABLE" ) ) 
	define( "UM_SESSION_TABLE", "usermanagement_sessions" );

if ( !defined( "UM_GROUPS_TABLE" ) ) 
	define( "UM_GROUPS_TABLE", "usermanagement_groups" );


// Error Constans
define( "UM_HACKER_ATTEMPT",            1000  );
define( "UM_INPUT_ERROR",               1001  );
define( "UM_SQL_ERROR",                 1002  );
define( "UM_LOGIN_FAILED",              1003  );
define( "UM_USER_NOT_ACTIVATED",        1004  );
define( "UM_USER_EXISTS",               1005  );
define( "UM_CREATE_USER_FAILED",        1006  );
define( "UM_SESSION_START_FAILED",      1007  );
define( "UM_USER_LOCKED",               1008  );
define( "UM_INSERT_SESSION_FAILED",     1009  );
define( "UM_UPDATE_SESSION_FAILED",     1010  );
define( "UM_MAX_FAILED_LOGINS_REACHED", 1011  );
define( "UM_GROUP_NOT_ACTIVATED",       1012  );
define( "UM_GROUP_NOT_EXISTS",          1013  );
define( "UM_NOT_LOGGED",                1014  );
define( "UM_LOGIN_TIMEOUT_REACHED",     1015  );
define( "UM_UNLOCK_USER_FAILED",        1016  );
define( "UM_ACTIVATED_USER_FAILED",     1017  );
define( "UM_USER_NOT_EXISTS",           1018  );
define( "UM_CHANGE_PASSWORD_FAILED",    1019  );
define( "UM_CHANGE_EMAIL_FAILED",       1020  );
define( "UM_ACCESS_DENIED",             1021  );
define( "UM_GROUP_EXISTS",              1022  );
define( "UM_USER_DELETE_FAILED",        1023  );
define( "UM_GROUP_DELETE_FAILED",       1024  );
define( "UM_GROUP_ACTIVATE_FAILED",     1025  );
define( "UM_GROUP_DEACTIVATE_FAILED",   1026  );
define( "UM_EDIT_USER_FAILED",          1027  );
define( "UM_GROUP_EDIT_FAILED",         1028  );
define( "UM_ACTION_FAILED",             1029  );


// Input Constans
define( "UM_MAX_USERNAME_LEN",          20    );
define( "UM_MAX_PASSWORD_LEN",          35    );
define( "UM_FIRST_NAME_MAX_LEN",        50    );
define( "UM_LAST_NAME_MAX_LEN",         75    );
define( "UM_STREET_MAX_LEN",            255   );
define( "UM_HOMETOWN_MAX_LEN",          100   );
define( "UM_POSTCODE_MAX_LEN",          10    );
define( "UM_EMAIL_MAX_LEN",             200   );
define( "UM_TELEPHONE_MAX_LEN",         50    );
define( "UM_FAX_MAX_LEN",               50    );
define( "UM_MOBIL_MAX_LEN",             25    );
define( "UM_SIGNATURE_MAX_LEN",         65535 );
define( "UM_ICQ_MAX_LEN",               20    );
define( "UM_MSN_MAX_LEN",               255   );
define( "UM_AIM_MAX_LEN",               255   );
define( "UM_MAX_FAILED_LOGINS",         5     ); // max 98!
define( "UM_TIMEOUT_MAX_LEN",           3     );

define( "UM_GROUP_NAME_MAX_LEN",        100   );
define( "UM_GROUP_DESC_MAX_LEN",        255   );
define( "UM_GROUP_LEVEL_MAX_LEN",       3     );


/**
 * @package user
 */
 
class UserManagement extends PEAR
{
    /**
	 * @access public
	 */
    var $err_code;
	
	/**
	 * @access public
	 */
    var $err_title;
	
	/**
	 * @access public
	 */
    var $err_msg;
	
	/**
	 * @access public
	 */
    var $err_line;
	
	/**
	 * @access public
	 */
    var $err_file;

	/**
	 * @access public
	 */
    var $query;
    
	/**
	 * @access public
	 */
    var $verbose;
	
	/**
	 * @access public
	 */
    var $result;
	
	/**
	 * @access public
	 */
    var $db;
    
	/**
	 * @access public
	 */
	var $unlock = array();
    
	/**
	 * @access private
	 */
	var $_sql_layer;


    /**
	 * Constructor
	 *
	 * @access public
	 */
    function UserManagement( $db, $sql_layer = "mysql", $verbose = false )
    {
        $this->verbose    = $verbose;
        $this->db         = $db;
		
        $this->_sql_layer = $sql_layer;
    }


	/**
	 * @access public
	 */    
    function login( $username, $password )
    {
		$check = $this->_validData( 
			array( 
				"username" => $username, 
				"password" => $password 
			) 
		);
		
       	if ( PEAR::isError( $check ) )
			return $check;
        
        $this->query = 'SELECT user_id, group_id, activated, locked, failed_logins, created, password, first_name, last_name, email
                        FROM '. UM_USERS_TABLE .'
                        WHERE
                        LOWER(username) = \''. strtolower($username)  .'\'';

        $this->result = $this->db->query( $this->query );
        
        if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        $this->query = '';

        if ( !$row = $this->result->fetchRow( DB_FETCHMODE_ASSOC ) )
            return $this->raiseError( UM_LOGIN_FAILED, "Login", "Login failed." );
        
        $this->result->free();

        if ( UM_MAX_FAILED_LOGINS != 0 )
        {
            if ( $row["failed_logins"] == UM_MAX_FAILED_LOGINS )
            {
				$res = $this->lockUser( $username );
				
                if ( PEAR::isError( $res ) )
                    return $res;
                
                $this->unlock["email"] = $row["email"];
                return $this->raiseError( UM_MAX_FAILED_LOGINS_REACHED, "Login", "Your account is locked, because too many failed logins." );
            }
        }

		// check is User locked
        if ( $row["locked"] != 0)
            return $this->raiseError( UM_USER_LOCKED, "Login", "Your account is locked." );

  		// check is Password valid, else set failed_login + 1 and return UM_LOGIN_FAILED
        if ( $row["password"] != sha1( $password ) )
        {
            $this->query = 'UPDATE '. UM_USERS_TABLE .'
                            SET
                            failed_logins = \''. ($row["failed_logins"] + 1) .'\'
                            WHERE
                            LOWER(username) = \''. strtolower($username) .'\'
                            LIMIT 1';

            $this->result = $this->db->query( $this->query );

            if ( PEAR::isError( $this->result ) )
                return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
            
            $this->query = '';
            return $this->raiseError( UM_LOGIN_FAILED, "Login", "Login failed." );
        }

		// check is User activated
        if ( $row["activated"] != 1)
            return $this->raiseError( UM_USER_NOT_ACTIVATED, "Login", "Your account is not activated." );

		// set output user vars
        $output = array( 
			"username"   => $username,
			"password"   => $password,
			"user_id"    => $row["user_id"],
			"group_id"   => $row["group_id"],
			"first_name" => $row["first_name"],
			"last_name"  => $row["last_name"],
			"email"      => $row["email"],
			"created"    => $row["created"]
		);

        if ( $row["failed_logins"] > UM_MAX_FAILED_LOGINS )
            $output["failed_logins"] = $row["failed_logins"] - 1;
        else
            $output["failed_logins"] = $row["failed_logins"];
                         
        unset( $row );
        
    	// Check Group Setting
        $this->query = 'SELECT group_name, group_desc, activated
                        FROM '. UM_GROUPS_TABLE .'
                        WHERE group_id = \''. $output["group_id"] .'\'';
        
		$this->result = $this->db->query( $this->query );
        
        if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        $this->query = '';
        
		if ( !$row = $this->result->fetchRow( DB_FETCHMODE_ASSOC ) )
            return $this->raiseError( UM_GROUP_NOT_EXISTS, "Login", "Given group_id does not exists." );
        
        $this->result->free();
		
        // Check is Group activated.
        if ( $row["activated"] != 1 )
            return $this->raiseError( UM_GROUP_NOT_ACTIVATED, "Login", "Your group is not activated." );

        // Set output group vars.
        $output["group_name"]  = $row["group_name"];
        $output["group_desc"]  = $row["group_desc"];
        $output["last_action"] = $this->_timestamp();
        
        unset( $row );
        
        // Update Session Data Section.
        $this->query = 'UPDATE '. UM_SESSION_TABLE .' SET
                        user_id = \''. $output["user_id"] .'\',
                        last_action = \''. $output["last_action"] .'\'
                        WHERE
                        session_id = \''. session_id() .'\'
                        LIMIT 1';
                        
        $this->result = $this->db->query( $this->query );

        if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        $this->query = '';
        
		if ( $this->db->affectedRows( $this->result ) != 1 )
            return $this->raiseError( UM_UPDATE_SESSION_FAILED, "DB_Handling", "Something goes wrong, while updating session data.", __LINE__, __FILE__ );
        
        // Updating USERS Table.
        $this->query = 'UPDATE '. UM_USERS_TABLE .' SET
                        session_id = \''. session_id() .'\',
                        failed_logins = \'0\'
                        WHERE
                        user_id = \''. $output["user_id"] .'\'
                        LIMIT 1';
        
		$this->result = $this->db->query( $this->query );

        if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        $this->query = '';
        
		if ( $this->db->affectedRows( $this->result ) != 1 )
            return $this->raiseError( UM_UPDATE_SESSION_FAILED, "DB_Handling", "Something goes wrong, while updating user data.", __LINE__, __FILE__ );
        
        return $output;
    }
    
    /**
     * - check is given session_id logged for a user
     * - if timeout set the function will return UM_LOGIN_TIMEOUT_REACHED, when reached
     * - if timeout reached function will logout user/session and destroy session.
	 *
	 * @access public
     */
    function isLogin( $session_id = null )
    {
        if ( is_null( $session_id ) )
            $session_id = session_id();
        
        $this->query = 'SELECT s.last_action, u.timeout
                        FROM '. UM_USERS_TABLE .' u, '. UM_SESSION_TABLE .' s
                        WHERE
                        u.session_id = \''. $session_id .'\'
                        AND
                        s.session_id = \''. $session_id .'\'
                        AND
                        s.user_id = u.user_id';
        
		$this->result = $this->db->query( $this->query );
        
        if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        $this->query = '';
        
        if ( !$row = $this->result->fetchRow( DB_FETCHMODE_ASSOC ) )
            return $this->raiseError( UM_NOT_LOGGED, "Login", "You are not logged." );
        
        // Timeout min -> sec
        if ( $row["timeout"] > 0 )
        {
            $timeout_sec  = $row["timeout"] * 60;
            $timeout_diff = $this->_timestamp() - $row["last_action"];
            
			if ( $timeout_diff > $timeout_sec )
            {
				$res = $this->logout( $session_id );
				
                if ( PEAR::isError( $res ) )
                    return $res;
                
                $this->destroySession( $session_id );
                return $this->raiseError( UM_LOGIN_TIMEOUT_REACHED, "Login", "Login timeout reached." );
            }
        }
		
        $this->result->free();
        return true;
    }

    /**
     * - set "session_stop", "last_action" and "user_id" = ''
	 *
	 * @access public
     */
    function logout( $session_id = null )
    {
        if ( is_null( $session_id ) )
            $session_id = session_id();

        $this->query = 'UPDATE '. UM_SESSION_TABLE .' SET
                       session_stop = \''. $this->_datetime() .'\',
                       last_action = \''. $this->_timestamp() .'\',
                       user_id = \'\'
                       WHERE
                       session_id = \''. $session_id .'\'
                       LIMIT 1';
                       
        $this->result = $this->db->query( $this->query );
        
		if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        $this->query = '';
        
		if ( $this->db->affectedRows( $this->result ) != 1 )
            return $this->raiseError( UM_UPDATE_SESSION_FAILED, "DB_Handling", "Something goes wrong, could not updating session data.", __LINE__, __FILE__ );
        
        return true;
    }

    /**
     * Note: User must be logged, befor you can use this function!
	 *
	 * @access public
     */
    function isAuthorized( $need_level )
    {
        $this->query = 'SELECT g.level FROM '. UM_USERS_TABLE .' u, '. UM_GROUPS_TABLE .' g
                        WHERE
                        u.session_id = \''. @session_id() .'\'
                        AND
                        g.group_id = u.group_id';
                        
        $this->result = $this->db->query( $this->query );
        
        if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        $this->query = '';
        
        if ( !$row = $this->result->fetchRow( DB_FETCHMODE_ASSOC ) )
            return $this->raiseError( UM_ACCESS_DENIED, "Access Denied", "You cannot access to this site." );
        
        $this->result->free();
        
		if ( $row["level"] > $need_level )
            return $this->raiseError( UM_ACCESS_DENIED, "Access Denied", "You cannot access to this site." );
        
        return true;
    }
    
	/**
	 * @access public
	 */
    function action( $session_id = null )
    {
        if ( is_null( $session_id ) )
        {
            $session_id = @session_id();
        }
        else
        {
			$check = $this->_validData( array("session_id" => $session_id ) );
			
           	if ( PEAR::isError( $check ) )
				return $check;
        }
		
        $this->query = 'UPDATE '. UM_SESSION_TABLE .' SET
                        last_action = \''. $this->_timestamp() .'\'
                        WHERE
                        session_id = \''. $session_id .'\'';
        
		$this->result = $this->db->query( $this->query );
        
		if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        $this->query = '';
        
		if ( $this->db->affectedRows( $this->result ) != 1 )
            return $this->raiseError( UM_ACTION_FAILED, "Action", "Something goes wrong, while do action.", __LINE__, __FILE__ );
        
        return true;
    }
	
    /**
     * - check if all input data are valid.
     * - check is User already exists.
     * - generate a activation id that will return if all right
     * - create user
	 *
	 * @access public
     */
    function createUser( $user )
    {
        // Check are all input data valid.
		$check = $this->_validData( $user );
		
		if ( PEAR::isError( $check ) )
			return $check;
        
		// Check is Username already exists.
		$res = $this->_userNotExists( $user["username"] );
		
		if ( PEAR::isError( $res ) )
			return $res;
        
        // Generate Activation ID.
        $user["session_id"] = $this->_generateID( $user["username"] );

        $this->query = 'INSERT INTO ' . UM_USERS_TABLE . ' VALUES (
                        \'\',
                        \''. $user["group_id"] .'\',
                        \''. $user["session_id"] .'\',
                        \''. $user["timeout"] .'\'
                        \'\',
                        \'\',
                        \''. $user["activated"] .'\',
                        \''. $this->_datetime() .'\',
                        \''. $user["username"] .'\',
                        \''. sha1($user["password"]) .'\',
                        \''. $user["first_name"] .'\',
                        \''. $user["last_name"] .'\',
                        \''. $user["street"] .'\',
                        \''. $user["postcode"] .'\',
                        \''. $user["hometown"] .'\',
                        \''. $user["email"] .'\',
                        \''. $user["website"] .'\',
                        \''. $user["telephone"] .'\',
                        \''. $user["fax"] .'\',
                        \''. $user["mobil"] .'\',
                        \''. $user["signature"] .'\',
                        \''. $user["icq"] .'\',
                        \''. $user["msn"] .'\',
                        \''. $user["aim"] .'\'
                        )';

        $this->result = $this->db->query( $this->query );
        
		if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        $this->query = '';
        
		if ( $this->db->affectedRows( $this->result ) != 1 )
            return $this->raiseError( UM_CREATE_USER_FAILED, "DB_Handling", "Something goes wrong, while create user.", __LINE__, __FILE__ );

        return $user["session_id"];
    }
    
    /**
     * - check input data
     * - activate user
	 *
	 * @access public
     */
    function activatedUser( $username, $activ_id )
    {
		$check = $this->_validData( array("username" => $username, "unlock_id" => $activ_id ) );

        if ( PEAR::isError( $check ) )
			return $check;

        $this->query = 'UPDATE ' . UM_USERS_TABLE . ' SET
                        activated = \'1\'
                        WHERE
                        username = \''. $username .'\'
                        AND
                        session_id = \''. $activ_id .'\'
                        LIMIT 1';

        $this->result = $this->db->query( $this->query );
		
        if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        if ( $this->db->affectedRows( $this->result ) != 1 )
            return $this->raiseError( UM_ACTIVATED_USER_FAILED, "DB Handling", "Something goes wrong while activate user.", __LINE__, __FILE__ );
        
        return true;
    }
    
    /**
     * set the unlock array
     * set in table users
	 *
	 * @access public
     */
    function lockUser( $username )
    {
		$check = $this->_validData( array("username" => $username ) );
		
		if ( PEAR::isError( $check ) )
			return $check;
		
        // Created unlock_id and set it in session_id field.
        $this->unlock["id"]       = $this->_generateID( $username );
        $this->unlock["username"] = $username;
        $this->unlock["password"] = substr( $this->unlock["id"], 9, 18 );

        $this->query = 'UPDATE '. UM_USERS_TABLE .' SET
                        locked = \'1\',
                        failed_logins = \''. (UM_MAX_FAILED_LOGINS + 1) .'\',
                        session_id = \''. $this->unlock["id"] .'\',
                        password = \''. sha1( $this->unlock["password"] ) .'\'
                        WHERE
                        LOWER(username) = \''. strtolower($username) .'\'
                        LIMIT 1';

        $this->result = $this->db->query( $this->query );

        if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        $this->query = '';
        return true;
    }
	
    /**
     * - set locked in DB "0" false.
	 *
	 * @access public
     */
    function unlockUser( $username, $unlock_id )
    {
		$check = $this->_validData( array( "username" => $username, "unlock_id" => $unlock_id ) );
		
		if ( PEAR::isError( $check ) )
			return $check;

        $this->query = 'UPDATE ' . UM_USERS_TABLE . ' SET
                        locked= \'0\'
                        WHERE
                        session_id = \''. $unlock_id .'\'
                        AND
                        LOWER(username) = \''. strtolower($username) .'\'
                        LIMIT 1';

        $this->result = $this->db->query( $this->query );
		
        if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        $this->query = '';
        
		if ( $this->db->affectedRows( $this->result ) )
            return $this->raiseError( UM_UNLOCK_USER_FAILED, "Unlock_User", "Something goes wrong while unlock user.", __LINE__, __FILE__ );
        
        return true;
    }
    
    /**
     * - check are all given string valid
     * - check is
     * - change password when old pwd is correct
	 *
	 * @access public
     */
    function changePassword( $username, $old_pwd, $new_pwd, $re_new_pwd )
    {
		$check = $this->_validData( array( "username" => $username, "password" => $old_pwd, "password" => $new_pwd, "password" => $re_new_pwd ) );
		
		if ( PEAR::isError( $check ) )
			return $check;

        if ( $new_pwd != $re_new_pwd )
            return $this->raiseError( UM_CHANGE_PASSWORD_FAILED, "Change Password", "Your new 2 password strings are not the same." );
        
        $this->query = 'UPDATE ' . UM_USERS_TABLE . ' SET
                        password = \''. sha1( $new_pwd ) .'\'
                        WHERE
                        LOWER(username) = \''. strtolower($username) .'\'
                        AND
                        password = \''. sha1( $old_pwd ) .'\'
                        LIMIT 1';

        $this->result = $this->db->query( $this->query );
        
		if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        $this->query = '';
        
		if ( $this->db->affectedRows( $this->result ) != 1 )
            return $this->raiseError( UM_CHANGE_PASSWORD_FAILED, "Change Password", "Your old password was not correct." );
        
        return true;
    }

    /**
     * - check is new email format valid
     * - generate new activation_id and set it in db
     * - change email, if not exists because email field is "unique_id"
     *   and set activated = false
     * - return activ_id
	 *
	 * @access public
     */
    function changeEmail( $username, $new_email )
    {
		$check = $this->_validData( array( "username" => $username, "email" => $new_email ) );
		
		if ( PEAR::isError( $check ) )
			return $check;
			
        $activ_id = $this->_generateID( $username );
        
        $this->query = 'UPDATE ' . UM_USERS_TABLE . ' SET
                        email = \''. $new_email .'\',
                        activated = \'0\'
                        WHERE
                        LOWER(username) = \''. strtolower($username) .'\'
                        LIMIT 1';

        $this->result = $this->db->query( $this->query );
        
        if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        $this->query = '';
        
		if ( $this->db->affectedRows( $this->result ) != 1 )
            return $this->raiseError( UM_CHANGE_EMAIL_FAILED, "DB Handling", "Something goes wrong while changing your email.", __LINE__, __FILE__ );
        
        return $activ_id;
    }

    /**
     * - check all data.
     * - update
	 *
	 * @access public
     */
    function editUser( $user )
    {
		$$check = $this->_validData( $user );
		
		if ( PEAR::isError( $check ) )
			return $check;

        $this->query = 'UPDATE ' . UM_USERS_TABLE . ' SET
                        timeout = \''. $user["timeout"] .'\',
                        first_name = \''. $user["first_name"] .'\',
                        last_name = \''. $user["last_name"] .'\',
                        street = \''. $user["street"] .'\',
                        postcode = \''. $user["postcode"] .'\',
                        hometown = \''. $user["hometown"] .'\',
                        website = \''. $user["website"] .'\',
                        telephone = \''. $user["telephone"] .'\',
                        fax = \''. $user["fax"] .'\',
                        mobil = \''. $user["mobil"] .'\',
                        signature = \''. $user["signature"] .'\',
                        icq = \''. $user["icq"] .'\',
                        msn = \''. $user["msn"] .'\',
                        aim = \''. $user["aim"] .'\'
                        WHERE
                        LOWER(username) = \''. strtolower($user["username"]) .'\'
                        LIMIT 1';

        $this->result = $this->db->query( $this->query );
		
        if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        $this->query = '';
        
		if ( $this->db->affectedRows( $this->result ) != 1 )
            return $this->raiseError( UM_EDIT_USER_FAILED, "DB Handling", "Something goes wrong while edit user data." );
        
        return true;
    }
    
    /**
     * - check is uder_id valid
     * - return all found able user data
	 *
	 * @access public
     */
    function returnUser( $user_id )
    {
		$check = $this->_validData( array( "icq" => $user_id ) );
		
		if ( PEAR::isError( $check ) )
			return $check;

        $this->query  = 'SELECT * FROM ' . UM_USERS_TABLE . ' WHERE user_id = \''. $user_id .'\' LIMIT 1';
        $this->result = $this->db->query( $this->query );
        
		if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        $this->query = '';
        
        if ( !$row = $this->result->fetchRow( DB_FETCHMODE_ASSOC ) )
            return $this->raiseError( UM_USER_NOT_EXISTS, "User", "User does not exist." );
        
        $this->result->free();
        return $row;
    }

	/**
	 * @access public
	 */    
    function deleteUser( $username )
    {
		$check = $this->_validData( array("username" => $username) );
		
		if ( PEAR::isError( $check ) )
			return $check;
        
        $this->query  = 'DELETE FROM ' . UM_USERS_TABLE . ' WHERE LOWER(username) = \''. strtolower($username) .'\' LIMIT 1';
        $this->result = $this->db->query( $this->query );
        
        if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        $this->query = '';
        
		if ( $this->db->affectedRows( $this->result ) )
            return $this->raiseError( UM_USER_DELETE_FAILED, "DB Handling", "Something goes wrong while delete user.", __LINE__, __FILE__ );
    }
    
    /**
     * - check are given datas correct
     * - check is group already exists
	 *
	 * @access public
     */
    function createGroup( $group )
    {
		$check = $this->_validData( $group );
		
		if ( PEAR::isError( $check ) )
			return $check;

        // Check is group already exists.
		$res = $this->_groupNotExists( $group["group_name"] );
		
        if ( PEAR::isError( $res ) )
            return $res;
        
        $this->query = 'INSERT INTO ' . UM_GROUPS_TABLE . ' VALUES (
                        \'\',
                        \''. $group["group_name"] .'\',
                        \''. $group_desc["group_desc"] .'\',
                        \''. $group["activated"] .'\',
                        \''. $group["level"] .'\' )';
        
        $this->result = $this->db->query( $this->query );
        
		if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        $this->query = '';
        
		if ( $this->db->affectedRows( $this->result ) != 1 )
            return $this->raiseError( UM_CREATE_GROUP_FAILED, "DB Handling", "Something goes wrong while created group.", __LINE__, __FILE__ );
        
        return true;
    }
    
	/**
	 * @access public
	 */
    function activateGroup( $group_name )
    {
		$check = $this->_validData( array( "group_name" => $group_name ) );
		
		if ( PEAR::isError( $check ) )
			return $check;

        $this->query = 'UPDATE ' . UM_GROUPS_TABLE . ' SET
                        activated = \'1\',
                        WHERE
                        LOWER(group_name) = \''. strtolower($group_name) .'\'';

        $this->result = $this->db->query( $this->query );
        
		if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        $this->query = '';
        
		if ( $this->db->affectedRows( $this->result ) != 1 )
            return $this->raiseError( UM_GROUP_ACTIVATE_FAILED, "Activate Group", "Something goes wrong while activate group.", __LINE__, __FILE__ );

        return true;
    }

	/**
	 * @access public
	 */
    function deactivateGroup( $group_name )
    {
		$check = $this->_validData( array( "group_name" => $group_name ) );
		
		if ( PEAR::isError( $check ) )
			return $check;

        $this->query = 'UPDATE ' . UM_GROUPS_TABLE . ' SET
                        activated = \'0\',
                        WHERE
                        LOWER(group_name) = \''. strtolower($group_name) .'\'
                        LIMIT 1';

        $this->result = $this->db->query( $this->query );
        
		if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        $this->query = '';
        
		if ( $this->db->affectedRows( $this->result ) != 1 )
            return $this->raiseError( UM_GROUP_DEACTIVATE_FAILED, "Deactivate Group", "Something goes wrong while deactivate group.", __LINE__, __FILE__ );

        return true;
    }
    
    /**
     * - if you not set level it will set to default "999"
	 *
	 * @access public
     */
    function editGroup( $group )
    {
		$check = $this->_validData( $group );
		
		if ( PEAR::isError( $check ) )
			return $check;

        if ( !isset( $group["level"] ) || $group["level"] == '' )
            $group["level"] = 999;
        
        $this->query = 'UPDATE ' . UM_GROUPS_TABLE . ' SET
                        group_name = \''. $group["group_name"] .'\',
                        group_desc = \''. $group["group_desc"] .'\',
                        activated = \''. $group["activated"] .'\',
                        level = \''. $group["level"] .'\'
                        WHERE
                        LOWER(group_name) = \''. strtolower($group["group_name"]) .'\' LIMIT 1';

        $this->result = $this->db->query( $this->query );
        
		if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        $this->query = '';
        
		if ( $this->db->affectedRows( $this->result ) != 1 )
            return $this->raiseError( UM_GROUP_EDIT_FAILED, "Edit Group", "Something goes wrong while edit group.", __LINE__, __FILE__ );
        
        return true;
    }

	/**
	 * @access public
	 */
    function deleteGroup( $group_name )
    {
		$check = $this->_validData( array( "group_name" => $group_name ) );
		
		if ( PEAR::isError( $check ) )
			return $check;

        $this->query  = 'DELETE FROM ' . UM_GROUPS_TABLE . ' WHERE LOWER(group_name) = \''. strtolower($group_name) .'\' LIMIT 1';
        $this->result = $this->db->query( $this->query );

        if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        $this->query = '';
        
		if ( $this->db->affectedRows( $this->result ) != 1 )
            return $this->raiseError( UM_GROUP_DELETE_FAILED, "DB Handling", "Something goes wrong while delete group.", __LINE__, __FILE__ );
        
        return true;
    }
	
	/**
     * Will load the session is given by sess_id, 
	 * else it will start a new one and put this data to sessions table.
	 *
	 * @access public
	 */
    function loadSession( $sess_id = null, $sess_name = "UM_SID", $sess_cache_limiter = "private", $sess_cache_expire = "0", $cookie_params = array() )
    {
        if ( !is_null( $sess_id ) )
            session_id( $sess_id );

        // Set session name.
        session_name( $sess_name );

        // "private", "public", "nocache", "private_no_expire"
        // http://de2.php.net/manual/en/function.session-cache-limiter.php
        session_cache_limiter( $sess_cache_limiter );

        // Browser Cache Time.
        session_cache_expire( $sess_cache_expire );

        // Cookie Parameters
        // http://de2.php.net/manual/en/function.session-set-cookie-params.php
        if ( !isset( $cookie_params["ttl"] ) )
            $ttl = 0;
        
        if ( !isset( $cookie_params["path"] ) )
            $path = "/";
        
        if ( !isset( $cookie_params["domain"] ) )
            $domain = "";
        
        if ( !isset( $cookie_params["secure"] ) )
            $secure = false;
        
        session_set_cookie_params( $ttl, $path, $domain, $secure );

        // start session
        if ( !session_start() )
            return $this->raiseError( UM_SESSION_START_FAILED, "Session", "Cannot start session." );
        
        // updating session table
        if ( $sess_id != session_id() )
        {
            $this->query = 'INSERT INTO ' . UM_SESSION_TABLE . ' VALUES (
                            \'\',
                            \''. session_id() .'\',
                            \''. $this->_datetime() .'\',
                            \'\',
                            \'\',
                            \''. $this->_timestamp() .'\',
                            \''. IPUtil::client_ip   .'\',
                            \''. $this->_getBrowser()  .'\',
                            \''. $this->_getReferer()  .'\'
                            )';

            $this->result = $this->db->query( $this->query );
            
            if ( PEAR::isError( $this->result ) )
                return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
            
            $this->query = '';
            
			if ( $this->db->affectedRows( $this->result ) != 1 )
                return $this->raiseError( UM_INSERT_SESSION_FAILED, "DB_Handling", "Something goes wrong while insert session data", __LINE__, __FILE__ );
        }

        return true;
    }
    
	/**
	 * @access public
	 */
    function destroySession()
    {
        $session_id = session_id();
        
		// unset all session vars
        session_unset();
        
		// destroy session
        session_destroy();

        // Get session file and delete it.
        if ( strtolower( 'files' == session_module_name() ) )
        {
            $os = substr( PHP_OS, 0, 3 );
			
            if ( $os == 'WIN' )
            {
                $tz   = "//";
                $path = str_replace( chr( 92 ), $tz, session_save_path() );
            }
            else
            {
                $tz   = "/";
                $path = session_save_path();
            }

            @unlink( $path . $tz .'sess_'. $session_id );
        }
		
        return true;
    }
    
	/**
	 * @access public
	 */
    function raiseError( $msg_code, $msg_title = "", $msg_text = "", $line = "", $file = "" )
    {
        if ( $msg_code == UM_SQL_ERROR )
        {
          	$this->err_code = $msg_code;
            
			if ( !$this->verbose )
            {
                $this->err_title = "SQL_Error";
                $this->err_msg   = $this->result->getMessage();
            }
            else
            {
                if ( $this->_sql_layer == 'mysql' )
                {
                    $this->err_title = @mysql_errno();
                    $this->err_msg   = @mysql_error();
                }
                else if ( $this->_sql_layer == 'mssql' )
                {
                    $this->err_title = "MSSQL_Error";
                    $this->err_msg   = @mssql_get_last_message();
                }
                else if ( $this->_sql_layer == 'odbc' )
                {
                    $this->err_title = @odbc_error();
                    $this->err_msg   = @odbc_errormsg();
                }
                else
                {
                    $this->err_title = "SQL Error";
                    $this->err_msg   = $this->result->getMessage();
                }

                $this->err_line = $line;
                $this->err_file = $file;
            }
        }
        
        if ( $msg_code == UM_INSERT_SESSION_FAILED || $msg_code == UM_CREATE_USER_FAILED || $msg_code == UM_UPDATE_SESSION_FAILED || $msg_code == UM_UNLOCK_USER_FAILED || $msg_code == UM_ACTIVATED_USER_FAILED || $msg_code == UM_CHANGE_EMAIL_FAILED || $msg_code == UM_GROUP_DELETE_FAILED || $msg_code == UM_GROUP_ACTIVATE_FAILED || $msg_code == UM_GROUP_DEACTIVATE_FAILED || $msg_code == UM_GROUP_EDIT_FAILED || $msg_code == UM_ACTION_FAILED )
        {
            $this->err_code  = $msg_code;
            $this->err_title = $msg_title;
            $this->err_msg   = $msg_text;
            $this->err_line  = $line;
            $this->err_file  = $file;
        }
        
        if ( $msg_code == UM_INPUT_ERROR )
        {
            $this->err_code = $msg_code;
            
			if ( $msg_title == "" )
                $this->err_title = "Input Error";
            else
                $this->err_title = $msg_title;
            
            $this->err_msg = $msg_text;
        }
        
        if ( $msg_code == UM_LOGIN_FAILED || $msg_code == UM_USER_NOT_ACTIVATED || $msg_code == UM_USER_EXISTS || $msg_code == UM_SESSION_START_FAILED || $msg_code == UM_USER_LOCKED || $msg_code == UM_GROUP_NOT_EXISTS || $msg_code == UM_GROUP_NOT_ACTIVATED || $msg_code == UM_NOT_LOGGED || $msg_code == UM_LOGIN_TIMEOUT_REACHED || $msg_code == UM_USER_NOT_EXISTS || $msg_code == UM_CHANGE_PASSWORD_FAILED || $msg_code == UM_ACCESS_DENIED || $msg_code == UM_GROUP_EXISTS || $msg_code == UM_EDIT_USER_FAILED )
        {
            $this->err_code  = $msg_code;
            $this->err_title = $msg_title;
            $this->err_msg   = $msg_text;
        }
        
        if ( $msg_code == UM_HACKER_ATTEMPT )
        {
            $this->err_code  = $msg_code;
            $this->err_title = "Hacker attempt";
            $this->err_msg   = "You try to hack this site, an email with your data was send to admin.";
        }

		$message = $this->err_title . " - " . $this->err_msg;
        return PEAR::raiseError( $message, $this->err_code );
    }
    
	
	// private methods
	
	/**
	 * @access private
	 */
    function _validData( $user )
    {
        while ( list( $key, $value ) = each( $user ))
        {
            // Check is Username String valid.
            if ( preg_match( "/username/i", $key ) )
            {
                if ( strlen( $value ) > UM_MAX_USERNAME_LEN )
                    return $this->raiseError( UM_HACKER_ATTEMPT );
                
                if ( !preg_match( "/^[A-Za-z0-9_-]+$/", $value ) )
                    return $this->raiseError( UM_INPUT_ERROR, "Username", "Invalid chars in your username." );
            }
			
            // Check is Password String valid.
            if ( preg_match( "/password/i", $key ) )
            {
                if ( strlen( $value ) > UM_MAX_PASSWORD_LEN )
                    return $this->raiseError( UM_HACKER_ATTEMPT );
            }
			
            // Check is first_name String valid.
            if ( preg_match( "/first_name/i", $key) )
            {
                if ( strlen( $value ) > UM_FIRST_NAME_MAX_LEN )
                    return $this->raiseError( UM_HACKER_ATTEMPT );
                
                if ( !preg_match( "/^[A-Za-z0-9]+$/", $value ) )
                    return $this->raiseError( UM_INPUT_ERROR, "First_Name", "Invalid chars in your first_name." );
            }
			
            // Check is last_name String valid.
            if ( preg_match( "/last_name/i", $key ) )
            {
                if ( strlen( $value ) > UM_LAST_NAME_MAX_LEN )
                    return $this->raiseError( UM_HACKER_ATTEMPT );
                
                if ( !preg_match( "/^[A-Za-z0-9]+$/", $value ) )
                    return $this->raiseError( UM_INPUT_ERROR, "Last_Name", "Invalid chars in your last_name." );
            }
			
            // Check is street String valid.
            if( preg_match("/street/i", $key) )
            {
                if ( strlen( $value ) > UM_STREET_MAX_LEN )
                    return $this->raiseError( UM_HACKER_ATTEMPT );
                
                if ( !preg_match( "/^[A-Za-z0-9.-]+$/", $value ) )
                    return $this->raiseError( UM_INPUT_ERROR, "Street", "Invalid chars in your street string." );
            }
			
            // Check is hometown String valid.
            if ( preg_match( "/hometown/i", $key ) )
            {
                if ( strlen( $value ) > UM_HOMETOWN_MAX_LEN )
                    return $this->raiseError( UM_HACKER_ATTEMPT );
                
                if ( !preg_match( "/^[A-Za-z0-9-_]+$/", $value ) )
                    return $this->raiseError( UM_INPUT_ERROR, "HomeTown", "Invalid chars in your hometown." );
            }
			
            // Check is postcode String valid.
            if ( preg_match( "/postcode/i", $key ) )
            {
                if ( strlen( $value ) > UM_POSTCODE_MAX_LEN )
                    return $this->raiseError( UM_HACKER_ATTEMPT );
  
                if ( !preg_match("/^[0-9]+$/", $value ) )
                    return $this->raiseError( UM_INPUT_ERROR, "Postcode", "Invalid chars in your postcode." );
            }

            // Check is E-Mail String valid.
            if ( preg_match( "/email/i", $key) )
            {
                if ( strlen( $value ) > UM_EMAIL_MAX_LEN )
                    return $this->raiseError( UM_HACKER_ATTEMPT );

                if ( !preg_match("/[a-z0-9_-]+(\.[a-z0-9_-]+)*@([0-9a-z][0-9a-z-]*[0-9a-z]\.)+([a-z]{2,4}|museum)/i", $value ) )
                    return $this->raiseError( UM_INPUT_ERROR, "E-Mail", "Invalid email address." );
            }
            
            // Check is telephone valid.
            if ( preg_match( "/telephone/i", $key) )
            {
                if ( strlen( $value ) > UM_TELEPHONE_MAX_LEN )
                    return $this->raiseError( UM_HACKER_ATTEMPT );
            }
            
            // Check is FAX valid.
            if ( preg_match( "/fax/i", $key) )
            {
                if ( strlen( $value ) > UM_FAX_MAX_LEN )
                    return $this->raiseError( UM_HACKER_ATTEMPT );
            }
            
            // Check is Mobil valid.
            if ( preg_match( "/mobil/i", $key ) )
            {
                if ( strlen( $value ) > UM_MOBIL_MAX_LEN )
                    return $this->raiseError( UM_HACKER_ATTEMPT );
            }
            
            // Check is ICQ Number valid.
            if ( preg_match( "/icq/i", $key ) )
            {
                if ( strlen( $value ) > UM_ICQ_MAX_LEN )
                    return $this->raiseError( UM_HACKER_ATTEMPT );
                
                if ( !preg_match( "/^[0-9]+$/", $value ) )
                    return $this->raiseError( UM_INPUT_ERROR, "ICQ_Number", "Invalid chars in your icq number." );
            }
			
            // Check is MSN valid.
            if ( preg_match( "/msn/i", $key) )
            {
                if ( strlen( $value ) > UM_MSN_MAX_LEN )
                    return $this->raiseError( UM_HACKER_ATTEMPT );
            }
			
            // Check is AIM valid.
            if ( preg_match( "/aim/i", $key ) )
            {
                if ( strlen( $value ) > UM_AIM_MAX_LEN )
                    return $this->raiseError( UM_HACKER_ATTEMPT );
            }

            // Check is Website valid.
            if ( preg_match( "/website/i", $key ) )
            {
                if ( strlen( $value ) > UM_WEBSITE_MAX_LEN )
                    return $this->raiseError( UM_HACKER_ATTEMPT );
                
                if ( !preg_match( "/^http:+./i", $value ) )
                    return $this->raiseError( UM_INPUT_ERROR, "Website", "Invalid URL given." );
            }

            // Extra checks
            if ( preg_match( "/unlock_id|session_id/i", $key ) )
            {
                if ( strlen( $value ) > 32 )
                    return $this->raiseError( UM_HACKER_ATTEMPT );
                
                if ( !preg_match( "/^[a-z0-9]+$/", $value ) )
                    return $this->raiseError( UM_INPUT_ERROR, "Id", "Invalid ". $key ." given." );
            }
            
            if ( preg_match( "/group_name/i", $key ) )
            {
                if ( strlen( $value ) > UM_GROUP_NAME_MAX_LEN )
                    return $this->raiseError( UM_HACKER_ATTEMPT );
                
                if ( !preg_match( "/^[A-Za-z0-9_-]+$/", $value) )
                    return $this->raiseError( UM_INPUT_ERROR, "Group Name", "Invalid group_name given." );
            }

            if ( preg_match( "/group_desc/i", $key ) )
            {
                if ( strlen( $value ) > UM_GROUP_DESC_MAX_LEN )
                    return $this->raiseError( UM_HACKER_ATTEMPT );
                
                if ( !preg_match("/^[A-Za-z0-9_-]+$/", $value) )
                    return $this->raiseError( UM_INPUT_ERROR, "Group Description", "Invalid group_desc given." );
            }

            if ( preg_match( "/level/i", $key ) )
            {
                if ( strlen( $value ) > UM_GROUP_LEVEL_MAX_LEN )
                    return $this->raiseError( UM_HACKER_ATTEMPT );
                
                if ( !preg_match( "/^[A-Za-z0-9_-]+$/", $value ) )
                    return $this->raiseError( UM_INPUT_ERROR, "Group Description", "Invalid group_desc given." );
            }
            
            if ( preg_match( "/timeout/i", $key ) )
            {
                if ( strlen( $value ) > UM_TIMEOUT_MAX_LEN )
                    return $this->raiseError( UM_HACKER_ATTEMPT );
                
                if ( !preg_match("/^[0-9]+$/", $value ) )
                    return $this->raiseError( UM_INPUT_ERROR, "Timeout", "Invalid timeout given." );
            }
            
        }
		
        return true;
    }

	/**
	 * @access private
	 */
    function _userNotExists( $username )
    {
        $this->query  = 'SELECT user_id FROM '. UM_USERS_TABLE .' WHERE LOWER(username) = \''. strtolower($username) .'\' LIMIT 1';   
        $this->result = $this->db->query( $this->query );
        
		if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR );
        
        $this->query = '';
        
        if ( $row = $this->result->fetchRow() )
            return $this->raiseError( UM_USER_EXISTS, "User", "User alreadey exists." );    

        $this->result->free();
        return true;
    }

	/**
	 * @access private
	 */
    function _groupNotExists( $group_name )
    {
        $this->query  = 'SELECT group_id FROM '. UM_GROUPS_TABLE .' WHERE LOWER(group_name) = \''. strtolower($group_name) .'\' LIMIT 1';
        $this->result = $this->db->query( $this->query );
        
        if ( PEAR::isError( $this->result ) )
            return $this->raiseError( UM_SQL_ERROR, "", "", __LINE__, __FILE__ );
        
        if ( $row = $this->result->fetchRow() )
            return $this->raiseError( UM_GROUP_EXISTS, "Create Group", "Group does already exists." );
        
        $this->result->free();
        return true;
    }
	
	/**
	 * @access private
	 */
    function _generateID( $username )
    {
        return md5( Util::getMicrotime() . $username );
    }

    /**
	 * Return if isset HTTP REFERER, else null.
	 *
	 * @access private
	 */
    function _getReferer()
    {
        if ( isset( $_SERVER["HTTP_REFERER"] ) && $_SERVER["HTTP_REFERER"] != '' )
            return $_SERVER["HTTP_REFERER"];
        else
            return null;
    }

    /**
	 * Return if isset $_SERVER["HTTP_USER_AGENT"], else null.
	 *
	 * @access private
	 */
    function _getBrowser()
    {
        if ( isset( $_SERVER["HTTP_USER_AGENT"] ) && $_SERVER["HTTP_USER_AGENT"] != '' )
            return $_SERVER["HTTP_USER_AGENT"];
        else
            return null;
    }
    
    /**
	 * Return Unix Timestamp.
	 *
	 * @access private
	 */
    function _timestamp()
    {
        return date( 'U' );
    }

    /**
	 * Return formatted datetime.
	 *
	 * @access private
	 */
    function _datetime()
    {
        return date( 'Y-m-d H:i:s' );
    }
} // END OF UserManagement

?>
