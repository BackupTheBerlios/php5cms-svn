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
 * @package peer_http_cookie
 */
 
class MultiCookie extends PEAR
{ 
	/**
	 * @access public
	 */
	var $cid;
	
	/**
	 * @access public
	 */
	var $expire;
	
	/**
	 * @access public
	 */
	var $path;
	
	/**
	 * @access public
	 */
	var $domain;
	
	/**
	 * @access public
	 */
	var $secure;
	
	/**
	 * @access public
	 */
    var $cookieArray;
	
	/**
	 * @access public
	 */
	var $keyArray;
	
	/**
	 * @access public
	 */
	var $cookiesCount;
	
	/**
	 * @access public
	 */
	var $parsingCheck;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */ 
    function MultiCookie( $cid, $expire = 0, $path = "/", $domain = "", $secure = 0 ) 
    {
        $this->cid          = $cid; 
        $this->expire       = $expire; 
        $this->path         = $path; 
        $this->domain       = $domain; 
        $this->secure       = $secure; 
        $this->parsingCheck = false; 
        $this->keyArray     = array(); 
    } 

	
	/**
	 * Function that creates cookie. Parameter of "key=>value" form. 
	 * Is stored with "1=>a:2=>b:3=>c:4=>d" in file. 
	 * create( "id=>ifsnow", "passwd=>12345", "level=>99" );
	 *
	 * @access public
	 */
    function create() 
    { 
		$temp = func_get_args(); 
		setcookie( $this->cid, join( ":", $temp ), $this->expire, $this->path, $this->domain, $this->secure ); 
		
		return true;  
    } 

	/**
	 * Erase cookie and initialize interior variables.
	 *
	 * @access public
	 */
    function destroy( $set = true ) 
    { 
        empty( $cookieArray ); 
        empty( $keyArray ); 
		
        $this->parsingCheck = false; 
        $this->cookiesCount = 0; 
        
		if ( $set ) 
            setcookie( $this->cid, "", 0, $this->path, $this->domain, $this->secure ); 
    } 

	/**
	 * @access public
	 */
    function parse() 
    {
        if ( ( $savedCookies = $_COOKIE[$this->cid] ) )
		{ 
            $tempCookies = explode( ":", $savedCookies ); 
            $this->cookiesCount = count( $tempCookies ); 
         
            if ( $this->cookiesCount > 0 )
			{ 
            	for ( $i = 0; $i < $this->cookiesCount; $i++ )
				{ 
                    $temp = split( "=>", $tempCookies[$i], 2 ); 
                    $this->cookieArray[$temp[0]] = $temp[1]; 
                    array_push( $this->keyArray, $temp[0] ); 
                }
				
                $this->parsingCheck = true; 
                return true; 
            }
            else
			{
				return false;
			}
        } 
    } 

	/**
	 * Return the value of an array.
	 *
	 * @access public
	 */
    function read( $key ) 
    { 
        if ( $this->parsingCheck == false )
			$this->parse(); 
        
		return $this->cookieArray[$key]; 
    } 

	/**
	 * Add new value to existent cookie.
	 *
	 * @access public
	 */
    function add() 
    { 
		$tempArgs   = func_get_args(); 
		$existCheck = false; 
		$tempString = ""; 

		for ( $i = 0 ; $i < $argNum ; $i++ ) 
		{ 
			$temp = split( "=>", $tempArgs[$i] ); 
			
			if ( !$this->exists( $temp[0]) && is_array( $temp ) )
			{ 
				$tempString .= $tempArgs[$i] . ":"; 
				$existCheck  = true; 
			} 
		} 

		if ( $existCheck )
		{ 
			if ( $this->cookiesCount > 0 ) 
				$tempString = $this->extract() . ":" . eregi_replace( ":$", "", $tempString ); 
			else 
				$tempString = eregi_replace( ":$", "", $tempString ); 

			setcookie( $this->cid, $tempString, $this->expire, $this->path, $this->domain, $this->secure ); 
			$this->destroy( false ); 
			
			return true; 
		} 
		else
		{
			return false;
		}
    } 

	/**
	 * Modify value of existing cookie.
	 *
	 * @access public
	 */
    function modify() 
    { 
        if ( $this->parsingCheck == false )
			$this->parse(); 

		$tempArgs   = func_get_args(); 
		$existCheck = false; 
		
		for ( $i = 0 ; $i < $argNum ; $i++ ) 
		{ 
			$temp = split( "=>", $tempArgs[$i] ); 
			
			if ( $this->exists( $temp[0] ) && is_array( $temp ) )
			{ 
				$this->cookieArray[$temp[0]] = $temp[1]; 
				$existCheck = true; 
			} 
		} 
		
		if ( $existCheck )
		{ 
			setcookie( $this->cid, $this->extract(), $this->expire, $this->path, $this->domain, $this->secure ); 
			$this->destroy( false ); 
			
			return true; 
		} 
		else
		{
			return false;
		}
    } 

	/**
	 * Erase value of existing cookie.
	 * delete( "email","icq" );
	 *
	 * @access public
	 */
    function delete() 
    { 
		if ( $this->parsingCheck == false )
			$this->parse(); 

		$tempArgs   = func_get_args(); 
		$existCheck = false; 
		
		for ( $i = 0 ; $i < $argNum ; $i++ ) 
		{ 
			if ( $this->exists( $tempArgs[$i] ) )
			{ 
				$this->cookieArray[$tempArgs[$i]] = ""; 
				$existCheck = true; 
			} 
		} 
		
		if ( $existCheck )
		{ 
			setcookie( $this->cid, $this->extract(), $this->expire, $this->path, $this->domain, $this->secure ); 
			$this->destroy( false ); 
			
			return true; 
		} 
		else
		{
			return true;
		}
    } 

	/**
	 * Return true if a $key exists in an array.
	 *
	 * @access public
	 */
    function exists( $key ) 
    { 
		if ( $this->parsingCheck == false )
			$this->parse(); 
        
		return in_array( $key, $this->keyArray ); 
    } 

	/**
	 * @access public
	 */
    function extract()
    { 
        $temp = ""; 
        
		foreach ( $this->cookieArray as $key => $value )
		{ 
            if ( $value ) 
                $temp .= "$key=>$value:"; 
        }
		
        return eregi_replace( ":$", "", $temp ); 
    } 

	/**
	 * Display information of cookie.
	 *
	 * @access public
	 */
    function dump() 
    { 
		if ( $this->parsingCheck == false )
			$this->parse(); 
        
		if ( $this->cookiesCount > 0 )
		{ 
			echo "cookie name : $this->cid<br>" .
				"cookie expire : " .( ( $this->expire == 0 )? "No" : $this->expire . "seconds" ) . "<br>" .
				"cookie path : "   .( ( $this->path        )? $this->path   : "No" ) . "<br>" .
				"cookie domain : " .( ( $this->domain      )? $this->domain : "No" ) . "<br>" .
				"cookie secure : " .( ( $this->secure == 1 )? "Use" : "Not Use"    ) . "<br>" .
				"============= $this->cookiesCount cookies exists =============<br>"; 

			foreach ( $this->cookieArray as $key => $value ) 
				echo "key : $key => value : $value<br>"; 
		} 
		else
		{ 
            echo "There is no cookie that is stored in $this->cid name<br>"; 
        } 
    } 
} // END OF MultiCookie

?>
