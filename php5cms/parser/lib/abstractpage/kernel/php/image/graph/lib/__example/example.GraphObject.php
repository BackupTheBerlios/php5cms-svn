<?php

require( '../../../../../../prepend.php' );

using( 'image.graph.lib.GraphObject' );
using( 'image.graph.lib.GraphDot' );
using( 'image.graph.lib.GraphLine' );


$g = new GraphObject();
$g->filename = "0.gif";
$g->font = 3;
$g->x_dashed_line = 1;
$g->show_y_detail = 1;
$g->y_axis_label  = "Graph Test";
$g->x_axis_label  = "OverTime of the Week";
$g->data = array( array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ) );
$g->x_labels = array( "adfadf", "dfdaf", "dsf", "adfa", "d", "e", "f", "g", "h", "i" );
$g->legend = array( "aaaa" );
$g->change_color( bd, 200,   0,   0 );
$g->change_color( bg, 222, 220, 220 );
$g->change_color( 1, 0, 0,   0 );
$g->change_color( 2, 0, 0, 255 );
$g->draw();

echo "<img src=$fileName><br>";


$gl = new GraphLine( 600, 300 );
$gl->filename = "1.gif";
$gl->font = 3;
$gl->show_y_detail = 1;
$gl->show_x_detail = 1;
$gl->y_axis_label  = "Graph Test";
$gl->x_axis_label  = "OverTime of the Week";

$gl->data = array(
	array(  1,  2,  3,  4, 35,  6,  7,  8,  9, 10 ),
	array(  2,  4,  6,  8, 10, 12, 14, 16, 18 ),
	array( 16, 14, 13, 12,  9,  6,  3 )
);

$gl->data_x = array(
	array( 2, 12, 23, 42, 46, 57, 58, 65, 67, 78 ),
	array( 4,  9, 12, 19, 24, 32, 38, 46, 58 ),
	array( 3, 12, 23, 32, 49, 56, 63 )
);

$gl->x_labels = array( "one", "two", "three" );
$gl->change_color( 1, 0, 0,   0 );
$gl->change_color( 2, 0, 0, 255 );
$gl->draw();

echo "<img src=$fileName><br>";


$gd = new GraphDot( 500, 300 ); 
$gd->filename = "2.gif"; 
$gd->data = array( 99, 5, 0 ); 
$gd->x_labels = array( "MS IE", "Netscape", "Others" ); 
$gd->x_centre = 150; 
$gd->show_y_detail = 1;
$gd->title_label   = "Browsers"; 
$gd->change_color( bd, 255, 255, 255 );
$gd->change_color( tt,   0,   0,   0 );
$gd->change_color( bg, 240, 240, 250 );
$gd->init(); 
$gd->close();
 
echo "<img src=$fileName><br>";

?>
