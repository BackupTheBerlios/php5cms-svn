<?php

require( '../../../../prepend.php' );

using( 'util.SortUtil' );


$test[0] = "BasicA";
$test[1] = "Silencer";
$test[2] = "GOOOOD";
$test[3] = "aerospace";
$test[4] = "Damn";
	 
SortUtil::flipsort( $test, true );

print $test[0] . "<br>";
print $test[1] . "<br>";
print $test[2] . "<br>";
print $test[3] . "<br>";
print $test[4] . "<br><br><br>";
         
SortUtil::flipsort( $test, false );

print $test[0] . "<br>";
print $test[1] . "<br>";
print $test[2] . "<br>";
print $test[3] . "<br>";
print $test[4] . "<br>";
	
?>
