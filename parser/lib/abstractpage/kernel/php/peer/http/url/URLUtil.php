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
 * @package peer_http_url
 */
 
class URLUtil
{
	/**
	 * This is a very simple function to FULLY encrypt the URL
	 * (e.g. http://%73%74%61%66%66%2e%6b%2d%64%65%73%69%67%6e%73%2e%63%6f%6d%2e%73%67/%62%61%73%69%63%61/)
	 *
	 * @access public
	 * @static
	 */
	function encrypt( $URL )
	{
		$Escaped = "";		

		for ( $i = 0; $i < strlen( $URL ); $i++ )
		{		
			if ( substr( $URL, $i, 1 ) == "/" )
			{
				$HEXConv = "/";
				$Escaped = $Escaped . $HEXConv;
				
				$i += 1;
			
				if ( $i >= strlen( $URL ) )
					break;
			}
			
			$HEXConv = DecHex( ord( substr( $URL, $i, 1 ) ) );
			$Escaped = $Escaped . "%$HEXConv";
		}

		return $Escaped;
	}
	
	/**
	 * This function returns the full URL and port of the current server.
	 * i.e. http://www.website.com:8000
	 *
	 * @access public
	 * @return full URL and port of the current server
	 */
	function getServerURL()
	{
		$protocol = ( $_SERVER( 'HTTPS' ) == 'on' )? 'https' : 'http';
		$url = $protocol . "://" . $_SERVER['HTTP_HOST'];

		if ( $_SERVER['SERVER_PORT'] != 80 )
  			$url = $url . ":" . $_SERVER['SERVER_PORT'];

		return $url;
	}

	/**	
	 * This function returns the directory path and filename of the currently
	 * executing script, i.e. /~user/directory/prog.php
	 *
	 * @access public
	 * @return directory path and filename of the currently executing script
	 */
	function getScriptPathName()
	{
		global $REDIRECT_URL;
		return isset( $REDIRECT_URL )? $REDIRECT_URL : $_SERVER["SCRIPT_NAME"]; 
	}

	/**
	 * This function returns the directory path of the currently executing script.
	 * i.e. /~user/directory
	 *
	 * @access public
	 * @return directory path of the currently executing script
	 */
	function getBasePath()
	{
		$pieces = split( '/', URLUtil::getScriptPathName() );
		$tail   = $pieces[count( $pieces ) - 1];
		
		unset( $pieces[count( $pieces ) - 1] ); 
		return join( '/', $pieces );
	}

	/**	
	 * This function returns the root directory of the currently executing script.
	 * i.e. /~user
	 *
	 * @access public
	 * @return root directory of the currently executing script
	 */
	function getRootPath()
	{
		$pieces = split( '/', URLUtil::getBasePath() );
		unset( $pieces[count( $pieces ) - 1] ); 

		return join( '/', $pieces );
	}

	/**
	 * This function returns the full URL, port, and directory path of the currently
	 * executing script, i.e. http://www.website.com:8000/~user/directory/
	 *
	 * @access public
	 * @return full URL, port, and directory path of the currently executing script
	 */
	function getBaseURL()
	{
		return URLUtil::getServerURL() . URLUtil::getBasePath() . "/";
	}

	/**
	 * This function returns the filename of the currently executing script.
	 * i.e. prog.php
	 *
	 * @access public
	 * @return filename of the currently executing script
	 */
	function getScriptName()
	{
		$pieces = split( '/', URLUtil::getScriptPathName() );
		return $pieces[count($pieces) - 1]; 
	}

	/**
	 * This function returns the filename and query associated with the currently
	 * executing script, i.e. prog.php?a=b
	 *
	 * @access public
	 * @return filename and query associated with the currently executing script
	 */
	function getScriptRequest()
	{
		return ( $_SERVER['QUERY_STRING'] == '' )? URLUtil::getScriptName() : URLUtil::getScriptName() . "?" . $_SERVER['QUERY_STRING'];
	}

	/**
	 * This function returns the full URL, port, directory path, and filename of the
	 * currently executing script, i.e. http://www.website.com:8000/~user/directory/prog.php
	 *
	 * @access public
	 * @return full URL, port, directory path, and filename of the currently executing script
	 */
	function getFullURL()
	{
		return URLUtil::getBaseURL() . URLUtil::getScriptName();
	}

	/**
	 * This function returns the full URL, port, directory path, filename, and query
	 * associated with the currently executing script,
	 * i.e. http://www.website.com:8000/~user/directory/prog.php?a=b
	 *
	 * @access public
	 * @return full URL, port, directory path, filename, and query associated
	 * 		   with the currently executing script
	 */
	function getFullRequest()
	{
		return URLUtil::getBaseURL() . URLUtil::getScriptRequest();
	}

	/**
	 * This function returns the passed URL with the passed parameters appended
 	 * together.
	 *
	 * Appending is behaving correctly both if the URL already has parameters as
	 * well as when it does not.
	 *
	 * @access public
	 * @param  $url  URL
	 * @param  $params  parameters
	 * @return passed URL with the passed parameters appended
	 */
	function getExtendedURL( $url, $params )
	{
		if ( !$params )
  			return $url;

		return $url . ( strstr( $url, '?' )? '&' : '?' ) . $params; 
	}

	/**
	 * This function submits an HTTP redirect header to the passed URL and then
	 * exits.
	 *
	 * @access public
	 * @param  $_url  URL to redirect to
	 */
	function redirect( $_url )
	{
		header( "Location: $_url" ); 
		exit;
	}

	/**
	 * Using an http redirect takes us to $_where (target page) in such a 
	 * way that the execution of stackRet() in the target page would 
	 * take us back here
	 *
	 * @access public
	 */
	function stackCall( $_where )
	{
		URLUtil::redirect( URLUtil::stackCallURL( $_where ) );
	}

	/**
	 * Using an http redirect takes us to $_where (target page) in such a 
	 * way that the execution of stackRet() in the target page would 
	 * take us to the same place that a stackRet() in our current page would
	 * take us
	 *
	 * @access public
	 */
	function stackGo( $_where )
	{
		URLUtil::redirect( URLUtil::stackGoURL( $_where ) );
	}

	/**
	 * Using an http redirect takes us to $_where (target page) in such a 
	 * way that the execution of stackRet() in the target page would 
	 * take us to $_done
	 *
	 * @access public
	 */
	function stackFwd( $_where, $_done )
	{
		URLUtil::redirect( URLUtil::stackFwdURL( $_where, $_done ) );
	}

	/**
	 * If we have been stackCall/stackGo/stackFwd -ed to the
	 * current page stackRet() will return us to url according to the
	 * rules described above
	 *
	 * @access public
 	 */
	function stackRet()
	{
		URLUtil::redirect( URLUtil::stackRetURL() );
	}

	/**
	 * Extends $_where with a parameter (.done) whose value is the url 
	 * that describes our current page and returns it. If the .done parameter is 
	 * already set n the current page, then the url describing the
	 * current page also includes the .done parameter as well
	 *
	 * @access public
	 */
	function stackCallURL( $_where )
	{
		return URLUtil::getExtendedURL( $_where, URLUtil::stackOptDone( URLUtil::getScriptRequest() ) );
	}

	/**
	 * Extends $_where with a parameter (.done) whose value is the value
	 * of $_done our current page and returns it. If .done has no value then
	 * it just returns $_where
	 *
	 * @access public
	 */
	function stackGoURL( $_where )
	{
		global $_done;
		return( URLUtil::getExtendedURL( $_where, URLUtil::stackOptDone( $_done ) ) );
	}

	/**
	 * Extends $_where with a parameter (.done) whose value is the value
	 * of the $_done function parameter and returns it
	 *
	 * @access public
	 */
	function stackFwdURL( $_where, $_done )
	{
		return( URLUtil::getExtendedURL( $_where, URLUtil::stackOptDone( $_done ) ) );
	}

	/**
	 * Returns the url specified by the .done parameter or the current
	 * url if no .done parameter is set
	 *
	 * @access public
	 */
	function stackRetURL()
	{
		global $_done;
		return ( $_done != '' )? $_done : URLUtil::getScriptRequest(); 
	}

	/**
	 * Returns the param=value combination that should be used to construct
	 * urls emanating from the current page. Used mostly internally
	 *
	 * @access public
	 */
	function stackOptDone( $_done )
	{
		return $_done? ( ".done=" . urlencode( $_done ) ) : '';
	}

	/**
	 * @access public
	 */
	function checkSyntax( $url ) 
	{
		$t = @parse_url( $url );
		return (bool)( ( is_array( $t ) ) && ( isset( $t['scheme'] ) ) && ( isset( $t['host'] ) ) );
	}

	/**
	 * @access public
	 */
	function validate( $url ) 
	{
		if ( !URLUtil::checkSyntax( $url ) ) 
			return false;
			
		$t   = @fopen( $url, 'r' );
		$ret = ( $t )? true : false;
		
		@fclose( $t );
		return $ret;
	}

	/**
	 * @access public
	 */
	function ipToNumber( $ip ) 
	{
		$quad = explode( '.', $ip );
		return $quad[0] * pow( 2, 24 ) + $quad[1] * pow( 2, 16 ) + $quad[2] * pow( 2, 8 ) + $quad[3];
	}
	
	/**
	 * @access public
	 */
	function numberToIp( $num ) 
	{
		if ( !is_numeric( $num ) ) 
			return false;
		
		$a    = (int)( $num / pow( 2, 24 ) );   
		$num -= $a * pow( 2, 24 );
		$b    = (int)( $num / pow( 2, 16 ) );   
		$num -= $b * pow( 2, 16 );
		$c    = (int)( $num / pow( 2,  8 ) );    
		$d    = $num - $c * pow( 2, 8 );
		
		return "$a.$b.$c.$d";
	}

	/**
	 * @access public
	 */
	function getPossibleDomainsForURL( $url ) 
	{
	}

	/**
	 * @access public
	 */
	function explodeIp( $ip, $zerofill = false ) 
	{
		$ret = explode( '.', $ip );
		
		if ( ( is_array( $ret ) ) && ( sizeof( $ret ) == 4 ) ) 
		{
			if ( $zerofill ) 
			{
				for ( $i = 0; $i <= 3; $i++ ) 
				{
					$t = strlen( $ret[$i] );
					
					switch ( $t ) 
					{
						case 0:
							$ret[$i] = '000';
							break;
							
						case 1:
							$ret[$i] = '00' . $ret[$i];
							break;
						
						case 2:
							$ret[$i] = '0' . $ret[$i];
							break;
					}
				}
			}

			return $ret;
		} 
		else 
		{
			return false;
		}
	}

	/**
	 * @access public
	 */
	function buildURL( $base_url, $elements, $delim = '&' )
	{
		$output_url = '';
		$output_url = $base_url;

		if ( count( $elements ) > 0 )
		{
			$first_elem = 0;

			if ( ! ereg( '\?', $output_url ) )
				$output_url .= '?';
			else
				$first_elem = 1;

			while ( list( $var, $value ) = each( $elements ) )
			{
				if ( $first_elem != 0  )
					$output_url .= $delim;

				$output_url .= $var . '=' . urlencode( $value );
				$first_elem  = 1;
			}
		}

		return $output_url;
	}

	/**
	 * @access public
	 */
	function parseUrlExtended( $url ) 
	{
		$ret = @parse_url( $url );
		
		if ( ( is_array( $ret ) ) && ( isset( $ret['path'] ) ) ) 
		{
			$ret['domain']    = URLUtil::getDomainForURL( $url, 2 );
			$ret['directory'] = URLUtil::getDirectoryForURL( $url );
			
			if ( empty( $ret['directory'] ) ) 
				unset( $ret['directory'] );
				
			$ret['file'] = URLUtil::getFileForURL( $url );
			
			if ( empty( $ret['file'] ) )      
				unset( $ret['file'] );
		} 
		else 
		{
			return false;
		}

		return $ret;
	}

	/**
	 * @access public
	 */
	function getUrlJunk( $junk, $url = null ) 
	{
		if ( !is_array( $url ) ) 
		{
			if ( is_null( $url ) ) 
			{
				$scheme = 'http://';
				
				if ( isset( $_SERVER['HTTPS'] ) ) 
				{
					$t = strtolower( $_SERVER['HTTPS'] );
					
					if ( $t != "" )
						$scheme = 'https://';
				}

				$url = $scheme . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
			}

			$url = URLUtil::parseUrlExtended($url);
			
			if ( $url === FALSE ) 
				return false;
		}
		
		$len = strlen( $junk );
		$ret = '';
		
		for ( $i = 0; $i < $len; $i++ ) 
		{
			switch ( $junk[$i] ) 
			{
				case 's':
					if ( !empty( $url['scheme'] ) ) 
					{
						$ret .= $url['scheme'];
						
						switch ( $junk[$i +1]) 
						{
							case 'u':
							
							case 'h':
							
							case 'd':
								$ret .= '://';
						}
					}

					break;
				
				case 'u':
					if ( !empty( $url['user'] ) ) 
						$ret .= $url['user'];
					
					break;
					
				case 'P':
					if ( !empty( $url['pass'] ) ) 
					{
						if ( $junk[$i -1] == 'u' ) 
							$ret .= ':';
							
						$ret .= $url['pass'];
					}

					break;
					
				case 'h':
					if ( !empty( $url['host'] ) ) 
					{
						if ( ( ( $junk[$i -1] == 'u' ) || ( $junk[$i -1] == 'P' ) ) && ( !empty( $url['user'] ) || !empty( $url['pass'] ) ) )
							$ret .= '@';

						$ret .= $url['host'];
					}
					
					break;
				
				case 'd':
					if ( !empty( $url['domain'] ) ) 
						$ret .= $url['domain'];

					break;
					
				case 'o':
					if ( !empty( $url['port'] ) ) 
					{
						if ( !empty( $url['port'] ) ) 
						{
							if ( ( $junk[$i -1] == 'h' ) || ( $junk[$i -1] == 'd' ) ) 
								$ret .= ':';
								
							$ret .= $url['port'];
						}
					}

					break;
					
				case 'O':
					if ( ( !empty( $url['port'] ) ) && ( $url['port'] != '80' ) ) 
					{
						if ( ( $junk[$i -1] == 'h' ) || ( $junk[$i -1] == 'd' ) ) 
							$ret .= ':';
							
						$ret .= $url['port'];
					}

					break;
					
				case 'p':
					if ( !empty( $url['path'] ) ) 
						$ret .= $url['path'];

					break;
					
				case 'i':
					if ( !empty( $url['directory'] ) ) 
						$ret .= $url['directory'];

					break;
					
				case 'f':
					if ( !empty( $url['file'] ) ) 
						$ret .= $url['file'];

					break;
					
				case 'q':
					if ( !empty( $url['query'] ) ) 
					{
						switch ( $junk[$i -1] ) 
						{
							case 'h':
							
							case 'd':
							
							case 'o':
							
							case 'O':
							
							case 'p':
							
							case 'i':
							
							case 'f':
							
							case '3':
							
							case '4':
							
							case '5':
							
							case '8':
							
							case '9':
								$ret .= '?';
						}

						$ret .= $url['query'];
					}

					break;
				
				case 'F':
					if ( !empty( $url['fragment'] ) ) 
					{
						switch ( $junk[$i -1] ) 
						{
							case 'h':
							
							case 'd':
							
							case 'o':
							
							case 'O':
							
							case 'p':
							
							case 'i':
							
							case 'f':
							
							case 'q':
							
							case '2':
							
							case '3':
							
							case '4':
							
							case '5':
							
							case '7':
							
							case '8':
							
							case '9':
								$ret .= '#';
						}

						$ret .= $url['fragment'];
					}
					
					break;
				
				case '1':

				case '2':
				
				case '3':
				
				case '4':
				
				case '5':
					$ret .= $url['scheme'] . '://';
					
					if ( !empty( $url['user'] ) ) 
					{
						$ret .= $url['user'];
						
						if ( !empty( $url['pass'] ) ) 
							$ret .= ':' . $url['pass'];
							
						$ret .= '@';	
					}

					$ret .= $url['host'] . ':' . $url['port'];
					
					if ( $junk[$i] <= 3 ) 
					{
						$ret .= $url['path'];
						
						if ( $junk[$i] <= 2 ) 
						{
							if ( !empty( $url['query'] ) ) 
								$ret .= '?' . $url['query'];
								
							if ( $junk[$i] == 1 ) 
							{
								if ( !empty( $url['fragment'] ) ) 
									$ret .= '#' . $url['fragment'];
							}
						}
					} 
					else if ( $junk[$i] == 4 ) 
					{
						$ret .= $url['directory'];
					}

					break;
					
				case '6':
				
				case '7':
				
				case '8':
					$ret .= $url['path'];
					
					if ( $junk[$i] <= 7 ) 
					{
						if ( !empty( $url['query'] ) ) 
							$ret .= '?' . $url['query'];
							
						if ( $junk[$i] == 6 ) 
						{
							if ( !empty( $url['fragment'] ) ) 
								$ret .= '#' . $url['fragment'];
						}
					}
					
					break;
				
				case '9':
					$ret .= $url['directory'];
					break;
				
				default:
			}
		}
		
		return $ret;
	}
	
	/**
	 * @access public
	 */
	function glueUrl( $url ) 
	{
		if ( ( !is_array( $url ) ) || ( !isSet( $url['host'] ) ) ) 
			return false;
			
		$uri = ( !empty( $url['scheme'] ) )? $url['scheme'] . '://' : '';
		
		if ( !empty( $url['user'] ) ) 
			$uri .= $url['user'] . ':' . $url['pass'] . '@';
			
		$uri .= $url['host'];
			
		if ( !empty( $url['port'] ) ) 
			$uri .= ':' . $url['port'];
		
		$uri .= $url['path'];
		
		if ( isset( $url['query'] ) ) 
			$uri .= '?' . $url['query'];
			
		if ( isset( $url['fragment'] ) ) 
			$uri .= '#' . $url['fragment'];
			
		return $uri;
	}
	
	/**
	 * @access public
	 */
	function getDomainForURL( $url, $num = 2 ) 
	{
		$t = @parse_url( $url );
		
		if ( ( !is_array( $t ) ) || ( !isset( $t['host'] ) ) ) 
			return false;
			
		if ( $num < 0 ) 
			return $t['host'];
			
		$tmp = '.' . $t['host'];
		
		for ( $i = 0; $i < $num; $i++ ) 
		{
			$pos = strrpos( $tmp, '.' );
			
			if ( $pos === false ) 
			{
				break; 
			}
			else
			{
				$lastPos = $pos;
				$tmp     = substr( $tmp, 0, $lastPos );
			}
		}

		return substr( $t['host'], $lastPos );
	}
	
	/**
	 * @access public
	 */
	function getDirectoryForURL( $url ) 
	{
		$t = @parse_url( $url );
		
		if ( ( !is_array( $t ) ) || ( !isset( $t['host'] ) ) ) 
			return false;
		
		if ( ( !isset( $t['path'] ) ) || ( empty( $t['path'] ) ) ) 
			return '/';
		
		$t = $t['path'];
		
		if ( substr( $t, -1 ) == '/' ) 
			return $t;
		
		$pos = strrpos( $t, '/' );
		
		if ( $pos === false )
			return '/';
		else
			return substr( $t, 0, $pos + 1 );
	}

	/**
	 * @access public
	 */
	function getFileForURL( $url ) 
	{
		$t = @parse_url( $url );
		
		if ( ( !is_array( $t ) ) || ( !isset( $t['host'] ) ) ) 
			return false;
		
		if ( ( !isset( $t['path'] ) ) || ( empty( $t['path'] ) ) ) 
			return '';
		
		$t = $t['path'];
		
		if ( substr( $t, -1 ) == '/' ) 
			return '';
			
		$pos = strrpos( $t, '/' );
		
		if ( $pos === false )
			return '';
		else 
			return substr( $t, $pos + 1 );
	}

	/**
	 * @access public
	 */
	function enableUrl( $str ) 
	{
		$str = eregi_replace( "((f|ht)tp:\/\/[a-z0-9~#%@\&:=?\/\._-]+)", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $str );
		$str = eregi_replace( "([[:space:]a-z0-9()\"'\[~#%@\&:=?\._-])(www.[a-z0-9~#%@\&:=?\/\._-]+)", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $str );
		$str = eregi_replace( "([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})", "<a href=\"mailto:\\1\">\\1</a>", $str );
		
		return $str;
	}

	/**
	 * @access public
	 */
	function hashArrayToQueryString( &$hashArray, $prefix = '', $firstSeparator = '&' ) 
	{
		if ( is_array( $hashArray ) ) 
		{
			reset( $hashArray );
			$ret = '';
			$beenHere = false;
			
			while ( list( $k, $v ) = each( $hashArray ) ) 
			{
				if ( $prefix != '' ) 
					$k = $prefix . "[$k]";
					
				if ( is_array( $v ) ) 
				{
					$ret .= URLUtil::hashArrayToQueryString( $v, $k );
				} 
				else 
				{
					if ( ( !$beenHere ) && ( $firstSeparator != '&' ) ) 
					{
						$beenHere = true;
						$ret .= "{$firstSeparator}{$k}=" . urlencode( $v );
					} 
					else 
					{
						$ret .= "&{$k}=" . urlencode( $v );
					}
				}
			}
		} 
		else 
		{
			$ret = '';
		}
		
		return $ret;
	}
	
	/**
	 * @access public
	 */
	function hashArrayToHiddenFields( &$hashArray, $prefix = '' ) 
	{
		if ( is_array( $hashArray ) ) 
		{
			reset( $hashArray );
			while ( list( $k, $v ) = each( $hashArray ) ) 
			{
				if ( $prefix != '' ) 
					$k = $prefix . "[$k]";
					
				if ( is_array( $v ) ) 
					$ret .= URLUtil::hashArrayToQueryString( $v, $k );
				else 
					$ret .= "<input type='hidden' name=\"{$k}\" value=\"" . urlencode( $v ) . "\">\n";
			}
		} 
		else 
		{
			$ret = '';
		}

		return $ret;
	}

	/**
	 * @access public
	 */
	function addQueryParam( $url, $key, $val ) 
	{
		$t = parse_url( $url );
		$value = $key . '=' . urlencode( $val );
		$t['query'] = ( isset( $t['query'] ) )? $t['query'] . '&' . $value : $value;
		
		return URLUtil::glueUrl( $t );
	}
	
	/**
	 * @access public
	 */
	function removeQueryParam() 
	{
	}

	/**
	 * @access public
	 */
	function modifyQueryParam( $url, $key, $val, $force = true ) 
	{
		if ( ereg( "(\\?|&)$key=([^&]*)(&|$)", $url ) ) 
		{
			// Change the value in query string 
			$val = urlencode( $val );
			$new_url = ereg_replace( "(\\?|&)$key=([^\\&]*)(&|$)","\\1$key=$val\\3", $url );
			$new_url = ereg_replace( "\\?\\?", "?", $new_url );
			
			return $new_url;
		} 
		else if ( $force )
		{
			// The variable doesn't exist in query string, add it
			return URLUtil::addQueryParam( $url, $key, $val );
		} 
		else 
		{
			// The variable doesn't exist in query string, return without change
			return $url;
		}
	}

	/**
	 * @access public
	 */
	function breadCrumb( $url = null ) 
	{
		if ( is_null( $url ) ) 
			$url = $_SERVER["SCRIPT_FILENAME"];
		
		$t = @parse_url( $url );
		$originalpath  = $t['path'];
		$urlbase       = $t['scheme'] . '://' . $t['host'];
		$roottitle     = 'Home';
		$seperator     = ' > ';
		$ignore        = 'Index';
		$path          = explode( "/", $originalpath );
		$totalelements = count( $path );
		
		printf( "<a href=\"%s\">%s</a>", $urlbase, $roottitle );
		
		for ( $number = 1; $number < $totalelements ; $number++ ) 
		{
			$urlbase = $urlbase . "/" . $path[$number];
			$path[$number] = str_replace( "___",  "_&_", $path[$number] );
			$path[$number] = str_replace( "_",    " ",   $path[$number] );
			$path[$number] = str_replace( "~",    "?",   $path[$number] );
			$path[$number] = str_replace( ".php", "",    $path[$number] );
			$path[$number] = ucwords( $path[$number] );
			
			if ( $path[$number] != $ignore ) 
				printf( "%s<a href=\"%s\">%s</a>", $seperator, $urlbase, $path[$number] );
		}
	}
	
	/**
	 * @access public
	 */
	function similar( $urlOne, $urlTwo ) 
	{
		$urlOne = strtolower( $urlOne );
		$urlTwo = strtolower( $urlTwo );
		
		if ( $urlOne == $urlTwo ) 
			return true;
			
		$urlOneLen = strlen( $urlOne );
		$urlTwoLen = strlen( $urlTwo );
		$lenDiff   = ( $urlOneLen - $urlTwoLen );
		
		if ( $lenDiff == 1 ) 
		{
			if ( substr( $urlOne, 0, -1 ) == $urlTwo ) 
				return true;
		} 
		else if ( $lenDiff == -1 ) 
		{
			if ( $urlOne == substr( $urlTwo, 0, -1 ) ) 
				return true;
		}

		$urlOneShort = ( ( $urlOne == '/' ) || ( $urlOne == '' ) );
		$urlTwoShort = ( ( $urlTwo == '/' ) || ( $urlTwo == '' ) );
		
		if ( $urlOneShort && $urlTwoShort ) 
			return true;
		else if ( $urlOneShort || $urlTwoShort )  
			return false;
		
		do 
		{
			$urlOneNoFile = $urlTwoNoFile = '';
			
			if ( $urlOne[$urlOneLen -1] == '/' ) 
			{
				$urlOneNoFile = $urlOne;
			} 
			else 
			{
				$t = strrpos( $urlOne, '/' );
				$urlOneNoFile = substr( $urlOneNoFile, 0, $t );
			}
			
			if ( $urlTwo[$urlTwoLen -1] == '/' ) 
			{
				$urlTwoNoFile = $urlTwo;
			} 
			else 
			{
				$t = strrpos( $urlTwo, '/' );
				$urlTwoNoFile = substr( $urlTwoNoFile, 0, $t );
			}
			
			$urlOneLastDir = URLUtil::getLastDir( $urlOne );
			$urlTwoLastDir = URLUtil::getLastDir( $urlTwo );
			
			if ( is_null( $urlOneLastDir ) || is_null( $urlTwoLastDir ) ) 
				break;
			
			if ( is_string( $urlOneLastDir ) ) 
				$urlOneLastDir = array( $urlOneLastDir );
				
			if ( is_string( $urlTwoLastDir ) ) 
				$urlTwoLastDir = array( $urlTwoLastDir );
				
			while ( list( $k ) = each( $urlOneLastDir ) ) 
			{
				while ( list( $k2 ) = each( $urlTwoLastDir ) ) 
				{
					if ( $urlTwoLastDir[$k2] == $urlOneLastDir[$k] ) 
						return true;
						
					if ( soundex( $urlTwoLastDir[$k2] ) == soundex( $urlOneLastDir[$k] ) ) 
						return true;
				}
			}
		} while ( false );
		
		$urlOneJunks = explode( '/', $urlOne );
		$urlTwoJunks = explode( '/', $urlTwo );
		
		if ( sizeof( $urlOneJunks ) == sizeof( $urlTwoJunks ) ) 
		{
			$isOk = true;
			
			while ( list( $k ) = each( $urlOneJunks ) ) 
			{
				if ( soundex( $urlOneJunks[$k] ) != soundex( $urlTwoJunks[$k] ) ) 
				{
					$isOk = false;
					break;
				}
			}

			if ( $isOk ) 
				return true;
		}

		return false;
	}

	/**
	 * @access public
	 */
	function crossUrlDecode( $source ) 
	{
		$decodedStr = '';
		$pos = 0;
		$len = strlen( $source );
		
		while ( $pos < $len ) 
		{
			$charAt = substr( $source, $pos, 1 );
			
			if ( $charAt == 'Ã' ) 
			{
				$char2 = substr( $source, $pos, 2 );
				$decodedStr .= htmlentities( utf8_decode( $char2 ), ENT_QUOTES, 'ISO-8859-1' );
				$pos += 2;
			} 
			else if ( ord( $charAt ) > 127 ) 
			{
				$decodedStr .= "&#" . ord( $charAt ) . ";";
				$pos++;
			} 
			else if ( $charAt == '%' ) 
			{
				$pos++;
				$hex2   = substr( $source, $pos, 2 );
				$dechex = chr( hexdec( $hex2 ) );
				
				if ( $dechex == 'Ã' ) 
				{
					$pos += 2;
					
					if ( substr( $source, $pos, 1 ) == '%' ) 
					{
						$pos++;
						$char2a = chr( hexdec( substr( $source, $pos, 2 ) ) );
						$decodedStr .= htmlentities( utf8_decode( $dechex . $char2a ), ENT_QUOTES, 'ISO-8859-1' );
					} 
					else 
					{
						$decodedStr .= htmlentities( utf8_decode( $dechex ) );
					}
				} 
				else 
				{
					$decodedStr .= $dechex;
				}

				$pos += 2;
			}
			else 
			{
				$decodedStr .= $charAt;
				$pos++;
			}
		}

		return $decodedStr;
	}

	/**
	 * @access public
	 */
	function getLastDir( $url ) 
	{
		list( $url, $dir ) = URLUtil::_removeFile( $url );
		
		if ( is_null( $url ) ) 
		{
			if ( is_null( $dir ) )
				return null;
			else
				return $dir;
		}
		
		$lastSlash = strrpos( $url, '/' );
		
		if ( $lastSlash === false ) 
			$dir2 = $url;
		else 
			$dir2 = substr( $url, $lastSlash + 1 );
		
		if ( !is_null( $dir ) ) 
			return array( $dir, $dir2 );
		else 
			return $dir2;
	}
	
	/**
	 * Takes a two URLs and returns the relative HREF required to get
 	 * you from the first URL to the second. The two given URLs MUST be absolute.
	 * protocol://domain/dir/dir2/file
	 *
	 * @access public
	 * @static
	 */
	function relativeHref( $from, $to ) 
	{
		$froms = split( "/+", $from );
		$tos   = split( "/+", $to   );
		$href  = '';

		// ensure the first element is the protocol
		if ( !eregi( "^[a-z]+\:$", $froms[0] ) ) 
			array_unshift( $froms, "http:" );
			
		if ( !eregi( "^[a-z]+\:$", $tos[0] ) ) 
			array_unshift( $tos, "http:" );
	
		// Different protocols or domains? ABSOLUTE HREF!
		if ( strtolower( $froms[0]) != strtolower( $tos[0] ) || strtolower( $froms[1] ) != strtolower( $tos[1] ) ) 
		{ 
			$tos[0] .= "/";
			$href    = implode( "/", $tos );
		
			return $href;
		}
		
		// Different first directories? Root path!
		if ( $froms[2] != $tos[2] ) 
		{ 
			array_shift( $tos ); // shift off protocol
			array_shift( $tos ); // shift off domains
		
			return "/" . implode( "/", $tos );
		}
		
		// Start from the second directory and find the place where the urls start to vary.
		$split_point = 3;
	
		while ( $froms[$split_point] && $froms[$split_point] == $tos[$split_point] ) 
			$split_point++;
	
		for ( $i = count( $froms ); $i > $split_point; $i-- )
			$href .= "../";

		// forward to the destination
		for ( $i = $split_point - 1; $i < count( $tos ); $i++ )
			$href .= "{$tos[$i]}/";
	
		$href = ereg_replace( "/$", "", $href ); // Don't need one of these.
		return $href;
	}
	
	/**
	 * Takes a URL and returns a valid URL, or if one can't be made, a blank string.
	 *
	 * @access public
	 * @static
	 */
	function validURL( $url ) 
	{
		if ( !ereg( "^(http[s]?|ftp)\:\/\/", $url ) ) 
		{
			if ( $url )
				return "http://" . $url;
			else
				return "";
		} 
		else 
		{
			return $url;
		}
	}
	
	/**
	 * Takes a filename and returns an alternative, possibly the same that is (more) url friendly
	 * (see http://www.w3.org/Addressing/URL/5_URI_BNF.html).
	 *
	 * @access public
	 * @static
	 */
	function urlFriendlyFilename( $filename ) 
	{
		$filename = ereg_replace( "[ \t\n\r]+", "_", $filename ); // Spaces! Grr!
		$filename = ereg_replace( "[^a-zA-Z0-9\$\_\@\.\&\!\*\'\(\)\,\-]", "", $filename );
		$filename = str_replace( "\\", "", $filename ); // Backslashes! GRR!
		
		return $filename;
	}

	/**
	 * @access public
	 */
	function getRelativePath( $baseDir, $destDir )
	{
		if ( $baseDir == $destDir )
			return "./";
		
		$baseDir   = ereg_replace( "^/", "", $baseDir ); // remove beginning
		$destDir   = ereg_replace( "^/", "", $destDir );
		
		$found     = true;
		$slash_pos = 0;
		
		do 
		{
			$slash_pos = strpos( $destDir, '/' );
			
			if ( substr( $destDir, 0, $slash_pos ) == substr( $baseDir, 0, $slash_pos ) )
			{ 
				$baseDir = substr( $baseDir, $slash_pos + 1 );
				$destDir = substr( $destDir, $slash_pos + 1 ); 
			} 
			else 
			{
				$found = false;
			}
		} while ( $found == true );
		
		$slashes = strlen( $baseDir ) - strlen( str_replace( "/", "", $baseDir ) );
		
		for ( $i = 0; $i < $slashes; $i++ )
			$destDir = "../" . $destDir;
		
		return $destDir;
	}
	
	// private methods
	
	/**
	 * @access private
	 */
	function _removeFile( $url ) 
	{
		if ( substr( $url, -1 ) == '/' ) 
		{
			$url = substr( $url, 0, -1 );
			return array( $url, null );
		} 
		else 
		{
			$lastSlash = strrpos( $url, '/' );
			
			if ( $lastSlash === false ) 
			{
				$restUrl = null;
				$workUrl = $url;
			} 
			else 
			{
				$restUrl = substr( $url, 0, $lastSlash  );
				$workUrl = substr( $url, $lastSlash + 1 );
				
				if ( $restUrl == '/' ) 
					$restUrl = null;
			}
			
			if ( strpos( $workUrl, '.' ) === false ) 
			{
				if ( strlen( $workUrl ) > 0 ) 
					return array( $restUrl, $workUrl );
					
				return array( $restUrl, null );
			} 
			else 
			{
				return array( $restUrl, null );
			}
		}
	}
} // END OF URLUtil

?>
