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
 * @package commerce_stocks_lib
 */
 
class StockSymbols extends PEAR
{
	/**
	 * @access private
	 */
	var $_aliases = array(
		"AMEX"   => "American Stock Exchange",
		"FOREX"  => "Foreign Exchange",
		"INDEX"  => "Global Indices",
		"HKSE"   => "Hong Kong Stock Exchange",
		"LSE"    => "London Stock Exchange",
		"MSE"    => "Madrid Stock Exchange",
		"MLSE"   => "Milan Stock Exchange",
		"NASDAQ" => "NASDAQ Stock Exchange",
		"NYSE"   => "New York Stock Exchange",
		"OBB"    => "OTC Bulletin Board",
		"PSE"    => "Paris Stock Exchange",
		"SGX"    => "Singapore Stock Exchange",
		"TSE"    => "Toronto Stock Exchange",
		"VSE"    => "Vancouver Stock Exchange"
	);
	
	/**
	 * @access private
	 */
	var $_symbols  = array();
	
	/**
	 * @access private
	 */
	var $_stock_ex = "";

	/**
	 * @access private
	 */
	var $_options = array();
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function StockSymbols( $options = array() )
	{
		$this->_options = $options;
	}
	
	
    /**
     * Attempts to return a concrete StockSymbols instance based on $driver.
     *
     * @param mixed $driver  The type of concrete StockSymbols subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included.
     * @param array $options (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object StockSymbols  The newly created concrete StockSymbols instance,
     *                       or false on an error.
     */
    function &factory( $driver, $options = array() )
    {	
        $driver = strtoupper( $driver );
		
        if ( empty( $driver ) || ( strcmp( $driver, 'none' ) == 0 ) )
            return new StockSymbols( $options );
	
        $symbol_class = "StockSymbols_" . $driver;

		using( 'commerce.stocks.lib.' . $symbol_class );
		
		if ( class_registered( $symbol_class ) )
	        return new $symbol_class( $options );
		else
			return PEAR::raiseError( 'Driver not supported.' );
    }

    /**
     * Attempts to return a reference to a concrete StockSymbols instance
     * based on $driver. It will only create a new instance if no
     * StockSymbols instance with the same parameters currently exists.
     *
     * This should be used if multiple types of file backends (and,
     * thus, multiple StockSymbols instances) are required.
     *
     * This method must be invoked as: $var = &StockSymbols::singleton()
     *
     * @param mixed $driver  The type of concrete StockSymbols subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included.
     * @param array $options (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object StockSymbols  The concrete StockSymbols reference, or false on an
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
            $instances[$signature] = &StockSymbols::factory( $driver, $options );

        return $instances[$signature];
    }
	
	/**
	 * @access public
	 */
	function valid( $symbol )
	{
		return isset( $this->_symbols[$symbol] )? true : false;
	}
	
	/**
	 * @access public
	 */
	function getDescription( $symbol )
	{
		return $this->_symbols[$symbol] || false;
	}
	
	/**
	 * @access public
	 */
	function getAll()
	{
		return $this->_symbols;
	}
	
	/**
	 * @access public
	 */
	function getStockExchange()
	{
		return $this->_stock_ex;
	}
	
	
	// private methods

	/**
	 * @access private
	 */	
	function _populate()
	{
		return false;
	}
} // END OF StockSymbols

?>
