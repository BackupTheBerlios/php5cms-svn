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
 * The NNTP:: class fetches UseNet news articles acording to the standard
 * based on RFC 1036.
 *
 * @package peer_nntp
 */

class NNTP extends PEAR
{
	/**
	 * @access public
	 */
    var $max = '';
	
	/**
	 * @access public
	 */
    var $min = '';
	
	/**
	 * @access public
	 */
	var $nntp_mask = '';
	
	/**
	 * @access public
	 */
    var $user = null;
	
	/**
	 * @access public
	 */
    var $pass = null;
	
	/**
	 * @access public
	 */
    var $authmode = null;

    /**
	 * File pointer of the nntp-connection.
	 * @access public
	 */
    var $fp = null;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function NNTP()
	{
		$this->nntp_mask = ap_ini_get( "agent_name", "settings" );
	}

	
    /**
     * Connect to the newsserver.
     *
     * @param string $nntpserver The adress of the NNTP-server to connect to.
     * @param int $port (optional) the port-number to connect to, defaults to 119.
     * @param string $user (optional) The user name to authenticate with
     * @param string $pass (optional) The password
     * @param string $authmode (optional) The authentication mode
     * @return mixed True on success or Pear Error object on failure
     * @see NNTP::authenticate()
     * @access public
     */
	function connect( $nntpserver, $port = 119, $user = null, $pass = null, $authmode = 'original' )
	{
		$fp = @fsockopen( $nntpserver, $port, $errno, $errstr, 15 );
		
		if ( !is_resource( $fp ) )
			return PEAR::raiseError( "Could not connect to NNTP-server " . $nntpserver );

		socket_set_blocking( $fp, true );
        
		if ( !$fp )
			return PEAR::raiseError( "Not connected." );

		$response = fgets( $fp, 128 );

        $this->fp       = $fp;
        $this->user     = $user;
        $this->pass     = $pass;
        $this->authmode = $authmode;

        return true;
    }

    /**
     * Connect to the newsserver, and issue a GROUP command.
     * Once connection is prepared, we can only fetch articles from one group
     * at a time, to fetch from another group, a new connection has to be made.
     *
     * This is to avoid the GROUP command for every article, as it is very
     * ressource intensive on the newsserver especially when used for
     * groups with many articles.
     *
     * @param string $nntpserver The adress of the NNTP-server to connect to.
     * @param int $port (optional) the port-number to connect to, defaults to 119.
     * @param string $newsgroup The name of the newsgroup to use.
     * @param string $user (optional) The user name to authenticate with
     * @param string $pass (optional) The password
     * @param string $authmode (optional) The authentication mode
     * @return mixed True on success or Error object on failure
     * @see NNTP::authenticate()
     * @access public
     */
	function prepare_connection( $nntpserver, $port = 119, $newsgroup, $user = null, $pass = null, $authmode = 'original' )
    {
		$err = $this->connect( $nntpserver, $port, $user, $pass, $authmode );
		
		if ( PEAR::isError( $err ) )
			return $err;
		
        // issue a GROUP command
        $r = $this->command( "GROUP $newsgroup" );

        if ( !$r || PEAR::isError( $r ) || $this->responseCode( $r ) > 299)
			return PEAR::raiseError( "Wrong response from NNTP-Server." );
        
        $response_arr = split(' ', $r);
        $this->max = $response_arr[3];
        $this->min = $response_arr[2];

        return true;
    }

    /**
     * Auth process (not yet standarized but used any way)
     * http://www.mibsoftware.com/userkt/nntpext/index.html
     *
     * @param string $user The user name
     * @param string $pass (optional) The password if needed
     * @param string $mode Authinfo form: original, simple, generic
     * @return mixed (bool) true on success or Error obj on fail
     */
    function authenticate( $user = null, $pass = null, $mode = 'original' )
    {
        if ( $user === null )
			return PEAR::raiseError( "Authorization required but not supplied." );
		
        switch ( $mode )
		{
            case 'original':
                /*
				281 Authentication accepted
				381 More authentication information required
				480 Authentication required
				482 Authentication rejected
				502 No permission
                */
				
                $response = $this->command( "AUTHINFO user $user", false );
				
                if ( $this->responseCode( $response ) != 281 )
				{
                    if ( $this->responseCode( $response ) == 381 && $pass !== null )
						$response = $this->command( "AUTHINFO pass $pass", false );
                }
				
                if ( $this->responseCode( $response ) != 281 )
					return PEAR::raiseError( "Authentication failed: " . $response );
				
                return true;
                break;
				
            case 'simple':
			
            case 'generic':
			
            default:
				return PEAR::raiseError( "Auth mode not implemented: " . $mode );
        }
    }

    /**
     * Get an article from the currently open connection.
     * To get articles from another newsgroup a new prepare_connection() -
     * call has to be made with apropriate parameters
     *
     * @param mixed $article Either the message-id or the message-number on the server of the article to fetch
     * @access public
     */
    function get_article( $article )
    {
        // tell the newsserver we want an article
        $r = $this->command( "ARTICLE $article" );
        
		if ( !$r || PEAR::isError( $r ) || $this->responseCode( $r ) > 299 )
			return false;

        $post = null;
        while ( !feof( $this->fp ) )
		{
            $line = trim( fgets( $this->fp, 256 ) );

            if ( $line == "." )
                break;
			else
                $post .= $line ."\n";
        }
		
        return $post;
    }

    /**
     * Post an article to a newsgroup.
     * Among the aditional headers you might think of adding could be:
     * "NNTP-Posting-Host: <ip-of-author>", which should contain the IP-adress
     * of the author of the post, so the message can be traced back to him.
     * Or "Organization: <org>" which contain the name of the organization
     * the post originates from.
     *
     * @param string $subject The subject of the post.
     * @param string $newsgroup The newsgroup to post to.
     * @param string $from Name + email-adress of sender.
     * @param string $body The body of the post itself.
     * @param string $aditionak (optional) Aditional headers to send.
     * @access public
     */
    function post( $subject, $newsgroup, $from, $body, $aditional = "" )
    {
        if ( !@is_resource( $this->fp ) )
			return PEAR::raiseError( "Not connected." );

        // tell the newsserver we want to post an article
        fputs( $this->fp, "POST\n" );

        // The servers' response
        $response = trim( fgets( $this->fp, 128 ) );

        fputs( $this->fp, "From: $from\n" );
        fputs( $this->fp, "Newsgroups: $newsgroup\n" );
        fputs( $this->fp, "Subject: $subject\n" );
        fputs( $this->fp, "X-poster: " . $this->nntp_mask );
        fputs( $this->fp, "$aditional\n" );
        fputs( $this->fp, "\n$body\n.\n" );

        // The servers' response
        $response = trim( fgets( $this->fp, 128 ) );

        return $response;
    }


    /**
     * Get the headers of an article from the currently open connection.
     * To get the headers of an article from another newsgroup, a new
     * prepare_connection()-call has to be made with apropriate parameters
     *
     * @param string $article Either a message-id or a message-number of the article to fetch the headers from.
     * @access public
     */
    function get_headers( $article )
    {
        // tell the newsserver we want an article
        $r = $this->command( "HEAD $article" );
		
        if ( !$r || PEAR::isError( $r ) || $this->responseCode( $r ) > 299 )
            return false;

        $headers = '';
        while( !feof( $this->fp ) )
		{
            $line = trim( fgets( $this->fp, 256 ) );

            if ( $line == '.' )
                break;
			else
                $headers .= $line . "\n";
        }
		
        return $headers;
    }


    /**
     * Returns the headers of a given article in the form of
     * an associative array. Ex:
     * array(
     *   'From'      => 'foo@bar.com (Foo Smith)',
     *   'Subject'   => 'Re: Using NNTP class',
     *   ....
     *   );
     *
     * @param $article string Article number or id
     * @return array Assoc array with headers names as key
     */
    function split_headers( $article )
    {
        $headers = $this->get_headers( $article );
		
		if ( !$headers )
			return false;

        $lines = explode( "\n", $headers );
        foreach ( $lines as $line )
		{
            $line = trim( $line );
			
            if ( ( $pos = strpos( $line, ':' ) ) !== false )
			{
                $head = substr( $line, 0, $pos );
                $ret[$head] = ltrim(substr( $line, $pos + 1 ) );
				
				// if the field was longer than 256 chars, look also in the next line
				// XXX a better way to discover that than strpos?
			}
			else
			{
                $ret[$head] .= $line;
            }
        }
		
        if ( isset( $ret['References'] ) )
            $ret['References'] = explode( ' ', $ret['References'] );
			
        return $ret;
    }

    /**
     * Get the body of an article from the currently open connection.
     * To get the body of an article from another newsgroup, a new
     * prepare_connection()-call has to be made with apropriate parameters
     *
     * @param string $article Either a message-id or a message-number of the article to fetch the headers from.
     * @access public
     */
    function get_body( $article )
    {
        // tell the newsserver we want an article
        $r = $this->command( "BODY $article" );
		
        if ( !$r || PEAR::isError( $r ) || $this->responseCode( $r ) > 299 )
            return false;

        $body = null;
        while( !feof( $this->fp ) )
		{
            $line = trim( fgets( $this->fp, 256 ) );

            if ( $line == '.' )
                break;
			else
                $body .= $line ."\n";
        }
		
        return $body;
    }

    /**
     * Selects a news group (issue a GROUP command to the server).
	 *
     * @param string $newsgroup The newsgroup name
     * @return mixed True on success or Pear Error object on failure
     */
    function select_group( $newsgroup )
    {
        $r = $this->command( "GROUP $newsgroup" );
		
        if ( !$r || PEAR::isError( $r ) || $this->responseCode( $r ) > 299)
            return false;
        
        $response_arr = split( ' ', $r );
        $this->max = $response_arr[3];
        $this->min = $response_arr[2];

        return true;
    }

    /**
     * Get the date from the newsserver.
     *
     * @access public
     */
    function date()
    {
        $r = $this->command( 'DATE' );
		
        if ( !$r || PEAR::isError( $r ) || $this->responseCode( $r ) > 299 )
            return false;
        
        return true;
    }

    /**
     * Maximum article number in current group.
     *
     * @access public
     */
    function max()
    {
        if ( !@is_resource( $this->fp ) )
			return PEAR::raiseError( "Not connected." );
		
        return $this->max;
    }

    /**
     * Minimum article number in current group.
     *
     * @access public
     */
    function min()
    {
        if ( !@is_resource( $this->fp ) )
			return PEAR::raiseError( "Not connected." );
		
        return $this->min;
    }

    /**
     * Test whether we are connected or not.
     *
     * @return bool true or false
     * @access public
     */
    function is_connected()
    {
        if ( @is_resource( $this->fp ) )
            return true;
        
        return false;
    }

    /**
     * Close connection to the newsserver.
     *
     * @access public
     */
    function quit()
    {
        $this->command( "QUIT" );
        fclose( $this->fp );
    }

    /**
     * Get response code.
     *
     * @access public
     */
    function responseCode( $response )
    {
        $parts = explode( ' ', ltrim( $response ) );
        return (int)$parts[0];
    }

    /**
     * Issue a command to the NNTP server.
	 *
     * @param string $cmd The command to launch, ie: "ARTICLE 1004853"
     * @param bool $testauth Test or not the auth
     * @return mixed True on success or Pear Error object on failure
     */
    function command( $cmd, $testauth = true )
    {
        if ( !@is_resource( $this->fp ) )
			return PEAR::raiseError( "Not connected." );
		
        fputs( $this->fp, "$cmd\r\n" );
        $response = fgets( $this->fp, 128 );

        // From the spec: "In all cases, clients must provide
        // this information when requested by the server. Servers are
        // not required to accept authentication information that is
        // volunteered by the client"
        $code = $this->responseCode( $response );
		
        if ( $testauth && ( $code == 450 || $code == 480 ) )
		{
            $a = $this->authenticate( $this->user, $this->pass, $this->authmode );
			
			if ( PEAR::isError( $a ) )
				return $a;

            // re-issue the command
            $response = $this->command( $cmd, false );
        }
		
        return $response;
    }
} // END OF NNTP

?>
