<?php

require( '../../../../../prepend.php' );

using( 'peer.http.Query' );


$q = new Query;
$q->getQueryVars( $_SERVER["QUERY_STRING"], $var, true ); 

while ( list( $a, $b ) = each( $var ) )
{ 
	while ( list( $c, $d ) = each( $b ) )
		print $a . "/" . $d . "<br>";
}
 
?>    
