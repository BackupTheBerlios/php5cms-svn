<?php

require( '../../../../../../prepend.php' );

using( 'locale.map.lib.LanguageMap' );
using( 'locale.Lang' );
using( 'locale.LanguageDetection' );
using('util.Util');

$lang = new Lang();
print $lang->select();

$langD = new LanguageDetection();



$map = &LanguageMap::factory( "af", "latin1" );

echo( "<pre>\n" );
echo( $map->language . "<br>\n" );
echo( $map->charset  . "<br>\n" );
echo( print_r( $map->map ) );
echo( "</pre>" );

?>
