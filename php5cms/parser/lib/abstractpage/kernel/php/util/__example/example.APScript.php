<?php

require( '../../../../prepend.php' );

using( 'util.APScript' );


function getText()
{
	return "text<br>\n";
}

function getFile( $file )
{
	$data = implode( '', file( $file ) );
	return $data;
}
	
	
$ap   = new APScript( "getText", "data->", "\$code->" );
$code = $ap->compile( getFile( "./example.aps" ) );

eval( "?>$code" );

?>
