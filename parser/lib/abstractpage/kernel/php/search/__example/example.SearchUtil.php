<?php

require( '../../../../prepend.php' );

using( 'io.FolderUtil' );
using( 'search.SearchUtil' );


// List of directories to be included in the search.
// Usage: "path" => "url" (path to directory and URL which corresponds to directory).
// Remember to include the trailing / on the URL.
$directories = array(
	"/usr/devel/var/src/web/scripts/cms/phpcms-1.2.0/demo" => "http://192.168.0.101/scripts/cms/phpcms-1.2.0/demo"
);

?>

<html>
<head>

<title>SearchUtil Example</title>

</head>

<body>

<form action="example.SearchUtil.php">
	<input type="text"   name="keyword">
    <input type="submit" value="Search">
    <input type="reset"  value="New Search">
</form>

<br>

<?php

if ( isset( $keyword ) )
{
	$keywords = explode( " ", $keyword );
	$pages    = array();

	while ( list( $key, $val ) = each( $directories ) )
	{
		$directory = $key;
		chdir( $directory ) || die( "Directory $directory Not found" );
			
		$filenames = FolderUtil::getFilenames( $directory );
		$found     = SearchUtil::keywordCheck( $filenames, $keywords );

		// add any pages with keywords in current directory to array
		for ( $i = 0; $i < count( $found ); $i++ )
		{
			$add = "$val$found[$i]";
			$pages[count( $pages )] = $add;
		}
	}

	// output results
	echo "<font color=Blue>" . count( $pages ) . " pages matching your query were found</font>";
	echo "<hr>";

	for ( $i = 0; $i < count( $pages ); $i++ )
	echo "<a href=\"$pages[$i]\">$pages[$i]</a><br>";
}

?>

</body>
</html>