<?php

require( '../../../../../prepend.php' );

using( 'image.graph.ThreeDChart' );


header( 'Content-type: image/png' );

$i = @imagecreate( 640, 480 ) or die( "Can't create image" );
$white = imagecolorallocate( $i, 255, 255, 255 );
$black = imagecolorallocate( $i, 0, 0, 0 );
$C[sizeof($C)] = imagecolorallocate( $i, 255, 0, 0 );
$C[sizeof($C)] = imagecolorallocate( $i, 0, 255, 0 );
$C[sizeof($C)] = imagecolorallocate( $i, 0, 0, 255 );
$C[sizeof($C)] = imagecolorallocate( $i, 255, 255, 255 );
$C["axis"]     = $black;
$C["grid"]     = $black;
$C["border"]   = $black;

srand( (double)microtime()*1000000 );
$bar_num = rand( 2, 40 );
$white   = rand( 0, 20 );
$font    = rand( 1,  3 );

for ( $j = 0; $j < $bar_num; $j++ )
{
	$D[$j] = rand( 0, 1000 );
	$Legend[$j] = $j;
}

$L = new ThreeDChart( 100, 360 );
$L->chart_font  = $font;
$L->chart_white = $white;
$L->mChart3d( $i, $D, $Legend, 460, 100, 300, $C, 100, "$bar_num bars, $white% white space, font $font", "rand(0, 1000)" );

imagepng( $i );
	
?>
