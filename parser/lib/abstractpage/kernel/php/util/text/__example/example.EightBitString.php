<?php

require( '../../../../../prepend.php' );

using( 'util.text.EightBitString' );


// You need to execute it on any page load that uses this library,
// and it needs to happen ONLY ONCE since it defines constants
// (IE, make sure to use include_once when you include this library).

$str = new EightBitString();
$replacement_chars = $str->get_replacement_chars();
$eight_bit_string  = "";
$seven_bit_string  = "";
  
for ( $i = 128; $i < 256; $i++ )
{
	$eight_bit_string .= chr( $i );
 	$seven_bit_string .= chr( $replacement_chars[$i] );
}
  
define( "EIGHT_BIT_STRING", $eight_bit_string );
define( "SEVEN_BIT_STRING", $seven_bit_string );

$str->print_replacement_array( $replacement_chars );

?>
