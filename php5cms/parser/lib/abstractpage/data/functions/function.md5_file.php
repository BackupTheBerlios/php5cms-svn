<?php

if ( !function_exists( "md5_file" ) )
{
	function md5_file( $inFile ) 
	{
		if ( file_exists( $inFile ) ) 
		{
			$fd = fopen( $inFile, 'r' );
			$fileContents = fread( $fd, filesize( $inFile ) );
			fclose( $fd );

			return md5( $fileContents );
		} 
		else 
		{
			return false;
		}
	}
}

?>
