<?php

require( '../../../../../prepend.php' );

using( 'db.mysql.StoredProcedure' );


// Recreate the junk database
$sp  = new StoredProcedure( "127.0.0.1", "3306", "root", "", "junk_db_create.sql" );
$res = $sp->execute();

// execute the createAccount stored procedure
$sp->sp_file = "createAccount.sql";
$sp->sp_params = array (
	"UserName"      => "Trial user",
	"UserPassword"  => "XXX",
	"FirstName"     => "FNM",
	"LastName"      => "LNM"
);

$res = $sp->execute();
print( "<hr>" );
print( "createAccount done with result: ");
print_r( $sp->results[count( $sp->results ) - 1] );

?>
