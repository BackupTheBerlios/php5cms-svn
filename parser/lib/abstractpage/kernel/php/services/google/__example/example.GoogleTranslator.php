<?php

require( '../../../../../prepend.php' );

using( 'services.google.GoogleTranslator' );


$res = GoogleTranslator::translate( 'Do you speak english?', 'en', 'de' );
echo $res;

?>
