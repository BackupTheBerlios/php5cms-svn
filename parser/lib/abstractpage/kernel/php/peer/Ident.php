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
 * Ident default values
 */
define( 'IDENT_DEFAULT_TIMEOUT', 30 );
define( 'IDENT_DEFAULT_PORT',   113 );

/**
 * Ident object states
 */
define( 'IDENT_STATUS_UNDEF',     0 );
define( 'IDENT_STATUS_OK',        1 );
define( 'IDENT_STATUS_ERROR',     2 );


/**
 * Ident - Identification Protocol implementation according to RFC 1413
 *
 * The Identification Protocol (a.k.a., "ident", a.k.a., "the Ident Protocol")
 * provides a means to determine the identity of a user of a particular TCP
 * connection.  Given a TCP port number pair, it returns a character string
 * which identifies the owner of that connection on the server's system.
 *
 * You can find out more about the Ident protocol at
 *
 *      http://www.ietf.org/rfc/rfc1413.txt
 *
 * Usage:
 *
 * $ident   = new Ident;
 * $user    = $ident->getUser();
 * $os_type = $ident->getOsType();
 * echo "user: $user, operating system: $os_type\n";
 *
 * @package peer
 */
 
class Ident extends PEAR
{
    /**
     * Current object state (undef, ok, error)
     *
     * @var     enum
     * @access  private
     */
    var $_status;

    /**
     * Error message string;
     *   if $_status is "error", $_error contains system error message;
     *   if $_status is "ok", $_error contains ident error message
     * or it is empty
     *
     * @var     string
     * @access  private
     */
    var $_error;

    /**
     * Properties array (contains remote host, remote port, ident port, etc.)
     *
     * @var     array
     * @access  private
     */
    var $_props;

    /**
     * Data array (contains ident username, ident operating system type, and
     * raw line returned from ident server)
     *
     * @var     array
     * @access  private
     */
    var $_data;

	
    /**
     * Constructor
     *
     * Initializes class properties. Use empty string '' for any string
     * parameter and value of 0 for any int parameter to set default value.
     *
     * @param   string  $remote_addr    Remote host address (IP or hostname)
     * @param   int     $remote_port    Remote port (default $REMOTE_PORT)
     * @param   int     $local_port     Local port  (default $SERVER_PORT)
     * @param   int     $ident_port     Ident port  (default 113)
     * @param   int     $timeout        Socket timeout (default 30 seconds)
     * @return  none
     * @access  public
     */
    function Ident( $remote_addr = '', $remote_port = 0, $local_port  = 0, $ident_port  = 0, $timeout = 0 )
    {
        $this->_status = IDENT_STATUS_UNDEF;
		
        $this->setRemoteAddr( $remote_addr );
        $this->setRemotePort( $remote_port );
        $this->setLocalPort( $local_port );
        $this->setIdentPort( $ident_port );
        $this->setTimeout( $timeout );
    }

	
    /**
     * Sets remote host address (IP or hostname).
     *
     * @param   string  $remote_addr    Remote host address (IP or hostname)
     * @return  none
     * @access  public
     */
    function setRemoteAddr( $remote_addr )
    {
        strlen( $remote_addr ) <= 0 && $remote_addr = $_SERVER['REMOTE_ADDR'];
        $this->_props['remote_addr'] = $remote_addr;
    }

    /**
     * Sets remote port.
     *
     * @param   int     $remote_port    Remote port (default $REMOTE_PORT)
     * @return  none
     * @access  public
     */
    function setRemotePort( $remote_port )
    {
        $remote_port = intval( $remote_port );
        $remote_port <= 0 && $remote_port = $_SERVER['REMOTE_PORT'];
        $this->_props['remote_port'] = $remote_port;
    }

    /**
     * Sets local port.
     *
     * @param   int     $local_port     Local port  (default $SERVER_PORT)
     * @return  none
     * @access  public
     */
    function setLocalPort( $local_port )
    {
        $local_port = intval( $local_port );
        $local_port <= 0 && $local_port = $_SERVER['SERVER_PORT'];
        $this->_props['local_port'] = $local_port;
    }

    /**
     * Sets ident port.
     *
     * @param   int     $ident_port     Ident port  (default 113)
     * @return  none
     * @access  public
     */
    function setIdentPort( $ident_port )
    {
        $ident_port = intval( $ident_port );
        $ident_port <= 0 && $ident_port = IDENT_DEFAULT_PORT;
        $this->_props['ident_port'] = $ident_port;
    }

    /**
     * Sets socket timeout.
     *
     * @param   int     $timeout        Socket timeout (default 30 seconds)
     * @return  none
     * @access  public
     */
    function setTimeout( $timeout )
    {
        $timeout = intval($timeout);
        $timeout <= 0 && $timeout = IDENT_DEFAULT_TIMEOUT;
        $this->_props['timeout'] = $timeout;
    }

    /**
     * Performs network socket ident query.
     *
     * @return  mixed   Error on connection error
     *                  rawdata read from socket on success
     * @access  public
     */
    function query()
    {
        // query forced, clean current result
        if ( $this->_status == IDENT_STATUS_OK ) 
		{
            unset( $this->_data['username'] );
            unset( $this->_data['os_type']  );
			
            $this->_status = IDENT_STATUS_UNDEF;
        }
		
        while ( 1 ) 
		{
            if ( $this->_status == IDENT_STATUS_ERROR )
                return PEAR::raiseError( $this->_error );

            if ( $socket = @fsockopen(
				$this->_props['remote_addr'],
				$this->_props['ident_port'],
				$errno, 
				$errstr,
				$this->_props['timeout'] ) )
			{
                break;
            }
			
            $this->_status = IDENT_STATUS_ERROR;
            $this->_error  = 'Error connecting to ident server (' . $this->_props['remote_addr'] . ':' . $this->_props['ident_port'] . "): $errstr ($errno)";
        }

        $line = $this->_props['remote_port'] . ',' . $this->_props['local_port'] . "\r\n";
        @fwrite( $socket, $line );
        $line = @fgets( $socket, 1000 ); // 1000 octets according to RFC 1413
        fclose( $socket );

        $this->_status = IDENT_STATUS_OK;
        $this->_data['rawdata'] = $line;
        $this->_parseIdentReponse( $line );

        return $line;
    }

    /**
     * Returns ident username.
     *
     * @return  mixed   Error on connection error
     *                  false boolean on ident protocol error
     *                  username string on success
     * @access  public
     */
    function getUser()
    {
        $this->_status == IDENT_STATUS_UNDEF && $this->query();
        
		if ( $this->_status == IDENT_STATUS_ERROR )
            return PEAR::raiseError( $this->_error );
        
        return $this->_data['username'];
    }

    /**
     * Returns ident operating system type.
     *
     * @return  mixed   Error on connection error
     *                  false boolean on ident protocol error
     *                  operating system type string on success
     * @access  public
     */
    function getOsType()
    {
        $this->_status == IDENT_STATUS_UNDEF && $this->query();
        
		if ( $this->_status == IDENT_STATUS_ERROR )
            return PEAR::raiseError( $this->_error );
        
        return $this->_data['os_type'];
    }

    /**
     * Returns ident protocol error.
     *
     * @return  mixed   error string if ident protocol error had occured
     *                  false otherwise
     * @access  public
     */
    function identError()
    {
        if ( $this->_status == IDENT_STATUS_OK && isset( $this->_error ) )
            return $this->_error;
        
        return false;
    }

    /**
     * Parses response from indent server and sets internal data structures
     * with ident username and ident operating system type.
     *
     * @param   string  $string     ident server response
     * @return  boolean true if no ident protocol error had occured
     *                  false otherwise
     * @access  private
     */
    function _parseIdentReponse( $string )
    {
        list( , $response )           = explode( ':', $string, 2 );
        list( $resp_type, $add_info ) = explode( ':', trim( $response ), 2 );
		
        if ( trim( $resp_type ) == 'USERID' ) 
		{
            list( $os_type, $username ) = explode( ':', trim( $add_info ), 2 );
            $this->_data['username']    = trim( $username );
            $this->_data['os_type']     = trim( $os_type  );
            
			return true;
        } 
		else if ( trim( $resp_type ) == 'ERROR' ) 
		{
            $this->_error = trim( $add_info );
        } 
		else 
		{
            $this->_error = 'Invalid ident server response.';
        }
		
        $this->_data['username'] = false;
        $this->_data['os_type']  = false;
        
		return false;
    }
} // END OF Ident

?>
