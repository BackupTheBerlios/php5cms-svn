<?php

// in this example a simple loop produces 10 instances of test5row.tpl, each parsed
// appending into TEST5_ALLROWS. Then test5.tpl is parsed, replacing TEST5_ALLROWS
// with the full set of rows.

require( '../../../../prepend.php' );

using( 'template.ConditionalParser' );


$test5 = new ConditionalParser( "tpl/" );
$test5->addtemplate( "template5", "test5.tpl" );
$test5->addtemplate( "TEST5_ROW", "test5row.tpl" ); // add row template
 
// simple example loop
for ( $i = 0; $i < 10; $i++ )
{
	$test5->define( "TEST5_VAR1", $i );					// simple definition making use of loop
	$test5->parse( "TEST5_ALLROWS", "TEST5_ROW", 1 );	// additional parameter 1 for recursion
}

$test5->parse( "test5out", "template5" ); 
$output = $test5->output( "test5out" );					// example, make engine output to PHP variable

echo $output; 											// output it
echo "<!-- " . strlen( $output ) . " chars -->";		// output it's length as an html comment

?>
