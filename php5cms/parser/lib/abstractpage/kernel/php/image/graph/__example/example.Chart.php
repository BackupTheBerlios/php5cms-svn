<?php

require( '../../../../../prepend.php' );

using( 'image.graph.Chart' );


$mygraph = new Chart( 600 );
$mygraph->setTitle( 'Regional Sales', 'Jan - Jun 2002' );
$mygraph->setXLabels( "Jan,Feb,Mar,Apr,May,Jun" );
$mygraph->addDataSeries( 'C', CHART_COLS_STACKED, "25,30,35,40,30,35", "South" );
$mygraph->addDataSeries( 'C', 0, "65,70,80,90,75,48", "North"  );
$mygraph->addDataSeries( 'C', 0, "12,18,25,20,22,30", "West"   );
$mygraph->addDataSeries( 'C', 0, "50,60,75,80,60,75", "East"   );
$mygraph->addDataSeries( 'L', 3, "30,45,50,55,52,60", "Europe" );
$mygraph->setBgColor( 0, 0, 0, 1 );     // transparent background
$mygraph->setChartBgColor( 0, 0, 0, 1); // as background
$mygraph->setXAxis( "Month", 1 );
$mygraph->setYAxis( "Sales (£000)", 0, 250, 50, 1 );
$mygraph->drawGraph();

$mygraph = new Chart( 600 );
$mygraph->setTitle( 'Regional Sales','Jan - Jun 2002' );
$mygraph->setXLabels( "Jan,Feb,Mar,Apr,May,Jun" );
$mygraph->addDataSeries( 'L', CHART_LINE_MARK_X,       "25,30,35,40,30,35", "South"  );
$mygraph->addDataSeries( 'L', CHART_LINE_MARK_CIRCLE,  "65,70,80,90,75,48", "North"  );
$mygraph->addDataSeries( 'L', CHART_LINE_MARK_SQUARE,  "12,18,25,20,22,30", "West"   );
$mygraph->addDataSeries( 'L', CHART_LINE_MARK_DIAMOND, "50,60,75,80,60,75", "East"   );
$mygraph->addDataSeries( 'L', CHART_LINE_MARK_NONE,    "30,45,50,55,52,60", "Europe" );
$mygraph->setBgColor( 255, 255, 255, 1 ); // transparent
$mygraph->setXAxis( "Month", 1 );
$mygraph->setYAxis( "Sales (£000)", 0, 100, 10, 0 );
$mygraph->drawGraph();

$mygraph = new Chart( 600 );
$mygraph->setTitle( 'Regional Sales','Jan - Jun 2002' );
$mygraph->addDataSeries( 'P', CHART_PIE_CHART_PCENT + CHART_PIE_LEGEND_VALUE, "25,30,35,40,30,35", "South"  );
$mygraph->addDataSeries( 'P', CHART_PIE_CHART_PCENT + CHART_PIE_LEGEND_VALUE, "65,70,80,90,75,48", "North"  );
$mygraph->addDataSeries( 'P', CHART_PIE_CHART_PCENT + CHART_PIE_LEGEND_VALUE, "12,18,25,20,22,30", "West"   );
$mygraph->addDataSeries( 'P', CHART_PIE_CHART_PCENT + CHART_PIE_LEGEND_VALUE, "50,60,75,80,60,75", "East"   );
$mygraph->addDataSeries( 'P', CHART_PIE_CHART_PCENT + CHART_PIE_LEGEND_VALUE, "30,45,50,55,52,60", "Europe" );
$mygraph->drawGraph();

?>
