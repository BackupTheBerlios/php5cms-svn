<?php

ob_start();

require( '../../../../../../prepend.php' );

using( 'peer.xmlrpc.lib.XMLRPCServer' );


$server = new XMLRPCServer();

$server->registerFunction( "myFunc" );
$server->registerFunction( "myFunc2", array( new XMLRPCString(), new XMLRPCString() )  );
$server->registerFunction( "myFile" );
$server->registerFunction( "tellMe" );
$server->registerFunction( "secret" );
$server->registerFunction( "add", array( new XMLRPCInt(), new XMLRPCInt() ) );
$server->registerFunction( "foo", array( new XMLRPCInt() ) );
$server->registerFunction( "myPi" );
$server->registerFunction( "currentTime" );
$server->registerFunction( "returnFirstArg", array( new XMLRPCArray() ) );
$server->registerFunction( "addArray", array( new XMLRPCArray() ) );
$server->registerFunction( "giveMeArray" );
$server->registerFunction( "giveMeStruct" );

$server->processRequest();


function myFunc( )
{
    $tmp = new XMLRPCString( "This comman< &&&&&d > & was>> run by xml rpc" );
    return $tmp;
}
function myFunc2( $args )
{
    $tmp = new XMLRPCString( "You send me: " . $args[0]->value() );
    return $tmp;
}
function secret( )
{
    $tmp = new XMLRPCString( "42, don't tell!" );
    return $tmp;
}
function tellMe( )
{
    $tmp = new XMLRPCBool( true );
    return $tmp;
}
function giveMeArray( )
{
    $tmp = new XMLRPCArray( array(
		new XMLRPCDouble( 3.14 ),
		new XMLRPCString( "second" ),
		new XMLRPCString( "second" ),
		new XMLRPCString( "second" ),
		new XMLRPCArray( array(
			new XMLRPCDouble( 3.14 ),
			new XMLRPCString( "second" ),
			new XMLRPCString( "second" ),
			new XMLRPCString( "second" ) ) )
		)
	);
	
    return $tmp;
}
function giveMeStruct( )
{
    $tmp = new XMLRPCStruct( array(
		"errorCode"     => new XMLRPCInt( 42 ),
		"errorMessage"  => new XMLRPCString( "Secret" ),
		"doubleTest"    => new XMLRPCDouble( 3.1415 ),
		"errorMessage2" => new XMLRPCString( "Secret, not!" ),
		"ArrayInside"   => new XMLRPCArray( array(
			new XMLRPCString( "first" ),
			new XMLRPCString( "level1_1" ) ) )
		)
	);
	
    return $tmp;
}
function returnFirstArg( $args )
{
    return $args[0];
}
function add( $args )
{
    $res = $args[0]->value() + $args[1]->value();

    $tmp = new XMLRPCDouble( $res );
    return $tmp;
}
function foo( $args )
{
    $ret = "";
	
    for ( $i=0; $i<$args[0]->value(); $i++ )
    {
        $ret .= "blaa $v";
    }
	
    return new XMLRPCString( $ret );
}
function myPi( )
{
    return new XMLRPCDouble( 3.1415 );
}
function currentTime( )
{
    return new XMLRPCDateTime( );
}
function myFile( )
{
    $filePath =  "folder.gif";
    $fp       =  fopen( $filePath, "r" );
    $fileSize =  filesize( $filePath );
    $content  =& fread( $fp, $fileSize );
    
    return new XMLRPCBase64( $content );
}
function addArray( $args )
{
    $ret = "";
	
    // fetch the first parameter
    $args = $args[0];
    
    foreach ( $args->value() as $arg )
    {
        print( $arg );
        $ret += $arg->value();
    }
    
    return new XMLRPCInt( $ret );
}

ob_end_flush();

?>
