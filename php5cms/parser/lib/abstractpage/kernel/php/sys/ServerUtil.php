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
|Authors: Chuck Hagenbuch <chuck@horde.org>                            |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'net.http.agent.Browser' );
using( 'util.array.ArrayUtil' );


/**
 * The ServerUtil class provides functions to determine server
 * configuration settings.
 *
 * @package sys
 */

class ServerUtil extends PEAR
{
	/**
	 * Get secure server variables from $_SERVER (PHP 4.2 or above) 
	 * or emulate them depending on os, mode or server software
	 * (for now: Linux/Apache, Win/Apache, Win/IIS)
	 *
	 * @access public
	 * @static
	 */	
	function getServerVar( $variable = "" )
	{		
		$os     = stristr( getenv( "OS" ), "Windows" )? "win" : "linux"; // blatant assumption
		$server = $_SERVER['SERVER_SOFTWARE'];

		if ( php_sapi_name() == "cgi"  )
			$mode = "cgi";
		else if ( php_sapi_name() == "isapi" )
			$mode = "isapi";
		else // I'm not sure about that
			$mode = "module";
			
		$check  = strtoupper( $variable );
		
		switch ( $check )
		{
			// These are let through without modification.
			
			case "HTTP_ACCEPT":
			
			case "HTTP_ACCEPT_CHARSET":
			
			case "HTTP_ACCEPT_ENCODING":
			
			case "HTTP_ACCEPT_LANGUAGE":
			
			case "HTTP_CONNECTION":
			
			case "HTTP_HOST":
			
			case "HTTP_KEEP_ALIVE":
			
			case "HTTP_USER_AGENT":
			
			case "HTTP_REFERER":
			
			case "PATH_TRANSLATED":
			
			case "PHP_SELF":
			
			case "REMOTE_ADDR":
			
			case "REMOTE_HOST":
			
			case "REMOTE_PORT":

			case "REQUEST_METHOD":
				
			case "SERVER_NAME":
			
			case "SERVER_PORT":
			
			case "SERVER_PROTOCOL":
			
			case "SERVER_SOFTWARE":
				return $_SERVER[$check];
				
				
			// depending on environment
			
			case "SCRIPT_NAME":
				return ( ( $mode == "cgi" )? $_SERVER["PATH_INFO"] : $_SERVER["SCRIPT_NAME"] );
				break;
				
			case "SCRIPT_FILENAME":
				return str_replace( '//', '/', str_replace( '\\', '/', ( ( $mode == "cgi" || $mode == "isapi" )? $_SERVER["PATH_TRANSLATED"] : $_SERVER["SCRIPT_FILENAME"] ) ) );
				break;
			
			case "PATH_INFO":
				// $HTTP_SERVER_VARS["PATH_INFO"] != $HTTP_SERVER_VARS["SCRIPT_NAME"] is necessary because some servers (Windows/CGI) are seen to set PATH_INFO equal to script_name
				// Further, there must be at least one "/" in the path - else the PATH_INFO value does not make sense.
				if ( $mode != "cgi" )
					return $_SERVER[$check];
				else 
					return "";
			
				break;
				
			case "CONTENT_LENGTH":
				// hmm, don't know about this
				if ( !$_SERVER['CONTENT_LENGTH'] )
				{
					if ( $c = ob_get_contents() )
						return strlen ( $c );
					else
						return null;
				}
				else
				{
					return $_SERVER['CONTENT_LENGTH'];
				}
				
			case "DOCUMENT_ROOT":
				// doesn't exist for IIS with php as cgi module
				if ( !$_SERVER['DOCUMENT_ROOT'] )
				{
					// TODO
				}
				else
				{
					return $_SERVER['DOCUMENT_ROOT'];
				}
				
			case "HTTP_CACHE_CONTROL":
				return $_SERVER['HTTP_CACHE_CONTROL'];
				
			case "HTTPS":
				if ( $_SERVER['HTTPS'] )
					return $_SERVER['HTTPS'];
				else
					return "off";
				
			case "LOCAL_ADDR":
				return $_SERVER['LOCAL_ADDR'];
				
			case "QUERY_STRING":
				if ( !$_SERVER['QUERY_STRING'] )
				{
					$uri = "";
				
					if ( !empty( $_GET ) )
					{
						$params = array();
					
						foreach ( $_GET as $name => $value )
							$params[] = urlencode( $name ) . "=" . urlencode( $value );
						
						$uri .= '?' . implode( '&', $params );
					}
				
					return $uri;
				}
				else
				{
					return $_SERVER['QUERY_STRING'];
				}

			case "REQUEST_URI":
				// for IIS
				if ( !$_SERVER['REQUEST_URI'] )
				{
					$uri = $_SERVER['PHP_SELF'];
				
					if ( !empty( $_GET ) )
					{
						$params = array();
					
						foreach ( $_GET as $name => $value )
							$params[] = urlencode( $name ) . "=" . urlencode( $value );
						
						$uri .= '?' . implode( '&', $params );
					}
				
					return $uri;
				}
				else
				{
					return $_SERVER['REQUEST_URI'];
				}
				
			case "SERVER_ADDR":
				if ( !( $result = $_SERVER['SERVER_ADDR'] ) )
				{
					if ( $fp = fopen( ap_ini_get( "file_hostname", "file" ), 'r' ) )
					{
						$result = trim( fgets( $fp, 4096 ) );
						fclose( $fp );
						$result = gethostbyaddr( gethostbyname( $result ) );
					}
					else
					{
						$result = "N.A.";
					}
					
					return $result;
				}
				else
				{
					return $_SERVER['SERVER_ADDR'];
				}
		
			default:
				return null;
		}
	}
	
    /**
     * Determine if we are using a Secure (SSL) connection.
     *
     * @access public
     * @return boolean  True if using SSL, false if not.
     */
    function usingSSLConnection()
    {
        return ( ( array_key_exists( 'HTTPS', $_SERVER) && ( $_SERVER['HTTPS'] == 'on' ) ) || getenv( 'SSL_PROTOCOL_VERSION' ) );
    }

    /**
     * Determine if output compression can be used.
     *
     * @access public
     * @return boolean  True if output compression can be used, false if not.
     */
    function allowOutputCompression()
    {
        $browser = &Browser::singleton();

        /* Turn off compression for buggy browsers. */
        if ( $browser->hasQuirk( 'buggy_compression' ) )
            return false;

        return ( ini_get( 'zlib.output_compression' ) == '' && ini_get( 'output_handler' ) != 'ob_gzhandler' );
    }

    /**
     * Determine if files can be uploaded to the system.
     *
     * @access public
     * @return integer  If uploads allowed, returns the maximum size of the
     *                  upload in bytes.  Returns 0 if uploads are not
     *                  allowed.
     */
    function allowFileUploads()
    {
        if ( ini_get( 'file_uploads' ) )
		{
            if ( ( $dir = ini_get( 'upload_tmp_dir' ) ) && !is_writable( $dir ) )
                return false;
            
            $size = ini_get( 'upload_max_filesize' );

            switch ( strtolower( substr( $size, -1, 1 ) ) )
			{
				case 'k':
                	$size = intval( floatval( $size ) * 1024 );
                	break;

	            case 'm':
                	$size = intval( floatval( $size ) * 1024 * 1024 );
                	break;
            
				default:
                	$size = intval( $size );
                	break;
            }
			
            return $size;
        } 
		else 
		{
            return false;
        }
    }

    /**
     * Determines if the file was uploaded or not. If not, will return the
     * appropriate error message.
     *
     * @access public
     * @param string $field           The name of the field containing the
     *                                uploaded file.
     * @param optional string $name   The file description string to use in the
     *                                error message. Default: 'file'.
     * @return mixed  True on success, Error on error.
     */
    function wasFileUploaded( $field, $name = null )
    {
        /* TODO: These error constants appeared in PHP 4.3.0. This code
           should be removed once Horde requires PHP 4.3.0+. */
        if ( !defined( 'UPLOAD_ERR_OK' ) ) 
		{
            $upload_const = array(
                'UPLOAD_ERR_OK'        => 0,
                'UPLOAD_ERR_INI_SIZE'  => 1,
                'UPLOAD_ERR_FORM_SIZE' => 2,
                'UPLOAD_ERR_PARTIAL'   => 3,
                'UPLOAD_ERR_NO_FILE'   => 4
            );
			
            foreach ( $upload_const as $key => $val )
                define( $key, $val );
        }

        if ( !( $uploadSize = ServerUtil::allowFileUploads() ) )
            return PEAR::raiseError( "File uploads not supported." );

        /* Get any index on the field name. */
        $index = ArrayUtil::getArrayParts( $field, $base, $keys );

        if ( $index ) 
		{
            /* Index present, fetch the error var to check. */
            $keys_path = array_merge( array( $base, 'error' ), $keys );
            $error = ArrayUtil::getElement( $_FILES, $keys_path );
			
            /* Index present, fetch the tmp_name var to check. */
            $keys_path = array_merge( array( $base, 'tmp_name' ), $keys );
            $tmp_name = ArrayUtil::getElement( $_FILES, $keys_path );
        } 
		else 
		{
            /* No index, simple set up of vars to check. */
            $error    = $_FILES[$field]['error'];
            $tmp_name = $_FILES[$field]['tmp_name'];
        }

        if ( !isset( $_FILES ) || ( $error == UPLOAD_ERR_NO_FILE ) ) 
            return PEAR::raiseError( sprintf( "There was a problem with the file upload: No %s was uploaded.", $name ), $error );
		else if ( ( $error == UPLOAD_ERR_OK ) && is_uploaded_file( $tmp_name ) ) 
            return true;
		else if ( ( $error == UPLOAD_ERR_INI_SIZE ) || ( $error == UPLOAD_ERR_FORM_SIZE ) ) 
            return PEAR::raiseError( sprintf( "There was a problem with the file upload: The %s was larger than the maximum allowed size (%d bytes).", $name, $uploadSize ), $error );
		else if ( $error == UPLOAD_ERR_PARTIAL ) 
            return PEAR::raiseError( sprintf( "There was a problem with the file upload: The %s was only partially uploaded.", $name ), $error );
    }

    /**
     * Returns the Web server being used.
     * PHP string list built from the PHP 'configure' script.
     *
     * @access public
     *
     * @return string  A web server identification string.
     * <pre>
     * 'aolserver' = AOL Server
     * 'apache1'   = Apache 1.x
     * 'apache2'   = Apache 2.x
     * 'caudium'   = Caudium
     * 'cgi'       = Unknown server - PHP built as CGI program
     * 'isapi'     = Zeus ISAPI
     * 'nsapi'     = NSAPI
     * 'phttpd'    = PHTTPD
     * 'pi3web'    = Pi3Web
     * 'roxen'     = Roxen/Pike
     * 'servlet'   = Servlet
     * 'thttpd'    = thttpd
     * 'tux'       = Tux
     * 'webjames'  = Webjames
     * </pre>
     */
    function webServerID()
    {
        $server = php_sapi_name();

        if ( $server == 'apache' ) 
		{
            return 'apache1';
        } 
		else if ( ( $server == 'apache2filter' ) || ( $server == 'apache2handler' ) )
            return 'apache2';
        else
            return $server;
    }

    /**
     * Returns the server protocol in use on the current server.
     *
     * @access public
     * @return string  The HTTP server protocol version.
     */
    function HTTPProtocol()
    {
        if ( array_key_exists( 'SERVER_PROTOCOL', $_SERVER ) ) 
		{
            if ( ( $pos = strrpos( $_SERVER['SERVER_PROTOCOL'], '/' ) ) )
                return substr( $_SERVER['SERVER_PROTOCOL'], $pos + 1 );
        }

        return null;
    }
} // END OF ServerUtil

?>
