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
 * CurrencyConverter can convert from USD, CAD, EUR and GBP to 
 * over 150 different currencies.
 *	
 * Usage:
 *
 * $c = new CurrencyConverter();
 *
 * if ( !PEAR::isError( $c ) )
 * {
 *		$convert = array( 
 *			array( 'from' => 'USD', 'to' => 'ILS' ),
 *			array( 'from' => 'USD', 'to' => 'ZWD' ),
 *			array( 'from' => 'CAD', 'to' => 'USD' )
 *		);
 *                 
 *		foreach ( $convert as $arr ) 
 *		{
 *			echo "1 ";
 * 			echo $c->getName( $arr['from'] );
 *			echo " = ";
 *			echo $c->convert( $arr['from'], $arr['to'] );
 *			echo " ";
 *			echo $c->getName( $arr['to'] );
 *			echo "<P>";
 *  	}
 * }
 *
 * @package commerce
 */

class CurrencyConverter extends PEAR
{
	/**
	 * @access public
	 */
    var $URL = "pacific.commerce.ubc.ca";
	
	/**
	 * @access public
	 */
    var $URI = "/xr/rates.html";
    
	/**
	 * @access public
	 */
    var $fp;
	
    /**
	 * The conversion rates
	 * @access public
	 */
    var $conv;
    
    /**
	 * The names of the currencies
	 * @access public
	 */
    var $names;
    
    /**
	 * A list that keeps the name of currencies that are realy 'aliases' to 
     * the same currency but with a different name
	 * @access public
	 */
    var $blacklist = array(
		'Nauru Island Dollar' 				=> true,
		'Christmas Island Dollar'			=> true,
		'Cocoskeeling Island' 				=> true,
		'Norfolk Island Dollar' 			=> true,
		'Kiribati Dollar' 					=> true,
		'Tuvalu Dollar' 					=> true,
		'Heard & Mcdonald Island Dollar'	=> true,
		'Liechtenstein Franc' 				=> true,
		'Faroe Island Krone' 				=> true,
		'North Africa Peseta' 				=> true,
		'French Guiana Franc' 				=> true,
		'Guadaloupe Franc' 					=> true,
		'St. Pierre Franc' 					=> true,
		'Miquelon Franc' 					=> true,
		'Andorran Franc' 					=> true,
		'Monaco Franc' 						=> true,
		'Martinique Franc' 					=> true,
		'Reunion Franc' 					=> true,
		'San Marino Lira' 					=> true,
		'Vatican City Lira' 				=> true,
		'Dronning Maudland Krone' 			=> true,
		'Bouvet Island Kroner' 				=> true,
		'Pitcairn Island Dollar' 			=> true,
		'Tokelau Dollar' 					=> true,
		'Nieue Dollar'		 				=> true,
		'Azores Escudo' 					=> true,
		'Madeira Escudo' 					=> true,
		'Samoa American Dollar' 			=> true,
		'Johnston Island Dollar' 			=> true,
		'Midway Island Dollar' 				=> true,
		'Turks & Caicos Dollar' 			=> true,
		'Virgin Island Dollar' 				=> true,
		'Puerto Rico Dollar'	 			=> true,
		'Brit. Indian Ocean Terr.'		 	=> true,
		'Guam Dollar' 						=> true,
		'Cameroon Franc' 					=> true,
		'Congo Franc' 						=> true,
		'Equatorial Guinea' 				=> true,
		'Gabon Franc' 						=> true,
		'Chad Franc' 						=> true,
		'St. Lucia Dollar' 					=> true,
		'Dominica Dollar' 					=> true,
		'Grenada Dollar' 					=> true,
		'St. Kitts Dollar' 					=> true,
		'Montserrat Dollar' 				=> true,
		'St. Vincent Dollar' 				=> true,
		'East Caribbean Dollar' 			=> true,
		'Burkino Faso' 						=> true,
		'Senegal Franc'			 			=> true,
		'Togo Republic Franc' 				=> true,
		'Ivory Coast Franc' 				=> true,
		'Mali Republic Franc' 				=> true,
		'Benin Franc' 						=> true,
		'Namibia Dollar' 					=> true,
		'South African Rand/fin' 			=> true
	);
    
    
    /**
	 * Constructor
	 *
	 * @access public
	 */ 
    function CurrencyConverter() 
	{
        $this->fp = fsockopen( $this->URL, 80, $errno, $errstr, 60 );        

        if ( !$this->fp ) 
		{
			$this = new PEAR_Error( "$errstr ($errno)" );
			return;
        } 
		else 
		{
            fputs( $this->fp, "GET $this->URI HTTP/1.0\r\n\r\n" );
            
            while ( !feof( $this->fp ) ) 
			{
                $line = fgets( $this->fp, 1024 );
				
                if ( eregi( "<tt>([[:alnum:]]+)</tt>", $line, $reg ) ) 
				{
                    $currency = $reg[1];
                    $line = fgets( $this->fp, 1024 );
                    eregi( ">([^<]+)</", $line, $reg );
					
                    if ( $this->blacklist[$reg[1]] ) 
						continue;
                    
					$this->names[$currency] = $reg[1];
                    
                    $this->_enterConvertion( 'CAD', $currency );
                    $this->_enterConvertion( 'USD', $currency );
                    $this->_enterConvertion( 'EUR', $currency );
                    $this->_enterConvertion( 'GBP', $currency );
                }
            }
            fclose ($this->fp);
        }
    }

    
    /**
	 * Given two currency codes returns their exchange rate.
	 *
	 * @access public
	 */
    function convert( $from, $to ) 
	{
        return $this->conv[$from][$to];
    }
    
    /**
	 * Given a curreny code returns it's full name.
	 *
	 * @access public
	 */
    function getName( $currency ) 
	{
        return $this->names[$currency];
    }
    
	
	// private methods

	/**
	 * @access private
	 */
    function _enterConvertion( $from, $to ) 
	{
        $line = fgets( $this->fp, 1024 );
        eregi( ">([^<]+)</", $line, $reg );
        $this->conv[$from][$to] = $reg[1];
    }
} // END OF CurrencyConverter

?>
