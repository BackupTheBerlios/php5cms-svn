<?php

require( '../../../../../prepend.php' );

using( 'image.watermark.ImageWatermark' );


// Instantiate phpWatermark
// The only parameter currently required is the name
// of the image, which should get marked
$wm = new ImageWatermark( "C:\Programme\TSW\Apache2\htdocs\apkernel\kernel\php\image\watermark\__example\marbles.png" );

// Optionally specify the position of
// the watermark on the image
$wm->setPosition( "RND" );

// Add a watermark containing the string
// "phpWatermark" to the image specified above
$wm->addWatermark( "Watermark" );

// Fetch the marked image
$im = $wm->getMarkedImage();

header( "Content-type: image/png" );
imagepng( $im );

?>
