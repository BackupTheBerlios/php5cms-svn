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


define( "SESSIONUTIL_SAVEPATH_WIN",   "c:\\temp" );
define( "SESSIONUTIL_SAVEPATH_LINUX", "/tmp" );


/**
 * @package session
 */
 
class SessionUtil extends PEAR
{
	/**
	 * @access private
	 */
	var $_diffSess;
	
	/**
	 * Save path for sessions
	 * @access private
	 */
	var $_path;
	
	
	/**
	 * Methods to transfer session between different servers.
	 *
	 * @access public
	 */
	function createTransmitString() 
	{
		$str =  "";
		$i   = 1;
		
		foreach( $_SESSION as $k => $v )
		{
			$str .= "&sn" . $i . "=" . base64_encode( $k ) . "&sv" . $i . "=" . base64_encode( serialize( $v ) );
			$i++;
		}
		
		return $str;
	}
   	
	/**
	 * @access public
	 */
	function readTransmitString() 
	{
		$i = 1;
		while ( 1 )
		{
			if ( isset( $_GET["sn" . $i] ) )
				$_SESSION[base64_decode( $_GET["sn" . $i] )] = unserialize( base64_decode( $_GET["sv" . $i] ) );
			else
				break;
			
			$i++;
   		}
   		
		return $str;
   	}
	
	/**
	 * This function takes the string data from session_encode()
	 * and returns the variable names for that session in an array.
	 *
	 * @access public
	 */
	function getSessionVars( $enc_data )
	{
		// It seems when there is an array it may contain | or ; between { and }
		// so here I effectively delete all data between { and } and set } = ;
		// so when I parse later it knows where the variable data ended.
  		while ( strpos( $enc_data, "{" ) )
    	{
    		$decode_data .= substr( $enc_data, 0, strpos( $enc_data, "{" ) );
    		$decode_data .= strstr( $enc_data, "}" );
    		$decode_data  = str_replace( "}", ";", $decode_data );
    		$enc_data     = $decode_data;
    	}

	  	// move all variable names and their data into array elements
  		$format_data = explode( ";", $enc_data );

	  	// snatch the variable name (first part of string before |)
  		for ( $i = 0; $i < count( $format_data ); $i++ )
   	 	{
    		// if the variable is null it is set to !varname so
    		// this grabs the first one and all subsequent null vars
    		if ( substr( $format_data[$i], 0, 1 ) == "!" )
      		{
      			$bang_vars = $format_data[$i];
      
	  			while ( strstr( $bang_vars, "!" ) )
        		{
        			$names[]   = substr( $bang_vars, 1, strpos( $bang_vars, "|" ) - 1 );
        			$bang_vars = strstr( $bang_vars, "|" );
        		}
      		}
    		else
      		{
      			$var_name = substr( $format_data[$i], 0, strpos( $format_data[$i], "|" ) );
      			$names[]  = $var_name;
      		}
    	}

  		// delete last entry if empty
  		if ( $names[count( $names )-1] == "" )
    		array_pop( $names );

		return $names;
	}
	
	
	/**
	 * Methods that are meant to lists all file based concurrent sessions on a site.
	 * It needs read access to session files temporary directory.
	 *
	 * @access public
	 */
	function getSessionsCount()
	{
		if ( !$this->_diffSess )
			$this->_readSessions();
		
		return sizeof( $this->_diffSess );
	}

   	/**
	 * @access public
	 */
	function getSessions()
	{
		if ( !$this->_diffSess )
			$this->_readSessions();
		
		return $this->_diffSess;
	}   		

	/**
	 * Set session save path.
	 *
	 * @access public
	 */
	function setSavePath( $path = "" )
	{
		$this->_path = ( empty( $path )? ini_get( 'session.save_path' ) : $path );
		
		if ( !is_dir( $this->_path ) )
		{
			if ( eregi( "win", strtolower( PHP_OS ) ) )
			{
				ini_set( "session.save_path", SESSIONUTIL_SAVEPATH_WIN );
				$res = mkdir( SESSIONUTIL_SAVEPATH_WIN );
				
				$this->_path = SESSIONUTIL_SAVEPATH_WIN;
			}
			else if ( eregi( "linux", strtolower( PHP_OS ) ) )
			{
				ini_set( "session.save_path", SESSIONUTIL_SAVEPATH_LINUX );
				$res = mkdir( SESSIONUTIL_SAVEPATH_LINUX, 0700 );
				
				$this->_path = SESSIONUTIL_SAVEPATH_LINUX;
			}
			
			return $res;
		}
		
		return true;
	}
	
	/**
	 * This function deletes any cookies on the browsing user's system that were
	 * sent from the current domain up to 86400 seconds (24 hours) ago.
	 *
	 * @access public
	 * @static
	 */
	function deleteSessionCookies()
	{
		$session_name = session_name();
		$cookie_parms = session_get_cookie_params();

		setcookie( $session_name, "", time() - 864000, $cookie_parms["path"], $cookie_parms["domain"] );
	}

	/**
	 * @access public
	 * @static
	 */
	function getUniqueID( $length = 32, $pool = "" )
	{ 
		// set pool of possible char 
		if ( $pool == "" )
			$pool  = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	
 		mt_srand( (double)microtime() * 1000000 ); 
 		$unique_id = ""; 
 
 		for ( $index = 0; $index < $length; $index++ )
			$unique_id .= substr( $pool, ( mt_rand() % ( strlen( $pool ) ) ), 1 );
		 
 		return( $unique_id ); 
	}
	
	/**
	 * @access public
	 * @static
	 */
	function getSessionID()
	{ 
		srand( time() ); 
    	$a = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"; 

		for ( $i; $i <= 16; $i++ )
			$id .= substr( $a, ( rand() % ( strlen( $a ) ) ), 1 ); 
    
		return( md5( $id ) ); 
	}
	
	/**
	 * @access public
	 * @static
	 */
	function getUniqueID_md5()
	{ 
		mt_srand( (double)microtime() * 1000000 ); 
		return $unique_id = md5( uniqid( mt_rand(), 1 ) ); 
	}
	
	
	// private methods

	/**
	 * @access private
	 */
	function _readSessions()
	{
		$sessPath = get_cfg_var( "session.save_path" ) . "\\";
		$this->_diffSess = array();
			
		$dh = @opendir( $sessPath );
		while ( ( $file = @readdir( $dh ) ) !== false )
		{
			if ( $file != "." && $file != ".." )
			{
				$fullpath = $sessPath . $file; 
				
				if ( !@is_dir( $fullpath ) )
				{
					// "sess_7480686aac30b0a15f5bcb78df2a3918"
					$fA = explode( "_", $file );
					
					// array( "sess", "7480686aac30b0a15f5bcb78df2a3918" )
					$sessValues = file_get_contents( $fullpath ); // get raw session data
					
					// this raw data looks like serialize() result, but is is not extactly this.
					$this->_diffSess[$fA[1]] = $sessValues;
				}
			}
		}
		
		@closedir( $dh );
   	}
} // END OF SessionUtil

?>
