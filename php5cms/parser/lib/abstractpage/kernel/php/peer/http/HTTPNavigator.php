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


using( 'peer.http.HTTPStorage' );
using( 'util.Util' );


define( 'HTTPNAV_RESPONSE',	'http_response' );
define( 'HTTPNAV_REQUEST',	'http_request'  );
define( 'HTTPNAV_CURRENT',	'cur'  );
define( 'HTTPNAV_LAST',		'last' );
define( 'HTTPNAV_HEADER',	'head' );
define( 'HTTPNAV_BODY',		'body' );


// codes below are NOT http status codes,
// do not use these to compare agains HTTP status codes

// warning
define( 'HTTPNAV_WRN_MIME_REJECTED',  301 );
define( 'HTTPNAV_WRN_MATCH_REJECTED', 302 );
define( 'HTTPNAV_WRN_BODY_SIZE',      303 );

// error
define( 'HTTPNAV_ERR_CONNECT',        401 );
define( 'HTTPNAV_ERR_TIMED_OUT',      402 );
define( 'HTTPNAV_ERR_URL_FORMAT',     403 );
define( 'HTTPNAV_ERR_HEADER_SIZE',    404 );
define( 'HTTPNAV_ERR_HEADER_STATUS',  405 );
define( 'HTTPNAV_ERR_CURL_INIT',      450 );
define( 'HTTPNAV_ERR_CURL_UNKNOWN',   498 );
define( 'HTTPNAV_ERR_UNKNOWN',        499 );


/**
 * HTTPNavigator class
 *
 * PHP class to simplify content grabbing from web sites,
 * while also keeping track of previous sites, auth details, 
 * and cookies set along the way (similar to a browser).
 *
 * @package peer_http
 */

class HTTPNavigator extends PEAR
{
	/**
	 * HTTP version number to use in requests
	 * @access public
	 */
	var $http_version;

	/**
	 * HTTP default port number
	 * @access public
	 */
	var $http_port;

	/**
	 * Socket connection timeout (seconds)
	 * @access public
	 */
	var $socket_timeout;

	/**
	 * Socket retry attempts
	 * @access public
	 */
	var $socket_retry = 2;

	/**
	 * Maximum bytes http header can be
	 * @access public
	 */
	var $max_head_bytes = 5000;

	/**
	 * Maximum bytes http body can be - rest truncated
	 * @access public
	 */
	var $max_body_bytes = 400000;

	/**
	 * Read timeout (seconds) - disabled: 0
	 * @access public
	 */
	var $read_timeout;

	/**
	 * Return HTTP header only - skips downloading body
	 * @access public
	 */
	var $header_only = false;

	/**
	 * Reject if regular expression match - reject if any header response line
	 * matches regular expression in this value (used with preg_* function)
	 * example value: '/^Server:.*?Apache.*?/i'
	 * @access public
	 */
	var $reject_if_match = '';

	/**
	 * Reject if mime match - reject if any mime type listed is found in content-type
	 * header field, list of mime types seperated by only one comma, 
	 * example: 'text/*,image/jpeg'
	 * If using cURL, the body will be downloaded before header can be checked, to
	 * avoid this problem, set $this->curl_head_first to true
	 * @access public
	 */
	var $reject_if_mime = '';

	/**
	 * Accept-Language - value to use in field of HTTP request
	 * Why? - Some dynamic sites can return content based on this value
	 * @access public
	 */
	var $hd_accept_lang = 'en-gb';

	/**
	 * User-Agent - value to use in field of HTTP request
	 * Why? - Some dynamic sites will modify/change output based on this
	 * See: http://mozilla-evangelism.bclary.com/sidebars/ua/ for alternative
	 * User-Agent strings
	 * @access public
	 */
	var $hd_user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-GB; rv:0.9.4) Gecko/20011019 Netscape6/6.2';

	/**
	 * Accept - mime type value to use in field of HTTP request
	 * @access public
	 */
	var $hd_accept = '*/*';
	
	/**
	 * Accept-Encoding - value to use in field of HTTP request
	 * best leave empty if you want to see plain html, otherwise, if server supports it,
	 * the returned body could be encoded based on this value.
	 * @access public
	 */	
	var $hd_accept_encoding	= '';

	/**
	 * Storage name - used as part of filename (eg. cookies.store_name,
	 * basic_auth.store_name), leave as empty string if you do not
	 * wish save values to disk
	 * @access public
	 */
	var $store_name = '';

	/**
	 * Store cookies - (''=no, 'plain'=yes, 'encrypt'=encrypted)
	 * values are serialized first, then stored (or encrypted and stored)
	 * @access public
	 */
	var $store_cookies = 'plain';
	
	/**
	 * Store basic auth - (''=no, 'plain'=yes, 'encrypt'=encrypted)
	 * values are serialized first, then stored (or encrypted and stored)
	 * @access public
	 */
	var $store_basic_auth = 'plain';
	
	/**
	 * Storage path - full path to directory where storage vars will be
	 * serialized and stored (make sure this path has write access for the httpd user)
	 * @access public
	 */
	var $store_path = '/home/me/secret/path/';

	/**
	 * Crypt key - 1-24 chars, if not empty, the vars marked 'encrypt' will be
	 * encrypted and stored (this requires php be compiled with mcrypt support,
	 * libmcrypt 2.4.x). If string empty ('') values will NOT be encrypted, 
	 * even if they're marked as 'encrypt'
	 * @access public
	 */
	var $store_crypt_key = 'secret';
	
	/**
	 * Save basic auth - store basic auth details for future requests,
	 * stored in: $this->basic_auth
	 * @access public
	 */
	var $basic_auth_save = true;

	/**
	 * Send basic auth - allow base64 encoded user and pass to be sent back to servers
	 * @access public
	 */
	var $basic_auth_send = true;

	/**
	 * Basic auth life - lifetime of auth details since being set (seconds), 
	 * setting this to 0 will clear $this->basic_auth array when
	 * $this->del_old_basic_auth() is called
	 * @access public
	 */
	var $basic_auth_life = 0;

	/**
	 * Use last referer - use last URL ($this->_last['url']) as referer
	 * @access public
	 */
	var $referer_use_last = false;

	/**
	 * Referer - holds referer URL
	 * @access public
	 */
	var $referer = '';

	/**
	 * Save cookies - save cookies returned in server response
	 * @access public
	 */
	var $cookie_save = true;

	/**
	 * Send cookies - use saved cookies in HTTP requests (if site and cookie criteria match)
	 * @access public
	 */
	var $cookie_send = true;

	/**
	 * Cookie life - lifetime of saved cookies since being set (seconds),
	 * setting this to 0 will clear the $this->cookie array when
	 * $this->del_old_cookies() is called
	 * @access public
	 */
	var $cookie_life = 172800;	// ((60 * 60) * 24) * 2 = 2 days

	/**
	 * Auto redirects - maximum number of continuous http redirects (disabed: 0)
	 * @access public
	 */
	var $redirect_auto = 5;

	/**
	 * Meta refresh delay - redirect if delay found in meta refresh is less than
	 * this value (seconds)
	 * Why? - Some sites with dynamic content set a meta tag to refresh every few minutes,
	 *        this serves a different purpose than those sites wanting to redirect us to the
	 *        correct page
	 * @access public
	 */
	var $redirect_meta_limit = 10;

	/**
	 * Redirect as new - treats a redirect as a new site request (calling $this->new_site())
	 * so $this->_last['url'] would be the url the redirect was detected in.
	 * Setting this to false would make redirects under same site id
	 * @access public
	 */
	var $redirect_as_new = true;

	/**
	 * Use cURL - If you have access to cURL then you might not need a class like this :)
	 * but if you have cURL support in PHP, you can enable this to let cURL handle connection
	 * and retrieval. (only enable if PHP has built in support)
	 *
	 * @access public
	 * @see	http://www.php.net/manual/en/ref.curl.php
	 * @see	http://curl.haxx.se/
	 */
	var $curl = false;

	/**
	 * Use cURL SSL - Set this to true if you want to use cURL to access SSL sites (https://),
	 * without this option you will NOT be able to retrieve SSL pages (URL will be rejected).
	 * (only enable if PHP has built in support)
	 * @access public
	 */
	var $curl_ssl = false;

	/**
	 * cURL low speed limit - transfer speed (bytes per second) that the transfer
	 * should be below (during $this->curl_low_speed_time seconds) for PHP to
	 * consider too slow and abort. (-1: disabled)
	 * @access public
	 */
	var $curl_low_speed_limit = -1;

	/**
	 * cURL low speed time - seconds that the transfer should be below for PHP to consider it
	 * too slow and abort. (-1: $this->read_timeout)
	 * @access public
	 */
	var $curl_low_speed_time = -1;

	/**
	 * cURL SSL version - SSL version (2 or 3). (-1: PHP will try and determine this by itself)
	 * @access public
	 */
	var $curl_ssl_version = -1;

	/**
	 * cURL grab head first - process head first, then, if appropriate, grab body as well.
	 * This will speed things up if you would like to reject content based on HTTP header,
	 * but if most of the sites are OK, you should set it to false.
	 * If true, cURL will make 2 requests (if the header is not rejected first)
	 * @access public
	 */
	var $curl_head_first = true;

	/**
	 * Proxy - use proxy? (untested by me, let me know if this works)
	 * @access public
	 */
	var $proxy = false;

	/**
	 * Proxy server
	 * @access public
	 */
	var $proxy_server = '';

	/**
	 * Proxy port
	 * @access public
	 */
	var $proxy_port = 0;
	
	/**
	 * Proxy username (if required)
	 * @access public
	 */
	var $proxy_user = '';
	
	/**
	 * Proxy password (if required)
	 * @access public
	 */
	var $proxy_pass = '';

	/**
	 * Parse body - some infomation can be grabbed from the body of the response that could
	 * be useful, set this to true if you'd like the html to be parsed.
	 * @access public
	 */
	var $parse_body = true;


	// html parsing details below will have no effect if parse_body (above) is false

	/**
	 * Parse if mime match - parse body only if mime type matches (obviously we wouldn't
	 * want to search through the contents of an image file for html tags)
	 * @access public
	 */
	var $parse_if_mime = 'text/html';

	/**
	 * Parse meta refresh - sets $this->_cur['redirect'] if meta refresh tag is found
	 * @access public
	 */
	var $parse_meta_refresh	= true;
	
	/**
	 * Parse title - sets $this->_cur['title'] with value found between <title></title> tags
	 * @access public
	 */
	var $parse_title = true;

	/**
	 * Holds cookies in associative array:
	 *   [domain][path][name]['value']		(string)
	 *						['secure']		(bool)
	 *						['expires']		(int - unix timestamp)
	 *						['date_added']	(int - unix timestamp)
	 *
	 * @access public
	 * @see	_set_cookie
	 */
	var $cookie = array();	// holds cookies found in response headers
	
	/**
	 * Holds basic auth in associative array:
	 *      [host][realm]['path']		(string)
	 *					['user']		(string)
	 *					['pass']		(string)
	 *					['date_added']	(int - unix timestamp)
	 * 
	 * @access public
	 * @see	_set_basic_auth
	 */
	var $basic_auth = array();
 
	/**
	 * Holds previous site URLs in array
	 * @access public
	 */
	var $site = array();

	/**
	 * Holds HTTP status codes as index and meaning as value
	 * @access public
	 */
	var $status_code = array();


	// private properties
	
	/**
	 * Current site
	 *
	 * Holds each piece of information about current site as an array element:
	 *		'scheme'		: scheme (eg. 'http' or 'https')
	 *		'url'			: full URL
	 *		'path'			: http path
	 *		'host'			: http host
	 *		'port'			: http port
	 *		'status_v'		: http response status: version number
	 *		'status_c'		: http response status: code
	 *		'status_p'		: http response status: phrase
	 *		'title'			: html found between <title></title>
	 *		'redirect'		: redirect URL found in http header
	 *		'basic_auth'	: username and pass for basic auth (array)
	 *		'time_taken'	: time taken to grab page in seconds
	 *		'curl_info'		: associative array with info from cURL transfer
	 *		'http_request'	: HTTP request array (HTTPNAV_HEADER, HTTPNAV_BODY)
	 *		'http_response'	: HTTP response array (HTTPNAV_HEADER, HTTPNAV_BODY)
	 *
	 * @access	private
	 */
	var $_cur = array();

	/**
	 * Last site
	 *
	 * Holds each piece of information about last site as an array element
	 * (see $_cur)
	 *
	 * @access	private
	 * @see		$_cur
	 */
	var $_last = array();

	/**
	 * new line to seperate each header with
	 * @access private
	 */
	var $_nl_rn = "\r\n";
	
	/**
	 * new line to explode returned headers with
	 * @access private
	 */
	var $_nl_n = "\n";
	
	/**
	 * holds header field names and position numbers
	 * @access private
	 */
	var $_http_header_field	= array();
	
	/**
	 * holds http request header in array
	 * @access private
	 */
	var $_http_header = null;
	
	/**
	 * holds start time
	 * @access private
	 */
	var $_time_start = null;
	
	/**
	 * holds end time
	 * @access private
	 */
	var $_time_end = null;
	
	/**
	 * true if current site was a result of automatic redirect
	 * @access private
	 */
	var $_was_redirect = false;
	
	/**
	 * true if 401 status returned
	 * @access private
	 */
	var $_send_with_auth = false;
	
	/**
	 * holds realm if 401 returned
	 * @access private
	 */
	var $_realm = null;
	
	/**
	 * holds current site id
	 * @access private
	 */
	var $_cur_id = 0;
	
	/**
	 * holds current number of continuous redirects
	 * @access private
	 */
	var $_cur_redirect_auto = 0;

	/**
	 * if disk storage enabled, this will hold instance of http_storage
	 * @access private
	 */
	var $_obj_storage = null;

	/**
	 * if $this->curl_head_first true, this will indicate when only head has been retreived
	 * @access private
	 */
	var $_curl_got_head	= false;


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function HTTPNavigator()
	{
		$this->http_version   = "HTTP/1.0";
		$this->http_port      = 80;
		$this->socket_timeout = 30;
		$this->read_timeout   = 60;
		
		// load storage vars from disk
		if ( !empty( $this->store_name ) )
			$this->obj_storage_action( 'load' );

		// delete old vars
		$this->del_old_cookies( false );
		$this->del_old_basic_auth();

		// store status code meanings
		$this->status_code[100] = 'Continue';
		$this->status_code[101] = 'Switching Protocols';
		$this->status_code[200] = 'OK';
		$this->status_code[201] = 'Created';
		$this->status_code[202] = 'Accepted';
		$this->status_code[203] = 'Non-Authoritative Information';
		$this->status_code[204] = 'No Content';
		$this->status_code[205] = 'Reset Content';
		$this->status_code[206] = 'Partial Content';
		$this->status_code[300] = 'Multiple Choices';
		$this->status_code[301] = 'Moved Permanently';
		$this->status_code[302] = 'Found';
		$this->status_code[303] = 'See Other';
		$this->status_code[304] = 'Not Modified';
		$this->status_code[305] = 'Use Proxy';
		$this->status_code[307] = 'Temporary Redirect';
		$this->status_code[400] = 'Bad Request';
		$this->status_code[401] = 'Unauthorized';
		$this->status_code[402] = 'Payment Required';
		$this->status_code[403] = 'Forbidden';
		$this->status_code[404] = 'Not Found';
		$this->status_code[405] = 'Method Not Allowed';
		$this->status_code[406] = 'Not Acceptable';
		$this->status_code[407] = 'Proxy Authentication Required';
		$this->status_code[408] = 'Request Time-out';
		$this->status_code[409] = 'Conflict';
		$this->status_code[410] = 'Gone';
		$this->status_code[411] = 'Length Required';
		$this->status_code[412] = 'Precondition Failed';
		$this->status_code[413] = 'Request Entity Too Large';
		$this->status_code[414] = 'Request-URI Too Large';
		$this->status_code[415] = 'Unsupported Media Type';
		$this->status_code[416] = 'Requested range not satisfiable';
		$this->status_code[417] = 'Expectation Failed';
		$this->status_code[500] = 'Internal Server Error';
		$this->status_code[501] = 'Not Implemented';
		$this->status_code[502] = 'Bad Gateway';
		$this->status_code[503] = 'Service Unavailable';
		$this->status_code[504] = 'Gateway Time-out';
		$this->status_code[505] = 'HTTP Version not supported';

		// store valid field names used for HTTP requests and order in which they should appear in
		// the request.  Shuffle these in the order the fields should appear in the actual request.
		// If a header is not listed here, you won't be able to add it using
		// $obj->add_http_header(), so if there's any missing, just add it to this list.
		$x = 1;

		// general headers
		$this->_http_header_field['cache-control']			= $x++;
		$this->_http_header_field['connection']				= $x++;
		$this->_http_header_field['date']					= $x++;
		$this->_http_header_field['pragma']					= $x++;
		$this->_http_header_field['trailer']				= $x++;
		$this->_http_header_field['transfer-encoding']		= $x++;
		$this->_http_header_field['upgrade']				= $x++;
		$this->_http_header_field['via']					= $x++;
		$this->_http_header_field['warning']				= $x++;

		// request headers
		$this->_http_header_field['accept']					= $x++;
		$this->_http_header_field['accept-charset']			= $x++;
		$this->_http_header_field['accept-encoding']		= $x++;
		$this->_http_header_field['accept-language']		= $x++;
		$this->_http_header_field['authorization']			= $x++;
		$this->_http_header_field['cookie']					= $x++;
		$this->_http_header_field['expect']					= $x++;
		$this->_http_header_field['from']					= $x++;
		$this->_http_header_field['host']					= $x++;
		$this->_http_header_field['if-match']				= $x++;
		$this->_http_header_field['if-modified-since']		= $x++;
		$this->_http_header_field['if-none-match']			= $x++;
		$this->_http_header_field['if-range']				= $x++;
		$this->_http_header_field['if-unmodified-since']	= $x++;
		$this->_http_header_field['max-forwards']			= $x++;
		$this->_http_header_field['proxy-authorization']	= $x++;
		$this->_http_header_field['range']					= $x++;
		$this->_http_header_field['referer']				= $x++;
		$this->_http_header_field['te']						= $x++;
		$this->_http_header_field['user-agent']				= $x++;

		// entity headers
		$this->_http_header_field['allow']					= $x++;
		$this->_http_header_field['content-encoding']		= $x++;
		$this->_http_header_field['content-language']		= $x++;
		$this->_http_header_field['content-length']			= $x++;
		$this->_http_header_field['content-location']		= $x++;
		$this->_http_header_field['content-md5']			= $x++;
		$this->_http_header_field['content-range']			= $x++;
		$this->_http_header_field['content-type']			= $x++;
		$this->_http_header_field['expires']				= $x++;
		$this->_http_header_field['last-modified']			= $x++;
	}

	
	/**
	 * Clears current site info ($this->_cur), last site info ($this->_last), or both
	 * (does not affect cookies or auth details, which are held in seperate arrays).
	 *
	 * @param	string	$which	either HTTPNAV_CURRENT or HTTPNAV_LAST, if none specified, both will be clared
	 * @return	bool
	 * @access	public
	 */
	function clear( $which = null )
	{
		if ( is_null( $which ) )
		{
			$this->_cur  = array();
			$this->_last = array();
		}
		else if ( $which == HTTPNAV_CURRENT )
		{
			$this->_cur = array();
		}
		else if ( $which == HTTPNAV_LAST )
		{
			$this->_last = array();
		}
		else
		{
			return false;
		}
		
		return true;
	}

	/**
	 * Sets last site details from current site,
	 * splits URL up into various parts, increments $this->_cur_id,
	 * resets a few private vars.
	 *
	 * @param	string	$url	full url (eg. http://example.com/dir/file.php?querystring=foo)
	 * @return	bool
	 * @see		get_url()
	 */
	function new_site( $url )
	{
		// set current site as last site
		if ( !empty( $this->_cur['host'] ) )
			$this->_set_last_url();
	
		// increment site id
		$this->_cur_id++;
		
		if ( $this->_set_cur_url( $url ) )
		{
			$this->_reset_vars();
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Return var content.
	 *
	 * @param	string	$var	name of var to return
	 * @return	mixed
	 * @see		set_var()
	 */
	function &get_var( $var )
	{
		return $this->$var;
	}

	/**
	 * Set var content.
	 *
	 * @param	string	$var	name of var to assign value to
	 * @param	string	$value	value to assign to $var
	 * @return	bool
	 * @see		get_var
	 */
	function set_var( $var, $value )
	{
		if ( $this->$var = $value )
			return true;
		else
			return false;
	}

	/**
	 * Get HTTP status.
	 *
	 * Return HTTP version, status code or phrase returned by server
	 *
	 * @param	string	$status	either 'version', 'code' (default), or 'phrase' (or 'v','c','p')
	 * @param	string	$which	either HTTPNAV_CURRENT for current or HTTPNAV_LAST for last, default: HTTPNAV_CURRENT
	 * @return	string
	 */
	function get_status( $status = 'code', $which = HTTPNAV_CURRENT )
	{
		$status = substr( strtolower( $which ), 0, 1 );
		
		if ( $which === HTTPNAV_CURRENT )
			$info = &$this->_cur;
		else if ( $which === HTTPNAV_LAST )
			$info = &$this->_last;
		
		switch ( $status )
		{
			case 'v':
				return @$info['status_v'];
				break;
			
			case 'c':
				return @$info['status_c'];
				break;
			
			case 'p':
				return @$info['status_p'];
				break;
			
			default:
				return false;
		}
	}

	/**
	 * Return value from site info, or return all info as array.
	 *
	 * @param	string	$var	info name, (eg. 'time_taken')
	 * @param	string	$which	either HTTPNAV_CURRENT for current or HTTPNAV_LAST for last, default: HTTPNAV_CURRENT
	 * @return	string
	 */
	function &get_info( $var = null, $which = HTTPNAV_CURRENT )
	{
		if ( $which == HTTPNAV_CURRENT )
			$info = &$this->_cur;
		else if ( $which == HTTPNAV_LAST )
			$info = &$this->_last;
		else
			return false;

		if ( is_null( $var ) )
			return $info;
		else if ( is_string( $var ) && isset( $info[strtolower( $var )] ) )
			return $info[strtolower( $var )];
		else
			return false;
	}

	/**
	 * Rebuild current URL as string (can override values by passing array).
	 * 
	 * Rebuilds current URL replacing some parts with ones supplied in $array
	 * Note: this will not include Basic Auth user and pass
	 *
	 * @param	array	$array		Associative array with valid keys being: 'host', 'port', 'path'
	 * @param	bool	$with_auth	If true and array has 'user' and 'pass' keys, they will be
	 *								built into the string, otherwise ignored
	 * @return	string				URL with host, port and path
	 * @access	public
	 */
	function rebuild_cur_url( $array = array(), $with_auth = false )
	{
		$scheme = ( isset( $array['scheme'] )? $array['scheme'] : $this->_cur['scheme'] );
		$host   = ( isset( $array['host']   )? $array['host']   : $this->_cur['host']   );
		$port   = ( isset( $array['port']   )? $array['port']   : $this->_cur['port']   );
		$path   = ( isset( $array['path']   )? $array['path']   : $this->_cur['path']   );
		$auth   = '';

		if ( $with_auth && isset( $array['host'] ) && isset( $array['user'] ) && isset( $array['pass'] ) )
		{
			if ( $array['user'] != '' && $array['pass'] != '' )
				$auth = "{$array['user']}:{$array['pass']}@";
		}
		
		if ( ( $scheme != 'http' ) && ( $scheme != 'https' ) )
			$scheme = 'http';
		
		if ( $port == $this->http_port )
			return $scheme . '://' . $auth . $host . $path;
		else
			return $scheme . '://' . $auth . $host . ':' . $port . $path;
	}

	/**
	 * Status phrase / group
	 *
	 * Returns array with status phrase and group.
	 *
	 * @param	int		$code	HTTP status code (eg. 404)
	 * @return	array			associative array with keys: 'meaning' and 'range_meaning'
	 * @access	public
	 */
	function status_info( $code )
	{
		$code      = intval( $code );
		$r_meaning = 'Unrecognised';
		$meaning   = 'Unrecognised';

		if ( isset( $this->status_code[$code] ) )
			$meaning = $this->status_code[$code];

		if ( $code >= 100 && $code <= 199 )
			$r_meaning = 'Informational: Request received, continuing process';
		else if ( $code >= 200 && $code <= 299 )
			$r_meaning = 'Success: The action was successfully received, understood, and accepted';
		else if ( $code >= 300 && $code <= 399 )
			$r_meaning = 'Redirection: Further action must be taken in order to complete the request';
		else if ( $code >= 400 && $code <= 499 )
			$r_meaning = 'Client Error: The request contains bad syntax or cannot be fulfilled';
		else if ( $code >= 500 && $code <= 599 )
			$r_meaning = 'Server Error: The server failed to fulfill an apparently valid request';

		return array(
			'meaning' => $meaning,
			'range_meaning' => $r_meaning
		);
	}

	/**
	 * Get body size.
	 *
	 * Returns size of body returned by HTTP server
	 *
	 * @param	string	$which	either HTTPNAV_CURRENT for current or HTTPNAV_LAST for last, default: HTTPNAV_CURRENT
	 * @return	int				length in bytes
	 * @access	public
	 */
	function get_body_size( $which = HTTPNAV_CURRENT )
	{
		if ( $which == HTTPNAV_CURRENT )
			$info = &$this->_cur;
		else if ( $which == HTTPNAV_LAST )
			$info = &$this->_last;
		else
			return false;

		if ( isset( $info[HTTPNAV_RESPONSE][HTTPNAV_BODY] ) )
			return strlen( $info[HTTPNAV_RESPONSE][HTTPNAV_BODY] );
		else
			return false;
	}

	/**
	 * Get header size.
	 *
	 * Returns size of header returned by HTTP server
	 *
	 * @param	string	$which	either HTTPNAV_CURRENT for current or HTTPNAV_LAST for last, default: HTTPNAV_CURRENT
	 * @return	int				length in bytes
	 * @access	public
	 */
	function get_header_size( $which = HTTPNAV_CURRENT )
	{
		if ( $which == HTTPNAV_CURRENT )
			$info = &$this->_cur;
		else if ( $which == HTTPNAV_LAST )
			$info = &$this->_last;
		else
			return false;

		if ( isset( $info[HTTPNAV_RESPONSE][HTTPNAV_HEADER] ) )
			return strlen( $info[HTTPNAV_RESPONSE][HTTPNAV_HEADER] );
		else
			return false;
	}

	/**
	 * Get body.
	 *
	 * Returns body as string
	 *
	 * @param	string	$type	either HTTPNAV_RESPONSE or HTTPNAV_REQUEST, default: HTTPNAV_RESPONSE
	 * @param	string	$which	either HTTPNAV_CURRENT for current or HTTPNAV_LAST for last, default: HTTPNAV_CURRENT
	 * @return	string			body
	 * @access	public
	 */
	function &get_body( $type = HTTPNAV_RESPONSE, $which = HTTPNAV_CURRENT )
	{
		if ( $which == HTTPNAV_CURRENT )
			$info = &$this->_cur;
		else if ( $which == HTTPNAV_LAST )
			$info = &$this->_last;
		else
			return false;
	
		return $info[$type][HTTPNAV_BODY];
	}

	/**
	 * Get headers.
	 *
	 * Returns headers as string
	 *
	 * @param	string	$type	either HTTPNAV_RESPONSE or HTTPNAV_REQUEST, default: HTTPNAV_RESPONSE
	 * @param	string	$which	either HTTPNAV_CURRENT for current or HTTPNAV_LAST for last, default: HTTPNAV_CURRENT
	 * @return	string			headers
	 * @access	public
	 * @see		explode_headers()
	 */
	function &get_headers( $type = HTTPNAV_RESPONSE, $which = HTTPNAV_CURRENT )
	{
		if ( $which == HTTPNAV_CURRENT )
			$info = &$this->_cur;
		else if ( $which == HTTPNAV_LAST )
			$info = &$this->_last;
		else
			return false;
		
		return $info[$type][HTTPNAV_HEADER];
	}

	/**
	 * Explode headers.
	 *
	 * Returns associative array with each header as an array element,
	 * with the header field name as the key.
	 *
	 * @param	string	$type	either HTTPNAV_RESPONSE or HTTPNAV_REQUEST, default: HTTPNAV_RESPONSE
	 * @param	string	$which	either HTTPNAV_CURRENT for current or HTTPNAV_LAST for last, default: HTTPNAV_CURRENT
	 * @return	array			associative array
	 * @access	public
	 * @see		get_headers()
	 */
	function explode_headers($type = HTTPNAV_RESPONSE, $which = HTTPNAV_CURRENT )
	{
		if ( $which == HTTPNAV_CURRENT )
			$info = &$this->_cur;
		else if ( $which == HTTPNAV_LAST )
			$info = &$this->_last;
		else
			return false;
		
		$ret = array();
		$exploded = explode( $this->_nl_n, $info[$type][HTTPNAV_HEADER] );
		
		foreach ( $exploded as $line )
		{
			if ( substr_count( $line, ':' ) > 0 )
			{
				list( $key, $val ) = explode( ':', $line, 2 );
				$ret[strtolower( trim( $key ) )] = trim( $val );
			}
		}
		
		return $ret;
	}

	/**
	 * Add / edit HTTP header.
	 *
	 * Adds header and value, or modifies existing header field if it exists.
	 * You can specify if you want the header to appear in all requests
	 * or only for the next request.
	 * Using this will not allow you have mutliple headers of the same name,
	 * You should use a comma to seperate the values.
	 *
	 * @param	string	$name	HTTP header field name (stored as array key)
	 * @param	string	$value	HTTP header value
	 * @param	bool	$sticky	(optional) will use header and value for further requests (if true)
	 * @param	bool	$only_if_empty	(optional) will only add if it does not already exist
	 * @return	bool
	 * @access	public
	 */
	function add_http_header( $name, $value, $sticky = false, $only_if_unset = false )
	{
		$sticky = $sticky? 1 : 0;

		if ( trim( $name ) != '' )
		{
			$name  = trim( $name  );
			$value = trim( $value );
			
			if ( isset( $this->_http_header_field[strtolower( trim( $name ) )] ) )
				$num_key = $this->_http_header_field[strtolower( trim( $name ) )];
			else
				return false;
			
			if ( isset( $this->_http_header[$num_key] ) && $only_if_unset )
				return false;
			
			$this->_http_header[$num_key][$sticky]["$name"] = "$value";
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Delete HTTP header.
	 *
	 * Builds string containing each HTTP header field and value, found in $this->_http_header,
	 * on a seperate line.
	 *
	 * @return	string	contains each HTTP header field and value on seperate lines
	 * @access	public
	 */
	function del_http_header( $name )
	{
		$name = trim( $name );
		
		if ( $name == 'all' )
		{
			$this->_http_header = array();
		}
		else if ( $name == 'all-non-sticky' )
		{
			// loop through and remove non sticky headers
			foreach ( $this->_http_header as $key => $val )
			{
				if ( !key( $val ) )
					unset( $this->_http_header[$key] );
			}
		}
		else
		{
			// find this header position number
			if ( isset( $this->_http_header_field[strtolower( $name )] ) )
				$del_key = $this->_http_header_field[strtolower( $name )];
			else
				return false;
			
			if ( isset( $this->_http_header[$del_key] ) )
				unset( $this->_http_header[$del_key] );
			else
				return false;
		}
		
		return true;
	}

	/**
	 * Set Basic Auth user and pass.
	 *
	 * sets username or password in $this->_cur['basic_auth']
	 *
	 * @param	string	$user	username
	 * @param	string	$pass	password (default '')
	 * @return	bool			false if both user and pass empty, true otherwise
	 * @access	public
	 */
	function set_basic_auth_user( $user, $pass = '' )
	{
		if ( ( !empty( $user ) ) || ( !empty( $pass ) ) )
		{
			$this->_cur['basic_auth'] = array();
			$this->_cur['basic_auth']['user'] = ( !empty( $user )? $user : '' );
			$this->_cur['basic_auth']['pass'] = ( !empty( $pass )? $pass : '' );
			
			return true;
		}
		
		return false;
	}

	/**
	 * Check if valid URL.
	 *
	 * Checks to see if URL contains the necessary parts (scheme and host).
	 *
	 * @param	string	$url	full URL to check for validity
	 * @return	bool			true if valid, false otherwise
	 * @access	public
	 */
	function is_valid_url( $url )
	{
		$url = trim( $url );
		
		if ( !empty( $url ) )
		{
			$split = @parse_url( $url );
			
			if ( isset( $split['scheme'] ) && isset( $split['host'] ) )
			{
				if ( strtolower( $split['scheme'] ) == 'http' )
					return true;
				else if ( ( strtolower( $split['scheme']) == 'https' ) && $this->curl_ssl )
					return true;
			}
		}
		
		return false;
	}

	/**
	 * Split URL.
	 *
	 * Returns exploded parts of url (uses default values for some parts) in associative array.
	 *
	 * @param	string	$url	full URL to explode
	 * @return	array			exploded parts of URL, same associative keys as parse_url(),
	 *							returns false if URL is not valid
	 * @access	public
	 */
	function split_url( $url )
	{
		$url = trim( $url );
		
		if ( $this->is_valid_url( $url ) )
		{
			$matches = @parse_url( $url );
			$matches['port'] = ( !empty( $matches['port'] ) )? $matches['port'] : $this->http_port;
			$matches['path'] = ( !empty( $matches['path'] ) )? trim( $matches['path'] ) : '/';

			return $matches;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Get cookie domains.
	 *
	 * Compares $host to domains stored in $this->cookie (stored as keys),
	 * it tries to match $host to the end of each domain, so if a site stores a cookie
	 * with the domain attribute set to .example.com and we're searching to find
	 * www.example.com or support.example.com then .example.com will match and be stored in the
	 * array.
	 *
	 * @param	string	$host	domain to match, eg. support.example.com
	 * @return	array			array of domain matches found
	 * @access	public
	 */
	function get_cookie_domains( $host )
	{
		$host = strtolower( $host );
		$matches = array();
		
		foreach ( $this->cookie as $domain => $domain_val )
		{
			if ( preg_match( '!' . preg_quote( $domain ) . '$!', $host ) )
				$matches[] = $domain;
		}
		
		return $matches;
	}

	/**
	 * Get cookie.
	 *
	 * Returns string with cookie names and values matching arguments passed to it in array, or,
	 * if empty string passed (default), current host and path ($this->_cur['host'] $this->_cur['path'])
	 * will be used to find the cookies.
	 *
	 * @param	array	$array	associative array with keys ('domain', 'path', 'name', 'secure')
	 *							if passing an array, the 'domain' key is required
	 * @return	string	cookie names and values (eg. NAME1=VALUE1; NAME2=VALUE2 ...)
	 * @access	public
	 */
	function get_cookie( $array = '' )
	{
		// sample cookie
		// NAME=VALUE; path=/;  domain=.mydomain.com;  expires=Wednesday, 31-Dec-2010 12:10:00 GMT; 

		if ( !$this->cookie_send )
			return false;

		$cookies = array();
		$tmp_cookies = array();

		if ( !is_array( $array ) && empty( $array ) )
		{
			$array = array(
				'domain' => $this->_format_cookie_domain( $this->_cur['host'] ),
				'path'   => $this->_format_cookie_path( $this->_cur['path'] )
			);
		}
		
		if ( !is_array( $array ) )
			return false;
		
		// set secure flag to false (don't include cookies marked for secure communication)
		if ( !isset( $array['secure'] ) )
			$array['secure'] = false;
		
		// extract vars from array and prefix them
		extract( $array, EXTR_PREFIX_ALL, 'cookie' );

		if ( !isset( $cookie_domain ) )
			return false;

		// grab all cookies in domain (required)
		$cookie_domain_matches = $this->get_cookie_domains( $cookie_domain );
		
		if ( count( $cookie_domain_matches ) == 0 )
			return false;
		
		foreach ( $cookie_domain_matches as $domain_match )
			$cookies["$domain_match"] = $this->cookie["$domain_match"];

		// grab all cookies in path (if set)
		if ( isset( $cookie_path ) )
		{
			// find cookies where path begins with path passed by argument
			// return the values of matching paths into a new array
			foreach ( $cookies as $dom => $dom_value )
			{
				foreach ( $dom_value as $path => $path_value )
				{
					if ( preg_match( '!^' . preg_quote( $path ) . '!', $cookie_path ) )
						$tmp_cookies["$dom"]["$path"] = $path_value;
				}
			}
			
			$cookies = $tmp_cookies;
			
			if ( count( $cookies ) == 0 )
				return false;
		}

		// grab cookie with this name
		if ( isset( $cookie_name ) )
		{
			// find cookies which match this name
			$tmp_cookies = array();
			
			foreach ( $cookies as $dom => $dom_value )
			{
				foreach ( $dom_value as $path => $path_value )
				{
					if ( is_array( $path_value["$cookie_name"] ) )
						$tmp_cookies["$dom"]["$path"]["$cookie_name"] = $path_value["$cookie_name"];
				}
			}
			
			$cookies = $tmp_cookies;
			
			if ( count( $cookies ) == 0 )
				return false;
		}
		
		// no need to sort by domain length :)
		// sort cookies by domain
		//   uksort($cookies, array($this, '_cmp_length'));
		// reverse cookies array
		//   $cookies = array_reverse($cookies, true);

		// copy paths over
		$tmp_cookies = array();
		
		foreach ( $cookies as $dom => $dom_value )
		{
			foreach ( $dom_value as $path => $path_value )
				$tmp_cookies["$path"] = $path_value;
		}
		
		$cookies = $tmp_cookies;
		
		// sort cookies by path string length
		uksort( $cookies, array( $this, '_cmp_length' ) );

		// return cookies
		$tmp_cookies = array();
		
		foreach ( $cookies as $path => $path_val )
		{
			foreach ( $path_val as $name => $name_val )
			{
				// exclude secure cookies, unless current site is SSL
				if ( ( !$name_val['secure'] ) || ( $name_val['secure'] && $this->_cur['scheme'] == 'https' ) || ( isset( $cookie_secure ) && $cookie_secure ) )
					$tmp_cookies[] = $name.'='.$name_val['value'];
			}
		}
		
		$tmp_cookies = implode( '; ', $tmp_cookies );
		return $tmp_cookies;
	}

	/**
	 * Delete old cookies.
	 *
	 * Deletes cookies older than $this->cookie_life and cookies which have expired.
	 * Note: if $this->cookie_life is set to 0 the number of deleted cookies will not be
	 * returned, instead true will be returned, you can test with === true (3 equal signs)
	 * to see if all cookies where deleted.
	 *
	 * @param	bool	$session_cookies	if true and a cookie has not expired or exceeded cookie lifetime,
	 *										but is marked as a session cookie, it will be deleted.
	 *										If this is false, the cookie lives on until it has exceeded
	 *										$this->cookie_lifetime, so to delete session cookies you would want
	 *										this value to be true (this is only confusing because we are not a
	 *										browser so it's up to you to determine when the session has ended).
	 * @return	int							number of cookies deleted, or true if all cookies deleted
	 * @access	public
	 * @see		end()
	 */
	function del_old_cookies( $session_cookies = true )
	{
		if ( $this->cookie_life == 0 )
		{			
			$this->cookie = array();
			return true;
		}

		$deleted = 0;

		if ( count( $this->cookie ) == 0 )
			return $deleted;

		foreach ( $this->cookie as $domain => $domain_val )
		{
			foreach ( $domain_val as $path => $path_val )
			{
				foreach ( $path_val as $cookie => $cookie_val )
				{
					if ( ( time() - (int)$cookie_val['date_added']) > $this->cookie_life )
					{
						unset( $this->cookie["$domain"]["$path"]["$cookie"] );
						$deleted++;
					}
					else if ( isset( $cookie_val['expires'] ) && is_int( $cookie_val['expires'] ) && ( time() > $cookie_val['expires'] ) )
					{
						unset ($this->cookie["$domain"]["$path"]["$cookie"] );
						$deleted++;
					}
					else if ( !isset( $cookie_val['expires'] ) && $session_cookies )
					{
						unset( $this->cookie["$domain"]["$path"]["$cookie"] );
						$deleted++;
					}
					
					// delete path if empty
					if ( count( $this->cookie["$domain"]["$path"] ) == 0 )
						unset( $this->cookie["$domain"]["$path"] );
					
					// delete domain if empty
					if ( count( $this->cookie["$domain"] ) == 0 )
						unset( $this->cookie["$domain"] );
				}
			}
		}
		
		return $deleted;
	}

	/**
	 * Delete old Basic Auth info.
	 *
	 * Deletes Basic Auth details older than $this->basic_auth_life.
	 * Note: if $this->basic_auth_life is set to 0 the number of details deleted will not be
	 * returned, instead true will be returned, you can test with === true (3 equal signs)
	 * to see if all details where deleted.
	 *
	 * @return	int		number of basic auth details deleted, or true if all deleted
	 * @access	public
	 * @see		end()
	 */
	function del_old_basic_auth()
	{
		if ( $this->basic_auth_life == 0 )
		{
			$this->basic_auth = array();
			return true;
		}

		$deleted = 0;

		if ( count( $this->basic_auth ) == 0 )
			return $deleted;
		
		// $this->basic_auth[$this->_cur['host']][$realm]['date_added']
		foreach ( $this->basic_auth as $host => $host_val )
		{
			foreach ( $host_val as $realm => $realm_val )
			{
				if ( ( time() - (int)$realm_val['date_added'] ) > $this->basic_auth_life )
				{
					unset( $this->basic_auth["$host"]["$realm"] );
					$deleted++;
				}
				
				// delete if host is empty
				if ( count( $this->basic_auth["$host"] ) == 0 )
					unset( $this->basic_auth["$host"] );
			}
		}
		
		return $deleted;
	}

	/**
	 * Deletes cookies in $this->cookie which match the details passed to $array (must
	 * be an associative array with keys "domain", "path", "name" - "domain" is required).
	 *
	 * @param	array	$array	associative array with keys 'domain' (required), 'path', 'name'
	 * @return	bool			true if cookie(s) removed, false otherwise
	 * @access	public
	 */
	function del_cookie( $array )
	{
		// sample cookie
		// NAME=VALUE; path=/;  domain=.mydomain.com;  expires=Wednesday, 31-Dec-2010 12:10:00 GMT; 

		$cookies = array();
		$delete  = array();

		if ( !is_array( $array ) )
			return false;

		// extract vars from array and prefix them  
		extract( $array, EXTR_PREFIX_ALL, 'cookie' );

		if ( !isset( $cookie_domain ) )
			return false;

		// check domain for cookies
		if ( is_array( $this->cookie[$cookie_domain] ) && count( $this->cookie[$cookie_domain] ) > 0 )
			$delete['domain'] = $cookie_domain;
		else
			return false;

		// check domain and path for cookies
		if ( isset( $cookie_path ) )
		{
			if ( is_array( $this->cookie[$cookie_domain][$cookie_path] ) && count( $this->cookie[$cookie_domain][$cookie_path] ) > 0 )
				$delete['path'] = $cookie_path;
			else
				return false;
		}

		// check name and delete
		if ( isset( $cookie_name ) )
		{
			if ( isset( $cookie_path ) )
			{
				if ( is_array( @$this->cookie[$cookie_domain][$cookie_path][$cookie_name] ) )
				{
					unset( $this->cookie[$cookie_domain][$cookie_path][$cookie_name] );
					
					// delete path if no more entries
					if ( count( $this->cookie[$cookie_domain][$cookie_path] ) == 0 )
						unset( $this->cookie[$cookie_domain][$cookie_path] );
					
					// delete domain if no more entries
					if ( count( $this->cookie[$cookie_domain] ) == 0 )
						unset( $this->cookie[$cookie_domain] );
				}
				else
				{
					return false;
				}
			}
			else
			{
				foreach ( $this->cookie[$cookie_domain] as $path => $path_val )
				{
					if ( is_array( @$this->cookie[$cookie_domain][$path][$cookie_name] ) )
					{
						unset( $this->cookie[$cookie_domain][$path][$cookie_name] );
						
						// delete path if no more entries
						if ( count( $this->cookie[$cookie_domain][$path] ) == 0 )
							unset( $this->cookie[$cookie_domain][$path] );
						
						// delete domain if no more entries
						if ( count( $this->cookie[$cookie_domain] ) == 0 )
							unset( $this->cookie[$cookie_domain] );
					}
				}
			}
			
			return true;
		}

		// delete path
		if ( isset( $delete['path'] ) )
		{
			unset( $this->cookie[$delete['domain']][$delete['path']] );
			
			// delete domain if no more entries
			if ( count( $this->cookie[$delete['domain']] ) == 0 )
				unset( $this->cookie[$delete['domain']] );
			
			return true;
		}

		// delete domain
		unset( $this->cookie[$delete['domain']] );
		return true;
	}

	/**
	 * POST URL
	 *
	 * Calls $this->_process() passing along data to post.
	 *
	 * @param	string	$formdata	data to post (eg. 'name=john&age=22&foo=bar'), or array:
	 *								(eg. 'key'=>'val', 'name'=>'john', 'age'=>'22', 'foo'=>'bar')
	 * @param	string	$url		full URL to post to, $this->_process() will then pass it to
	 *								$this->new_site(), (if not supplied, current site details used)
	 * @return	array				contains HTTP head and body (if returned) with keys HTTPNAV_HEADER and HTTPNAV_BODY
	 * @access	public
	 */
	function post_url( $formdata, $url = '' )
	{
		if ( is_array( $formdata ) )
		{
			$formdata_str = '';
			
			foreach ( $formdata as $key => $val )
				$formdata_str .= urlencode( $key ) . '=' . urlencode( $val ) . '&';
			
			if ( !empty( $formdata_str ) )
				$formdata_str = substr( $formdata_str, 0, -1 );
			
			$formdata = $formdata_str;
		}

		return $this->_process( 'POST', $formdata, $url );
	}

	/**
	 * GET URL
	 *
	 * Calls $this->_process() to fetch URL as a GET request.
	 *
	 * @param	string	$url	full URL to fetch, $this->_process() will then pass it to
	 *							$this->new_site(), (if not supplied, current site details used)
	 * @return	array			contains HTTP head and body (if returned) with keys HTTPNAV_HEADER and HTTPNAV_BODY
	 * @access	public
	 */
	function get_url( $url = '' )
	{
		return $this->_process( 'GET', '', $url );
	}

	/**
	 * HEAD URL
	 *
	 * Calls $this->_process() to ask server for HTTP headers only (no body should be returned).
	 *
	 * @param	string	$url	full URL, $this->_process() will then pass it to
	 *							$this->new_site(), (if not supplied, current site details used)
	 * @return	array			contains HTTP headers with key HTTPNAV_HEADER
	 * @access	public
	 */
	function head_url( $url = '' )
	{
		return $this->_process( 'HEAD', '', $url );
	}

	/**
	 * Storage action
	 *
	 * Save, Load or Delete storage files from disk
	 *
	 * @param	string	$action		'save', 'load', 'delete'
	 * @return	bool
	 * @access	public
	 */
	function obj_storage_action( $action )
	{
		if ( !empty( $this->store_name ) )
		{
			if ( !is_object( $this->_obj_storage ) )
				$this->_obj_storage = new HTTPStorage;
			
			switch ( $action )
			{
				case 'save':
					$result = $this->_obj_storage->save_files( $this );
					break;
				
				case 'load':
					$result = $this->_obj_storage->load_files( $this );
					break;

				case 'delete':
					$result = $this->_obj_storage->del_files( $this );
					break;

				default:
					return PEAR::raiseError( "Unknown action '$action'" );
			}
			
			return $result;
		}
		
		return false;
	}

	/**
	 * END
	 *
	 * Use when finished with the class, if disk storage enabled, vars will be written to disk
	 *
	 * @param	bool	$session_cookies	see del_old_cookies() for an explanation
	 * @return	bool
	 * @access	public
	 */
	function end( $session_cookies = true )
	{
		$this->del_old_cookies( $session_cookies );
		$this->del_old_basic_auth();

		// write vars to disk
		if ( !empty( $this->store_name ) )
		{
			if ( $this->obj_storage_action( 'save' ) )
				return true;
			else
				return false;
		}
	}
	
	// private methods
	
	/**
	 * Raise error / warning
	 *
	 * @param	int		$code	One of the HTTPNAV_ERR_* constants
	 * @param	string	$text	(optional) extra info about error
	 * @return	array			Associative array with keys: 'errno' and 'error'
	 * @access	private
	 */
	function _error( $code = HTTPNAV_ERR_UNKNOWN, $text = '' )
	{
		static $error;
		
		if ( !isset( $error ) )
		{
			$error = array(
				// reject
				HTTPNAV_WRN_MIME_REJECTED	=> 'MIME content-type rejected',
				HTTPNAV_WRN_MATCH_REJECTED	=> 'Header regex match rejected',
				HTTPNAV_WRN_BODY_SIZE		=> 'Body truncated',
				// error
				HTTPNAV_ERR_CONNECT			=> 'Connecting',
				HTTPNAV_ERR_TIMED_OUT		=> 'Connection timed out',
				HTTPNAV_ERR_URL_FORMAT		=> 'Incorrect URL format',
				HTTPNAV_ERR_HEADER_SIZE		=> 'Invalid header size',
				HTTPNAV_ERR_HEADER_STATUS	=> 'Invalid header status line',
				HTTPNAV_ERR_CURL_INIT		=> 'Unable to initialize cURL',
				HTTPNAV_ERR_CURL_UNKNOWN	=> 'Unknown cURL error',
				HTTPNAV_ERR_UNKNOWN			=> 'Unknown'
			);
		}
		
		$text = ( !empty( $text )? @$error[$code] . ' - ' . $text : @$error[$code] );
		return PEAR::raiseError( $text );
	}

	/**
	 * Build HTTP headers.
	 *
	 * Builds string containing each HTTP header field and value, found in $this->_http_header,
	 * on a seperate line.
	 *
	 * @return	string	contains each HTTP header field and value on seperate lines
	 * @access	private
	 */
	function _build_http_headers()
	{
		$headers = array();
		ksort( $this->_http_header, SORT_NUMERIC );

		foreach ( $this->_http_header as $val )
		{
			$val = $val[key( $val )];
			$headers[] = key( $val ) . ': ' . $val[key( $val )];
		}
		
		return implode( $this->_nl_rn, $headers );
	}

	/**
	 * Set current URL (splits URL string into parts, and stores).
	 *
	 * Splits URL into parts and stores those parts in the appropriate
	 * vars.
	 *
	 * @param	string	$url	full URL to set as current
	 * @return	bool			returns false if $url is not valid or cannot be split properly
	 * @access	private
	 * @see		new_site()
	 */
	function _set_cur_url( $url )
	{
		$url = trim( $url );
		
		if ( !$this->is_valid_url( $url ) )
			return false;

		if ( $split = $this->split_url( $url ) )
		{
			$this->_cur['scheme'] = strtolower( $split['scheme'] );
			$this->_cur['host']   = strtolower( $split['host']   );
			$this->_cur['port']   = ( isset( $split['port'] )? $split['port'] : $this->http_port );
			$this->_cur['path']   = $split['path'] . ( !empty( $split['query'] )? '?' . $split['query'] : '' );
			$this->_cur['url']    = $this->rebuild_cur_url();
			
			// set basic auth
			if ( $this->basic_auth_save && ( !empty( $split['user'] ) || !empty( $split['pass'] ) ) )
			{
				$this->_cur['basic_auth'] = array();
				$this->_cur['basic_auth']['user'] = ( !empty( $split['user'] )? $split['user'] : '' );
				$this->_cur['basic_auth']['pass'] = ( !empty( $split['pass'] )? $split['pass'] : '' );
			}
			
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Set last URL.
	 *
	 * Clears last site details, then adds current site details as the last details,
	 * also stores these details in $this->site array.
	 *
	 * @return	bool
	 * @access	private
	 * @see		new_site()
	 */
	function _set_last_url()
	{
		// clear last URL
		$this->clear( HTTPNAV_LAST );

		// copy current info to last info array
		$this->_last = $this->_cur;

		// add current url to site history
		$this->site[$this->_cur_id] = $this->_cur['url'];

		return true;
	}
	
	/**
	 * Reset vars.
	 *
	 * Resets a few private vars
	 *
	 * @access	private
	 * @see		new_site()
	 */
	function _reset_vars()
	{
		$this->_time_start	   = null;
		$this->_time_end	   = null;
		$this->_was_redirect   = false;
		$this->_send_with_auth = false;
		$this->_realm		   = null;
		$this->_curl_got_head  = false;
		
		return true;
	}

	/**
	 * Set cookie.
	 *
	 * Split set-cookie http header and store it in $this->cookie array, will also remove
	 * expired cookies from $this->cookie array.
	 *
	 * @param	string	$cookie set-cookie header (name, path, domain, etc..)
	 * @return	bool			true if cookie is set, false otherwise
	 * @access	private
	 * @see		_parse_http_header()
	 */
	function _set_cookie( $cookie )
	{
		// sample cookie
		//   NAME=VALUE; path=/;  domain=.mydomain.com;  expires=Wednesday, 31-Dec-2010 12:10:00 GMT;
		// cookie spec
		//   http://home.netscape.com/newsref/std/cookie_spec.html

		if ( !$this->cookie_save )
			return false;

		$cur_cookie = array();
		$cur_cookie['secure'] = false;

		$cookie = trim( $cookie );
		
		if ( substr( $cookie, -1 ) == ';' )
			$cookie = substr( $cookie, 0, -1 );
			
		$cookie = explode( ';', $cookie, 2 );
		$cookie_attributes = trim( $cookie[1] );
		$cookie_name_val   = explode( '=', $cookie[0], 2 );

		// check if secure cookie
		if ( ( substr_count( $cookie_attributes, ' secure' ) > 0 ) || ( substr_count( $cookie_attributes, ';secure' ) > 0 ) )
			$cur_cookie['secure'] = true;

		// Name
		$cur_cookie['name'] = trim( $cookie_name_val[0] );
		
		if ( $cur_cookie['name'] == '' )
			return false;

		// Value
		$cur_cookie['val'] = trim( $cookie_name_val[1] );
		
		if ( $cur_cookie['val'] == '' )
			return false;
		
		// Domain
		$cur_cookie['domain'] = $this->_cur['host'];
		
		if ( preg_match( '/domain=([^;]+)/i', $cookie_attributes, $matches ) )
		{
			if ( trim( $matches[1] ) != '' )
				$cur_cookie['domain'] = trim( $matches[1] );
			
			unset( $matches );
		}
		
		$cur_cookie['domain'] = $this->_format_cookie_domain( $cur_cookie['domain'] );

		// Path
		$cur_cookie['path'] = $this->_cur['path'];
		
		if ( preg_match( '/path=([^;]+)/i', $cookie_attributes, $matches ) )
		{
			if ( trim( $matches[1] ) != '' )
				$cur_cookie['path'] = trim( $matches[1] );
			
			unset( $matches );
		}
		
		$cur_cookie['path'] = $this->_format_cookie_path( $cur_cookie['path'] );

		// Expires
		if ( preg_match( '!expires=[a-z, ]*([0-9]{1,2}[- ][a-z]+[- ][0-9]{2,4} +[0-9]{2}:[0-9]{2}:[0-9]{2})!i', $cookie_attributes, $matches ) )
		{
			if ( strtotime( $matches[1] ) < time() )
			{
				$this->del_cookie(
					array(
						'domain' => $cur_cookie['domain'], 
						'path'   => $cur_cookie['path'], 
						'name'   => $cur_cookie['name']
					)
				);
				
				return false;
			}
			else
			{
				$cur_cookie['expires'] = strtotime( $matches[1] );
			}
		}
		
		// set domain
		if ( !isset( $this->cookie[$cur_cookie['domain']] ) )
			$this->cookie[$cur_cookie['domain']] = array();
		
		// set path
		if ( !isset( $this->cookie[$cur_cookie['domain']][$cur_cookie['path']] ) )
			$this->cookie[$cur_cookie['domain']][$cur_cookie['path']] = array();
		
		// set name
		if ( !isset( $this->cookie[$cur_cookie['domain']][$cur_cookie['path']][$cur_cookie['name']] ) )
			$this->cookie[$cur_cookie['domain']][$cur_cookie['path']][$cur_cookie['name']] = array();
		
		// set secure flag
		$this->cookie[$cur_cookie['domain']][$cur_cookie['path']][$cur_cookie['name']]['secure'] = $cur_cookie['secure'];
		
		// set value
		$this->cookie[$cur_cookie['domain']][$cur_cookie['path']][$cur_cookie['name']]['value'] = $cur_cookie['val'];
		
		// set expires
		if ( isset( $cur_cookie['expires'] ) )
			$this->cookie[$cur_cookie['domain']][$cur_cookie['path']][$cur_cookie['name']]['expires'] = $cur_cookie['expires'];
		
		// include current date
		$this->cookie[$cur_cookie['domain']][$cur_cookie['path']][$cur_cookie['name']]['date_added'] = time();

		return true;
	}

	/**
	 * Start timer
	 *
	 * @access   private
	 */
	function _timer_start()
	{
		list($msec, $sec) = explode( ' ', microtime() );
		$this->_time_start = ( (float)$msec + (float)$sec );
		$this->_time_end = false;
		
		return true;
	}

	/**
	 * Stop timer
	 *
	 * @access private
	 */
	function _timer_stop()
	{
		list( $msec, $sec ) = explode( ' ', microtime() );
		$this->_time_end = ( (float)$msec + (float)$sec );
		
		return true;
	}

	/**
	 * Time taken
	 *
	 * Based on start and stop time, will return how long the process took (seconds)
	 *
	 * @return	double	Time taken between $this->_time_start and $this->_time_end (seconds)
	 * @access	private
	 */
	function _timer_current()
	{
		if ( $this->_time_start === false || $this->_time_start == '' )
			return false;
		else if ( $this->_time_end === false || $this->_time_end == '' )
			$this->_timer_stop();

		$current = $this->_time_end - $this->_time_start;
		return (double)sprintf( '%.10f', $current );
	}

	/**
	 * Process HTTP request.
	 *
	 * Core function, will build a HTTP request, submit to server, grab results,
	 * process results, update current vars, time the operation
	 *
	 * @param	string	$type		type of HTTP request (can only be 'GET', 'POST' or 'HEAD'
	 * @param	string	$formdata	if $type is 'POST', supply the post value here
	 * @param	string	$url		full URL, if provided will set as new site, if not provided, will use existing
	 * @return	array				if successful will return associative array (keys: HTTPNAV_HEADER, HTTPNAV_BODY) containing results
	 * @access	private
	 * @see		get_url()
	 */
	function _process( $type = '', $formdata = '', $url='' )
	{
		$return = true;

		if ( ( $type = strtoupper( $type ) ) && ( $type == 'GET' || $type == 'POST' || $type == 'HEAD' ) )
		{
			;
		}
		else
		{
			return false;
		}

		if ( !empty( $url ) )
		{
			if ( !$this->new_site( $url ) )
				return $this->_error( HTTPNAV_ERR_URL_FORMAT );
		}

		// set referer
		if ( $this->referer_use_last && empty( $this->referer ) )
			$this->referer = @$this->_last['url'];

		$this->_create_http_header();

		if ( $type == 'POST' )
		{
			$this->add_http_header( 'Content-Type',  'application/x-www-form-urlencoded' );
			$this->add_http_header( 'Content-Length', strlen( $formdata ) );
		}

		// begin building http header string
		$req  = "$type {$this->_cur['path']} HTTP/{$this->http_version}" . $this->_nl_rn;
		$req .= $this->_build_http_headers();
		$req .= $this->_nl_rn . $this->_nl_rn . $formdata;

		// unset referer
		if ( $this->referer_use_last )
			$this->referer = '';

		$this->_set_http_request( $req );

		// start timer
		if ( empty( $this->_time_start ) )
			$this->_timer_start();

		if ( ( $this->curl) || ( $this->curl_ssl && $this->_cur['scheme'] == 'https' ) )
		{
			// with cURL
			do
			{
				if ( $this->curl_head_first && !$this->_curl_got_head )
				{
					$this->_curl_got_head = true;
					$result = $this->_process_with_curl( $req, $type, $formdata, false );
				}
				else
				{
					$this->_curl_got_head = false;
					$result = $this->_process_with_curl( $req, $type, $formdata, true );
				}
				
				if ( PEAR::isError( $result ) )
					return $result;
				
				// parse response header, return false if there's a problem
				if ( ( $this->curl_head_first && $this->_curl_got_head ) || !$this->curl_head_first )
				{
					$parse_res = $this->_parse_http_header( $result );
					
					if ( PEAR::isError( $parse_res ) )
						return $parse_res;
				}
			} while ( $this->curl_head_first && $this->_curl_got_head );
		}
		else
		{
			// without cURL
			if ( !$this->_open_socket( $fp ) )
				return $this->_error( HTTPNAV_ERR_CONNECT );
			
			if ( $this->read_timeout )
				@socket_set_timeout( $fp, $this->read_timeout );
			
			// send request
			fwrite( $fp, $req );

			// check if timeout exceeded
			if ( $this->_timed_out( $fp ) )
				return $this->_error( HTTPNAV_ERR_TIMED_OUT );

			$result = '';
			while ( !feof( $fp ) )
			{
				$line_cur  = fgets( $fp, 4096 );
				$result   .= $line_cur;
				
				if ( ( $line_cur == $this->_nl_rn ) || ( $line_cur == $this->_nl_n ) )
					break;
				else if ( strlen( $result ) > $this->max_head_bytes )
					return $this->_error( HTTPNAV_ERR_HEADER_SIZE, "exceeded limit: {$this->max_head_bytes} bytes" );
			}
			
			// check if timeout exceeded
			if ( $this->_timed_out( $fp ) )
				return $this->_error( HTTPNAV_ERR_TIMED_OUT );

			// parse response header, return false if there's a problem
			$parse_res = $this->_parse_http_header( $result );
			
			if ( PEAR::isError( $parse_res ) )
				return $parse_res;

			// read body
			if ( $this->header_only )
			{
			}
			else
			{
				$result .= fread( $fp, $this->max_body_bytes );
				
				if ( !feof( $fp ) )
					$return = $this->_error( HTTPNAV_WRN_BODY_SIZE );
			}

			// check if timeout exceeded
			if ( $this->_timed_out( $fp ) )
				return $this->_error( HTTPNAV_ERR_TIMED_OUT );

			$this->_close_socket( $fp );
		}

		// stop timer
		$this->_timer_stop();
		$this->_cur['time_taken'] = $this->_timer_current();
		
		$this->_set_http_response( $result );
		$this->del_http_header( 'all-non-sticky' );

		if ( $this->parse_body )
			$this->_parse_http_body();
	
		// if basic auth required, if we have it, then send it
		if ( $this->basic_auth_send && intval( $this->_cur['status_c'] ) == 401 && $this->_send_with_auth && $this->_check_basic_auth() )
		{
			if ( $type == 'GET' )
				$this->get_url();
			else if ( $type == 'POST' )
				$this->post_url();
			else
				$this->head_url();
		}

		// if redirect found, follow it
		if ( $this->redirect_auto && !empty( $this->_cur['redirect'] ) )
			$return = $this->_auto_redirect();
		
		$this->_cur_redirect_auto = 0;
		return $return;
	}

	/**
	 * Process with cURL.
	 *
	 * Send request with cURL (must be used for SSL)
	 *
	 * @param	string	$request	full http request string (built in _process())
	 * @param	string	$type		request type (eg. 'POST')
	 * @param	string	$formdata	data to send with POST request
	 * @param	bool	$with_body	indicate wether cURL should return response body or not,
	 *								if $this->header_only is true, this is ignored
	 * @access	private
	 * @see		_process()
	 */
	function _process_with_curl( $request = '', $type = '', $formdata = '', $with_body = null )
	{
		if ( !Util::extensionExists( 'curl' ) )
			return PEAR::raiseError( "cURL extension not available, please disable cURL usage." );

		// initialize
		$ch = @curl_init();
		
		// check for error
		if ( !$ch )
			return $this->_error( HTTPNAV_ERR_CURL_INIT );
		
		// set URL
		curl_setopt( $ch, CURLOPT_URL, strval( $this->_cur['url'] ) );
		
		// grab header
		curl_setopt( $ch, CURLOPT_HEADER, 1 );
		
		curl_setopt($ch, CURLOPT_MUTE, 1);
		
		// only grab header
		if ( $this->header_only || ( !is_null( $with_body ) && ( $with_body === false ) ) )
			curl_setopt( $ch, CURLOPT_NOBODY, 1 );
		
		// return transfer
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		
		// set timeout
		curl_setopt( $ch, CURLOPT_TIMEOUT, $this->socket_timeout + $this->read_timeout );
		
		// set low speed time limit
		if ( $this->curl_low_speed_time > 0 )
			curl_setopt( $ch, CURLOPT_LOW_SPEED_TIME, $this->curl_low_speed_time );
		else
			curl_setopt( $ch, CURLOPT_LOW_SPEED_TIME, $this->read_timeout );
		
		// set low speed bps limit
		if ( $this->curl_low_speed_limit > 0 )
			curl_setopt( $ch, CURLOPT_LOW_SPEED_LIMIT, $this->curl_low_speed_limit );
		
		// use custom request (built in _process())
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, strval( $request ) );
		
		// ssl version if set
		if ( $this->curl_ssl_version > 0 )
			curl_setopt( $ch, CURLOPT_SSLVERSION, $this->curl_ssl_version );
		
		// execute
		$result = curl_exec( $ch );

		if ( curl_error( $ch ) != '' )
			$result = $this->_error( HTTPNAV_ERR_UNKNOWN, curl_error( $ch ) );

		$this->_cur['curl_info'] = @curl_getinfo( $ch );

		curl_close( $ch );
		return $result;
	}

	/**
	 * If auto redirects are allowed and a Location header is found, this will be called
	 * to redirect (get_url() the new URL).  Will also pass Basic Auth details to the new URL
	 * if the last host matches the new host.
	 *
	 * @param	string	$redirect	if supplied, this URL will be the redirect destination,
	 *								otherwise, $this->_cur['redirect'] will be used.
	 * @return	bool				false if auto redirects exceed $this->redirect_auto or if both
	 *								$redirect and $this->_cur['redirect'] are empty, otherwise true.
	 * @access	private
	 * @see		_process()
	 */
	function _auto_redirect( $redirect = '' )
	{
		$return = false;

		if ( $this->_cur_redirect_auto >= $this->redirect_auto )
			return false;

		if ( empty( $redirect ) && !empty( $this->_cur['redirect'] ) )
			$redirect = $this->_cur['redirect'];
		else
			return false;

		if ( !empty( $redirect ) && $this->is_valid_url( $redirect ) )
		{
			if ( $this->redirect_as_new && $this->new_site( $redirect ) )
			{
				// redirect, setting new URL as a new site
				$this->_cur_redirect_auto++;
				$this->_was_redirect = true;
				
				// copy basic auth user and pass over to current url (if supplied in last url)
				if ( is_array( @$this->_last['basic_auth'] ) && ( $this->_cur['host'] == $this->_last['host'] ) )
					$this->_cur['basic_auth'] = $this->_last['basic_auth'];
			}
			else if ( !$this->redirect_as_new )
			{
				// redirect, without setting new site
				if ( is_array( $this->_cur['basic_auth'] ) )
				{
					$tmp_last_auth = $this->_cur['basic_auth'];
					$tmp_last_host = $this->_cur['host'];
				}
				
				if ( $this->referer_use_last )
					$this->referer = $this->_cur['url'];
				
				if ( $this->_set_cur_url( $redirect ) )
				{
					$this->_reset_vars();
					$this->_cur_redirect_auto++;
					$this->_was_redirect = true;
					
					// copy basic auth user and pass over
					if ( isset( $tmp_last_auth ) && $this->_cur['host'] == $tmp_last_host )
						$this->_cur['basic_auth'] = $tmp_last_auth;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
			
			// success
			$return = $this->get_url();
		}
		else
		{
			return false;
		}
		
		return $return;
	}

	/**
	 * Parses HTTP header
	 * stores cookies found, http status, redirect path.
	 *
	 * @param	string	$head	HTTP response header to parse
	 * @return	array			associative array containg header info (most of this info will have been written to current vars)
	 * @access	private
	 * @see		_process()
	 */
	function _parse_http_header( $head )
	{
		$head = trim( $head );
		
		if ( empty( $head ) )
			return $this->_error( HTTPNAV_ERR_HEADER_SIZE, 'Invalid size of response header' );
		
		$return = array();
		$return['cookies_set']    = 0;
		$return['cookies_found']  = 0;
		$return['status_version'] = null;
		$return['status_code']    = null;
		$return['status_phrase']  = null;
		$return['redirect_url']   = null;
		$return['redirect_path']  = null;

		$header_parts = explode( $this->_nl_n, $head );
		
		if ( preg_match( "!^HTTP/([^\\s]+)\\s+([0-9]{3})\\s+(.*)\$!i", trim( $header_parts[0] ), $matches ) )
		{
			$return['status_version'] = $matches[1];
			$return['status_code']    = $matches[2];
			$return['status_phrase']  = $matches[3];
			
			array_shift( $header_parts );
		}
		else
		{
			return $this->_error( HTTPNAV_ERR_HEADER_STATUS, 'unrecognized status line returned' );
		}

		// check against regex or mime (reject if found)
		if ( !empty( $this->reject_if_match ) || !empty( $this->reject_if_mime ) )
		{
			if ( !empty( $this->reject_if_mime ) )
			{
				$reject_mime = explode( ',', $this->reject_if_mime );
				$reject_mime = str_replace( '*', '[^/ ]+?', $reject_mime );
				$reject_mime = implode( '|', $reject_mime );
			}
			
			foreach ( $header_parts as $key => $val )
			{
				$header_parts[$key] = trim( $val );
				
				if ( !empty( $this->reject_if_mime ) && preg_match( "!^content-type:.*?(" . $reject_mime . ")(;|\\s|\$)!i", $header_parts[$key], $match ) )
					return $this->_error( HTTPNAV_WRN_MIME_REJECTED, "content-type matched: {$match[1]}" );
				else if ( !empty( $this->reject_if_match ) && preg_match( "{$this->reject_if_match}", $header_parts[$key] ) )
					return $this->_error( HTTPNAV_WRN_MATCH_REJECTED, "header line rejected: '{$header_parts[$key]}'" );
			}
		}

		// save status
		$this->_cur['status_v'] = $return['status_version'];
		$this->_cur['status_c'] = $return['status_code'];
		$this->_cur['status_p'] = $return['status_phrase'];
		
		foreach ( $header_parts as $key => $val )
		{
			if ( strtolower( substr( $val, 0, 10 ) ) == 'location: ' )
			{
				$location = trim( substr( $val, 10 ) );
			}
			else if ( $this->basic_auth_save && strtolower( substr( $val, 0, 18 ) ) == 'www-authenticate: ' )
			{
				$this->_set_basic_auth( substr( $val, 18 ) );
			}
			else if ( $this->cookie_save && strtolower( substr( $val, 0, 12 ) ) == 'set-cookie: ' )
			{
				$result = $this->_set_cookie( substr( $val, 12 ) );
				
				if ( $result )
				{
					$return['cookies_set']++;
					$return['cookies_found']++;
				}
				else
				{
					$return['cookies_found']++;
				}
			}
		}
		
		if ( !empty( $location ) )
		{
			if ( substr( $location, 0, 7 ) == 'http://' )
			{
				$return['redirect_url'] = $location;
			}
			else if ( ( substr( $location, 0, 8 ) == 'https://' ) && $this->curl_ssl )
			{
				$return['redirect_url'] = $location;
			}
			else
			{
				$location = ( $location[0] != '/' )? $this->_format_path( $this->_cur['path'] ) . '/' . $location : $location;
				$location = str_replace( '//', '/', $location );
				
				$return['redirect_path'] = $location;
			}
		}

		// save redirect if present
		if ( !empty( $return['redirect_url'] ) )
			$this->_cur['redirect'] = $return['redirect_url'];
		else if ( !empty( $return['redirect_path'] ) )
			$this->_cur['redirect'] = $this->rebuild_cur_url( array( 'path' => $return['redirect_path'] ) );

		return $return;
	}

	/**
	 * Parses body for things like html title, meta redirects, etc..
	 * Doesn't do much at the moment, but working on it.
	 *
	 * @return	bool	returns false if parsing of body is disabled ($this->parse_body) or body not found
	 * @access	private
	 * @see		_process()
	 */
	function _parse_http_body()
	{
		if ( !$this->parse_body )
			return false;

		if ( empty( $this->_cur[HTTPNAV_RESPONSE][HTTPNAV_BODY] ) )
			return false;

		// check mime type
		if ( !empty( $this->parse_if_mime ) )
		{
			$mime = explode( ',', $this->parse_if_mime );
			$mime = str_replace( '*', '[^/ ]+?', $mime );
			$mime = implode( '|', $mime );
			$header_parts = $this->explode_headers();
			
			if ( isset( $header_parts['content-type'] ) )
			{
				if ( !preg_match( "!.*?(" . $mime . ")(;|\\s|\$)!i", $header_parts['content-type'] ) )
					return false;
			}
			else
			{
				return false;
			}
		}

		// set title
		if ( $this->parse_title && preg_match( '!<title>([^<]+)</title>!i', $this->_cur[HTTPNAV_RESPONSE][HTTPNAV_BODY], $matches ) )
		{
			$matches[1] = trim( $matches[1] );
			
			if ( $matches[1] != '' )
				$this->_cur['title'] = $matches[1];
		}

		// set meta redirect
		// ex: <meta http-equiv="refresh" content="0;URL=http://blah.com"> 
		if ( $this->parse_meta_refresh && preg_match( "!<meta\\s+http-equiv\\s*=\\s*[\"']?refresh[\"']?\\s+"."content\\s*=\\s*[\"']?([0-9]+)\\s*(;\\s*URL\\s*=\\s*([^\"']*?))?[\"']?".">!i", $this->_cur[HTTPNAV_RESPONSE][HTTPNAV_BODY], $matches ) )
		{
			$delay = @intval( $matches[1] );
			
			if ( $this->redirect_meta_limit < $delay )
			{
				$this->_cur['redirect'] = '';
			}
			else if ( empty( $matches[3] ) )
			{
				$this->_cur['redirect'] = $this->_cur['url'];
			}
			else
			{
				$found = true;
				$url_parts = @parse_url( $matches[3] );
				
				// check if it's HTTP
				if ( isset( $url_parts['scheme'] ) )
				{
					if ( !$this->is_valid_url( $matches[3] ) )
						$found = false;
				}
				
				if ( $found )
				{
					// check if path is set
					if ( !isset( $url_parts['path'] ) || ( isset( $url_parts['path'] ) && $url_parts['path'] == '' ) )
						$url_parts['path'] = '/';
					
					// format path
					if ( substr( $url_parts['path'], 0, 1 ) != '/' )
					{
						$url_parts['path'] = $this->_format_path( $this->_cur['path'] ) . '/' . $url_parts['path'];
						$url_parts['path'] = str_replace( '//', '/', $url_parts['path'] );
					}
					
					// append querystring if available
					if ( isset( $url_parts['query'] ) )
						$url_parts['path'] .= '?' . $url_parts['query'];
					
					// build full url
					$this->_cur['redirect'] = $this->rebuild_cur_url( $url_parts, true );
				}
			}
		}
	}

	/**
	 * Check if socket timed out.
	 *
	 * Checks if $this->read_timeout seconds has been exceeded
	 *
	 * @param	resource	$fp	file pointer to open HTTP connection (passed by ref)
	 * @return	bool			true if $this->read_timeout has been exceeded, false otherwise
	 * @access	private
	 * @see		_process()
	 */
	function _timed_out( &$fp )
	{
		if ( $this->read_timeout && $fp )
		{
			$status = socket_get_status( $fp );
			
			if ( $status['timed_out'] )
				return true;
		}
		
		return false;
	}

	/**
	 * Close socket.
	 *
	 * Closes open HTTP connection (fclose)
	 *
	 * @param	resource	$fp	file pointer to open HTTP connection (passed by ref)
	 * @return	bool			true if fclose succeeds, false otherwise
	 * @access	private
	 * @see		_process()
	 */
	function _close_socket( &$fp )
	{
		$result = @fclose( $fp );

		if ( $result )
			return true;
		else
			return false;
	}

	/**
	 * Open socket.
	 *
	 * Opens HTTP connection to current site (or proxy server, if supplied),
	 * retries $this->socket_retry times if it fails
	 *
	 * @param	resource	$fp	file pointer (passed by ref)
	 * @return	bool			true if fsockopen succeeds, false otherwise
	 * @access	private
	 * @see		_process()
	 */
	function _open_socket( &$fp )
	{
		$retry_count = 0;

		do
		{
			if ( $this->proxy )
				$fp = @fsockopen( $this->proxy_server, $this->proxy_port, $errno, $errstr, $this->socket_timeout );
			else
				$fp = @fsockopen( $this->_cur['host'], $this->_cur['port'], $errno, $errstr, $this->socket_timeout );
			
			if ( !$fp )
			{
				$retry_count++;
				
				if ( $retry_count >= $this->socket_retry )
					return false;
				
				sleep( 1 );
			}
		} while( !$fp && $retry_count < $this->socket_retry );

		if ( !$fp )
			return false;

		return true;
	}

	/**
	 * Create HTTP headers.
	 *
	 * Based on existing settings, cookies, auth, proxy, etc.. this will
	 * create the headers to be sent out to the server
	 *
	 * @param	string	$url	if supplied will create new site and, based on that, create the headers
	 * @return	bool
	 * @access	private
	 * @see		_process()
	 */
	function _create_http_header( $url = '' )
	{
		// create new site entry if required
		if ( !empty( $url ) )
			$this->new_site( $url );

		$this->add_http_header( 'Host', $this->_cur['host'] );

		if ( !empty( $this->referer ) )
			$this->add_http_header( 'Referer', $this->referer, false, true );

		if ( $this->basic_auth_send )
		{
			if ( $this->_send_with_auth && $this->_check_basic_auth() )
				$this->add_http_header( 'Authorization', 'Basic ' . base64_encode( $this->_get_basic_auth() ), false, true );
			else if ( $basic_auth = $this->_get_basic_auth_from_path() )
				$this->add_http_header( 'Authorization', 'Basic ' . base64_encode( $basic_auth ), false, true );
		}

		$this->add_http_header( 'User-Agent',      $this->hd_user_agent,  false, true );
		$this->add_http_header( 'Accept',          $this->hd_accept,      false, true );
		$this->add_http_header( 'Accept-Language', $this->hd_accept_lang, false, true );
		
		if ( !empty( $this->hd_accept_encoding ) )
			$this->add_http_header( 'Accept-Encoding', $this->hd_accept_encoding, false, true );

		if ( $this->cookie_send && $cookies = $this->get_cookie() )
			$this->add_http_header( 'Cookie', $cookies, false, true );
		
		$this->add_http_header( 'Connection', 'close', false, true );

		if ( $this->proxy && ( ( $this->proxy_user != '' ) || ( $this->proxy_pass != '' ) ) )
			$this->add_http_header( 'Proxy-Authorization', 'Basic ' . base64_encode( $this->proxy_user . ':' . $this->proxy_pass ), false, true );

		return true;
	}

	/**
	 * Set Basic Auth.
	 *
	 * Splits Basic Auth reply header, usually received from server, stores the realm,
	 * sets domain, realm, path, user and pass in the $this->basic_auth array, flags
	 * $this->_send_with_auth as true, so next request will try and find auth details based on the
	 * realm found here and the host name.
	 *
	 * @param	string	$auth   Basic Auth header returned by server, usually with 401 status code
	 * @return	bool			returns false if last request was sent with auth (prevents loop),
	 *							if no auth details set for this site, if saving auth disabled.
	 * @access	private
	 * @see		_process()
	 */
	function _set_basic_auth( $auth )
	{
		if ( !$this->basic_auth_save )
			return false;

		if ( $this->_send_with_auth )
		{
			$this->_send_with_auth = false;
			return false;
		}

		$auth = trim( $auth );
		
		if ( preg_match( '!Basic +realm="([^"]+)"!i', $auth, $matches ) )
		{
			$realm = strval( $matches[1] );
			$this->_realm = $realm;
			$this->_send_with_auth = true;
		}
		else
		{
			return false;
		}

		if ( !is_array( $this->_cur['basic_auth'] ) )
			return false;

		if ( !@is_array( $this->basic_auth[$this->_cur['host']][$realm] ) )
			$this->basic_auth[$this->_cur['host']][$realm] = array();
		
		$this->basic_auth[$this->_cur['host']][$realm]['path'] = $this->_format_basic_auth_path( $this->_cur['path'] );
		$this->basic_auth[$this->_cur['host']][$realm]['user'] = $this->_cur['basic_auth']['user'];
		$this->basic_auth[$this->_cur['host']][$realm]['pass'] = $this->_cur['basic_auth']['pass'];
		$this->basic_auth[$this->_cur['host']][$realm]['date_added'] = time();

		return true;
	}

	/**
	 * Returns string user:pass (NOT base64 encoded at this point) for current site, based on
	 * realm and current host, $this->_realm should have been set by _set_basic_auth when 401
	 * status code was returned by server.
	 *
	 * @return	string	user and pass seperated by a colon: 'user:pass', false if sending basic
	 *					auth disabled, if $this->_realm empty, or if user and pass both empty.
	 * @access	private
	 */
	function _get_basic_auth()
	{
		if ( !$this->basic_auth_send )
			return false;

		if ( empty( $this->_realm ) )
			return false;
		
		$realm = strval( $this->_realm );
		$user  = @$this->basic_auth[$this->_cur['host']][$realm]['user'];
		$pass  = @$this->basic_auth[$this->_cur['host']][$realm]['pass'];

		if ( empty( $user ) && empty( $pass ) )
			return false;

		return "{$user}:{$pass}";
	}

	/**
	 * Returns string user:pass for current site if host matches and path matches, this is used
	 * to send user and pass straight away, rather than waiting for a 401 status code to be returned
	 * by the server.  If 401 is returned again it probably means user and pass was wrong, or we're
	 * in a different realm (in which case, if we have details of that realm, we'll try again).
	 *
	 * @return	string	user and pass returned as 'user:pass', false if sending basic auth disabled,
	 *					if no auth details found for this host and path.
	 * @access	private
	 * @see		_create_http_header()
	 */
	function _get_basic_auth_from_path()
	{
		if ( !$this->basic_auth_send )
			return false;

		if ( !isset( $this->basic_auth[$this->_cur['host']] ) )
			return false;

		$matches = array();
		foreach ( $this->basic_auth[$this->_cur['host']] as $realm => $realm_val )
		{
			if ( preg_match( '!^' . preg_quote( $realm_val['path'] ) . '!', $this->_format_basic_auth_path( $this->_cur['path'] ) ) )
			{
				// stores key as length of path, will be sorted later
				// based on this length, to find nearest match
				$matches[strlen( $realm_val['path'] )] = @$realm_val['user'] . ':' . @$realm_val['pass'];
			}
		}
		
		if ( count( $matches ) == 0 )
			return false;

		// reverse sort key so nearest path matching at the top
		krsort( $matches, SORT_NUMERIC );
		reset( $matches );
		
		return current( $matches );
	}

	/**
	 * Returns true if basic auth found for this host and this realm, false otherwise.
	 *
	 * @return	bool	true if user and pass found in $this->basic_auth
	 * @access	private
	 */
	function _check_basic_auth()
	{
		if ( empty( $this->_realm ) )
			return false;

		$realm = strval( $this->_realm );
		
		if ( !empty( $this->basic_auth[$this->_cur['host']]["$realm"]['user'] ) )
			return true;
		else if ( !empty( $this->basic_auth[$this->_cur['host']]["$realm"]['pass'] ) )
			return true;
		else
			return false;
	}

	/**
	 * Splits request in two, header and body (if body available), and stores in
	 * $this->_cur[HTTPNAV_REQUEST] as associative array with keys HTTPNAV_HEADER and HTTPNAV_BODY.
	 * Similar to $this->_set_http_response
	 *
	 * @param	string	$request	full HTTP request, usually passed by $this->_process
	 * @return	bool				false if $request empty
	 * @access	private
	 * @see		_process
	 */
	function _set_http_request( $request )
	{
		$request = trim( $request );
		
		if ( empty( $request ) )
			return false;

		$this->_cur[HTTPNAV_REQUEST] = array();
		
		if ( substr_count( $request, $this->_nl_rn . $this->_nl_rn ) > 0 )
			$request = explode( $this->_nl_rn . $this->_nl_rn, $request, 2 );
			
		if ( is_array( $request ) && trim( $request[0] ) != '' && trim( $request[1] ) != '' )
		{
			$this->_cur[HTTPNAV_REQUEST][HTTPNAV_HEADER] = trim( $request[0] );
			$this->_cur[HTTPNAV_REQUEST][HTTPNAV_BODY]   = trim( $request[1] );
		}
		else
		{
			$this->_cur[HTTPNAV_REQUEST][HTTPNAV_HEADER] = $request;
		}
		
		return true;
	}

	/**
	 * Splits response in two, header and body (if body available), and stores in
	 * $this->_cur[HTTPNAV_RESPONSE] as associative array with keys HTTPNAV_HEADER and HTTPNAV_BODY.
	 * Similar to $this->_set_http_request
	 *
	 * @param	string  $response	full HTTP response, usually passed by $this->_process
	 * @return	bool				false if $response empty
	 * @access	private
	 * @see		_process()
	 */
	function _set_http_response( $response )
	{
		$response = trim( $response );
		
		if ( empty( $response ) )
			return false;
	
		$this->_cur[HTTPNAV_RESPONSE] = array();
		
		if ( substr_count( $response, $this->_nl_rn . $this->_nl_rn ) > 0 )
			$response = explode( $this->_nl_rn . $this->_nl_rn, $response, 2 );
		else if ( substr_count( $response, $this->_nl_n . $this->_nl_n ) > 0 )
			$response = explode( $this->_nl_n . $this->_nl_n, $response, 2 );
		
		if ( is_array( $response ) && trim( $response[0] ) != '' && trim( $response[1] ) != '' )
		{
			$this->_cur[HTTPNAV_RESPONSE][HTTPNAV_HEADER] = trim( $response[0] );
			$this->_cur[HTTPNAV_RESPONSE][HTTPNAV_BODY]   = trim( $response[1] );
		}
		else
		{
			$this->_cur[HTTPNAV_RESPONSE][HTTPNAV_HEADER] = $response;
		}
		
		return true;
	}

	/**
	 * Basic formatting of domain so it can be stored in cookie array,
	 * trying to follow cookie spec: http://home.netscape.com/newsref/std/cookie_spec.html
	 *
	 * @param	string	$domain domain to format
	 * @return	string			formatted domain
	 * @access	private
	 */
	function _format_cookie_domain( $domain )
	{
		// seems netscape stores cookies with domain values of bbc.co.uk
		// even though cookie spec says it should be .bbc.co.uk
		// so I'm not bothering with 3 dots
		// http://home.netscape.com/newsref/std/cookie_spec.html
		
		if ( substr_count( $domain, '.' ) < 2 )
			$domain = '.' . $domain;
		
		return strtolower( $domain );
	}

	/**
	 * Format cookie path (for storage and future lookups).
	 *
	 * Basic formatting of path so it can be stored in cookie array,
	 * trying to follow cookie spec: http://home.netscape.com/newsref/std/cookie_spec.html
	 *
	 * @param	string	$path	path to format
	 * @return	string			formatted path
	 * @access	private
	 * @see		_format_path()
	 */
	function _format_cookie_path( $path )
	{
		return $this->_format_path( $path );
	}

	/**
	 * Format Basic Auth path (for storage and future lookups).
	 *
	 * Basic formatting of path so it can be stored in basic auth array
	 *		
	 * @param	string	$path   path to format
	 * @return	string			formatted path
	 * @access	private
	 * @see		_format_path()
	 */
	function _format_basic_auth_path( $path )
	{
		return $this->_format_path( $path );
	}

	/**
	 * Format path.
	 *
	 * Basic formatting of path so it can be stored in cookies or auth array,
	 * removes filename and querystring if present.
	 *
	 * @param	string	$path	path to format
	 * @return	string			formatted path
	 * @access	private
	 */
	function _format_path( $path )
	{
		$path = trim( $path );
		
		if ( substr( $path, 0, 1 ) != '/' )
			$path = '/' . $path;
		
		$split = @parse_url( $path );
		$path  = $split['path'];
		$path  = preg_replace( "![^/]+?\\.[^/]+?\$!", "", $path );

		return $path;
	}

	/**
	 * Compare string length (used in sorting arrays)
	 *
	 * This is only used with sort to return array based on key/value string length.
	 *
	 * @param	string  $a 
	 * @param	string  $b
	 * @return	int 0 if both are equal, -1 if length $a > $length b, 1 if length $a < $length b
	 * @access	private
	 * @see		get_cookie()
	 */
	function _cmp_length( $a, $b )
	{
		$la = strlen( strval( $a ) );
		$lb = strlen( strval( $b ) );
		
		if ( $la == $lb )
			return false;
		
		return ( ( $la > $lb )? -1 : 1 );
	}
} // END OF HTTPNavigator

?>
