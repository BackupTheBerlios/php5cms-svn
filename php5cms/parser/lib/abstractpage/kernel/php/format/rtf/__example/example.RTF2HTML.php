<?php

require( '../../../../../prepend.php' );

using( 'format.rtf.RTF2HTML' );


$obj = new RTF2HTML();
echo $obj->getHTML( "example.rtf" );

?>
