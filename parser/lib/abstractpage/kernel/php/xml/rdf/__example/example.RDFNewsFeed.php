<?php

require( '../../../../../prepend.php' );

using( 'xml.rdf.RDFNewsFeed' );
	

$rdf = new RDFNewsFeed();
$rdf->setChannel( "Docuverse News", "http://www.docuverse.de/news/" );

echo( $rdf->getDeclaration() . "\n\n" );
echo( $rdf->getSource( true ) );

?>
