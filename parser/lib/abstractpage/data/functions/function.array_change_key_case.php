<?php

if ( !function_exists( 'array_change_key_case' ) )
{
	function array_change_key_case( $an_array )
	{
		if ( is_array( $an_array ) )
   		{
       		foreach ( $an_array as $key => $value )
         		$new_array[strtolower($key)] = $value;

       		return $new_array;
   		}
   		else
		{
       		return $an_array;
		}
	}
}

?>
