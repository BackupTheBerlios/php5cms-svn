<?php

require( '../../../../../prepend.php' );

using( 'html.form.FormParser' );


echo( "<pre>" );

$parser =& new FormParser( file_get_contents( 'http://www.docuverse.de/kontakt.php' ) );
$result = $parser->parse();

print_r( $result );
echo( "</pre>" );

?>
