<?php

require( '../../../../../../prepend.php' );

using( 'locale.stopwords.lib.StopWords' );


$words = &StopWords::factory( "de" );

echo( "<pre>\n" );
echo( print_r( $words->words ) );
echo( "</pre>" );

?>
