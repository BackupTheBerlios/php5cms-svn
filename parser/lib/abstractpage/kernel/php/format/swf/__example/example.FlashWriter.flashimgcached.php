<?php

require( '../../../../../prepend.php' );

using( 'format.swf.FlashImage' );
using( 'format.swf.FlashWriter' );


// global variables
$currentByte =  0; // the value of the current byte being created
$bytePos     =  8; // the number of bits left to fill in the current byte
$minBits     =  0; // mininum bits required to store the movies dimensions

$jpg_path = "./";
$swf_path = "./";

if ( !$image )
	$image = "test";

$uptodate = false;

// do both files (image.jpg and image.swf) exists?
if ( file_exists( $jpg_path . $image . ".jpg" ) && file_exists( $swf_path . $image . ".swf" ) )
{
	// and is the swf more recent?
	if ( filemtime( $swf_path . $image . ".swf" ) >= filemtime( $jpg_path . $image . ".jpg" ) )
	{
		// then we can use it
		$uptodate = true;
	}
}
	
// is the swf up to date
if ( $uptodate )
{
	// just pass the swf
	header( "Content-type: application/x-shockwave-flash" );
	readfile( $swf_path . $image . ".swf" );
}
else
{
	// the swf seems to be outdated, create a new one
	$flashimg = new FlashImage;
	$flashimg->setImage( $jpg_path . $image . ".jpg" );
	$flashimg->saveToFile( $swf_path . $image . ".swf" );
	$flashimg->outputSWF();
}

?>
