<?php

require( '../../../../../../prepend.php' );

using( 'io.config.deep.AbstractPageLicense' );


$di = new AbstractPageLicense;
$di->read( 'license.ini' );

echo $di->getProduct() . "<br />\n";
echo $di->getVendor()  . "<br />\n";
echo $di->getVersion() . "<br />\n";
echo $di->getBuild()   . "<br />\n";
echo $di->getSerial()  . "<br />\n";

?>
