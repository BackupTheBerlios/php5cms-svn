<?php

require( '../../../../../prepend.php' );

using( 'image.verification.VerificationImage' );


session_start();
$vImage = new VerificationImage;
$vImage->generateText( $_GET['size'] );
$vImage->showimage();

?>
