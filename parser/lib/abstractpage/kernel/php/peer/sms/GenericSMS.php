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
 * Class to send SMS (Short Message Service) Message to "ANY" mobile or SMS server.
 *
 * Requirements:
 * - You should have access to any SMS Server with a valid username and passsword
 *
 * @package peer_sms
 */

class GenericSMS extends PEAR 
{
	/**
	 * SMS server
	 * @access public
	 */
	var $smsHostname = "127.0.0.1";
	
	/**
	 * Port on which SMS server is running
	 * @access public
	 */
  	var $smsPort = "80";

	/**
	 * Username for logging into server
	 * @access public
	 */
  	var $smsUsername = "a";
	
	/**
	 * @access public
	 */
  	var $smsPassword = "a";

	/**
	 * @access private
	 */
  	var $_fp = "";

	/**
	 * Flag to indicate whether logged or not.
	 * @access private
	 */
  	var $_logged = false;
	
	/**
	 * Max length of a SMS Message
	 * @access private
	 */
  	var $_msgLength = 160;
	
	/**
	 * @access private
	 */
  	var $_error = 0;
	
	/**
	 * @access private
	 */
  	var $_errStr = "";
  

	/**
     * Connect to SMS Server via TELNET.
	 *
	 * @access public
	 */
  	function connect( $smshost = "", $smsport = "" )
	{
      	if ( !empty( $smshost ) )
			$this->smsHostname = $smshost;
			
      	if ( !empty( $smsport ) )
			$this->smsPort = $smsport;
      
	  	$fp = fsockopen( $this->smsHostname, $this->smsPort, &$errno, &$errstr );

      	$this->_error  = $errno;
      	$this->_errStr = $errstr;

      	$this->_fp = $fp;

      	if ( !$this->_fp )
           return false;
      
	  	return true;
	}
  
  	/**
	 * Login to SMS Server.
	 *
	 * @access public
	 */
  	function login( $username = "", $password = "" ) 
	{
      	if ( !$this->_fp )
          	return false;
      
      	if ( !empty( $username ) )
			$this->smsUsername = $username;
			
      	if ( !empty( $password ) )
			$this->smsPassword = $password;
      
	  	$var = fputs( $this->_fp, "LNRQ\$USER=" . $this->smsUsername . ",PW=" . $this->smsPassword . "\r\n" );
      
	  	if ( $var != -1 ) 
		{
       		$return = fgets( $this->_fp, 100 );
       
	   		if ( trim( $return ) == 'LNACK' ) 
           		$this->_logged = true;
			else 
          		$this->_logged = false;
      	} 
		else 
		{
         	$this->_error  = 2;
         	$this->_errStr = "Cannot Logon to server '" . $this->smsHostname . ":" . $this->smsPort . "'";
      	}
	  
      	return $this->_logged;
  	}

	/**
 	 * SEND SMS Message.
	 *
	 * @access public
	 */
  	function send( $smsnummer, $text, $org = "" ) 
	{
        if ( strlen( $text ) > $this->_msgLength )
          	$text = substr( $text, 0, $this->_msgLength );
        
        $var = fputs( $this->_fp, "SRQ\$DEST=$smsnummer,ORG=$org,BODY=$text\r\n" );
        flush();
        
		if ( $var != -1 ) 
		{
          	$return = fgets( $this->_fp, 100 );

          	if ( trim( $return ) == 'SACK' )
            	return true;
        }
		
 		return false;
	}
  
  	/**
  	 * Simply executes any command and returns its output.
	 *
	 * @access public
  	 */
  	function command( $cmd ) 
	{
     	$cmd = trim( $cmd );
        
		if ( !empty( $cmd ) ) 
		{
         	fputs( $this->_fp, "$smd\r\n" );
            $return = fgets( $this->_fp, 100 );
			
            return $return;
     	}
		
        return false;
  	}
} // END OF GenericSMS

?>
