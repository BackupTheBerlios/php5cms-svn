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


// NOTE: possible there are problems with trailing slash on path infortmation; remove

define( "WEBDAV_HTTP_STATUS_OK",                   200 );
define( "WEBDAV_HTTP_STATUS_CREATED",              201 );
define( "WEBDAV_HTTP_STATUS_NOCONTENT",            204 );
define( "WEBDAV_HTTP_STATUS_MULTISTATUS",          207 );
define( "WEBDAV_HTTP_STATUS_MOVEDPERMANENTLY",     301 );
define( "WEBDAV_HTTP_STATUS_FORBIDDEN",            403 );
define( "WEBDAV_HTTP_STATUS_NOTFOUND",             404 );
define( "WEBDAV_HTTP_STATUS_METHODNOTALLOWED",     405 );
define( "WEBDAV_HTTP_STATUS_CONFLICT",             409 );
define( "WEBDAV_HTTP_STATUS_PRECONDITIONFAILED",   412 );
define( "WEBDAV_HTTP_STATUS_UNSOPPORTEDMEDIATYPE", 415 );
define( "WEBDAV_HTTP_STATUS_LOCKED",               423 );
define( "WEBDAV_HTTP_STATUS_FAILEDDEPENDENCY",     424 );
define( "WEBDAV_HTTP_STATUS_BADGATEWAY",           502 );
define( "WEBDAV_HTTP_STATUS_UNSUFFICIENTSTORAGE",  507 );

if ( !defined( "WEBDAV_DIR_FS" ) )
	define( "WEBDAV_DIR_FS", 'webdav/tmp/' );

if ( !defined( "WEBDAV_DIR_DOCROOT" ) )
	define( "WEBDAV_DIR_DOCROOT", 'webdav/' );

define( "WEBDAV_DIR_WEB", ap_ini_get( "path_tmp_os", "path" ) );


/**
 * @package peer_webdav
 */
 
class WebDavServer extends PEAR
{
	/**
	 * @access public
	 */
	var $httpStatusMessages = array(
		200 => "200 Ok",
		201 => "201 Created",				
		204 => "204 No Content",
		207 => "207 Multi-Status",
		403 => "403 Forbidden",
		404 => "404 Not Found",
		405 => "405 Method Not Allowed",
		409 => "409 Conflict",
		412 => "412 Precondition Failed",
		423 => "423 Locked",
		424 => "424 Failed Dependency",
		502 => "502 Bad Gateway",
		507 => "507 Unsifficient Storage"		
	);
				
	
	/**
	 * Constructor
	 *
	 * @access public
	 */ 	
    function WebDavServer( $callbacks )
    {
        $this->callback = $callbacks;
    }
	

	/**
	 * @access public
	 */
    function start()
	{
		header( "AP-Dav-Powered-By: " . ap_ini_get( "agent_name", "settings" ) );
		
 		if ( isset( $_GET["path"] ) )
		{
			// maybe we should use something else than getcwd() or at least make it configurable
        	$this->path = preg_replace( "#^/*" . WEBDAV_DIR_FS . "#", "/", $_GET["path"] );
		}
		else
		{
			$this->path = "/";
		}
 
		switch ( $_SERVER["REQUEST_METHOD"] )
        {
        	case "PROPFIND":
                // Return Codes
                // 207 Multi-Stats (true/false)
                $this->propfind();
            	break;
        
			case "GET":
            	// Return Codes
            	// 200				(true)
            	// 404 Not Found	(false)
            	$this->get();
            	break;

        	case "MOVE":
            	// Return Codes
               	// 201 Created 		(true)
               	// 204 No Content
               	// 403 Forbidden	(false)
               	// 409 Conflict
               	// 412 Precondidtion Failed
               	// 423 Locked
               	// 424 Failed Dependency 
               	// 502 Bad Gateway
				$this->move();
				break;
				
			case "MKCOL":
				// Return Codes
				// 201 Created 		(true)
				// 403 Forbidden	(false)
				// 405 Method not allowed
				// 409 Conflict
				// 415 Unsopperted Media Type
				// 507 Insuficient Storage
				$this->mkcol();
				break;
				
			case "DELETE":
				// Return Codes
				// 204 No Content	(true)
				// 207 Mulit-Status	(false)
				$this->delete();
				break;
			
			case "PUT":
            	$this->put();
            	break;
        
			case "OPTIONS":
            	$this->options();
            	break;
        
			default:
            	header( "HTTP/1.1 404 file not found" );
            	return false;
        }
    }

	/**
	 * @access public
	 */
    function propfind() 
	{
        $options = array();
        $options["path"] = $this->path;
		
		if ( isset( $_SERVER['HTTP_DEPTH'] ) )
        	$options["depth"] = $_SERVER["HTTP_DEPTH"];
        else
        	$options["depth"] = "infinity";
        
        $ret = call_user_func_array( $this->callback["PROPFIND"], array( $options, &$files ) );
        
		if ( $ret ) 
		{
            header( "HTTP/1.1 " . $this->httpStatusMessages[207] );
            header( 'Content-Type: text/xml' );
			
            print '<?xml version="1.0" encoding="utf-8"?>
            <D:multistatus xmlns:D="DAV:">';
            
			foreach ( $files["files"] as $file )
            {
                print '<D:response xmlns:lp0="DAV:" xmlns:lp1="http://apache.org/dav/props/" xmlns:i0="DAV:" xmlns:i1="http://services.eazel.com/namespaces">';
				print "\n";
				print '<D:href>' . str_replace( ' ', '%20', $file['href'] ) . '</D:href>';
				// print '<D:href>' . str_replace( ' ',   '%20', utf8_encode( $file['href'] ) ) . '</D:href>';
				// print '<D:href>' . str_replace( '%2F', '/',   urlencode( $file['href'] ) )   . '</D:href>';
				// print '<D:href>' . recode( "iso-8859-1..xml", $file['href'] ) . '</D:href>';

				print "\n";				
                print '<D:propstat>
                <D:prop>
                <lp0:creationdate xmlns:b="urn:uuid:c2f41010-65b3-11d1-a29f-00aa00c14882/" b:dt="dateTime.tz">2002-05-19T13:47:20Z</lp0:creationdate>
                <lp0:getcontentlength>' . $file['contentlength'] . '</lp0:getcontentlength>
                <lp0:getlastmodified xmlns:b="urn:uuid:c2f41010-65b3-11d1-a29f-00aa00c14882/" b:dt="dateTime.rfc1123">Sun, 19 May 2002 13:47:20 GMT</lp0:getlastmodified>
                <D:supportedlock>
                <D:lockentry>
                <D:lockscope><D:exclusive/></D:lockscope>
                <D:locktype><D:write/></D:locktype>
                </D:lockentry>
                <D:lockentry>
                <D:lockscope><D:shared/></D:lockscope>
                <D:locktype><D:write/></D:locktype>
                </D:lockentry>
                </D:supportedlock>
                <D:lockdiscovery/>';

                if ( $file['iscollection'] )
                {
                    print "<D:resourcetype><D:collection/></D:resourcetype>";
                }
                else
                {
                    print "<D:getcontenttype>" . $file['contenttype'] . "</D:getcontenttype>";
                    print "<D:resourcetype/>";
                }
				
                print '</D:prop>
                <D:status>HTTP/1.1 200 OK</D:status>
                </D:propstat>
				<D:propstat>
				<D:prop>
				<i0:name/>
				<i0:parentname/>
				<i0:href/>
				<i0:ishidden/>
				<i0:iscollection/>
				<i0:isreadonly/>
				<i0:contentclass/>
				<i0:getcontentlanguage/>
				<i0:lastaccessed/>
				<i0:isstructureddocument/>
				<i0:defaultdocument/>
				<i0:displayname/>
				<i0:isroot/>
 
                <i1:nautilus-treat-as-directory/>
				
				</D:prop>
				<D:status>HTTP/1.1 404 Not Found</D:status>
				</D:propstat>
                </D:response>';
            }
			
            print '</D:multistatus>';
		}
		else
		{
			header( "HTTP/1.1 404 File Not Found" );
		}
	}

	/**
	 * @access public
	 */
	function get() 
	{
		$options = array();
        $options["path"] = $this->path;

        $ret = call_user_func_array( $this->callback["GET"], array( $options ) );
		
		if ( $ret !== true )
			header( "HTTP/1.1 ".$this->httpStatusMessages[WEBDAV_HTTP_STATUS_NOTFOUND] );
	}

	/**
	 * @access public
	 */ 
	function move() 
	{
		$options = array();
		$options["path"] = $this->path;
		$url = parse_url( $_SERVER["HTTP_DESTINATION"] );
		$options["destination"] = urldecode( $url["path"] );
		$ret = call_user_func_array( $this->callback["MOVE"], array( $options ) );
		
		if ( $ret === true )
			$ret = WEBDAV_HTTP_STATUS_CREATED;
			
		header( "HTTP 1.1 " . $this->httpStatusMessages[$ret] );
	}

	/**
	 * @access public
	 */
	function mkcol() 
	{
		$options = array();
		$options["path"] = $this->path;
		$ret = call_user_func_array( $this->callback["MKCOL"], array( $options ) );
			
		if ( $ret === true )
			$ret = WEBDAV_HTTP_STATUS_CREATED;
		else if ( $ret === false )
			$ret = WEBDAV_HTTP_STATUS_FORBIDDEN;
			
		header( "HTTP 1.1 " . $this->httpStatusMessages[$ret] );
	}

	/**
	 * @access public
	 */
	function delete() 
	{
		$options = array();
		$options["path"] = $this->path;
		$ret = call_user_func_array( $this->callback["DELETE"], array( $options ) );
		
		if ( $ret === true )
			$ret = WEBDAV_HTTP_STATUS_NOCONTENT;
			
		header( "HTTP 1.1 " . $this->httpStatusMessages[$ret] );
	}

	/**
	 * @access public
	 */
	function options()
	{
		header( "HTTP/1.1 200 OK" );
	 	header( "MS-Author-Via: DAV" );
	 	header( "Allow: OPTIONS, GET, HEAD, POST, DELETE, TRACE, PROPFIND, PROPPATCH, COPY, MOVE" ); // LOCK, UNLOCK");
		header( "DAV: 1,2,<http://apache.org/dav/propset/fs/1>" );
        
		call_user_func_array( $this->callback["OPTIONS"], array() );
	}

	/**
	 * @access public
	 */
	function put()
	{
		global $HTTP_RAW_POST_DATA;
     	
		$options = array();
        $options["path"] = $this->path;
 		$options["content-length"] = $_SERVER["CONTENT_LENGTH"];
	    $ret = call_user_func_array( $this->callback["PUT"], array( $options, &$HTTP_RAW_POST_DATA ) );
		
		header( "HTTP 1.1 " . $this->httpStatusMessages[$ret] );
	}
} // END OF WebDavServer

?>
