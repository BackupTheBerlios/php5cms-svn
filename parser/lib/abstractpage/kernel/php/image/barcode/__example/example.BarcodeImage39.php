<?php

/*
PARAMETERS:
-----------

$barcode        = [required] The barcode you want to generate

$type           = (default=0) It's 0 for Code 3 of 9 (the only one supported)
        
$width          = (default=160) Width of image in pixels. The image MUST be wide
                                enough to handle the length of the given value. The default
                                value will probably be able to display about 6 digits. If you
                                get an error message, make it wider!

$height         = (default=80) Height of image in pixels
        
$format         = (default=jpeg) Can be "jpeg", "png", or "gif"
        
$quality        = (default=100) For JPEG only: ranges from 0-100

$text           = (default=1) 0 to disable text below barcode, >=1 to enable


USAGE EXAMPLES FOR ANY PLAIN OLD HTML DOCUMENT:
-----------------------------------------------

<IMG SRC="example.BarcodeImage39.php?barcode=HELLO&quality=75">
<IMG SRC="example.BarcodeImage39.php?barcode=123456&width=320&height=200">
*/

require( '../../../../../prepend.php' );

using( 'image.barcode.BarcodeImage39' );


if ( isset( $_GET["text"] ) ) 
	$text = $_GET["text"];

if ( isset( $_GET["format"] ) ) 
	$format = $_GET["format"];

if ( isset( $_GET["quality"] ) ) 
	$quality = $_GET["quality"];

if ( isset( $_GET["width"] ) ) 
	$width = $_GET["width"];

if ( isset( $_GET["height"] ) ) 
	$height = $_GET["height"];

if ( isset( $_GET["barcode"] ) ) 
	$barcode = $_GET["barcode"];


$bi = new BarcodeImage39;
$bi->generate( $barcode, $width, $height, $quality, $format, $text );

?>
