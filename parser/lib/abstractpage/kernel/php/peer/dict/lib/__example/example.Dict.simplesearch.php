<?php 

require( '../../../../../../prepend.php' );

using( 'peer.dict.lib.DictQuery' );
using( 'peer.dict.lib.DictServerInfo' );


$start = time(); 

?>

<html>
<head>

<title>Simple Form to Query dict.org</title>

</head>

<body>

Search the dict.org server

<p>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<input type="text" name="query_term" size="60" 
<?php
if ( $query_term )
	echo "value=\"".$query_term."\"";
?>
><br>
<input type="hidden" name="database" value="*">
<input type="hidden" name="strategy" value="exact">
<input type="submit" name="submit" value=" Search "> 
<input type="reset" name="reset" value=" Clear form input ">
</form>

<hr>

<?php

// check if element is in the array
function inArray( $element, $arr )
{
	// figure out version
	list( $major, $minor, $release ) = explode( ".", phpversion() );
	
	if ( ( $major == 3 && $relesase >= 12 ) || $major == 4 )
	{
		return in_array($element, $arr);
	}
	else
	{
		// assumes that we want to compare element value
		while ( list( $key, $val ) = each( $arr ) )
		{
			if ( $val == $element )
				return true;
		}
		
		return false;
	}
}

// remove duplicates from array
// and eliminate the patterns in $nolinks
function cleanArray( $arr )
{
	$nolinks = "rfc:";
	$out = array();
	
	for ( $i = 0; $i < count( $arr ); $i++ )
	{
		if ( !inArray( $arr[$i], $out ) && !ereg( $nolinks, $arr[$i] ) ) 
			$out[] = $arr[$i];
	}
	
	return $out;
}

// make the links to other words in the description
function mkLinks( $str, $db )
{
	// modified the regexes to fix the bug reported by <davor@croart.com>
	$patt = "\{+([^{}]+)\}+";
	$regex = "<b>\\1</b>";
	$out = ereg_replace( $patt, $regex, $str );
	$patt = "/\{+([^{}]+)\}+/";
	preg_match_all( $patt, $str, &$reg );
	$link = $reg[1];
	
	// clean up array
	$link = cleanArray( $link );
	
	if ( count( $link ) > 0 )
		$out .= "<i>See also:</i>\n";
	
	for ( $i = 0; $i < count( $link ); $i++ )
	{
		// added the line below to fix a second bug reported by <davor@croart.com>
		$link[$i] = ereg_replace( "[[:space:]]+", " ", $link[$i] );
		
		// observed myself another bug with references to URLs - JMC
		// check if it is a HTTP URL or a crossrefence
		$protocols = "(http|https|ftp|telnet|gopher)://|(mailto|news):";
		
		if ( ereg( $protocols, $link[$i] ) )
		{
			// parse the link and mark it using <>
			$prot1 = "^((mailto|news):.+)$";
			$prot2 = "(http|https|ftp|telnet|gopher)://";
			$prot2 = "^(.*) *\((" . $prot2 . ".+)\)$";
			
			if ( ereg( $prot1, $link[$i], &$regurl ) )
			{
				list( $tmp, $url ) = $regurl;
				$desc = $url;
			}
			else if ( ereg( $prot2, $link[$i], &$regurl ) )
			{
				list( $tmp, $desc, $url ) = $regurl;
				
				if ( $desc == "" )
					$desc = $url;
			}
			
			$out .= "&lt;<a href=\"".chop($url)."\" target=\"_blank\">";
			$out .= chop( $desc ) . "</a>&gt; ";
		}
		else
		{
			$out .= "[<a href=\"".$_SERVER['PHP_SELF']."?query_term=";
			$out .= urlencode( $link[$i] ) . "&database=" . urlencode( $db );
			$out .= "&strategy=exact\">".$link[$i]."</a>] ";
			
			if ( ( $i % 5 ) == 0 && $i > 0 )
				$out .= "\n";
		}
	}

	$out .= "\n";
	return $out;
}

function parr( $arr )
{
	echo "<ul>";

	while ( list( $k, $v ) = each( $arr ) )
	{
		if ( gettype( $v ) == "array" )
		{
			echo "<ul>";
			echo "* $k , new array*<br>";
			parr( $v );
			echo "</ul>";
		}
		else
		{
			echo "$k = $v<br>";
		}
	}
	
	echo "</ul>";
}

// perform a query to the server
function doQuery( $str, $db, $strategy )
{
	$query = new DictQuery();
	
	if ( $strategy == "exact" )
	{
		$query->define( $str, $db );
	}
	else
	{
		$query->match( $str, $strategy, $db );
	}
	
	$n = $query->get( "numres" );
	$res = $query->get( "result" );
	$out = "<b>Found " . count( $res );
	$out .= ( count( $res ) == 1 )? " hit" : " hits";
	$out .= "</b> - <i>Term: " . $str . ", Database: " . $db . ", Strategy: " . $strategy;
	$out .= "</i><br>\n<dl>\n";
	
	for ( $i = 0; $i < count( $res ); $i++ )
	{
		$entry = $res[$i];
		
		if ( $strategy == "exact" )
		{		
			$out .= "<dt>[" . ( $i + 1 ) . "] : " . $entry["dbname"] . "</dt>\n";
			$out .= "<dd><pre>" . mkLinks( $entry["definition"], $db ) . "</pre></dd>\n";
		}
		else
		{
			$match = explode( " ", chop( $entry ) );
			$match_term = str_replace( "\"", "", $match[1] );
			$out .= "<dt>[" . ( $i + 1 ) . "] : ";
			$out .= "<A HREF=\"".$_SERVER['PHP_SELF']."?query_term=".urlencode($match_term);
			$out .= "&database=" . urlencode( $db );
			$out .= "&strategy=exact\">";
			$out .= $match_term . "</a></dt>\n";
			$out .= "<dd> Database: " . $match[0] . "</dd>";
		}
	}
	
	$out .= "</dl>";
	return $out;
}

if ( $query_term )
{
	$out = doQuery( $query_term, $database, $strategy );
	echo $out."\n<hr>\n";
}

?>

Last accessed:

<?php

$end = time();
echo date( "Y/m/d H:i:s", time() );
echo " [Total processing time: " . ( $end - $start ) . " seconds]"; 

?>

</body>
</html>
