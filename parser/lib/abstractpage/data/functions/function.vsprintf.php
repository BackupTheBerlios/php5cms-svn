<?php

if ( !function_exists( "vsprintf" ) )
{
	function vsprintf( $str, $args ) 
	{
		$etc = '$args[' . implode('], $args[', array_keys($args)) . ']';
		return $args? eval( "return sprintf(\$str, $etc);" ) : sprintf( $str );
	}
}

?>
