<?php

require( '../../../../../prepend.php' );

using( 'peer.js.JSRemoteScripting' );


$rs = new JSRemoteScripting;
$rs->dispatch( "getPostCode" );

function getPostCode( $val )
{
	switch( $val )
	{
		case "75008" : 
			$ville = "Paris 8ieme";
			break;
		
		case "75013" : 
			$ville = "Paris 13ieme";
			break;
		
		case "92100" : 
			$ville = "Boulogne Billnacourt";
			break;
	}
	
	return $ville;
}

?>
