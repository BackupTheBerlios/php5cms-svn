<?php

if ( !function_exists( "array_search" ) )
{
	function array_search( $needle, $hay )
	{
		foreach ( $hay as $k => $v )
		{
			if ( $v == $needle )
				return $k;
		}
			
		return false;
	}
}

?>
