<HTML>
<HEAD>

<TITLE>NetGeo Example</TITLE>

</HEAD>

<BODY>

<?php

require( '../../../../prepend.php' );

using( 'peer.NetGeo' );


$netgeo = new NetGeo;
$ip     = $_SERVER["REMOTE_ADDR"];

if ( $netgeo->getAddressLocation( $ip, $location ) )
{
	$longitude = doubleval( $location["LONG"] );
	$latitude  = doubleval( $location["LAT"]  );
	
	echo "<CENTER><H2>Your approximate location:</H2></CENTER>\n";
	echo "<CENTER><TABLE BORDER>\n<TR>\n";
	
	if ( isset( $location["CITY"]    ) ||
		 isset( $location["STATE"]   ) ||
		 isset( $location["COUNTRY"] ) )
	{
		echo "<TD>\n<TABLE>";
		
		if ( isset( $location["CITY"] ) )
			echo "<TR>\n<TH ALIGN=\"right\">City:</TH>\n<TD>"  . htmlentities( ucwords( strtolower( $location["CITY"] ) ) )  . "</TD>\n</TR>\n";
		
		if(isset($location["STATE"]))
			echo "<TR>\n<TH ALIGN=\"right\">State:</TH>\n<TD>" . htmlentities( ucwords( strtolower( $location["STATE"] ) ) ) . "</TD>\n</TR>\n";
		
		if(isset($location["COUNTRY"]))
			echo "<TR>\n<TH ALIGN=\"right\">Country:</TH>\n<TD>" . htmlentities( strtolower( $location["COUNTRY"] ) ) . "</TD>\n</TR>\n";
		
		echo "</TABLE>\n</TD>\n";
	}
	
	echo "<TD>\n<TABLE>\n";
	echo "<TR>\n<TH ALIGN=\"right\">Longitude:</TH>\n<TD>" . ( ( $longitude >= 0.0 )? $longitude . "&deg; East"  : ( -$longitude ) . "&deg; West"  ) . "</TD>\n</TR>\n";
	echo "<TR>\n<TH ALIGN=\"right\">Latitude:</TH>\n<TD>"  . ( ( $latitude  >= 0.0 )? $latitude  . "&deg; North" : ( -$latitude  ) . "&deg; South" ) . "</TD>\n</TR>\n";
	echo "</TABLE>\n</TD>\n</TR>\n";
	echo "</TABLE></CENTER>\n";
	
	$places = array(
		"Equator line"   =>array( $longitude, 0.0 ),
		"Greenwich line" =>array( 0.0, $latitude ),
		"North Pole"     =>array( 0.0,  90.0 ),
		"South Pole"     =>array( 0.0, -90.0 )
	);
	
	echo "<CENTER><H2>Distance to reference places in the world</H2></CENTER>\n";
	echo "<CENTER><TABLE BORDER>\n<TR>\n<TH>Place</TH>\n<TH>Longitude</TH>\n<TH>Latitude</TH>\n<TH>Distance</TH>\n</TR>\n";
	
	for ( Reset( $places ), $place = 0; $place < count( $places ); Next( $places ), $place++ )
	{
		$name = Key($places);
		$long = $places[$name][0];
		$lat  = $places[$name][1];
		
		echo "<TR>\n";
		echo "<TH>$name</TH>\n";
		echo "<TD ALIGN=\"right\"><TT>" . ( ( $long > 0 )? $long . "&deg; East"  : ( ( $long < 0 )? ( -$long ) . "&deg; West"  : "0&deg;" ) ) . "</TT></TD>\n";
		echo "<TD ALIGN=\"right\"><TT>" . ( ( $lat  > 0 )? $lat  . "&deg; North" : ( ( $lat  < 0 )? ( -$lat  ) . "&deg; South" : "0&deg;" ) ) . "</TT></TD>\n";
		echo "<TD ALIGN=\"right\"><TT>" . intval( $netgeo->calculateDistance( $longitude, $latitude, $long, $lat ) ) . " Km</TT></TD>\n";
		echo "</TR>\n";
	}
	
	echo "</TABLE></CENTER>\n";
}
else
{
	echo "<CENTER><H2>Sorry, could not determine your network location!</H2></CENTER>\n";
	echo "<CENTER><H3>Error: " . $netgeo->error . ".</H3></CENTER>\n";
}

?>

</BODY>
</HTML>
