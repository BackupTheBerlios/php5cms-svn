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
|Authors: Dan Ellis <danellis@rushmore.com>                            |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'util.Util' );


define( "SIEVE_F_NO",   0 );		
define( "SIEVE_F_OK",   1 );
define( "SIEVE_F_DATA", 2 );
define( "SIEVE_F_HEAD", 3 );

define( "SIEVE_EC_NOT_LOGGED_IN",   0 );
define( "SIEVE_EC_QUOTA",          10 );
define( "SIEVE_EC_NOSCRIPTS",      20 );
define( "SIEVE_EC_UNKNOWN",       255 );


/**
 * SIEVE class - A Class that implements MANAGESIEVE in PHP.
 *
 * SIEVE is a mail filtering language, intended for server-side filtering
 * of emails. See RFC 3028 and Cyrusoft's page about SIEVE for more
 * details.
 *
 * In a nutshell, with SIEVE, all email gets filtered even before the
 * mail hits your inbox. This has numerous implications, both good and
 * bad. The language was designed to have no variables and no methods for
 * iterating, thus making the language very secure.
 *
 * MANAGESIEVE is a protocol for managing (uploading, downloading,
 * activating) SIEVE scripts on a remote mail server. Cyrus IMAP
 * project's timsieved is the most popular impelementation of SIEVE and
 * MANAGESIEVE in the open source world.
 *
 * This is a class written in PHP that supports the MANAGESIEVE
 * protocol, in order to allow users of web interfaces written in PHP to
 * interact with such servers without knowing any of the backend details.
 *
 * This program provides a handy interface into the Cyrus timsieved server
 * under php. It is tested with Sieve server included in Cyrus 2.0, but it
 * has been upgraded (not tested) to work with older Sieve server versions.
 *
 * @link http://www.faqs.org/rfcs/rfc3028.html
 * @link http://www.cyrusoft.com/sieve/
 *
 * @todo Maybe add the NOOP function.
 * @todo Have timing mechanism when port problems arise.
 *
 * @package peer_mail_sieve
 */
 
class Sieve extends PEAR
{
	/**
	 * @access public
	 */
	var $host;
	
	/**
	 * @access public
	 */
	var $port;
	
	/**
	 * @access public
	 */
	var $user;
	
	/**
	 * @access public
	 */
	var $pass;
	
	/**
	 * A comma seperated list of allowed auth types, in order of preference
	 * @access public
	 */
	var $auth_types;
	
	/**
	 * Type of authentication attempted
	 * @access public
	 */
	var $auth_in_use;

	/**
	 * @access public
	 */  
	var $line;
	
	/**
	 * @access public
	 */
	var $fp;
	
	/**
	 * @access public
	 */
	var $retval;
	
	/**
	 * @access public
	 */
	var $tmpfile;
	
	/**
	 * @access public
	 */
	var $fh;
	
	/**
	 * @access public
	 */
	var $len;
	
	/**
	 * @access public
	 */
	var $script;

	/**
	 * @access public
	 */
	var $loggedin;
	
	/**
	 * @access public
	 */
	var $capabilities;
	
	/**
	 * @access public
	 */
	var $response;
	

	/**
	 * Constructor
	 * 
	 * It will return
	 * false if it fails, true if all is well.  This also loads some arrays up
	 * with some handy information:
	 *
	 * @param $host string hostname to connect to. Usually the IMAP server where
	 * a SIEVE daemon, such as timsieved, is listening.
	 *
	 * @param $port string Numeric port to connect to. SIEVE daemons usually
	 * listen to port 2000.
	 *
	 * @param $user string is a super-user or proxy-user that has ACL rights to
	 * login on behalf of the $auth.
	 *
	 * @param $pass string password to use for authentication
	 *
	 * @param $auth string is the authorized user identity for which the SIEVE
	 * scripts will be managed.
	 *
	 * @param $auth_types string a string containing all the allowed
	 * authentication types allowed in order of preference, seperated by spaces.
	 * (ex.  "PLAIN DIGEST-MD5 CRAM-MD5"  The method the library will try first
	 * is PLAIN.) The default for this value is PLAIN.
	 *
	 * Note: $user, if included, is the account name (and $pass will be the
	 * password) of an administrator account that can act on behalf of the user.
	 * If you are using Cyrus, you must make sure that the admin account has
	 * rights to admin the user.  This is to allow admins to edit/view users
	 * scripts without having to know the user's password.  Very handy.
	 */
	function Sieve( $host, $port, $user, $pass, $auth = "", $auth_types = 'PLAIN' )
	{
		$this->host = $host;
		$this->port = $port;
		$this->user = $user;
		$this->pass = $pass;
		
		/* If there is no auth user, we deem the user itself to be the auth'd user */
		if ( !strcmp( $auth, "" ) )
			$this->auth = $this->user;
		else
			$this->auth = $auth;
			
		/* Allowed authentication types */
		$this->auth_types = $auth_types;
		
		$this->fp           = 0;
		$this->line         = "";
		$this->retval       = "";
		$this->tmpfile      = "";
		$this->fh           = 0;
		$this->len          = 0;
		$this->capabilities = "";
		$this->loggedin     = false;
	}
  
  
	/**
	 * Get response.
	 *
	 * @access public
	 * @throws Error
	 */
	function getResponse()
	{
		if ( $this->loggedin == false || feof( $this->fp ) )
			return PEAR::raiseError( "You are not logged in.", SIEVE_EC_NOT_LOGGED_IN );
		
		$error_raw = array();
		unset( $this->response );

		$this->line  = fgets( $this->fp, 1024 );
		$this->token = split( " ", $this->line, 2 );

		if ( $this->token[0] == "NO" )
		{
			/* we need to try and extract the error code from here. There are two possibilites: one, that it will take the form of:
			   NO ("yyyyy") "zzzzzzz" or, two, NO {yyyyy} "zzzzzzzzzzz" */
			$this->x = 0;
			list( $this->ltoken, $this->mtoken, $this->rtoken ) = split( " ", $this->line . " ", 3 );
        
			if ( $this->mtoken[0] == "{" )
			{
				while ( $this->mtoken[$this->x] != "}" || $this->err_len < 1 )
				{
					$this->err_len = substr( $this->mtoken, 1, $this->x );
					$this->x++;    
				}

				$this->line     = fgets( $this->fp, $this->err_len );
				$error_raw[]    = substr( $this->line, 0, strlen( $this->line ) - 2 ); // we want to be nice and strip crlf's
				$this->err_recv = strlen( $this->line );

				while ( $this->err_recv < $this->err_len )
				{
					$this->line      = fgets( $this->fp, ( $this->err_len - $this->err_recv ) );
					$error_raw[]     = substr( $this->line, 0, strlen( $this->line ) -2 ); // we want to be nice and strip crlf's
					$this->err_recv += strlen( $this->line );
				}
				
				$this->line = fgets( $this->fp, 1024 );
				
				$error_code = SIEVE_EC_UNKNOWN;
				$error_raw  = implode( ":", $error_raw );
			}
			else if ( $this->mtoken[0] == "(" )
			{
            	switch ( $this->mtoken )
				{
                	case "(\"QUOTA\")":
                    	$error_code = SIEVE_EC_QUOTA;
                    	$error_raw  = $this->rtoken;

                    	break;

	                default:
                    	$error_code = SIEVE_EC_UNKNOWN;
                    	$error_raw  = $this->rtoken;
                    	
						break;
				}
			}
			else
			{
            	$error_code = SIEVE_EC_UNKNOWN;
            	$error_raw  = $this->line;
        	}
        	
			return PEAR::raiseError( $error_raw, $error_code );
    	}
    	else if ( substr( $this->token[0], 0, 2 ) == "OK" )
		{
			return true;
		}
		else if ( $this->token[0][0] == "{" )
		{
			/* Unable wild assumption: that the only function that gets here is the get_script(), doesn't really matter though */       

			/* the first line is the len field {xx}, which we don't care about at this point */
			$this->line = fgets( $this->fp, 1024 );
			
			while ( substr( $this->line, 0, 2 ) != "OK" && substr( $this->line, 0, 2 ) != "NO" )
			{
				$this->response[] = $this->line;
				$this->line = fgets( $this->fp, 1024 );
        	}
        
			if ( substr( $this->line, 0, 2 ) == "OK" )
            	return true;
        	else
            	return false;
    	}
    	else if ( $this->token[0][0] == "\"" )
		{
        	/* I'm going under the _assumption_ that the only function that will get here is the listscripts().
			   I could very well be mistaken here, if I am, this part needs some rework */
        	$this->found_script = false;        

			while ( substr( $this->line, 0, 2 ) != "OK" && substr( $this->line, 0, 2 ) != "NO" )
			{
            	$this->found_script = true;
				list( $this->ltoken, $this->rtoken ) = explode( " ", $this->line . " ", 2 );
				
				/* hmmm, a bug in php, if there is no space on explode line, a warning is generated... */
           
				if ( strcmp( rtrim( $this->rtoken ), "ACTIVE" ) == 0 )
                	$this->response["ACTIVE"] = substr( rtrim( $this->ltoken ), 1, -1 );  
				else
					$this->response[] = substr( rtrim( $this->ltoken ), 1, -1 );
					
				$this->line = fgets( $this->fp, 1024 );
			}
        
			return true;
		}
		else
		{
			return PEAR::raiseError( $this->line, SIEVE_EC_UNKNOWN );
		}
	}

	/**
	 * Tokenize a line of input by quote marks and return them as an array
	 *
	 * @param  string $string Input line to parse for quotes
	 * @return array  Array of broken by quotes parts of original string
	 * @access public
	 */
	function parseForQuotes( $string )
	{
		$start = -1;
		$index = 0;

		for ( $ptr = 0; $ptr < strlen( $string ); $ptr++ )
		{
			if ( $string[$ptr] == '"' && $string[$ptr] != '\\' )
			{
				if ( $start == -1 )
				{
					$start = $ptr;
				}
				else
				{
					$token[$index++] = substr( $string, $start + 1, $ptr - $start - 1 );
					$found = true;
					$start = -1;
				}
			}
		}

		if ( isset( $token ) )
			return $token;
		else
			return false;
	}

	/**
	 * Parser for status responses.
	 *
	 * This should probably be replaced by a smarter parser.
	 *
	 * @param  string $string Input that contains status responses.
	 * @access public
	 * @todo   remove this function and dependencies
	 */
	function status( $string )
	{
		switch ( substr( $string, 0, 2 ) )
		{
			case "NO":
				return SIEVE_F_NO;
				break;
			
			case "OK":
				return SIEVE_F_OK;
				break;
			
			default:
				switch ( $string[0] )
				{
					case "{":
						// do parse here for curly braces - maybe modify
						// parseForQuotes to handle any parse delimiter?
						return SIEVE_F_HEAD;
						break;
					
					default:
						return SIEVE_F_DATA;
						break;
				}
		}
	}
  
	/**
	 * Attemp to log in to the sieve server.
	 * 
	 * It will return false if it fails, true if all is well.  This also loads
	 * some arrays up with some handy information:
	 *
	 * capabilities["implementation"] contains the sieve version information
	 * 
	 * capabilities["auth"] contains the supported authentication modes by the
	 * SIEVE server.
	 * 
	 * capabilities["modules"] contains the built in modules like "reject",
	 * "redirect", etc.
	 * 
	 * capabilities["starttls"] , if is set and equal to true, will show that the
	 * server supports the STARTTLS extension.
	 * 
	 * capabilities["unknown"] contains miscellaneous/extraneous header info sieve
	 * may have sent
	 *
	 * @access public
	 * @return boolean
	 * @throws Error
	 */
	function login()
	{
    	$this->fp = fsockopen( $this->host, $this->port );
		
		if ( $this->fp == false )
			return false;
 
		$this->line = fgets( $this->fp, 1024 );

		// Hack for older versions of Sieve Server.  They do not respond with the Cyrus v2. standard
		// response.  They repsond as follows: "Cyrus timsieved v1.0.0" "SASL={PLAIN,........}"
		// So, if we see IMLEMENTATION in the first line, then we are done.
		if ( ereg( "IMPLEMENTATION", $this->line ) )
    	{
      		// we're on the Cyrus V2 sieve server
			while ( Sieve::status( $this->line ) == SIEVE_F_DATA )
			{
				$this->item = Sieve::parseForQuotes( $this->line );

				if ( strcmp( $this->item[0], "IMPLEMENTATION" ) == 0 )
				{
					$this->capabilities["implementation"] = $this->item[1];
				}
				else if ( strcmp( $this->item[0], "SIEVE" ) == 0 || strcmp( $this->item[0], "SASL" ) == 0 )
				{
					if ( strcmp( $this->item[0], "SIEVE" ) == 0 )
						$this->cap_type = "modules";
					else
						$this->cap_type = "auth";            

					$this->modules = split( " ", $this->item[1] );
					
					if ( is_array( $this->modules ) )
					{
						foreach ( $this->modules as $this->module )
							$this->capabilities[$this->cap_type][$this->module] = true;
					}
					else if ( is_string( $this->modules ) )
					{
						$this->capabilites[$this->cap_type][$this->modules] = true;
					}
				}    
				else if ( strcmp( $this->item[0], "STARTTLS" ) == 0 )
				{
					$this->capabilities['starttls'] = true;
				}
				else
				{ 
					$this->capabilities["unknown"][] = $this->line;
				}    
				
				$this->line = fgets( $this->fp, 1024 );
			}
		}
		else
		{
			// we're on the older Cyrus V1. server  
			// this version does not support module reporting. We only have auth types.
			$this->cap_type = "auth";
       
			// break apart at the "Cyrus timsieve...." "SASL={......}"
			$this->item = Sieve::parseForQuotes( $this->line );

			$this->capabilities["implementation"] = $this->item[0];

			// we should have "SASL={..........}" now.  Break out the {xx,yyy,zzzz}
			$this->modules = substr( $this->item[1], strpos( $this->item[1], "{" ), strlen( $this->item[1] ) - 1 );

			// then split again at the ", " stuff.
			$this->modules = split( $this->modules, ", " );
 
			// fill up our $this->modules property
			if ( is_array( $this->modules ) )
			{
				foreach ( $this->modules as $this->module )
					$this->capabilities[$this->cap_type][$this->module] = true;
			}
			else if ( is_string( $this->modules ) )
			{
				$this->capabilites[$this->cap_type][$this->module] = true;
			}
    	}

		// here we should do some returning of error codes?
		if ( Sieve::status( $this->line ) == SIEVE_F_NO )
			return PEAR::raiseError( "Server not allowing connections.", SIEVE_EC_UNKNOWN );

		/* decision login to decide what type of authentication to use... */

		/* Loop through each allowed authentication type and see if the server allows the type */
		foreach ( explode( " ", $this->auth_types ) as $auth_type )
		{
			if ( $this->capabilities["auth"][$auth_type] )
        	{
            	/* We found an auth type that is allowed. */
            	$this->auth_in_use = $auth_type;
        	}
     	}

		return Sieve::authenticate();
	}

	/**
	 * Log out of the sieve server.
	 *
	 * @return boolean Always returns true at this point
	 * @access public
	 */
	function logout()
	{
    	if ( $this->loggedin == false )
			return false;

		fputs( $this->fp, "LOGOUT\r\n" );
		fclose( $this->fp );
		$this->loggedin = false;
		
		return true;
	}

	/**
	 * Send the script contained in $script to the server.
	 *
	 * @param  $scriptname string The name of the SIEVE script.
	 * @param  $script The script to be uploaded.
	 * @return mixed Returns true if script has been successfully uploaded.
	 * @access public
	 */
	function sendScript( $scriptname, $script ) 
	{
    	if ( $this->loggedin == false )
			return false;
		
		$this->script = stripslashes( $script );
		$len = strlen( $this->script );
		fputs( $this->fp, "PUTSCRIPT \"$scriptname\" \{$len+}\r\n" );
		fputs( $this->fp, "$this->script\r\n" );
  
		return Sieve::getResponse();
	}  
  
	/**
	 * Check if there is enough space for a script to be uploaded. 
	 *
	 * This function returns true or false based on whether the sieve server will
	 * allow your script to be sent and your quota has not been exceeded.  This
	 * function does not currently work due to a believed bug in timsieved.  It
	 * could be my code too.
	 *
	 * It appears the timsieved does not honor the NUMBER type.  see lex.c in
	 * timsieved src.  don't expect this function to work yet.  I might have
	 * messed something up here, too.
	 *
	 * @param  $scriptname string The name of the SIEVE script.
	 * @param  $scriptsize integer The size of the SIEVE script.
	 * @return boolean
	 * @access public
	 * @todo Does not work; bug fix and test.
	 */
	function haveSpace( $scriptname, $scriptsize )
	{
		if ( $this->loggedin == false )
			return false;
		
		fputs( $this->fp, "HAVESPACE \"$scriptname\" $scriptsize\r\n" );
		return Sieve::getResponse();
	}  

	/**
	 * Set the script active on the sieve server.
	 *
	 * @param  $scriptname string The name of the SIEVE script.
	 * @access public
	 * @return boolean
	 */
	function setScriptActive( $scriptname )
	{
		if ( $this->loggedin == false )
			return false;

		fputs( $this->fp, "SETACTIVE \"$scriptname\"\r\n" );   
		return Sieve::getResponse();
	}
  
	/**
	 * Return the contents of the requested script.
	 * 
	 * If you want to display the script, you will need to change all CrLf to
	 * '.'.
	 *
	 * @param  $scriptname string The name of the SIEVE script.
	 * @access public
	 * @return arr SIEVE script data.
	 */
	function getScript( $scriptname )
	{
		unset( $this->script );
		
		if ( $this->loggedin == false )
			return false;

		fputs( $this->fp, "GETSCRIPT \"$scriptname\"\r\n" );
		return Sieve::getResponse();
	}

	/**
	 * Attempt to delete the script requested.
	 *
	 * If the script is currently active, the server will not have any active
	 * script after the deletion.
	 *
	 * @param  $scriptname string The name of the SIEVE script.
	 * @access public
	 * @return mixed
	 */
	function deleteScript( $scriptname )
	{
		if ( $this->loggedin == false )
			return false;

		fputs( $this->fp, "DELETESCRIPT \"$scriptname\"\r\n" );
		return Sieve::getResponse();
	}
  
	/**
	 * List available scripts on the SIEVE server.
	 *
	 * This function returns true or false. $sieve->response will be filled
	 * with the names of the scripts found. If a script is active, the
	 * $sieve->response["ACTIVE"] will contain the name of the active script.
	 *
	 * @access public
	 * @return boolean
	 * @throws Error
	 */
	function listScripts()
	{ 
		fputs( $this->fp, "LISTSCRIPTS\r\n" ); 
		$res = Sieve::getResponse();
		
		if ( PEAR::isError( $res ) )
			return $res;
			
		if ( isset( $this->found_script ) && $this->found_script )
			return true;
		else
			return PEAR::raiseError( "No scripts found for this account.", SIEVE_EC_NOSCRIPTS );
	}

	/**
	 * Check availability of connection to the SIEVE server.
	 *
	 * This function returns true or false based on whether the connection to the
	 * sieve server is still alive.
	 *
	 * @access public
	 * @return boolean
	 * @throws Error
	 */
	function alive()
	{
		if ( ( !isset( $this->fp ) || $this->fp == 0 ) || feof( $this->fp ) )
			return PEAR::raiseError( "You are not logged in.", SIEVE_EC_NOT_LOGGED_IN );
		else
			return true;
	}

	/**
	 * Perform SASL authentication to SIEVE server.
	 *
	 * Attempts to authenticate to SIEVE, using some SASL authentication method
	 * such as PLAIN or DIGEST-MD5.
	 *
	 * @access public
	 */
	function authenticate()
	{
		switch ( $this->auth_in_use )
		{
			case "PLAIN":
				$auth = base64_encode( "$this->auth\0$this->user\0$this->pass" );
				$this->len = strlen( $auth );			
				fputs( $this->fp, "AUTHENTICATE \"PLAIN\" \{$this->len+}\r\n" );
				fputs( $this->fp, "$auth\r\n" );
				$this->line = fgets( $this->fp, 1024 );
						
				while ( Sieve::status( $this->line ) == SIEVE_F_DATA )
					$this->line = fgets( $this->fp, 1024 );

				if ( Sieve::status( $this->line ) == SIEVE_F_NO )
					return false;
				
				$this->loggedin = true;
				return true;    
				
				break;
	
			case "DIGEST-MD5":
				// SASL DIGEST-MD5 support works with timsieved 1.1.0
				// follows rfc2831 for generating the $response to $challenge
				fputs( $this->fp, "AUTHENTICATE \"DIGEST-MD5\"\r\n" );
				
				// $clen is length of server challenge, we ignore it. 
				$clen = fgets( $this->fp, 1024 );
				
				// read for 2048, rfc2831 max length allowed
				$challenge = fgets( $this->fp, 2048 );
				
				// vars used when building $response_value and $response
				$cnonce = base64_encode( bin2hex( hmac_md5( microtime() ) ) );
				$ncount = "00000001";
				$qop_value = "auth"; 
				$digest_uri_value = "sieve/$this->host";
				
				// decode the challenge string
				$result = decode_challenge( $challenge );
				
				// verify server supports qop=auth 
				$qop = explode( ",", $result['qop'] );
				
				if ( !in_array( $qop_value, $qop ) ) 
				{
					// rfc2831: client MUST fail if no qop methods supported
					return false;
				}
				
				// build the $response_value
				$string_a1  = utf8_encode( $this->user ) . ":";
				$string_a1 .= utf8_encode( $result['realm'] ) . ":";
				$string_a1 .= utf8_encode( $this->pass );
				$string_a1  = hmac_md5( $string_a1 );
				
				$A1 = $string_a1 . ":" . $result['nonce'] . ":" . $cnonce . ":" . utf8_encode( $this->auth );
				$A1 = bin2hex( hmac_md5( $A1 ) );
				$A2 = bin2hex( hmac_md5( "AUTHENTICATE:$digest_uri_value" ) );
				$string_response = $result['nonce'] . ":" . $ncount . ":" . $cnonce . ":" . $qop_value;
				$response_value  = bin2hex( hmac_md5( $A1 . ":" . $string_response . ":" . $A2 ) );
				
				// build the challenge $response
				$reply  = "charset=utf-8,username=\"".$this->user."\",realm=\"".$result['realm']."\",";
				$reply .= "nonce=\"".$result['nonce']."\",nc=$ncount,cnonce=\"$cnonce\",";
				$reply .= "digest-uri=\"$digest_uri_value\",response=$response_value,";
				$reply .= "qop=$qop_value,authzid=\"".utf8_encode($this->auth)."\"";
				
				$response = base64_encode( $reply );
				fputs( $this->fp, "\"$response\"\r\n" );
 	
				$this->line = fgets( $this->fp, 1024 );
				while ( Sieve::status( $this->line ) == SIEVE_F_DATA )
					$this->line = fgets( $this->fp,1024 );

				if ( Sieve::status( $this->line ) == SIEVE_F_NO )
					return false;
				
				$this->loggedin = true;
				return true;    
				
				break;
	
			case "CRAM-MD5":
				// SASL CRAM-MD5 support works with timsieved 1.1.0
				// follows rfc2195 for generating the $response to $challenge
				// CRAM-MD5 does not support proxy of $auth by $user
				// requires php mhash extension
				fputs( $this->fp, "AUTHENTICATE \"CRAM-MD5\"\r\n" );
				
				// $clen is the length of the challenge line the server gives us
				$clen = fgets( $this->fp, 1024 );
				
				// read for 1024, should be long enough?
				$challenge = fgets( $this->fp, 1024 );
				
				// build a response to the challenge
				$hash = bin2hex( hmac_md5( base64_decode( $challenge ), $this->pass ) );
				$response = base64_encode( $this->user . " " . $hash );
				
				// respond to the challenge string
				fputs( $this->fp, "\"$response\"\r\n" );
	     
				$this->line = fgets( $this->fp, 1024 );		
				while ( Sieve::status( $this->line ) == SIEVE_F_DATA )
					$this->line = fgets( $this->fp, 1024 );

				if ( Sieve::status( $this->line ) == SIEVE_F_NO )
					return false;
				
				$this->loggedin = true;
				return true;    
				
				break;

			case "LOGIN":
				$login = base64_encode( $this->user );
				$pass  = base64_encode( $this->pass );
 	
				fputs( $this->fp, "AUTHENTICATE \"LOGIN\"\r\n" );
				fputs( $this->fp, "{" . strlen( $login ) . "+}\r\n" );
				fputs( $this->fp, "$login\r\n" );
				fputs( $this->fp, "{" . strlen( $pass ) . "+}\r\n" );
				fputs( $this->fp, "$pass\r\n" );
 
				$this->line = fgets( $this->fp, 1024 );
				while ( Sieve::status( $this->line ) == SIEVE_F_HEAD || Sieve::status( $this->line ) == SIEVE_F_DATA )
					$this->line = fgets( $this->fp, 1024 );
 	
				if ( Sieve::status( $this->line ) == SIEVE_F_NO )
					return false;
				
				$this->loggedin = true;
				return true;
				
				break;

			default:
				return false;
				break;
		}
	}

	/**
	 * Return an array of available capabilities.
	 *
	 * @access public
	 * @return array
	 */
	function getCapability()
	{
		if ( $this->loggedin == false )
			return false;
		
		fputs( $this->fp, "CAPABILITY\r\n" ); 
		$this->line = fgets( $this->fp, 1024 );

		// Hack for older versions of Sieve Server.  They do not respond with the Cyrus v2. standard
		// response.  They repsond as follows: "Cyrus timsieved v1.0.0" "SASL={PLAIN,........}"
		// So, if we see IMLEMENTATION in the first line, then we are done.

		if ( ereg( "IMPLEMENTATION", $this->line ) )
		{
			// we're on the Cyrus V2 sieve server
			while ( Sieve::status( $this->line ) == SIEVE_F_DATA )
			{
				$this->item = Sieve::parseForQuotes( $this->line );

				if ( strcmp( $this->item[0], "IMPLEMENTATION" ) == 0 )
				{
					$this->capabilities["implementation"] = $this->item[1];
				}
				else if ( strcmp( $this->item[0], "SIEVE" ) == 0 || strcmp( $this->item[0], "SASL" ) == 0 )
				{
					if ( strcmp( $this->item[0], "SIEVE" ) == 0 )
						$this->cap_type = "modules";
					else
						$this->cap_type = "auth";            

					$this->modules = split( " ", $this->item[1] );
					
					if ( is_array( $this->modules ) )
					{
						foreach ( $this->modules as $this->module )
							$this->capabilities[$this->cap_type][$this->module] = true;
					}
					else if ( is_string( $this->modules ) )
					{
						$this->capabilites[$this->cap_type][$this->modules] = true;
					}
				}    
				else
				{ 
					$this->capabilities["unknown"][] = $this->line;
				}    
				
				$this->line = fgets( $this->fp, 1024 );
			}
		}
		else
		{
			// we're on the older Cyrus V1. server  
			// this version does not support module reporting. We only have auth types.
			$this->cap_type = "auth";
       
			// break apart at the "Cyrus timsieve...." "SASL={......}"
			$this->item = Sieve::parseForQuotes( $this->line );

			$this->capabilities["implementation"] = $this->item[0];

			// we should have "SASL={..........}" now.  Break out the {xx,yyy,zzzz}
			$this->modules = substr( $this->item[1], strpos( $this->item[1], "{"), strlen( $this->item[1] ) - 1 );

			// then split again at the ", " stuff.
			$this->modules = split( $this->modules, ", " );
 
			// fill up our $this->modules property
			if ( is_array( $this->modules ) )
			{
				foreach ( $this->modules as $this->module )
					$this->capabilities[$this->cap_type][$this->module] = true;
			}
			else if ( is_string( $this->modules ) )
			{
				$this->capabilites[$this->cap_type][$this->module] = true;
			}
		}

		return $this->modules;
	}
} // END OF Sieve


/**
 * Creates a HMAC digest that can be used for auth purposes.
 * See RFCs 2104, 2617, 2831
 * Uses mhash() extension if available
 *
 * Squirrelmail has this function in functions/auth.php, and it might have been
 * included already. However, it helps remove the dependancy on mhash.so PHP
 * extension, for some sites. If mhash.so _is_ available, it is used for its
 * speed.
 *
 * This function is Copyright (c) 1999-2003 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * @param  string $data Data to apply hash function to.
 * @param  string $key Optional key, which, if supplied, will be used to calculate data's HMAC.
 * @return string HMAC Digest string
 */
if ( !function_exists( 'hmac_md5' ) )
{
	function hmac_md5( $data, $key = '' )
	{
    	// See RFCs 2104, 2617, 2831
    	// Uses mhash() extension if available
    	if ( Util::extensionExists( 'mhash' ) ) 
		{
      		if ( $key == '' )
        		$mhash = mhash( MHASH_MD5, $data );
      		else
        		$mhash = mhash( MHASH_MD5, $data, $key );
      		
			return $mhash;
    	}
    
		if ( !$key )
			return pack( 'H*', md5( $data ) );
    
    	$key = str_pad( $key, 64, chr( 0x00 ) );
    
		if ( strlen( $key ) > 64 )
        	$key = pack( "H*", md5( $key ) );
    
		$k_ipad = $key ^ str_repeat( chr( 0x36 ), 64 );
		$k_opad = $key ^ str_repeat( chr( 0x5c ), 64 );
    	
		/* Heh, let's get recursive. */
    	$hmac = hmac_md5( $k_opad . pack( "H*", md5( $k_ipad . $data ) ) );
    	return $hmac;
	}
}

/**
 * A hack to decode the challenge from timsieved 1.1.0.
 * 
 * This function may not work with other versions and most certainly won't work
 * with other DIGEST-MD5 implentations
 *
 * @param $input string Challenge supplied by timsieved.
 */
function decode_challenge( $input ) 
{
    $input = base64_decode( $input );
    preg_match( "/nonce=\"(.*)\"/U", $input, $matches );
    $resp['nonce'] = $matches[1];
    preg_match( "/realm=\"(.*)\"/U", $input, $matches );
    $resp['realm'] = $matches[1];
    preg_match( "/qop=\"(.*)\"/U",   $input, $matches );
    $resp['qop'] = $matches[1];
	
    return $resp;
}

?>
