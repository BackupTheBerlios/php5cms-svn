<?php

require( '../../../../prepend.php' );

using( 'xml.PathParser' );


function name( $name, $attribs, $content )
{
	print( "<br/>" );
	print( "Hey $name <br/>\n" );
	print_r( $attribs );
	print( "<br/>" );
}

$parser = new PathParser();
$parser->setHandler( "/foo/data/name", "name" );
$parser->setHandler( "/foo/data", "name" );
$parser->setHandler( "/foo/data/type/var", "name" );

if ( !$parser->parseFile( "foo.xml" ) )
	print( "Error:" . $parser->getError() . "<br/>\n" ); 

?>
