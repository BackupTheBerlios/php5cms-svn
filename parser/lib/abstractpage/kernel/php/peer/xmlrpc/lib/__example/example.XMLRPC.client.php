<?php

require( '../../../../../../prepend.php' );

using( 'peer.xmlrpc.lib.XMLRPCClient' );
using( 'peer.xmlrpc.lib.XMLRPCCall' );


$client = new XMLRPCClient( "docuverse.de", "example.minimalserver.php" );

// error test, to many parameters
print( "error test:<br>" );

$call = new XMLRPCCall( );
$call->setMethodName( "myFunc2" );
$call->addParameter( new XMLRPCString( "bla" ) );
$call->addParameter( new XMLRPCString( "bla" ) );
$call->addParameter( new XMLRPCDouble( "bla" ) );

$response = $client->send( $call );
$result   = $response->result();

if ( $response->isFault() )
{
    print( "The server returned an error (" .  $response->faultCode() . "): ". 
		$response->faultString() .
		"<br>" );
}
else
{
    print( "The server returned: " . $result->value() . "<br>" );
}

$call = new XMLRPCCall( );
$call->setMethodName( "currentTime" );

$response = $client->send( $call );
$result   = $response->result();

print( "The server returned: " . $result->value() . "<br>" );

// array test
$call = new XMLRPCCall( );
$call->setMethodName( "giveMeArray" );

$response = $client->send( $call );
$result   = $response->result();

print( "The server returned: " . "<br>" );
print( "<pre>" );

print_r( $result->value() );

print( "</pre>" );
    
foreach ( $result->value() as $item )
{
    print( $item->value() . "<br>" );
    
    if ( gettype( $item->value() )  == "array" )
    {
        foreach ( $item->value() as $subItem )
			print( $subItem->value() . "<br>" );
    }
}

// struct
print( "<hr>Struct:<br>" );

$call = new XMLRPCCall( );
$call->setMethodName( "giveMeStruct" );

$response = $client->send( $call );
$result   = $response->result();
$struct   = $result->value();

print( "<pre>" );

print_r( $struct );

print( "</pre>" );
print( $struct["errorCode"]->value()    . "<br>" );
print( $struct["errorMessage"]->value() . "<br>" );

$call = new XMLRPCCall( );
$call->setMethodName( "add" );
$call->addParameter( new XMLRPCInt( 2 ) );
$call->addParameter( new XMLRPCInt( 3 ) );

$response = $client->send( $call );
$result   = $response->result();

print( "The server returned: " . $result->value() . "<br>" );

// array as argument
$call = new XMLRPCCall( );
$call->setMethodName( "addArray" );

$call->addParameter( new XMLRPCArray( array(
	new XMLRPCDouble( "1" ),
	new XMLRPCInt( "2" ),
	new XMLRPCInt( "3" ),
	new XMLRPCInt( "4" ) 
) ) );

$response = $client->send( $call );
$result   = $response->result();

print( "The server returned: " . $result->value() . "<br>" );

// send return test, with array
print( "<hr>" );
print( "<br>Send and return test:<br>" );

$call = new XMLRPCCall( );
$call->setMethodName( "returnFirstArg" );

// create an advanced datatype combination:
$call->addParameter( new XMLRPCArray( array(
	new XMLRPCDouble( 4.32 ),
	new XMLRPCInt( "2" ),
	new XMLRPCString( "Foo bar" ),
	new XMLRPCInt( "3" ),
	new XMLRPCStruct( array(
		"ADoubleValue" => new XMLRPCDouble( 42.2223 ),
		"AnInt"        => new XMLRPCInt( 2 ),
		"AString"      => new XMLRPCString( "3" ),                                                                          
		"BoolItIS"     => new XMLRPCBool( true ),
		"ASubArray"    => new XMLRPCArray( array(
			new XMLRPCDouble( 3.1415 ),
			new XMLRPCInt( "2" ),
			new XMLRPCInt( "3" ),
			new XMLRPCInt( "4" ) ) ) ) ),
				new XMLRPCArray( array(
					new XMLRPCDouble( 3.1415 ),
					new XMLRPCInt( "2" ),
					new XMLRPCInt( "3" ),
					new XMLRPCStruct( array(
						"ADoubleValue" => new XMLRPCDouble( 42.2223 ),
						"AnInt"        => new XMLRPCInt( 2 ),
						"AString"      => new XMLRPCString( "3" ),                                                                          
						"BoolItIS"     => new XMLRPCBool( true ),
						"ASubArray"    => new XMLRPCArray( array(
							new XMLRPCDouble( 3.1415 ),
							new XMLRPCInt( "2" ),
							new XMLRPCInt( "3" ),
							new XMLRPCArray( array( new XMLRPCDouble( 3.1415 ),
							new XMLRPCInt( "2" ),
							new XMLRPCInt( "3" ),
							new XMLRPCStruct( array(
								"ADoubleValue" => new XMLRPCDouble( 42.2223 ),
								"AnInt"        => new XMLRPCInt( 2 ),
								"AString"      => new XMLRPCString( "3" ),                                                                          
								"BoolItIS"     => new XMLRPCBool( true ),
								"ASubArray"    => new XMLRPCArray( array(
									new XMLRPCDouble( 3.1415 ),
									new XMLRPCInt( "2" ),
									new XMLRPCInt( "3" ),
									new XMLRPCInt( "4" ) ) ) ) ),
									
										new XMLRPCInt( "4" ) ) ),
									
										new XMLRPCInt( "4" ) ) ) ) ),
										
										new XMLRPCInt( "4" ) ) ) ) ) );
                     
$response = $client->send( $call );

if ( $response->isFault() )
{
    print( "The server returned an error (" .  $response->faultCode() . "): ". 
		$response->faultString() .
		"<br>" );
}
else
{
    $result = $response->result();

    print( "<pre>" );
	
    print_r( $result->value() );
    
	print( "</pre>" );
    print( "The server returned: " . $result->value() . "<br>" );
    print( "The server returned: " . $result->value() . "<br>" );
}

print( "<hr>" );

/// misc tests

$call = new XMLRPCCall( );
$call->setMethodName( "foo" );
$call->addParameter( new XMLRPCInt( 10 ) );

$response = $client->send( $call );
$result   = $response->result();

print( "The server returned: " . $result->value() . "<br>" );

$call = new XMLRPCCall( );
$call->setMethodName( "secret" );

$response = $client->send( $call );
$result   = $response->result();

print( "The server returned: " . $result->value() . "<br>" );

$call = new XMLRPCCall( );
$call->setMethodName( "tellMe" );

$response = $client->send( $call );
$result   = $response->result();

print( "The server returned: " . $result->value() . "<br>" );

?>
