<?php
header();

require( '../../../../prepend.php' );

using( 'xml.CSV2XML' );


$csv2xml = new CSV2XML( null, null, "bank" );
$csv2xml->encoding = "ISO-8859-1";
$csv2xml->convert( "blz.csv" );

?>
