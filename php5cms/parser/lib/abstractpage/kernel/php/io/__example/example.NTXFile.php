<?php

require( '../../../../prepend.php' );

using( 'io.NTXFile' );


// this function opens the dbase file
function myopen( $dbname )
{
	$path  = "/path/to/dbase/file/";
	$fname = $path . strtoupper( $dbname );
	$res   = dbase_open( $fname, 0 );
	
	if ( !$res )
		die( "Cannot open $dbname" );
        
	return $res;
}
// this function dumps the dbase record
function dumprec( $rec )
{
	echo "<p>Info about record: <u>" . $rec["FIRSTNAME"] . " " . ["LASTNAME"] . "</u></p>\n";
	reset( $rec );
	echo "<table border=1>";
	$cnt = 0;
	
	while ( list( $key, $val ) = each( $rec ) )
	{
		// hide password from user
		$typ = $key == "PASSWORD"? "PASSWORD" : "TEXT";
		$val = $key == "PASSWORD"? "XXXXXX"   : $val;
		
		echo "<tr><td>$key</td><td>:</td><td><input type=$typ value=\"$val\"></td></tr>\n";
		
		if ( $cnt++ > 15 )
			break;
	}
	
	echo "</table>";
}


// set the password
// usually we get this from a form
$password = "secret";

// open the dbase file and the index file
$db  = myopen( "users.dbf" );
$ntx = new NTXFile( "userpass.ntx" );

// the following is as we would do in clipper
$res = $ntx->Seek( $password );

if ( $res === false )
{
    echo "<p>User Not Found</p>";
}
else
{
    // At this point, $res holds the record number
    $rec = dbase_get_record_with_names( $db, $res );
    dumprec( $rec );
}

?>
