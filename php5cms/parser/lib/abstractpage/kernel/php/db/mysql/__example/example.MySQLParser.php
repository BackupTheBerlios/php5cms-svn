<?php

require( '../../../../../prepend.php' );

using( 'db.mysql.MySQLParser' );


$contents = array(
	"item1 OR item2 AND (item3 AND item4 NOT (item5 OR item6))",
	"NOT item1 OR item2 AND (item3 OR item4)",
	"I wanna doll AND AND AND I wanna puppy as well OR OR OR maybe I want a kitten",
	"Our mission statement is to maximize on our profits and or provide added value to our shareholders",
	"Boolean logic uses the keywords AND OR NOT"
);

$parser = new MySQLParser();

foreach ( $contents as $line )
{
	echo "$line\n";
	$res = $parser->parse( $parser->atomize( $line ) );
	
	if ( $parser->error )
	{
		foreach ( $parser->log as $msg )
			echo "\t-$msg\n";
	}
	
	echo "$res\n\n";
}

?>
