<?php

require( '../../../../../prepend.php' );

using( 'image.text.ImageText' );


if ( !isset( $_GET['text'] ) )
	$_GET['text'] = 0;
    

$img = imagecreatetruecolor('500', '500');

$white = imagecolorallocate( $img, 255, 255, 255 );
$black = imagecolorallocate( $img, 0, 0, 0 );

imagefill( $img, 0, 0, $white );
imagerectangle( $img, 0, 0, 499, 499, $black );

$texts[] = "geek by nature, php by choice"; // Short Text
$texts[] = "a good computer is like a tipi - no windows, no gates and an apache inside"; // Normal Text
$texts[] = "What is PEAR? The fleshy pome, or fruit, of a rosaceous tree (Pyrus communis), cultivated in many varieties in temperate climates. PEAR is a framework and distribution system for reusable PHP components. PECL, being a subset of PEAR, is the complement for C extensions for PHP. See the FAQ and manual for more information"; // Long Text

$colorsets[] = array(
	'#ff0000', 
	'#00ff00', 
	'#0000ff'
);

$colorsets[] = array(
	array( 'r' => 100, 'g' => 100, 'b' => 100 ),
	array( 'r' => 150, 'g' => 150, 'b' => 150 ),
	array( 'r' => 200, 'g' => 200, 'b' => 200 )
);

$colorsets[] = "#000000";

$options = array(
	'width'        => 300,
	'height'       => 50,
	'fontPath'     => './',
	'fontFile'     => 'trigger.ttf',
	'antiAliasing' => true
);


$test = new ImageText( $texts[$_GET['text']], $options );
$test->measurize();
$test->colorize( $colorsets[array_rand($colorsets)] );
$test->align( $_GET['align'] );

if ( @$_GET['border'] == 1 )
	$test->addBorder( 10, '#ff0000', '#ffffff', 10, 20 );

if ( @$_GET['shadow'] == 1 )
	$test->addShadow( 2, 2, '#c0c0c0' );

$test->renderImage( 100, 50, $img );

header( "Content-type: image/png" );
imagepng( $img );
imagedestroy( $img );

?>
