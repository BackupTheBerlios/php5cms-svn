<?php

if ( !function_exists( "xml_trim_array" ) )
{
	function xml_trim_array( $array, $first_element, &$close_element_number )
	{
		// works with an array generated from php's xml_parse_into_struct() function
		// returns (by reference) the closing element number
		// so if you are in a for next loop when you call this, you can set the loop counter
		// to the closing element and the main loop will have skipped the entire tag
		// i.e. from open to close,
		// this function returns an array of only the inside of the open and close tags
		// but not the open and close tags.
		// if they contain important data, you'll have to get at it external to this function
		// which makes sense anyway because you'll know what position in the main array
		// they have since you're calling this with an open tag, and it returns the close tag number.

		if ( $array[$first_element]['type'] == "open" )
		{
			$tag_looking_for   = $array[$first_element]['tag'];
			$level_looking_for = $array[$first_element]['level'];

			for ( $a = $first_element + 1; $a < count( $array ); $a++ )
			{
				if ( $array[$a]['type'] == "close" && $array[$a]['tag'] == $tag_looking_for && $array[$a]['level'] == $level_looking_for )
				{
					$close_element_number = $a;
					break ;
				}
				else
				{
					$out[] = $array[$a];
				}
			}
		}
		else
		{
			echo "ERROR in trim_array(): the element specified is not an open tag";
		}
		
		return $out;
	}
}

?>
