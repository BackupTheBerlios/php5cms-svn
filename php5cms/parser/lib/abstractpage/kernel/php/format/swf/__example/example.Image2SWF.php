<?php

require( '../../../../../prepend.php' );

using( 'format.swf.Image2SWF' );


$swf = new Image2SWF;
$swf->buildFromJPG( "picture.jpg" );

$swf->show();
// $swf->save( "demo.swf" );

?>
