<?php

require( '../../../../../prepend.php' );

using( 'html.css.CSSParser' );


$css =& new CSSParser;
$css->parseFile( 'style.css' );
print( '<pre>' );

// get class "format"
print( '.format -> ' );
print_r( $css->get( 'Classes', 'format' ) );
print( '<hr>' );

// get the value of the key "bla" of the selector "sonstwas" of the section "Global"
print( '@sonstwas.bla -> ' );
print( $css->get( 'Globals', 'sonstwas', 'bla' ) );
print( '<hr>' );

// get the selector "test" from the section "Classes"
print( '.box -> ' );
print_r($css->get( 'Classes', 'box' ) );
print( '.menu -> ' );
print_r( $css->get( 'Classes', 'menu' ) );
print( '<hr>' );

// get the whole "Tags" section
print( 'Tags -> ' );
print_r( $css->get('Tags' ) );
print('</pre>');
// $css->parseFile('test.css');

?>
