<html>
<head>

<meta name="index" content="no">

<title>SiteIndexer Example</title>

</head>

<body>

<?php

require( '../../../../prepend.php' );

using( 'search.PHPSearch' );
#error_reporting(E_ERROR);
extract($_REQUEST);


$search         = new PHPSearch;
$document_root	= "/usr/devel/var/src/web/scripts/cms/phpcms-1.2.0/demo";
$server_root 	= "http://192.168.0.101/scripts/cms/phpcms-1.2.0/demo";
$index_file 	= "/usr/devel/var/src/web/scripts/libs/abstractpage/kernel/php/search/__example/search_index.dat";
$nonword_file 	= "/usr/devel/var/src/web/scripts/libs/abstractpage/kernel/php/search/__example/nonwords.txt";


function print_search_form( $query )
{
	echo "<form action=\"search.php\" method=\"post\">";
	echo "<br><b>Search for:</b>";
	echo "<br><input type=\"text\" name=\"query\" value=\"$query\">";
	echo "<br><input type=\"submit\" value=\"Search\">";
	echo "</form>";
}

function print_header()
{
	echo( "<html>\n<head>\n<title>PHPSearch\n</title><meta name=\"index\" content=\"no\"\n</head>\n<body>\n");
}

function print_footer()
{
	echo( "<br><hr><br><br><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">" .
      	"<tr><td colspan=\"2\" align=\"center\"><b>Site Indexer Options:</b><br>&nbsp;</td>" .
      	"</tr><tr><td align=\"left\" valign=\"middle\"><b>Index Site:&nbsp;</b></td>" .
      	"<td><form action=\"indexer.php\" method=\"post\">" .
      	"<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td>" .
      	"<input type=\"hidden\" name=\"action\" value=\"index\"><input type=\"radio\" name=\"meta\" value=\"on\">Index From Meta Tags &nbsp;<br>" .
      	"<input type=\"radio\" name=\"meta\" value=\"off\">Full Index</td><td>" .
      	"<input type=\"submit\" value=\"Index Site\">" .
      	"</td></tr></table></form></td></tr>" .
      	"<tr><td align=\"left\" valign=\"middle\"><b>Clear Index:&nbsp;</b></td>" .
      	"<td align=\"right\" valign=\"middle\"><form action=\"indexer.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"clear\"><input type=\"submit\" value=\"Clear Index\"></form></td></tr>" .
      	"<tr><td align=\"left\" valign=\"middle\"><b>View Index:&nbsp;</b></td>" .
      	"<td align=\"right\" valign=\"middle\"><form action=\"indexer.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"view_index\"><input type=\"submit\" value=\"View Index\"></form></td></tr></table>"
	);
}


// if statements to determine the actions.
if ( $action == "clear" )
	$search->clearIndex( $index_file );
elseif ( $action == "index" )
	$search->indexSite( $document_root, $server_root, $index_file, $nonword_file, $meta );
elseif ( $action == "view_index" )
{
	// shows the index file in a table
 	$index_f = @file( $index_file );
 
 	if ( !$index_f )
		echo "<b>Could not open index file.</b><br>The file may be empty, you may have specified the incorrect path or the the file permissions are incorrect.";
 	else
	{
  		echo("<b>Your Index File:<br><br></b>" .
      		"<table border=\"1\" bordercolor=\"#000000\" bgcolor=\"#ffffff\" cellspacing=\"0\" cellpadding=\"2\"><tr><td align=\"center\"><b>#</b></td><td align=\"center\"><b>URL</b></td><td align=\"center\"><b>Title</b></td><td align=\"center\"><b>Keywords</b></td>" );
  
  		foreach ( $index_f as $key => $line )
		{
   			$key = $key + 1;
   			echo "<tr><td NOWRAP bgcolor=\"999999\" align=\"center\">$key</td>";
   			$key_words = explode( "|", $line );
   
   			foreach ( $key_words as $key => $word )
			{
    			if ( $key == "0" )
					echo "<td NOWRAP bgcolor=\"#CCCCCC\" align=\"center\"><a href=\"$word\">$word</a></td>";
    			else
					echo "<td NOWRAP align=\"center\">$word</td>";
   			}
   
   			echo "</tr>";
  		}
  
  		echo "</table>";
 	}
}

?>

</body>
</html>
