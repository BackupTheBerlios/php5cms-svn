<?php

error_reporting( E_ALL & ~E_NOTICE );


require( '../../../../../../prepend.php' );

using( 'image.watermark.lib.CachedThumbnail' );
using( 'image.watermark.lib.WatermarkThumbnail' );


ob_start();


/* Using basic thumbnail class */
/*
$thumbnail = new CachedThumbnail( 'sample.jpg );

$thumbnail->setMaxSize( 55, 151 );
$thumbnail->setQualityOutput( true );

$thumbnail->outputThumbnail( 'jpg', 80 ); // use returnThumbnail() to work with the created thumbnail
*/


/* Using the thumbnail class with built-in cache functions */
/*
$thumbnail = new CachedThumbnail( 'sample.jpg', 10 ); // picture, cache time in seconds (default: 0 sec. = no caching)

$thumbnail->setMaxSize( 155, 151 ); // set max. width and height of the thumbnail (default: 100, 100)
$thumbnail->setQualityOutput( true ); // quality or speed when creating the thumbnail (default: true)

$thumbnail->outputThumbnail( 'jpg', 80 ); // picture type (png, jpg, gif, wbmp), jpg-quality (0-100) (default: png, 75)
*/


/* Using the thumbnail class with built-in cache and watermark / logo function */

/* picture, cache time in seconds (default: 0 sec. = no caching) */
$thumbnail = new WatermarkThumbnail( 'sample.jpg' );

/* path to logo/watermark picture, position of the logo: 1 = left-top,
2 = right-top, 3 = right-bottom, 4 = left-bottom, 5 = center (default = 3),
margin to the border (default = 1) */
$thumbnail->addLogo( 'logo.png', 3, 1 );
// $thumbnail->addLogo( 'icon.png', 2, 3 ); // add more logos if you want

/* set max. width and height of the thumbnail (default: 100, 100) */
$thumbnail->setMaxSize( 150, 120 );

/* quality or speed when creating the thumbnail (default: true) */
$thumbnail->setQualityOutput( true );

/* picture type (png, jpg, gif, wbmp), jpg-quality (0-100) (default: png, 75) */
$thumbnail->outputThumbnail( 'jpg', 80 );

/*
or create a hardcoded html image-tag like this:
echo '<img src="' . $thumbnail->getCacheFilepath('jpg', 80) . '" width="' .
$thumbnail->getThumbWidth() . '" height="' . $thumbnail->getThumbHeight() . '" />';
*/
ob_end_flush();

?>
