<?php

require( '../../../../../../../prepend.php' );

using( 'util.text.encoding.lib.Encoding' );


$enc =& Encoding::factory( "iso_8859_9" );

echo( "<pre>\n" );
echo( $enc->getEncodingType . "<br>\n" );
echo( print_r( $enc->encoding_map ) . "\n\n" );
echo( print_r( $enc->decoding_map ) );
echo( "</pre>" );

?>
