<html>
<body>

<?php

ini_set('max_execution_time','120');


require( '../../../../../prepend.php' );

using( 'commerce.payment.SurePay' );


/**
 * This version of test.php has more comments and usable samples than before,
 * two AUTH objects are created, one creditcard and one telecheck.
 * Many functions now support multiple/optional arguments, such as with
 * addCreditcard you can now put the billing address right with it, or you can
 * add the billing address with addBillingAddress afterwards/separate.
 * The first part of this document does the creditcard request, and uses
 * all the functions split up.
 * The second part is a Telecheck request and uses less functions with more
 * parameters, just to show the differences.
 * The third part takes care of submitting, receiving and decoding the
 * response.
 * See the syntax specs with functions below, and the readme.html for more
 * information.
 *
 * NOTE:
 * Although some variables are noted as being "Object Identifiers", it may
 * not be a object type variable. In fact, in this version it is a text
 * string pointing to a a location in an array. BUT, this may change in
 * future versions, so you should not base anything on this variable other
 * than passing it on to other functions. (No typecasting)
 *
 * Another NOTE:
 * Surepay have changed their test numbers and such severald times, at the
 * moment of release of this version it is
 *
 *  Creditcard test AUTH:
 *     cc number: 4012000033330026   any future exp date
 *     $11 = AUTH     $21 = "REF"   $31 = "DCL"   any other may give "ERR"
 *     This test request uses total $1 shipping, and 1x$5 + 1x$5 goods,
 *     total $11. Just change the item quantities to try the other status.
 *
 *  TeleCheck AUTH:
 *     Check number 1001
 *     MICR number  1234567804390850001001
 *     Any value should work, but at this very moment there are some problems
 *     with Telechecks test server, it works thru Surepay and the Telecheck
 *     server returns: General failure: com.purepayments.biz.telecheck.TeleCheckFailureException: 
 *     TeleCheckProcessingError: TeleCheck returned Check Failure (807)
 *     If you get that your XML and request may be ok.
 *
 *  I have also found some bugs and weakneses in Surepays processing, here is a
 *  couple of them:
 *
 *   - Do not add % as character data, best off staying away from it totally.
 *     This can cause surepays servlets to crash and gives a 500 server error.
 *
 *   - Do not convert < > & ' " to the correct XML entities, surepays parser or
 *     data collector rejects any characterdata like &lt; and so on.
 *
 *   - Phone numbers in address are limited length (stay 11 digits or less), they
 *     claim to be "US domestic oriented" (?)
 *
 *   - The surepayd SDK documentation is outdated and very inaccurate. Several things
 *     does not work as specified nor does the DTD specify all required fields, such
 *     as cvv2 must be specified even if not being used. Surepays own sample does not
 *     work very well either. It has been like this for more than 6 month now, as
 *     of the date of release of this script. When will we see SDK 1.5? And a new
 *     improved DTD or Schema?
 *
 *   - What is the correct way to handle ref? Should the script handle and check
 *     avs and cvv2status responses to something automatically? If someone has
 *     a guideline to this would be appreciated..
 */


// Syntax:
// vobject_identifier: sausp (
//		boolean: live?
//    	,string: merchant id
//    	,string: password
//   	[,array: extra pp.request parameters ]
//  )

$ssp = new SurePay( false, '1001' , 'password' );

if ( PEAR::isError( $ssp ) )
	die( $ssp->getMessage() );

echo '<h1>SurePay ' . $ssp->version . " test:</h1><PRE>\n";

   
/**
 * CREDIT CARD REQUEST
 *
 * addAuth
 * addShippingAddress
 * addOrdertext
 * addCreditcard
 * addBillingAddress
 * addLineItem
 * addOption
 * addOption
 * addLineItem (incl 2 option's)
 */

// addAuth
// Create an AUTH object in the request
// Optionally you can add shipping address and/or order text here instead
// of using their own functions.

// Syntax:
// object_identifier: addAuth (
//		array: auth parameters
//    	,[array: shipping address]
//    	,[array: ordertext (type and text)]
// )

$auth = $ssp->addAuth( 
	array(
		'ordernumber'   => '18140517',
		'ecommerce'     => 'true',
		'ecommercecode' => '07',
		'ponumber'      => 'Verbal Hagar',
		'ipaddress'     => $REMOTE_ADDR,
		'shippingcost'  => '0.93USD',
		'taxamount'     => '0.07USD',
		'referringurl'  => $HTTP_REFERER,
		'browsertype'   => $HTTP_USER_AGENT,
   	)
);

if ( PEAR::isError( $auth ) )
	die( $auth->getMessage() );


// addShippingAddress
// Create an ADDRESS object with type shipping. You want to attach this to
// the auth request, so use the return from that as a parent identifier here.

// Syntaxt:
// object_identifier: addShippingAddress (
//	 	object identifier : parent object
//		,array: address details
// )

$ssp->addShippingAddress(
   	$auth,
   	array(
		'fullname' => 'Hagar Tarball Horrible',
		'address1' => '123 Pilage street',
		'address2' => 'Suite 100',
		'city'     => 'Valhalla',
		'state'    => 'NA',
		'zip'      => '12345',
		'country'  => 'NO',
		'phone'    => '5555551234',
		'email'    => 'devnull@vikings.dot'
   	)
);


// addOrdertext
// Inserts (optional) ordertext.
// Remember: ordertext is very limited, check the Surepay SDK documentation,
// people keep asking me about this and getting errors, my advice: Skip it!
// (This could easily have been done in the addAuth function instead)

// Syntax:
// object_identifier: addOrdertext (
// 		object_identifier: parent object
//		,string: type set (description|instructions|classification)
//		,string: text (very limited length!)
// )

$res = $ssp->addOrdertext(
	$auth,
	'instructions',
	'Mark as <urgent>'
);

if ( PEAR::isError( $res ) )
	die( $res->getMessage() );

	
// addCreditcard
// Adds a creditcard object. The cvv2 security feature should always be uses,
// therefor the cvv2status is 1 by default an no need to specify it.
// to turn it of, set code to 0 and status to 0. Status 9 can be
// uses optionally if the clients card has no code.

// Syntax:
// object_identifier: addCreditcard (
//		object_identifier: parent object
//		,string: card number
//		,string: expiration (mm/yy)
//		,int: cvv2 code from card
//    	[,int: cvv2status (security mode) (0|1|9) default is 1 (In Use)]
//    	[,array: billing address]
//  )
   
// This sample does not use the cvv2 code since it is a test request
// according to the surepay SDK specifications

$creditcard = $ssp->addCreditcard(
   	$auth,
   	'4012000033330026',
   	'12/05',
   	'0',
   	'0' 
);


// addBillingAddress
// Create an ADDRESS object with type billing. You want to attach this to
// the payment object, so use the return from that as a parent identifier here.

// Syntaxt:
// object_identifier: addBillingAddress (
//		object identifier : parent object
//		,array: address details
// )

$ssp->addBillingAddress(
	$creditcard,
	array(
		'fullname' => 'Hagar T Horrible',
		'address1' => '123 Pilage street',
		'address2' => 'Kitchen Delivery Door',
		'city'     => 'Valhalla',
		'state'    => 'NA',
		'zip'      => '12345',
		'country'  => 'NO',
		'phone'    => '5555551234',
		'email'    => 'devnull@vikings.dot'
   	)
);


// addLineItem
// Adds an item to the order. We recommend splitting it up and not
// collect a total sum in one item. If you add only one item with the
// total charge, it is between you, surepay, and your merchant account
// provider to determine if it is the right way or not.
// alternatively you can add option sub objects in the same function.

// Syntax:
// object_identifier: addLineItem (
//		object_identifier: parent object (The AUTH object)
//		,int: quantity,
//		,string: sku,
//		,string: description,
//		,string: unit cost (currency) [d..d]d.ccUSD
//		,real: taxrate in decimal notation (0.08 = 8%)
//    	[,array: options ]
// )

$item = $ssp->addLineItem (
   	$auth,
   	'60',
   	'BEER_001',
   	'Sixpack Samuel Adamas Boston Lager',
   	'5.00USD',
   	'0.08'
);

if ( PEAR::isError( $item ) )
	die( $item->getMessage() );

	
// addOption
// Add options to lineitem

// Syntax:
// object_identifier: addOption (
//		object_identifier: parent object
//		,string: option label
//		,string: option value
// )

$res = $ssp->addOption( $item, 'color', 'brown' );

if ( PEAR::isError( $res ) )
	die( $res->getMessage() );

$res = $ssp->addOption( $item, 'bottle', 'glas' );

if ( PEAR::isError( $res ) )
	die( $res->getMessage() );

	
// Add another lineitem object (inlcuding two options) in the AUTH object

$item = $ssp->addLineItem(
   	$auth,
   	'1',
   	'BEER_052',
   	'Sixpack George Killians Irish Red',
   	'5.00USD',
   	'0.08',
   	array (
 		'type' => 'domestic',
 		'logo' => 'horsehead'
   	)
);

if ( PEAR::isError( $item ) )
	die( $item->getMessage() );


// This ends the Creditcard request, it could have been used as it is
// at this point to do a request to surepay.


/**
 * TELECHECK REQUEST ***
 *
 * addAuth (inluding shipping address and ordertext)
 * addTelecheck (including billing address and id)
 * addLineItem (incl option)
 * addLineItem
 */

// First we'll add another AUTH object to our request
// This time we'll include shipping address and ordertext in the same
// function.

$auth = $ssp->addAuth( 
   	array(
		'ordernumber'   => '18140518',
		'ecommerce'     => 'true',
		'ecommercecode' => '07',
		'ponumber'      => 'E-mail Hagar',
		'ipaddress'     => $REMOTE_ADDR,
		'shippingcost'  => '1.00USD',
		'taxamount'     => '0.00USD',
		'referringurl'  => $HTTP_REFERER,
		'browsertype'   => $HTTP_USER_AGENT,
   	),
   	array(
		'fullname'      => 'Jr. Hagar Horrible',
		'address1'      => '145 Vaernes Gata',
		'address2'      => 'Poelse med Lompe',
		'city'          => 'Valhalla',
		'state'         => 'NA',
		'zip'           => '12345',
		'country'       => 'NO',
		'phone'         => '+4712555554',
		'email'         => 'devnull@vikings.dot'
   	),
   	array(
		'instructions'  => 'Pottittstappe med saus'
   	)
);

if ( PEAR::isError( $auth ) )
	die( $auth->getMessage() );


// addTelecheck
// Adds a telecheck object. See http://www.telecheck.com for info on what it is
// Basically it lets people pay with a paper check, telecheck "Converts" it to
// an electronic payment. You must have a TeleCheck Merchant account to use it!

// Syntax:
// object_identifier: addTelecheck (
//		object_identifier: parent object
//		,string: Check number
//		,string: MICR number (Continous string of EVERY number on the bottom of the check)
//		,boolean: isbusiness (true = business, false=persona)
//    	[,array: identification object attributes (Dr.license)]
//    	[,array: billing address]
// )
   
// This sample does not use the cvv2 code since it is a test request
// according to the surepay SDK specifications

$telecheck = $ssp->addTelecheck(
   	$auth,
   	'1001',
   	'1234567804390850001001',
   	'true',
   	array (
		'type'     => 'driverslicense',
		'number'   => '123123123',
		'issuedby' => 'NY'
   	),
   	array(
		'fullname' => 'Hagar Zipdrive Horrible',
		'address1' => 'PO Box 1',
		'city'     => 'Valhalla',
		'state'    => 'NA',
		'zip'      => '12345',
		'country'  => 'NO',
		'phone'    => '+4712555554',
		'email'    => 'devnull@vikings.dot'
   	) 
);

if ( PEAR::isError( $telecheck ) )
	die( $telecheck->getMessage() );

   
// Add a couple of items

$item = $ssp->addLineItem (
   	$auth,
   	'1',
   	'BEER_021',
   	'Heineken Dark',
   	'5.00USD',
   	'0.08',
   	array (
 		'type' => 'import',
   	)
);

if ( PEAR::isError( $item ) )
	die( $item->getMessage() );

$item = $ssp->addLineItem (
   	$auth,
   	'1',
   	'BEER_031',
   	'Bass',
   	'5.00USD',
   	'0.08'
);

if ( PEAR::isError( $item ) )
	die( $item->getMessage() );


// This ends the Telecheck request


/**
 * Submittal to Surepay's server and response decode
 */

// prepareRequest
// Takes all the added objects and creates XML data
// The XML data is stored in variable xml_request as well as returned
// You MUST execute this before posting a request

// Syntax:
// string: prepareRequest ()

$res = $ssp->prepareRequest();

if ( PEAR::isError( $res ) )
	die( $res->getMessage() );


// At this point in the script you could:
// Use the created XML data and submit by other means
//    or
// Have built the XML data your self and assign it to xml_request
//    or
// Manipulate the XML data to fit spesific needs or perhaps store it
// in a database or file or send email (USE ENCRYPTION!!!)

// Give some output for this test script
echo "</PRE><h2>The Request we are submitting:</H2><PRE>" .
htmlentities( $ssp->xml_request, ENT_QUOTES ) . "\n<hr>";


// submitRequest
// Submits your prepared XML data to the surepay server and
// awaits a response. The response is stored in xml_response,
// as well as being returned.

// Syntax
// string: submitRequest (
//		[ integer: timeout in seconds (default 90)]
//		[,integer: SSL version (0|2|3)]
// )

$res = $ssp->submitRequest();

if ( PEAR::isError( $res ) )
	die( $res->getMessage() );

// Give some respons output to the test script
echo "</PRE><h2>The response xml we got back:</H2><PRE>" . 
htmlentities( $ssp->xml_response, ENT_QUOTES ) . "\n<hr>";


// parseResponse
// Parses received response data and returns a total amount of responses

// Syntax:
// int: parseResponse ()

$responsecount = $ssp->parseResponse();

if ( PEAR::isError( $responsecount ) )
	die( $responsecount->getMessage() );


// auths
// returns an array holding the keys of all responses of type AUTH

// Syntax:
// array: auths( )

$auths = $ssp->auths();


// Output our responses and their status to the test script
// This output is a little messy, but you get the general idea
// from the authStatus control.
// It is up to you and your application how to handle the
// various states, check avs and cvv2 status and so on...

echo "</PRE><h3>Results:</H3>\n";
echo "<p>" . $responsecount . " responses was successfully parsed</p>\n";

while ( list( $key, $order ) = each( $auths ) ) 
{
	echo "<p>Response $key - Ordernumber <b>$order</b><br>\n";
	echo "Transaction id: <b>" . ( $ssp->authTransId( $order ) ) . "</b><BR>\n";
	echo "Authorization status: <b>" . $ssp->authStatus( $order ) . " </b> --> ";

	if ( $ssp->authStatus( $order ) == 'AUTH' ) 
		echo 'Successfull authorized';
	else if ( $ssp->authStatus( $order ) == 'DCL' ) 
		echo 'Declined';
	else if ( $ssp->authStatus( $order ) == 'REF' ) 
		echo 'Refered (NOT authorized!)';
	else if ( $ssp->authStatus( $order ) == 'ERR' ) 
		echo 'An error occured while processing';
	else 
		echo 'Unknown or no status type (probably failure)';

	echo "<br>\n";
	echo "Authcode: <b>"   . $ssp->authAuthCode( $order )   . "</b><br>\n";
	echo "cvv2result: <b>" . $ssp->authCVV2Result( $order ) . "</b><br>\n";
	echo "avs: <b>"        . $ssp->authAVS( $order )        . " </b><br>\n";
	echo "failure: <b>"    . $ssp->authFailure( $order )    . "</b><br>\n";
	echo "error text: <b>" . $ssp->authText( $order )       . "</b></p>\n";
}

// For more information on the various response datam seethe surepay SDK doc (XML section).
// Especially Security code verification status, cvv2status, and address verification
// status, avs, may be interresting.
 
unset( $ssp );

?>

</body>
</html>
