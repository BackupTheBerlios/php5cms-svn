<html>
<head>
	
<title>Sysmon Example</title>

</head>

<body>

<h2>System Resource Monitor</h2>

The system resource monitor is designed for WML browsers (e.g. phones).<br><br>

<?php

require( '../../../../prepend.php' );

using( 'sys.System' );

$sys   = new System;
$ports = $sys->checkPorts();
$keys  = array_keys( $ports );
$len   = count( $keys );


echo "<u>ports:</u><br><br>\n";

for( $i = 0; $i < $len; $i++ )
{
	if( 1 == $ports[$keys[$i]] )
		echo( "port " . $keys[ $i] . ": open<br>\n" );
	else
		echo( "port " . $keys[ $i] . ": not open<br>\n" );
}

echo "<br><br>\n";

$proc = $sys->checkUptime();

echo "<u>uptime:</u><br><br>\n";

echo "time: " 		   . $proc['time']  . "<br>\n";
echo "up: " 		   . $proc['days']  . " days, " . $proc['hours'] . "<br>\n";
echo "users: " 		   . $proc['users'] . "<br>\n";
echo "load avg (1): "  . $proc['1min']  . "<br>\n";
echo "load avg (5): "  . $proc['5min']  . "<br>\n";
echo "load avg (15): " . $proc['15min'] . "<br>\n";

echo "<br><br>\n";

$mem = $sys->checkMemory();

echo "<u>memory:</u><br><br>\n";

echo "total: "   . $mem['total']   . "<br>\n";
echo "used: "    . $mem['used']    . "<br>\n";
echo "free: "    . $mem['free']    . "<br>\n";
echo "shared: "  . $mem['shared']  . "<br>\n";
echo "buffers: " . $mem['buffers'] . "<br>\n";
echo "cached: "  . $mem[ 'cached'] . "<br>\n";

?>

</body>
</html>
