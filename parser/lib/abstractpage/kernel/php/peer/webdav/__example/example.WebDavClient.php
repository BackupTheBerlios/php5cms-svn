<html>
<body>

<?php

require( '../../../../../prepend.php' );

using( 'peer.webdav.WebDavClient' );


$wdc = new WebDavClient;
$wdc->setServer( 'demo.webdav.ch' );
$wdc->setPort( 80 );
$wdc->setUser( 'demouser' );
$wdc->setPass( 'demouser' );

// use HTTP/1.1
$wdc->setProtocol( 1 );

if ( !$wdc->open() ) 
{
  	print 'Error: could not open server connection';
  	exit;
}

// check if server supports webdav rfc 2518
if ( !$wdc->checkWebDav() ) 
{
  	print 'Error: server does not support webdav';
  	exit;
}

$dir = $wdc->ls( '/' );

?>

<h1>WebDavClient Test-Suite:</h1><p>
Using method webdav_client::ls to get a listing of dir /:<br>
<table summary="ls" border="1">
<th>Filename</th><th>Size</th><th>Creationdate</th><th>Resource Type</th><th>Content Type</th><th>Activelock Depth</th><th>Activelock Owner</th><th>Activelock Token</th><th>Activelock Type</th>

<?php 

foreach ( $dir as $e ) 
{
  	$ts = $wdc->iso8601ToTime( $e['creationdate'] );
  	
	$line = sprintf('<tr><td>%s&nbsp;</td><td>%s&nbsp;</td><td>%s&nbsp;</td><td>%s&nbsp;</td><td>%s&nbsp;</td><td>%s&nbsp;</td><td>%s&nbsp;</td><td>%s&nbsp;</td><td>%s&nbsp;</td></tr>',
		$e['href'], 
		$e['getcontentlength'], 
		date( 'd.m.Y H:i:s', $ts ),
		$e['resourcetype'],
		$e['getcontenttype'],
		$e['activelock_depth'],
		$e['activelock_owner'],
		$e['activelock_token'],
		$e['activelock_type']
	);
  	
	print urldecode( $line );
}

?>

</table>
<p>
Create a new collection (Directory) using method webdav_client::mkcol...

<?php
$test_folder = '/wdc test 1 folder';
print '<br>creating collection ' . $test_folder .' ...<br>';
$http_status  = $wdc->mkcol($test_folder);
print 'webdav server returns ' . $http_status . '<br>';

print 'removing collection just created using method webdav_client::delete ...<br>';
$http_status_array = $wdc->delete( $test_folder );
print 'webdav server returns ' . $http_status_array['status'] . '<br>';

print 'let\'s see what\'s happening when we try to delete the same nonexistent collection again....<br>';
$http_status_array = $wdc->delete( $test_folder );
print 'webdav server returns ' . $http_status_array['status'] . '<br>';

print 'let\'s see what\'s happening when we try to delete an existent locked collection....<br>';
$http_status_array = $wdc->delete( '/packages.txt' );
print 'webdav server returns ' . $http_status_array['status'] . '<br>';

$test_folder = '/wdc test 2 folder';
print 'let\'s create a second collection ...' . $test_folder . '<br>';
$http_status  = $wdc->mkcol( $test_folder );
print 'webdav server returns ' . $http_status . '<br>';

// put a file to webdav collection
$filename = './Testfiles/test_ref.rar';
print 'Let\'s put the file ' . $filename . ' using webdav::put into collection...<br>';
$handle   = fopen( $filename, 'r' );
$contents = fread( $handle, filesize( $filename ) );
fclose( $handle );
$target_path = $test_folder . '/test 123 456.rar';
$http_status = $wdc->put( $target_path, $contents );
print 'webdav server returns ' . $http_status .'<br>';

$filename = './Testfiles/Chiquita.jpg';
print 'Let\'s Test second put method...<br>';
$target_path = $test_folder . '/picture.jpg';
$http_status = $wdc->putFile( $target_path, $filename );
print 'webdav server returns ' . $http_status . '<br>';

print 'Let\'s use method webdav_client::copy to create a copy of file ' . $target_path . ' the webdav server<br>';
$new_copy_target = '/copy of picture.jpg';
$http_status = $wdc->copyFile( $target_path, $new_copy_target, true );
print 'webdav server returns ' . $http_status . '<br>';

print 'Let\'s use method webdav_client::copy to create a copy of collection ' . $test_folder . ' the webdav server<br>';
$new_copy_target = '/copy of wdc test 2 folder';
$http_status = $wdc->copyColl( $test_folder, $new_copy_target, true );
print 'webdav server returns ' . $http_status . '<br>';

print 'Let\'s move/rename a file in a collection<br>';
$new_target_path = $test_folder . '/picture renamed.jpg';
$http_status = $wdc->move( $target_path, $new_target_path, true );
print 'webdav server returns ' . $http_status . '<br>';

print 'Let\'s move/rename a collection<br>';
$new_target_folder = '/wdc test 2 folder renamed';
$http_status = $wdc->move( $test_folder, $new_target_folder, true );
print 'webdav server returns ' .  $http_status . '<br>';

print 'Let\'s lock this moved collection<br>';
$http_status_array = $wdc->lock( $new_target_folder );
print 'webdav server returns ' . $http_status_array['status'] . '<br>';

print 'locktocken is ' . $http_status_array[0]['locktoken']. '<br>';
print 'Owner of lock is ' . $http_status_array[0]['owner'] . '<br>';

print 'Let\'s unlock this collection with a wrong locktoken<br>';
$http_status = $wdc->unlock( $new_target_folder, 'wrongtoken' );
print "webdav server returns $http_status<br>";

print 'Let\'s unlock this collection with the right locktoken<br>';
$http_status = $wdc->unlock( $new_target_folder, $http_status_array[0]['locktoken'] );
print 'webdav server returns ' . $http_status .'<br>';

print 'Let\'s remove/delete the moved collection ' . $new_target_folder . '<br>';
$http_status_array = $wdc->delete( $new_target_folder );
print 'webdav server returns ' . $http_status_array['status'] . '<br>';

$wdc->close();
flush();

?>

</body>
<html>
