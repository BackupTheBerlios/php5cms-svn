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
|         M.W.Heald <matthew.heald@virgin.net>                         |
+----------------------------------------------------------------------+
*/


/**
 * @package commerce
 */
 
class CreditCard extends PEAR
{
	/**
	 * @access public
	 * @static
	 */
  	function check( $cc_no, $valid_from, $valid_to )
  	{  
		$cc_no   = CreditCard::_cleanNo( $cc_no );
     	$valid   = CreditCard::_validate( $cc_no );
     	$cc_data = CreditCard::_identifyCard( $cc_no );
     	$dates   = CreditCard::_checkDates( $valid_from, $valid_to );
     	
		return array(
			'valid'     => $valid,
			'index'     => $cc_data['index'], 
			'type'      => $cc_data['type'],
			'validto'   => $dates['validto'], 
			'validfrom' => $dates['validfrom']
		);
	}
		
	
	// private methods

	/**
	 * Get card type based on prefix and length of card number.
	 *
	 * @access private
	 * @static
	 */
  	function _identifyCard( $cc_no )
  	{  
     	if ( ereg( '^5[1-5].{14}$', $cc_no ) )
		{
			return array(
				'type'  => 'Mastercard',
				'index' => 11
			);
		}

     	if ( ereg( '^6334[5-9].{11}$', $cc_no ) )
		{
			return array(
				'type'  => 'Solo / Maestro',
				'index' => 16
			);
		}
     	
		if ( ereg( '^6767[0-9].{11}$', $cc_no ) )
		{
			return array(
				'type'  => 'Solo / Maestro',
				'index' => 16
			);
		}

     	if ( ereg( '^564182[0-9].{9}$', $cc_no ) )
		{
			return array(
				'type'  => 'Switch/Maestro',
				'index' => 19
			);
		}
		
     	if ( ereg( '^6333[0-4].{11}$', $cc_no ) )
		{
			return array(
				'type'  => 'Switch/Maestro',
				'index' => 19
			);
		}
     	
		if ( ereg( '^6759[0-9].{11}$', $cc_no ) )
		{
			return array(
				'type'  => 'Switch/Maestro',
				'index' => 19
			);
		}

		if ( ereg( '^49030[2-9].{10}$', $cc_no ) )
		{
			return array(
				'type'  => 'Switch',
				'index' => 18
			);
		}
     	
		if ( ereg( '^49033[5-9].{10}$', $cc_no ) )
		{
			return array(
				'type'  => 'Switch',
				'index' => 18
			);
		}
		
     	if ( ereg( '^49110[1-2].{10}$', $cc_no ) )
		{
			return array(
				'type'  => 'Switch',
				'index' => 18
			);
		}
     	
		if ( ereg( '^49117[4-9].{10}$', $cc_no ) )
		{
			return array(
				'type'  => 'Switch',
				'index' => 18
			);
		}
     	
		if ( ereg( '^49118[0-2].{10}$', $cc_no ) )
		{
			return array(
				'type'  => 'Switch',
				'index' => 18
			);
		}
     	
		if ( ereg( '^4936[0-9].{11}$', $cc_no ) )
		{
			return array(
				'type'  => 'Switch',
				'index' => 18 
			);
		}

     	if ( ereg( '^6011.{12}$', $cc_no ) )
		{
			return array(
				'type'  => 'Discover Card',
				'index' => 23 
			);
		}

     	// failing earlier 6xxx xxxx xxxx xxxx checks then its a Maestro card
     	if ( ereg( '^6[0-9].{14}$', $cc_no ) )
		{
			return array(
				'type'  => 'Maestro',
				'index' => 20
			);
		}
     	
		if ( ereg( '^5[0,6-8].{14}$', $cc_no ) )
		{
			return array(
				'type'  => 'Maestro',
				'index' => 20
			);
		}

     	if ( ereg( '^450875[0-9].{9}$', $cc_no ) )
		{
			return array(
				'type'  => 'UK Electron',
				'index' => 21
			);
		}
     	
		if ( ereg( '^48440[6-8].{10}$', $cc_no ) )
		{
			return array(
				'type'  => 'UK Electron',
				'index' => 21
			);
		}
     	
		if ( ereg( '^48441[1-9].{10}$', $cc_no ) )
		{
			return array(
				'type'  => 'UK Electron',
				'index' => 21
			);
		}
     	
		if ( ereg( '^4844[2-4].{11}$', $cc_no ) )
		{
			return array(
				'type'  => 'UK Electron',
				'index' => 21
			);
		}
     	
		if ( ereg( '^48445[0-5].{10}$', $cc_no ) )
		{
			return array(
				'type'  => 'UK Electron',
				'index' => 21 
			);
		}
     	
		if ( ereg( '^4917[3-5].{11}$', $cc_no ) )
		{
			return array(
				'type'  => 'UK Electron',
				'index' => 21 
			);
		}
     	
		if ( ereg( '^491880[0-9].{9}$', $cc_no ) )
		{
			return array(
				'type'  => 'UK Electron',
				'index' => 21
			);
		}

     	if ( ereg( '^41373[3-7].{10}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa Delta',
				'index' => 13
			);
		}
     	
		if ( ereg( '^4462[0-9].{11}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa Delta',
				'index' => 13
			);
		}
     	
		if ( ereg( '^45397[8-9].{10}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa Delta',
				'index' => 13 
			);
		}
     	
		if ( ereg( '^454313[0-9].{9}$', $cc_no ) ) 
		{
			return array(
				'type'  => 'Visa Delta',
				'index' => 13 
			);
		}
     	
		if ( ereg( '^45443[2-5].{10}$', $cc_no ) ) 
		{
			return array(
				'type'  => 'Visa Delta',
				'index' => 13 
			);
		}
     	
		if ( ereg( '^454742[0-9].{9}$', $cc_no ) ) 
		{
			return array(
				'type'  => 'Visa Delta',
				'index' => 13 
			);
		}
     	
		if ( ereg( '^45672[5-9].{10}$', $cc_no ) ) 
		{
			return array(
				'type'  => 'Visa Delta',
				'index' => 13 
			);
		}
     	
		if ( ereg( '^45673[0-9].{10}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa Delta',
				'index' => 13 
			);
		}
     	
		if ( ereg( '^45674[0-5].{10}$', $cc_no ) ) 
		{
			return array(
				'type'  => 'Visa Delta',
				'index' => 13 
			);
		}
     	
		if ( ereg( '^4658[3-7].{11}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa Delta',
				'index' => 13
			);
		}
     	
		if ( ereg( '^4659[0-5].{11}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa Delta',
				'index' => 13
			);
		}
     	
		if ( ereg( '^484409[0-9].{9}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa Delta',
				'index' => 13
			);
		}
     	
		if ( ereg( '^48441[0-9].{10}$', $cc_no ) ) 
		{
			return array(
				'type'  => 'Visa Delta',
				'index' => 13
			);
		}
     	
		if ( ereg( '^4909[6-7].{11}$', $cc_no ) ) 
		{
			return array(
				'type'  => 'Visa Delta',
				'index' => 13
			);
		}
     	
		if ( ereg( '^49218[1-2].{10}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa Delta',
				'index' => 13
			);
		}
     	
		if ( ereg( '^498824[0-9].{9}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa Delta',
				'index' => 13
			);
		}

     	if ( ereg( '^40550[1-4].{10}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa Purchasing',
				'index' => 12
			);
		}
     	
		if ( ereg( '^40555[0-4].{10}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa Purchasing',
				'index' => 12
			);
		}
     	
		if ( ereg( '^415928[0-4].{9}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa Purchasing',
				'index' => 12
			);
		}
     	
		if ( ereg( '^42460[4-5].{10}$', $cc_no ) ) 
		{
			return array(
				'type'  => 'Visa Purchasing',
				'index' => 12
			);
		}
     	
		if ( ereg( '^427533[0-9].{9}$', $cc_no ) ) 
		{
			return array(
				'type'  => 'Visa Purchasing',
				'index' => 12 
			);
		}
     	
		if ( ereg( '^4288[0-9].{11}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa Purchasing',
				'index' => 12 
			);
		}
     	
		if ( ereg( '^443085[0-9].{9}$', $cc_no ) ) 
		{
			return array(
				'type'  => 'Visa Purchasing',
				'index' => 12 
			);
		}
     	
		if ( ereg( '^448[4-6].{12}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa Purchasing',
				'index' => 12 
			);
		}
     	
		if ( ereg( '^471[5-6].{12}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa Purchasing',
				'index' => 12 
			);
		}
     	
		if ( ereg( '^4804[0-9].{11}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa Purchasing',
				'index' => 12 
			);
		}

     	if ( ereg( '^49030[0-1].{10}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa ATM',
				'index' => 14 
			);
		}
     	
		if ( ereg( '^4903[1-2].{11}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa ATM',
				'index' => 14 
			);
		}
     	
		if ( ereg( '^49033[0-4].{10}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa ATM',
				'index' => 14 
			);
		}
     	
		if ( ereg( '^4903[4-9].{11}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa ATM',
				'index' => 14 
			);
		}
     	
		if ( ereg( '^49040[0-9].{10}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa ATM',
				'index' => 14 
			);
		}
     	
		if ( ereg( '^490419[0-9].{9}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa ATM',
				'index' => 14 
			);
		}
     	
		if ( ereg( '^490451[0-9].{9}$', $cc_no ) )  
		{
			return array(
				'type'  => 'Visa ATM',
				'index' => 14 
			);
		}
     	
		if ( ereg( '^490459[0-9].{9}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa ATM',
				'index' => 14 
			);
		}
     	
		if ( ereg( '^490467[0-9].{9}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa ATM',
				'index' => 14 
			);
		}
     	
		if ( ereg( '^49047[5-8].{10}$', $cc_no ) )  
		{
			return array(
				'type'  => 'Visa ATM',
				'index' => 14 
			);
		}
     	
		if ( ereg( '^4905[0-9].{11}$', $cc_no ) )   
		{
			return array(
				'type'  => 'Visa ATM',
				'index' => 14 
			);
		}
     	
		if ( ereg( '^491001[0-9].{9}$', $cc_no ) )  
		{
			return array(
				'type'  => 'Visa ATM',
				'index' => 14 
			);
		}
     	
		if ( ereg( '^49110[3-9].{10}$', $cc_no ) )  
		{
			return array(
				'type'  => 'Visa ATM',
				'index' => 14 
			);
		}
     	
		if ( ereg( '^4911[1-6].{11}$', $cc_no ) )   
		{
			return array(
				'type'  => 'Visa ATM',
				'index' => 14 
			);
		}
     	
		if ( ereg( '^49117[0-3].{10}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa ATM',
				'index' => 14 
			);
		}
     	
		if ( ereg( '^49118[3-9].{10}$', $cc_no ) )  
		{
			return array(
				'type'  => 'Visa ATM',
				'index' => 14 
			);
		}
     	
		if ( ereg( '^49119[0-9].{10}$', $cc_no ) )  
		{
			return array(
				'type'  => 'Visa ATM',
				'index' => 14 
			);
		}
     	
		if ( ereg( '^4928[0-9].{11}$', $cc_no ) )   
		{
			return array(
				'type'  => 'Visa ATM',
				'index' => 14 
			);
		}
     	
		if ( ereg( '^4987[0-9].{11}$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa ATM',
				'index' => 14 
			);
		}

     	// failing earlier 4xxx xxxx xxxx xxxx checks then it must be a Visa
     	if ( ereg( '^4(.{12}|.{15})$', $cc_no ) )
		{
			return array(
				'type'  => 'Visa',
				'index' => 12 
			);
		}

     	if ( ereg( '^3[4-7].{13}$', $cc_no ) )
		{
			return array(
				'type'  => 'American Express',
				'index' => 18 
			);
		}

     	if ( ereg( '^3(0[0-5].{11}|[6].{12}|[8].{12})$', $cc_no ) )
		{
			return array(
				'type'  => 'Diners Club/Carte Blanche',
				'index' => 19 
			);
		}

     	if ( ereg( '^(3.{15}|(2131|1800).{11})$', $cc_no ) )
		{
			return array(
				'type'  => 'JCB',
				'index' => 21
			);
		}
     	
		if ( ereg( '^(3528[0-9].{11})$', $cc_no ) ) 
		{
			return array(
				'type'  => 'JCB',
				'index' => 21 
			);
		}
     	
		if ( ereg( '^(35[3-8].{13})$', $cc_no ) )
		{
			return array(
				'type'  => 'JCB',
				'index' => 21 
			);
		}

     	if ( ereg( '^2(014|149).{11})$', $cc_no ) ) 
		{
			return array(
				'type'  => 'enRoute',
				'index' => 22 
			);
		}

		
		// the following are from http://www.merriampark.com/anatomycc.htm
		// put in for 'fullness' - the following indicate the broad type of card
		// if you are in a business that can reasonably be expected to accept fuel cards then you could accept cards starting with a 7
		// please note I do not know how many digits there could be on the card, some specs suggest that cards can have
		// upto 19 digits including the check digit.
		//
		// cards starting with a ? are issued by the following industry sectors
		//	0	ISO/OTC 68 and related industries - if you know what that means good luck
		//	1	Airlines
		//	2	Airlines and other industries
		//	3	Travel and Entertainment	comment: AMEX / Cart Blanche etc
		//	4	Banking and Financial		comment: read VISA
		//	5	Banking and Financial		comment: read MasterCard
		//	6	Merchandising and Banking	comment: read store cards, bank cash cards / EFT cards
		//	7	Petroleum					comment: read fuel cards
		//	8	Telecomm and related
		//	9	National assignements		comment: who knows, who cares?

    	return array(
			'type'  => 'unknown or invalid',
			'index' => 0
		);
  	}
  
	/**
	 * Implements the 'Luhn' modulo 10 check on the supplied number.
	 *
	 * @access private
	 * @static
	 */
	function _validate( $cc_no )
  	{ 
		$cc_no     = strrev( $cc_no );
    	$no_digits = strlen( $cc_no );
    	$checksum  = 0;
    
		for ( $digit = 0; $digit < $no_digits; $digit = $digit + 2 )
    		$checksum = $checksum + ( $cc_no[$digit] ) + CreditCard::_singleDigit( ( $cc_no[$digit + 1] * 2 ) );
    
    	if ( floor( $checksum / 10 ) != ( $checksum / 10 ) )
			return false;
		else
			return true;
  	}
	
	/**
	 * Remove non-numeric characters from $cc_no.
	 *
	 * @access private
	 * @static
	 */
	function _cleanNo( $cc_no )
  	{
    	return ereg_replace( '[^0-9]+', '', $cc_no );
  	}
	
  	/**
	 * @access private
	 * @static
	 */
  	function _singleDigit( $iDigit )
  	{ 
		// if the number is greater than 10 subtract 9 to generate a single digit
		if ( $iDigit >= 10 )
			$iDigit = $iDigit - 9; // reqired for the Luhn check
    	
		return $iDigit;
 	}
	
	/**
	 * This function validates the dates.
	 * The valid from can be mm/yy or xx or NULL (a date, or issue number or nothing).
     *
	 * @access private
	 * @static
	 */
  	function _checkDates( $vfrom, $vto )
  	{ 
    	$error_code['validfrom'] = false; // indicates the Valid From / Issue number has an error
    	$error_code['validto']   = false; // indicates the Valid To date has an error
    	
		// vfrom is either a 2 digit number OR as date in the form mm/yy
    	if ( isset( $vfrom ) == true )
    	{ 
			if ( strlen( $vfrom ) == 2 )
      		{ 
				if ( ereg( "^[[:digit:]]{2}$", $vfrom ) != true )
        			$error_code['validfrom'] = true;
      		} 
			else if ( strlen( $vfrom ) == 5 )
      		{ 
				if ( ereg( "^[[:digit:]/[:digit:]]${5}", $vfrom ) != true )
        		{ 
					$error_code['validfrom'] = true;
        		} 
				else
        		{ 
					$tVFr = explode( "/", $vfrom );
          			
					if ( $tVFr[0] <=0 || $tVFr[0] >= 13 )
						$error_code['validfrom'] = true;
          			
					// year cannot be greater than current year
					if ( $tVFr[1] > date( y ) )
          			{ 
						$error_code['validfrom'] = true;
          			} 
					// if the years are the same then the month cannot be greater than the current month
					else if ( $tVFr[1] == date( y ) )
          			{ 
						if ( $tVFr[0] > date( m ) )
							$error_code['validfrom'] = true;
          			}
        		}
      		} 
			// catch all (ie neither 2 or 5 characters supplied
			else if ( strlen( $vfrom ) > 0 )
      		{ 
				$error_code['validfrom'] = true;
      		}
    	}
    	
		if ( isset( $vto ) == true )
    	{ 
			if ( strlen( $vto ) == 5 )
      		{ 
				if ( ereg( "^[[:digit:]/[:digit:]]${5}", $vto ) != true )
        		{ 
					$error_code['validto'] = true;
        		} 
				else
        		{ 
					$tVTo = explode( "/", $vto );
          			
					if ( $tVTo[0] <=0 || $tVTo[0] >= 13 )
						$error_code['validto'] = true;
          			
					// year cannot be less than current year
					if ( $tVTo[1] < date( y ) )
          			{ 
						$error_code['validto'] = true;
          			} 
					// if the years are the same then the month cannot be less than the current month
					else if ( $tVTo[1] == date( y ) )
          			{ 
						if ( $tVTo[0] < date( m ) )
							$error_code['validto'] = true;
          			}
        		}
      		} 
			else
      		{ 
				$error_code['validto'] = true;
      		}
    	}
    	
		// so finally the From date MUST be less than or equal to the To date
    	return array(
			'validto'   => $error_code['validto'],
			'validfrom' => $error_code['validfrom']
		);
	}
} // END OF CreditCard

?>
