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


using( 'peer.http.cookie.CookieUtil' );


/**
 * @package peer_http_session
 */
 
class Session
{
	/**
	 * @access public
	 */
	public $sidName = 'ap_session';
	
	/**
	 * @access protected
	 */
	protected $_sid;
	
	/**
	 * @access protected
	 */
	protected $_data;
	
	/**
	 * @access protected
	 */
	protected $_gc;
	
	/**
	 * @access protected
	 */
	protected $_ttl;
	
	/**
	 * @access protected
	 */
	protected $_techType;

	/**
	 * @access protected
	 */	
	protected $_hasChanged = false;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct( $gc = null, $ttl = 30 ) 
	{
		$this->_gc  = $gc;
		$this->_ttl = $ttl;
		
		mt_srand( (double)microtime() * 1000000 );
	}


	/**
	 * Destructor
	 *
	 * @access public
	 */
	public function __destruct()
	{
		$this->write();
	}
	
	
	/**
	 * @access public
	 */
	public function init( $techType ) 
	{
		if ( $techType == 0 ) 
			$techType = 1;
			
		$this->_techType = $techType;
		
		switch ( $techType ) 
		{
			case 1:
				if ( !empty( $_COOKIE[$this->sidName] ) ) 
					$sid = $_COOKIE[$this->sidName];
				else if ( !empty( $_GET[$this->sidName] ) ) 
					$sid = $_GET[$this->sidName];
				
				break;
			
			case 2:
				if ( !empty( $_COOKIE[$this->sidName] ) ) 
					$sid = $_COOKIE[$this->sidName];

				break;
				
			case 3:
				if ( !empty( $_GET[$this->sidName] ) ) 
					$sid = $_GET[$this->sidName];

				break;
		}

		if ( isset( $sid ) ) 
		{
			if ( $this->_checkIntegrity( $sid ) ) 
			{
				$this->_sid = $sid;
				
				if ( $this->read() === true ) 
					return true;
				else 
					unset( $this->_sid );
			}
		}

		return false;
	}
	
	/**
	 * @access public
	 */
	public function start( $type = 'cookie' ) 
	{
		if ( $this->_gc > 0 ) 
		{
			$randVal = mt_rand( 1, 100 );
			
			if ( $randVal <= $this->_gc ) 
				$this->gc();
		}

		$this->_sid = md5( uniqid( mt_rand() ) );
		
		if ( $type != 'query' ) 
			CookieUtil::set( $this->sidName, $this->_sid, "", "/", "", 0 );
		else 
			; // What to do? Maybe some sort of rewrite url session.

		$this->_hasChanged = true;
	}

	/**
	 * @access public
	 */
	public function destroy() 
	{
		unset( $this->_data );
		CookieUtil::set( $this->sidName, '', time() - 50000, "/", "", 0 );
	}

	/**
	 * @access public
	 */
	public function read() 
	{
		$this->_hasChanged = false;
	}

	/**
	 * @access public
	 */
	public function write() 
	{
		$this->_hasChanged = false;
	}

	/**
	 * @access public
	 */
	public function getSid() 
	{
		if ( is_null( $this->_sid ) ) 
			return null;
			
		return $this->_sid;
	}

	/**
	 * @access public
	 */
	public function setSid( $sid ) 
	{
		if ( isset( $this->_sid ) ) 
			$this->_hasChanged = true;
		
		$this->_sid = $sid;
	}

	/**
	 * @access public
	 */
	public function register( $key, $value ) 
	{
		$this->_data[$key] = &$value;
		$this->_hasChanged = true;
	}

	/**
	 * @access public
	 */
	public function unRegister( $key ) 
	{
		unset( $this->_data[$key] );
		$this->_hasChanged = true;
	}

	/**
	 * @access public
	 */
	public function isRegistered( $key ) 
	{
		return (bool)( isset( $this->_data[$key] ) );
	}

	/**
	 * @access public
	 */
	public function &getVar( $key ) 
	{
		return $this->_data[$key];
	}

	/**
	 * @access public
	 */	
	public function gc() 
	{
	}

	
	// protected methods

	/**
	 * @access protected
	 */		
	protected function _checkIntegrity( $sid ) 
	{
		return true;
	}
} // END OF Session


$GLOBALS["AP_SIMPLESESSION_CLASS"] =& new Session();

?>
