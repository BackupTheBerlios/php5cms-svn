/**
 * The function str_split() has been including, not part of the class for
 * it's ability to emulate Perl's split( //, $text ). This function was
 * stolen from the PHP function documentation comments on PHP's str_split()
 * which is to be included in PHP5.
 *
 * @param string $str
 * @param int number of characters you wish to split on
 * @return array|false Returns an array or false when $num is less than 1
 */
if ( !function_exists( "str_split" ) )
{
	function str_split( $str, $num = '1' ) 
	{
   		if ( $num < 1 ) 
			return false;
   	
		$arr = array();
   		
		for ( $j = 0; $j < strlen( $str ); $j = $j + $num )
       		$arr[] = substr( $str, $j, $num );
   
   		return $arr;
	}
}
