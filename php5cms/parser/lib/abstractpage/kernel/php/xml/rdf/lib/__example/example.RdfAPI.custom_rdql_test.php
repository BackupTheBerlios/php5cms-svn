<?php

/*
 * This is an online demo of RAP's RDQL engine.
 * Input an RDQL query string and the engine will query the document
 * specified in the source clause.
 */

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<HTML>
<HEAD>

<TITLE>RDQL-Engine</TITLE>

<META content="text/html; charset=iso-8859-1" http-equiv=Content-Type>

</HEAD>

<BODY>

<TABLE border=0>
  <TBODY>
  <TR>
    <TD align=left vAlign=top>
      <H1>RDQL-Engine Online Demo</H1><BR>
      <P>
	  
<?php

// Test if the form is submitted or the query_string is too long
if ( !isset( $_POST['submit'] ) || ( strlen( $_POST['query_string'] ) > 1000 ) )
{
	// Show error message if the rdf is too long
	if ( ( isset( $_POST['submit'] ) && ( strlen( $_POST['query_string'] ) > 1000 ) ) )
	{
		echo "<center><a href='" .$HTTP_SERVER_VARS['PHP_SELF'] ."'><h2>Go back to input form.</h2></a></center>";
		echo "<center><p class='rdql_comment'>We're sorry, but your RDQL query is bigger than the allowed size of 1000 characters</p></center>";
   	}

?>

<form method="post" action="<?php echo $HTTP_SERVER_VARS['PHP_SELF']; ?>">
      Paste an RDQL query string into the text field below.
      In the FROM clause you can indicate an URL or a path for local RDF document to be queried.
	  </P>
      <H3>Please paste your RDQL query here:</H3>
      <P><TEXTAREA cols=80 name=query_string rows=15>
/* Find the name of the creator of <http://www.w3.org/Home/Lassila> */
/* ---------------------------------------------------------------- */

SELECT ?Name
/* --- Input file --- */
FROM <example1.rdf>
WHERE (?x, <desc:Creator>, ?z)
      (?z, <ex:Name>, ?Name)
AND ?x eq <http://www.w3.org/Home/Lassila>
USING desc FOR <http://description.org/schema/>
      ex FOR <http://example.org/stuff/1.0/>

         </TEXTAREA> <BR>
	  </P>
      <H3>Please choose the output format:</H3>
          <TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
            <TBODY>
              <TR>
                <TD> <DIV align=center>
                    <INPUT id="show_input" name="show_input"
            type=checkbox value=1>
                  </DIV></TD>
                <TD><STRONG>Show the source model</STRONG> (only if it contains
                  fewer than 100 statements)</TD>
              </TR>
              <TR>
                <TD>&nbsp;</TD>
                <TD>&nbsp;</TD>
              </TR>
            </TBODY>
          </TABLE>
      <P><INPUT name=submit type=submit value="submit me!">
      </P></FORM><BR>
<?php
} 
else 
{
	// Process the query if submitted
	require( '../../../../../../prepend.php' );

	using( 'xml.rdf.lib.RdfAPI' );
	using( 'xml.rdf.lib.util.RdfMemoryModel' );
	
	
	echo "<a href='" .$HTTP_SERVER_VARS['PHP_SELF'] ."'>
	<h2>Go back to input form.</h2></a>";

	if ( isset( $_POST['query_string'] ) )
	{
		$queryString = stripslashes( $_POST['query_string'] );

		// Parse the query
		$parser = new RdqlParser;
		$parsed = &$parser->parseQuery( $queryString );

		// If more than one source file provided show an error message
		if ( count( $parsed['sources'] ) > 1 )
			echo "<center><p class='rdql_comment'>We're sorry, but this Online Demo allows you to query only one document</p></center>";

		// Load the input model into memory
		$memModel = new RdfMemoryModel();
		$memModel->load( $parsed['sources'][0] );

		// Process the query
		$engine = new RdqlMemEngine;
		$queryResult = $engine->queryModel( $memModel, $parsed, true );
   		
		if ( PEAR::isError( $queryResult ) )
			die( $queryResult->getMessage() );
			
		// Show the query string
		echo "<br><h3>Your query: </h3>";
		echo "<table width='100%' bgcolor=#e7e7ef><tr><td>";
		echo "<p bgcolor='34556'><code>" . nl2br( htmlspecialchars( stripslashes( $_POST['query_string'] ) ) ) . "</code></p>";
		echo "</td></tr></table><br>";
   
		// Show query result
		echo "<br><h3>Query result: </h3>";
		$engine->writeQueryResultAsHtmlTable( $queryResult );
   
		// Show the input model if option chosen
		if ( isset( $_POST['show_input'] ) && $_POST['show_input'] == "1" )
		{
			echo "<br><br><h3>Source model: </h3>";
			$memModel->writeAsHtmlTable();
		}
	}
}

echo "<br><br><br>";
?>

	</TR>
	</TBODY>
</TABLE>

</BODY>
</HTML>
