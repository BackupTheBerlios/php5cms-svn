<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>

<head>

<title>OpenSSL Example</title>

</head>

<body>
	
<?php

/**
 *  Steps to Using Class:
 *    1. Include the class file in your source
 *    2. Create an instance of the class
 *    3. Set the public key path
 *    4. Set the private key path
 *    5. Set the passphrase ( set to "" if passphrase not used)
 *    6. To Encrypt:
 *    		a. Call encrypt_data_public() to encrypt
 *    		b. Call get_encrypted_data() to retrieve data
 *
 *    7. To Decrypt:
 *    	 	a. Call decrypt_data_private
 *    	 	b. Call get_decrypted_data() to retrieve data
 */

require( '../../../../../prepend.php' );

using( 'security.openssl.OpenSSL' );


// Create an instance of the class
$cls_encrypt = new OpenSSL;

// set or get? required parameters
// this way it works in development environment
// and through test posting page
if ( !isset( $_POST["public_key"] ) )
	$path_to_cert = "F:\\ApacheGroup\\Apache\\htdocs\\cvs_working\\rterra\\online_signup\\includes\\cacert.pem";
else
  	$path_to_cert = $_POST["public_key"];

if ( !isset( $_POST["private_key"] ) )
  	$path_to_key = "F:\\ApacheGroup\\Apache\\htdocs\\cvs_working\\rterra\\online_signup\\includes\\privkey.pem";
else
  $path_to_key = $_POST["private_key"];

if ( !isset( $_POST["passphrase"] ) )
  	$pass_phrase = "testing";
else
  	$pass_phrase = $_POST["passphrase"];

if ( !isset( $_POST["encrypt_string"] ) )
  	$string_to_encrypt = "411111111111-09/2004";
else
  	$string_to_encrypt = $_POST["encrypt_string"];

// set the public key path
$cls_encrypt->set_public_key( $path_to_cert );

// set the private key path
$cls_encrypt->set_private_key( $path_to_key );

// set the passphrase if applicable
$cls_encrypt->set_passphrase( $pass_phrase );

echo( "<div>Data to Encrypt:</div>\n" );
echo( "<div>".$string_to_encrypt."</div>\n" );

echo ( "<div>Encrypting Data....</div>\n\n" );

// Call encrypt_data_public() to encrypt
$ret = $cls_encrypt->encrypt_data_public( $string_to_encrypt );

if ( $ret != OPENSSL_SUCCESS )
{
  	print_error( $cls_encrypt->get_error_number(), $cls_encrypt->get_error_string(),$cls_encrypt );
  	die();
}

//6b. Call get_encrypted_data() to retrieve data
$encrypted_data = $cls_encrypt->get_encrypted_data();

//output encryption strength just for fun:)
$encryption_strength = strlen( $encrypted_data ) * 8;

echo( "<div>Encryption Strength:</div>\n" );
echo( "<div>".$encryption_strength." bit Encryption</div>\n" );

echo( "<div>Encrypted Data:</div>\n" );
echo( "<div>".$encrypted_data."</div>\n" );

echo( "<div>URL Encoded Encrypted Data:</div>\n" );
echo( "<div>". urlencode($encrypted_data)."</div>\n" );

echo( "<div>Hex Encoded Encrypted Data:</div>\n" );
echo( "<div>". bin2hex($encrypted_data)."</div>\n" );

echo( "<div>Decrypting Data...</div>\n\n" );


// Call decrypt_data_private
$ret = $cls_encrypt->decrypt_data_private($encrypted_data);
if ( $ret != OPENSSL_SUCCESS )
{
  	print_error( $cls_encrypt->get_error_number(), $cls_encrypt->get_error_string(), $cls_encrypt );
  	die();
}

//7b. Call get_decrypted_data() to retrieve data
$decrypted_data = $cls_encrypt->get_decrypted_data();

echo( "<div>Decrypted Data:</div>\n" );
echo( "<div>" . $decrypted_data . "</div>\n" );

echo( "</body></html>" );


function print_error( $errno, $error, $cls )
{
 	echo( "<div><p>Class Error(s):</p>".$errno." ** ".$error."</div>");
 	echo( "<div><p>OpenSSL Error(s):</p>");
 	$cls->kick_openssl_errors();
 	echo( "</div>\n\n");
}

function hex_encode( $email_address )
{
	$encoded = bin2hex("$email_address");
	$encoded = chunk_split($encoded, 2, '%');
	$encoded = '%' . substr($encoded, 0, strlen($encoded) - 1);
	
	return $encoded;
}

?>
