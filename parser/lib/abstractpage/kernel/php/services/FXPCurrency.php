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
 * Uses OANDA to retrive FXP currency rate data, compared to USD 
 * eventually a MySQL interface will cache the data, but for now, it is real time.
 *
 * @package services
 */
 
class FXPCurrency extends PEAR
{ 
	/**
	 * @access public
	 */
	var $rate; 
	
	/**
	 * @access public
	 */
    var $from; 
	
	/**
	 * @access public
	 */
    var $to; 

	/**
	 * @access public
	 */	
	var $fxp_server = "www.oanda.com"; 
	
	/**
	 * @access public
	 */
	var $fxp_port   = 5011; 
    
	/**
	 * @access public
	 */
	var $lookup = array ( 
		"usd" => 1, 
		"ukp" => 0.69330, 
		"eur" => 1.12273, 
    ); 
	
	
	/**
	 * @access public
	 */     
    function fxp( $currency ) 
	{ 
		if ( $currency == "" ) 
		{ 
			// This will get ALL currencies, useful for updating. 
			$currency = "USD"; 
		} 
		else 
		{ 
			// currency code 
        } 
         
        if ( $s = fsockopen( $this->fxp_server, $this->fxp_port, &$err_num, &$err_msg, 5 ) ) 
		{ 
			fputs( $s, "fxp/1.1\r\nbasecurrency: USD\r\nquotecurrency: $currency\r\n\r\n" ); 
			$reply = fgets( $s, 128 ); 
			
			if ( trim( $reply ) == "fxp/1.1 200 ok" ) 
			{ 
				// dump first line 
				while ( $reply != "\r\n" )  
					$reply = fgets( $s, 128 ); 
                        
				// get next line 
				if ( !$reply = fgets( $s, 128 ) )  
					$reply = "0"; 
			} 
			else 
			{ 
				$reply = "0"; 
			} 
		} 
		else 
		{ 
			return PEAR::raiseError( "Could not retrive currency information: $err_msg ($err_num)." );
        } 
		
        fclose( $s ); 
        return trim( $reply ); 
    } 

	/**
	 * @access public
	 */     
    function quick( $from, $to, $amount ) 
	{ 
		// for inline processing 
        $this->from( $from ); 
        $this->to( $to ); 
        $this->convert( $amount ); 
		
        return $this->result; 
    } 

	/**
	 * @access public
	 */
    function convert( $amount ) 
	{ 
        $this->rate(); 
        $this->result = round( $this->rate * $amount, 2 ); 
    } 

	/**
	 * @access public
	 */
    function from( $from ) 
	{ 
        $this->from = $from; 
    } 

	/**
	 * @access public
	 */
    function to( $to ) 
	{ 
        $this->to = $to; 
    } 

	/**
	 * This function calculates the relative rate 
	 * everything is calculated relative to the usd.
	 *
	 * @access public
	 */
    function rate() 
	{ 
        $this->rate = ( $this->lookup[$this->from] / $this->lookup["usd"] ) * $this->lookup[$this->to]; 
    }         
} // END OF FXPCurrency

?> 
