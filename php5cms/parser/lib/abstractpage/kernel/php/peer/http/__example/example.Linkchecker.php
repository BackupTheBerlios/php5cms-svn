<?php

require( '../../../../../prepend.php' );

using( 'peer.http.Linkchecker' );


$ln = new Linkchecker;

if ( $url && !eregi( "^http://", $url ) )
	$url = "http://$url";

if ( $url && ( eregi( "^http://[0-9a-z.-@:]+", $url ) || !eregi( "^http://.*/.*[|><]", $url ) ) )
{
	if ( $removeQ )
		$url = ereg_replace( "\?.*$", "", $url );
   
   	$urlArray = parse_url( $url );
   
   	if ( !$urlArray[port] )
		$urlArray[port] = "80";
   
   	if ( !$urlArray[path] )
		$urlArray[path] = "/";
   
   	if ( $urlArray[query] )
		$urlArray[path] .= "?$urlArray[query]";
   
   	$uri = "http://" . $extra . $urlArray[host] . $urlArray[path];
   
   	while ( $uri != $ln->firstArd( $uri ) && $trin++ < 5 )
	{
  		$uri = $ln->firstArd( $uri );
		$steps[] = $uri;
	}
}

?>

<html>
<head>

<title>Linkchecker Example</title>

</head>

<body>

<h2>Linkchecker</h2>

<table>
	<form action="<? print basename($_SERVER['PHP_SELF']) ?>" name="submitForm">
  	<tr>
		<td colspan="2"><input name="url" size="40" value="<? $uri ? print $uri : print $url ?>"></td>
	</tr>
  	
	<tr>
		<td><input type="checkbox" name="removeQ" value="1" <? if ( $removeQ ) print "checked"; ?>></td>
		<td>Remove querystring</td>
	</tr>
  	
	<tr>
		<td><input type="checkbox" name="otherLinks" value="1" <? if ( $otherLinks ) print "checked"; ?>></td>
		<td>Other links</td>
	</tr>
  	
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="Check">&nbsp;<input type="reset" value="Reset"></td>
	</tr>
 	</form>
</table>

<?php

if ( $uri )
{
	$liste = $ln->liste( $uri );

	if ( is_array( $liste ) )
	{
		print "<br><br>\n\n";
      	print "<table border=\"1\" cellspacing=\"0\">\n";
      	print "<tr><th>Status</th><th>Description</th><th>URL</th></tr>";
      
	  	for ( $i = 0; $i < count( $liste ); $i++ )
		{
	     	if ( $i == count( $liste ) - 1 )
			{
				$printTemp = $uri;
			}
		 	else
			{
				$procent = number_format( $i * 100 / count( $liste ), 0, ".", "" );
		    	$printTemp = "$procent% - $liste[$i]";
	     	}

       		$check = $ln->check( $liste[$i] );
		 	$code  = $check[code];
		 	$check[contentType]? $contentType = ereg_replace( ";.*$", "", $check[contentType]) : $contentType = "N/A";
		 	$statCode[$code]++;
		 	$statContentType[$contentType]++;
         
		 	print "<tr>
		 		<td nowrap>$code</td>
		 		<td nowrap>$ln->getText( $code )</td>
		 		<td nowrap>";
		 
		 	if ( eregi( "^text/html", $contentType ) && ereg( "^(2|3)+[0-9]{2}", $code ) )
		    	print "<a href=\"./" . basename( $_SERVER['PHP_SELF'] ) . "?url=" . rawurlencode( $liste[$i] ) . "\">" . rawurldecode( $liste[$i] ) . "</a>";
		 	else
				print rawurldecode( $liste[$i] );
		 
		 	print "</td nowrap>
		 		</tr>\n";
      	}

      	print "</table>\n\n";
	}
	else print "<p><strong>I didn't find any links.</strong></p>\n";

	if ( count( $steps ) >= 1 )
	{
      	print "<br><br><strong>Passerede</strong><br>\n";
	  	print "<ol>";
      
	  	for ( $i = 0; $i < count( $steps ); $i++ )
			print "<li><p>$steps[$i]</li>\n";
	  
	  	print "</ol>\n";
   	}

   	if ( count( $statCode ) >= 1 )
	{
      	while ( list( $key, $value ) = each( $statCode ) )
		{
		 	$procent = ereg_replace( '(\.)?0+$', '', number_format( ( $value * 100 / count( $liste ) ), 2, ".", "" ) );
		 	$space   = "";
		 
		 	for ( $i = 0; $i < $procent / 3; $i++ )
				$space .= "&nbsp;";
         
		 	$print_statsCode .= "<tr>
		 		<td>$ln->getText( $key )</td>
		 		<td>$value</td>
		 		<td><span style=\"background-color:navy;\">$space</span>&nbsp;$procent%</td>
		 		</tr>\n";
      	}
	  
	  	print "<br><br><strong>Response codes</strong><br>\n";
	  	print "<table border=\"1\">\n";
	  	
		print "<tr><th nowrap>Status&nbsp;&nbsp;</th>
	    	<th nowrap>Number&nbsp;&nbsp;</th>
			<th nowrap>Percent</th></tr>\n";
	  
	  	print $print_statsCode;
	  	print "</table>\n\n";
   	}

   	if ( count( $statContentType ) >= 1 )
	{
      	while ( list( $key, $value ) = each( $statContentType ) )
		{
		 	$procent = ereg_replace( '(\.)?0+$', '', number_format( ( $value * 100 / count( $liste ) ), 2, ".", "" ) );
		 	$space   = "";
		 
		 	for ( $i = 0; $i < $procent / 3; $i++ )
				$space .= "&nbsp;";
         
		 	$print_statsContent .= "<tr>
		 		<td>$key</td>
		 		<td>$value</td>
		 		<td><span style=\"background-color:navy;\">$space</span>&nbsp;$procent%</td>
		 		</tr>\n";
      	}
	  
	  	print "<br><br><strong>Content-Type</strong><br>\n";
	  	print "<table border=\"1\">\n";
	  
	  	print "<tr><th nowrap>Content-Type&nbsp;&nbsp;</th>
			<th nowrap>Number&nbsp;&nbsp;</th>
			<th nowrap>Percent</th></tr>\n";
	  
	  	print $print_statsContent;
	  	print "</table>\n";
	}
}

if ( $url && !$uri )
	print "<br><br><strong>Invalid address.</strong><br>\n";

?>

</body>
</html>
