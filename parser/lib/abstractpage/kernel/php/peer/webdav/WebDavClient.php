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
 * @package peer_webdav
 */
 
class WebDavClient extends PEAR
{
	/**
	 * @access private
	 */
  	var $_fp;
	
	/**
	 * @access private
	 */
  	var $_server;
	
	/**
	 * @access private
	 */
  	var $_port = 80;
	
	/**
	 * @access private
	 */
  	var $_path ='/';
	
	/**
	 * @access private
	 */
  	var $_user;
	
	/**
	 * @access private
	 */
  	var $_protocol = 'HTTP/1.0';
	
	/**
	 * @access private
	 */
  	var $_pass;
	
	/**
	 * @access private
	 */
  	var $_socket_timeout = 5;
	
	/**
	 * @access private
	 */
  	var $_errno;
	
	/**
	 * @access private
	 */
  	var $_errstr;
	
	/**
	 * @access private
	 */
  	var $_user_agent = 'WebDavClient $Revision: 1.6 $';
	
	/**
	 * @access private
	 */
  	var $_crlf = "\r\n";
	
	/**
	 * @access private
	 */
  	var $_req;
	
	/**
	 * @access private
	 */
  	var $_resp_status;
	
	/**
	 * @access private
	 */
  	var $_parser;
	
	/**
	 * @access private
	 */
  	var $_xmltree;
	
	/**
	 * @access private
	 */
  	var $_tree;
	
	/**
	 * @access private
	 */
  	var $_ls = array();
	
	/**
	 * @access private
	 */
  	var $_ls_ref;
	
	/**
	 * @access private
	 */
  	var $_ls_ref_cdata;
	
	/**
	 * @access private
	 */  
  	var $_delete = array();
	
	/**
	 * @access private
	 */
  	var $_delete_ref;
	
	/**
	 * @access private
	 */
  	var $_delete_ref_cdata;
  
  	/**
	 * @access private
	 */
  	var $_lock = array();
	
	/**
	 * @access private
	 */
  	var $_lock_ref;
	
	/**
	 * @access private
	 */
  	var $_lock_rec_cdata;
	
	/**
	 * @access private
	 */
  	var $_null = null;
	
	/**
	 * @access private
	 */
  	var $_buffer='';
	
	/**
	 * @access private
	 */
  	var $_connection_closed = false;
	
	
	/**
	 * @access public
	 */
  	function setServer( $server ) 
	{
    	$this->_server = $server;
  	}
  
  	/**
	 * @access public
	 */
  	function setPort( $port ) 
	{
    	$this->_port = $port;
  	}
  
  	/**
	 * @access public
	 */
  	function setUser( $user ) 
	{
    	$this->_user = $user;
  	}
  
  	/**
	 * @access public
	 */
  	function setPass( $pass ) 
	{
    	$this->_pass = $pass;
  	}
  
  	/**
	 * Should be HTTP/1.0 or HTTP/1.1 be used?
	 *
	 * @access public
	 */
  	function setProtocol( $version ) 
	{
    	if ( $version == 1 )
      		$this->_protocol = 'HTTP/1.1';
    	else
      		$this->_protocol = 'HTTP/1.0';
  	}
  
  	/**
	 * Convert ISO 8601 Date and Time Profile used in RFC 2518 to unix timestamp.
	 *
	 * @access public
	 */
  	function iso8601ToTime( $iso8601 ) 
	{
    	/*
     	date-time       = full-date "T" full-time
  
  	   	full-date       = date-fullyear "-" date-month "-" date-mday
    	full-time       = partial-time time-offset
  
     	date-fullyear   = 4DIGIT
     	date-month      = 2DIGIT  ; 01-12
     	date-mday       = 2DIGIT  ; 01-28, 01-29, 01-30, 01-31 based on
     	month/year
     	time-hour       = 2DIGIT  ; 00-23
     	time-minute     = 2DIGIT  ; 00-59
     	time-second     = 2DIGIT  ; 00-59, 00-60 based on leap second rules
     	time-secfrac    = "." 1*DIGIT
     	time-numoffset  = ("+" / "-") time-hour ":" time-minute
     	time-offset     = "Z" / time-numoffset
  
     	partial-time    = time-hour ":" time-minute ":" time-second
                      	  [time-secfrac]
     	*/

     	$regs = array();
     
	 	/*           [1]        [2]        [3]        [4]        [5]        [6]  */   
     	if ( ereg( '^([0-9]{4})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})Z$', $iso8601, $regs ) )
       		return mktime( $regs[4],$regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );   
     
     	// to be done: regex for partial-time...apache webdav mod never returns partial-time
     	return false;
  	}

	/**
	 * @access public
	 */  
  	function open()
	{
    	$this->_fp = fsockopen( $this->_server, $this->_port, $this->_errno, $this->_errstr, $this->_socket_timeout );
    	socket_set_blocking( $this->_fp, true );
    
		if ( !$this->_fp ) 
		{
      		return false;
    	} 
		else 
		{
      		$this->_connection_closed = false;
      		return true;
    	} 
  	}

	/**
	 * Closes an open socket connection.
	 *
	 * @access public
	 */  
  	function close()
	{
    	$this->_connection_closed = true;
    	fclose( $this->_fp );
  	}

  	/**
	 * Checks if server supports webdav methods
   	 * We only check if server returns a DAV Element in Header and if so
  	 * if schema 1,2 is supported...
	 *
	 * @access public
	 */
  	function checkWebDav()
	{
    	$resp = $this->options();
    
		if ( !$resp )
      		return false;
    
    	// check schema
    	if ( preg_match( '/1,2/', $resp['header']['DAV'] ) )
      		return true;
    
    	// otherwise return false
    	return false;
  	}
  
  	/**
	 * @access public
	 */  
  	function options()
	{
    	$this->_headerUnset();
    	$this->_createBasicRequest( 'OPTIONS' );
    	$this->_sendRequest();
    	$this->_getResponse();
    	
		$response = $this->_processResponse();     
    
		// validate the response ... 
    	// check http-version
    	if ( $response['status']['http-version'] == 'HTTP/1.1' || $response['status']['http-version'] == 'HTTP/1.0' )
       		return $response;
    
    	return false; 
  	}
  
  	/**
	 * Creates a new collection/directory on webdav server.
	 *
	 * @access public
	 */
  	function mkcol( $path ) 
	{
    	// $this->_fp = pfsockopen( $this->_server, $this->_port, $this->_errno, $this->_errstr, $this->_socket_timeout );
    
		$this->_path = $this->_translateURI( $path );
    	$this->_headerUnset();
    	$this->_createBasicRequest( 'MKCOL' );
    	$this->_sendRequest();
    	$this->_getResponse();
    
		$response = $this->_processResponse();
    
		// validate the response ... 
    	// check http-version
    	if ( $response['status']['http-version'] == 'HTTP/1.1' || $response['status']['http-version'] == 'HTTP/1.0' ) 
			return $response['status']['status-code'];
  	}
  
  	/**
	 * Gets a file from a webdav collection.
	 * Returns status code and fills on success
  	 * buffer with the file received from webdav server
	 *
	 * @access public
	 */
  	function get( $path, &$buffer ) 
	{
    	$this->_path = $this->_translateURI( $path );
    	$this->_headerUnset();
    	$this->_createBasicRequest( 'PUT' );    
    	$this->_sendRequest();
    	$this->_getResponse();
    	
		$response = $this->_processResponse();
    
    	// validate the response 
    	// check http-version 
    	if ( $response['status']['http-version'] == 'HTTP/1.1' || $response['status']['http-version'] == 'HTTP/1.0' ) 
		{
      		// seems to be http ... proceed 
      		// We expect a 200 code 
      		if ( $response['status']['status-code'] == 200 )
        		$buffer = $response['body'];
      
      		return $response['status']['status-code'];
     	} 
     	
		// no http status was returned?
     	return false; 
  	}
  
  	/**
	 * Puts a file into a collection.
  	 * Wants data to putted as one chunk.
	 *
	 * @access public
	 */
  	function put( $path, $data ) 
	{
    	$this->_path = $this->_translateURI( $path );
    	$this->_headerUnset();
    	$this->_createBasicRequest( 'PUT' );
    
		// add more needed header information ...
    	$this->_headerAdd( 'Content-length: ' . strlen( $data ) ); 
    	$this->_headerAdd( 'Content-type: application/octet-stream' );
    
		// send header 
    	$this->_sendRequest();
    
		// send the rest (data)
    	fputs( $this->_fp, $data );
    	
		$this->_getResponse();
    	$response = $this->_processResponse();
    
    	// validate the response 
    	// check http-version 
    	if ( $response['status']['http-version'] == 'HTTP/1.1' || $response['status']['http-version'] == 'HTTP/1.0' ) 
		{
      		// seems to be http ... proceed 
      		// We expect a 200 or 204 status code 
      		// see rfc 2068 - 9.6 PUT...
      		return $response['status']['status-code'];
     	} 
     	
		// no http status was returned ?
     	return false; 
  	}
  
  	/**
	 * @access public
	 */
  	function putFile( $path, $filename ) 
	{
    	// try to open the file ...
    	$handle = fopen( $filename, 'r' );
    
		if ( $handle ) 
		{
      		// $this->_fp = pfsockopen( $this->_server, $this->_port, $this->_errno, $this->_errstr, $this->_socket_timeout );
      		$this->_path = $this->_translateURI( $path );
      		$this->_headerUnset();
      		$this->_createBasicRequest( 'PUT' ); 
      		
			// add more needed header information ...
      		$this->_headerAdd( 'Content-length: ' . filesize( $filename ) ); 
      		$this->_headerAdd( 'Content-type: application/octet-stream' );
      		
			// send header 
      		$this->_sendRequest();
      
	  		while ( !feof( $handle ) )
        		fputs( $this->_fp, fgets( $handle, 4096 ) );
      
      		fclose( $handle );
      		$this->_getResponse();
      		$response = $this->_processResponse();
    
      		// validate the response 
      		// check http-version 
      		if ( $response['status']['http-version'] == 'HTTP/1.1' || $response['status']['http-version'] == 'HTTP/1.0' ) 
			{
        		// seems to be http ... proceed 
        		// We expect a 200 or 204 status code 
        		// see rfc 2068 - 9.6 PUT...
        		return $response['status']['status-code'];
      		}   
      		
			// no http status was returned ?
      		return false;   
    	} 
		else 
		{
      		return false;
    	}
  	}

  	/**
	 * Copy a file on webdav server.
	 *
	 * @access public
	 */
  	function copyFile( $src_path, $dst_path, $overwrite ) 
	{
   		$this->_path = $this->_translateURI( $src_path );
   		$this->_headerUnset();
   		$this->_createBasicRequest( 'COPY' );    
   		$this->_headerAdd( sprintf( 'Destination: http://%s%s', $this->_server, $this->_translateURI( $dst_path ) ) );
   
   		if ( $overwrite )
     		$this->_headerAdd( 'Overwrite: T' );
   		else
     		$this->_headerAdd( 'Overwrite: F' );
   
   		$this->_headerAdd( '' ); 
   		$this->_sendRequest();
   		$this->_getResponse();
   		
		$response = $this->_processResponse();
   
   		// validate the response ... 
   		// check http-version
   		if ( $response['status']['http-version'] == 'HTTP/1.1' || $response['status']['http-version'] == 'HTTP/1.0' ) 
			return $response['status']['status-code']; 
   		
		return false;
  	}
  
  	/**
	 * Copy a collection on webdav server.
	 *
	 * @access public
	 */
  	function copyColl( $src_path, $dst_path, $overwrite ) 
	{
   		$this->_path = $this->_translateURI( $src_path );
   		$this->_headerUnset();
   		$this->_createBasicRequest( 'COPY' );    
   		$this->_headerAdd( sprintf( 'Destination: http://%s%s', $this->_server, $this->_translateURI( $dst_path ) ) );
   		$this->_headerAdd( 'Depth: Infinity' );
   
   		$xml  = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\r\n";
   		$xml .= "<d:propertybehavior xmlns:d=\"DAV:\">\r\n";
   		$xml .= "  <d:keepalive>*</d:keepalive>\r\n";
   		$xml .= "</d:propertybehavior>\r\n";
   
   		$this->_headerAdd( 'Content-length: ' . strlen( $xml ) ); 
   		$this->_headerAdd( 'Content-type: text/xml' );
   		$this->_sendRequest();
    
		// send also xml 
   		fputs( $this->_fp, $xml );
   
   		$this->_getResponse();
   		$response = $this->_processResponse();
   
   		// validate the response ... 
   		// check http-version
   		if ( $response['status']['http-version'] == 'HTTP/1.1' || $response['status']['http-version'] == 'HTTP/1.0' ) 
			return $response['status']['status-code'];
   		
		return false;
  	}
  
  	/**
	 * Move/rename a file/collection on webdav server.
	 *
	 * @access public
	 */
  	function move( $src_path,$dst_path, $overwrite ) 
  	{  
   		$this->_path = $this->_translateURI( $src_path );
    	$this->_headerUnset();
    	$this->_createBasicRequest( 'MOVE' );    
    	$this->_headerAdd( sprintf( 'Destination: http://%s%s', $this->_server, $this->_translateURI( $dst_path ) ) );
    
		if ( $overwrite )
      		$this->_headerAdd( 'Overwrite: T' );
    	else
      		$this->_headerAdd( 'Overwrite: F' );
    
    	$this->_headerAdd(''); 
    	$this->_sendRequest();
    	$this->_getResponse();
    	
		$response = $this->_processResponse();
    
		// validate the response ... 
    	// check http-version
    	if ( $response['status']['http-version'] == 'HTTP/1.1' || $response['status']['http-version'] == 'HTTP/1.0' ) 
      		return $response['status']['status-code']; 
    
    	return false;
  	}
  
  	/**
	 * Locks a webdav resource.
	 *
	 * @access public
	 */
  	function lock( $path ) 
  	{
    	$this->_path = $this->_translateURI( $path );
    
		$this->_headerUnset();
    	$this->_createBasicRequest( 'LOCK' );    
    	$this->_headerAdd( 'Timeout: Infinite' );
    	$this->_headerAdd( 'Content-type: text/xml' );
    	
		// create the xml request ...
    	$xml =  "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\r\n";
    	$xml .= "<D:lockinfo xmlns:D='DAV:'\r\n>";
    	$xml .= "  <D:lockscope><D:exclusive/></D:lockscope>\r\n";
    	$xml .= "  <D:locktype><D:write/></D:locktype>\r\n";
    	$xml .= "  <D:owner>\r\n";
    	$xml .= "    <D:href>chris</D:href>\r\n";
    	$xml .= "  </D:owner>\r\n";
    	$xml .= "</D:lockinfo>\r\n";
    	
		$this->_headerAdd( 'Content-length: ' . strlen( $xml ) ); 
    	$this->_sendRequest();
    	
		// send also xml 
    	fputs( $this->_fp, $xml );
    	$this->_getResponse();
    	
		$response = $this->_processResponse();
    
		// validate the response ... (only basic validation)
    	// check http-version
    	if ( $response['status']['http-version'] == 'HTTP/1.1' || $response['status']['http-version'] == 'HTTP/1.0' ) 
	   	{     
      		switch ( $response['status']['status-code'] ) 
			{
        		case 200:
          			// collection was successfully locked... see xml response to get lock token...
          			if ( strcmp( $response['header']['Content-Type'], 'text/xml; charset="utf-8"' ) == 0 ) 
					{
            			// ok let's get the content of the xml stuff
            			$this->_parser = xml_parser_create_ns();
            			
						// forget old data...
            			unset( $this->_lock[$this->_parser] );
            			unset( $this->_xmltree[$this->_parser] );
            			
						xml_parser_set_option( $this->_parser, XML_OPTION_SKIP_WHITE,   0 );
            			xml_parser_set_option( $this->_parser, XML_OPTION_CASE_FOLDING, 0 );
            			xml_set_object( $this->_parser, $this );
            			xml_set_element_handler( $this->_parser, "_lock_startElement", "_endElement" );
            			xml_set_character_data_handler( $this->_parser, "_lock_cdata" ); 
                    
            			if ( !xml_parse( $this->_parser, $response['body'] ) ) 
						{
							xml_parser_free( $this->_parser ); 
							
              				return PEAR::raiseError(
								sprintf( "XML error: %s at line %d",
								xml_error_string( xml_get_error_code( $this->_parser ) ),
								xml_get_current_line_number( $this->_parser ) )
							);
            			} 
          
            			// Free resources 
            			xml_parser_free( $this->_parser ); 
            			
						// add status code to array
            			$this->_lock[$this->_parser]['status'] = 200;
            			return $this->_lock[$this->_parser];
          			} 
					else 
					{
            			print 'Missing Content-Type: text/xml header in response.<br>';
          			}
          			
					return false;
          
        		default:
          			// collection or file was successfully deleted 
          			$this->_lock['status'] = $response['status']['status-code'];
          			return $this->_lock;
      		}
    	}
  	}
  
  	/**
	 * Unlocks a locked resource.
	 *
	 * @access public
	 */
  	function unlock( $path, $locktoken ) 
	{
    	$this->_path = $this->_translateURI( $path );
    	$this->_headerUnset();
    	$this->_createBasicRequest( 'UNLOCK' );    
    	$this->_headerAdd( sprintf( 'Lock-Token: <%s>', $locktoken ) );
    	$this->_sendRequest();
    	$this->_getResponse();
    	
		$response = $this->_processResponse();
    
		if ( $response['status']['http-version'] == 'HTTP/1.1' || $response['status']['http-version'] == 'HTTP/1.0' )
      		return $response['status']['status-code'];
     
    	return false;
  	}
  
  	/**
	 * Deletes a collection/directory on a webdav server.
	 *
	 * @access public
	 */
  	function delete( $path ) 
	{
    	$this->_path = $this->_translateURI( $path );
    	$this->_headerUnset();
    	$this->_createBasicRequest( 'DELETE' );
    	$this->_headerAdd('');
    	$this->_sendRequest();
    	$this->_getResponse();
    
		$response = $this->_processResponse();
        
    	// validate the response ... 
    	// check http-version
    	if ( $response['status']['http-version'] == 'HTTP/1.1' || $response['status']['http-version'] == 'HTTP/1.0' ) 
		{
      		// seems to be http ... proceed 
      		// We expect a 207 Multi-Status status code 
      
      		switch ( $response['status']['status-code'] ) 
			{
        		case 207:
          			// collection was NOT deleted... see xml response for reason...
          			// next there should be a Content-Type: text/xml; charset="utf-8" header line
          			if ( strcmp( $response['header']['Content-Type'], 'text/xml; charset="utf-8"' ) == 0 ) 
					{
            			// ok let's get the content of the xml stuff
            			$this->_parser = xml_parser_create_ns();
            
						// forget old data...
            			unset( $this->_delete[$this->_parser]  );
            			unset( $this->_xmltree[$this->_parser] );
            			
						xml_parser_set_option( $this->_parser, XML_OPTION_SKIP_WHITE,   0 );
            			xml_parser_set_option( $this->_parser, XML_OPTION_CASE_FOLDING, 0 );
            			xml_set_object( $this->_parser, $this );
            			xml_set_element_handler( $this->_parser, "_delete_startElement", "_endElement" );
            			xml_set_character_data_handler( $this->_parser, "_delete_cdata" ); 
                    
            			if ( !xml_parse( $this->_parser, $response['body'] ) ) 
						{
							xml_parser_free( $this->_parser ); 
							
              				return PEAR::raiseError(
								sprintf( "XML error: %s at line %d",
                           		xml_error_string( xml_get_error_code( $this->_parser ) ),
                           		xml_get_current_line_number( $this->_parser ) )
							);
            			} 
          
            			print_r( $this->_delete[$this->_parser] );
            			print "<br>";
          
            			// Free resources 
            			xml_parser_free( $this->_parser ); 
            			$this->_delete[$this->_parser]['status'] = $response['status']['status-code'];
            			
						return $this->_delete[$this->_parser];
          			} 
					else 
					{
           	 			print 'Missing Content-Type: text/xml header in response.<br>';
          			}
          			
					return false;
          
        		default:
          			// collection or file was successfully deleted 
          			$this->_delete['status'] = $response['status']['status-code'];
          			return $this->_delete;
      		}   
    	}
  	}
  
  	/**
	 * Get's directory information from webdav server in flat array using PROPFIND.
	 *
	 * @access public
	 */
  	function ls( $path ) 
  	{
    	$this->_path = $this->_translateURI( $path );
    
    	$this->_headerUnset();
    	$this->_createBasicRequest( 'PROPFIND' );
    	$this->_headerAdd( 'Depth: 1' );
    	$this->_headerAdd( 'Content-type: text/xml' );
    	
		// create profind xml request...
    	$xml  = "<?xml version=\"1.0\"?>\r\n";
    	$xml .= "<A:propfind xmlns:A=\"DAV:\">\r\n";
    	
		// shall we get all properties ?
    	$xml .= "    <A:allprop/>\r\n";
    	
		// or should we better get only wanted props ?
    	$xml .= "</A:propfind>\r\n";
    	
		$this->_headerAdd('Content-length: ' . strlen( $xml ) ); 
    	$this->_sendRequest();
    	fputs( $this->_fp, $xml );
    	$this->_getResponse();
    	
		$response = $this->_processResponse();
    
		// validate the response ... (only basic validation)
    	// check http-version
    	if ( $response['status']['http-version'] == 'HTTP/1.1' || $response['status']['http-version'] == 'HTTP/1.0' ) 
		{
      		// seems to be http ... proceed 
      		// We expect a 207 Multi-Status status code 
      		if ( strcmp( $response['status']['status-code'],'207' ) == 0 ) 
			{
        		// ok so far 
        		// next there should be a Content-Type: text/xml; charset="utf-8" header line
        		if ( strcmp( $response['header']['Content-Type'], 'text/xml; charset="utf-8"' ) == 0 ) 
				{
          			// ok let's get the content of the xml stuff
          			$this->_parser = xml_parser_create_ns();
          			
					// forget old data...
          			unset( $this->_ls[$this->_parser] );
          			unset( $this->_xmltree[$this->_parser] );
          
		  			xml_parser_set_option( $this->_parser, XML_OPTION_SKIP_WHITE,   0 );
          			xml_parser_set_option( $this->_parser, XML_OPTION_CASE_FOLDING, 0 );
          			xml_set_object( $this->_parser, $this );
          			xml_set_element_handler( $this->_parser, "_propfind_startElement", "_endElement" );
          			xml_set_character_data_handler( $this->_parser, "_propfind_cdata" ); 
          
          			if ( !xml_parse( $this->_parser, $response['body'] ) ) 
					{
						xml_parser_free( $this->_parser ); 
						
            			return PEAR::raiseError(
							sprintf( "XML error: %s at line %d",
                        	xml_error_string( xml_get_error_code( $this->_parser ) ),
                        	xml_get_current_line_number( $this->_parser ) )
						);
          			} 
          
          			// Free resources 
          			xml_parser_free( $this->_parser ); 
          			return $this->_ls[$this->_parser];    
        		} 
				else 
				{
          			return false;
        		}
      		}
    	} 
    
    	// response was not http 
    	return false;
  	}


  	// private methods
	
	/**
	 * @access private
	 */
 	function _endElement( $parser, $name ) 
	{
      	$this->_xmltree[$parser] = substr( $this->_xmltree[$parser], 0, strlen( $this->_xmltree[$parser] ) - ( strlen( $name ) + 1 ) );
  	} 

	/**
	 * @access private
	 */  
  	function _propfind_startElement( $parser, $name, $attrs ) 
	{
    	// lower XML Names... maybe break a RFC, don't know ...
    
    	$propname = strtolower( $name );
    	$this->_xmltree[$parser] .= $propname . '_';

    	// translate xml tree to a flat array ...
    	switch ( $this->_xmltree[$parser] ) 
		{
      		case 'dav::multistatus_dav::response_':
        		// new element in mu
        		$this->_ls_ref =& $this->_ls[$parser][];
        		break;
      		
			case 'dav::multistatus_dav::response_dav::href_':
        		$this->_ls_ref_cdata = &$this->_ls_ref['href'];
        		break;
      		
			case 'dav::multistatus_dav::response_dav::propstat_dav::prop_dav::creationdate_':
        		$this->_ls_ref_cdata = &$this->_ls_ref['creationdate'];
        		break;
      		
			case 'dav::multistatus_dav::response_dav::propstat_dav::prop_dav::getlastmodified_':
        		$this->_ls_ref_cdata = &$this->_ls_ref['lastmodified'];
        		break;
      		
			case 'dav::multistatus_dav::response_dav::propstat_dav::prop_dav::getcontenttype_':
        		$this->_ls_ref_cdata = &$this->_ls_ref['getcontenttype'];
        		break;
      		
			case 'dav::multistatus_dav::response_dav::propstat_dav::prop_dav::getcontentlength_':
        		$this->_ls_ref_cdata = &$this->_ls_ref['getcontentlength'];
        		break;
      		
			case 'dav::multistatus_dav::response_dav::propstat_dav::prop_dav::lockdiscovery_dav::activelock_dav::depth_':
        		$this->_ls_ref_cdata = &$this->_ls_ref['activelock_depth'];
        		break;
      		
			case 'dav::multistatus_dav::response_dav::propstat_dav::prop_dav::lockdiscovery_dav::activelock_dav::owner_dav::href_':
        		$this->_ls_ref_cdata = &$this->_ls_ref['activelock_owner'];
        		break;
      		
			case 'dav::multistatus_dav::response_dav::propstat_dav::prop_dav::lockdiscovery_dav::activelock_dav::owner_':
        		$this->_ls_ref_cdata = &$this->_ls_ref['activelock_owner'];
        		break;
      		
			case 'dav::multistatus_dav::response_dav::propstat_dav::prop_dav::lockdiscovery_dav::activelock_dav::timeout_':
        		$this->_ls_ref_cdata = &$this->_ls_ref['activelock_timeout'];
        		break;
      		
			case 'dav::multistatus_dav::response_dav::propstat_dav::prop_dav::lockdiscovery_dav::activelock_dav::locktoken_dav::href_':
        		$this->_ls_ref_cdata = &$this->_ls_ref['activelock_token'];
        		break;
      		
			case 'dav::multistatus_dav::response_dav::propstat_dav::prop_dav::lockdiscovery_dav::activelock_dav::locktype_dav::write_':
        		$this->_ls_ref_cdata = &$this->_ls_ref['activelock_type'];
        		$this->_ls_ref_cdata = 'write';
        		$this->_ls_ref_cdata = &$this->_null;
        		break;
      		
			case 'dav::multistatus_dav::response_dav::propstat_dav::prop_dav::resourcetype_dav::collection_':
        		$this->_ls_ref_cdata = &$this->_ls_ref['resourcetype'];
        		$this->_ls_ref_cdata = 'collection';
        		$this->_ls_ref_cdata = &$this->_null;
        		break;
      		
			case 'dav::multistatus_dav::response_dav::propstat_dav::status_':
        		$this->_ls_ref_cdata = &$this->_ls_ref['status'];
        		break;
      
      		default:
       			// handle unknown xml elements...
       			$this->_ls_ref_cdata = &$this->_ls_ref[$this->_xmltree[$parser]];
    	}  
  	}  
  
  	/**
	 * @access private
	 */ 
  	function _propfind_cData( $parser, $cdata ) 
	{
    	if ( trim( $cdata ) <> '' )
      		$this->_ls_ref_cdata = $cdata;
    	else
      		;
  	}
  
  	/**
	 * @access private
	 */
  	function _delete_startElement( $parser, $name, $attrs ) 
	{
    	// lower XML Names... maybe break a RFC, don't know ...
    	$propname = strtolower( $name );    
		$this->_xmltree[$parser] .= $propname . '_';

    	// translate xml tree to a flat array ...
    	switch ( $this->_xmltree[$parser] ) 
		{
      		case 'dav::multistatus_dav::response_':
        		// new element in mu
        		$this->_delete_ref =& $this->_delete[$parser][];
        		break;
      		
			case 'dav::multistatus_dav::response_dav::href_':
        		$this->_delete_ref_cdata = &$this->_ls_ref['href'];
       	 		break;
      
      		default:
       			// handle unknown xml elements...
       			$this->_delete_cdata = &$this->_delete_ref[$this->_xmltree[$parser]];
    	}  
  	}  
	
	/**
	 * @access private
	 */
  	function _delete_cData( $parser, $cdata ) 
	{
    	if ( trim( $cdata ) <> '' ) 
      		$this->_delete_ref_cdata = $cdata;
    	else
      		;
  	}

	/**
	 * @access private
	 */  
  	function _lock_startElement( $parser, $name, $attrs ) 
	{
    	// lower XML Names... maybe break a RFC, don't know ...
    	$propname = strtolower( $name );
    	$this->_xmltree[$parser] .= $propname . '_';

    	// translate xml tree to a flat array ...
    	/*
    	dav::prop_dav::lockdiscovery_dav::activelock_dav::depth_=
    	dav::prop_dav::lockdiscovery_dav::activelock_dav::owner_dav::href_=
    	dav::prop_dav::lockdiscovery_dav::activelock_dav::timeout_=
    	dav::prop_dav::lockdiscovery_dav::activelock_dav::locktoken_dav::href_=
    	*/
    	switch ( $this->_xmltree[$parser] ) 
		{
      		case 'dav::prop_dav::lockdiscovery_dav::activelock_':
        		// new element
        		$this->_lock_ref =& $this->_lock[$parser][];
        		break;
      
	  		case 'dav::prop_dav::lockdiscovery_dav::activelock_dav::locktype_dav::write_':
        		$this->_lock_ref_cdata = &$this->_lock_ref['locktype'];
        		$this->_lock_cdata = 'write';
        		$this->_lock_cdata = &$this->_null;
        		break;
      
	  		case 'dav::prop_dav::lockdiscovery_dav::activelock_dav::lockscope_dav::exclusive_':
        		$this->_lock_ref_cdata = &$this->_lock_ref['lockscope'];
        		$this->_lock_ref_cdata = 'exclusive';
        		$this->_lock_ref_cdata = &$this->_null;
        		break;
      		
			case 'dav::prop_dav::lockdiscovery_dav::activelock_dav::depth_':
        		$this->_lock_ref_cdata = &$this->_lock_ref['depth'];
        		break;  
      		
			case 'dav::prop_dav::lockdiscovery_dav::activelock_dav::owner_dav::href_':
        		$this->_lock_ref_cdata = &$this->_lock_ref['owner'];
        		break;
      		
			case 'dav::prop_dav::lockdiscovery_dav::activelock_dav::timeout_':
        		$this->_lock_ref_cdata = &$this->_lock_ref['timeout'];
        		break;
      		
			case 'dav::prop_dav::lockdiscovery_dav::activelock_dav::locktoken_dav::href_':
        		$this->_lock_ref_cdata = &$this->_lock_ref['locktoken'];
        		break;
      		
			default:
       			// handle unknown xml elements...
       			$this->_lock_cdata = &$this->_lock_ref[$this->_xmltree[$parser]];
    	}  
  	}  

  	/**
	 * @access private
	 */
  	function _lock_cData( $parser, $cdata ) 
	{
    	if ( trim( $cdata ) <> '' )
      		$this->_lock_ref_cdata = $cdata;
    	else
      		;
  	}

	/**
	 * @access private
	 */  
  	function _headerAdd( $string )
	{
    	$this->_req[] = $string;
  	}
  
  	/**
	 * @access private
	 */
  	function _headerUnset()
	{
    	unset( $this->_req );
  	}
  
  	/**
	 * Generates a minimum request header for all request methods.
	 *
	 * @access private
	 */
  	function _createBasicRequest( $method ) 
	{
    	$request = '';
    
		$this->_headerAdd( sprintf( '%s %s %s', $method, $this->_path, $this->_protocol ) );
    	$this->_headerAdd( sprintf( 'Host: %s', $this->_server ) );
    
		// $request .= sprintf( 'Connection: Keep-Alive' );
    	$this->_headerAdd( sprintf( 'User-Agent: %s', $this->_user_agent ) );
    	$this->_headerAdd( sprintf( 'Authorization: Basic %s', base64_encode( "$this->_user:$this->_pass" ) ) );
  	}

	/**
	 * Sends the client request to the webdav server.
	 *
	 * @access private
	 */  
  	function _sendRequest()
	{
    	// check if stream is declared to be open
    	// only logical check we are not sure if socket is really still open ...
    	if ( $this->_connection_closed ) 
		{
      		// reopen it 
      		// be sure to close the open socket.
      		$this->close();
      		$this->_reOpen();
    	}
    
    	// convert array to string 
    	$buffer  = implode( "\r\n", $this->_req );
    	$buffer .= "\r\n\r\n";
    	fputs( $this->_fp, $buffer );
  	}

  	/**
	 * @access private
	 */
  	function _getResponse()
	{
    	$buffer = '';
    	$header = '';   
    
    	// following code maybe helps to improve socket behaviour ... more testing needed
    	// disabled at the moment ...
    	// socket_set_timeout($this->_fp,1 );
    	// $socket_state = socket_get_status($this->_fp);
    
    	// read stream one byte by another until http header ends
    	do 
		{
      		$header .= fread( $this->_fp, 1 );
    	} while ( !preg_match( '/\\r\\n\\r\\n$/', $header ) );
        
    	if ( preg_match( '/Connection: close\\r\\n/', $header ) ) 
		{
      		// This says that the server will close connection at the end of this stream. 
      		// Therefore we need to reopen the socket, before are sending the next request...
      		$this->_connection_closed = true;
    	}
    	
		// check how to get the data on socket stream 
    	// chunked or content-length (HTTP/1.1) or 
    	// one block until feof is received (HTTP/1.0)
    	switch ( true ) 
		{
      		case ( preg_match( '/Transfer\\-Encoding:\\s+chunked\\r\\n/', $header ) ):
        		do 
				{
          			$byte = '';
          			$chunk_size = '';
          			
					do 
					{
            			$chunk_size .= $byte;
            			$byte = fread( $this->_fp, 1 );
          			} while ( $byte != "\r" && strlen( $byte ) > 0 );	// till we match the Carriage Return
          			
					fread( $this->_fp, 1 );								// also drop off the Line Feed
          			$chunk_size = hexdec( $chunk_size );                // convert to a number in decimal system
          			$buffer .= fread( $this->_fp, $chunk_size );
          			fread( $this->_fp, 2 );								// ditch the CRLF that trails the chunk
        		} while ( $chunk_size );								// till we reach the 0 length chunk (end marker)
        		
				break;
        
      		// check for a specified content-length
      		case preg_match( '/Content\\-Length:\\s+([0-9]*)\\r\\n/', $header, $matches ):
        		$buffer = fread( $this->_fp, $matches[1] );
        		break;
      
      		// check for 204 No Content 
      		// 204 responds have no body.
      		// Therefore we do not need to read any data from socket stream. 
      		case preg_match( '/HTTP\/1\.1\ 204/', $header ):
        		break;
      		
			default:
        		// just get the data until foef appears...
        		socket_set_timeout( $this->_fp, 0 );
        		
				while ( !feof( $this->_fp ) )
          			$buffer .= fread( $this->_fp, 4096 );
        
        		// renew the socket timeout...does it do something ???? Is it needed. More debugging needed...
        		socket_set_timeout( $this->_fp, $this->_socket_timeout );
    	}
    
    	$this->_buffer = $header . "\r\n\r\n" . $buffer;
  	}

	/**
	 * Analyse the reponse from server and divide into header and body part
  	 * returns an array filled with components.
  	 *
	 * @access private
	 */  
  	function _processResponse()
	{
    	$lines = explode( "\r\n", $this->_buffer );
    	$header_done = false;
    	
		// First line should be a HTTP status line (see http://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html#sec6)
    	// Format is: HTTP-Version SP Status-Code SP Reason-Phrase CRLF
    	list( $ret_struct['status']['http-version'], 
         	  $ret_struct['status']['status-code'], 
         	  $ret_struct['status']['reason-phrase'] ) = explode( ' ', $lines[0], 3 );
         
    	// print "HTTP Version: '$http_version' Status-Code: '$status_code' Reason Phrase: '$reason_phrase'<br>";
    	// get the response header fields
    	// See http://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html#sec6
    	for ( $i = 1; $i < count( $lines ); $i++ ) 
		{
      		if ( rtrim( $lines[$i] ) == '' && !$header_done )
        		$header_done = true;
			   
      		if ( !$header_done ) 
			{
        		// store all found headers in array ...
        		list( $fieldname, $fieldvalue )   = explode( ':', $lines[$i] );
        		$ret_struct['header'][$fieldname] = trim( $fieldvalue );
      		} 
			else 
			{
          		$response_body .= $lines[$i];        
      		}
    	}
    	
		// print 'string len of response_body:' . strlen( $response_body );
    	// print '[' . htmlentities( $response_body ) . ']';
    	$ret_struct['body'] = $response_body; 
    	return $ret_struct;
  	}

  	/**
	 * Reopens a socket, if 'connection: closed'-header was received from server.
	 *
	 * @access private
	 */
  	function _reOpen()
	{
    	// let's try to reopen a socket 
    	return $this->open();
    
		/* 
    	$this->_fp = fsockopen( $this->_server, $this->_port, $this->_errno, $this->_errstr, 5 );
    	set_time_limit( 180 );
    	socket_set_blocking( $this->_fp, true );
    	socket_set_timeout( $this->_fp, 5 );
    
		if ( !$this->_fp ) 
		{
      		return false;
    	} 
		else 
		{
      		$this->_connection_closed = false;
      		return true;
    	} 
    	*/
  	}

	/**
	 * Translates an uri to url encoded string.
	 *
	 * @access private
	 */  
  	function _translateURI( $uri ) 
	{
    	$parts = explode( '/', $uri );
    
		for ( $i = 0; $i < count( $parts ); $i++ )
      		$parts[$i] = rawurlencode( $parts[$i] );
    
    	return implode( '/', $parts );
  	}
} // END OF WebDavClient

?>
