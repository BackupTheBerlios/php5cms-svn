<?php

require( '../../../../../prepend.php' );

using( 'format.swf.FlashImage' );
using( 'format.swf.FlashWriter' );


// global variables
$currentByte =  0; // the value of the current byte being created
$bytePos     =  8; // the number of bits left to fill in the current byte
$minBits     =  0; // mininum bits required to store the movies dimensions

$jpg_path = "./";
$flashimg = new FlashImage;

if ( !$flashimg->setImage( $image . ".jpg" ) )
	$flashimg->setImage( $jpg_path . "test.jpg" );

if ( !$flashimg->checkImage() )
	$flashimg->setImage( $jpg_path . "test.jpg" );

$flashimg->outputSWF();

?>
