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


using( 'auth.UnixUser' );


/**
 * @package auth
 */
 
class UnixPasswordFile extends PEAR
{
	/**
	 * @access public
	 */
	var $passwd_file;
	
	/**
	 * @access public
	 */
	var $shadow_file;
	
	/**
	 * @access public
	 */
	var $uses_shadowed_passwords;
	
	/**
	 * @access public
	 */
	var $fp;
	
	/**
	 * @access public
	 */
	var $file_open;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function UnixPasswordFile()
	{
		$this->passwd_file = ap_ini_get( "file_passwd", "file" );
		$this->shadow_file = ap_ini_get( "file_shadow", "file" );

		if ( file_exists( $this->shadow_file ) )
			$this->uses_shadowed_passwords = 1;

		$this->fp            = '';
		$this->file_open     = 0;
	}


	/**
	 * @access public
	 */	
	function OpenFile()
	{
		$this->file_open = ( $this->fp = fopen( $this->passwd_file, 'r' ) );
		return $this->file_open;
	}

	/**
	 * @access public
	 */
	function CloseFile()
	{
		$this->file_open = 0;
		fclose( $this->fp );
		
		return true;
	}

	/**
	 * @access public
	 */
	function ResetToBof()
	{
		if ( $this->file_open )
			fseek( $this->fp, 0 );
	}

	/**
	 * @access public
	 */
	function GetPasswordEntry( $user_obj )
	{
		if ( $this->file_open == 1 )
			$this->ResetToBof();

		$tmp_obj = new UnixUser();

		while( $tmp_obj = $this->NextPasswordEntry() )
		{
			if ( PEAR::isError( $tmp_obj ) )
				return array( false, $tmp_obj );
				
			if ( $tmp_obj->name == $user_obj->name )
				return array( true, $tmp_obj );
         
			if ( $tmp_obj->uid == $user_obj->name )
				return array( true, $tmp_obj );
		}

		return array( false, 'No match found.' );
	}

	/**
	 * @access public
	 */
	function NextPasswordEntry()
	{
		if ( $this->file_open == 0 )
		{
			if ( ! $this->OpenFile() )
				return PEAR::raiseError( 'Failed to open ' .  $this->passwd_file . ' for reading!' );
		}

		// Okay we have a open file read a line and parse it.
		$line_buffer = '';
		$read_result = 0;

		if ( !( $line_buffer = fgets( $this->fp, 4096) ) )
			return PEAR::raiseError( 'EOF' );

		// Okay we have a good line buffer let's parse it.
		$tmp_obj = new UnixUser();
		
		list (
			$tmp_obj->name,
			$tmp_obj->passwd,
			$tmp_obj->uid,
			$tmp_obj->gid,
			$tmp_obj->gcos,
			$tmp_obj->dir,
			$tmp_obj->shell ) = explode( ':', $line_buffer );
		
		return $tmp_obj;
	}

	/**
	 * @access public
	 */
	function All()
	{
		if ( $this->file_open == 1 )
			$this->ResetToBof();

		$tmp_arr = array();
		$tmp_cnt = 0;

		while ( $tmp_unix_user = $this->NextPasswordEntry() )
		{
			$tmp_arr[ $tmp_cnt ] = $tmp_unix_user;
			$tmp_cnt++;
		}

		return array( true, $tmp_cnt, $tmp_arr );
	}
} // END OF UnixPasswordFile

?>
