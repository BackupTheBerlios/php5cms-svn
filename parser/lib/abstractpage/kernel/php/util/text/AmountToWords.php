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
 * @package util_text
 */
 
class AmountToWords extends PEAR
{
	/**
	 * @access public
	 */
	var $amount_in_words;
	
	/**
	 * @access public
	 */
	var $decimal;
	
	/**
	 * @access public
	 */
	var $decimal_len;
	
	/**
	 * @access public
	 */
	var $words = array();
	
	/**
	 * @access public
	 */
	var $places = array();
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function AmountToWords( $amount, $currency = "Pesos" )
	{
		$this->assign();
		
		$temp = (string)$amount;
		$pos  = strpos( $temp, "." );
		
		if ( $pos )
		{
			$temp = substr( $temp, 0, $pos );
			$this->decimal = strstr( (string)$amount, "." );
			$this->decimal_len = strlen( $this->decimal ) - 2;
			$this->decimal = substr( $this->decimal, 1, $this->decimal_len + 1 );
		}
		
		$len = strlen( $temp )-1;
		$ctr = 0;
		$arr = array();
		
		while ( $len >= 0 )
		{
			if ( $len >= 2 )
			{
				$arr[$ctr++] = substr( $temp, $len - 2, 3 );
				$len -= 3;
			}
			else
			{
				$arr[$ctr++] = substr( $temp, 0, $len + 1 );
				$len = -1;
			}
		}
		
		$str = "";
		
		for ( $i = count( $arr ) - 1; $i >= 0; $i-- )
		{
			$figure = $arr[$i];
			$sub    = array();
			$temp   ="";
			
			for ( $y = 0; $y < strlen( trim( $figure ) ); $y++ )
				$sub[$y] = substr( $figure, $y, 1 );
			
			$len = count( $sub );
			
			if ( $len == 3 )
			{
				if ( $sub[0] != "0" )
					$temp .= ( ( strlen( $str ) > 0 )? " " : "" ) . trim( $this->words[$sub[0]] ) . " Hundred";
				
				$temp .= $this->processTen( $sub[1], $sub[2] );
			}
			else if ( $len == 2 )
			{
				$temp .= $this->processTen( $sub[0], $sub[1] );
			}
			else
			{
				$temp .= $words[$sub[0]];
			}
			
			if ( strlen( $temp ) > 0 )
				$str .= $temp . $this->places[$i];
		}
		
		$str .= " " . $currency;
		
		if ( $this->decimal_len > 0 )
			$str .= " And " . $this->decimal . "/" . $this->denominator( $this->decimal_len + 1 ) .  " Cents";
		
		$this->amount_in_words = $str;
	}
	
	
	/**
	 * @access public
	 */
	function denominator( $x )
	{
		$temp = "1";
		
		for ( $i = 1; $i <= $x; $i++ )
			$temp .= "0";
		
		return $temp;
	}
	
	/**
	 * @access public
	 */
	function display()
	{
		echo $this->amount_in_words;
	}

	/**
	 * @access public
	 */
	function processTen( $sub1, $sub2 )
	{
		if ( $sub1 == "0" )
		{
			if ( $sub2 == "0" )
				return "";
			else
				return $this->words[$sub2];
		}
		else if ( $sub1 != "1" )
		{
			if ( $sub2 != "0" )
				return $this->words[$sub1 . "0"] . $this->words[$sub2];
			else
				return $this->words[$sub1 . $sub2];
		}
		else
		{
			if ( $sub2 == "0" )
				return $this->words["10"];
			else
				return $this->words[$sub1 . $sub2];
		}
	}

	/**
	 * @access public
	 */
	function assign()
	{
		$this->words["1"]  = " One";
		$this->words["2"]  = " Two";
		$this->words["3"]  = " Three";
		$this->words["4"]  = " Four";
		$this->words["5"]  = " Five";
		$this->words["6"]  = " Six";
		$this->words["7"]  = " Seven";
		$this->words["8"]  = " Eight";
		$this->words["9"]  = " Nine";
	
		$this->words["10"] = " Ten";
		$this->words["11"] = " Eleven";
		$this->words["12"] = " Twelve";
		$this->words["13"] = " Thirten";
		$this->words["14"] = " Fourten";
		$this->words["15"] = " Fiften";
		$this->words["16"] = " Sixten";
		$this->words["17"] = " Seventen";
		$this->words["18"] = " Eighten";
		$this->words["19"] = " Nineten";

		$this->words["20"] = " Twenty";
		$this->words["30"] = " Thirty";
		$this->words["40"] = " Forty";
		$this->words["50"] = " Fifty";
		$this->words["60"] = " Sixty";
		$this->words["70"] = " Seventy";
		$this->words["80"] = " Eighty";
		$this->words["90"] = " Ninety";
	
		$this->places[0]   = "";
		$this->places[1]   = " Thousand";
		$this->places[2]   = " Million";
		$this->places[3]   = " Billion";
		$this->places[4]   = " Thrillion";
	}
} // END OF AmountToWords

?>
