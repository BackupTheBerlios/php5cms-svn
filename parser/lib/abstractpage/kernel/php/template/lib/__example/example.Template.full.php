<?php

require( '../../../../../prepend.php' );

using( 'template.lib.Template' );


$templater = new Template( 'tpl/full.tpl' );
$items = array( 'tag1' => 'value1', 'tag2' => 'value2' ); 
$templater->set( $items );
$templater->set( 'tag3','value3' );
$templater->setFromFile( 'tag4','data/full.data' );
$result = $templater->parse();

print( $result );

?>
