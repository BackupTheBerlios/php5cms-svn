<?php

require( '../../../../../prepend.php' );

using( 'image.watermark.TransparentWatermark' );


//set file names
$logo  = "logo.png";
$image = "marbles.jpg";
$markedImage = "marked" . $image;

// compute new watermark on same image
if ( !file_exists( $markedImage ) )
{	
	// open classe with logo
	$tw = new TransparentWatermark( $logo );
	
	// set logo's position (optional)
	$tw->setStampPosition( TRANSPARENTWATERMARK_RIGHT, TRANSPARENTWATERMARK_BOTTOM );
	
	// create new image with logo
	if ( PEAR::isError( $tw->markImageFile( $image, $markedImage ) ) ) 
		die( "Error!" );
}

// redirection to watermarked image
header( "Location: $markedImage" );

?>
