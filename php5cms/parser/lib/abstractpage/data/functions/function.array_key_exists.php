<?php

if ( !function_exists( "array_key_exists" ) )
{
	function array_key_exists( $key, $array, $value = false ) 
	{
		while ( list( $k, $v ) = each( $array ) )
		{
			if ( $key == $k )        
			{
				if ( $value && $value == $v )
					return true;
				else if ( $value && $value != $v )
					return false;
				else
					return true;
			}
		}
	
		return false;
	}
}

?>
