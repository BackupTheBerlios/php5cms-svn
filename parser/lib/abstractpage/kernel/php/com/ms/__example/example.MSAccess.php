<?php

require( '../../../../../prepend.php' );

using( 'com.ms.MSAccess' );


/*
 * This is some wired example.
 * You cannot use it out of the box.
 * 
 * Use your own mdb filename and 
 * your own tablename and 
 * your own fieldnames.
 * 
 * You really need a Windows Server and a mdb file on it to have it work!
 * Big chance it does not work with Linux or Unix - but tell me if it does.
 * 
 * This example opens the mdb once and then reads it twice.
 * I know about 2 ways to get the values, so I show them all.
 * Maybe there is a speed difference.
 * 
 * Works on a Win 2000 server with PHP 4.3.4 and Microsoft-IIS/5.0
 */

$mdb = new MSAccess( 'test.mdb' ); // your own mdb filename required
$mdb->open();
$mdb->execute( 'select * from test_table' ); // your own table in the mdb file

// first example: using fieldnames
while( !$mdb->eof() )
{
  echo $mdb->fieldvalue( 'description' ); // using your own fields name
  echo ' = ';
  echo $mdb->fieldvalue( 1 ); // using the fields fieldnumber
  echo '<br>';
  
  $mdb->movenext();
}

echo '<br><hr><br>';

// Going back to the first recordset for the second example
$mdb->movefirst();

// This works, too: Make each Field an object. The values change
// when the data pointer advances with movenext().
$url = $mdb->RS->Fields( 1 );
$bez = $mdb->RS->Fields( 2 );
$kat = $mdb->RS->Fields( 3 );

while( !$mdb->eof() )
{
  // works!
  echo $bez->value;
  echo ' = ';
  echo $url->value;
  echo '<br>';
  
  $mdb->movenext(); 
}

?>
