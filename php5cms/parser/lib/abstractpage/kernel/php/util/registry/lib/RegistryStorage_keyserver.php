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

 
using( 'peer.Socket' );
using( 'util.registry.lib.RegistryStorage' );


/**
 * Remote Storage client implementation of key/vales pairs
 * The server component has the following syntax:
 * 
 * <pre>
 * GET <key>                  Gets a key
 * SET <key>=<value>          Inserts/Updates a key
 * DELE <key>                 Deletes a key
 * KEYS                       Returns all keys
 * </pre>
 *
 * Not implemented:
 * <pre>
 * SAVE                       Saves to disk
 * </pre>
 *
 * @package util_registry
 */

class RegistryStorage_keyserver extends RegistryStorage
{
	/**
	 * @access private
	 */
    var $_sock = null;


	/**
	 * Constructor
	 *
	 * @access  public
	 */
	function RegistryStorage_keyserver( $options = array() )
	{
		$this->RegistryStorage( $options );
	}
	
	
    /**
     * Initialize this storage.
     *
     * @access  public
     * @param   string host Hostname or IP
     * @param   int port default 6100 Port
     * @throws  Error
     */
    function initialize( $host, $port = 6100 ) 
	{
      	$this->_sock = &new Socket;
		$this->_sock->setHost( $host );
		$this->_sock->setPort( $port );
		
      	return $this->_sock->open();
    }

    /**
     * Returns whether this storage contains the given key.
     *
     * @access  public
     * @param   string key
     * @return  bool true when this key exists
     */
    function contains( $key ) 
	{
      	return $this->_cmd( 'GET %s/%s', urlencode( $this->id ), urlencode( $key ) ) !== false;
    }

    /**
     * Get all keys.
     *
     * @access  public
     * @return  string[] key
     */
    function keys()
	{ 
      	if ( ( $ret = $this->_cmd( 'KEYS' ) ) === false ) 
			return false;
      
	  	return explode( '|', $ret );
    }

    /**
     * Get a key by it's name.
     *
     * @access  public
     * @param   string key
     * @return  &mixed
     */
    function &get($key)
	{
      	if ( ( $return = $this->_cmd( 'GET %s/%s', urlencode( $this->id ), urlencode( $key ) ) ) === false )
        	return PEAR::raiseError( $key . ' does not exist.' );
      
      	return unserialize( urldecode( $return ) );
    }

    /**
     * Insert/update a key.
     *
     * @access  public 
     * @param   string key
     * @param   &mixed value
     * @param   int permissions default 0666
     */
    function put( $key, &$value, $permissions = 0666 ) 
	{
      	if ( $this->_cmd( 'SET %s/%s=%s', urlencode( $this->id ), urlencode( $key ), urlencode( serialize( $value ) ) ) === false ) 
        	return PEAR::raiseError( $key . ' could not be written.' );
      
	  	return true;
    }

    /**
     * Remove a key.
     *
     * @access  public
     * @param   string key
     */
    function remove( $key ) 
	{
      	if ( $this->_cmd( 'DELE s/%s', urlencode( $this->id ), urlencode( $key ) ) === false )
        	return PEAR::raiseError( $key . ' could not be deleted.' );
      
      	return true;
    }
    
    /**
     * Remove all keys.
     *
     * @access  public
     */
    function free()
	{ 
      	foreach ( $this->keys() as $key )
        	$this->remove( $key );
    }
	
	
	// private methods
	
    /**
     * Sends a command and retrieves the answer.
     *
     * @access  private
     * @param   mixed* vars Arguments to sprintf
     * @return  string Stored Value
     * @throws  FormatException in case of an error
     */
    function _cmd()
	{
      	$args = func_get_args();
      	$this->_sock->write( $cmd = vsprintf( $args[0] . "\n", array_slice( $args, 1 ) ) );
      	$return = chop( $this->_sock->read( 65536 ) );
      
      	// +OK text saved.
      	// -ERR SET format: key=val
      	// -ERR not understood
      	if ( '+OK' != substr( $return, 0, $i = strpos( $return, ' ' ) ) )
        	return false;
      
      	return substr( $return, $i + 1 );
    }
} // END OF RegistryStorage_keyserver

?>
