<?php

require( '../../../../../prepend.php' );

using( 'xml.xslt.XSLProcessor' );


$xslp = &new XSLProcessor();
$xslp->setBase( 'C:\Programme\TSW\Apache2\htdocs\apkernel\kernel\php\xml\xslt\__example' );
$xslp->setXSLFile( 'example.xsl' );
$xslp->setXMLFile( 'example.xml' );
$xslp->run();

echo $xslp->output();

?>
