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
 * @package commerce
 */
 
class Finance extends PEAR
{
	/** 
	 * Returns a number formatted as a price, eg 45115 -> 451.15
	 *
	 * @access public static
	 * @param string $price                 the number to be converted into a 'price'
	 * @param char   $centsSeparateChar     the character that is used to separate dollars from cents, franken from rappen, ... (franctions). default is a dot '.'
	 * @param char   $thousandsSeparateChar the character that is used to separate 1000-groups like 346'341'934.25 default is "'". note: this option doesn't get uses yet.
	 * @return string
	 * @todo make use of the param char $thousandsSeparateChar
	 * @todo this function is not tested enough/has some new code. don't use it in a production environment. test it before!
	 */
	function toPrice( $price = 0, $centsSeparateChar = '.', $thousandsSeparateChar = "'" ) 
	{
		$price = trim( $price );
		
		if ( strlen( $price ) == 0 ) 
			$price = "000";
		else if ( strlen( $price ) == 1 ) 
			$price = "00" . $price;
		else if ( strlen( $price ) == 2 ) 
			$price = "0" . $price;
		
		$newPrice    = round( $price ) / 100;
		$pricePoint  = strpos( $newPrice, "." );
		$priceLength = strlen( $newPrice );
		
		if ( $pricePoint == 0 ) 
			$newPrice .= ".00";
		else if ( ( $priceLength - $pricePoint == 2 ) && ( $pricePoint > 0 ) ) 
			$newPrice .= "0";
		
		if ( $centsSeparateChar != '.' ) 
			$newPrice = str_replace( '.', $centsSeparateChar, $newPrice );
			
		return $newPrice;
	}
	
	/** 
	 * Returns a percent value, eg 650 -> 6.50%
	 *
	 * @todo this function is not tested enough/has some new code. don't use it in a production environment. test it before!
	 * @access public static
	 * @param string $number       the number to be converted into a 'price'
	 * @param char   $separateChar the character that is used to separate full % from fractions. default is '.'
	 * @param string $percentSign  the % string that is attached to the new number. eg "5.35" -> "5.35%". default is '%'.
	 * @return string
	 */
	function toPercent( $number = 0, $separateChar = '.', $percentSign = '%' ) 
	{
		$number = trim( $number );
		
		if ( strlen( $number ) == 0 ) 
			$number = "000"; 
		else if ( strlen( $number ) == 1 ) 
			$number = "00" . $number;
		else if ( strlen( $number ) == 2 ) 
			$number = "0" . $number;
		
		$newPercent    = round( $number ) / 100;
		$percentPoint  = strpos( $newPercent, "." );
		$percentLength = strlen( $newPercent );
		
		if ( $percentPoint == 0 ) 
			$newPercent .= ".00";
		else if ( ( $percentLength - $percentPoint == 2 ) && ( $percentPoint > 0 ) ) 
			$newPercent .= "0";
		
		if ( $separateChar != '.' ) 
			$newPercent = str_replace( '.', $separateChar, $newPercent );
			
		return $newPercent . $percentSign;
	}
	 
	function ccVal( $Num, $Name = 'n/a' ) 
	{
		$GoodCard = true;
		$Num = ereg_replace( "[^[:digit:]]", "", $Num );
		
		switch ( $Name ) 
		{
			case "mcd" : 
				$GoodCard = ereg( "^5[1-5].{14}$", $Num );
				break;
				
			case "vis" : 
				$GoodCard = ereg( "^4.{15}$|^4.{12}$", $Num );
				break;
				
			case "amx" : 
				$GoodCard = ereg( "^3[47].{13}$", $Num );
				break;
				
			case "dsc" : 
				$GoodCard = ereg( "^6011.{12}$", $Num );
				break;
				
			case "dnc" : 
				$GoodCard = ereg( "^30[0-5].{11}$|^3[68].{12}$", $Num );
				break;
				
			case "jcb" : 
				$GoodCard = ereg( "^3.{15}$|^2131|1800.{11}$", $Num );
				break;
		}

		$Num   = strrev( $Num );
		$Total = 0;
		
		for ( $x = 0; $x < strlen( $Num ); $x++ ) 
		{
			$digit = substr( $Num, $x, 1 );
			
			if ( $x / 2 != floor( $x / 2 ) ) 
			{
				$digit *= 2;
				
				if ( strlen( $digit ) == 2 )  
					$digit = substr( $digit, 0, 1 ) + substr( $digit, 1, 1 );
			}

			$Total += $digit;
		}

		if ( $GoodCard && $Total % 10 == 0 ) 
			return true; 
		else 
			return false;
	}
} // END OF Finance

?>
