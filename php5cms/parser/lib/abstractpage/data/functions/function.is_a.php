<?php

if ( !function_exists( 'is_a' ) )
{
	function is_a( $obj, $name )
	{
		return ( get_class( $obj ) == strtolower( $name ) || is_subclass_of( $obj, strtolower( $name ) ) );
	}
}

?>
