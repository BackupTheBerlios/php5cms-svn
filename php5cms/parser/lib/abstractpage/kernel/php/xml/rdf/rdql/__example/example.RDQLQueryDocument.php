<html>
<head>

<title>RDQL Example</title>
  
</head>

<body>

<?php

require( '../../../../../../prepend.php' );

using( 'xml.rdf.rdql.RDQLQueryDocument' );


print("<h1>RDQL test</h1><br/>\n");

$query[0] = 'SELECT ?z
FROM <people.rdf>
WHERE (?x,<dt:members>,?y),(?y,?w,?z)
AND ?z<>"http://www.w3.org/1999/02/22-rdf-syntax-ns#Bag" && ?x=="http://foo.org/team"
USING dt for <http://foo.org#>, rdf for <http://www.w3.org/1999/02/22-rdf-syntax-ns#>';        
$query[1]='SELECT ?sal, ?t, ?x 
FROM	<http://ilrt.org/discovery/2000/11/rss-query/jobs-rss.rdf>,
	<http://ilrt.org/discovery/2000/11/rss-query/jobs.rss>
WHERE	(?x, <job:advertises>, ?y),
	(?y, <job:salary>, ?sal),
	(?y, <job:title>, ?t)
AND	(?sal >= 100000)
USING job for <http://ilrt.org/discovery/2000/11/rss-query/jobvocab.rdf#>';

foreach ( $query as $a_query )
{
	$head = false;
	$rows = RDQLQueryDocument::rdql_query_url( $a_query );
	
	print( "<table border='1' width='80%'>" );
	print( "<tr><td bgcolor='#aaaacc'>Query:</td></tr>" );

	$a_query = str_replace( "<", "&lt;", $a_query );
	$a_query = str_replace( ">", "&gt;", $a_query );

	print( "<tr><td bgcolor='#ccccee'><pre>$a_query</pre></td></tr>" );
	print( "</table>" );
	print( "<b>Result:</b>" );
	print( "<table border='1'width='80%'>" );

	foreach ( $rows as $row )
	{
    	if ( !$head )
		{
      		print("<tr>");
      
	  		foreach ( array_keys($row) as $k )
        		print( "<td bgcolor='#bbbbbb'><b>$k</b></td>" );
      
      		print("</tr>");
      		$head = true; 
    	}
    
		print( "<tr>" );
    
		foreach ( $row as $key=>$val )
      		print( "<td bgcolor='#dddddd'>$val</td>" );
    
    	print("</tr>");
  	}

	print("</table>");
	print("<br/>");
}

?>

</body>
</html>
