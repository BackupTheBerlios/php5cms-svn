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
 * Class to manage User and Group Accounts on Shadow based Linux/Unix Systems.
 *
 * To easily manage all accounts we first read them into some arrays.
 * And after making all changes, we write them back to the file. I
 * think thats the fastest way to do the job. Yes it consumes much
 * memory but I think thats not so much important.
 * 
 * This is the structure of the files, which we use :
 * /etc/passwd -> Username:x:UID:GID:GCOS:Home:Shell
 * /etc/shadow -> Username:Password:DOC:MindD:MaxD:Warn:Exp:Dis:Res
 * /etc/group  -> Groupname:Password:GID:Member
 * /etc/gshadow-> Groupname:Password:Groupmanager:Member
 * 
 * To prevent, that two jobs are using the user and Group Files we
 * create a simple lock file, and if it the file exists at startup
 * we do nothing.
 * 
 * All functions in this class return TRUE on success and FALSE on error,
 * so you can easily catch all errors. All errors of this function are
 * stored in the variable $error_msg. You can then read it and provide
 * a custom error message.
 * 
 * You can use the following functions in your applications :
 * int user_add(username,userid,primary_group,name,shell,homedir,password);
 * int user_del(username);
 * int user_mod(username,new_name,new_shell,new_password);
 * int group_add(groupname,member,password,manager);
 * int group_del(groupname);
 * int group_mod(groupname,new_name);
 * int add_to_group(groupname,username);
 * int del_from_group(groupname,username);
 * int del_from_all_groups(username);
 * 
 * There are 2 more functions, which provide some simple constructor and
 * destructor functions for the class. So you had to call the constructor
 * first, then use the functions and then call the destructor to write all
 * data back into the files and remove the lock file.
 * 
 * int shadow(passwd_path,shadow_path,group_path,gshadow_path,enable_logging);      -> The pseudo constructor
 * int stop_shadow();                     -> The pseudo destructor
 * 
 * If you like, you can activate the logging into your syslog, so all actions
 * somebody makes, will be reported to it. To do this call the pseudo constructor
 * with a 1 as first parameter.
 * 
 * We use the following arrays to manage the user and group Accounts in memory
 *
 * $user["username"]   = Username;
 * $user["password"]   = Password;
 * $user["uid"]        = UserID;
 * $user["gid"]        = GroupID;
 * $user["gcos"]       = Real Name;
 * $user["home"]       = Homedir;
 * $user["shell"]      = Shell;
 * $user["doc"]        = Date of last password change in days from the 1.1.1970
 * $user["mind"]       = Minimal days the password is valid
 * $user["maxd"]       = Maximum days the password is valid
 * $user["warn"]       = Number of days before the user recives a message that he should change his password
 * $user["exp"]        = After maxd, how many days is the password still valid
 * $user["dis"]        = Up to this day (counted from 1.1.1970) the account is locked
 * 
 * $group["groupname"] = Groupname;
 * $group["password"]  = Password;
 * $group["gid"]       = GroupID;
 * $group["member"]    = Comma seperated list of members;
 * $group["manager"]   = Comma seperated list of group managers;
 * 
 * $homed["dir"]       = Homedirectory;
 * $homed["user"]      = User which this directory belongs to;
 * $homed["group"]     = The group which this directory belongs to;
 *
 * All homedirectorys are chmoded 755;
 *
 * @package auth
 */

class Shadow extends PEAR
{
	/**
	 * @access public
	 */
	var $error_msg;
	
	/**
	 * Here are all arrays of the type $user are stored in
	 * @access public
	 */
	var $userdata;
	
	/**
	 * Here are all arrays of the type $group are stored in
	 * @access public
	 */
	var $groupdata;
	
	/**
	 * Little Array which contains number to name assignments for usernames
	 * @access public
	 */
	var $fastseek;
	
	/**
	 * Little Array which contains number to name assignments for groupnames
	 * @access public
	 */
	var $g_fastseek;
	
	/**
	 * The homedirectorys which have to be created
	 * @access public
	 */
	var $homedirs;
	
	/**
	 * The first UserID a user can have
	 * @access public
	 */
	var $start_uid;
	
	/**
	 * The first GroupID a group can have
	 * @access public
	 */
	var $start_gid;
	
	/**
	 * The location of the shadow file
	 * @access public
	 */
	var $shadow_file;
	
	/**
	 * The location of the passwd file
	 * @access public
	 */
	var $passwd_file;
	
	/**
	 * The location of the group file
	 * @access public
	 */
	var $group_file;
	
	/**
	 * The location of the gshadow file
	 * @access public
	 */
	var $g_shadow_file;
	
	/**
	 * The name of the lock file
	 * @access public
	 */
	var $lock_file;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Shadow( $passwd = "/etc/passwd", $shadow = "/etc/shadow", $groupf = "/etc/group", $gshadow = "/etc/gshadow" )
  	{
		// Set the minimum UserID
		$this->start_uid = 100;

		// Set the minimum GroupID
		$this->start_gid = 100;

		// Set the passwd file
		$this->passwd_file = $passwd;

		// Set the shadow file
		$this->shadow_file = $shadow;

		// Set the group file
		$this->group_file = $groupf;

		// Set the gshadow file
		$this->g_shadow_file = $gshadow;

		// Set the lockfile name
		$this->lock_file = "C:\\temp\\shadow.lock";

		// Initalize an array
		$this->homedirs = array();

		// Read the shadow user file into the array
		$sp = fopen( $this->shadow_file, "r" );
		$i  = 0;

		while ( !feof( $sp ) )
		{
			$temp = fgets( $sp, 8069 );
			$temp = rtrim( ereg_replace( "\n", " ", $temp ) );
			$temp = explode( ":", $temp );
			
			$user["username"] = $temp[0];
			$user["password"] = $temp[1];
			$user["doc"]      = $temp[2];
			$user["mind"]     = $temp[3];
			$user["maxd"]     = $temp[4];
			$user["warn"]     = $temp[5];
			$user["exp"]      = $temp[6];
			$user["dis"]      = $temp[7];
			
			$temp = $user["username"];

			$this->userdata[$i] = $user;
			$this->fastseek[$temp] = $i;
			
			$i++;
		}

		fclose( $sp );

		$pp = fopen( $this->passwd_file, "r" );
		
		while ( !feof( $pp ) )
		{
			$temp = fgets( $pp, 8096 );
			$temp = rtrim( ereg_replace( "\n", " ", $temp ) );
			$temp = explode( ":", $temp );
			$username = $temp[0];
			$i = -1;
			$i = $this->fastseek[$username];

			if ( $i != -1 )
			{
				$this->userdata[$i]["uid"]   = $temp[2];
				$this->userdata[$i]["gid"]   = $temp[3];
				$this->userdata[$i]["gcos"]  = $temp[4];
				$this->userdata[$i]["home"]  = $temp[5];
				$this->userdata[$i]["shell"] = $temp[6];
			}
		}

		fclose( $pp );

		// Now we read all groups into another array

		$gp = fopen( $this->group_file, "r" );
		$i = 0;
		
		while ( !feof( $gp ) )
		{
			$temp = fgets( $gp, 8096 );
			$temp = rtrim( ereg_replace( "\n", " ", $temp ) );
			$temp = explode( ":", $temp );

			$group["groupname"] = $temp[0];
			$group["gid"]       = $temp[2];
			$group["member"]    = $temp[3];
			
			$temp = $temp[0];

			$this->groupdata[$i] = $group;
			$this->g_fastseek[$temp] = $i;
			
			$i++;
		}

		fclose( $gp );

		$gs = fopen( $this->g_shadow_file, "r" );
		$i = -1;
		
		while ( !feof( $gs ) )
		{
			$temp = fgets( $gs, 8096 );
			$temp = rtrim( ereg_replace( "\n", " ", $temp ) );
			$temp = explode( ":", $temp );
			
			$groupname = $temp[0];
			$i = $this->g_fastseek[$groupname];

			if ( $i != -1 )
			{
				$this->groupdata[$i]["password"] = $temp[1];
				$this->groupdata[$i]["manager"]  = $temp[2];
			}
		}
		
		fclose( $gs );
	}

	/**
	 * @access public
	 */
	function stop_shadow()
	{
		// To avoide damages to production files, we make backup copies
		// of the original files. We delete them at the next run.

		if ( file_exists( $this->passwd_file . "-backup" ) )
			unlink( $this->passwd_file ."-backup" );
    
		copy( $this->passwd_file, $this->passwd_file . "-backup" );
		
		if ( file_exists( $this->shadow_file . "-backup" ) )
			unlink( $this->shadow_file . "-backup" );
		
		copy( $this->shadow_file, $this->shadow_file . "-backup" );
		
		if ( file_exists( $this->group_file . "-backup" ) )
			unlink( $this->group_file . "-backup" );
    
		copy( $this->group_file, $this->group_file . "-backup" );
		
		if ( file_exists( $this->g_shadow_file . "-backup" ) )
			unlink( $this->g_shadow_file . "-backup" );
    
		copy( $this->g_shadow_file, $this->g_shadow_file . "-backup" );


		// Write back all user informations into the correct files.
		
		$pf = fopen( $this->passwd_file, "w" );
		$sf = fopen( $this->shadow_file, "w" );
		
		for ( $i = 0 ; $i < count( $this->userdata ) ; $i++ )
    	{
      		if ( $this->userdata[$i]["username"] != "" )
      		{
       			if ( $i == count( $this->userdata ) - 1 )
       			{
         			$passwd = $this->userdata[$i]["username"] . ":x:" . $this->userdata[$i]["uid"]      . ":" . $this->userdata[$i]["gid"] . ":" . $this->userdata[$i]["gcos"] . ":" . $this->userdata[$i]["home"] . ":" . $this->userdata[$i]["shell"];
         			$shadow = $this->userdata[$i]["username"] . ":"   . $this->userdata[$i]["password"] . ":" . $this->userdata[$i]["doc"] . ":" . $this->userdata[$i]["mind"] . ":" . $this->userdata[$i]["maxd"] . ":" . $this->userdata[$i]["warn"] . ":" . $this->userdata[$i]["exp"] . ":" . $this->userdata[$i]["dis"] . ":";
       			}
       			else
       			{
         			$passwd = $this->userdata[$i]["username"] . ":x:" . $this->userdata[$i]["uid"]      . ":" . $this->userdata[$i]["gid"] . ":" . $this->userdata[$i]["gcos"] . ":" . $this->userdata[$i]["home"] . ":" . $this->userdata[$i]["shell"] . "\n";
         			$shadow = $this->userdata[$i]["username"] . ":"   . $this->userdata[$i]["password"] . ":" . $this->userdata[$i]["doc"] . ":" . $this->userdata[$i]["mind"] . ":" . $this->userdata[$i]["maxd"] . ":" . $this->userdata[$i]["warn"]  . ":" . $this->userdata[$i]["exp"] . ":" . $this->userdata[$i]["dis"] . ":\n";
       			}
       
	   			fputs( $pf, $passwd );
       			fputs( $sf, $shadow );
      		}
    	}
    
		fclose( $pf );
    	fclose( $sf );
		
		
		// Write all group informations into the correct files.

    	$gf = fopen( $this->group_file,   "w" );
		$sg = fopen( $this->g_shadow_file, "w" );
		
    	for ( $i = 0 ; $i < count( $this->groupdata ) ; $i++ )
    	{
      		if ( $this->groupdata[$i]["groupname"] != "" )
      		{
       			if ( $i == count( $this->groupdata ) - 1 )
       			{
        	 		$group   = $this->groupdata[$i]["groupname"] . ":x:" . $this->groupdata[$i]["gid"]      . ":" . $this->groupdata[$i]["member"];
         			$gshadow = $this->groupdata[$i]["groupname"] . ":"   . $this->groupdata[$i]["password"] . ":" . $this->groupdata[$i]["manager"] . ":" . $this->groupdata[$i]["member"];
       			}
       			else
       			{
         			$group   = $this->groupdata[$i]["groupname"] . ":x:" . $this->groupdata[$i]["gid"]      . ":" . $this->groupdata[$i]["member"]  ."\n";
         			$gshadow = $this->groupdata[$i]["groupname"] . ":"   . $this->groupdata[$i]["password"] . ":" . $this->groupdata[$i]["manager"] . ":" . $this->groupdata[$i]["member"] . "\n";
       			}
       
	   			fputs( $gf, $group   );
       			fputs( $sg, $gshadow );
      		}
    	}
    
		fclose( $gf );
    	fclose( $sg );

    	
		// Create or delete homedirectorys and set permissions;

		for ( $i = 0 ; $i < count( $this->homedirs ) ; $i++ )
    	{
      		if ( $this->homedirs[$i]["action"] == "create" )
      		{
       			mkdir( $this->homedirs[$i]["dir"], 0755 );
       			chown( $this->homedirs[$i]["dir"], $this->homedirs[$i]["user"]  );
       			chgrp( $this->homedirs[$i]["dir"], $this->homedirs[$i]["group"] );
      		}
      		else
      		{
       			$cmd = "/bin/rm -R " . $this->homedirs[$i]["dir"];
       			$res = exec( $cmd );
			}
		}

		unlink( $this->lock_file );
		return true;
	}

	/**
	 * @access public
	 */
	function user_add( $username, $uid, $group, $gcos, $shell, $home, $password )
  	{
   		if ( $username == "" )
   		{
     		$this->error_msg = "NO Username specified";
     		return false;
   		}

   		if ( $uid < 0 || $uid > 64999 )
   		{
     		$this->error_msg = "Incorrect UserID";
     		return false;
   		}

   		if ( $home == "" )
   		{
     		$this->error_msg = "Please provide a home directory !";
     		return false;
   		}

		for ( $i = 0 ; $i < count( $this->userdata ) ; $i++ )
   		{
     		if ( $this->userdata[$i]["uid"] == $uid )
     		{
       			$this->error_msg = "Userid already exists";
       			return false;
     		}
   		}

   		for ( $i = 0 ; $i < count( $this->userdata ) ; $i++ )
   		{
			if ( $this->userdata[$i]["username"] == $username )
			{
				$this->error_msg = "Username already exists";
				return false;
			}
		}

		$i   = count( $this->userdata );
		$doc = time() / 86400;
		$doc = explode( ".", $doc );
		
		$this->userdata[$i]["username"] = $username;
		$this->userdata[$i]["password"] = crypt( $password );
		$this->userdata[$i]["uid"]      = $uid;
		$this->userdata[$i]["gid"]      = $this->group_to_gid( $group );
		$this->userdata[$i]["gcos"]     = $gcos;
		$this->userdata[$i]["home"]     = $home;
		$this->userdata[$i]["shell"]    = $shell;
		$this->userdata[$i]["doc"]      = $doc[0];
		$this->userdata[$i]["mind"]     = "";
		$this->userdata[$i]["maxd"]     = "";
		$this->userdata[$i]["warn"]     = "";
		$this->userdata[$i]["exp"]      = "";
		$this->userdata[$i]["dis"]      = "";

		$i = count($this->homedirs);
		
		$this->homedirs[$i]["action"]   = "create";
		$this->homedirs[$i]["dir"]      = $home;
		$this->homedirs[$i]["user"]     = $username;
		$this->homedirs[$i]["group"]    = $group;

		$this->fastseek[$username] = $i;

		if ( !$this->add_to_group( $group, $username ) )
   		{
      		$this->error_msg = "Could not add user " . $username . " to group " . $group;
      		unset( $this->userdata[count( $this->userdata )] );
      
	  		return false;
   		}

   		return true;
	}

	/**
	 * @access public
	 */
	function user_del($username)
	{
		if ( $username == "" )
    	{
      		$this->error_msg = "No Username specified !";
      		return false;
    	}

    	for ( $i = 0 ; $i < count( $this->userdata ) ; $i++ )
    	{
      		if ( $this->userdata[$i]["username"] == $username )
      		break;
    	}

    	$this->del_from_all_groups( $username );
    	$j = count( $this->homedirs );
    	$this->homedirs[$j]["action"] = "delete";
    	$this->homedirs[$j]["dir"]    = $this->userdata[$i]["home"];
    	$this->homedirs[$j]["user"]   = $username;
    	unset( $this->userdata[$i] );

    	return true;

    	// unset($this->fastseek[$username]);
	}

	/**
	 * @access public
	 */
	function user_mod( $username, $new_gcos, $new_shell, $new_password )
  	{
    	if ( $username == "" )
    	{
      		$this->error_msg = "No Username specified!";
      		return false;
    	}
    	else
    	{
      		for ( $i = 0 ; $i < count( $this->userdata ) ; $i++ )
      		{
        		if ( $this->userdata[$i]["username"] == $username )
          		break;
      		}

      		if ( $new_gcos != "" )
				$this->userdata[$i]["gcos"]  = $new_gcos;

			if ( $new_shell != "" )
				$this->userdata[$i]["shell"] = $new_shell;

			if ( $new_password != "" )
				$this->userdata[$i]["password"] = crypt( $new_password );
		}

		return true;
	}

	/**
	 * @access public
	 */
	function group_add( $groupname, $member = "",$password = "",$manager = "" )
  	{
    	if ( $groupname == "" )
    	{
      		$this->error_msg = "No groupname specified";
      		return false;
    	}

    	$i = count( $this->groupdata );
		$this->groupdata[$i]["groupname"] = $groupname;
    
		if ( $password != "" )
			$this->groupdata[$i]["password"] = crypt( $password );
		else
			$this->groupdata[$i]["password"] = "";
    
		$this->groupdata[$i]["gid"]     = $this->get_next_gid();
		$this->groupdata[$i]["member"]  = $member;
		$this->groupdata[$i]["manager"] = $manager;

		$this->g_fastseek[$groupname] = $i;
		return true;
	}

	/**
	 * @access public
	 */
	function group_del( $groupname )
	{
		$i = $this->g_fastseek[$groupname];
		unset( $this->groupdata[$i] );
		unset( $this->g_fastseek[$groupname] );
		
		return true;
	}

	/**
	 * @access public
	 */
	function group_mod( $groupname, $new_name )
  	{
   		if ( $groupname == "" )
   		{
     		$this->error_msg = "No Groupname specified";
     		return false;
   		}

   		if ( $new_name == "" )
   		{
     		$this->error_msg = "No new Groupname specified";
     		return false;
   		}

   		$i = $this->g_fastseek[$groupname];
   
   		if ( !isset( $i ) )
   		{
     		$this->error_msg = "Group does not exist";
     		return false;
   		}

   		$this->groupdata[$i]["groupname"] = $new_name;
   		return true;
  	}

	/**
	 * @access public
	 */
	function add_to_group( $groupname, $user )
  	{
    	$new_member = "";
    	$i = $this->g_fastseek[$groupname];
    
		if ( !isset( $i ) )
    	{
      		$this->error_msg = "Group does not exist";
      		return false;
    	}

    	$member   = $this->groupdata[$i]["member"];
    	$member   = explode( ",", $member );
    	$memcount = count( $member );
    	$member[$memcount] = $user;

    	for ( $j = 0; $j <= $memcount; $j++ )
    	{
      		if ( $j == 0 || $new_member == "" )
				$new_member = $member[$j];
			else
				$new_member = $new_member.",".$member[$j];
      
      		echo $j . "=>" . $new_member . "<br>\n";
    	}

    	$this->groupdata[$i]["member"] = $new_member;
		return true;
  	}

	/**
	 * @access public
	 */
	function del_from_group( $groupname, $user )
  	{
    	$i = $this->g_fastseek[$groupname];
    
		if ( !isset( $i ) )
    	{
      		$this->error_msg = "Group does not exist";
      		return false;
    	}

		$member   = $this->groupdata[$i]["member"];
		$member   = explode( ",", $member );
		$memcount = count( $member );

    	for ( $j = 0; $j < $memcount; $j++)
    	{
      		if ( $member[$j] != $user )
      		{
        		if ( $j == 0 )
					$new_member = $member[$j];
				else
					$new_member = $new_member . "," . $member[$j];
			}
		}

		$this->groupdata[$i]["member"] = $new_member;
		return true;
	}

	/**
	 * @access public
	 */
	function del_from_all_groups( $user )
  	{
    	for ( $i = 0; $i < count( $this->groupdata ); $i++ )
			$this->del_from_group( $this->groupdata[$i]["groupname"], $user );

    	return true;
  	}

	/**
	 * @access public
	 */
	function get_next_uid()
	{
		$uid = $this->start_uid;

		do
		{
      		$used = 0;
      
	  		for ( $i = 0; $i < count( $this->userdata ); $i++ )
      		{
        		if ( $uid == $this->userdata[$i]["uid"] )
        		{
          			$used = 1;
          			$uid++;
          
		  			break;
        		}
      		}
    	} while ( $used == 1 && $uid <= 64999 );

    	if ( $uid <= 64999 )
    	{
      		return $uid;
    	}
    	else
    	{
      		$this->error_msg = "UserID greater than 65000";
      		return false;
    	}
  	}

	/**
	 * @access public
	 */
	function get_next_gid()
  	{
    	$gid = $this->start_gid;

    	do
		{
      		$used = 0;
      
	  		for ( $i = 0; $i < count( $this->groupdata ); $i++ )
      		{
        		if ( $gid == $this->groupdata[$i]["gid"] && $gid <= 64999 )
        		{
          			$used = 1;
          			$gid++;
          
		  			break;
        		}
      		}
    	} while ( $used == 1 && $gid <= 64999 );

    	if ( $gid <= 64999 )
    	{
      		return $gid;
    	}
    	else
    	{
      		$this->error_msg = "GroupID greater than 65000";
      		return false;
    	}
  	}

	/**
	 * @access public
	 */
  	function uid_to_user( $uid )
  	{
    	for ( $i = 0; $i < count( $this->userdata ); $i++ )
    	{
      		if ( $uid == $this->userdata[$i]["uid"] )
      			return $this->userdata[$i]["username"];
    	}
  	}

	/**
	 * @access public
	 */
	function gid_to_group( $gid )
  	{
    	for ( $i = 0; $i < count( $this->groupdata ); $i++ )
    	{
      		if ( $gid == $this->groupdata[$i]["gid"] )
      			return $this->groupdata[$i]["groupname"];
		}
	}

	/**
	 * @access public
	 */
	function group_to_gid( $group )
  	{
    	for ( $i = 0; $i < count( $this->groupdata ); $i++ )
    	{
      		if ( $group == $this->groupdata[$i]["groupname"] )
      			return $this->groupdata[$i]["gid"];
    	}
  	}

	/**
	 * @access public
	 */
  	function user_to_uid( $user )
  	{
    	for ( $i = 0; $i < count( $this->userdata ); $i++ )
    	{
      		if ( $user == $this->userdata[$i]["username"] )
      			return $this->userdata[$i]["uid"];
    	}
  	}

	/**
	 * @access public
	 */
  	function show_all()
  	{
    	for ( $i = 0; $i < count( $this->userdata ); $i++ )
    	{
     		while ( list( $key, $val ) = each( $this->userdata[$i] ) )
     			echo "$key => $val\n";
    	}
    
		for ( $i = 0; $i < count( $this->groupdata ); $i++ )
    	{
     		while ( list( $key, $val ) = each( $this->groupdata[$i] ) )
     			echo "$key => $val\n";
    	}
  	}
} // END OF Shadow

?>
