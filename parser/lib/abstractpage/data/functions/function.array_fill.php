<?php

if ( !function_exists( "array_fill" ) )
{
	function array_fill(  $index, $size, $value )
	{
		for ( $i = $index; $i < ( $index + $size ); $i++ )
			$arr[$i] = $value;

		return $arr;
	}
}

?>
