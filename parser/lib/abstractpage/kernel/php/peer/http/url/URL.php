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
|Authors: Richard Heyes <richard@php.net>                              |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * @package peer_http_url
 */
 
class URL extends PEAR
{
    /**
     * Full url
     * @var string
     */
    var $url;
    
    /**
     * Protocol
     * @var string
     */
    var $protocol;

    /**
     * Username
     * @var string
     */
    var $username;

    /**
     * Password
     * @var string
     */
    var $password;

    /**
     * Host
     * @var string
     */
    var $host;
    
    /**
     * Port
     * @var integer
     */
    var $port;
    
    /**
     * Path
     * @var string
     */
    var $path;
    
    /**
     * Query string
     * @var array
     */
    var $querystring;

    /**
     * Anchor
     * @var string
     */
    var $anchor;

    /**
     * Whether to use []
     * @var bool
     */
    var $useBrackets;


    /**
     * Constructor
     *
     * Parses the given url and stores the various parts
     * Defaults are used in certain cases
     *
     * @param string $url         Optional URL
	 * @param bool   $useBrackets Whether to use square brackets when
	 *                            multiple querystrings with the same name
	 *                            exist
     */
    function URL( $url = null, $useBrackets = true )
    {
        $this->useBrackets = $useBrackets;
        $this->url         = $url;
        $this->user        = '';
        $this->pass        = '';
		$this->host        = '';
		$this->port        = 80;
		$this->path        = '';
		$this->querystring = array();
		$this->anchor      = '';

		// Only use defaults if not an absolute URL given
		if ( !preg_match( '/^[a-z0-9]+:\/\//i', $url ) ) 
		{
	        /**
	         * Figure out host/port
	         */
	        if ( !empty( $_SERVER['HTTP_HOST'] ) && preg_match( '/^(.*)(:([0-9]+))?$/U', $_SERVER['HTTP_HOST'], $matches ) ) 
			{
	            $host = $matches[1];
	            
				if ( !empty($matches[3] ) )
	                $port = $matches[3];
	            else
	                $port = '80';
	        }
	
	        $this->protocol    = 'http' . ( @$HTTP_SERVER_VARS['HTTPS'] == 'on'? 's' : '' );
	        $this->user        = '';
	        $this->pass        = '';
	        $this->host        = !empty( $host )? $host : ( isset( $_SERVER['SERVER_NAME'] )? $_SERVER['SERVER_NAME'] : 'localhost' );
	        $this->port        = !empty( $port )? $port : ( isset( $_SERVER['SERVER_PORT'] )? $_SERVER['SERVER_PORT'] : 80 );
	        $this->path        = !empty( $_SERVER['PHP_SELF'] )? $_SERVER['PHP_SELF'] : '/';
	        $this->querystring = isset( $_SERVER['QUERY_STRING'] )? $this->_parseRawQuerystring( $_SERVER['QUERY_STRING'] ) : null;
	        $this->anchor      = '';
		}

        // Parse the url and store the various parts.
        if ( !empty( $url ) ) 
		{
            $urlinfo = parse_url( $url );

            // Default querystring
            $this->querystring = array();
    
            foreach ( $urlinfo as $key => $value ) 
			{
                switch ( $key ) 
				{
                    case 'scheme':
                        $this->protocol = $value;
                        break;
                    
                    case 'user':
                
					case 'pass':
                
					case 'host':
                
					case 'port':
                        $this->$key = $value;
                        break;

                    case 'path':
                        if ( $value{0} == '/' ) 
						{
                            $this->path = $value;
                        } 
						else 
						{
                            $path = ( dirname( $this->path ) == DIRECTORY_SEPARATOR )? '' : dirname( $this->path );
                            $this->path = sprintf( '%s/%s', $path, $value );
                        }
						
                        break;
                    
                    case 'query':
                        $this->querystring = $this->_parseRawQueryString( $value );
                        break;

                    case 'fragment':
                        $this->anchor = $value;
                        break;
                }
            }
        }
    }

    /**
     * Returns full url.
     *
     * @return string Full url
     * @access public
     */
    function getURL()
    {
        $querystring = $this->getQueryString();

        $this->url = $this->protocol . '://'
                   . $this->user . ( !empty($this->pass )? ':' : '' )
                   . $this->pass . ( !empty($this->user )? '@' : '' )
                   . $this->host . ( $this->port == '80' ? ''  : ':' . $this->port )
                   . $this->path
                   . ( !empty( $querystring  )? '?' . $querystring  : '' )
                   . ( !empty( $this->anchor )? '#' . $this->anchor : '' );

        return $this->url;
    }

    /**
     * Adds a querystring item.
     *
     * @param  string $name       Name of item
     * @param  string $value      Value of item
     * @param  bool   $preencoded Whether value is urlencoded or not, default = not
     * @access public
     */
    function addQueryString( $name, $value, $preencoded = false )
    {
        $this->querystring[$name] = $preencoded? $value : urlencode( $value );
		
        if ( $preencoded )
            $this->querystring[$name] = $value;
        else
            $this->querystring[$name] = is_array( $value )? array_map( 'urlencode', $value ): urlencode( $value );
    }    

    /**
     * Removes a querystring item.
     *
     * @param  string $name Name of item
     * @access public
     */
    function removeQueryString( $name )
    {
        if ( isset( $this->querystring[$name] ) )
            unset( $this->querystring[$name] );
    }    
    
    /**
     * Sets the querystring to literally what you supply.
     *
     * @param  string $querystring The querystring data. Should be of the format foo=bar&x=y etc
     * @access public
     */
    function addRawQueryString( $querystring )
    {
        $this->querystring = $this->_parseRawQueryString( $querystring );
    }
    
    /**
     * Returns flat querystring.
     *
     * @return string Querystring
     * @access public
     */
    function getQueryString()
    {
        if ( !empty( $this->querystring ) ) 
		{
            foreach ( $this->querystring as $name => $value ) 
			{
                if ( is_array( $value ) ) 
				{
                    foreach ( $value as $k => $v )
                        $querystring[] = $this->useBrackets? sprintf( '%s[%s]=%s', $name, $k, $v ) : ( $name . '=' . $v );
                } 
				else if ( !is_null( $value ) ) 
				{
                    $querystring[] = $name . '=' . $value;
                } 
				else 
				{
                    $querystring[] = $name;
                }
            }
			
            $querystring = implode( '&', $querystring );
        } 
		else 
		{
            $querystring = '';
        }

        return $querystring;
    }

    /**
     * Resolves //, ../ and ./ from a path and returns
     * the result. Eg:
     *
     * /foo/bar/../boo.php    => /foo/boo.php
     * /foo/bar/../../boo.php => /boo.php
     * /foo/bar/.././/boo.php => /foo/boo.php
     *
     * This method can also be called statically.
     *
     * @param  string $url URL path to resolve
     * @return string      The result
     */
    function resolvePath( $path )
    {
        $path = explode( '/', str_replace( '//', '/', $path ) );
        
        for ( $i = 0; $i < count( $path ); $i++ ) 
		{
            if ( $path[$i] == '.' ) 
			{
                unset( $path[$i] );
                $path = array_values( $path );
                $i--;
            } 
			else if ( $path[$i] == '..' && ( $i > 1 || ( $i == 1 && $path[0] != '' ) ) ) 
			{
                unset( $path[$i] );
                unset( $path[$i - 1] );
                $path = array_values( $path );
                $i -= 2;
            } 
			else if ( $path[$i] == '..' && $i == 1 && $path[0] == '' ) 
			{
                unset( $path[$i] );
                $path = array_values( $path );
                $i--;
            } 
			else 
			{
                continue;
            }
        }

        return implode( '/', $path );
    }
	
	
	// private methods
	
    /**
     * Parses raw querystring and returns an array of it.
     *
     * @param  string  $querystring The querystring to parse
     * @return array                An array of the querystring data
     * @access private
     */
    function _parseRawQuerystring( $querystring )
    {
        $querystring = rawurldecode( $querystring );
        $parts = preg_split( '/&/', $querystring, -1, PREG_SPLIT_NO_EMPTY );

        $return = array();
        
        foreach ( $parts as $part ) 
		{
            if ( strpos( $part, '=' ) !== false ) 
			{
                $value = rawurlencode( substr( $part, strpos( $part, '=' ) + 1 ) );
                $key   = substr( $part, 0, strpos( $part, '=' ) );
            } 
			else 
			{
                $value = null;
                $key   = $part;
            }
            
            if ( substr( $key, -2 ) == '[]' ) 
			{
                $key = substr( $key, 0, -2 );
				
                if ( @!is_array( $return[$key] ) ) 
				{
                    $return[$key]   = array();
                    $return[$key][] = $value;
                } 
				else 
				{
                    $return[$key][] = $value;
                }
            } 
			else if ( !$this->useBrackets && !empty( $return[$key] ) ) 
			{
                $return[$key]   = (array)$return[$key];
                $return[$key][] = $value;
            } 
			else 
			{
                $return[$key] = $value;
            }
        }

        return $return;
    }
} // END OF URL

?>
