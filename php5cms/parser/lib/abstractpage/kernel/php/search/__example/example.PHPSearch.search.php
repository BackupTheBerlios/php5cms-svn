<?php

require( '../../../../prepend.php' );

using( 'search.PHPSearch' );


$search = new PHPSearch;

// You only need to edit this line to point to your
// index file (if in windows make sure you use double slashes (\\))
$index_file = "search_index.dat";


function print_search_form( $query )
{
	echo "<form action=\"example.PHPSearch.search.php\" method=\"post\">";
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
      	"<td><form action=\"example.PHPSearch.indexer.php\" method=\"post\">" .
      	"<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td>" .
      	"<input type=\"hidden\" name=\"action\" value=\"index\"><input type=\"radio\" name=\"meta\" value=\"on\">Index From Meta Tags &nbsp;<br>" .
      	"<input type=\"radio\" name=\"meta\" value=\"off\">Full Index</td><td>" .
      	"<input type=\"submit\" value=\"Index Site\">" .
      	"</td></tr></table></form></td></tr>" .
      	"<tr><td align=\"left\" valign=\"middle\"><b>Clear Index:&nbsp;</b></td>" .
      	"<td align=\"right\" valign=\"middle\"><form action=\"example.PHPSearch.indexer.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"clear\"><input type=\"submit\" value=\"Clear Index\"></form></td></tr>" .
      	"<tr><td align=\"left\" valign=\"middle\"><b>View Index:&nbsp;</b></td>" .
      	"<td align=\"right\" valign=\"middle\"><form action=\"example.PHPSearch.indexer.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"view_index\"><input type=\"submit\" value=\"View Index\"></form></td></tr></table>"
	);
}


if ( $query == "" )
{
	print_header();
	print_search_form( "" );
	print_footer();
}
else if ( $print_form == 1 )
{
  	print_search_form( "" );
}
else
{
	$query = strip_tags( $query );
	$query = $search->removeChars( $query );
	$query = strtolower( $query );
	$query = trim( $query );
	$result_arr = $search->search( $query, $index_file );
	$result_count = sizeof( $result_arr ); 

	if ( $result_count < 1 )
	{ 
  		print_header();  
  		echo "<b>No results were found. Please search again</b>\n";
  		print_search_form( $query );
  		print_footer();
 	}
 	else
	{
  		print_header();
  
  		echo( "\n<b>Search Results</b>\n" .
       		"<i><br>Results: 1 - $result_count</i>\n".
       		"<ul>\n"
		);
  
  		foreach ( $result_arr as $result )
		{
   			$meta_tags = @get_meta_tags( $result );
   
   			if ( $meta_tags['title'] == "" )
				echo( "<li><b><a href=\"$result\">$result</a></b>\n" );
   			else
				echo( "<li><b><a href=\"$result\">$meta_tags[title]</a></b> - <i>$result</i>\n" );
   
   			if ($meta_tags['description'] == "" )
				echo("<br>...\n<br>&nbsp;\n");
   			else
				echo( "<br>$meta_tags[description]\n<br>&nbsp;\n" );
  		}
  
  		echo( "</ul>" );
  		print_search_form( $query );
  		print_footer();
 	}
}

?>

</body>
</html>
