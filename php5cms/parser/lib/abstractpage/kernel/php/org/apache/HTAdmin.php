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
 * @package org_apache
 */
 
class HTAdmin extends PEAR
{
	/**
	 * @access public
	 */
	function is_valid_string( $string )
	{
		global $cfgBadChars;

		if ( empty( $string ) )
    		return true;

	  	for ( $i = 0; $i < strlen( $cfgBadChars ); $i++ )
		{
    		if ( strstr( $string, $cfgBadChars[$i] ) )
      			return true;
	  	}

		return false;
	}

	/**
	 * @access public
	 */
	function init_passwd_file( $filenum )
	{
		global $cfgHTPasswd;

		if ( empty( $cfgHTPasswd[0][N] ) )
    		return PEAR::raiseError( "First .htpasswd file is not set in config file." );

	  	if ( empty( $cfgHTPasswd[$filenum][N] ) )
    		return PEAR::raiseError( "First .htpasswd file is not set in config file." );

	  	if ( !file_exists( $cfgHTPasswd[$filenum][N] ) )
    		return PEAR::raiseError( ".htpasswd file does not exists." );

	  	if ( !is_readable( $cfgHTPasswd[$filenum][N] ) )
    		return PEAR::raiseError( ".htpasswd file is not readable." );

	  	if ( !is_writeable( $cfgHTPasswd[$filenum][N] ) )
    		return PEAR::raiseError( ".htpasswd file is not writeable." );
			
		return true;
	}

	/**
	 * @access public
	 */
	function read_passwd_file( $filenum )
	{
		global $cfgHTPasswd;
		global $htpUser;

	  	$res = $this->init_passwd_file( $filenum );

		if ( PEAR::isError( $res ) )
			return $res
		
		$htpUser = array();

	  	if ( !( $fpHt = fopen( $cfgHTPasswd[$filenum][N], "r" ) ) )
			return PEAR::raiseError( "Could not open file for reading: " . $cfgHTPasswd[$filenum][N] );
  	
  		$htpCount = 0;
  
  		while ( !feof( $fpHt ) )
		{
    		$fpLine    = fgets( $fpHt, 512 );
    		$fpLine    = trim( $fpLine );
    		$fpData    = explode( ":", $fpLine );
    		$fpData[0] = trim( $fpData[0] );
    		$fpData[1] = chop( trim( $fpData[1] ) );

	    	if ( empty( $fpLine ) || $fpLine[0] == '#' || $fpLine[0] == '*' ||	empty( $fpData[0] ) || empty( $fpData[1] ) )
    	  		continue;

	    	$htpUser[$htpCount][username] = $fpData[0];
    		$htpUser[$htpCount][password] = $fpData[1];
    		$htpCount++;
  		}
  
  		fclose( $fpHt );
  		return;
	}

	/**
	 * @access public
	 */
	function write_passwd_file( $filenum )
	{
		global $cfgHTPasswd;
		global $htpUser;

	  	$res = $this->init_passwd_file( $filenum );
	
		if ( PEAR::isError( $res ) )
			return $res
			
  		if ( ( $fpHt = fopen( $cfgHTPasswd[$filenum][N], "w" ) ) )
		{
    		for ( $i = 0; $i < count( $htpUser ); $i++ )
			{
      			if ( !empty( $htpUser[$i][username] ) )
        			fwrite( $fpHt, $htpUser[$i][username] . ":". $htpUser[$i][password] . "\n" );
	    	}
    
			fclose( $fpHt );
  		}
  		else
		{
    		return PEAR::raiseError( "Could not open file for writing: " . $cfgHTPasswd[$filenum][N] );
  		}
  	
		return true;
	}

	/**
	 * @access public
	 */
	function is_user( $username )
	{
		global $htpUser;

		if ( empty( $username ) )
    		return false;

	  	for ( $i = 0; $i < count( $htpUser ); $i++ )
		{
    		if ( $htpUser[$i][username] == $username )
      			return true;
  		}

		return false;
	}

	/**
	 * @access public
	 */
	function random()
	{
		srand( (double)microtime() * 1000000 );
		return rand();
	}

	/**
	 * @access public
	 */
	function crypt_password( $password )
	{
		if ( empty( $password ) )
    		return "** EMPTY PASSWORD **";

	  	$salt = random();
  		$salt = substr( $salt, 0, 2 );
  
  		return crypt( $password, $salt );
	}

	/**
	 * @access public
	 */
	function ht_auth()
	{
		global $cfgProgName;
		global $cfgVersion;
		global $cfgUseAuth;
  		global $cfgSuperUser;
		global $cfgSuperPass;

	  	if ( !$cfgUseAuth )
    		return;

	  	if ( ( $_SERVER["PHP_AUTH_USER"] != $cfgSuperUser ) || ( $_SERVER["PHP_AUTH_PW"] != $cfgSuperPass ) )
		{
    		header( "WWW-Authenticate: Basic realm=\"$cfgProgName $cfgVersion\"" );
    		header( "HTTP/1.0 401 Unauthorized" );
    		
			echo "Authentication failed.";
			exit;
  		}
	}
} // END OF HTAdmin

?>
