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
 * Stocks Class
 *
 * Usage:
 *
 * $stocks = new Stocks;
 * $quotes = array( 'SEPR' => 'Sepracor' );
 * $result = $stocks->get_qoutes( $quotes );
 * print_r( $result );
 *
 * @package commerce_stocks
 */
 
class Stocks extends PEAR
{
	/**
	 * @access private
	 */
	var $_URL = 'http://finance.yahoo.com/d/quotes.csv?f=sl1d1t1c1ohgv&e=.csv&s=';


	/**
	 * @access public
	 */
	function get_qoutes( $stocks_list ) 
	{
        if ( !$stocks_list ) 
			return array();
        
        $this->stocks_list = $stocks_list;
        
        $symbols = '';
        foreach ( $this->stocks_list as $symbol => $name ) 
		{
            $symbol   = rawurldecode( $symbol );
            $symbols .= ( $symbols == '' )? $symbol : '+' . $symbol;
        }
        
        $lines = $this->get_data( $symbols );
        $this->last_quotes = $this->calculate( $lines );
        
        return $this->last_quotes;
    }

	/**
	 * @access public
	 */
    function get_data( &$symbols ) 
	{
        $url = $this->_URL . $symbols;
        $fp  = fopen( $url, "r" );
        $result = '';
        
        while ( !feof( $fp ) )
            $result .= fread( $fp, 1024 );
        
        $lines = split( "\n", $result, count( $this->stocks_list ) );
        
        return $lines;
    }

	/**
	 * @access public
	 */
    function calculate( &$lines ) 
	{
        $quotes = array();
        
        foreach( $lines as $line ) 
		{
            $data = $this->parse( $line );
            
            if ( $data[1] > 0 && $data[4] != 0 )
                $pchange = round( ( 10000 * $data[1] / ( $data[1]-$data[4] ) ) / 100 - 100, 2 );
            else 
				$pchange = 0;
            
            if ( $data[4] > 0 )
                $pchange = '+'.$pchange;
            else if ( $data[4] == 0 ) 
				$pchange = 0;
            
            $name = isset( $this->stocks_list[$data[0]] )? $this->stocks_list[$data[0]] : $data[0];
            $name = ( $name != '' )? $name : $data[0];
            
            $quotes[] = array(
				'symbol'     => $data[0],
				'price_last' => $data[1],
				'date'       => $data[2],
				'time'       => $data[3],
				'dchange'    => $data[4],
				'price_min'  => $data[5],
				'price_max'  => $data[6],
				'pchange'    => $pchange,
				'name'       => $name,
				'volume'     => $data[8]
            );
        }
        
        return $quotes;
    }

	/**
	 * @access public
	 */
    function parse( &$line ) 
	{
        $line = ereg_replace( '"', '',$line );
        
        // symbol, price_last, date, time, dchange, price_min, price_max, not used, volume
        return split( ',', $line );
    }

	/**
	 * @access public
	 */
    function get_last() 
	{
        return $this->last_quotes;
    }
} // END OF Stocks

?>
