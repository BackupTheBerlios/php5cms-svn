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
 * @package commerce_payment_lib
 */
 
class Payment extends PEAR
{
	/**
     * @var array
	 * @access private
     */
    var $_options = array();
	
	
	/**
	 * @var array
	 * @access private
	 */
	var $_features = array();
	
	/**
	 * last error
	 *
	 * @var mixed
	 * @access private
	 */
	var $_error;

	/**
	 * curl path
	 *
	 * @var string
	 * @access private
	 */
	var $_curl_path = "/usr/bin/curl";
	
	/**
	 * gateway url
	 *
	 * @var string
	 * @access private
	 */
	var $_gateway;
	 
	/**
	 * gateway port
	 *
	 * @var int
	 * @access private
	 */
	var $_port;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Payment( $options = array() )
	{
		$this->_hashKeysToLower( &$options );
		$this->_options = $options;
	}
	
	
    /**
     * Attempts to return a concrete Payment instance based on $driver.
     *
     * @param mixed $driver  The type of concrete Payment subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included. If $driver is an array,
     *                       then we will look for the subclass implementation named 
	 *                       $driver[1].php.
     * @param array $options (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object Payment  The newly created concrete Payment instance,
     *                       or false on an error.
     */
    function &factory( $driver, $options = array() )
    {
        if ( is_array( $driver ) )
            list( $app, $driver ) = $driver;

        /* Return a base Payment object if no driver is specified. */
        $driver = strtolower( $driver );
		
        if ( empty( $driver ) || ( strcmp( $driver, 'none' ) == 0 ) )
            return new Payment( $options );
			
        $payment_class = "Payment_" . strtolower( $driver );

		using( 'commerce.payment.lib.' . $payment_class );
		
		if ( class_registered( $payment_class ) )
	    	return new $payment_class( $options );
		else
			return PEAR::raiseError( "Driver not supported." );
    }

    /**
     * Attempts to return a reference to a concrete Payment instance
     * based on $driver. It will only create a new instance if no
     * Payment instance with the same parameters currently exists.
     *
     * This should be used if multiple types of file backends (and,
     * thus, multiple Payment instances) are required.
     *
     * This method must be invoked as: $var = &Payment::singleton()
     *
     * @param mixed $driver  The type of concrete Payment subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included. If $driver is an array,
     *                       then we will look for the subclass implementation 
	 *                       named $driver[1].php.
     * @param array $options (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object Payment  The concrete Payment reference, or false on an
     *                       error.
     */
    function &singleton( $driver, $options = array() )
    {
        static $instances;
        
		if ( !isset( $instances ) )
            $instances = array();

        if ( is_array( $driver ) )
            $drivertag = implode( ':', $driver );
        else
            $drivertag = $driver;
        
        $signature = md5( strtolower( $drivertag ) . '][' . implode('][', $options ) );
        
		if ( !isset( $instances[$signature] ) )
            $instances[$signature] = &Payment::factory( $driver, $options );

        return $instances[$signature];
    }
	
	/**
	 * Check if a specific feature is enabled for this driver.
	 *
	 * @access public
	 */
	function hasFeature( $feature )
	{
		return in_array( strtolower( $feature ), $this->_features );
	}
		
    /**
     * Validate a number.
     *
     * @param string    $number     Number to validate
     * @param array     $options    array where:
     *                              'decimal'   is the decimal char or false when decimal not allowed
     *                                          i.e. ',.' to allow both ',' and '.'
     *                              'dec_prec'  Number of allowed decimals
     *                              'min'       minimun value
     *                              'max'       maximum value
	 * @access public
     */
    function isNumber( $number, $options )
    {
        $decimal = $dec_prec = $min = $max = null;

        if ( is_array( $options ) )
            extract( $options );

        $dec_prec  = $dec_prec? "{1,$dec_prec}" : '+';
        $dec_regex = $decimal ? "[$decimal][0-9]$dec_prec" : '';

        if ( !preg_match( "|^[-+]?\s*[0-9]+($dec_regex)?\$|", $number ) )
            return false;
        
        if ( $decimal != '.' )
            $number = strtr( $number, $decimal, '.' );
        
        $number = (float)str_replace( ' ', '', $number );
		
        if ( $min !== null && $min > $number )
            return false;
        
        if ( $max !== null && $max < $number )
            return false;
        
        return true;
    }
	
	/** 
	 * Return the error code from processing.
	 *
	 * @return string
	 * @access public
	 */
    function getError()
    { 
        return $this->_error; 
    } 
	
	/**
	 * Set curl path.
	 *
	 * @access public
	 */
	function setCurlPath( $path = '' )
	{
		$this->_curl_path = $path;
	}
	
	/**
	 * Get curl path.
	 *
	 * @access public
	 */
	function getCurlPath()
	{
		return $this->_curl_path;
	}
	
	/**
	 * Trivial check for curl extension.
	 *
	 * @return bool true if curl is available, otherwise false
	 * @access public
	 * @todo   Try to load curl dynamically
	 */
	function hasCurl()
	{
		return function_exists( 'curl_init' )? true : false;
	}
	
	/**
	 * @abstract
	 */
	function process( $options = array() )
	{
		return PEAR::raiseError( "Abstract method. Needs to be implemented by subclass." );
	}
	
	
	// private methods
	
	/**
	 * Set gateway.
	 *
	 * @access public
	 */
	function _setGateway( $url = '' )
	{
		$this->_gateway = $url;
	}
	
	/**
	 * Get gateway.
	 *
	 * @return string URL of Gateway
	 * @access public
	 */
	function _getGateway()
	{
		return $this->_gateway;
	}
	
	/**
	 * Set port.
	 *
	 * @access public
	 */
	function _setPort( $port )
	{
		$this->_port = $port;
	}
	
	/**
	 * Get gateway.
	 *
	 * @return int    Port of Gateway
	 * @access public
	 */
	function _getPort()
	{
		return $this->_port;
	}
	
	/**
	 * Lowercase hash keys.
	 *
	 * @param  array
	 * @return array
	 * @access private
	 */
	function &_hashKeysToLower( &$array ) 
	{
		$newHash = array();
		
		if ( !is_array( $array ) ) 
			return $newHash;
		
		reset( $array );
		while ( list( $key ) = each( $array ) ) 
			$newHash[strtolower( $key )] = $array[$key];

		return $newHash;
	}
} // END OF Payment

?>
