<?php

header("Content-Type: text/plain");


require( '../../../../../prepend.php' );

using( 'util.array.BitArray' );

	
function printValues( $myList, $myWidth )
{
	for ( $i = 0; $i <= ( $myWidth - 1 ); $i++ ) 
	{
		if ( $myList[$i] )
			echo "true";
		else
			echo "false";
		
		echo "\t";
	}
	
	echo "\n";
}

	
$myBA1 = new BitArray( 4 );
	
$myBA1->set( 0, false );
$myBA1->set( 1, false );
$myBA1->set( 2,  true );
$myBA1->set( 3,  true );

echo "\nBA1 values:\n";
printValues( $myBA1->getAll(), 4 );
echo "\nBA1 values after _not():\n";
$myBA1->_not( $myBA1 );
printValues( $myBA1->getAll(), 4 );	

?>
