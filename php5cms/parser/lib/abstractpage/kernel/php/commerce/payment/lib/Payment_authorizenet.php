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


using( 'commerce.payment.lib.Payment' );


/**
 * Requirements:
 *
 * PHP4+, cURL, SSL
 * I have tested this class on a slackware linux system, although
 * I don't see any reason why it shouldn't work on other UNIX based
 * systems.
 *
 * @package commerce_payment_lib
 */
 
class Payment_authorizenet extends Payment
{
	/**
	 * @access private
	 */
	var $_type;
	
	/**
	 * @access private
	 */
	var $_method;
	
	 
	/**
	 * Constructor
	 */
	function Payment_authorizenet( $options = array() )
	{
		$this->Payment( $options );
		
		$this->_setGateway( "https://secure.authorize.net/gateway/transact.dll" );
		$this->_setPort( 443 );
		
		$this->_type   = "AUTH_CAPTURE"; 
        $this->_method = "CC"; // the default is for credit card responses 
		
		foreach ( $this->_options as $key => $value )
			$this->$key = urlencode( $value );
				
		if ( !$this->hasCurl() )
		{
			$this = new PEAR_Error( "Warning: Curl extension is not available." );
			return;
		}
	}
	
	
	/**
	 * Process request.
	 *
	 * @return bool   true if success, otherwise false
	 * @access public
	 */
    function process() 
    { 
        $data    = $this->_getUrlData();
		$gateway = $this->_getGateway();

		// run curl with the data 
        exec( $this->getCurlPath() . " -d \"$data\" $gateway", $returnArray );

        $returnString = ""; 
        
		foreach( $returnArray as $item )
			$returnString .= $item; 
         
        $returnArray  = array(); 
        $returnArray  = split( "\|", $returnString ); 
        $responseCode = $returnArray[2]; 
        
		if ( $responseCode == 1 )
		{
			return true;
		}
        else  
        { 
            if ( $returnArray[3] )
				$this->_error = $returnArray[3]; 
            else
				$this->_error = $returnString; 
             
            return false; 
        } 
    }
	
	/**
	 * Set method.
	 *
	 * @param  string
	 * @access public
	 */
	function setMethod( $method = "CC" )
	{
		$this->_method = strtoupper( $method );
	}
	
	/**
	 * Get method.
	 *
	 * @access public
	 */
	function getMethod()
	{
		return $this->_method;
	}
	
	/**
	 * Set type.
	 *
	 * @param  string
	 * @access public
	 */
	function setType( $type = "AUTH_CAPTURE" )
	{
		$this->_type = strtoupper( $type );
	}
	
	/**
	 * Get type.
	 *
	 * @access public
	 */
	function getType()
	{
		return $this->_type;
	}
	
	
	// private methods
	
	/**
	 * Construct URL.
	 *
	 * @return string
	 * @access private
	 */
    function _getUrlData() 
    { 
        $data .=  
        "x_Login="                    . $this->loginid .	// the login id 
        "&x_ADC_Delim_Data="          . "TRUE" .			// make it ADC 
        "&x_ADC_Delim_Character="     . "|" .				// the character to delim by 
        "&x_ADC_URL="                 . "FALSE" . 
        "&x_Email_Customer="          . $this->customeremail . 
        "&x_Address="                 . $this->address . 
        "&x_City="                    . $this->city . 
        "&x_State="                   . $this->state . 
        "&x_Zip="                     . $this->zip . 
        "&x_Country="                 . $this->country . 
        "&x_Phone="                   . $this->phone . 
        "&x_Type="                    . $this->_type . 
        "&x_Method="                  . $this->_method . 
        "&x_First_Name="              . $this->firstname . 
        "&x_Last_Name="               . $this->lastname . 
        "&x_Amount="                  . $this->amount; 
         
		// if it is a credit card transaction 
        if ( $this->_method == "CC" )
        { 
            $data .= 
            "&x_Card_Num=" 		. $this->cardnumber . 
            "&x_Exp_Date=" 		. $this->expiredate; 
        } 
         
		// if it is a check transaction 
        if ( $this->_method == "ECHECK" )
        { 
            $data .= 
            "&x_Bank_ABA_Code="	. $this->bankabacode . 
            "&x_Bank_Acct_Num="	. $this->bankacctnum . 
            "&x_Bank_Name="		. $this->bankname; 
        }

        return $data; 
    } 
} // END OF Payment_authorizenet

?>
