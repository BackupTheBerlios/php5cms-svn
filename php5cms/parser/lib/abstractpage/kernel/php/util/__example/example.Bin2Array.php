<?php

require( '../../../../prepend.php' );

using( 'util.Bin2Array' );


$ba = new Bin2Array( "11111111" );						// initialize with a string of a number in binary representation
$ba = new Bin2Array( "255" );							// initialize with a string of decimal number
$ba = new Bin2Array( "FF" );							// initialize with a string of hexa number
$ba = new Bin2Array( true );							// initialize with a simple value as boolean variable 1:0
$ba = new Bin2Array( array( 1, 1, 1, 1, 1, 1, 1, 1 ) );	// initialize with an array of binary M7....M0 (BE)
$ba = new Bin2Array( 15.5 );							// initialize with a float value (this will be casted to integer)
$ba->add( 15 );											// add a decimal value
$ba->add( "FF" );										// add, in hexa string
$ba->add( "15" );										// add, in decimal string
$ba->add( "1111" );										// add, in binary string
$ba->add( $ba );										// add, two objects
$ba->op( "add", "15" );									// operation main

echo $ba->hbin;

?>
