<?php

require( '../../../../prepend.php' );

using( 'io.FileUtil' );


$res = FileUtil::getFileAttr( 'logo_docuverse.gif' );
echo( "<pre>" );
print_r( $res );
echo( "</pre>" );

?>

