<html>
<body>

<pre>

<?php

require( '../../../../../prepend.php' );

using( 'db.nanosql.NanoDB' );

	
// Make a sample database and fill it with data.
$schema = array(
   array( "id",   NANODB_INT_AUTOINC, "key" ),
   array( "name", NANODB_STRING )
);

$db = new NanoDB();
$db->create( NANODB_DB_DIRECTORY . "testdb", $schema );

$record["name"] = "john";
$db->add( $record );

$record["name"] = "joe";
$db->add( $record );

$record["name"] = "john";
$db->add( $record );

$record["name"] = "jane";
$db->add( $record );

// Show all records in a primitive way.
echo "All records:\n\n";
print_r( $db->getall() );

// removebyfield(...)
echo "Deleting all records with 'john' in the 'name' field\n";
$db->removebyfield( "name", "/John/i" );

echo "All records:\n\n";
print_r( $db->getall() );

// removebyfunction(...)
echo "Deleting all records with 'jane' in the 'name' field using a function\n";
$db->removebyfunction( "deleteselect" );

echo "All records:\n\n";
print_r( $db->getall() );

// Delete the database
// $db->drop();


// Function to select records with the "name" as "jane".
function deleteselect( $record )
{
   return ( $record["name"] == "jane" );
}

?>

</pre>

</body>
</html>
