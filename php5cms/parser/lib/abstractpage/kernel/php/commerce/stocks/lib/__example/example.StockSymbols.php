<?php

require( '../../../../../../prepend.php' );

using( 'commerce.stocks.lib.StockSymbols' );


$sym = &StockSymbols::factory( "mse" );

echo( "<pre>\n" );
echo( print_r( $sym->getAll() ) );
echo( "</pre>" );

?>
