<?php

require( '../../../../../../../prepend.php' );

using( 'xml.dom.simple.lib.SimpleDomDocument' );


class Output 
{
	function println( $string ) 
	{
		printf( "%s <br>\n", $string );
	}
} // END OF Ouptut


function instanceof( $objekt, $instance ) 
{
	return ( get_class( $objekt ) == $instance );
} 
	
function getout( $element ) 
{
	$GLOBALS['out']->println( $element->toString() );
	$GLOBALS['out']->println( "<hr>" );
		
	while( $element->hasElement() ) 
		getout( $element->getNext() );
}

?>
<html>
<head>

<title>SimpleDOM Example</title>

</head>

<body bgcolor="#ffffff">

<?php

$out = new Output();
$xml = new SimpleDomDocument( 'address.xml' );
	
$out->println( "Document Information:" );
$out->println( "&nbsp;&nbsp;&nbsp;Filename: " . $xml->getFilename() );
$out->println( "&nbsp;&nbsp;&nbsp;Encoding: " . $xml->getEncoding() );
$out->println( "&nbsp;&nbsp;&nbsp;Entities:" );

$entity	= $xml->getEntity();
$keys   = array_keys( $entity );
	
for ( $i = 0; $i < count( $keys ); $i++ )
	$out->println( "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $keys[$i] . "=" . $entity[$keys[$i]] );
	
$comment = $xml->getComment();
	
$out->println( $comment->toString() );
$out->println( "<hr>" );
	
getout( $xml->getElement() );

?>

</body>
</html>
