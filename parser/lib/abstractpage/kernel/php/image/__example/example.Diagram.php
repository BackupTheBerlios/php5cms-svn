<?php

require( '../../../../prepend.php' );

using( 'image.Diagram' );


$g = new Diagram;

$arr = array(
	'this' => array(
		'is' => array(
			'just' => array(
				'a'
			),
			'test'
		),
		'to' => array(
			'test' => array(
				'my',
				'new' => array(
					'class',
					'called'
				),
				'diagram'
			)
		),
		'graph'
	)
);
  
$g->setRectangleBorderColor( 124, 128, 239 );
$g->setRectangleBackgroundColor( 194, 194, 239 );
$g->setFontColor( 255, 255, 255 );
$g->setBorderWidth( 0 );
$g->setData( $arr );
$g->draw();

?>
