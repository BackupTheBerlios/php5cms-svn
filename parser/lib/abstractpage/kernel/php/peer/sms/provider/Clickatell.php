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


/**
 * Simple CLICKATELL SMS API using CURL PHP module
 *
 * NOTE: CURL PHP module must have SSL support for maximum security of your
 * CLICKATELL account. If CURL is not built with SSL support, change $base_s to 
 * "http://api.clickatell.com/http" instead "https://api.clickatell.com/http". 
 *
 * For more information about CLICKATELL service visit http://www.clickatell.com
 *
 * Usage:
 *
 * $mysms = new Clickatell();
 * echo $mysms->send( "38160123", "docuverse", "TEST MESSAGE" );
 *
 * @package peer_sms_provider
 */

class Clickatell extends PEAR
{
	/**
	 * @access public
	 */
	var $api_id = "YOUR_CLICKATELL_API_NUMBER";
	
	/**
	 * @access public
	 */
	var $user = "YOUR_CLICKATELL_USERNAME";
	
	/**
	 * @access public
	 */
	var $password = "YOUR_CLICKATELL_PASSWORD";

	/**
	 * Gateway URL
	 *
	 * @access public
	 */
	var $base   = "http://api.clickatell.com/http";
	
	/**
	 * Gateway URl (secure)
	 *
	 * @access public
	 */
	var $base_s = "https://api.clickatell.com/http";

	/**
	 * Define SMS balance limit 
	 *
	 * @access public
	 */
	var $balace_linit = 20;

	/**
	 * Session variable 
	 *
	 * @access public
	 */
	var $session;


	/**
	 * Constructor
	 *
	 * @access public
	 */
    function Clickatell() 
	{
		if ( !Util::extensionExists( 'curl' ) ) 
		{
			$this = new PEAR_Error( "This SMS API class can not work without CURL PHP module." );
            return;
        }
		
		if ( PEAR::isError( $this->auth() ) ) 
		{
			$this = new PEAR_Error( "Authentication failed." );
            return;
        }	
    }

	
    /**
	 * SMS gateway authentication.
	 *
	 * @access public
	 */
    function auth()
	{
    	$comm = sprintf( "%s/auth?api_id=%s&user=%s&password=%s", $this->base_s, $this->api_id, $this->user, $this->password );
        $this->session = $this->_parseAuth( $this->_curl( $comm ) );
		
		return $this->session;
    }

    /**
	 * Query SMS credis balance.
	 *
	 * @access public
	 */
    function getBalance()
	{
    	$comm = sprintf( "%s/getbalance?session_id=%s", $this->base, $this->session );
        return $this->_parseGetBalance( $this->_curl( $comm ) );
    }

    /**
	 * Send SMS message.
	 *
	 * @access public
	 */
    function send( $to = null, $from = null, $text = null ) 
	{
    	/* Check SMS credits balance */
    	if ( $this->getBalance() < $this->balace_linit )
    	    return PEAR::raiseError( "You have reach the SMS credit limit!" );

    	/* Check SMS $text length */
        if ( strlen( $text ) > 160 )
    	    return PEAR::raiseError( "Your message is to long! (Current lenght=" . strlen( $text ) . ")");

    	/* Check $to and $from is not empty */
        if ( empty( $to ) )
    	    return PEAR::raiseError( "You not specify destination address (TO)!" );
    	
        if ( empty( $from ) )
    	    return PEAR::raiseError( "You not specify source address (FROM)!" );

    	/* Reformat $to number */
        $cleanup_chr = array( "+", " ", "(", ")", "\r", "\n", "\r\n" );
        $to = str_replace( $cleanup_chr, "", $to );

    	/* Send SMS now */
    	$comm = sprintf( "%s/sendmsg?session_id=%s&to=%s&from=%s&text=%s", $this->base, $this->session, rawurlencode( $to ), rawurlencode( $from ), rawurlencode( $text ) );
        return $this->_parseSend( $this->_curl( $comm ) );
    }


    // private methods

    /**
	 * Execute gateway commands.
	 *
	 * @access private
	 */
    function _curl( $command ) 
	{
        $ch = curl_init( $command );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        $result = curl_exec( $ch );
        curl_close( $ch );
		
        return $result;
    }

    /**
	 * Parse authentication command response text.
	 *
	 * @access private
	 */
    function _parseAuth( $result ) 
	{
    	$session = substr( $result, 4 );
        $code    = substr( $result, 0, 2 );
        
		if ( $code <> "OK" )
            return PEAR::raiseError( "Error in SMS authorization! ($result)" );
        
        return $session;
    }

    /**
	 * Parse send command response text.
	 *
	 * @access private
	 */
    function _parseSend( $result ) 
	{
    	$code = substr( $result, 0, 2 );
		
    	if ( $code <> "ID" )
    	    return PEAR::raiseError( "Error sending SMS! ($result)" );
    	else
    	    $code = "OK";
    	
        return $code;
    }

    /**
	 * Parse getbalance command response text.
	 *
	 * @access private
	 */
    function _parseGetBalance( $result )
	{
    	$result = substr( $result, 8 );
        return (int)$result;
    }
} // END OF Clickatell

?>
