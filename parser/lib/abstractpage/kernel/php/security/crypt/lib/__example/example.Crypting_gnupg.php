<?php

require( '../../../../../../prepend.php' );

using( 'security.crypt.lib.Crypting' );


echo "<pre>\n";

$options = array(
	"gnupghome" => "/home/ap/.gnupg",
	"gnupgtemp" => "/home/ap/.gnupg/temp/"
);

$cGNUPG = Crypting::factory( 'gnupg', $options );
$c = $cGNUPG->encrypt( "GNU's Not Unix!", array( 'recipients' => array( 'Paul McCartney', 'one@two.com' ) ) );

echo "</pre>\n";

?>
