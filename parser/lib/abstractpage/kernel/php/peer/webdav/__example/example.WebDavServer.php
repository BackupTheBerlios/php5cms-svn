<?php

require( '../../../../../prepend.php' );

using( 'peer.webdav.WebDavServer' );


ini_set( "error_reporting", 2037 );

$callback = array(
	"PROPFIND" => "propfind",
	"GET"      => "get",
	"PUT"      => "put",
	"MOVE"     => "move",
	"MKCOL"    => "mkcol",
	"DELETE"   => "delete"
);

$webdav = new WebDAVServer( $callback );
$webdav->start();

function propfind( $options, &$files )
{
	$fspath = realpath( WEBDAV_DIR_FS . $options["path"] );
	
	if ( !file_exists( $fspath ) )
		return false;
	
	$files["files"]   = array();
	$files["files"][] = fileinfo( $options["path"] );

	if ( $options["depth"] != 0 || $options["depth"] == "infinity" ) 
   	{
		if ( substr( $options["path"],-1) != "/" )
			$options["path"] .= "/";
		
     	$handle = opendir( $fspath );

 	    while ( $filename = readdir( $handle ) )
        {
   	    	if ( $filename != "." && $filename != ".." )
            	$files["files"][] = fileinfo( $options["path"] . $filename );
       	}
   	}
   
   	return true;
} 

function get( $options )
{
	$fspath = WEBDAV_DIR_FS . $options["path"];
	
	if ( file_exists( $fspath ) )
	{
		if ( !is_dir( $fspath ) )
			header( "Content-Type: " . `file -izb '$fspath' 2> /dev/null` );
		else
			header( "Content-Type: httpd/unix-directory" );

		readfile( $fspath );
		return true;
	}
	else 
	{
		return false;
	}	
}

function fileinfo( $uri )
{
	$fspath = WEBDAV_DIR_FS . $uri;
	$file = array();
	$file["href"] = WEBDAV_DIR_WEB . $uri;	

	if ( is_dir( $fspath ) )
	{
		$file["contentlength"] = 0;
		$file["iscollection"]  = true;
		$file["contenttype"]   = "httpd/unix-directory";
	}
	else
	{
		$file["iscollection"]  = false;
		$file["contentlength"] = filesize( $fspath );

		if ( is_readable( $fspath ) )
			$file["contenttype"] = rtrim( preg_replace("/^([^;]*);.*/","$1", `file -izb '$fspath' 2> /dev/null` ) );
		else
			$file["contenttype"] = "application/x-non-readable";
	}
	
	return $file;
}

function put( $options,&$data ) 
{
	if ( $options['content-length'] == 0 ) 
	{
	    if ( file_exists( WEBDAV_DIR_FS . $options["path"] ) )
		{
			header("HTTP/1.1 204 No Content");
		}
		else
		{
			if ( substr( $options["path"], -3 ) != "new" )
			{
				header( "HTTP/1.1 301 Moved Permanently" );
				header( "Location: " . $options["path"] . ".new" );
			}
			else
			{
				header( "HTTP/1.1 201 Created" );
				touch( WEBDAV_DIR_FS . $options["path"] );
			}
		}
	}
	else 
	{
		write_file( $data, WEBDAV_DIR_FS . $options["path"] );	
		header( "HTTP/1.1 204 No Content" );
	}
}

function move( $options )
{
	rename( WEBDAV_DIR_FS . $options["path"], WEBDAV_DIR_DOCROOT . $options["destination"] );
	header( "HTTP/1.1 201 Created" );
}
	
function mkcol( $options ) 
{
	mkdir( WEBDAV_DIR_FS . $options["path"], 0700 );
	header( "HTTP/1.1 201 Created" );
}

function delete( $options ) 
{
	$fspath = WEBDAV_DIR_FS . $options["path"];
		
	if ( is_dir( $fspath ) )
		rmdir( $fspath );
	else 
		unlink( $fspath );
	
	header("HTTP/1.1 204 No Content");
}
	
function write_file( $content, $file, $append = false )
{
	if ( $append )
		$mode = "a";
    else
		$mode = "w";
        
	$fd = fopen( $file, $mode );
	fwrite( $fd, $content);
	fclose( $fd );
}

?>
