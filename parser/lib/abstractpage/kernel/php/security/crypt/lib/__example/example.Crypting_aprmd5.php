<?php

require( '../../../../../../prepend.php' );

using( 'security.crypt.lib.Crypting' );


echo "<pre>\n";

$cAPR = Crypting::factory( 'aprmd5' );
$c = $cAPR->encrypt( 'Apache Portable Runtime' );

echo $c . "\n";
echo $m;

echo "</pre>\n";

?>
