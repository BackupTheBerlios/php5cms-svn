<?php

require( '../../../../prepend.php' );

using( 'session.SessionUtil' );
error_reporting(E_ERROR);
extract($_REQUEST);

if ( !empty( $id ) )
	echo "ID: $id<br>\n";

$id = SessionUtil::getSessionID(); 

print( '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '">' ); 
print( '<input type="submit" value="Generate new ID">' ); 
print( '</form>' ); 

?>
