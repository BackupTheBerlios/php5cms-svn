<?php

require( '../../../../../prepend.php' );

using( 'format.vformat.VCard' );


$v = new VCard();

$v->setPhoneNumber( "+49 23 456789", "PREF;HOME;VOICE" );
$v->setName( "Mustermann", "Thomas", "", "Herr" );
$v->setBirthday( "1960-07-31" );
$v->setAddress( "", "", "Musterstrasse 20", "Musterstadt", "", "98765", "Deutschland" );
$v->setEmail( "thomas.mustermann@thomas-mustermann.de" );
$v->setNote( "You can take some notes here.\r\nMultiple lines are supported via \\r\\n." );
$v->setURL( "http://www.thomas-mustermann.de", "WORK" );

$output   = $v->getVCard();
$filename = $v->getFileName();

header( "Content-Disposition: attachment; filename=$filename" );
header( "Content-Length: " . strlen( $output ) );
header( "Connection: close" );
header( "Content-Type: text/x-vCard; name=$filename" );

echo $output;

?>
