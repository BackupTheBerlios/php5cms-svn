<?php
error_reporting(E_ERROR);

require( '../../../../../../prepend.php' );

using( 'xml.cbl.lib.CBL' );


$doc = &CBL::createDocument( 'myXUL.xml' );
// $doc->setDTD( 'http://www.docuverse.de/dtd/cbl2.0/cbl2.dtd' );
$doc->enableValidation();

/* create root */
$cbl = &$doc->createElement( 'cbl', array( 'key'=> '12a4-15tz-uz8i-tr56' ) );

/* head */
$head = &$doc->createElement( 'head' );
$cbl->appendChild( $head );

/* body */
$body = &$doc->createElement( 'body' );

for ( $i = 0; $i < 100; $i++ )
	$body->addItem( array( 'id'=> 'item_' . $i ) );

$cbl->appendChild( $body );

$doc->addRoot( $cbl );
$doc->send();

?>
