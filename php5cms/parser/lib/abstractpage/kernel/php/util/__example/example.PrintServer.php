<?php

require( '../../../../prepend.php' );

using( 'util.PrintServer' );


$pr = new PrintServer;

?>

<html>
<head>

<title>PrintServer Example</title>

</head>

<body>

<?php

echo "\n<b>LPQ Output</b>\n";

$lpqarray = $pr->lpq();

while( list( $pserver, $parray ) = @each( $lpqarray ) )
{
	while ( list( $printer, $line ) = @each( $parray ) )
	{
        printf( "%s@%s:\n", $printer, $pserver );
        printf( "    %s: %s\n", "comment",     $line["comment"] );
        printf( "    %s: %s\n", "destination", $line["destination"] );
        printf( "    %s: %s\n", "subservers",  @implode($line["subservers"], " " ) );
		printf( "    %s: %s\n", "server",      $line["server"] );

        while ( list( $index, $jobline ) = @each( $line["jobs"] ) )
		{
            printf( "    %s: %s\n",  "job",  implode( $jobline, " " ) );
            printf( "    %s: %ld\n", "time", $pr->lp_parsetime( $jobline["time"] ) );
		}

        printf( "\n" );
	}
}

echo "\n<b>Printcap Output</b>\n";

$lpcarray = $pr->lpc( "all", "printcap" );
while ( list( $index, $lpcline ) = @each( $lpcarray ) )
	echo $lpcline . "\n";

echo "\n<b>Status Output</b>\n";

$lpcarray = $pr->lp_status( "all" );
while ( list( $index, $lpcline ) = @each( $lpcarray ) )
	echo implode( $lpcline, " " ) . "\n";

echo "\n<b>Getprinters Output</b>\n";

$lpcarray = $pr->lp_getprinters();
while ( list( $index, $lpcline ) = @each( $lpcarray ) )
	echo $lpcline . "\n";

echo "\n<b>Getsubservers Output</b>\n";

$lpcarray = $pr->lp_getsubservers( "all" );
while ( list( $printer, $lpcline ) = @each( $lpcarray ) )
	echo "$printer: " . implode( $lpcline, " " ) . "\n";

?>

</body>
</html>
