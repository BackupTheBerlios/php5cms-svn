<?php

require( '../../../../prepend.php' );

using( 'template.ConditionalParser' );


$test4 = new ConditionalParser( "tpl/" );		// relative path
$test4->addtemplate( "template", "test.tpl" );	// add one or more templates (array)

// define variables
$test4->define( "TEST4_VAR1", "1" );
$test4->define( "TEST4_VAR2", "2" );
$test4->define( "TEST4_VAR3", "3" );
$test4->define( "TEST4_VAR4", "4" );

$test4->parse( "testout", "template" );			// parse template4 into variable test4out
echo $test4->output( "testout" );				// output the result
 
?>
