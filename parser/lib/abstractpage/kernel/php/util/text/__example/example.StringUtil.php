<?php

require( '../../../../../prepend.php' );

using( 'util.text.StringUtil' );


$before = "Get free Viagra";
$after  = StringUtil::getAntiSpamfilterHeadline( $before );

header( "Content-type: text/plain" );
echo $before . "\n";
echo $after;

?>
