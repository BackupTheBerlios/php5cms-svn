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
 * PayPal IPN Class is a simple PHP class for interfacing with PayPal's Instant
 * Payment Notification system. It allows you to send the POST response back to PayPal
 * to verify the information.
 *
 * @package commerce_payment
 */

class Paypal extends PEAR
{
	/**
	 * @access public
	 */
	var $paypal_post_vars;
	
	/**
	 * @access public
	 */
	var $paypal_response;
	
	/**
	 * @access public
	 */
	var $timeout;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Paypal( $paypal_post_vars )
	{
		$this->paypal_post_vars = $paypal_post_vars;
		$this->timeout = 120;
	}


	/**
	 * Sends response back to paypal.
	 *
	 * @access public
	 */
	function sendResponse()
	{
		$fp = @fsockopen( "www.paypal.com", 80, &$errno, &$errstr, 120 ); 

		if ( !$fp )
		{
			return PEAR::raiseError( $errstr );
		}
		else 
		{
			// put all POST variables received from Paypal back into a URL encoded string
			foreach ( $this->paypal_post_vars as $key => $value )
			{
				// if magic quotes gpc is on, PHP added slashes to the values so we need
				// to strip them before we send the data back to Paypal.
				if ( @get_magic_quotes_gpc() )
					$value = stripslashes( $value );

				// make an array of URL encoded values
				$values[] = "$key" . "=" . urlencode( $value );
			}

			// join the values together into one url encoded string
			$response = @implode( "&", $values );

			// add paypal cmd variable
			$response .= "&cmd=_notify-validate";

			fputs( $fp, "POST /cgi-bin/webscr HTTP/1.0\r\n" ); 
			fputs( $fp, "Host: https://www.paypal.com\r\n" ); 
			fputs( $fp, "User-Agent: " . $GLOBALS['HTTP_USER_AGENT'] ."\r\n" ); 
			fputs( $fp, "Accept: */*\r\n" ); 
			fputs( $fp, "Accept: image/gif\r\n" ); 
			fputs( $fp, "Accept: image/x-xbitmap\r\n" ); 
			fputs( $fp, "Accept: image/jpeg\r\n" ); 
			fputs( $fp, "Content-type: application/x-www-form-urlencoded\r\n" ); 
			fputs( $fp, "Content-length: " . strlen( $response ) . "\r\n\n" ); 

			// send url encoded string of data
			fputs( $fp, "$response\n\r" ); 
			fputs( $fp, "\r\n" );

			$this->send_time = time();
			$this->paypal_response = ""; 

			// get response from paypal
			while ( !feof( $fp ) ) 
			{ 
				$this->paypal_response .= fgets( $fp, 1024 ); 

				// waited too long?
				if ( $this->send_time < time() - $this->timeout )
					return PEAR::raiseError( "Timeout ($this->timeout seconds)." );
			}

			fclose( $fp );
		}
	}

	/**
	 * Returns true if paypal says the order is good, false if not.
	 *
	 * @access public
	 */
	function isVerified()
	{
		if ( ereg( "VERIFIED", $this->paypal_response ) )
			return true;
		else
			return false;
	}

	/**
	 * Returns the paypal payment status.
	 *
	 * @access public
	 */
	function getPaymentStatus()
	{
		return $this->paypal_post_vars['payment_status'];
	}
} // END OF Paypal

?>
