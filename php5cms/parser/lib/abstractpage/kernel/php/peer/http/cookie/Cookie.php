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
 * Class that represents a cookie.
 *
 * @package peer_http_cookie
 */
 
class Cookie extends PEAR
{
	/**
	 * @access public
	 */
	var $name = null;
	
	/**
	 * @access public
	 */
	var $value = null;
	
	/**
	 * @access public
	 */
	var $expire = 0;
	
	/**
	 * @access public
	 */
	var $path = '/';
	
	/**
	 * @access public
	 */
	var $domain = null;
	
	/**
	 * @access public
	 */
	var $secure = false;
   
   
	/**
	 * Constructor
	 *
	 * @param string  name of the cookie
     * @param string  the cookie value
     * @param integer expiration value
     * @param string  path restriction
     * @param string  domain restriction
     * @param boolean TRUE if it's  a secure cookie
	 * @access public
     */
	function Cookie( $name, $value = null, $expire = 0, $path = '/', $domain = null, $secure = false )
   	{
		$this->setName( $name );
		$this->setValue( $value );
		$this->setMaxAge( $value );
		$this->setPath( $path );
		$this->setDomain( $domain );
		$this->setSecure( $secure );
	}


	// cookie methods
	
	/**
	 * Set the cookie name.
	 *
     * @param  string the cookie name
	 * @access public
     */
   	function setName( $name )
   	{
      	$this->name = $name;
   	}
   
   	/**
     * Get the cookie name.
	 *
     * @return string the cookie name
	 * @access public
     */
   	function getName()
   	{
      	return $this->name;
   	}
   
   	/**
     * Set the cookie value.
	 *
     * @param  string the cookie value
	 * @access public
     */
   	function setValue( $value )
   	{
      	$this->value = $value;
   	}
   
   	/**
     * Get the cookie value.
	 *
     * @return string the cookie value
	 * @access public
     */
   	function getValue()
   	{
      	return isset( $this->value )? $this->value : '';
   	}
   
   	/**
     * Set the cookie expiration timeout.
	 *
     * @param  integer timeout value
	 * @access public
     */
   	function setMaxAge( $expire )
   	{
      	settype( $expire, 'integer' );
      	$this->expire = $expire;
   	}
   
   	/**
     * Get the cookie expiration timeout.
	 *
     * @return integer timeout value
	 * @access public
     */
   	function getMaxAge()
   	{
      	return $this->expire;
   	}
   
   	/**
     * Set the cookie path restriction.
	 *
     * @param  string the path restriction
	 * @access public
     */
   	function setPath( $path )
    {
      	$this->path = $path;
   	}
   
   	/**
     * Get the cookie path restriction.
	 *
     * @return string the path restriction
	 * @access public
     */
   	function getPath()
   	{
      	return $this->path;
   	}
   
   	/**
     * Set the cookie domain restriction.
	 *
     * @param  string the domain restriction
	 * @access public
     */
   	function setDomain( $domain )
   	{
      	$this->domain = $domain;
   	}
   
   	/**
     * Get the cookie domain restriction.
	 *
     * @param  string the domain restriction
	 * @access public
     */
   	function getDomain()
   	{
      	return $this->domain;
   	}
   
   	/**
     * Set the cookie secure state.
	 *
     * @param  boolean TRUE if the cookie is secure
	 * @access public
     */
   	function setSecure( $secure )
   	{
      	$this->secure = ( ( $secure == true )? true : false );
   	}
   
   	/**
     * Check if the cookie is secure.
	 *
     * @param  boolean TRUE if the cookie is secure
	 * @access public
     */
   	function isSecure()
   	{
      	return $this->secure;
   	}
} // END OF Cookie

?>
