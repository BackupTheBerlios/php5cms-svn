<?php

require( '../../../../prepend.php' );

using( 'image.ImageSize' );


$myimage = new ImageSize( "marbles.jpg" );
$x = $myimage->x;
$y = $myimage->y;

if ( $x != -1 && $y != -1 )
	print "$myimage->id ($x * $y)";
	
?>
