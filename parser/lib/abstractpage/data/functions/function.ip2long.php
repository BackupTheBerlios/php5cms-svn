<?php

if ( !function_exists( "ip2long" ) )
{
	function ip2long( $dotted )
	{
       $dotted = preg_split( "/[.]+/", $dotted );
       $ip     = (double)0;
       $y      = 0x1000000;

		for ( $i = 0; $i < 4; $i++ )
		{
			$ip += ( $dotted[$i] * ( $y ) );
			$y   = ( $y >> 8 );
		}
		
		return $ip;
	}
}

?>
