<?php

require( '../../../../../prepend.php' );

using( 'db.mysql.XMLDbInterpreter' );


$xml = new XMLDbInterpreter();
$xml->setSchema( './table.xml', true );
$xml->parseSchema();

// $xml->create( 'localhost', 'richard', '95219430450' );

header( 'Content-Type: text/plain' );
print_r( $xml->getSQL() );

?>
