<?php

if ( !function_exists( "file_get_contents" ) )
{
	function file_get_contents( $name )
	{
		return implode( '', file( $name ) );
	}
}

?>
