<?php

require( '../../../../../prepend.php' );

using( 'image.graph.Graph' );


$ColorArray = array(
	"black"		=> array(   0,   0,   0 ),
	"maroon"	=> array( 128,   0,   0 ),
	"green"		=> array(   0, 128,   0 ),
	"olive"		=> array( 128, 128,   0 ),
	"navy"      => array(   0,   0, 128 ),
	"purple"    => array( 128,   0, 128 ),
	"teal"      => array(   0, 128, 128 ),
	"gray"      => array( 128, 128, 128 ),
	"silver"    => array( 192, 192, 192 ),
	"red"		=> array( 255,   0,   0 ),
	"lime"		=> array(   0, 255,   0 ),
	"yellow"	=> array( 255, 255,   0 ),
	"blue"		=> array(   0,   0, 255 ),
	"fuchsia"	=> array( 255,   0, 255 ),
	"aqua"		=> array(   0, 255, 255 ),
	"lrot"		=> array( 211, 167, 168 ),
	"mrot"		=> array( 140,  34,  34 ),
	"drot"		=> array(  87,  16,  12 ),
	"wueste"	=> array( 195, 195, 180 ),
	"white"		=> array( 255, 255, 255 )
);


function ColorSet( $p_name )
{
	global $ColorArray;
	return $ColorArray[$p_name];
}


$graph = new Graph;

$graph->SetImageSize( 320, 240 );
$graph->SetTitleFont( "verdana.ttf" );
$graph->SetFont( "verdana.ttf" );
$graph->SetFileFormat( "png" );
$graph->SetBackgroundColor( "white" );
$graph->SetChartBackgroundColor( "silver" );
$graph->SetMaxStringSize( 9 );
$graph->SetChartBorderColor( "black" );
$graph->SetChartType( "lines" );
$graph->SetChartTitleSize( 14 );
$graph->SetChartTitle( "Test Data" );
$graph->SetChartTitleColor( "blue" );
$graph->SetFontColor( "black" );
$graph->SetBarColor( array( "green", "red", "yellow", "blue" ) );
$graph->SetBarBorderColor( array( "black" ) );
$graph->SetLegend( array( "eins", "zwei", "drei", "vier" ) );
$graph->SetLegendPosition( 2 );
$graph->SetTitleAxisX( "" );
$graph->SetTitleAxisY( "" );
$graph->SetAxisFontSize( 8 );
$graph->SetAxisColor( "black" );
$graph->SetAxisTitleSize( 12 );
$graph->SetTickLength( 2 );
$graph->SetTickInterval( 6 );
$graph->SetGridX( 6 );
$graph->SetGridY( 0 );
$graph->SetGridColor( "white" );
$graph->SetLineThickness( 1 );
$graph->SetPointSize( 2 ); // no float, please
$graph->SetPointShape( "dots" );
$graph->SetShading( 0 );
$graph->SetNoData( "Sorry, no data." );


srand((double) microtime() * 1000000);

$a = 25;
$b = 23;
$c = 18;
$d = 0;

for ( $i = 0; $i < 25; $i++ )
{
	$a += rand( -2, 2 );
	$b += rand( -5, 5 );
	$c += rand( -2, 2 );

	if ( $a < 0 )
		$a = 0;

	if ( $b < 0 )
		$b = 0;

	if ( $c < 0 )
		$c = 0;

	$data[$i] = array( "value $i", $a, $b, $c );
}

$graph->SetDataValues( $data );

/*
$graph->SetDataValues( array(
	array( "Brother HL 2060", 55, 40, 12 ),
	array( "Epson Aculaser 2700", 38, 55, 42 ),
    array( "Epson EPL n4000+", 35, 50, 9 ),
    array( "Brother HL 2400 CE", 55, 48, 17 ),
    array( "Epson Aculaser 2700", 38, 55, 42 ),
    array( "Epson EPL n4000+", 35, 50, 9 ),
    array( "Brother HL 2400 CE", 55, 48, 17 ),
    array( "Epson Aculaser C2000", 41, 55, 18 ),
    array( "Epson EPL n4000+", 35, 50, 9 ),
    array( "Brother HL 2060", 55, 40, 12 ),
));
*/

$graph->DrawGraph();

?>