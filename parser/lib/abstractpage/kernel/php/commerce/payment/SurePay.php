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
 * Surepay Class (requires curl extension)
 *
 * @package commerce_payment
 */	

class SurePay extends PEAR
{
	/** 
	 * Constructor
	 *
	 * @access public
	 */
	function SurePay(
		$live = false,		// R: boolean (False = surepay test, true = real surepay transaction)
		$merchant = '',	 	// R: string (numeric?)
		$passwd = '',		// R: string
		$params = array() ) // O: Associative array
	{
	 	// Set server to use (live or test).
		if ( $live )
		  	$this->serverurl = 'https://xml.surepay.com';
		else
		  	$this->serverurl = 'https://xml.test.surepay.com';

	 	// Default doctype/dtd specs as of the release date of this lib.
		$this->request_dtd  = 'pp.request PUBLIC "-//IMALL//DTD PUREPAYMENTS 1.0//EN" "http://www.purepayments.com/dtd/purepayments.dtd"';
		$this->response_dtd = 'pp.response PUBLIC "-//IMALL//DTD PUREPAYMENTS 1.0//EN" "http://www.purepayments.com/dtd/purepayments.dtd"';

	 	// Lib version and info.
		$this->version = '0.1.1';
		$this->lib     = 'SurePay Class v' . $this->version;

	 	// Do some error control.
		if ( !$merchant || !$passwd )
		{
			$this = new PEAR_Error( "Merchant id and/or password not specified." );
			return;
		}
		else if ( !is_array( $params ) )
		{
			$this = new PEAR_Error( "Extra pp.request parameters was provided but not as an array." );
			return;
		}

	 	// Put stuff together (we'll try even if there has been an error).
		$params = array_merge( 
			array(
				'merchant' => $merchant,
				'password' => $passwd 
			),
		  	$params 
		);

	 	// Some XML parsing settings.
		$this->parse_responsetypes = array(    
			// Known response types
			"PP.AUTHRESPONSE"
		);
		    
		// Excluded for now: "PP.ADJUSTRESPONSE","PP.REPLACERESPONSE",
		//   				 "PP.CREDITRESPONSE","PP.SETTLENOWRESPONSE"
		$this->parse_xmlroot = 'PP.RESPONSE'; // Root element
   
	 	// Set up stuff
		$this->request = array( 
			'dtd'	  => $this->request_dtd,
			'cnt'	  => 0,
		  	'params'  => $params,
		  	'request' => array()
		);
		 
		$this->xml_root = 'pp.request';

	 	// clear stuff
		$this->response     = array();
		$this->xml_request  = '';
		$this->xml_response = '';
		$this->responseattr = array();
		$this->responsedata = array();
	}

	
	/**
	 * Create an AUTH object.
	 *
	 * @access public
	 */
	function addAuth(
		$params       = array(),	// R: Associative array. Must include surepay pp.auth params
		$ship_address = array(), 	// O: Associative array.
		$ordertext    = array() )   // O: Associative array. (One element)
	{
		if ( is_array( $params ) && $params ) 
		{
		  	$this->request['request'][$idx = ++$this->request['cnt']]['type'] = 'pp.auth';
		  	$this->request['request'][$idx]['params'] = $params;
		  	$this->request['request'][$idx]['cnt'] = 0;
		  
		  	$pstr = '[' . $idx . ']';
		  
		  	if ( $ship_address ) 
			{
			 	if ( is_array( $ship_address ) )
					$this->addShippingAddress( $pstr, $ship_address );
			 	else
					return PEAR::raiseError( "Address supplied to addAuth was not an array." );
		  	}
		  
		  	if ( $ordertext ) 
			{
			 	if ( is_array( $ordertext ) ) 
				{
					reset( $ordertext );
					
					$res = $this->addOrdertext( $pstr, key( $ordertext ), $ordertext[key( $ordertext )] );
					
					if ( PEAR::isError( $res ) )
						return $res;
			 	} 
				else 
				{
					return PEAR::raiseError( "Ordertext supplied to addAuth was not an array." );
			 	}
		  	}
		  
			return $pstr;
		} 
		else 
		{
		  	return PEAR::raiseError( "pp.auth parameters error. Not given or none-array." );
		}
	}

	/**
	 * Adds an adress of type shipping.
	 *
	 * @access public
	 */
	function addShippingAddress(
		$pstr,		// "Object" identifier
		$params )	// Array: address parameters excluding type	
	{
		 $params = array_merge( array( 'type' => 'shipping' ), $params );
		 return $this->addAddress( $pstr, $params );
	}

	/**
	 * Adds an adress of type billing.
	 *
	 * @access public
	 */
	function addBillingAddress(
		$pstr,		// "Object" identifier
		$params )	// Array: address parameters excluding type
	{
		$params = array_merge( array( 'type' => 'billing' ), $params );
		return $this->addAddress( $pstr, $params );
	}

	/**
	 * Adds an ordertext object.
	 *
	 * @access public
	 */
	function addOrdertext(
		$pstr,	// "Object" identifier
		$type,	// String
		$data )	// String		
	{
		if ( strlen( $data ) > 25 ) 
		{
		  	return PEAR::raiseError( "Order text length exceeded 25 characters." );
		}
		elseif( ( strtolower( $type ) == 'description' ) || ( strtolower( $type ) == 'instructions' ) || ( strtolower( $type ) == 'classification' ) ) 
		{
			eval( $this->peval( $pstr ) );
			$idx = ++$ptr['cnt'];
			$ptr[$idx]['type']   = 'pp.ordertext';
			$ptr[$idx]['params'] = array( 'type' => strtolower( $type ) );
			$ptr[$idx]['data']   = trim( $data );
			$ptr[$idx]['cnt']    = 0;
			
			return $pstr.'['.$idx.']';
		} 
		else 
		{
		  	return PEAR::raiseError( "Order text type was not (description|instructions|classification)." );
		}
	}

	/**
	 * Add a credit card object.
	 *
	 * @access public
	 */
	function addCreditcard(
	  	$pstr,					// "Object" identifier
	  	$number,				// string cc number
	  	$expiration,		 	// string expiration format mm/yy
	  	$cvv2,					// string (must be 0 or '' if not used, then set stat to 0 or 9)
	  	$cvv2status = 1,		// int set(0,1,9)
	  	$baddress   = array() )	// Array
	{
		if ( !$number || !$expiration )
		{
			return PEAR::raiseError( "Number or Expiration not present in addCreditcard." );
		}
		else if ( !preg_match( '/^[0-9]{1,2}\/[0-9]{1,2}$/', $expiration ) )
		{
			return PEAR::raiseError( "Expiration is formatted wrong (mm/yy)." );
		}
		else if ( !$cvv2 && $cvv2status == 1 )
		{
			return PEAR::raiseError( "cvv2 enabled but no cvv2 given." );
		}
		else if ( $cvv2status == 1 && strlen( $cvv2 ) > 4 )
		{
			return PEAR::raiseError( "cvv2 code to long (max 4)." );
		}
		else if ( $cvv2status == 1 && strlen( $cvv2 ) < 3 ) 
		{
			return PEAR::raiseError( "cvv2 code to short (min 3)." );
		}
		else 
		{
			if ( !$cvv2 ) 
				$cvv2 = '0';
			
			eval( $this->peval( $pstr ) );

			$idx = ++$ptr['cnt'];
			$ptr[$idx]['type']   = 'pp.creditcard';
			$ptr[$idx]['params'] = array(
				'number'     => $number,
				'expiration' => $expiration,
				'cvv2'       => $cvv2,
				'cvv2status' => $cvv2status
			);
			$ptr[$idx]['data'] = '';
			$ptr[$idx]['cnt']  = 0;
			$pstr = $pstr . '[' . $idx . ']';
			
			if ( $baddress ) 
			{
				if ( is_array( $baddress ) )
				  	$this->addBillingAddress( $pstr, $baddress );
				else
				  	return PEAR::raiseError( "Billing address supplied in add_cc but was not an array." );
			}
			
			return $pstr;
		}
	}

	/**
	 * Add an item object to an auth object.
	 *
	 * @access public
	 */
 	function addLineItem(
			$pstr,		 			// Object identifier
			$quantity,	  			// int
			$sku,		  			// product sku
			$description,    		// string (200)
			$unitprice,	 			// string (nnn.nnUSD)
			$taxrate = '0',    		// real (0.08 = 8%)
			$options = array() )	// Optional options array
	{  
		if ( !$quantity || !$sku || !$description || !$unitprice ) 
		{
		 	return PEAR::raiseError( "Missing required parameters for addLineItem." );
		}
		else 
		{
			eval( $this->peval( $pstr ) );
			$idx = ++$ptr['cnt'];
			$ptr[$idx]['type']   = 'pp.lineitem';
			$ptr[$idx]['params'] = array(
				'quantity'    => $quantity,
				'sku'         => $sku,
				'description' => substr( $description, 0, 200 ),
				'unitprice'   => $unitprice,
				'taxrate'     => $taxrate
			);
			$ptr[$idx]['data'] = '';
			$ptr[$idx]['cnt']  = 0;
			$pstr = $pstr . '[' . $idx . ']';
			
			if ( $options ) 
			{
				if ( is_array( $options ) ) 
				{
				  	reset( $options );
				  	$cnt = 3; // Max 3 options per line item
				  
				  	while ( ( list( $key, $val ) = each( $options ) ) && $cnt-- )
					{
						$res = $this->addOption( $pstr, $key, $val );
						
						if ( PEAR::isError( $res ) )
							return $res;
					}
				} 
				else 
				{
				  	return PEAR::raiseError( "Options defined but was not an array for addLineItem." );
				}
			}
			
			return $pstr;
		}
    }   

	/**
	 * Adds an option (for line item).
	 *
	 * @access public
	 */
	function addOption(
		$pstr,	  	// Object identifier
		$label,	 	// String
		$value )	// String
	{
		if ( !$label )
		{
			return PEAR::raiseError( "Option label not specified." );
		}
		else 
		{
			eval( $this->peval( $pstr ) );
			$idx = ++$ptr['cnt'];
			$ptr[$idx]['type']   = 'pp.option';
			$ptr[$idx]['params'] = array(
				'label' => $label,
				'value' => $value
			);
			$ptr[$idx]['data'] = '';
			$ptr[$idx]['cnt']  = 0;
			
			return $pstr . '[' . $idx . ']';
		}
	}

	/**
	 * Adds a telecheck.
	 *
	 * object_identifier: addTelecheck (
   	 *		object_identifier: parent object,
	 *		string: Check number,
	 *		string: MICR number (Continous string of EVERY number on the bottom of the check),
	 *		boolean: isbusiness (true = business, false=persona)
  	 *		[,array: identification object attributes (Dr.license)]
	 *		[,array: billing address]
   	 * )
	 *
	 * @access public
	 */
	function addTelecheck(
		 $pstr,					// Object identifier
		 $number,		 		// String (int)
		 $micr,					// String
		 $isbusiness,	  		// Boolean
		 $id = array(),	  		// Array
		 $baddress = array() )	// Array
	{
		if ( strlen( $number ) > 6 ) 
		{ 
			return PEAR::raiseError( "Check number to long (max 6 digits)." ); 
		}
		else if ( !$number ) 
		{ 
			return PEAR::raiseError( "No Check number provided." ); 
		}
		else if ( strlen( $micr ) > 35 ) 
		{ 
			return PEAR::raiseError( ""Check MICR is to long (max 35)." ); 
		}
		else 
		{
			if ( $isbusiness ) 
				$isbusiness = 'true'; 
			else 
				$isbusiness = 'false';
			
			eval( $this->peval( $pstr ) );
			$idx  = ++$ptr['cnt'];
			$pstr = $pstr . '[' . $idx . ']';
			$ptr[$idx]['type']   = 'pp.telecheck';
			$ptr[$idx]['params'] = array(
				'number'     => $number,
				'micr'       => $micr,
				'isbusiness' => $isbusiness
			);
			$ptr[$idx]['data'] = '';
			$ptr[$idx]['cnt']  = 0;
			
			if ( $id ) 
			{
			  	if ( !is_array( $id ) )
				{
				 	return PEAR::raiseError( "Identification data supplied to addTelecheck but was not an array." );
				}
			  	else
				{
				 	$res = $this->addIdentification( $pstr, $id );
					
					if ( PEAR::isError( $res ) )
						return $res;
				}
			}
			
			if ( $baddress ) 
			{
			  	if ( !is_array( $baddress ) ) 
					return PEAR::raiseError( "Billing address data supplied to addTelecheck but was not an array." );
			  	else
				 	$this->addBillingAddress( $pstr, $baddress );
			}
			
			return $pstr;
		}
	}

	/**
	 * Add a id object.
	 *
	 * @access public
	 */
 	function addIdDriversLicense(
	  	$pstr,	  	// Object_identifier
	  	$number,	// String
	  	$issuedby )	// String
 	{
	  	if ( !$number || !$issuedby ) 
		{
		 	return PEAR::raiseError( "Required ID number and Issue (State-abr) is missing." );
	  	}
	  	else 
		{
		 	return $this->addIdentification(
				$pstr,
				array(
			  		'type'     => 'driverslicense',
			  		'number'   => $number,
			  		'issuedby' => $issuedby 
				) 
			);
	  	}
   	}

	/**
	 * Add an address to the object.
	 *
	 * @access public
	 */
	function addAddress(
		$pstr,		// "Object" identifier
		$params )	// Array: address parameters excluding type
	{
		eval( $this->peval( $pstr ) );
		$idx = ++$ptr['cnt'];
		$ptr[$idx]['type']   = 'pp.address';
		$ptr[$idx]['params'] = $params;
		$ptr[$idx]['data']   = '';
		$ptr[$idx]['cnt']    = 0;
		
		return $pstr . '[' . $idx . ']';
	}
	
	/**
	 * Only reason this was made was for compliance with possible future options
  	 * of surepay/telecheck accepting any other than drivers license.
	 *
	 * @access public
	 */
	function addIdentification(
		$pstr,		// Object identifier
		$params )  	// Array: id parameters
	{
		// Change/add to these if future types are added.
		$validtypes = array( 'driverslicense' );
		$required['driverslicense'] = array( 'number', 'issuedby' );

		if ( !$params['type'] ) 
		{ 
			return PEAR::raiseError( "Identification type not specified." ); 
		}
		else if ( !in_array( ( $params['type'] = strtolower( $params['type'] ) ) ,$validtypes ) ) 
		{
			return PEAR::raiseError( "Identification type not valid." ); 
		}
		else 
		{
			while ( $row = each( $required[$params['type']] ) )
			{
			  	if ( !$params[$row[1]] ) 
				 	return PEAR::raiseError( "Required argument " . $row[1] . " was not given to addIdentification." );
			}
			
			eval( $this->peval( $pstr ) );
			$idx = ++$ptr['cnt'];
			$ptr[$idx]['type']   = 'pp.identification';
			$ptr[$idx]['params'] = $params;
			$ptr[$idx]['data']   = '';
			$ptr[$idx]['cnt']    = 0;
		
			return $pstr . '[' . $idx . ']';
		}
	}
	
	/**
	 * Build the xml document from held data.
	 *
   	 * This function does not nest dynamically any level, 
	 * I was too tired when creating it so it is limited to 3 levels now..
  	 * In the future we should use a level digger and auto generation fo any tag,
  	 * and perhaps use DOM for the objects to begin with.
	 *
	 * @access public
	 */
	function prepareRequest() 
	{
		if ( $this->request['cnt'] ) 
		{
		 	$requests = '';
		 	
			reset( $this->request['request'] );
		 	while ( list( $idx, $req ) = each( $this->request['request'] ) ) 
			{
				$requests2 = '';
			
				for ( $idx2=$this->request["request"][$idx]['cnt']; $idx2; $idx2-- ) 
				{
				 	$requests3 = '';
				 
				 	for ( $idx3 = $this->request["request"][$idx][$idx2]['cnt']; $idx3; $idx3-- ) 
					{				 
				  		$requests3 .= "\n" . $this->xmltag(
							$this->request["request"][$idx][$idx2][$idx3]['type'],
							$this->request["request"][$idx][$idx2][$idx3]['params'],
							trim( $this->request["request"][$idx][$idx2][$idx3]['data'] )
						);
				 	}
				 
				 	if ( $requests3 ) 
						$requests3 .= "\n";
				 
				 	$requests2 .= "\n" . $this->xmltag(
				  		$this->request["request"][$idx][$idx2]['type'],
				  		$this->request["request"][$idx][$idx2]['params'],
				  		trim( $this->request["request"][$idx][$idx2]['data'] ) . "$requests3" );
				}
			
				if ( $requests2 ) 
					$requests2 .= "\n";
			
				$requests .= "\n" . $this->xmltag(
				 	$this->request["request"][$idx]['type'],
				 	$this->request["request"][$idx]['params'],
				 	trim( $this->request["request"][$idx]['data'] ) . "$requests2"
				);
		 	}
		 
		 	$this->xml_request = $this->xmldtd( $this->request_dtd ) . "\n" . 
				$this->xmltag(
					$this->xml_root,
					$this->request['params'],
					$this->request['data']. $requests . "\n"
			 	) . "\n";
		} 
		else 
		{
			return PEAR::raiseError( "No request objects?" );
		}
	
		return $this->xml_request;
	}

	/**
	 * Post the request XML to surepays server.
	 *
	 * @access public
	 */
	function submitRequest(
	  	$timeout = 90,	// Integer
	  	$sslver  = 3 )	// Integer 0/2/3
	{
		if ( !$this->xml_request ) 
		{ 
			return PEAR::raiseError( "No XML data to post." ); 
		}
		else 
		{
		  	$curlsession = curl_init();
		  	curl_setopt( $curlsession, CURLOPT_URL, $this->serverurl );
		  	curl_setopt( $curlsession, CURLOPT_POST, 1 );
		  	curl_setopt( $curlsession, CURLOPT_POSTFIELDS, 'xml=' . $this->xml_request );
		  	curl_setopt( $curlsession, CURLOPT_TIMEOUT, $timeout );
		  	curl_setopt( $curlsession, CURLOPT_HEADER, 0 );
			curl_setopt( $curlsession, CURLOPT_SSLVERSION, $sslver );
		  	curl_setopt( $curlsession, CURLOPT_RETURNTRANSFER, 1 );
		  
		  	$this->xml_response = curl_exec( $curlsession );    
		  	$curlinfo = curl_getinfo( $curlsession );
		  
		  	if ( !$this->xml_response ) 
			{
				$curl_error = curl_error( $curlsession );
				$curl_errno = curl_errno( $curlsession );
				$error      = 'Curl error: ' . $curl_errno . ' ' . $curl_error . "\nConnection details:\n";
			
				if ( is_array( $curlinfo ) ) 
				{
					while ( $row = each( $curlinfo ) )
						$error .= '  ' . $row[0] . ' => ' . $row[1] . "\n";
				} 
				else 
				{
				 	$error .= '  ' . $curlinfo;
				}
				
				curl_close( $curlsession );
				return PEAR::raiseError( $error );
			} 
		  
		  	curl_close( $curlsession );
			return $this->xml_response;
		}
	}
	
	/**
	 * @access public
	 */
    function parseResponse() 
	{
		if ( !$this->xml_response )
			return PEAR::raiseError( "No response XML to parse." );
		else
		  	return $this->parse( $this->xml_response );
	}

  
  	// methods for returning status of response

  	/**
	 * Return amount of AUTH response records found.
	 *
	 * @access public
	 */
    function countAuthResponse() 
	{
		return count( $this->responseattr['PP.AUTHRESPONSE'] ) ;
    }

  	/**
	 * AUTHRESPONSE: transaction id by orderno.
	 *
	 * @access public
	 */
  	function authTransId( $on ) 
	{
		return $this->responseattr['PP.AUTHRESPONSE'][$on]['TRANSACTIONID'];
    }

  	/**
	 * AUTHRESPONSE: cvv2result by orderno.
	 *
	 * @access public
	 */
  	function authCVV2Result( $on ) 
	{
		return $this->responseattr['PP.AUTHRESPONSE'][$on]['CVV2RESULT'];
    }

  	/**
	 * AUTHRESPONSE: response avs by orderno.
	 *
	 * @access public
	 */
    function authAVS( $on ) 
	{
		return $this->responseattr['PP.AUTHRESPONSE'][$on]['AVS'];
   	}

  	/**
	 * AUTHRESPONSE: return auth status by orderno.
	 *
	 * @access public
	 */
    function authStatus( $on ) 
	{
		return $this->responseattr['PP.AUTHRESPONSE'][$on]['AUTHSTATUS'];
    }
    	
  	/**
	 * AUTHRESPONSE: return authcode if status is AUTH and there is no failure.
	 *
	 * @access public
	 */
  	function authAuthCode( $on ) 
	{
		if ( $this->authStatus( $on ) == 'AUTH' && !$this->authFailure( $on ) )
		 	return $this->responseattr['PP.AUTHRESPONSE'][$on]['AUTHCODE'];
  	}

  	/**
	 * AUTHRESPONSE: return failure attribute.
	 *
	 * @access public
	 */
    function authFailure( $on ) 
	{
		return $this->responseattr['PP.AUTHRESPONSE'][$on]['FAILURE'];
    }

  	/**
	 * AUTHRESPONSE: return response text (Should only hold data if DCL, REF or ERR status).
	 *
	 * @access public
	 */
    function authText( $on ) 
	{
		return $this->responsedata['PP.AUTHRESPONSE'][$on];
  	}

  	/**
	 * AUTHRESPONSE: return an array of all AUTH response order numbers.
	 *
	 * @access public
	 */
    function auths() 
	{
		return array_keys( $this->responseattr['PP.AUTHRESPONSE'] );
   	}
	
	
	// xml handling
    
	/**
	 * Creation of xml tag.
	 *
   	 * Planned for future version: Check tagnames and field values for use of
  	 * legal characters and entities, perhaps automatic XML entity conversion?
	 *
	 * @access public
	 */
	function xmltag(
	  	$tagname,		 	// R: String
	  	$params = array(), 	// O: Assosiative array
	  	$data   = '' )		// O: String
	{
		$result = "<$tagname";
		 
		if ( $params ) 
		{
		 	while ( list( $key, $val ) = each( $params ) )
		 		$result .= ' ' . $key . '="' . addslashes( $val ) . '"';
		} 
		
		if ( trim( $data ) ) 
		{ 
			// unless it contains XML tags and data...
		  	if ( !preg_match( '/<.+?(\/>|>.*?<\/.+?>)/', $data ) ) 
			{ 
			 	// <? # Added this line to fool my txt editor here, it got confused about that regex :)
			 	// Here I try to remove any special chars which surepay may fail on, replaced with _
			 	// Had some oddities with [] and ^ so I separated them.
			 	$data = preg_replace( '/([^A-z0-9$._\-()]|\^|\[|\])/', '_', $data );
		  	} 
		  
		  	$result .= '>' . $data . '</' . $tagname . '>'; 
		}
		else 
		{ 
			$result .= " />"; 
		}
		
		return $result;
	}

	/**
	 * Return a doctype tag.
	 *
	 * @access public
	 */
	function xmldtd( $str )
	{
    	if ( $str ) 
			return '<!DOCTYPE ' . $str . '>';
		else
			return "";
	}

	/**
	 * Create eval ready string for object pointers.
	 *
     * This is sort of a trick, only way I found at the moment to
   	 * make a reliable/re-usable ref to array inside object.
	 *
	 * @access public
	 */
	function peval( $pstr ) 
	{
		return '$ptr = & $this->request["request"]' . $pstr . ';';
	}

	/**
	 * Parses xml data and create a multidim array of the data.
	 *
	 * @access public
	 */
	function parse( $xmldata ) 
	{ 
    	// The whole XML parser here got started off the wrong way. It works, but it is not very
    	// maintainable for possible future releases of the surepay XML DTD, if more levels
    	// are added or sub tags and so on. The xml parsing could need some rethinking.
    	$this->parse_lvl      = "0";
    	$this->parse_mother   = "/";
    	$this->responseattr   = array();
    	$this->responsedata   = array();
    	$this->parseerr       = array();
    	$this->last_char_data = '';
    
		if ( !preg_match ( '/^<!DOCTYPE .*>/', $xmldata, $dtd ) )
			return PEAR::raiseError( "No DOCTYPE declaration/dtd found in XML data." );
    
    	$this->parse_dtd = $dtd[0];
    
		if ( !( $dtd[0] == $this->xmldtd( $this->response_dtd ) ) )
			return PEAR::raiseError( "DOCTYPE declaration/dtd not supported (new version?)." );

		$this->parse_parser = xml_parser_create();    	// Create the object
		xml_set_object( $this->parse_parser, &$this );	// Reference to the object from this object
		xml_set_element_handler( $this->parse_parser, "startElement", "endElement" );
		xml_set_character_data_handler( $this->parse_parser, "cdata" );
		
		if ( !xml_parse( $this->parse_parser, $xmldata ) ) 
		{
	  		$error = sprintf(
				"XML response data error: %s at line %d",
			 	xml_error_string( xml_get_error_code( $this->parse_parser ) ),
			 	xml_get_current_line_number( $this->parse_parser ) 
			);
			
			xml_parser_free( $this->parse_parser );
			return PEAR::raiseError( $error );
		}
		
		xml_parser_free( $this->parse_parser ); 
		return $this->countAuthResponse();
 	}
  
  	/**
	 * @access public
	 */
   	function startElement( $parser, $name, $attrib ) 
	{
		if ( ( $this->parse_mother == '/' ) && !( $name == $this->parse_xmlroot ) )
		{
			trigger_error( "Unknown root element in XML response data.", E_USER_ERROR );
		}
		else if ( $this->parse_lvl >= 1 ) 
		{
	  		if ( in_array( $name, $this->parse_responsetypes ) ) 
			{
				$this->parse_current_order = $attrib['ORDERNUMBER'];
		
				if ( $this->parse_current_order ) 
				{ 
			 		$this->responseattr[$name][$this->parse_current_order] = $attrib;
			 
			 		if ( $attrib['AUTHSTATUS'] == 'AUTH' ) 
						$this->parse_auth[$attrib['ORDERNUMBER']] = true; 
			 		else 
						$this->parse_auth[$attrib['ORDERNUMBER']] = false; 
				}
				else 
				{ 
					$this->parseerr = array(
						true,
						"No order number response. Gateway said: {sp.text}."
					);
				}
	  		}
	  		else 
			{ 
				$this->parseerr = array(
					true,
					"Unknown response type $name."
				);
			}
		}
	
		$this->parse_mother = $name;
		$this->parse_lvl++;
 	}

	/**
	 * @access public
	 */
   	function endElement( $parser, $name ) 
	{
	  	if ( $this->parse_lvl <= 1 )
		{
			$this->parse_mother = '/'; 
		}
	  	else if ( $this->parse_lvl == 2 )
		{
			$this->parse_mother =  $this->parse_xmlroot; 
		}
	  	else
		{
			$this->parseerr = array(
				true,
				"Too many levels in XML data (" . $this->parse_lvl . ")."
			);
		}
	  
	  	$this->parse_lvl--;
 	}

	/**
	 * @access public
	 */
 	function cdata( $parser, $cdata ) 
	{
	  	if ( ( sizeof( $this->parseerr ) == 0 ) && trim( $cdata ) && ( in_array( $this->parse_mother, $this->parse_responsetypes ) ) ) 
		{
			$this->responsedata[$this->parse_mother][$this->parse_current_order] = trim( $cdata );
	  	}
	  	else 
		{
		 	$this->last_char_data .= trim( $cdata );
		 	$this->parseerr = 0;
	  	}
   	}
} // END OF SurePay
   
?>
