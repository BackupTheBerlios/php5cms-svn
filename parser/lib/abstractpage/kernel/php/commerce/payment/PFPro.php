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
 * Verisign PFPro payment processing class
 * Please consult the payflowpro guide available from the Verisign Manager (https://manager.verisign.com)
 *
 * Example:
 *
 * $tx = new PFPro;
 *
 * // for a sale - settle imediately
 * $tx->sale( 22.22, "4111 1111 1111 1111","02","2003" );
 *
 * // ...or an authorization (settle later)
 * $tx->authorize( 22.22, "4111 1111 1111 1111","02","2002" );
 *
 * $tx->credit( "VABC12345678" );
 * $tx->capture( "VABC12345678" );
 * $tx->voidSale( "VABC12345678" );
 * $tx->avs( "434 Test Ave","32612-3422" );
 * $tx->comments( "comment 1", "comment 2" );
 * $tx->process();
 *
 * // $tx->result is the array returned from Verisign.
 * if ( $tx->result['RESULT'] == "0" )
 * {
 *   	// Transaction success
 *  	print "Transaction success";
 *  	// process order
 * }
 * else
 * {
 *  	print "Transaction error";
 *  	// log error and try again
 * }
 *
 * // See complete transaction request and result
 * print "<pre>";
 * print_r( $tx->transaction );
 * print_r( $tx->result );
 * print "</pre>";
 *
 * @package commerce_payment
 */ 

class PFPro extends PEAR
{
	/**
	 * @access public
	 */
    var $host;
	
	/**
	 * @access public
	 */
    var $port;
	
	/**
	 * @access public
	 */
    var $timeout;
	
	/**
	 * @access public
	 */
    var $proxyaddress; 
	
	/**
	 * @access public
	 */
    var $proxyport;
	
	/**
	 * @access public
	 */
    var $proxyuser;
	
	/**
	 * @access public
	 */
    var $proxypassword; 

	/**
	 * @access public
	 */
	      
    var $transaction;
	
	/**
	 * @access public
	 */
    var $result;

	
    /**
	 * Constructor
	 *
	 * @access public
	 */  
    function PFPro()
	{
        $this->transaction = array(); 
        $this->result = array(); 
         
        $this->transaction['VENDOR']  = "VENDOR";   // Case-sensitive login.
        $this->transaction['USER']    = "USER";     // Case-sensitive. Use your login for this parameter. In future releases you will be able to use this parameter to create multiple users for a single account.
        $this->transaction['PWD']     = "PASSWORD"; // Case-sensitive password.
        $this->transaction['PARTNER'] = "VeriSign"; // This field is case-sensitive. Your partner ID is provided to you by the authorized VeriSign Reseller who signed you up for the Payflow Pro service. If you signed up yourself, use VeriSign.
         
        $this->host          = "test-payflow.verisign.com"; 
        $this->port          = 443; 
        $this->timeout       = 30; 
        $this->proxyaddress  = null; 
        $this->proxyport     = null; 
        $this->proxyuser     = null; 
        $this->proxypassword = null; 
    } 


    /** 
	 * Charge and settle a transaction using a credit card. 
	 *
     * @return void 
     * @param  amount float 
     * @param  card_no int 
     * @param  exp_month int 
     * @param  exp_year int
	 * @access public
     */ 
    function sale( $amount, $card_no, $exp_month, $exp_year )
	{ 
        $this->transaction['TRXTYPE'] = "S"; 
        $this->transaction['TENDER']  = "C"; 
        $this->transaction['AMT']     = sprintf( "%.2f", $amount ); 
        $this->transaction['ACCT']    = ereg_replace( "[^0-9]", "", $card_no ); 
        $this->transaction['EXPDATE'] = $exp_month . substr( $exp_year, -2 ); 
    }

    /** 
	 * Authorize a credit card for later settlement. 
	 *
     * @return void 
     * @param  amount float 
     * @param  card_no int 
     * @param  exp_month int 
     * @param  exp_year int
	 * @access public
     */ 
    function authorize( $amount, $card_no, $exp_month, $exp_year )
	{ 
        $this->transaction['TRXTYPE'] = "A"; 
        $this->transaction['TENDER']  = "C"; 
        $this->transaction['AMT']     = sprintf( "%.2f", $amount ); 
        $this->transaction['ACCT']    = ereg_replace( "[^0-9]", "", $card_no ); 
        $this->transaction['EXPDATE'] = $exp_month . substr( $exp_year, -2 ); 
    } 
     
    /** 
	 * Request a settlement from a previous authorization request. 
	 * Optional amount to specify a lower or higher (additional charges apply) amount.
	 *
     * @return void 
     * @param  PNREF string 
     * @param  amount float
	 * @access public
     */ 
    function capture( $PNREF, $amount = "" )
	{ 
        if ( $amount != "" )
		{ 
            // Specify lower amount to capture if supplied 
            $this->transaction['AMT'] = $amount;     
        }
		 
        $this->transaction['TRXTYPE'] = "D"; 
        $this->transaction['TENDER']  = "C"; 
        $this->transaction['ORIGID']  = $PNREF; 
    }
     
    /** 
	 * Issue a credit. Either using original PNREF or a credit card 
	 *
     * @return void 
     * @param  PNREF string 
     * @param  amount float 
     * @param  card_no int 
     * @param  exp_month int 
     * @param  exp_year int
	 * @access public 
     */ 
    function credit( $PNREF = "", $amount = "", $card_no = "", $exp_month = "", $exp_year = "" )
	{ 
        if ( !$PNREF && !$card_no )
			return false;     
        
        if ( $amount ) 
            $this->transaction['AMT'] = $amount;     
      
        if ( $PNREF )
		{ 
            $this->transaction['ORIGID'] = $PNREF; 
        }
		else if ( $card_no )
		{ 
            $this->transaction['ACCT']    = ereg_replace( "[^0-9]", "", $card_no ); 
            $this->transaction['EXPDATE'] = $exp_month . substr( $exp_year, -2 ); 
        } 
        $this->transaction['TRXTYPE'] = "C"; 
        $this->transaction['TENDER']  = "C";         
    } 
     
    /** 
	 * A void prevents a transaction from being settled.
	 * A void does not release the authorization (hold on funds) on the cardholder account 
	 *
     * @return void 
     * @param  PNREF string 
	 * @access public
     */ 
    function voidSale( $PNREF )
	{ 
        $this->transaction['TRXTYPE'] = "V"; 
        $this->transaction['TENDER']  = "C"; 
        $this->transaction['ORIGID']  = $PNREF; 
    } 
     
    /** 
	 * Optional, used for AVS check (Address Verification Service) 
	 *
     * @return void 
     * @param  avs_address string 
     * @param  avs_zip int
	 * @access public
     */ 
    function avs( $avs_address = "", $avs_zip = "" )
	{ 
        $this->transaction["STREET[" . strlen( $avs_address ) . "]"] = $avs_address; 
        $this->transaction['ZIP'] = ereg_replace( "[^0-9]", "", $avs_zip ); 
    }
     
	/**
	 * @access public
	 */
    function comments( $comment1 = "", $comment2 = "" )
	{ 
        $this->transaction["COMMENT1[" . strlen( $comment1 ) . "]"] = $comment1; 
        $this->transaction["COMMENT2[" . strlen( $comment2 ) . "]"] = $comment2; 
    }

    /** 
	 * Process the transaction. Result contains the response from Verisign. 
	 *
     * @return array 
	 * @access public
     */ 
    function process()
	{ 
        pfpro_init(); 
        
		$this->result = pfpro_process(
			$this->transaction,
			$this->host,
			$this->port,
			$this->timeout,
			$this->proxyaddress,
			$this->proxyport,
			$this->proxyuser,
			$this->proxypassword
		);
		 
        pfpro_cleanup(); 
    } 
} // END OF PFPro

?>
